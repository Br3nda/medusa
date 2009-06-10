<?php
/**
 * wrms.user.whoAmI
 * Returns some basic info about the current user.
 */
class wrms_user_whoAmI extends wrms_base_method {
    /**
     * Returns some basic info about the current user.
     * 
     * @param $params - ignored, filled for API concurrency.
     * @return response array with username, id, fullname
     * 
     */
    function run($params) {
        if (currentuser::getInstance() != null) {
            $response = new response('Success');
          $user = currentuser::getInstance();
            $response->set('name', $user->fullname);
            $response->set('user', $user->getUsername());
            $response->set('id', $user->getID());
            return $response;
        }
        else {
            return new error('Access denied', 403);
        }
    }
}
