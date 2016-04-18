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
    * Base class for all Api classes
    */
    abstract class HttpResponse {
        
        private $data = array();
        
        private $error = false;
        
        function __construct() { }
        
        /**
        * Sets the data to send with the response
        * @param mixed $data An object that contains the data  
        */
        public function setData($data) {
            
            $this->data = $data;
        }
        
        /**
        * Sets the error data to send with the response
        * @param string $error String describing the generated error
        */
        public function setError($error) {
            
            $this->error = $error;
        }
        
        /**
        * Sends the response to the client
        * @param int $encode_constant Type of encoding for the json_encode PHP function
        */
        public function response($encode_constant = 0) {
            
            $response = array(
                
                'data' => $this->data,
                'error' => $this->error
            );
            
            header('Content-Type: application/json; charset=utf-8');
            
            echo json_encode($response, $encode_constant);
        }
    }
}