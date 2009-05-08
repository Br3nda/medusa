<?php
class testDatabase extends UnitTestCase {
  function testConnection() {
    
  }
  function testQuery() {
    $result = db_query("SELECT * FROM request ORDER BY request_id LIMIT 10");
    $this->assertTrue($result != false);
    while ($row = db_fetch_object($result)) {
      foreach (array('request_id', 'request_on', 'active', 'last_status', 'requester_id', 'last_activity', 'sla_response_time', 'sla_response_type',  'brief', 'entered_by', 'system_id',  ) as $param) {
        
        $this->assertTrue(($row->$param), $param .' missing from object');
      }
      
    }
  }
}
