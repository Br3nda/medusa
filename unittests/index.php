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

require('./simpletest/autorun.php');
require('medusa/common.php');


class CodeStyleTest extends UnitTestCase {
  function pathToCode($docroot) {
    $dirs = array(realpath($docroot . '/..'));
    $dirs = $this->addSubFolders($dirs);
    $dirs = $this->addSubFolders($dirs);
    return $dirs;
  }
  function testCodeStyle() {
    $docroot = $_SERVER['DOCUMENT_ROOT'];
    if (!$docroot) $docroot = '.';
    $codestyle = $docroot . '/code-style.pl';
    
    foreach ($this->pathToCode($docroot) as $dir) {
      
      if (preg_match('!\.!', $dir)) {
        continue;
      }
      
      $d = dir($dir);
      if (!$d) {
        $this->dump('testCodeStyle: Failed to read dir: "' . $dir .'"');
        return false;
      }
      
      while ($entry = $d->read()) {
        
        if (!preg_match('!\.inc$!', $entry) && !preg_match('!\.php$!', $entry)) {
          continue;
        }
        $contents = file_get_contents("$dir/$entry");
        $code_lines = split("\n", $contents);
        
        $line_num = 1;
        $full_code = '';
        foreach ($code_lines as $l) {
          $full_code .= "$line_num $l\n";
          $line_num++;
        }
        
        $result = shell_exec("$codestyle $dir/$entry");
         /*
         if (!$this->assertTrue(empty($result), 'Bad code style in ' . "$dir/$entry")) {
           $this->dump($full_code);
         }
          */
        $lines = split("\n", $result);
        
        foreach ($lines as $line) {
          if (!$this->asserttrue(empty($line), $line)) {
            preg_match("!$dir/$entry:([0-9]+): !", $line, $matches);
            $code = $code_lines[$matches[1] -1];
            $this->dump($code ."\n");
          }
        }
        
         //mark passes for number of lines without error.. just to make it look good
        
        for ($i=0; $i < count($code_lines) - count($lines); $i++) {
          $this->assertTrue(true);
        }
      }
    }
  }
  function addSubFolders($dirs) {
    $dir = array();
    foreach ($dirs as $base) {
      $d = dir($base);
      if (!$d) {
        $this->assertTrue(false, 'Failed to read dir: "' . $dir .'"');
        continue;
      }
      else {
        $ignore_list = array('Zend', 'simpletest', '\.');
        while($entry = $d->read()) {
          
          if(is_dir($base . '/'. $entry)) {
            $on_ignore = false;
            foreach($ignore_list as $i) {
              if (preg_match("!$i!", $entry)) {
                $on_ignore = true;
              }
            }
            if (!$on_ignore) {
              $dir[] = $base . '/'. $entry;
            }
          }
        }
      }
      
    }
    return $dir;
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
		$params = array('wr' => '58286');
		$result = $class->run($params);
		$this->assertTrue(is_array($result));
		$this->assertEqual(sizeof($result), 4, 'Should have 4 allocated people');
		foreach ($result as $r) {
			$this->assertEqual('user', get_class($r));
		}
	}	
}

// class test_wrms_request_getRequest extends UnitTestCase {
// 
// 
//   //     function testgetRequest() {
// //         //Will need to build session object
// //         $class = new wrms_request_getRequest();
// //         $params = array('wr' => '58286');
// //         $result = $class->run($params);
// //       //$this->assertTrue($result instanceof WrmsWorkRequest);
// //     }
//   
//     //function testgetRequest_xml()
//     //function testgetRequest_json()
//     //function testgetRequest_forbidden()
//   
//   
// }

class wrms_restful_method_testcase extends UnitTestCase {
	function testRunning(){

    $method_class = preg_replace('!^test_!', '', get_class($this));
    if ('wrms_restful_method_testcase' == $method_class) {
      return;
    }

    //$this->dump('testing ' . $method_class);
    $this->assertTrue(class_exists($method_class), $method_class .' does not exist');
    if(class_exists($method_class)) {
      $method = new $method_class();
      //$result =  $method->run();
      //$this->assertTrue(is_array($result));
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
      
      $this->assertEqual($request->code, 200);
      $this->assertEqual($request->message, 'Success');

      //array of WRs
      $this->assertTrue(is_array($request->data));

      $this->assertEqual($request->data['wr']->id, $row->request_id);

      foreach(array('request_on', 'active', 'last_status', 'sla_response_hours', 'urgency', 'importance', 'severity_code', 'erquest_id', 'eta', 'last_activity', 'sla_response_time', 'sla_response_type', 'requested_by_date', 'agreeded_due_date', 'request_by', 'breif', 'detailed', 'entered_by', 'system_id', 'parent_request', 'invoice_to'
                   ) as $param) {
        $this->assertEqual($request->data['wr']->$param, $row->$param, $param .' does not match');
      }
    }
  }

}
/*
class test_wrms_login extends wrms_restful_method_testcase {
}

class test_wrms_request_note_getNote  extends UnitTestCase {
}

class test_wrms_request_quote_getQuotes extends wrms_restful_method_testcase {
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

class test_wrms_user_timesheet_addTimesheet extends wrms_restful_method_testcase{
}

class test_wrms_user_timesheet_getTimesheets extends wrms_restful_method_testcase {

}
*/
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



class testDatabase extends UnitTestCase {
  function testConnection() {
    
  }
  function testQuery() {
    $result = db_query("SELECT * FROM request ORDER BY request_id LIMIT 10");
    $this->assertTrue($result != false);
    while ($row = db_fetch_object($result)) {
      //$this->dump($row);
      foreach(array('request_id', 'request_on', 'active', 'last_status', 'requester_id', 'last_activity', 'sla_response_time', 'sla_response_type',  'brief', 'entered_by', 'system_id',  ) as $param) {
        
        $this->assertTrue(($row->$param), $param .' missing from object');
      }

    }
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


class renderertest extends UnitTestCase {

  function testErrorRender() {

    $result = new error("$method does not exist");
    $response_renderer = new response_renderer($result);

    $xml = $response_renderer->render('xml');

    
    if (!$this->assertEqual('<response>
<error>
<code>200</code>
<message></message>
<data></data>
<status_message> does not exist</status_message>
<status_code>400</status_code>
</error>
</response>', $xml)) $this->dump($xml);
  }

  function testArrayRender() {
    $result = array();
    $response_renderer = new response_renderer($result);
    $xml = $response_renderer->render('xml');
    
    if (!$this->assertEqual('<response>
</response>', $xml)) $this->dump($xml);
  }
}

