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
                
            $api->postApi(filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));

        } elseif ($requestMethod == "get") {

            $api->getApi();
        }
    }
}