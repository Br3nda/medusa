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

if (!is_file('./simpletest/autorun.php')) {
  echo 'You need to download simpletest and extract into unitests folder';
  exit;
}
require('./simpletest/autorun.php');
require('medusa/common.php');


class AllTests extends TestSuite {
  function allTests() {
    $this->TestSuite('Medusa WRMS api tests');
    $this->addFile('codestyle.tests.php');
    $this->addFile('render.tests.php');
    $this->addFile('database.tests.php');
  }
}






// class test_wrms_request_getRequest extends UnitTestCase {
// 
// 

//   
//     //function testgetRequest_xml()
//     //function testgetRequest_json()
//     //function testgetRequest_forbidden()
//   
//   
// }

class wrms_restful_method_testcase extends UnitTestCase {
	
  function testRunning() {
    $method_class = preg_replace('!^test_!', '', get_class($this));
    if ('wrms_restful_method_testcase' == $method_class) {
      return;
    }

    $this->assertTrue(class_exists($method_class), $method_class .' does not exist');
    if (class_exists($method_class)) {
      $params = array();
      $method = new $method_class();
      $result =  $method->run($params);
    }
	}

  function result_okay($result) {
    if (!$this->assertEqual($result->status['code'], 200)) {
      $this->dump($result);
    }
    if (!$this->assertEqual($result->status['message'], 'Success')) {
      $this->dump($result);
    }
  }


}
class test_wrms_request_getRequest extends wrms_restful_method_testcase {
  
  function test_pulling_data() {
    $pg_result = db_query("SELECT * FROM request ORDER BY request_id LIMIT 10");
    $this->assertTrue($pg_result != false, 'Unable to pull database records');
    $this->assertEqual(10, db_num_rows($pg_result));
    while ($row = db_fetch_object($pg_result)) {
      $params['GET']['wr'] = $row->request_id;
      $method = new wrms_request_getRequest();
      $request = $method->run($params);
      //$this->dump($row);
      //$this->dump($request->data['wr']);
      $this->assertTrue(is_object($request));
      
      $this->result_okay($request);

      //array of WRs
      $this->assertTrue(is_array($request->data));

      $this->assertEqual($request->data['wr']->id, $row->request_id);

      foreach (array('request_on', 'active', 'last_status', 'sla_response_hours', 'urgency', 'importance', 'severity_code', 'erquest_id', 'eta', 'last_activity', 'sla_response_time', 'sla_response_type', 'requested_by_date', 'agreeded_due_date', 'request_by', 'breif', 'detailed', 'entered_by', 'system_id', 'parent_request', 'invoice_to'
                   ) as $param) {
        $this->assertEqual($request->data['wr']->$param, $row->$param, $param .' does not match');
      }
    }
  }

}

/**
* wrms.request.allocated.getAllocated
* Gets a list of the people whom this work is currently assigned to.
* Method Arguments Argument Title  Name  Data type
* Work Request ID  wr  int
*/
require('methods/wrms_request_allocated_getAllocated.php');
class test_wrms_request_allocated_getAllocated extends wrms_restful_method_testcase {
  
  function testgetAllocated() {
    //You probably need a session
    $class = new wrms_request_allocated_getAllocated();
    $params = array('GET'=> array('wr' => '58286'));
    $result = $class->run($params);
    
    $this->assertTrue(is_object($result));
    $this->result_okay($result);
    
    
    if (! $this->assertEqual(sizeof($result->data['allocated']), 4, 'Should have 4 allocated people on WR 58286')) {
      $this->dump($result);
    }
    //TODO more tests for data types
  }
}


class test_wrms_login extends wrms_restful_method_testcase {
  //TODO!
}


class test_wrms_request_note_getNotes  extends wrms_restful_method_testcase {
  function testGetNote() {
    $class = new wrms_request_note_getNotes();
    $params = array('GET' => array('wr' => 666));
    $result = $class->run($params);
    $this->result_okay($result);
    if (!$this->assertNotEqual(0, sizeof($result->data['notes']), 'No notes found')) {
      $this->dump($result->data);
    }
  }
}

class test_wrms_request_quote_getQuotes extends wrms_restful_method_testcase {
  function testGetQuotes() {
    $class = new wrms_request_quote_getQuotes();
    $params = array('GET' => array('wr' => '56409'));
    $resylt - $class->run($params);
    
  }
}

class test_wrms_request_status_getCurrentStatus extends wrms_restful_method_testcase {
}

class test_wrms_request_status_getStatusHistory extends wrms_restful_method_testcase {
}

class test_wrms_request_subscriber_getSubscribers extends wrms_restful_method_testcase {
}

class test_wrms_request_timesheet_addTimesheet extends wrms_restful_method_testcase {
}

class test_wrms_request_timesheet_getTimesheets extends wrms_restful_method_testcase {
}

class test_wrms_user_timesheet_addTimesheet extends wrms_restful_method_testcase {
}

class test_wrms_user_timesheet_getTimesheets extends wrms_restful_method_testcase {

}

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
    
    $this->assertEqual($request->get_method(), 'wrms_request_get_request');
    $this->assertEqual($request->get_format(), 'xml');

    $param = $request->get_params();
    $this->assertEqual($param['user'], 'brenda');
    $this->assertEqual(sizeof($param), 1);
    
  }
   
}




/*
class TestLogin extends UnitTestCase {
  function testLogin() {
    //$response = new response();
    //$this->assertFalse(check_credentials('user', 'password', &$userid, &$response));
  }
}*/



function unittest_header($string) {
  $this->dump($string);
}




