<?php

class wrms_search {
    private $parameters;
    private $gettodbfields;
    private $gettodbjoins;

    function __construct() {
        $this->gettodbfields = array();
        $this->gettodbjoins = array();

        $this->gettodbfields['requesterusername'] = 'requsrname.username';
        $this->gettodbjoins ['requesterusername'] = 'INNER JOIN usr AS requsrname ON requsrname.user_no=request.requester_id';

        $this->gettodbfields['assignedusername'] = 'assusrname.username';
        $this->gettodbjoins ['assignedusername'] = 'INNER JOIN request_allocated ON request_allocated.request_id=request.request_id INNER JOIN usr AS assusrname ON assusrname.user_no=request_allocated.allocated_to_id';

    }


    public function run($parameters) {
      	error_logging('DEBUG', "Found '". $parameters['GET']['type'] ."' for type");
        	error_logging('WARNING', "No type provided.");
            return null;
        }
        else {
                $this->parameters = $parameters['GET'];
                switch ($this->parameters['type']) {
                case 'request':
                        return $this->searchWorkRequests();
                        break;
                case 'workrequest':
                        return $this->searchWorkRequests();
                        break;
                default:
                        error_logging('WARNING', "Search type ". $parameters['type']." doesn't exist.");
                        break;
                }
            }
    }

    /**
    * Added the structure for dynamically generated SQL code, and some example stuff.:lib/methods/wrms_search.php
    * If a search request is found for workrequests, search for and builds workrequest objects
    * based on the records found.
    */
    private function searchWorkRequests() {

        $matches = array();
        /* Acceptable paramters are; 
        * requester
        * status history
        * watchers (users)
        * todo (users)
        */
            
        $joinsql = array(); # We could do a big string, but this is similar to the below bit, which is nice
        $wheresql = array(); # list of where's to join together in abig happy array

        foreach ($this->parameters as $parameterkey => $parameterstring) {

            if (array_key_exists($parameterkey,$this->gettodbfields) && array_key_exists($parameterkey,$this->gettodbjoins)) {
                $joinsql[] = $this->gettodbjoins[$parameterkey];
                $wheresql[] = $this->formatBoolValues($this->gettodbfields[$parameterkey],$parameterstring);
            }
       }
        $sql = "SELECT * FROM request ".implode(' ',$joinsql)." WHERE " . implode(' AND ',$wheresql);
        $result = db_query($sql);

        while ($row = db_fetch_assoc($result)) {
          error_logging('DEBUG', "Creating WrmsWorkRequest in wrms_search");
            $workreq = new WrmsWorkRequest();
            $workreq->populate($row);
            $matches[] = $workreq;
        }
        return $matches;
    }

    /**
    * creates an SQL string from boolean search
    * @param $string = string to fix up
    * @param $key = db table column name
    * Example;
    * usr.username, "joe OR alice AND bob"
    * usr.username='joe' OR usr.username='alice' AND usr.username='bob'
    */
    private function formatBoolValues($key,$string) {
        # TODO add some function checking here        
        return preg_replace(array('/([a-zA-Z0-9]+)/','/\+/','/\|/'),array($key.'=\'${1}\'',' AND ',' OR '),$string);
    }

    function __destruct() {
    }
}


