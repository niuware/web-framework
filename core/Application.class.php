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
* Executes the application processing the correct routing
* and loading/rendering the called controller 
*/
final class Application {

    private $router;

    private $controller = null;

    private $language = array();
    
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

        $this->initialize();
    }

    /**
    * Calls all necessary methods to execute the application
    */
    private function initialize() {
        
        Auth::start();

        $this->setLanguage();

        $this->router = new Router();

        $this->start();
    }

    /**
    * Sets the language defined in the application settings file
    */
    private function setLanguage($lang = 'default') {

        $this->language = Settings::$languages[$lang];

        define(__NAMESPACE__ . "\BASE_LANG", $this->language['prefix']);
        define(__NAMESPACE__ . "\DB_LANG", $this->language['db_prefix']);
    }
    
    /**
     * Calls the method associated with the Uri query string if exists,
     * if not, calls the default method
     */
    private function loadController() {
        
        $baseMethodName = $this->router->getControllerAction();
        $methodName = $this->router->getRequestMethod() . $baseMethodName;
        
        if (!method_exists($this->controller, $methodName)) {
            
            $methodName = $baseMethodName;
            
            if (!method_exists($this->controller, $methodName)) {
                    
                $methodName = 'renderDefault';
            }
        }
        
        call_user_func([$this->controller, $methodName], $this->router->getControllerParams());
    }

    /**
    * Loads all necessary classes to load the called controller
    * This method is only called if the server request is  
    * NOT an API call
    */
    private function start() {

        spl_autoload_register(__NAMESPACE__ . "\Autoloader::controller");
        spl_autoload_register(__NAMESPACE__ . "\Autoloader::model");
        
        if ($this->router->isAdmin()) {
            
            spl_autoload_register(__NAMESPACE__ . "\Autoloader::controllerAdmin");
        }

        Database::boot();

        $this->controller = $this->router->getControllerInstance();
        
        if (is_object($this->controller) && 
                get_parent_class($this->controller) == __NAMESPACE__ . '\Controller') {
        
            $this->controller->template = $this->router->getDefaultTemplate();

            $this->loadController();
            
        } else {
            
            die("Error 0x104");
        }
    }

    /**
    * Returns the called controller object instance
    * @return Controller instance
    */
    public function controller() {

        return $this->controller;
    }
}