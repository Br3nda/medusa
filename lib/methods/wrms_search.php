<?php

class wrms_search {
    private $parameters;

    function __construct() {
    }

    public function run($paramters) {
        if ($paramters['type'] == null) {
        	error_logging('WARNING', "No type provided.");
            return null;
        }
        else {
                $this->parameters = $parameters;
                switch ($paramters['type']) {
                case 'request':
                        return $this->searchWorkRequests();
                        break;
                case 'workrequest':
                        return $this->searchWorkRequests();
                        break;
                default:
                        error_logging('WARNING', "Search type ". $paramters['type']." doesn't exist.");
                        break;
                }
            }
    }

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

        if ($paramters['requester'] != null) {
            $joinsql[] = 'INNER JOIN usr ON usr.user_no=request.requester_id';
            $wheresql[] = 
        }

#        $result = db_query("SELECT * FROM request WHERE request_id=%d", $_GET['id']);
#       $result = db_query("SELECT * FROM request WHERE requester_id=%d", $_GET['uid']);
       $result = db_query("SELECT * FROM request INNER JOIN usr ON usr.user_no=request.requester_id WHERE usr.username='%s'", $_GET['username']);
        while ($row = db_fetch_assoc($result)) {
            $workreq = new WrmsWorkRequest();
            $workreq->populate($row);
            $matches[] = $workreq;
            
        }
        return $matches;
    }

    function __destruct() {
    }
}

?>

