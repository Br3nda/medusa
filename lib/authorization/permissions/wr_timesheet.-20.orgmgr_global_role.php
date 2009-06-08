<?php
//File: wr_timesheet.-20.orgmgr_global_role.php
//Permission: wr/timesheet
//Weight: -20

class permissions_wr_timesheet_orgmgr_global_role extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user has the global OrgMgr role
        $return = false;
        $roles = $user->getRoles();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (strtolower($role['role_name']) == 'orgmgr') {
                    //User has the OrgMgr role
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }
}

?>
