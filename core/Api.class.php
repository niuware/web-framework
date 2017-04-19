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
    private $classFile;
    private $methodName;
    private $params = array();

    function __construct() {

        // Default values
        $this->error = true;
        $this->errCode = "0x201";
        $this->exitFail = false;
    }

    /**
    * Calls all necessary methods to execute the api call
    */
    private function start() {

        $this->load();

        $this->execute();

        $this->response();
    }

    /**
    * Register the class autoloaders for an api call
    */
    private function load() {

        spl_autoload_register(__NAMESPACE__ . "\Autoloader::api");
        spl_autoload_register(__NAMESPACE__ . "\Autoloader::model");
        
        Database::boot();
    }

    /**
    * Sends back a response if an error was generated
    */
    private function response() {

        if ($this->error) {

            header('Content-Type: application/json');
            echo json_encode(array('error' => true, 'data' => array('errcode' => $this->errCode)));
        }
    }

    /**
    * Instanciate an object of the desired API class and
    * executes the called method
    */
    private function execute() {

        if (class_exists($this->className)) {

            $obj = new $this->className;

            if (method_exists($obj, $this->methodName)) {

                $this->error = false;

                call_user_func(array($obj, $this->methodName), $this->params);
            } else {

                $this->errCode = "0x202";
            }
        } else {

            $this->errCode = "0x203";
        }
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

            $path = str_replace(BASE_PATH, "", $currentPath);
        }

        return explode('/', $path);
    }
    
    /**
     * Sets a value to the requested method if not set
     * @param array $func Source path array
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
    public function postApi($apiCall, $params) {

        $func = explode("/", $apiCall);

        $this->className = "Niuware\WebFramework\Api\\" . $func[0];
        $this->classFile = $func[1];
        $this->methodName = "post" . $func[1];
        $this->params = $params;

        $this->start();
    }

    /**
     * Executes a GET API call
     */
    public function getApi() {

        $currentUri = parse_url(filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));

        $func = $this->actionPath($currentUri['path']);
        
        $this->setGetMethod($func);

        if (isset($func[1]) && !empty($func[1]))
        {
            $this->className = "Niuware\WebFramework\Api\\" . $func[1];
            $this->classFile = $func[1];
            $this->methodName = "get" . $func[2];

            // Parse the query for the requested URL
            if (isset($currentUri['query'])) {

                $params = array();

                parse_str($currentUri['query'], $params);

                $this->params = $params;
            }

            $this->start();
        }
    }

}