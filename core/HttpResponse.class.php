<?php 

namespace Niuware\WebFramework {
    
    abstract class HttpResponse {
        
        private $data = array();
        
        private $error = false;
        
        function __construct() { }
        
        public function setData($data) {
            
            $this->data = $data;
        }
        
        public function setError($error) {
            
            $this->error = $error;
        }
        
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