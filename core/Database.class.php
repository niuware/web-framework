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
use Illuminate\Database\Query\Builder;
    
/**
* Creates a connection to a database using Capsule (Eloquent)
*/
final class Database {

    private static $isLoaded = false;

    /**
     * Connects with the database registered in the settings file
     * @return type
     */
    static function boot() {

        if (self::$isLoaded == true) {
            
            return;
        }
        
        // Create the Eloquent object and attempt a connection to the database
        try {

            $capsule = new Capsule;

            $capsule->addConnection([
                'driver' => Settings::$databases['default']['engine'],
                'host' => Settings::$databases['default']['host'],
                'port' => Settings::$databases['default']['port'],
                'database' => Settings::$databases['default']['schema'],
                'prefix' => Settings::$databases['default']['prefix'],
                'username' => Settings::$databases['default']['user'],
                'password' => Settings::$databases['default']['pass'],
                'charset' => Settings::$databases['default']['charset'],
                'collation' => Settings::$databases['default']['collation']
            ]);

            $capsule->bootEloquent();
            
            $capsule->setAsGlobal();
            
            self::$isLoaded = true;

        } catch (\Exception $e) {

            die("Error 0x102");
        }
    }
    
    /**
     * Returns an Eloquent Builder instance 
     * @param type $tableName Table name from which the instance will be generated
     * @return type
     */
    static function table($tableName) {
        
        return Capsule::table($tableName);
    }
}