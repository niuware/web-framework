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
     * The Response object
     * @var Niuware\WebFramework\Response;
     */
    public $response;
    
    private $api;
    
    public function __construct(Api $api) {

        $this->response = new Response();
        $this->api = $api;
    }

    /**
     * Returns the endpoint response
     * @param int $options Type of encoding for the json_encode PHP function
     * @param int $depth
    */
    protected function render($options = 0, $depth = 512) {
        
        $response = [

            'data' => $this->response->data(),
            'error' => $this->response->error()
        ];
        
        $this->api->setOutputOptions($options, $depth);
        
        return $response;
    }
}