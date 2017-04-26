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
* Defines static methods for autoloading 
* core, API, controller and model classes independently
*/
class Autoloader {

    /**
     * Loads the requested file if exists
     * @param type $filename File to load
     * @return boolean
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
     * @param type $class Class or Interface to load
     */
    public static function core($class) {

        $file = './core/' . substr($class, strrpos($class, '\\') + 1);

        if (!self::load($file . '.class.php')) {

            self::load($file . '.interface.php');
        }
    }

    /**
     * Registers the autoloading for API classes
     * @param type $class Class to load
     */
    public static function api($class) {

        $file = './api/' . strtolower(substr($class, strrpos($class, '\\') + 1));

        self::load($file . '.api.php');
    }

    /**
     * Registers the autoloading for controller classes
     * @param type $class Class to load
     */
    public static function controller($class) {

        $file = './controllers/' . substr($class, strrpos($class, '\\') + 1);

        self::load($file . '.controller.php');
    }

    /**
     * Registers the autoloading for model classes
     * @param type $class Class to load
     */
    public static function model($class) {

        $file = './models/' . substr($class, strrpos($class, '\\') + 1);

        self::load($file . '.model.php');
    }

    /**
     * Registers the autoloading for admin controller classes
     * @param type $class Class to load
     */
    public static function controllerAdmin($class) {

        $file = './controllers/' . substr($class, strrpos($class, '\\') + 1);
        $file = str_replace('Admin', '', $file);

        self::load($file . '.admin.controller.php');
    }
    
    /**
     * Registers the autoloading for helper classes
     * @param type $class Class to load
     */
    public static function helpers($class) {
        
        $file = './helpers/' . substr($class, strrpos($class, '\\') + 1);

        self::load($file . '.helper.php');
    }
}