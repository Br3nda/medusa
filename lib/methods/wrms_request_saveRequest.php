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
class wrms_request_saveRequest extends wrms_base_method {
    
    function run($params) {

      /*
       * I know this seems backwards, but we check access as one of the last steps
       * We really need the full WR so we can check if the person has enough access,
       * so we will build the WR first then check permissions, then write it to the DB
       */
        
        // WR number - If present, this is an edit, if not it's a create
        $wr = $params['GET']['wr']; 
        $brief = $params['GET']['brief']; 
        $org = $params['GET']['org']; 
        $person = $params['GET']['person']; 
        $sys = $params['GET']['sys']; 
        $type = $params['GET']['type']; 
        $urgency = $params['GET']['urgency']; 
        $importance = $params['GET']['importance']; 
        $requested_by = $params['GET']['requested_by']; 
        $agreed_due = $params['GET']['agreed_due']; 
        $invoice_to = $params['GET']['invoice_to']; 
        $details = $params['GET']['details']; 

/*
 * Other things you can do to a WR that will need implementing
 * Add files
 * Add Quotes
 * Link WRs
 * Subscribe people
 * Allocate to people
 * Assign a tag
 * Add a QA action
 * Add a Note - (preserve HTML)
 * Change the status
 * Select Quiet update
 */

        if (isset($wr) && is_numeric($wr)) {
            // We are editing a WR
        }
        else {
            $wr = new WrmsWorkRequest();
            //$urgency, $importance, $type, $person, $brief, $details, $sys)
            return $wr->create($urgency, $importance, $type, $person, $brief, $details, $sys);
        }


        $access = access::getInstance();
        if ($access->permitted('wr/create', $wr)) {
            return new response('Access granted');
        }
        else {
            return new error('You cannot add a WR for this system.', '403');
        }
          
    

    }
}
