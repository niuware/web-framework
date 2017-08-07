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
    public function add(array $data, bool $clear = false) {
        
        // Prevent error overwrite
        $saveError = $this->error;
        
        if ($clear === true) {
            
            $this->clear();
        }
        
        foreach ($data as $key => $value) {
            
            $this->data[$key] = $value;
        }
        
        $this->error = $saveError;
    }
    
    /**
     * Removes multiple keys from the data array
     * @param array $keys
     */
    public function remove(array $keys) {
        
        // Prevent error overwrite
        $saveError = $this->error;
        
        foreach ($keys as $key) {
            
            if (isset($this->data[$key])) {

                unset($this->data[$key]);
            }
        }
        
        $this->error = $saveError;
    }
    
    /**
     * Clears the data array
     */
    public function clear() {
        
        $this->data = [];
    }
    
    /**
     * Returns the response array (data and error)
     * @return array
     */
    public function output() {
        
        $response = [

            'data' => $this->data(),
            'error' => $this->error()
        ];
        
        return $response;
    }
    
    /**
     * Renders the output response as a 
     * JSON string
     */
    public function render($options = 0, $depth = 512) {
        
        echo json_encode($this->output(), $options, $depth);
    }
}