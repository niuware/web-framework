<?php

namespace App\Config;

/**
 * Defines the routes for the web application
 */
final class Routes {
    
    public static $views = [
        'main' => [
            'home' => ['use' => 'Home', 'login' => false]
            ],
        'admin' => [
            'home' => ['use' => 'Home']
        ]
    ];
}