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
        $result = db_query("SELECT * FROM request WHERE request_id='%d'", $_GET['id']);

        foreach ($result as $row) {
            $matches[] = new wrms.WorkRequest()->populate($row);
        }
        return $matches;
    }

    function __destruct() {
    }
}

?>

