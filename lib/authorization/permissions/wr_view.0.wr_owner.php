<?php
//File: wr_view.0.wr_owner.php
//Permission: wr/view
//Weight: 0

class permissions_wr_view_wr_owner extends permissions {
    public function performCheck(&$obj, &$user) {
        //Check if the user owns the work request
        $return = false;
        if (is_numeric($obj)) {
            //Was passed request ID
            $result = db_query("SELECT requester_id,entered_by FROM request WHERE request_id = %d", $obj);
            if ($result) {
                $info = db_fetch_object($result);
                if ($user->getID() == $info->requester_id) {
                    $return = true;
                } elseif ($user->getID() == $info->entered_by) {
                    $return = true;
                }
            }
        }
        return $return;
    }
}

?>
