<?php
/**
 * @file 
 * @ingroup Methods
 * wrms.request.search
 * Returns a list specified objects based on a dynamic list of arguments
 */

 /**
  * @ingroup Methods
  * Search objects
  */
class wrms_search extends wrms_base_method {
  private $parameters;
  private $gettable;
  private $gettodbjoins;
  private $gettodbfields;

  function __construct() {
  }

  /**
   * Search on the requested options
   *
   * @param $params
   *   Associative array of parameters
   *   - $params->type: Type of object to search for. See wrms_search_sql_feed for more details.
   *   - $params->xxx: Attributes to search against. See wrms_search_sql_feed and wrms_search::formatBoolValues for formatting for more details.
   * @return
   *   - An array of object on success
   *   - An empty array if the request was valid, but no matches were found
   *   - An error on failure
   */
  public function run($parameters) {
    if ($parameters['GET']['type'] == null) {
      error_logging('WARNING', "No type provided.");
      return new error('"No type provided.');
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
    * Adds the structure for dynamically generated SQL code, and some example stuff.:lib/methods/wrms_search.php
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
            if (array_key_exists($parameterkey, $this->gettodbfields) && array_key_exists($parameterkey, $this->gettodbjoins)) {
                $joinsql[] = $this->gettodbjoins[$parameterkey];
                $wheresql[] = $this->formatBoolValues($this->gettodbfields[$parameterkey], $parameterstring);
            }
        }
        $sql = "SELECT * FROM request ".implode(' ', $joinsql) ." WHERE ". implode(' AND ', $wheresql);
        error_logging('DEBUG', "wrms_search auto generated $sql");
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
  * If a search request is found for workrequests, search for and builds workrequest objects
  * based on the records found.
  */
  private function search() {
  /**
    * Acceptable paramters are;
    * requester
    * status history
    * watchers (users)
    * todo (users)
  */
    $found = false;
    foreach ($this->parameters as $parameterkey => $parameterstring) { 
      if (array_key_exists($parameterkey, $this->gettodbfields) && array_key_exists($parameterkey, $this->gettodbjoins)) {
        $found = true;
        $joinsql[] = $this->gettodbjoins[$parameterkey];
        $wheresql[] = $this->formatBoolValues($this->gettodbfields[$parameterkey], $parameterstring);
      }
    }
    if ($found == false)
      return new error("No usable search terms found.");
    $sql = "SELECT ". $this->gettable .".* FROM ". $this->gettable ." ". implode(' ', $joinsql) ." WHERE ". implode(' AND ', $wheresql);
    error_logging('DEBUG', "wrms_search auto generated $sql");
    $result = db_query($sql);

	$resp = new response('Success');
    while ($row = db_fetch_assoc($result)) {
      error_logging('DEBUG', "Creating WrmsWorkRequest in wrms_search");
      $workreq = new WrmsWorkRequest();
      $workreq->populate($row);
      $workreq->populateChildren();
      $resp->data[] = $workreq;
    }
    return $resp;
  }

  /**
  * creates an SQL string from boolean search
  * @param $string = string to fix up
  * @param $key = db table column name
  * Example;
  * usr.username, "joe OR alice AND bob"
  * usr.username='joe' OR usr.username='alice' AND usr.username='bob'
  */
  private function formatBoolValues($key, $string) {
    // TODO add some function checking here
    return preg_replace(array('/([a-zA-Z0-9]+)/', '/\+/', '/\|/'), array($key .'=\'${1}\'', ' AND ', ' OR '), $string);
  }

  function __destruct() {
  }
}



