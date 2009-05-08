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
      $requests = explode(',', $params['GET']['wr']); // requested ids
      $response = new response('Success');
      $sql = 'SELECT * FROM request WHERE request_id IN (' . implode(', ', $requests)  . ')';
      while ($row = db_fetch_object($result)) {
        if ($access->permitted('wr/view', $row->id)) {
          $object = new WrmsWorkRequest();
          $object->populate($row);
          $object->populateChildren();
          $response->data[] = $object;
#        } else {
#          $response->data[] =  new error('No work request number (wr) provided.',403); # EKM TODO add id not allowed option
        }
      }
      return $response;
}
