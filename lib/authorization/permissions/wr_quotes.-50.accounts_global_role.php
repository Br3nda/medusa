<?php
//File: wr_quotes.-50.accounts_global_role.php
//Permission: wr/quotes
//Weight: -20

class permissions_wr_quotes_accounts_global_role extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user has the global accounts role
        $return = false;
        $roles = $user->getRoles();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (strtolower($role['role_name']) == 'accounts') {
                    //User has the accounts role
                    $return = true;
                    break;
                }
            }
        }
        return $return;
    }
}

?>
