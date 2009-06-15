<?php

class access {
    private static $instance;
    private $perm_dir;
    private $user;
    private $permissions_map = array();
    private $loaded_files = array();
    private $chains = array();

    /*
     * build_map
     *
     * Function to build a map of available files and the permissions they are associated with
     * Reads the directory permissions/ relative to this file
     */
    private function build_map() {
        error_logging('DEBUG', "Building permissions map");
        //Assuming location of files is one directory off
        //Read through the directory and place files into the permissions map
        //Assumes files are of the format [permission].[weight].[class name].php
        $dir = $this->perm_dir;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '..' && $file != '.') {
                        $parts = explode('.', $file);
                        $this->permissions_map[$parts[0]][] = array(
                            'file' => $file,
                            'weight' => $parts[1],
                            'class' => 'permissions_'.$parts[0].'_'.$parts[2],
                        );
                        $index = $parts[0];
                    }
                    
                }
                closedir($dh);
            }
        }
    }

    private function includeFile($permission) {
        $dir = $this->perm_dir;
        if (!isset($this->loaded_files[$permission])) {
            $filename = $dir.$permission;
            error_logging('DEBUG', "Including permission $permission");
            include_once($filename);
        }
    }

    private function __construct() {
        //On construction build our permissions map from files
        $this->perm_dir = dirname(__FILE__).'/permissions/';
        if (empty($this->permissions_map)) {
            $this->build_map();
        }
    }

    private function getQueue($action) {
        //Get all permissions associated with this access
        //Also looks for parents to the action and merges all into a single queue
        /*
         * Example: wr/timesheet/add
         *
         * Checks for permissions on:
         * wr
         * wr/timesheet
         * wr/timesheet/add
         */
        $parts = explode('/', $action);
        $search = array();
        $return = array();
        foreach ($parts as $part) {
            $search[] = $part;
            $perm = implode('_', $search);
            error_logging('DEBUG', "Searching for $perm");
            $items = $this->permissions_map[$perm];
            if (!empty($items) && is_array($items)) {
                error_logging('DEBUG', 'Returning item of class/file '. $items[0]['class'].'/'. $items[0]['file']);
                $return = array_merge($return, $items);
            }
        }
        return $return;
    }

    public function permitted ($action, $object) {
        error_logging('DEBUG', "Executing permissions check for $action");
        if (defined('AUTHORIZE_FREE_ACCESS') && constant('AUTHORIZE_FREE_ACCESS')) {
            //Free access to all requests including anonymous
            error_logging('DEBUG', "Allowing access to all sessions including anonymous");
            return true;
        }
        if (defined('AUTHORIZE_ALLOW_ALL') && constant('AUTHORIZE_ALLOW_ALL')) {
            //Free access to logged in users
            error_logging('DEBUG', "Allowing access to all autheticated sessions");
            if ($this->user) {
                //Got a filled user object, thus a logged in users
                error_logging('DEBUG', "User is logged in, granting permission");
                return true;
            } else {
                //Empty users object, must be an anonymous user
                error_logging('DEBUG', "User is not logged in, witholding permission");
                return false;
            }
        }
        $queue = $this->getQueue($action);
        if (!empty($queue)) {
            if (isset($this->chains[$action])) {
                //This permission chain was already created
                //So reuse it
                return $this->chains[$action]->execute($object, $this->user);
            } else {
                //A brand new permission chain is necessary
                $this->chains[$action] = new permissionsChain();
                foreach ($queue as $item) {
                    //Foreach queued permission include the file and add to the chain
                    $this->includeFile($item['file']);
                    $this->chains[$action]->addCommand(new $item['class'], $item['weight']);
                }
                $this->chains[$action]->sortCommands(); //Sorts commands into correct order
                return $this->chains[$action]->execute($object, $this->user);
            }
        } else {
            //No permissions to process so return false as default
            return false;
        }
    }

    public function setUser($user) {
        if (!empty($user)) {
            $this->user = $user;
        }
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    public function __clone() {
        trigger_error('Clone is forbidden on this object', E_USER_ERROR);
    }
}
