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
        
        if (substr($class, 0, 20) !== __NAMESPACE__) {
            
            return;
        }
        
        $baseNamespace = str_replace(__NAMESPACE__, '', $class);
        
        $last = strrpos($baseNamespace, '\\');
        
        $className = substr($class, strrpos($class, '\\') + 1);
        
        if ($last === 0) {

            $file = './core/' . $className;

            self::load($file . '.class.php');
        }
        else {
            
            $subNamespace = lcfirst(substr($baseNamespace, 1, $last - 1));
            
            if (method_exists(get_called_class(), $subNamespace)) {
            
                self::$subNamespace($className);
            }
        }
    }

    /**
     * Registers the autoloading for API classes
     * @param type $class Class to load
     */
    private static function api($class) {

        $file = './app/api/' . $class;

        self::load($file . '.api.php');
    }

    /**
     * Registers the autoloading for controller classes
     * @param type $class Class to load
     */
    private static function controllers($class) {

        $file = './app/controllers/' . $class;

        if (!self::load($file . '.controller.php')) {
            
            self::controllerAdmin($class);
        }
    }

    /**
     * Registers the autoloading for model classes
     * @param type $class Class to load
     */
    private static function models($class) {

        $file = './app/models/' . $class;

        self::load($file . '.model.php');
    }

    /**
     * Registers the autoloading for admin controller classes
     * @param type $class Class to load
     */
    private static function controllerAdmin($class) {

        $file = './app/controllers/' . $class;
        $file = str_replace('Admin', '', $file);

        self::load($file . '.admin.controller.php');
    }
    
    /**
     * Registers the autoloading for helper classes
     * @param type $class Class to load
     */
    private static function helpers($class) {
        
        $file = './app/helpers/' . $class;

        self::load($file . '.helper.php');
    }
}