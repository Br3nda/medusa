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
class wrms_request_addRequest extends wrms_base_method {
    
    function run($params) {

      /*
       * I know this seems backwards, but we check access as one of the last steps
       * We really need the full WR so we can check if the person has enough access,
       * so we will build the WR first then check permissions, then write it to the DB
       */








        $access = access::getInstance();
        $access->permitted('wr/create', $request_id);
          
    

    }
}
