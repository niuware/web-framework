<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

use Phinx\Console\PhinxApplication;
use Phinx\Config\Config;
use Phinx\Console\Command\Create;
use Phinx\Console\Command\Migrate;
use Phinx\Console\Command\Rollback;
use Phinx\Console\Command\Status;
use Phinx\Console\Command\SeedCreate;
use Phinx\Console\Command\SeedRun;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Executes migration commands
 */
final class MigrationManager {
    
    private $command;
    
    private $commandArgs;
    
    private $result;
    
    private $availableCommands = ['create', 'migrate', 'rollback', 'status', 'seedcreate', 'seedrun'];
    
    public function __construct($command, $args = []) {
        
        $this->command = $command;
        $this->commandArgs = $args;
        
        $this->initialize();
    }
    
    /**
     * Return command execution result
     * @return string
     */
    public function getResult() {
        
        return $this->result;
    }
    
    /**
     * Calls the command to execute
     * @return null
     */
    private function initialize() {
        
        if (in_array($this->command, $this->availableCommands)) {
        
            $phinxApp = new PhinxApplication();
            
            $stream = fopen('php://temp', 'w+');
            
            $config = new Config($this->getConfig());
            
            call_user_func([$this, $this->command], $phinxApp, $config, $stream);
            
            $this->result = stream_get_contents($stream, -1, 0);
            
            fclose($stream);
            
            return;
        }
        
        echo sprintf("The option '%s' for the command 'migrations' does not exist.\n", $this->command);
    }
    
    /**
     * Sets the configuration for the migration adapter
     * @return array
     */
    private function getConfig() {
        
        return [
            'paths' => [
                'migrations' => 'app/migrations/migrations',
                'seeds' => 'app/migrations/seeds'
            ],
            'migration_base_class' => 'Niuware\WebFramework\Migration',
            'environments' => [
                'default_migration_table' => 'migrations_log',
                'default_database' => Settings::$databases['default']['schema'],
                Settings::$databases['default']['schema'] => [
                    'adapter' => Settings::$databases['default']['engine'],
                    'host' => Settings::$databases['default']['host'],
                    'name' => Settings::$databases['default']['schema'],
                    'user' => Settings::$databases['default']['user'],
                    'pass' => Settings::$databases['default']['pass'],
                    'port' => Settings::$databases['default']['port']
                ]
            ]
        ];
    }
    
    /**
     * Sets the command arguments if exist
     * @param array $command
     * @param string $argumentShort
     */
    private function setCommandArguments(&$command, $argumentShort = '-t') {
        
        if (count($this->commandArgs) > 2) {
            
            if ($this->commandArgs[1] === $argumentShort && 
                    $this->commandArgs[2] !== '') {
                
                $command[$argumentShort] = $this->commandArgs[2];
            }
        }
    }
    
    /**
     * Creates a migration definition class
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function create(PhinxApplication $app, Config $config, $stream) {
        
        $command = array(
            'command' => 'create',
            'name' => 'V' . time(),
            '--class' => 'Niuware\WebFramework\MigrationTemplate'
        );
        
        $arrayInput = new ArrayInput($command);
        
        $create = new Create();
        
        $create->setApplication($app);

        $create->setConfig($config);
            
        $create->run($arrayInput, new StreamOutput($stream));
    }
    
    /**
     * Executes the migration
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function migrate(PhinxApplication $app, Config $config, $stream) {
        
        $command = [
            'command' => 'migrate'
        ];
        
        $this->setCommandArguments($command);
        
        $arrayInput = new ArrayInput($command);
        
        $migrate = new Migrate();
        
        $migrate->setApplication($app);

        $migrate->setConfig($config);
            
        $migrate->run($arrayInput, new StreamOutput($stream));
    }
    
    /**
     * Rollback a migration
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function rollback(PhinxApplication $app, Config $config, $stream) {
        
        $command = array(
            'command' => 'rollback'
        );
        
        $this->setCommandArguments($command);
        
        // Target date to rollback to
        $this->setCommandArguments($command, '-d');
        
        if (isset($command['-t']) && $command['-t'] === '0') {
            
            // Force rollback
            if (isset($this->commandArgs[3]) && 
                    $this->commandArgs[3] === '-f') {
                
                $command['-f'] = '';
            }
        }
        
        $arrayInput = new ArrayInput($command);
        
        $migrate = new Rollback();
        
        $migrate->setApplication($app);

        $migrate->setConfig($config);
            
        $migrate->run($arrayInput, new StreamOutput($stream));
    }
    
    /**
     * Shows the current migrations status
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function status(PhinxApplication $app, Config $config, $stream) {
        
        $command = array(
            'command' => 'status'
        );
        
        $arrayInput = new ArrayInput($command);
        
        $create = new Status();
        
        $create->setApplication($app);

        $create->setConfig($config);
            
        $create->run($arrayInput, new StreamOutput($stream));
    }
    
    /**
     * Creates a seed migration definition class
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function seedcreate(PhinxApplication $app, Config $config, $stream) {
        
        $command = [
            'command' => 'seed:create',
            'name' => 'Seed' . Security::generateToken(5)
        ];
        
        // Sets an specific seed class name
        if (isset($this->commandArgs[1]) && $this->commandArgs[1] !== '') { 
            
            $command['name'] = $this->commandArgs[1];
        }
        
        $arrayInput = new ArrayInput($command);
        
        $migrate = new SeedCreate();
        
        $migrate->setApplication($app);

        $migrate->setConfig($config);
            
        $migrate->run($arrayInput, new StreamOutput($stream));
    }
    
    /**
     * Runs a seed definition class
     * @param PhinxApplication $app
     * @param Config $config
     * @param Stream $stream
     */
    private function seedrun(PhinxApplication $app, Config $config, $stream) {
        
        $command = [
            'command' => 'seed:run'
        ];
        
        // Target a specific seed class
        $this->setCommandArguments($command, '-s');

        if (isset($command['-s'])) {
            
            $command['-s'] = [$command['-s']];
        }
        
        $arrayInput = new ArrayInput($command);
        
        $migrate = new SeedRun();
        
        $migrate->setApplication($app);

        $migrate->setConfig($config);
            
        $migrate->run($arrayInput, new StreamOutput($stream));
    }
}