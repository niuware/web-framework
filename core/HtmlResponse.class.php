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
 * Renders a response based on an HTML view
 */
final class HtmlResponse extends Response {
    
    private $controller;
    
    /**
     * Returns the singleton of the class
     * @staticvar type $instance
     * @return \Niuware\WebFramework\HtmlResponse
     */
    public static function getInstance() {
        
        static $instance = null;
        
        if ($instance === null) {
            
            $instance = new HtmlResponse();
        }
        
        return $instance;
    }

    /**
     * Renders the header for the current session
     */
    private function renderHeader() {

        if (!Auth::useAuth('admin')) {

            include 'views/main-header.view.php';
        } 
        else {

            include 'views/admin-header.view.php';
        }
    }
    
    /**
     * Renders the view for the passed controller
     * @param \Niuware\WebFramework\Controller $controller
     */
    public function render(Controller &$controller) {
        
        $this->controller = $controller;
        
        self::renderHeader();
        
        $this->controller->render();
        
        self::renderFooter();
    }
    
    /**
     * Renders the footer for the current session
     */
    private function renderFooter() {
        
        if (!Auth::useAuth('admin')) {

            include 'views/main-footer.view.php';    
        } 
        else {

            include 'views/admin-footer.view.php';
        }
    }
}