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
 * Base class for a response
 */
final class Response {
    
    private $data = [];

    private $error = false;
    
    public function __get($name) {
            
        return $this->data[$name];
    }
    
    public function __set($name, $value) {
        
        if ($name !== 'error') {
            
            $this->data[$name] = $value;
        }
        else {
            
            $this->error = $value;
        }
    }
    
    /**
    * Gets the data
    * @return mixed  
    */
    public function data() {
        
        return $this->data;
    }
    
    /**
     * Returns the current error status
     * @return mixed
     */
    public function error() {
        
        return $this->error;
    }
    
    /**
     * Adds multiple values at once
     * @param array $data
     */
    public function bulkAdd(array $data) {
        
        // Prevent error overwrite
        $saveError = $this->error;
        
        foreach ($data as $key => $value) {
            
            $this->data[$key] = $value;
        }
        
        $this->error = $saveError;
    }
}