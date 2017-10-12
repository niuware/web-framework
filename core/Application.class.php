<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

require 'app/config/settings.php';
require 'core/Autoloader.class.php';
require 'vendor/autoload.php';
    
/**
* Executes the application processing the correct routing
* and loading/rendering the called controller 
*/
final class Application {

    private $router;

    private $controller = null;

    private $language = [];
    
    /**
     * Returns the singleton for the class
     */
    public static function getInstance() {
        
        static $instance = null;
        
        if ($instance === null) {
            
            $instance = new Application();
        }
        
        return $instance;
    }

    /**
     * Initializes the application
     */
    private function __construct() {
        
        spl_autoload_register(null, false);
        spl_autoload_extensions('.class.php .controller.php .model.php .api.php .helper.php');
        spl_autoload_register(__NAMESPACE__ . "\Autoloader::core");
    }

    /**
    * Calls all necessary methods to execute the application
    */
    public function run() {
        
        Auth::start();

        $this->setLanguage();

        $this->router = new Router();
        
        try {

            $this->start();
        }
        catch (FrameworkException $exception) {
            
            echo $exception->renderAll();
        }
    }
    
    /**
    * Initialize the console mode
    */
    public function console() {
        
        if (\App\Config\CONSOLE_MODE === 'terminal' || \App\Config\CONSOLE_MODE === 'enabled') {

            $command = $_SERVER['argv'];

            if ($command !== null) {

                $console = new Console($command);

                exit($console->getResult());
            }
        }
        else {
            
            echo "Niuware WebFramework console is disabled.\n";
            
            exit;
        }
    }

    /**
    * Sets the language defined in the application settings file
    */
    private function setLanguage($lang = 'default') {

        $this->language = \App\Config\Settings::$languages[$lang];

        define("App\Config\BASE_LANG", $this->language['prefix']);
        define("App\Config\DB_LANG", $this->language['db_prefix']);
    }
    
    /**
     * Calls the method associated with the Uri query string if exists,
     * if not, calls the default method
     */
    private function loadController() {
        
        $baseMethodName = str_replace(['-', '_'], '', $this->router->getControllerAction());
        
        $methodPrefix = $this->router->getRequestMethod();
        
        if ($methodPrefix === null) {
            
            header('HTTP/1.0 405 Method Not Allowed');
            
            exit;
        }
        
        $methodName = $methodPrefix . $baseMethodName;
        
        if (!method_exists($this->controller, $methodName)) {
            
            $methodName = $baseMethodName;
            
            if (!method_exists($this->controller, $methodName)) {
                    
                return $this->methodNotFound();
            }
        }
        
        return $this->executeController($methodName);
    }
    
    /**
     * Throws the method not found FrameworkException
     * @throws FrameworkException
     */
    private function methodNotFound() {
        
        $rootMethodName = str_replace(['get', 'post'], '', $this->router->getControllerAction());
        $reason = "";

        if ($this->router->getRequestMethod() === 'get') {

            $reason = "'get" . strtolower($rootMethodName) . "()' or 'get" . ucfirst($rootMethodName);
        }
        elseif ($this->router->getRequestMethod() === 'post') {

            $reason = "'post" . strtolower($rootMethodName) . "()' or 'post" . ucfirst($rootMethodName);
        }

        throw new FrameworkException("There is no method called '$rootMethodName()' or " . $reason . "()'.", 100);
    }
    
    /**
     * Executes the method on the controller
     * @param type $methodName
     * @throws FrameworkException
     */
    private function executeController($methodName) {
        
        $reflectionMethod = new \ReflectionMethod($this->controller, $methodName);
        
        if ($reflectionMethod->isPublic()) {
            
            try {
                
                $redirectTo = $reflectionMethod->invoke($this->controller, $this->router->getControllerParams());
                
                $this->router->redirect($redirectTo);
            }
            catch (\ReflectionException $exception) {
                
                throw new FrameworkException("Invocation of method '$methodName' failed.", 103, $exception);
            }
            catch (\Twig_Error_Runtime $exception) {
                
                throw new FrameworkException("Twig exception found when rendering '$methodName'().", 107, $exception);
            }
            catch (\Exception $exception) {
                
                throw new FrameworkException("Render of '$methodName' for the controller '{$this->router->getControllerName()}' failed.", 102, $exception);
            }
        }
        else {
            
            throw new FrameworkException("No callable method with the name '$methodName' was found.", 105);
        }
    }

    /**
    * Loads all necessary classes to load the called controller
    * This method is only called if the server request is  
    * NOT an API call
    */
    private function start() {

        Database::boot();

        try {
            
            $this->controller = $this->router->getControllerInstance();
        }
        catch (\Exception $exception) {
            
            throw new FrameworkException($exception->getMessage(), $exception->getCode());
        }
        
        $this->controller->view = $this->router->getDefaultView();

        $this->loadController();
    }
}