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
    * Base class for all View classes
    */
    abstract class View {
        
        protected $attributes = array();
        
        private $authenticate;
        private $isAdmin;
        
        /**
        * Set default values for the view
        */
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
        
        /**
        * Gets a View property
        */
        public function __get($name) {
            
            return $this->attributes[$name];
        }
        
        /**
        * Sets a property to the View
        */
        public function __set($name, $value) {
            
            $this->attributes[$name] = $value;
        }
        
        /**
        * Prints the View's HTML template
        */
        protected function render() {
            
            echo file_get_contents("./templates/" . $this->attributes['template']);
        }
        
        /**
        * Prints the Javascript files' HTML import tags  
        */
        public function js() {

            foreach ($this->attributes['js'] as $file) {

                echo "\n\t\t";
                echo '<script src="js/' . $file . '.js"></script>';
                
            }
        }
        
        /**
        * Prints the CDN URLs' HTML import tags  
        */
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
        
        /**
        * Prints the meta tags (names and properties)  
        */
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
        
        /**
        * Prints the CSS files' HTML import tags  
        */
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