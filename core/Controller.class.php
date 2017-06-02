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
     * Sets the renderer to use for the controller
     * @param string $renderer Should be either 'php' or 'twig'
     */
    public function setRenderer(string $renderer = 'twig') {
        
        if ($renderer === 'twig' || $renderer === 'php') {
            
            $this->renderer = $renderer;
        }
    }
    
    /**
     * Gets the renderer for the controller
     * @return string Either 'php' or 'twig'
     */
    public function getRenderer() {
        
        return $this->renderer;
    }

    /**
     * Renders the view for the controller
     */
    public function render() {
        
        $pathToView = "./views/";
        
        if (!file_exists($pathToView . $this->view)) {

            $this->view = "default.twig.php";
        }
        
        if ($this->renderer === 'twig') {
            
            $this->renderWithTwig($pathToView);
        }
        else {
            
            $phpView = str_replace(".twig", ".php", $this->view);
            
            include ($pathToView . $phpView);
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
        
        $twig->addExtension(new \Twig_Extension_Debug());
        $twig->addExtension(new Extension());
        
        echo $twig->render($this->view, $this->attributes);
    }

    /**
     * Renders the default view for any controller
     * @return type
     */
    public function renderDefault() {
        
        $this->view = '404.view.twig';

        HtmlResponse::getInstance()->render($this);
    }
}