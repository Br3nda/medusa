<?php
/** 
* @file funcs.db.conn.inc
* @description Database connection and query functions
*
*/

/**
 * @defgroup Database Database Queries
*  For people hacking on medusa 
 * @{
*/
class db {
  
  // Connection Object
  private static $con = null;
  
  // used for black transactions
  private static $query = array();
  
  private static function resetBlockTransaction() {
    self::$query = array();
    return true;
  }
  
  /**
    * start transaction block
    */
    public static function begin() {
	  error_logging('DEBUG', "Begin transaction");
      return self::resetBlockTransaction();
    }
  
  /**
    * append query to transaction block -> can be called the same way as db_query
    */
    public static function block_query($query) {
      $args = func_get_args();
      array_shift($args);
      if ($args) {
        _sort_db_query($args, true);
        $query = preg_replace_callback('/(%d|%s|%f)/', '_sort_db_query', $query);
      }
      self::$query[] = $query;
      return true;
    }
  
  /**
    * commits database queries built up from block_query
    */
    public static function commit($commit = true) {
  	  error_logging('DEBUG', "commit() called");
      self::query("BEGIN");
      $rollback = false;
      foreach (self::$query as $query) {
        if (!db_query($query)) {
          errorLogging('ERROR', "QUERY FAILED: $query");
          self::query("ROLLBACK");
          $rollback = true;
          break;
        }
      }
      self::$query = array();
      /**
        * this allows testing to see if queries run without fail
        * without commiting at the end
        */
        if ($commit && !$rollback) {
          self::query("COMMIT");
          return true;
        } 
        else {
          self::query("ROLLBACK");
        }
      if ($rollback) {
        return false;
      }
      return true;
    }
  
  
  /**
    * insure connection creditials are available
    */
    public static function init() {
      $msg = '';
      if (!defined('CONFIG_DBNAME')) {
        $msg = "Database Is Not Defined.";
      }
      if (!defined('CONFIG_DBHOST')) {
        $msg .= "CONFIG_DBHOST Is Not Defined.";
      }
      if (!defined('CONFIG_DBUSER')) {
        $msg .= "Username Is Not Defined.";
      }
      if (!defined('CONFIG_DBPORT')) {
        $msg .= "Port Is Not Defined.";
      }
      if (!empty($msg)) {
        exit($msg);
      }
      return true;
    }
  
  /**
    * Connection function
    * @return connection object or exit on failure
    */
    public static function connect() {
      if (self::$con) {
        return self::$con;
      }
      if (self::init()) {
        $str = 'host='.CONFIG_DBHOST.' dbname='.CONFIG_DBNAME.' user='.CONFIG_DBUSER . ' port='.CONFIG_DBPORT;
        self::$con = pg_connect($str);
        if (!self::$con) {
          errorLogging('CRITICAL', "Could not connect to database: $str");
          exit("Could Not Connect to Database");
        }
        return self::$con;
      }
    }
  
  /**
    * escapes str for query
    */
    public static function escape_string($str) {
      return pg_escape_string($str);
    }
  
  /**
    * query database
    * @param $query string - string of SQL
    * @return recordset object on success or false on failure
    */
    public static function query($query) {
      return pg_query(self::connect(), $query);
    }
  
  /**
    * returns number of rows in given dataset
    * @param $rs Recordset to get count of
    */
    public static function num_rows($rs) {
      if (get_resource_type($rs) == 'pgsql result') {
        return pg_num_rows($rs);
      }
      return false;
    }
}//end class


 /**
  * recording load times and queries
  */
class stopwatch {
         //start time
         private $start;

         //stop time
         private $stop;

         private $recordset = array();

         public function __construct() {
                 $this->start = date('U');
         }

         public function reset() {
                 $this->recordset = array();
                 $this->start = date('U');
         }

         public function stop() {
                 $this->stop = date('U');
                 $this->recordset[] = $this->stop - $this->start;
                 if (count($this->recordset) > 1) {
                         return $this->recordset;
                 }
                 return $this->stop - $this->start;
         }

         public function getTime() {
                 if ($this->start > $this->stop) {
                         $this->stop = date('U');
                 }
                 if (count($this->recordset) > 1) {
                         return $this->recordset;
                 }
                 return $this->stop - $this->start;
         }

         public function split() {
                 $this->recordset[] = date('U') - $this->start;
                 $this->start = date('U');
         }
 }



