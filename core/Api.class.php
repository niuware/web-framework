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

    private $className;
    
    private $methodName;
    
    private $currentUri;
    
    private $requestMethod;
    
    private $params;
    
    private $methodResponse;
    
    private $outputOpts;
    
    private $outputDepth;

    function __construct($requestMethod) {
        
        $this->error = true;
        $this->requestMethod = $requestMethod;
        $this->methodResponse = [];
        $this->rendered = false;
        $this->params = new HttpRequest();
        
        register_shutdown_function(function() {
            
            $this->shutdown(error_get_last());
        });
    }
    
    private function getDetailedError($error) {
        
        $output = [];
        
        $output['error'] = 'There was an unknown error in the execution of this endpoint.';

        if (isset($error['message'])) {

            $output['error'] = 'Error while executing the endpoint: ' . $this->className . ':' . $this->methodName;
            $output['file'] = 'File: ' . $error['file']. ' at line ' . $error['line'];

            $errorListRaw = explode("\n", $error['message']);
            $errorList = [];

            foreach ($errorListRaw as $err) {

                $errorList[] = $err;
            }

            $output['trace'] = $errorList;
        }
        
        return $output;
    }
    
    /**
     * Renders the endpoint output as a JSON formatted string.
     * @param array $error
     */
    private function shutdown($error) {
        
        if (!empty($error)) {
            
            $this->errCode = '0x205';
            $this->error = true;
            
            $output = $this->getDetailedError($error);
        }
        else {
            
            $output = $this->methodResponse;
        }
        
        $this->response($output);
    }
    
    /**
     * Sets the requested configuration for the API call
     * @return bool
     */
    private function initialize() {
        
        $this->currentUri = parse_url(filter_input(\App\Config\SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));

        $func = $this->actionPath($this->currentUri['path']);
        
        $this->setGetMethod($func);
        
        if (isset($func[1]) && !empty($func[1]))
        {
            $this->className = "App\Api\\" . $func[1];
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
    }

    /**
    * Register the class Autoloader for an API call
    */
    private function load() {
        
        Database::boot();
    }

    /**
    * Sends back a response if an error was generated
    */
    private function response($output) {

        header('Content-Type: application/json');
        
        if ($this->error) {
            
            echo json_encode(['error' => true, 'data' => ['errcode' => $this->errCode, 'error_message' => $output]]);
        }
        else {
            
            $json = json_encode($output, $this->outputOpts, $this->outputDepth);
            
            if (function_exists('mb_strlen')) {
                
                $size = mb_strlen($json, '8bit');
                
            } else {
                
                $size = strlen($json);
            }
            
            header('Content-Length: ' . $size);
            
            echo $json;
        }
    }

    /**
    * Instantiate an object of the desired API class and
    * executes the called method
    */
    private function execute() {

        if (class_exists($this->className)) {

            $instance = new $this->className($this);
            
            if (get_parent_class($instance) !== __NAMESPACE__ . '\ApiResponse' ) {
                
                $this->errCode = "0x204";
                
                return;
            }
            
            if ($this->verifyMethod($instance)) {
                
                $this->error = false;

                $this->methodResponse = call_user_func([$instance, $this->methodName], $this->params);
                
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
    private function verifyMethod(&$obj) {
        
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
    private function actionPath($customPath = "") {

        $currentPath = $customPath;

        if (\App\Config\BASE_PATH == "/") {

            $path = substr($currentPath, 1);

        } else {

            $path = str_replace('/' . \App\Config\BASE_PATH, '', $currentPath);
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
     * @param array $params Method arguments
     * @param array $files PHP $_FILES 
     */
    public function postApi($params, $files = null) {
        
        if ($this->initialize()) {
            
            $this->params = new HttpRequest($params, $files, $this->currentUri);

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

                $params = [];

                parse_str($this->currentUri['query'], $params);

                $this->params = new HttpRequest($params, null, $this->currentUri);
            }

            $this->start();
        }
    }

    /**
     * Sets the JSON output options (See PHP json_encode function)
     * @param int $options
     * @param int $depth
     */
    public function setOutputOptions($options, $depth) {
        
        $this->outputOpts = $options;
        $this->outputDepth = $depth;
    }
    
    /**
     * Sets the error when the request method is not supported
     */
    public function unsupportedRequestMethod() {
        
        $this->errCode = "0x206";
    }
}