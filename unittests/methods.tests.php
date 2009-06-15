<?php
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
      if (!$this->assertTrue(is_object($result), get_class($this) .': Methods should always return an object.')) {
        $this->dump($result);
      }
      
      if (!$this->assertTrue(get_class($result) == 'error' || get_class($result) == 'response',
                             'Methods must return an Error or a Response object. '. get_class($this) .' returned a '. get_class($result))) {
           $this->dump($result);
      }
    
    }
  }
  
  function result_okay($result) {
    
    if (!$this->assertNotNull($result, 'Result cannot be null')) {
      return false;
    }
    
    if (!$this->assertEqual($result->status['code'], 200)) {
      $this->dump($result);
      return false;
    }
    if (!$this->assertEqual($result->status['message'], 'Success')) {
      $this->dump($result);
      return false;
    }
    return true;
  }
  
  
}


class test_wrms_request_getRequest extends wrms_restful_method_testcase {
  
  function test_pulling_data() {
    $pg_result = db_query("SELECT * FROM request ORDER BY request_id LIMIT 10");
    $this->assertTrue($pg_result != false, 'Unable to pull database records');
    $this->assertEqual(10, db_num_rows($pg_result));
    
    while ($row = db_fetch_object($pg_result)) {
      
      $method = new wrms_request_getRequests();
      
      $params = array('GET' => array('wr' => $row->request_id));
      $request = $method->run($params);
           //$this->dump($row);
      //$this->dump($request->data['wr']);
      $this->assertTrue(is_object($request));
      
      if (!$this->result_okay($request)) {
        $this->dump($params);
      }
      
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
    foreach($result->data['allocated'] as $user) {
    	$allocated[] = $user->fullname;
    }
    
    //check the four of us are in the array
    foreach(array('Brenda Wallace', 'Ben Bradshaw', 'Josh Schmidt', 'Edward Murrell') as $name) {
    	if (!$this->assertTrue(in_array($name, $allocated), $name .' should be in array')) {
    		$this->dump($allocated);
    	}
    }
  }
}


class test_wrms_login extends wrms_restful_method_testcase {
  //TODO!
  function testLogin() {
    $class = new wrms_login();
    $params = array();
  }
}


class test_wrms_request_note_getNotes  extends wrms_restful_method_testcase {
  function testGetNote() {
    $wr_no = '666';
    
    $class = new wrms_request_note_getNotes();
    $params = array('GET' => array('wr' => $wr_no));
    $result = $class->run($params);
    $this->result_okay($result);
    if (!$this->assertNotEqual(0, sizeof($result->data['notes']), 'No notes found')) {
      $this->dump($result->data);
    }

    $this->assertNotNull($result);
    $this->assertTrue(is_array($result->status));
    $this->assertTrue(is_array($result->data));
    $this->assertTrue(is_array($result->data['notes']));
    $this->assertTrue(sizeof($result->data['notes']) >= 20, 'At least 20 notes');
    
    foreach ($result->data['notes'] as $note) {
      $this->assertEqual(get_class($note), 'WrmsRequestNote');
      $this->assertNotNull($note->id);
      $this->assertEqual($note->request_id, $wr_no);
    }
    
    $good = true;
    $good = $good && $this->assertEqual($result->data['notes'][0]->id, 299);
    $good = $good && $this->assertEqual($result->data['notes'][0]->note_on, '2006-12-05 13:40:07.949311');
    $good = $good && $this->assertEqual($result->data['notes'][0]->note_by, '');
    $good = $good && $this->assertTrue(preg_match('!^The first three are covered. David Zanetti will be in touch regarding firewall questions.!', $result->data['notes'][0]->note_detail));
    
    if (!$good) {
      $this->dump($result->data['notes'][0]);
    }
                     
  }
}

class test_wrms_request_quote_getQuotes extends wrms_restful_method_testcase {
  function testGetQuotes() {
    $class = new wrms_request_quote_getQuotes();
    $params = array('GET' => array('wr' => '56409'));
    $result = $class->run($params);
    $this->result_okay($result);
    //TODO .. this class hasn't been written yet
    
  }
}

class test_wrms_request_status_getCurrentStatus extends wrms_restful_method_testcase {
  function testGetStatus() {
    $class = new wrms_request_status_getCurrentStatus();
    $params = array('GET' => array('wr' => '10'));
    $result = $class->run($params);
    $this->result_okay($result);
    $good = true;
    

    $status = $result->data['status'];
    $good = $good && $this->assertEqual('WrmsStatus', get_class($status));

    $good = $this->assertEqual('2008-07-31 16:03:02.382866', $status->status_on) && $good;
    $good = $this->assertEqual('498', $status->status_by_id) && $good;
    $good = $this->assertEqual('', $status->status_by) && $good;
    $good = $this->assertEqual('F', $status->status_code) && $good;
    if (! $good) {
      $this->signal('Failed', $result);
      $this->dump($result->data);
    }
    
  }
}

class test_wrms_request_status_getStatusHistory extends wrms_restful_method_testcase {
  function testGetStatusHistory() {
    $class = new wrms_request_status_getCurrentStatus();
    $params = array('GET' => array('wr' => '58286'));
    $result = $class->run($params);
    $this->result_okay($result);
    //$this->signal('wrms_request_status_getStatusHistory code not complete', $result);
    //TODO .. this class hasn't been written yet
  }
    
}

class test_wrms_request_subscriber_getSubscribers extends wrms_restful_method_testcase {
  function testGetSubscribers() {
    $class = new wrms_request_subscriber_getSubscribers();
    $params = array('GET' => array('wr' => '10'));
    $result = $class->run($params);
    $this->result_okay($result);
  }

}

class test_wrms_request_timesheet_addTimesheet extends wrms_restful_method_testcase {
  function testaddTimesheet() {
    $class = new wrms_request_status_getCurrentStatus();
    $params = array('GET' => array('wr' => ''));
    $result = $class->run($params);
    
  }
}

class test_wrms_request_timesheet_getTimesheets extends wrms_restful_method_testcase {
  function testGetTimesheets() {
    $class = new wrms_request_status_getCurrentStatus();
    $params = array('GET' => array('wr' => ''));
    $result = $class->run($params);
    $this->result_okay($result);
    
    //TODO
    
  }
}





