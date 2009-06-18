<?php
//File: user_timesheet_view.-90.self.php
//Permission: user/timesheet/view
//Weight: -90

class permissions_user_timesheet_view_self extends permissions {
    public function performCheck(&$obj, &$user) {
        if ($obj->getID() == $user->getID())
          return true;
        else
          return false;
    }
}

?>
