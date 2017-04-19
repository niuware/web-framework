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

    function __construct() { 

        $this->initialize();
    }

    /**
    * Instantiate a new API class object to execute an 
    * API call, depending on the type of HTTP requested method
    */
    private function initialize() {

        if (filter_input(SERVER_ENV_VAR, 'REQUEST_METHOD') == "POST") {

            if (filter_input(INPUT_POST, 'api')) {

                $api = new Api();

                $api->postApi(filter_input(INPUT_POST, 'api'), filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));
            }

        } elseif (filter_input(SERVER_ENV_VAR, 'REQUEST_METHOD') == "GET") {

            $api = new Api();

            $api->getApi();
        }
    }
}

// Creates an object of this class 
$http = new HttpInput();