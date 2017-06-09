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
* Renders a JSON response
*/
abstract class ApiResponse {
    
    /**
     * Gets the Response object
     * @var Niuware\WebFramework\Response;
     */
    public $response;
    
    public function __construct() {
        
        $this->response = new Response();
    }

    /**
    * Renders the JSON encoded response
    * @param int $encode_constant Type of encoding for the json_encode PHP function
    */
    protected function render($encode_constant = 0) {

        $response = [

            'data' => $this->response->data(),
            'error' => $this->response->error()
        ];

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($response, $encode_constant);
    }
}