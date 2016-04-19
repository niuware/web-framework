<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {
    
    /**
    * Defines static methods for autoloading 
    * core, api, view and model classes independently
    */
    class Autoloader {
        
        /**
        * Loads the requested file if exists
        */
        public static function load($filename) {
            
            if (!file_exists($filename))
            {
                return false;
            }
            
            require_once $filename;
        }
        
        /**
        * Registers the autoloading for core classes
        */
        public static function core($class) {
            
            $file = './core/' . substr($class, strrpos($class, '\\') + 1);
            
            if (!self::load($file . '.class.php')) {
                
                self::load($file . '.interface.php');
            }
        }
        
        /**
        * Registers the autoloading for api classes
        */
        public static function api($class) {
            
            $file = './api/' . str_replace("Api", "", substr($class, strrpos($class, '\\') + 1));
            
            self::load($file . '.api.php');
        }
        
        /**
        * Registers the autoloading for view classes
        */
        public static function view($class) {
            
            $file = './views/' . substr($class, strrpos($class, '\\') + 1);;
            
            self::load($file . '.view.php');
        }
        
        /**
        * Registers the autoloading for model classes
        */
        public static function model($class) {
            
            $file = './models/' . substr($class, strrpos($class, '\\') + 1);;
            
            self::load($file . '.model.php');
        }
    }
}