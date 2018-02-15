<?php 
/**
 * ====================================================================================
 *
 *
 * @author Emrul (https://lemexit.com)
 * @link https://lemexit.com 
 * @license https://lemexit.com/license
 * @package PremiumMediaScript
 * @subpackage API Handler
 */
// Database Configuration
  $dbinfo = array(
    "host" => 'localhost',        // Your mySQL Host (usually Localhost)
    "db" => 'wittylab',            // The database where you have dumped the included sql file
    "user" => 'root',        // Your mySQL username
    "password" => '',    //  Your mySQL Password
    "prefix" => 'lit_'      // Prefix for your tables if you are using same db for multiple scripts, e.g. short_
  );

  $config = array(
    // Your Server's Timezone - List of available timezones (Pick the closest): https://php.net/manual/en/timezones.php
    "timezone" => date_default_timezone_get(),
    // Use CDN to host libraries for faster loading
    "cdn" => TRUE,
    // Enable mode_rewrite? e.g. user/login instead of index.php?a=user/login
    "mod_rewrite" => TRUE,    
    // Enable Compression? Makes your website faster
    "gzip" => TRUE,
    /*
     ====================================================================================
     *  Security Key & Token - Please don't change this if your site is live.
     * ----------------------------------------------------------------------------------
     *  - Setup a security phrase - This is used to encode some important user 
     *    information such as password. The longer the key the more secure they are.
     *
     *  - If you change this, many things such as user login and even admin login will 
     *    fail.
     *
     *  - If the two config below don't have any values or have c2a95de457e886d0cd684977bcc7fe4a or 55bc07663299e11074103f57eab3de85, replace these by a random key.
     ====================================================================================
    */
    "security" => 'c2a95de457e886d0cd684977bcc7fe4a',  // !!!! DON'T CHANGE THIS IF YOUR SITE IS LIVE !!!!
    "public_token" => '55bc07663299e11074103f57eab3de85', // This is randomly generated and it is a public key

    "debug" => 2,   // Enable debug mode (outputs errors) - 0 = OFF, 1 = Error message, 2 = Error + Queries (Don't enable this if your site is live!)
    "demo" => 0 // Demo mode
  );

// Include core.php
include ('Core.php');