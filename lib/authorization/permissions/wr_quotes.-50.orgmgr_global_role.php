<?php
//File: wr_quotes.-50.orgmgr_global_role.php
//Permission: wr/quotes
//Weight: -50

class permissions_wr_quotes_orgmgr_global_role extends permissions {
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
