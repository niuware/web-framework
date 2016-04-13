<?php

namespace Niuware\WebFramework {
    
    class Autoloader {
        
        public static function load($filename) {
            
            if (!file_exists($filename))
            {
                return false;
            }
            
            require_once $filename;
        }
        
        public static function core($class) {
            
            $file = './core/' . substr($class, strrpos($class, '\\') + 1);
            
            self::load($file . '.class.php');
        }
        
        public static function api($class) {
            
            $file = './api/' . str_replace("Api", "", substr($class, strrpos($class, '\\') + 1));
            
            self::load($file . '.api.php');
        }
        
        public static function view($class) {
            
            $file = './views/' . substr($class, strrpos($class, '\\') + 1);;
            
            self::load($file . '.view.php');
        }
        
        public static function model($class) {
            
            $file = './models/' . substr($class, strrpos($class, '\\') + 1);;
            
            self::load($file . '.model.php');
        }
    }
}