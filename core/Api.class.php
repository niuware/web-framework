<?php 

namespace Niuware\WebFramework {
    
    final class Api {
        
        private $error;
        private $errCode;
        
        private $className;
        private $classFile;
        private $methodName;
        private $params = array();
        
        function __construct() {
             
            $this->error = true;
            $this->errCode = "0x201";
            $this->exitFail = false;
        }
        
        private function start() {

            $this->load();
            
            $this->execute();
            
            $this->response();
        }
        
        private function load() {
            
            spl_autoload_register(__NAMESPACE__ . "\Autoloader::api");
            spl_autoload_register(__NAMESPACE__ . "\Autoloader::model");
        }
        
        private function response() {
            
            if ($this->error) {

                header('Content-Type: application/json');
                echo json_encode(array('error' => true, 'data' => array('errcode' => $this->errCode)));
            }
        }
        
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
        
        private function actionPath($customPath = "") {
            
            $currentPath = $customPath;

            if (BASE_PATH == "/") {
            
                $path = substr($currentPath, 1);
                
            } else {
                
                $path = str_replace(BASE_PATH, "", $currentPath);
            }
            
            return explode('/', $path);
        }

        /**
         * Executes a POST API call
         * @param   string  $task   Name of the task to execute
         * @param   array   $params Task function arguments
         */
        public function postApi($task, $params) {

            $func = explode("/", $task);
                    
            $this->className = "Niuware\WebFramework\Api\\" . $func[0];
            $this->classFile = $func[1];
            $this->methodName = "post" . $func[1];
            $this->params = $params;

            $this->start();
        }
        
        public function postApiDirect($params) {
            
            $func = $this->actionPath();

            $this->className = "Niuware\WebFramework\Api\\" . $func[1];
            $this->classFile = $func[1];
            $this->methodName = "post" . $func[2];
            
            $this->params = $params;
            
            $this->start();
        }
        
        public function getRequest() {
            
            $currentUri = parse_url(filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));
            
            $func = $this->actionPath($currentUri['path']);
            
            if (isset($func[1]) && !empty($func[1]))
            {
                $this->className = "Niuware\WebFramework\Api\\" . $func[1];
                $this->classFile = $func[1];
                $this->methodName = "get" . $func[2];

                if (isset($currentUri['query'])) {

                    $params = array();

                    parse_str($currentUri['query'], $params);

                    $this->params = $params;
                }

                $this->start();
            }
        }

    }
}