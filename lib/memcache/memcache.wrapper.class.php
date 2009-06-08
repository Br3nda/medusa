<?php

require("memcache/memcache.class.php");

class memcached {
    private static $memcache = null;

    /**
     * Connects to the memcache server set in the config
     */
    private function connect() {
        if (self::$memcache == null) {
            self::$memcache = new MemCachedClient(array(CONFIG_MEMCACHE_HOST.':'.CONFIG_MEMCACHE_PORT));
        }        
    }

    /**
     * Sets a key/value pair in memcache
     *
     * @param     $key: The value to set
     * @param     $data: The value set
     * @param     $exptime: Expiration time
     * @param     $forcehost: Not sure, was part of the Memcache Client class
     * @return
     *      Whatever the Memcache Client cache returns
     */
    function set($key, $data, $exptime = CONFIG_MEMCACHE_EXPIRY, $forcehost = false) {
        self::connect();
        return self::$memcache->set($key, $data, $exptime, $forcehost);       
    }

    /**
     * Gets a value from memcache
     * @param $keys: A key or keys (in an array?)
     * @param $forcehost:  Not sure, was part of the Memcache Client class
     * @return
     *       Whatever the Memcache Client cache returns
     */
    function get($keys, $forcehost = false) {
        self::connect();
        return self::$memcache->get($keys, $forcehost);
    }

    /**
     * Reports the last error memcache encountered
     * @return
     *      A string reporting the type of error
     */
    function report_last_error() {
        return "Memcache error (". self::$memcache->errno .") ". self::$memcache->errstr;
    }

}