/**
 * Runs a basic query in the active database.
 *
 * User-supplied arguments to the query should be passed in as separate
 * parameters so that they can be properly escaped to avoid SQL injection
 * attacks.
 *
 * @param $query
 *   A string containing an SQL query.
 * @param ...
 *   A variable number of arguments which are substituted into the query
 *   using sprintf() syntax. Instead of a variable number of query arguments,
 *   you may also pass a single array containing the query arguments.
 *
 *   Valid %-modifiers are: %s, %d, %f, %b (binary data, do not enclose
 *   in '') and %%.
 *
 *   NOTE: using this syntax will cast NULL and FALSE values to decimal 0,
 *   and TRUE values to decimal 1.
 *
 * @return
 *   A database query result resource, or FALSE if the query was not
 *   executed correctly.
 * @ingroup Database
 */
function db_query($query) {
  static $watch;
  if (get_class($watch) != 'stopwatch') {
  	$watch = new stopwatch();
  }
  $args = func_get_args();
  array_shift($args);
  if ($args) {
    _sort_db_query($args, true);
    $query = preg_replace_callback('/(%d|%s|%f|%b)/', '_sort_db_query', $query);
  }
  //Record time to make query
  error_logging('DEBUG-SQL', $query);
  $watch->reset();
  $rs = db::query($query);
  $time = $watch->stop();
  if ($time > 1 && !DEBUG_MODE) {
    $query = preg_replace('/\\n/', '', $query);
    error_logging("ERROR", "SQL Query Took $time seconds: $query");
  }
  if (!$rs) {
    $query = preg_replace('/\\n/', '', $query);
    error_logging("ERROR", "SQL query failed: $query");
  }
  return $rs;
}

/**
 * makes db querys safe
 * @param $matches - if $reset is true, $matches should be an array of arguments else $matches is populated
 *                                from preg_replace_callback in db_query()
 * @param $reset - Defaults to false
 * @return db query safe string
 * @ingroup Database
 */
function _sort_db_query($matches, $reset = false) {
  static $args;
  if ($reset) {
    $args = $matches;
    return;
  }
  switch ($matches[1]) {
    case '%d':
      if (!is_int($args[0])) {
        return intval(array_shift($args));
      }
    return array_shift($args);
    break;
    case '%s':
      if (is_string($args[0])) {
        return db::escape_string(array_shift($args));
      }
    $str = array_shift($args);
    return db::escape_string("$str");
    break;
    case '%f':
      return floatvar(array_shift($args));
    break;
    case '%b':
        //echo "Boolean found.\n";
      return "'". pg_escape_bytea(array_shift($args)) ."'";
    break;
  }
}

/**
 * Fetch associative array from row of recordset
 * @param $result recordset to get associative array from
 * @param $int offset of row
 * @return array
 * @ingroup Database
 */
function db_fetch_assoc($result, $int = false) {
  if ($result && is_int($int)) {
    return pg_fetch_assoc($result);
  } 
  elseif ($result) {
    return pg_fetch_assoc($result);
  }
  return false;
}

/**
 * @return number of rows
 * @param $rs recordset
 * @ingroup Database
 */
function db_num_rows($rs) {
  return db::num_rows($rs);
}

/**
 * @return object as recordset rows
 * @param $result recordset
 * @param $int offset of row
 * @ingroup Database
 */
function db_fetch_object($result, $int = false) {
  if ($result && is_int($int)) {
    return pg_fetch_object($result, $int);
  } 
  elseif ($result) {
    return pg_fetch_object($result);
  }
}

/**
 * Begins a block transaction
 * @ingroup Database
 */
function db_begin() {
  db::begin();
}

/**
 * appends a query to a block transaction
 * @ingroup Database
 */
function db_block_query($query) {
  $args = func_get_args();
  array_shift($args);
  if ($args) {
    _sort_db_query($args, true);
    $query = preg_replace_callback('/(%d|%s|%f|%b)/', '_sort_db_query', $query);
  }
  db::block_query($query);
}

/**
 * commits a block transaction
 * @ingroup Database
 */
function db_commit($bool = true) {
  return db::commit($bool);
}

/**
 * Perform an SQL query and return success or failure.
 *
 * @param $sql
 *   A string containing a complete SQL query.  %-substitution
 *   parameters are not supported.
 * @return
 *   An array containing the keys:
 *      success: a boolean indicating whether the query succeeded
 *      query: the SQL query executed, passed through check_plain()
 * @ingroup Database
 */
function update_sql($sql) {
  $result = db_query($sql, true);
  return array('success' => $result !== FALSE, 'query' => check_plain($sql));
}



