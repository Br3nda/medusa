<?php
//File: wr.-100.admin_role.php
//Permission: wr
//Weight: -100

class permissions_wr_admin_role extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user is an admin
        //If an admin give all rights
        $return = false;
        $roles = $user->getRoles();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (strtolower($role['role_name']) == 'admin') {
                    //User has the admin role
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }
}

?>
