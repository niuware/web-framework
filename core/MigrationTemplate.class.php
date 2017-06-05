<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

use Phinx\Migration\AbstractTemplateCreation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrationTemplate extends AbstractTemplateCreation {
    
    public function __construct(InputInterface $input = null, OutputInterface $output = null) {
        
        parent::__construct($input, $output);
    }
    
    public function getMigrationTemplate() {
        
        $template = 
<<<EOD
<?php

use \$useClassName;
use Illuminate\Database\Schema\Blueprint;

class \$className extends \$baseClassName
{
    /**
     * Illuminate\Database\Schema\Builder \$schema
     * Use the \$schema object to execute your migration queries
     */
    
    public function up()
    {
        
    }
    
    public function down()
    {
        
    }
}
EOD;
        return $template;
    }
    
    public function postMigrationCreation($migrationFilename, $className, $baseClassName) {
    }
}