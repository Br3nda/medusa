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

require_once('../lib/request/request.class.php');
require('../lib/response/response.class.php');
require('../lib/authentication/login.class.php');

require_once('simpletest/autorun.php');

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

class TestLogin extends UnitTestCase {
    function testLogin() {
        $response = new response();
        $this->assertFalse(check_credentials('user', 'password', &$userid, &$response));

    }
}

