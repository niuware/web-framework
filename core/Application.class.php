<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {
    
    /**
    * Executes the application processing the correct routing
    * and loading/rendering the called view 
    */
    final class Application {
        
        private $router;
        
        private $view = null;
        
        private $language = array();
        
        function __construct() {
            
            $this->initialize();
        }
        
        /**
        * Calls all necessary methods to execute the applicaiton
        */
        private function initialize() {
            
            session_start();
            
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
        * Loads the template for the called view.
        * If it is for public users then is the main template, otherwise
        * loads the admin template.
        */
        private function loadBaseTemplate() {
            
            if (!$this->router->isAdmin()) {
                
                include 'templates/main.template.php';    
            } 
            else {
                
                include 'templates/admin.template.php';
            }
        }
        
        /**
        * Loads all necessary classes to load the called view
        * This method is only called if the server request is  
        * NOT an API call
        */
        private function start() {
            
            spl_autoload_register(__NAMESPACE__ . "\Autoloader::view");
            
            $this->view =  $this->router->getViewInstance();
            
            $this->loadBaseTemplate();
        }
        
        /**
        * Prints an HTML template
        * @param string $template Name of the template file
        */
        private function render($template) {
            
            echo file_get_contents("./templates/" . $template);
        }
        
        /**
        * Returns the prefix for a template
        * @return string Main or Admin
        */
        private function templatePrefix() {
            
            $template = "main";
            
            if ($this->router->isAdmin()) {
                
                $template = "admin";
            }
            
            return $template;
        }
        
        /**
        * Renders a header custom template
        */
        public function renderHeader() {
            
            $this->render($this->templatePrefix() . "-header-template.html");
        }
        
        /**
        * Renders a footer custom template
        */
        public function renderFooter() {
            
            $this->render($this->templatePrefix() . "-footer-template.html");
        }
        
        /**
        * Returns the called view object instance
        * @return View Instance of the view
        */
        public function view() {
            
            return $this->view;
        }
    }    
}