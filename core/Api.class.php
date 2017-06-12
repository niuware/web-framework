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
* Executes an API call
*/

final class Api {

    private $error;
    
    private $errCode;
    
    private $errMessage = [];

    private $className;
    
    private $methodName;
    
    private $currentUri;
    
    private $requestMethod;
    
    private $params;

    function __construct($requestMethod) {
        
        $this->error = true;
        $this->exitFail = false;
        $this->requestMethod = $requestMethod;
        $this->params = new HttpRequest();
        
        register_shutdown_function(function() {
            
            $this->shutdown(error_get_last());
        });
    }
    
    /**
     * Renders the last triggered error by PHP. This will not render if a web framework
     * error was found before.
     * @param array $error
     * @return string
     */
    private function shutdown($error) {
        
        if ($this->errCode !== null) {
            
            return;
        }
        
        $this->errMessage['error'] = 'There was an unknown error in the execution of this endpoint.';
        $this->errCode = '0x205';
        $this->error = true;
        
        if (!empty($error)) {
        
            if (isset($error['message'])) {
                
                $this->errMessage['error'] = 'Error while executing the endpoint: ' . $this->className . ':' . $this->methodName;
                $this->errMessage['file'] = 'File: ' . $error['file']. ' at line ' . $error['line'];

                $errorListRaw = explode("\n", $error['message']);
                $errorList = [];

                foreach ($errorListRaw as $err) {

                    $errorList[] = $err;
                }

                $this->errMessage['trace'] = $errorList;
            }
        }
        
        $this->response();
    }
    
    /**
     * Sets the requested configuration for the API call
     * @return bool
     */
    private function initialize() : bool {
        
        $this->currentUri = parse_url(filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));

        $func = $this->actionPath($this->currentUri['path']);
        
        $this->setGetMethod($func);
        
        if (isset($func[1]) && !empty($func[1]))
        {
            $this->className = "Niuware\WebFramework\Api\\" . $func[1];
            $this->methodName = str_replace(['-', '_'], '', $func[2]);
            
            return true;
        }
        
        return false;
    }

    /**
    * Calls all necessary methods to execute the API call
    */
    private function start() {

        $this->load();

        $this->execute();

        $this->response();
    }

    /**
    * Register the class Autoloader for an API call
    */
    private function load() {

        spl_autoload_register(__NAMESPACE__ . "\Autoloader::api");
        spl_autoload_register(__NAMESPACE__ . "\Autoloader::model");
        spl_autoload_register(__NAMESPACE__ . "\Autoloader::helper");
        
        Database::boot();
    }

    /**
    * Sends back a response if an error was generated
    */
    private function response() {

        if ($this->error) {

            header('Content-Type: application/json');
            echo json_encode(array('error' => true, 'data' => array('errcode' => $this->errCode, 'error_message' => $this->errMessage)));
        }
    }

    /**
    * Instantiate an object of the desired API class and
    * executes the called method
    */
    private function execute() {

        if (class_exists($this->className)) {

            $instance = new $this->className;
            
            if (get_parent_class($instance) !== __NAMESPACE__ . '\ApiResponse' ) {
                
                $this->errCode = "0x204";
                
                return;
            }
            
            if ($this->verifyMethod($instance)) {
                
                $this->error = false;

                call_user_func([$instance, $this->methodName], $this->params);
                
            } else {
                
                $this->errCode = "0x202";
            }

        } else {

            $this->errCode = "0x203";
        }
    }
    
    /**
     * Verifies if the called API class method exists
     * @param type $obj API class object
     * @return bool
     */
    private function verifyMethod(&$obj) : bool {
        
        $baseMethodName = $this->methodName;
        $this->methodName = $this->requestMethod . $baseMethodName;

        if (!method_exists($obj, $this->methodName)) {

            $this->methodName = $baseMethodName;

            if (!method_exists($obj, $this->methodName)) {

                return false;
            }
        }
        
        $reflection = new \ReflectionMethod($obj, $this->methodName);
        
        if (!$reflection->isPublic()) {
            
            return false;
        }
        
        return true;
    }

    /**
    * Parses the request URL
    * @param string $customPath The path to parse
    * @return array Split URL
    */
    private function actionPath($customPath = "") : array {

        $currentPath = $customPath;

        if (BASE_PATH == "/") {

            $path = substr($currentPath, 1);

        } else {

            $path = str_replace('/' . BASE_PATH, '', $currentPath);
        }

        return explode('/', $path);
    }
    
    /**
     * Sets a value to the requested method if not set
     * @param array $func Source path array
     * @param int $index Index to set
     */
    private function setGetMethod(array &$func) {
        
        if (!isset($func[2])) {
            
            $func[2] = "";
        }
    }

    /**
     * Executes a POST API call
     * @param string $apiCall Name of the method to execute
     * @param array $params Method arguments
     */
    public function postApi($params) {
        
        if ($this->initialize()) {
            
            $this->params = new HttpRequest($params);

            $this->start();
        }
    }

    /**
     * Executes a GET API call
     */
    public function getApi() {

        if ($this->initialize()) {
        
            // Parse the query for the requested URL
            if (isset($this->currentUri['query'])) {

                $params = array();

                parse_str($this->currentUri['query'], $params);

                $this->params = new HttpRequest($params);
            }

            $this->start();
        }
    }

}