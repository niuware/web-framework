<?php

namespace App\Config;

/**
 * Defines the routes for the web application
 */
final class Routes {
    
    public static $views = [
        'main' => [

            'home' => ['use' => 'Home', 'require' => []]
        ],

        'admin' => [

            'login' => ['use' => 'Login@login'],
            'logout' => ['use' => 'Login@logout'],

            'home' => ['use' => 'Home']
        ]
    ];
}