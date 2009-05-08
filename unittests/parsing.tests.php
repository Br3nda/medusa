<?php
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