<?php
//File: wr_view.0.common_roles.php
//Permission: wr/view
//Weight: 0

class permissions_wr_view_common_roles extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user is one of the common roles
        $return = false;
        $roles = $user->getRoles();
        if (!empty($roles)) {
            $allowed = array(
                'support',
                'qa',
                'accounts',
                'orgmgr',
            );
            foreach ($roles as $role) {
                $user_role = strtolower($role['role_name']);
                if (in_array($user_role, $allowed)) {
                    //User has an allowed role
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }
}

?>
