<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

final class Console {
    
    private $command;
    
    private $commandOption;
    
    private $commandArgs;
    
    private $result;
    
    public function __construct($input) {
        
        $this->initialize($input);
    }
    
    public function getResult() {
        
        return $this->result;
    }
    
    private function initialize($input) {
        
        $this->command = $input[1] ?? null;
        $this->commandOption = $input[2] ?? null;
        
        if ($this->command === null) {
            
            echo "Did you forgot to write the command?\n";
        }
        else if ($this->commandOption === null) {
            
            echo sprintf("The comand '%s' is missing the action.\n", $this->command);
        }
        else {
        
            $this->setCommandArgs($input);
            
            $this->executeCommand();
        }
    }
    
    private function setCommandArgs($input) {
        
        $this->commandArgs = [];
        
        if (count($input) > 2) {
            
            $this->commandArgs = array_slice($input, 2);
        }
    }
    
    private function executeCommand() {
                
        switch ($this->command) {
            
            case 'migrations' :
                $migration = new MigrationManager($this->commandOption, $this->commandArgs);
                
                $this->result = $migration->getResult();
            break;
            default: 
                echo sprintf("Command '%s' does not exist.\n", $this->command);
            break;
        }
    }
}
