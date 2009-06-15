<?php
/*
 * Permissions object
 *
 * Each permissions object defines a check to occur when run
 * The filename of the permissions object defines:
 *   1) The permission to observe for
 *   2) Relative weight in the command queue
 *   3) The class name
 */
abstract class permissions {
    abstract public function performCheck(&$object, &$user);
    
    /*
     * Any addition options to send to the queue, return an associative array
     * Options
     *    force_full_chain (bool): forces the full chain to process. Default: false
     *	  aggegrate_results (bool): forces system to aggegrate the final result using processResult functions. Default: false
     *    process_weight (int): weight/rank to call processResult if aggegrating results. Default: 0
     *	  has_process (bool): whether this permission has a processResult that should be called on aggegrate. Default false
     */
    public function getOptions() {
        return array();
    }

    /*
     * Called when processing an aggegrated result
     *
     * $results_map is an array of results mapped to the permission class that called them
     * $final_result is the current final result
     */
    public function processResult(&$results_map, &$final_result) {
        //
    }
}
