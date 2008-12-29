<?php
/**
 * @file Unittests
 *
 * @ingroup Unittests
 */

/**
 * @defgroup Unittests
 * @link http://www.simpletest.org/en/start-testing.html Simple test quick start @endlink
 */


require_once('simpletest/autorun.php');
require_once('../lib/request/request.class.php');

/**
 * @ingroup Unittests
 */
class TestRequestParsing extends UnitTestCase {
  function testParsing() {
    $request = new Request('/search.xml?user=brenda');
    
    $this->assertEqual($request->get_method(), 'search');
    $this->assertEqual($request->get_format(), 'xml');

    $param = $request->get_method();
    $this->assertEqual($param['user'], 'brenda');
    $this->assertEqual(sizeof($param), 1);
  }
    
}

require('../lib/request/request.class.php');

class testSearch extends UnitTestCase {
  function testSearch() {
    //TODO
    //$this->assertTrue(false);
  }
}

class testUser extends UnitTestCase {
  function testStuff() {
    $user = new user($username, $password);
    
  }
}

class testDatabase extends UnitTestCase {
  function testConnection() {
    
  }
  function testQuery() {
    $result = db_query("SELECT * FROM users");
    $this->assertTrue($result != false);
    
  }
}