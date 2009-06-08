<?php
//File: wr_quote_add.-10.support_global_role.php
//Permission: wr/quote
//Weight: -10

class permissions_wr_quote_add_support_global_role extends permissions {
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
