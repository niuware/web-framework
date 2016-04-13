<?php 

namespace Niuware\WebFramework {
    
    class Application {
        
        private $router;
        
        private $view = null;
        
        private $language = array();
        
        function __construct() {
            
            $this->initialize();
        }
        
        private function initialize() {
            
            session_start();
            
            $this->setLanguage();
            
            $this->router = new Router();
        }
        
        private function setLanguage($lang = 'default') {

            $this->language = Settings::$languages[$lang];

            define(__NAMESPACE__ . "\BASE_LANG", $this->language['prefix']);
            define(__NAMESPACE__ . "\DB_LANG", $this->language['db_prefix']);
        }
        
        private function loadBaseTemplate() {
            
            if (!$this->router->isAdmin()) {
                
                include 'templates/main.template.php';    
            } 
            else {
                
                include 'templates/admin.template.php';
            }
        }
        
        public function start() {
            
            spl_autoload_register(__NAMESPACE__ . "\Autoloader::view");
            
            $this->view =  $this->router->getViewInstance();
            
            $this->loadBaseTemplate();
        }
        
        public function view() {
            
            return $this->view;
        }
    }    
}