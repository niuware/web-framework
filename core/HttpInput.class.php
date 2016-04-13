<?php

namespace Niuware\WebFramework {
    
    final class HttpInput {
        
        function __construct() { 
            
            $this->initialize();
        }
        
        private function initialize() {
            
            if (filter_input(SERVER_ENV_VAR, 'REQUEST_METHOD') == "POST") {

                if (filter_input(INPUT_POST, 'api')) {

                    $api = new Api();

                    $api->postApi(filter_input(INPUT_POST, 'api'), filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));
                } else {
                    
                    $api = new Api();
                    
                    $api->postApiDirect(filter_input(INPUT_POST, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));
                }
            } elseif (filter_input(SERVER_ENV_VAR, 'REQUEST_METHOD') == "GET") {

                $api = new Api();

                $api->getRequest();
            }
        }
    }
    
    $http = new HttpInput();
}