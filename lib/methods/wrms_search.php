<?php

class wrms_search {
  private $parameters;
  private $sqldata;
  private $gettable;
  private $gettodbjoins;
  private $gettodbfields;

  function __construct() {
  }


  public function run($parameters) {
    if ($parameters['GET']['type'] == null) {
      error_logging('WARNING', "No type provided.");
      return null;
    }

    $this->sqldata       = new wrms_search_sql_feed($parameters['GET']['type']);
    $this->gettable      = $this->sqldata->getSearchTable();
    $this->gettodbjoins  = $this->sqldata->getJoinSQL(); # Array of joins
    $this->gettodbfields = $this->sqldata->getWhereFields(); # List of fields

    // TODO - run our access magic here.
    if ($this->sqldata == null) {
      error_logging('WARNING', "No type provided.");
      return null;
    }

    $this->parameters = $parameters['GET'];
    return $this->search();
  }

    /**
  * If a search request is found for workrequests, search for and builds workrequest objects
  * based on the records found.
    */
  private function search() {
    $matches = array();

  /**
    * Acceptable paramters are;
    * requester
    * status history
    * watchers (users)
    * todo (users)
  */
    foreach ($this->parameters as $parameterkey => $parameterstring) {
      if (array_key_exists($parameterkey, $this->gettodbfields) && array_key_exists($parameterkey, $this->gettodbjoins)) {
        $joinsql[] = $this->gettodbjoins[$parameterkey];
        $wheresql[] = $this->formatBoolValues($this->gettodbfields[$parameterkey], $parameterstring);
      }
    }
    $sql = "SELECT ". $this->gettable .".* FROM ". $this->gettable ." ". implode(' ', $joinsql) ." WHERE ". implode(' AND ', $wheresql);
    error_logging('DEBUG', "wrms_search auto generated $sql");
    $result = db_query($sql);

    while ($row = db_fetch_assoc($result)) {
      error_logging('DEBUG', "Creating WrmsWorkRequest in wrms_search");
      $workreq = new WrmsWorkRequest();
      $workreq->populate($row);
      $workreq->populateChildren();
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
  private function formatBoolValues($key, $string) {
    // TODO add some function checking here
    return preg_replace(array('/([a-zA-Z0-9]+)/', '/\+/', '/\|/'), array($key .'=\'${1}\'', ' AND ', ' OR '), $string);
  }

  function __destruct() {
  }
}

