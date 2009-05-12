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
  private $sqldata;
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
      $this->sqldata       = new wrms_search_sql_feed($parameters['GET']['type']);
      $this->gettable      = $this->sqldata->getSearchTable();
      $this->gettodbjoins  = $this->sqldata->getJoinSQL(); # Array of joins
      $this->gettodbfields = $this->sqldata->getWhereFields(); # List of fields
      // TODO - run our access magic here.
      if ($this->sqldata == null) {
        error_logging('WARNING', "Invalid search type provided.");
        return new error('Invalid search type  provided.');
      } else {
        $this->parameters = $parameters['GET'];
        return $this->search();
      }
    }
  }

  /**
  * Performs a search using dynamically generated SQL from the input parameters.
  */
  private function search() {
  /**
    * Acceptable paramters are;
    *
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
    $sql = "SELECT DISTINCT ". $this->gettable .".* FROM ". $this->gettable ." ". implode(' ', $joinsql) ." WHERE ". implode(' AND ', $wheresql);
    error_logging('DEBUG', "wrms_search auto generated $sql");
    $result = db_query($sql);

	  $resp = new response('Success');
    while ($row = db_fetch_assoc($result)) {
      $object = $this->sqldata->getNewObject();
      error_logging('DEBUG', "Creating new ". get_class ($object) . " in wrms_search");
      $object->populate($row);
      $object->populateChildren();
      $resp->data[] = $object;
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



