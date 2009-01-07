<?php
/**
 * @file Unittests
 *
 * @ingroup Unittests
 */

/**
 * @defgroup Unittests
 * 
 * @link http://www.simpletest.org/en/start-testing.html Simple test quick start @endlink
 */
 
 
 
set_include_path(get_include_path() . PATH_SEPARATOR . realpath('../lib/'));

require('medusa/common.php');
require('simpletest/autorun.php');


/**
 * @ingroup Unittests
 */
class TestRequestParsing extends UnitTestCase {
  function testParsing() {
    $request = new Uri_Parser('/search.xml?user=brenda');
    
    $this->assertEqual($request->get_method(), 'search');
    $this->assertEqual($request->get_format(), 'xml');

    $param = $request->get_params();
    $this->assertEqual($param['user'], 'brenda');
    $this->assertEqual(sizeof($param), 1);
    
    
    $request = new Uri_Parser('/wrms.request.get_request.xml?user=brenda');
    
    $this->assertEqual($request->get_method(), 'wrms.request.get_request');
    $this->assertEqual($request->get_format(), 'xml');

    $param = $request->get_params();
    $this->assertEqual($param['user'], 'brenda');
    $this->assertEqual(sizeof($param), 1);
    
  }
   
}

/**
 * wrms.request.allocated.getAllocated 
 * Gets a list of the people whom this work is currently assigned to. 
 * Method Arguments Argument Title 	Name 	Data type 
 * Work Request ID 	wr 	int
 */
 require('methods/wrms_request_allocated_getAllocated.php');
class test_wrms_request_allocated_getAllocated extends UnitTestCase {
	function testgetAllocated() {
		//You probably need a session
		$class = new wrms_request_allocated_getAllocated();
		$params = array('request_id' => '58286');
		$result = $class->run($params);
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4);
		foreach($result as $r) {
			$this->assertEqual('user', get_class($r));
		}
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

/*
class TestLogin extends UnitTestCase {
  function testLogin() {
    //$response = new response();
    //$this->assertFalse(check_credentials('user', 'password', &$userid, &$response));
  }
}*/
