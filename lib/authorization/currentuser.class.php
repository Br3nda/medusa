<?php
class currentuser extends user {
    // Our singleton
    private static $user = null;

    /*
     * Get the current user, if there isn't one, then set them to be the 'anonymous' user
     */
    public static function getInstance() {
#        if (self::$user == null) {
#            self::$user = new user(null);
#        }
        return self::$user;
    }
    /*
     * Expects a user object
     * @param $usr Valid user object
     */
    public static function set($usr) {
        if (self::$user != null) {
          error_logging('ERROR', "Currentuser class being overwritten");
          return new error('Invalid user.');
        }
        else if (get_class($usr) != 'user') {
          error_logging('ERROR', 'Attempt to run currentuser::set() with class of wrong type; '. get_class($usr));
          return new error('Invalid class use.');
        }
        else if (!$usr->populated) {
          error_logging('ERROR', 'Attempted to use unpopulated class as current user.');
          return new error('Invalid user.');
        }
        else if ($usr->getID() < 1) {
          error_logging('ERROR', 'Attempted to use broken user class as current user. ID;' . $usr->getID());
          return new error('Invalid user.');
        }
        else if ($usr->enabled != 1) {
          error_logging('ERROR', 'Attempted to use disabled user class as current user.');
          return new error('Invalid user.');
        }
        else {
          error_logging('DEBUG', 'currentuser setting new user id; ' . $usr->getID());
          self::$user = $usr;
        }
    }
}
?>
