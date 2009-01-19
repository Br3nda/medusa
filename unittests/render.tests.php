<?php
class renderertest extends UnitTestCase {
  
  function testErrorRender() {
    
    $result = new error("$method does not exist");
    $response_renderer = new response_renderer($result);
    
    $xml = $response_renderer->render('xml');
    
    $xml = preg_replace('!\s!', '', $xml);
    $correct = preg_replace('!\s!', '', "<error><status><message>doesnotexist</message><code>400</code></status><data></data></error>");
    if (!$this->assertEqual($correct, $xml)) {
      //what we g
      $this->dump('I got this: '. $xml);
      $this->dump('I expected this: '. $correct);
    }
  }
  
  function testArrayRender() {
    $result = array();
    $response_renderer = new response_renderer($result);
    $xml = preg_replace('!\s!', '', $response_renderer->render('xml'));
    $correct = "<response></response>";
    if (!$this->assertEqual($correct, $xml, 'Render empty response object')) {
      $this->dump('I got: '. $xml);
      $this->dump('I expected: '. $correct);
    }
  }
}