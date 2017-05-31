<?php
/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

/**
* Configuration for the web application.
* This file may contain secret API keys and database access passwords, 
* so keep this file out of the public scope, for example:
* {web-root}/etc/nwf/settings.php
*/
date_default_timezone_set('UTC');

// Routing
define(__NAMESPACE__ . '\SERVER_ENV_VAR',   \INPUT_SERVER);
define(__NAMESPACE__ . '\BASE_PATH',        '/');
define(__NAMESPACE__ . '\BASE_URL',         'http://localhost/');
define(__NAMESPACE__ . '\BASE_URL_SSL',     'https://localhost/');
define(__NAMESPACE__ . '\BASE_URL_ADMIN',   'http://localhost/admin/');

// General
define(__NAMESPACE__ . '\DEBUG_MODE',           false);
define(__NAMESPACE__ . '\DEFAULT_RENDERER',     'php');
define(__NAMESPACE__ . '\SESSION_ID',           'e5f5d640');
define(__NAMESPACE__ . '\HOMEPAGE',             'home');
define(__NAMESPACE__ . '\HOMEPAGE_ADMIN',       'home');
define(__NAMESPACE__ . '\DEFAULT_TITLE',        'Niuware WebFramework');
define(__NAMESPACE__ . '\DEFAULT_DESCRIPTION',  '');
define(__NAMESPACE__ . '\DEFAULT_KEYWORDS',     '');

final class Settings {

    // Database connection settings
    static $databases = [
        'default' => [
            'engine'    => 'mysql', 
            'schema'    => 'myschema', 
            'user'      => 'root', 
            'pass'      => 'root', 
            'host'      => 'localhost', 
            'charset'   => 'utf8'
        ]
    ];

    // 3rd party apps configuration
    static $apps = [
        'facebook' => [
            'public_name'   => '', 
            'app_id'        => '', 
            'app_secret'    => ''
        ],
        'twitter' => [
            'public_name'           => '',
            'consumer_key'          => '',
            'consumer_secret'       => '',
            'access_token'          => '',
            'access_token_secret'   => ''
        ],
        'youtube' => [
            'public_name'   => '', 
            'api_key'       => '', 
            'client_id'     => '', 
            'client_secret' => ''
        ]
    ];

    // prefix, db_prefix => ISO 639-1 abbrev, code => ISO 639-1 
    static $languages = [
        'default' => [
            'prefix'        => 'en', 
            'db_prefix'     => 'en', 
            'code'          => 'en_US', 
            'date_format'   => 'm/j/Y'
        ],
        'spanish' => [
            'prefix'        => 'es', 
            'db_prefix'     => 'es', 
            'code'          => 'es_MX', 
            'date_format'   => 'j/m/Y'
        ]
    ];
}