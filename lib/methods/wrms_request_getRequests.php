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
     *    - The request object on success
     *    - Error message if access is denied, or wr was not filled.
     */
    function run($params) {
      $access = access::getInstance();

      if ($params['GET']['wr'] == null) {
        error_logging('WARNING', "No work request number (wr) provided.");
        return new error('No work request number (wr) provided.');
      }
      if (!preg_match('/^(\d+)(,\d+)*$/',$params['GET']['wr'])) {
        error_logging('WARNING', 'Provided work request (wr) of; "'. $params['GET']['wr'] .'" argument does not match required format.');
        return new error('Bad work request (wr) argument. Argument must be in the format of one or more integers seperated by commas.');
      }

      $response = new response('Success');
      $sql = 'SELECT * FROM request WHERE request_id IN (' . $params['GET']['wr']  . ')';
      $result = call_user_func_array('db_query', $sql);
      while ($row = db_fetch_object($result)) {
        if ($access->permitted('wr/view', $row->request_id)) {
          $object = new WrmsWorkRequest();
          $object->populate($row);
          $object->populateChildren();
          $response->data[] = $object;
        } else {
          $response->data[] =  new error('You cannot access this work request.',403); # EKM TODO add id not allowed option
        }
      }
      return $response;
    }
}
