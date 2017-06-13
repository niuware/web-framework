<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

require_once 'app/config/routes.php';

/**
* Process the URL to the correct route
*/
class Router {

    private $path;

    private $controller;

    private $action;

    private $error = true;

    private $admin = false;
    
    private $requestMethod = null;
    
    private $queryString = array();
    
    private $postParams = array();

    function __construct() {

        $this->initialize();

        $this->redirectFail();
    }

    /**
    * Parse the request URL and executes the routing
    */
    private function initialize() {
        
        $this->setRequestMethod();

        if (BASE_PATH == "/") {

            $currentUri = substr(filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL), 1);
        } else {

            $currentUri = str_replace('/' . BASE_PATH, '', filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));
        }

        $this->path = explode('/', $currentUri);

        $this->action = $this->path[0];
        
        $this->setQueryString();

        // If the URL is associated to a controller, then load it
        if (isset(Routes::$views['main'][$this->path[0]])) {

            $this->redirectMain();

            $this->error = false;

        } else {

            $this->redirectTask($this->path[0]);
        }
    }
    
    private function setQueryString() {
        
        $queryString = filter_input(INPUT_SERVER, 'QUERY_STRING');
        
        if ($queryString!= "") {
            
            parse_str($queryString, $this->queryString);
            
            $this->path[0] = str_replace(['?' . $queryString, $queryString], '', $this->path[0]);
            
            $this->action = $this->path[0];
        }
    }
    
    /**
     * Sets the request method
     */
    private function setRequestMethod() {
        
        $requestMethod = filter_input(SERVER_ENV_VAR, 'REQUEST_METHOD', FILTER_SANITIZE_URL);
        
        if ($requestMethod == 'GET') {
            
            $this->requestMethod = 'get';
            
        } elseif ($requestMethod == 'POST') {
            
            $this->requestMethod = 'post';
            
            $this->postParams = filter_input_array(INPUT_POST);
        }
    }

    /**
    * Executes the routing for controllers (NOT API calls or admin controllers)
    */
    private function redirectMain() {

        if (!$this->requireLogin()) {
            
            Auth::requireAuth(false);

            $this->controller = Routes::$views['main'][$this->path[0]][0];

        } else {

            $this->setRequireAuthMode();
        }
    }

    /**
    * Verify if the controller requires user login
    * @return bool Login required?
    */
    private function requireLogin() : bool {

        $requireLogin = false;

        if (isset(Routes::$views['main'][$this->path[0]][1])) {

            $requireLogin = Routes::$views['main'][$this->path[0]][1];
        }

        return $requireLogin;
    }

    /**
     * Redirects to an API call or admin controller 
     * @param type $action
     * @return type
     */
    private function redirectTask($action) {

        if ($action == "api") {
            
            new HttpInput($this->requestMethod);

            exit;

        } 
        else if ($action == "admin") {

            return $this->setRequireAdminAuthMode();
        }
        else if ($action == "console:nwf") {
            
            if (CONSOLE_MODE === 'web' || CONSOLE_MODE === 'enabled') {
                
                $console = new Console($this->path, 'web');

                exit(nl2br($console->getResult()));
            }
        }
    }
    
    /**
     * Sets the Router as require admin authenticating mode
     */
    private function setRequireAdminAuthMode() {
        
        $this->admin = true;
        
        Auth::requireAuth(true, 'admin');
        
        $this->redirectAuthAdminMode();
    }

    /**
    * Sets the Router as require authenticating mode
    */
    private function setRequireAuthMode() {
        
        Auth::requireAuth(true);

        $this->redirectAuthMode();
    }

    /**
    * Redirects the browser to a default route, if an error was 
    * generated by the routing
    */
    private function redirectFail() {

        if ($this->error) {

            if (!$this->admin) {

                $this->redirectFailMain();

            } else {

                $this->redirectFailAdmin();
            }

            header('HTTP/1.0 403 Forbidden');
            
            exit;
        }
    }
    
    /**
     * Redirects the browser to the default main application route
     */
    private function redirectFailMain() {
        
        if (!empty(Routes::$views['main'])) {
                    
            header("Location: " . BASE_URL . HOMEPAGE);
            
            exit;
        }
    }
    
    /**
     * Redirects the browser to the default admin application route
     */
    private function redirectFailAdmin() {
        
        if (!empty(Routes::$views['admin'])) {
                    
            header("Location: " . BASE_URL_ADMIN . HOMEPAGE_ADMIN);
            
            exit;
        }
    }

    /**
    * Executes the routing for controllers requiring authentication
    */
    private function redirectAuthMode() {
        
        if (!Auth::verifiedAuth()) {

            $this->controller = "Login";
            $this->path[0] = "login";

        } else {

            $this->controller = Routes::$views['main'][$this->path[0]][0];
        }

        $this->error = false;
    }
    
    /**
     * Executes the routing for admin controllers
     */
    private function redirectAuthAdminMode() {

        if (!Auth::verifiedAuth('admin')) {

            $this->controller = "LoginAdmin";
            $this->path[1] = "login";

        } else {

            $this->controller = "";
            
            if (isset($this->path[1])) {
                
                $this->controller = Routes::$views['admin'][$this->path[1]][0] ?? "";
            }
            
            $this->controller.= "Admin";
        }

        $this->error = ($this->controller == "Admin");
    }

    /**
    * Returns a new instance of the requested controller
    * @return Controller instance
    */
    public function getControllerInstance() : Controller {

        $controllerClass = "\Niuware\WebFramework\Controllers\\" . $this->controller;
        
        if (!class_exists($controllerClass)) {
            
            throw new \Exception("The controller class '" . $this->getControllerName() 
                        . "' does not exist.", 106);
        }

        $controllerObject = new $controllerClass;
        
        if (is_object($controllerObject) && 
                get_parent_class($controllerObject) == __NAMESPACE__ . '\Controller') {
            
            return $controllerObject;
        }
        
        throw new \Exception("The controller class '" . $this->getControllerName() 
                    . "' is not an instance of ". __NAMESPACE__ . "\Controller.", 104);
    }

    /**
    * Returns the name of the requested view
    * @return string View name
    */
    public function getControllerName() {

        return $this->controller;
    }

    /**
     * Gets the controller action (method to execute)
     * @return type
     */
    public function getControllerAction() {

        if (!$this->admin) {
            
            return $this->path[0];
        }
        else {
            
            return $this->path[1];
        }
    }

    /**
     * Gets the parameters for the current method (Uri query)
     * @return array
     */
    public function getControllerParams() : HttpRequest {
        
        $pathParams = [];

        if (!$this->admin) {
            
            $pathParams = array_splice($this->path, 1);
        }
        else {
            
            $pathParams = array_splice($this->path, 2);
        }
            
        $allParams = array_merge($pathParams, $this->queryString);
        
        if ($this->requestMethod == 'post' && 
                $this->postParams != null) {
            
            $allParams = array_merge($allParams, $this->postParams);
        }
        
        return new HttpRequest($allParams);
    }
    
    /**
     * Returns true if the current routing requires admin validation
     * @return bool
     */
    public function isAdmin() : bool {
        
        return $this->admin;
    }
    
    /**
     * Gets the request method
     * @return string
     */
    public function getRequestMethod() : string {
        
        return $this->requestMethod;
    }
    
    /**
     * Gets a default view name based on the requested path
     * @return string
     */
    public function getDefaultView() : string {
        
        $viewName = $this->getControllerAction();
        
        if (!$this->admin) {
            
            $viewName.= ".view.twig";
        }
        else {
            
            $viewName.= "-admin.view.twig";
        }
        
        return $viewName;
    }
    
    /**
     * Redirects the browser to a path
     * @param type $path
     * @return type
     */
    public function redirect($path) {
        
        if ($path === null) {
            
            return;
        }
        
        $redirectBaseUrl = BASE_URL;
        $redirectPath = $path;
        $container = 'main';
        
        if ($this->admin) {
            
            $container = 'admin';
            $redirectBaseUrl = BASE_URL_ADMIN;
        }
            
        if (isset(Routes::$views[$container])) {
                
            header("Location: " . $redirectBaseUrl . $redirectPath);

            exit;
        }
    }
}