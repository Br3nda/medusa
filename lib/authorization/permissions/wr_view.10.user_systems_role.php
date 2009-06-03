<?php
//File: wr_view.10.user_systems_role.php
//Permission: wr/view
//Weight: 10

class permissions_wr_view_user_systems_role extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if user has been assigned to the system
        $return = false;
        $systems = $user->getSystems();
        $system_id = 0;
        if (is_numeric($obj)) {
            //Was passed request ID
            $result = db_query("SELECT system_id FROM request WHERE request_id = %d", $obj);
            if ($result) {
                $info = db_fetch_object($result);
                $system_id = $info->system_id;
            }
        } 
        if (isset($systems[$system_id])) {
            //If the system info exists user must have access to view it
            $return = true;
        }
        return $return;
    }
}

?>
