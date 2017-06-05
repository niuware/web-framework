<?php

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration {

    public $schema;
    
    public function init() {
        
        Database::boot();
        
        $this->schema = \Illuminate\Database\Capsule\Manager::schema();
    }
}