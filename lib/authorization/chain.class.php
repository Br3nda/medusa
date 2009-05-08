<?php
/*
 * Permissions Chain class
 *
 * Contains a chain of commands to execute in the processing of permissions
 * A chain is made up of objects which extend from the permissions class
 * When added they are placed into the checks_queue and if they have a process method are also placed into the process_queue
 * The queues must be explicitly sorted prior to execution otherwise they will execute in the order the commands were added
 *
 * Commands are sorted by their associated weight, weight is specified when the command is added
 *
 * The checks queue is always executed
 * The process queue is only executed if a command sets aggegrate_results to true
 *
 * The chain only holds a single instance of each permission despite the fact that each permission can be called in two seperate chains (checks, process)
 * The checks and process chains hold references/pointers to the location of the actual object in the $commands array
 * This funkiness is to allow permissions object to change their state after performing a check for use when processing results if aggegrating
 */
class permissionsChain {
    private $options = array(
        'force_full_chain' => false, //forces the full chain to execute, though will still return the standard result
        'aggegrate_results' => false, //forces the full chain to execute and executes the process queue to get an altered final result
    );
    private $commands = array(); //Holds all the actual permissions objects
    private $checks_queue = array(); //An array of rows, each row contains a weight and a reference to the permissions object in the $commands array
    private $process_queue = array(); //An array of rows, each row contains a weight and a reference to the permissions object in the $commands array

    /*
     * checkOptions
     *
     * Internal function to set $this->options from an array of provided options ($new)
     * Function only sets to true as default is false
     */
    private function checkOptions($new) {
        if (!empty($new)) {
            foreach ($new as $key=>$opt) {
                if (isset($this->options[$key]) && $opt) {
        	        $this->options[$key] = $opt;
                }
            }
    	}
    }

    /*
     * addCommand
     *
     * Adds a permissions object to the queues with the specified weights
     * $command is the permissions object
     * $weight is an integer determining where it should be placed in the queue
     *      Higher values cause it to sink, lighter values to rise
     */
    public function addCommand($command, $weight) {
        //Add the command to the main collection
        $this->commands[] = $command;

        //Grab internal options from the command
        $opts = $command->getOptions();

        //Check command options againsts the chain options
        //This will set aggegrate_results and force_full_chain if necessary
        $this->checkOptions($opts);

        //Grabbing a reference to the object in the commands array
        end($this->commands);
        $key = key($this->commands);

        //Add this reference to the checks queue
        $this->checks_queue[] = array(
            'weight' => $weight,
            'class' => &$this->commands[$key],
        );
        error_logging('DEBUG', 'Added command '.get_class($command).' to check queue');

        if($opts['has_process']) {
            //This command object has a process method it wants added to the queue
            $this->process_queue[] = array(
                'weight' => is_numeric($opts['process_weight']) ? $opts['process_weight'] : 0, //Set weight to zero if it doesn't have one
                'class' => &$this->commands[$key],
            );
            error_logging('DEBUG', 'Added command '.get_class($command).' to process queue');
        }
    }

    /*
     * sortCommands
     *
     * Sorts the internal queues by each commands weight field
     * Order is by ascending
     */
    public function sortCommands() {
        //Only sort queues if they have something in them
        if (!empty($this->checks_queue)) {
            //Need weights into a seperate array for array_multisort
            $weights = array();
            foreach ($this->checks_queue as $key=>$check) {
                $weights[$key] = $check['weight'];
            }
            //Actual sorting function
            error_logging('DEBUG', 'Sorting checks_queue');
            array_multisort($weights, SORT_ASC, $this->checks_queue);
        }
        if (!empty($this->process_queue)) {
            $weights = array();
            foreach ($this->process_queue as $key=>$check) {
                $weights[$key] = $check['weight'];
            }
            error_logging('DEBUG', 'Sorting process_queue');
            array_multisort($weights, SORT_ASC, $this->process_queue);
        }
    }

    /*
     * Executes the chain of commands
     * Returns a boolean
     *
     * $object is the WR object to determine the permissions of
     * $user is the user to check against
     */
    public function execute(&$object, &$user) {
        //Initialize main variables
        $result = false; //function returns this var, default should be false
        $results_map = array(); //stores the result from each called permission
        $local_result = false; //used to store the result generated from the current check

        //Iternates though the checks_queue
        foreach ($this->checks_queue as &$check) {
            //Gets the result of the current permission check
            //Note that $object and $user are passed by reference through the permission class
            error_logging('DEBUG', 'Executing performCheck on '.get_class($check['class']));
            $local_result = $check['class']->performCheck($object, $user);

            //If the local result is true then the final result should be true
            //This is to prevent false being set if a further check fails when forcing a full chain
            if ($local_result) {
                $result = true;
            }

            //Add the current result to the results map
            $results_map[] = array(
                'result' => $local_result,
                'class' => get_class($check['class']),
            );

            //If this is standard processing break the foreach and return the result if $local_result is true
            if ($local_result && !$this->options['aggegrate_results'] && !$this->options['force_full_chain']) {
                break;
            }
        }

        //If the aggegrate_results option has been set process the result
        if ($this->options['aggegrate_results'] && !empty($this->process_queue)) {
            //Iternate through the process_queue
            foreach ($this->process_queue as &$command) {
                //Execute each procesResult method
                //$result_map and $result are passed by reference
                error_logging('DEBUG', 'Executing processResult on '.get_class($command['class']));
                $command['class']->processResult($result_map, $result);
            }
        }
        //Return the final result
        return $result;
    }
}

