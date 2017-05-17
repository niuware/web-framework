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
final class JsonResponse extends Response {

    private static $data = [];

    private static $error = false;

    /**
    * Sets the data to send with the response
    * @param mixed $data An object that contains the data  
    */
    public static function data($data) {

        self::$data = $data;
    }

    /**
    * Sets the error data to send with the response
    * @param string $error String describing the generated error
    */
    public static function error($error) {

        self::$error = $error;
    }
    
    /**
     * Returns the current error status
     * @return type
     */
    public static function getError() {
        
        return self::$error;
    }

    /**
    * Sends the response to the client
    * @param int $encode_constant Type of encoding for the json_encode PHP function
    */
    public static function render($encode_constant = 0) {

        $response = [

            'data' => self::$data,
            'error' => self::$error
        ];

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($response, $encode_constant);

        exit;
    }
}