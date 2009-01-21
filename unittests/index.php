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

$found = false;
foreach(explode(PATH_SEPARATOR, get_include_path()) as $path) {
	if (file_exists($path . '/simpletest/autorun.php')) {
		$found = true;
		break;
	}
}
if (!$found) {
  echo 'You need to download simpletest and extract into unitests folder';
  exit;
}
require('simpletest/autorun.php');
require('medusa/common.php');


class AllTests extends TestSuite {
  function AllTests() {
    $this->TestSuite('Medusa WRMS api tests');
    $dir = $_SERVER['DOCUMENT_ROOT'];
    $this->addFile($dir .'render.tests.php');
    $this->addFile($dir .'database.tests.php');
    $this->addFile($dir .'methods.tests.php');
    $this->addFile($dir .'parsing.tests.php');
    //$this->addFile($dir .'codestyle.tests.php');
    
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




