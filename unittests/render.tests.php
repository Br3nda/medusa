<?php
class renderertest extends UnitTestCase {
  
  function testErrorRender() {
    
    $result = new error("wrms_fail does not exist");
    $response_renderer = response_renderer::getInstance();
    $response_renderer->set_format('xml');
    
    $xml = $response_renderer->render($result);
    
    $xml = preg_replace('!\s!', '', $xml);
    $correct = preg_replace('!\s!', '', "<error><status><message>wrms_fail does not exist</message><code>400</code></status><data></data></error>");
    if (!$this->assertEqual($correct, $xml)) {
      //what we g
      $this->dump('I got this: '. $xml);
      $this->dump('I expected this: '. $correct);
    }
  }
  
	//TODO need more tests
}
