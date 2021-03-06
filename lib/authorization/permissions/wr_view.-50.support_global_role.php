<?php
//File: wr_view.-50.support_global_role.php
//Permission: wr/view
//Weight: -50

class permissions_wr_view_support_global_role extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user has the global support role
        $return = false;
        $roles = $user->getRoles();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (strtolower($role['role_name']) == 'support') {
                    //User has the support role
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }
}

?>
