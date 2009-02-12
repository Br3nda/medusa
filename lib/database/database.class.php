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
    public static function block_query($query, $args) {
      self::$query[] = array($query, $args);
      return true;
    }

  /**
    * commits database queries built up from block_query
    */
    public static function commit($commit = true) {
  	  error_logging('DEBUG', "commit() called");
  	  $con = self::connect();
  	  $con->beginTransaction();
      $rollback = false;
      foreach (self::$query as $query) {
        try {
          $stmt = $con->prepare($query[0]);
          $stmt->execute($query[1]);
        }
        catch (PDOException $e) {
          errorLogging('ERROR', "QUERY FAILED: $query[0]");
          $con->rollback();
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
          try {
            $con->commit();
            return true;
          }
          catch (PDOException $e) {
            return false;
          }
        } 
        else {
          $con->rollBack();
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
    * @return PDO connection object or exit on failure
    */
    public static function connect() {
      if (self::$con) {
        return self::$con;
      }
      if (self::init()) {
        $str = 'host='.CONFIG_DBHOST.' dbname='.CONFIG_DBNAME.' user='.CONFIG_DBUSER . ' port='.CONFIG_DBPORT;
        try {
          self::$con = new PDO('pgsql:'.$str, null, null, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
          ));
        }
        catch (PDOException $e) {
          error_logging('CRITICAL', 'Could not connect to database: '.$e->getMessage());
          exit('Could not Connect to Database');
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
    * @param array two-element array with SQL query in 0 and binds array in 1
    * @return PDOStatement PDO statement that may be iterated over
    */
    public static function query($query) {
      try {
        $stmt = self::connect()->prepare($query[0]);
        $stmt->execute($query[1]);
        return $stmt;
      }
      catch (PDOException $e) {
        error_logging('ERROR', 'QUERY FAILED: '.$query[0].' '.print_r($query[1], true).' '.$e->getMessage());
        return false;
      }
    }
  
  /**
    * returns number of rows in given dataset
    * @param PDOStatement $stmt the PDO executed statement handle
    */
    public static function num_rows(PDOStatement $stmt) {
      if ($stmt instanceof PDOStatement) {
        return count($stmt->fetchAll());
      }
      return 0;
    }

    /**
     * Converts a SQL query with Drupal syntax (%s, %d, %f, %b) to prepared
     * statement syntax
     *
     * @param string $query the incoming query
     * @return string the PDO query with ? placeholders
     */
    public static function _to_pdo_format($query) {
      return str_replace(array('%s', "'%s'", '%d', '%f', '%b'), '?', $query);
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
    $query = db::_to_pdo_format($query);
  }
  if (is_array($args[0])) {
    $args = $args[0];
  }

  //Record time to make query
  error_logging('DEBUG-SQL', $query);
  $watch->reset();
  $stmt = db::query(array($query, $args));
  $time = $watch->stop();
  if ($time > 1 && !DEBUG_MODE) {
    $query = preg_replace('/\\n/', '', $query);
    error_logging("ERROR", "SQL Query Took $time seconds: $query");
  }
  if (!$stmt) {
    $query = preg_replace('/\\n/', '', $query);
    error_logging("ERROR", "SQL query failed: $query");
  }
  return $stmt;
}

/**
 * Fetch associative array from row of recordset
 * @param PDOStatement result of executed query
 * @param int offset of row
 * @return array
 * @ingroup Database
 */
function db_fetch_assoc(PDOStatement $result, $int = false) {
  if ($result && is_int($int)) {
    return $result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST, $int);
  } 
  elseif ($result) {
    return $result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
  }
  return false;
}

/**
 * @return int number of rows
 * @param PDOStatement recordset
 * @ingroup Database
 */
function db_num_rows($rs) {
  return db::num_rows($rs);
}

/**
 * @return object as recordset rows
 * @param recordset
 * @param offset of row
 * @ingroup Database
 */
function db_fetch_object($result, $int = false) {
  if ($result && is_int($int)) {
    return $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_FIRST, $int);
  } 
  elseif ($result) {
    return $result->fetch(PDO::FETCH_OBJ, PDO::FETCH_ORI_NEXT);
  }
  return false;
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
    $query = db::_to_pdo_format($query);
  }
  return db::block_query($query, $args);
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
  $result = db_query($sql);
  return array('success' => $result !== FALSE, 'query' => check_plain($sql));
}



