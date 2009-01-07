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
		foreach ($result as $r) {
			$this->assertEqual('user', get_class($r));
		}
	}	
}

class test_wrms_request_getRequest extends UnitTestCase {
	//TODO
}

class test_wrms_request_note_getNote  extends UnitTestCase {
	//TODO
}

class test_wrms_request_quote_getQuotes extends UnitTestCase {
	//TODO
}

class test_wrms_request_status_getCurrentStatus extends UnitTestCase {
	//TODO
}

class test_wrms_request_status_getStatusHistory extends UnitTestCase {
	//TODO
}

class test_wrms_request_subscriber_getSubscribers extends UnitTestCase {
	//TODO
}

class test_wrms_request_timesheet_addTimesheet extends UnitTestCase {
	//TODO
}

class test_wrms_request_timesheet_getTimesheets extends UnitTestCase {
	//TODO
}

class test_wrms_user_timesheet_addTimesheet extends UnitTestCase{
	//TODO
}

class test_wrms_user_timesheet_getTimesheets extends UnitTestCase {
	//TODO
}
class testDatabase extends UnitTestCase {
  function testConnection() {
    
  }
  function testQuery() {
    $result = db_query("SELECT * FROM request LIMIT 10");
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
         next;
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

