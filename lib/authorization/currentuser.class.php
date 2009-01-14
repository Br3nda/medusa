<?php
class currentuser extends user {
    // Our singleton
    private static $user = null;

    /*
     * Get the current user, if there isn't one, then set them to be the 'anonymous' user
     */
    public static function getInstance() {
        if (self::$user == null) {
            self::$user = new user(null);
        }
        return self::$user;
    }
    /*
     * Expects a user object
     */
    public static function set($usr) {
            self::$user = $usr;
    }
}
?>
