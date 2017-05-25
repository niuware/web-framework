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
* HTTP request class
*/
final class HttpRequest {
    
    private $headers = [];
    
    private $attributes = [];
    
    /**
     * Gets a request property
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        if (isset($this->attributes[$name])) {
            
            return $this->attributes[$name];
        }
        
        return null;
    }

    /**
     * Sets a request property
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value) {

        $this->attributes[$name] = $value;
    }

    function __construct(array $params = null) { 
        
        if ($params !== null) {
            
            $this->attributes = $params;
        }
        
        $this->setHeaders();
    }
    
    /**
     * Sets all HTTP headers
     */
    private function setHeaders() {
        
        if (function_exists('getallheaders')) {
            
            $this->headers = getallheaders();
        }
        else {
        
            $headers = ""; 
            
            foreach (array_keys($_SERVER) as $header) {
                
                if (substr($header, 0, 5) == 'HTTP_') { 
                    
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($header, 5)))))] = filter_input(INPUT_SERVER, $header); 
                } 
            } 

            $this->headers = $headers; 
        }
    }
    
    /**
     * Gets all HTTP headers
     * @return array
     */
    public function headers() {
        
        return $this->headers;
    }
}