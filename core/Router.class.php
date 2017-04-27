<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

require_once 'routes.php';

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

            $currentUri = str_replace(BASE_PATH, "", filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));
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
            
            $this->postParams = filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
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

            include 'core/HttpInput.class.php';
            
            new HttpInput($this->requestMethod);

            exit;

        } 
        else if ($action == "admin") {

            return $this->setRequireAdminAuthMode();
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

                header("Location: " . BASE_URL . HOMEPAGE);

            } else {

                header("Location: " . BASE_URL_ADMIN . HOMEPAGE_ADMIN);
            }

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

        return new $controllerClass;
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
    public function getControllerParams() : array {
        
        $pathParams = [];

        if (!$this->admin) {
            
            $pathParams = array_splice($this->path, 1);
        }
        else {
            
            $pathParams = array_splice($this->path, 2);
        }
            
        $allParams = array_merge($pathParams, $this->queryString);
        
        if ($this->requestMethod == 'post') {
            
            $allParams = array_merge($allParams, $this->postParams);
        }
        
        return $allParams;
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
            
            $viewName.= ".view.php";
        }
        else {
            
            $viewName.= "-admin.view.php";
        }
        
        return $viewName;
    }
}