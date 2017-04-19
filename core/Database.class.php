<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

use Illuminate\Database\Capsule\Manager as Capsule;
    
/**
* Creates a connection to a database using Capsule (Eloquent)
*/
final class Database {

    private static $capsule = null;
    
    /**
     * Gets the Capsule (Eloquent) connection object
     * @return bool
     */
    private static function isLoaded() : bool {
        
        if (self::$capsule != null) {
            
            if (self::$capsule->getConnection() == null) {
                
                return true;
            }
        }
        
        return false;
    }

    /**
     * Connects with the database registered in the settings file
     * @return type
     */
    static function boot() {

        if (self::isLoaded()) {
            
            return;
        }
        
        // Create the Eloquent object and attempt a connection to the database
        try {

            self::$capsule = new Capsule;

            self::$capsule->addConnection([
                'driver' => Settings::$databases['default']['engine'],
                'host' => Settings::$databases['default']['host'],
                'database' => Settings::$databases['default']['schema'],
                'username' => Settings::$databases['default']['user'],
                'password' => Settings::$databases['default']['pass'],
                'charset' => Settings::$databases['default']['charset'],
                'collation' => 'utf8_unicode_ci'
            ]);

            self::$capsule->bootEloquent();

        } catch (\Exception $e) {

            die("Error 0x102");
        }
    }

}