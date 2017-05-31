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
* Base class for all Controller classes
*/
abstract class Controller {

    private $authenticate;
    
    private $isAdmin;
    
    private $renderer;
    
    private $attributes = [];

    /**
    * Set default values for the controller
    */
    function __construct() { 

        $this->title = DEFAULT_TITLE;
        $this->styles = ['default' => ["main"]];
        $this->js = [];
        $this->cdn = [];
        $this->metaTags = [];
        $this->metaProps = [];
        $this->renderer = DEFAULT_RENDERER;

        $this->authenticate = false;
        $this->isAdmin = false;
    }

    /**
     * Gets a controller property
     * @param type $name
     * @return type
     */
    public function __get($name) {

        if (isset($this->attributes[$name])) {
            
            return $this->attributes[$name];
        }
        
        return null;
    }

    /**
     * Sets a property to the controller
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {

        $this->attributes[$name] = $value;
    }

    /**
    * Prints the Javascript HTML import tags
    */
    public function js() {

        foreach ($this->js as $file) {

            echo "\n\t\t";
            echo '<script src="js/' . $file . '.js"></script>';

        }
    }

    /**
    * Prints the CDN URLs' HTML import tags  
    */
    public function cdn() {

        foreach ($this->cdn as $url => $attributes) {

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

        foreach($this->metaTags as $name => $content) {

            echo '<meta name="' . $name . '" content="' . $content . '" />';
            echo "\n\t\t";
        }

        foreach($this->metaProps as $property => $content) {

            echo '<meta property="' . $property . '" content="' . $content . '" />';
            echo "\n\t\t";
        }
    }

    /**
    * Prints the CSS files import tags  
    */
    public function styles() {

        foreach ($this->styles as $path => $fileArray) {

            foreach ($fileArray as $file) {

                echo '<link rel="stylesheet" href="styles/' . $path . '/' . $file . '.css" />';
                echo "\n\t\t";
            }
        }
    }
    
    /**
     * Sets the renderer to use for the controller
     * @param string $renderer Should be either 'php' or 'twig'
     */
    public function setRenderer(string $renderer = 'php') {
        
        $this->renderer = $renderer;
    }

    /**
     * Renders the view for the controller
     */
    public function render() {
        
        $pathToView = "./views/";
        
        if (!file_exists($pathToView . $this->view)) {

            $this->view = "default.view.php";
        }
        
        if ($this->renderer === 'twig') {
            
            $this->renderWithTwig($pathToView);
        }
        else {
            
            include ($pathToView . $this->view);
        }
    }
    
    /**
     * Renders the controller view using Twig Template Engine
     */
    public function renderWithTwig() {
        
        $twigLoader = new \Twig_Loader_Filesystem('./views');
        
        $rendererSettings['cache'] = './cache';
        
        if (DEBUG_MODE === true) {
            
            $rendererSettings['debug'] = true;
            $rendererSettings['strict_variables'] = true;
        }
        
        $twig = new \Twig_Environment($twigLoader, $rendererSettings);
        
        $twigView = str_replace(".php", ".twig", $this->view);
        
        $twig->addExtension(new \Twig_Extension_Debug());
        $twig->addExtension(new Extension());
        
        echo $twig->render($twigView, $this->attributes);
    }

    /**
     * Renders the default view for any controller
     * @return type
     */
    public function renderDefault() {
        
        $this->view = '404.view.php';

        HtmlResponse::getInstance()->render($this);
    }
}