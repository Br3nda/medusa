<?php
require_once('simpletest/autorun.php');


require('../lib/request/request.class.php');

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