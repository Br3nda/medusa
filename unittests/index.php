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
  function AllTests() {
    $this->TestSuite('Medusa WRMS api tests');
    $this->addFile('codestyle.tests.php');
    $this->addFile('render.tests.php');
    $this->addFile('database.tests.php');
    $this->addFile('methods.tests.php');
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




