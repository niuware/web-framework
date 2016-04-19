<?php
 
/**
* Niuware WebFramework
* Settings class
*
* General configuration for the web application.
* This file may contain secret API keys and database access passwords, 
* so keep this file out of the public scope, for example:
* {web-root}/etc/nwf/Settings.php
*
*/
namespace Niuware\WebFramework {
    
    date_default_timezone_set('UTC');
    
    // Routing
    define(__NAMESPACE__ . "\SERVER_ENV_VAR",   \INPUT_SERVER);
    define(__NAMESPACE__ . "\BASE_PATH",        "/");
    define(__NAMESPACE__ . "\BASE_URL",         "http://localhost/");
    define(__NAMESPACE__ . "\BASE_URL_SSL",     "https://localhost/");
    define(__NAMESPACE__ . "\BASE_URL_ADMIN",   "http://localhost/admin/");
    
    // General
    define(__NAMESPACE__ . "\SESSION_ID",           "e5f5d640");
    define(__NAMESPACE__ . "\HOMEPAGE",             "home");
    define(__NAMESPACE__ . "\HOMEPAGE_ADMIN",       "home");
    define(__NAMESPACE__ . "\DEFAULT_TITLE",        "Niuware WebFramework");
    define(__NAMESPACE__ . "\DEFAULT_DESCRIPTION",  "");
    define(__NAMESPACE__ . "\DEFAULT_KEYWORDS",     "");
    
    final class Settings {
        
        // string : path => array(fileBaseName, authenticate = false)
        static $views = array(

            "main" => array("home" => array("Home", false)),
            "admin" => array()
        );
        
        // Database connection configuration
        static $databases = array(
            "default" => array(
                "engine" => "mysql", 
                "schema" => "", 
                "user" => "", 
                "pass" => "", 
                "host" => "host=localhost", 
                "charset" => "charset=UTF8"
            )
        );
        
        // 3rd party apps configuration
        static $apps = array();
        
        // prefix, db_prefix => ISO 639-1 abbrev, code => ISO 639-1 
        static $languages = array(
            "default" => array(
                "prefix" => "en", 
                "db_prefix" => "en", 
                "code" => "en_US", 
                "date_format" => "j/m/Y"
            )
        );
    }
}