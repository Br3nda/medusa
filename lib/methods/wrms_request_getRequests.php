<?php
/**
 * @file 
 * @ingroup Methods
 * wrms.request.getRequest
 * Returns the specified request
 */

 /**
  * @ingroup Methods
  * Work Requests
  */
class wrms_request_getRequests extends wrms_base_method {
    /**
     * Performs the fetch of the work request
     *
     * @param $params
     *   Associative array of parameters
     *   - $params->wr: Work Request ID or array of
     *   - $params->user: User ID making the request
     *   @return
     *     The request object on success
     *     FALSE if permission is denied
     *     NULL if no work request
     */
    function run($params) {
      $requests = $params['GET']['wr'];
      if (!is_array($requests)) {
        $requests = array($requests);
      }
      $access = access::getInstance();
      $wr = array();
      foreach ($requests as $request_id) {
        if ($access->canUserSeeRequest($request_id)) {
          $wr[$request_id] = null;
        }
      }
      $placeholders = array();
      $placeholders = array_pad($placeholders, count($wr), '%d');
      $sql = 'SELECT * FROM request WHERE request_id IN (' . implode(', ', $placeholders)  . ')';
      $result = call_user_func_array('db_query', array_unshift($sql, $wr));
      if (!db_num_rows($result)) {
        return new Error('Request does not exist or you do not have permission to view it');
      }
      $response = new response('Success');
      while ($row = db_fetch_objet($result)) {
        $object = new WrmsWorkRequest();
        $object->populate($row);
        $wr[$row->request_id] = $object;
      }
      $response->set('wr', $wr);
      return $response;
    }
}
