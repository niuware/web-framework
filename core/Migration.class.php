<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

if (!class_exists('\Phinx\Migration\AbstractMigration')) {
    
    die("ERROR: Add phinx to your composer.json file and run composer to use the Migration functionality.");
}

use Phinx\Migration\AbstractMigration;

/**
 * Base class for migration definition classes
 */
class Migration extends AbstractMigration {

    public $schema;
    
    /**
     * Initializes Eloquent engine
     */
    public function init() {
        
        Database::boot();
        
        $this->schema = \Illuminate\Database\Capsule\Manager::schema();
    }
}