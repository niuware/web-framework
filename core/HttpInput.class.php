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
* Process an HTTP request
*/
final class HttpInput {
    
    private $requestMethod;

    function __construct($requestMethod) {
        
        $this->requestMethod = $requestMethod;
    }
    
    /**
     * Parses a POST or DELETE input
     * @param array $data
     * @param array $files
     */
    public function parse(&$data, &$files) {
        
        $data = null;
        $files = null;
        
        if ($this->requestMethod === 'post' || $this->requestMethod === 'delete') {

            $contentType = filter_input(INPUT_SERVER, 'CONTENT_TYPE');

            if (substr($contentType, 0, 16) == 'application/json') {

                $data = json_decode(file_get_contents('php://input'), true);
            }
            else {

                $data = filter_input_array(INPUT_POST);
            }

            $files = $_FILES;
        }
    }

    /**
    * Instantiate a new API class object to execute an 
    * API call, depending on the type of HTTP requested method
    */
    public function withApi() {

        $api = new Api($this->requestMethod);
        
        if ($this->requestMethod === 'post' || $this->requestMethod === 'delete') {
            
            $data = null;
            $files = null;
            
            $this->parse($data, $files);
            
            $api->postApi($data, $files);

        } elseif ($this->requestMethod === 'get') {

            $api->getApi();
        }
        else {
            
            $api->unsupportedRequestMethod();
            
            exit;
        }
    }
}