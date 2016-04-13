<?php

namespace Niuware\WebFramework {
    
    abstract class View {
        
        protected $attributes = array();
        
        private $authenticate;
        private $isAdmin;
        
        function __construct() { 
            
            $this->attributes = array(
                'title' => constant(__NAMESPACE__ . "\DEFAULT_TITLE"),
                'template' => "default.html",
                'styles' => array("default" => array("main")),
                'js' => array(),
                'cdn' => array(),
                'metaTags' => array(),
                'metaProps' => array()
            );
            
            $this->authenticate = false;
            $this->isAdmin = false;
        }
        
        public function __get($name) {
            
            return $this->attributes[$name];
        }
        
        public function __set($name, $value) {
            
            $this->attributes[$name] = $value;
        }
        
        protected function renderTemplate() {
            
            echo file_get_contents("./templates/" . $this->attributes['template']);
        }
        
        public function js() {

            foreach ($this->attributes['js'] as $file) {

                echo "\n\t\t";
                echo '<script src="js/' . $file . '.js"></script>';
                
            }
        }
        
        public function cdn() {

            foreach ($this->attributes['cdn'] as $url => $attributes) {

                echo "\n\t\t";
                echo '<script src="' . $url . '"';
                
                foreach ($attributes as $attrib => $value) {
                    
                    echo ' ' . $attrib . '="' . $value . '"';
                }
                
                echo '></script>';
                
            }
        }
        
        public function metas() {
            
            foreach($this->attributes['metaTags'] as $name => $content) {
                
                echo '<meta name="' . $name . '" content="' . $content . '" />';
                echo "\n\t\t";
            }
            
            foreach($this->attributes['metaProps'] as $property => $content) {
                
                echo '<meta property="' . $property . '" content="' . $content . '" />';
                echo "\n\t\t";
            }
        }
        
        public function styles() {

            foreach ($this->attributes['styles'] as $path => $fileArray) {

                foreach ($fileArray as $file) {
                    
                    echo '<link rel="stylesheet" href="styles/' . $path . '/' . $file . '.css" />';
                    echo "\n\t\t";
                }
            }
        }
    }
}