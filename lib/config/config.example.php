<?php
/**
 * @file Configuration
*/

/**
 * @ingroup Database database
 */
define("CONFIG_DBTYPE", "pgsql"); # mysql or pgsql
define("CONFIG_DBHOST", "localhost");
define("CONFIG_DBPORT", "5432");
define("CONFIG_DBNAME", "wrms");
define("CONFIG_DBUSER", "general");
define("CONFIG_DBPASS", "");


/**
 * @defgroup Database Database
 * connecting and querying and etc - uses PDO
 */

/**
 * @ingroup General config
 */
define("CONFIG_OUTPUT", "json");
define("CONFIG_MEMCACHE_HOST", "memcache");
define("CONFIG_MEMCACHE_PORT", "11211");
define("CONFIG_MEMCACHE_EXPIRY", "600"); // 10 Minutes
define("DEBUG_MODE", false); // Set to true to enable debug functions
define("AUTHORIZE_FREE_ACCESS", false); //Set to true to allow anon users to access all methods
define("AUTHORIZE_ALLOW_ALL", false); //Set to true to allow all logged users to access all methods
