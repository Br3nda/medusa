<?php

include("../lib/memcache/memcache.class.php");
include("../lib/config/general.php");

class memcache {
    private static $memcache = null;

    private function connect() {
        if (self::$memcache == null) {
            self::$memcache = new MemCachedClient(array(CONFIG_MEMCACHE_HOST.':'.CONFIG_MEMCACHE_PORT));
        }        
    }

    function set($key, $data, $exptime=CONFIG_MEMCACHE_EXPIRY, $forcehost=false) {
        self::connect();
        return self::$memcache->set($key, $data, $exptime, $forcehost);       
    }

    function get($keys,$forcehost=false) {
        self::connect();
        return self::$memcache->get($keys, $forcehost);
    }

    function report_last_error() {
        return "Memcache error (" . self::$memcache->errno.") ". self::$memcache->errstr;
    }

}

?>
