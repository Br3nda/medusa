<?php

/*
* Feed class for wrms_search class
*/
class wrms_search_sql_feed extends wrms_base_method {

    function __construct($search) {
        $this->searchtable = null;
        $this->gettodbfields = array();
        $this->gettodbjoins = array();
        $this->newobject = null;

        switch ($search) {
            case 'request':
                return $this->fillWorkRequest();
                break;
            case 'workrequest':
                return $this->fillWorkRequest();
                break;
            case 'roles':
                return $this->fillRoles();
                break;
            case 'user':
                return $this->fillUser();
                break;
            case 'users':
                return $this->fillUser();
                break;
            case 'organisation':
                return $this->fillOrganisation();
                break;
            default:
                error_logging('WARNING', "Search type $search doesn't exist.");
                break;
        }
    }

    /*
    * Return's the apropriate search table string.
    * If no such report is avaliable, you'll get a null
    */
    public function getSearchTable() {
        return $this->searchtable;
    }
    public function getJoinSQL() {
        return $this->gettodbjoins;        
    }
    public function getWhereFields() {
        return $this->gettodbfields;
    }

    public function getNewObject() {
      return new $this->newobject;
    }

    public function run($input = null) {
        // TODO return fail here.
    }

    /*
    * If a search request is found for workrequests, search for and builds workrequest objects
    * based on the records found.
    */
    private function fillWorkRequest() {
        $this->newobject = 'WrmsWorkRequest';
        $this->searchtable = 'request';

        $this->gettodbfields['requesterusername'] = 'requsrname.username';
        $this->gettodbjoins ['requesterusername'] = 'INNER JOIN usr AS requsrname ON requsrname.user_no=request.requester_id';

        $this->gettodbfields['assignedusername'] = 'assusrname.username';
        $this->gettodbjoins ['assignedusername'] = 'INNER JOIN request_allocated ON request_allocated.request_id=request.request_id INNER JOIN usr AS assusrname ON assusrname.user_no=request_allocated.allocated_to_id';

        $this->gettodbfields['statuscode'] = 'reqstatcode.status_code';
        $this->gettodbjoins ['statuscode'] = 'INNER JOIN request_status AS reqstatcode ON reqstatcode.request_id=request.request_id';

        $this->gettodbfields['currentstatuscode'] = 'request.last_status';
        $this->gettodbjoins ['currentstatuscode'] = '';
    }

    private function fillRoles() {
        $this->newobject = 'WrmsRoles';
        $this->searchtable = 'roles';

        $this->gettodbfields['name'] = 'rmroles.role_name';
        $this->gettodbjoins ['name'] = ''; # EKM, owtf, start again here on Tuesday

        $this->gettodbfields['member'] = 'rlusr.username';
        $this->gettodbjoins ['member'] = 'INNER JOIN role_member AS rlrm ON roles.role_no=rlrm.role_no INNER JOIN usr AS rlusr ON rlrm.user_no=rlusr.user_no';

    }

    private function fillUser() {
        $this->newobject = 'user';
        $this->searchtable = 'usr';

        $this->gettodbfields['name'] = 'usr.fullname';
        $this->gettodbjoins ['name'] = '';

        $this->gettodbfields['username'] = 'usr.username';
        $this->gettodbjoins ['username'] = '';

        $this->gettodbfields['email'] = 'usr.email';
        $this->gettodbjoins ['email'] = '';

    }

    private function fillOrganisation() {
        $this->newobject = 'WrmsOrganisation'; # TODO, this doesn't work
        $this->searchtable = 'organisation';

        $this->gettodbfields['name'] = 'organisation.org_name';
        $this->gettodbjoins ['name'] = '';

        $this->gettodbfields['abbreviation'] = 'organisation.abbreviation';
        $this->gettodbjoins ['abbreviation'] = '';

        $this->gettodbfields['email'] = 'organisation.email';
        $this->gettodbjoins ['email'] = '';

    }

    function __destruct() {
    }
}


