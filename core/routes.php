<?php

/**
* This class is part of the core of Niuware WebFramework.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

/**
 * Defines the routes for the web application
 */
final class Routes {
    
    public static $views = [
        'main' => [
            'home' => ['Home', false]
            ],
        'admin' => [
            'home' => ['Home']
        ]
    ];
}