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

    function __construct(string $requestMethod) { 

        if ($requestMethod != null) {
            
            $this->initialize($requestMethod);
        }
    }

    /**
    * Instantiate a new API class object to execute an 
    * API call, depending on the type of HTTP requested method
    */
    private function initialize($requestMethod) {

        $api = new Api($requestMethod);
        
        if ($requestMethod == "post") {
                
            $postData = null;
            
            $contentType = filter_input(INPUT_SERVER, 'CONTENT_TYPE');
            
            if (substr($contentType, 0, 16) == 'application/json') {
                
                $postData = json_decode(file_get_contents("php://input"), true);
            }
            else {
                
                $postData = filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            }
            
            $api->postApi($postData);

        } elseif ($requestMethod == "get") {

            $api->getApi();
        }
    }
}