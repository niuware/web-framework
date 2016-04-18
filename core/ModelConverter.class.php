<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {
    
    /**
    * Class to parse and filter any Model before executing
    * the transaction to the database
    */
    final class ModelConverter  {
        
        private $filter;
        
        private $purifier;
        
        function __construct() {
            
            $this->filter = true;
            $this->purifier = null;
        }
        
        /**
        * Sanitize a field value based on its name
        * @param string $name Name of the field
        * @param mixed $value Value to sanitize
        * @return mixed The sanitized value
        */
        private function sanitizeElement($name, $value) {
            
            // This is used when the field is an advanced textarea, for 
            // example a TinyMCE editor input
            if (stripos($name, "content")!== false) {
                
                return $this->purifyElement($value);
            }
            
            if (stripos($name, "email")!== false) {
                
                return filter_var($value, FILTER_SANITIZE_EMAIL);
            }
            
            if (stripos($name, "url")!== false) {
                
                return filter_var($value, FILTER_SANITIZE_URL);
            }
            
            // General string sanitizing
            return filter_var($value, FILTER_SANITIZE_STRING, array('flags' => FILTER_FLAG_NO_ENCODE_QUOTES));
        }
        
        /**
        * Purifies (filters) the value using the HTMLPurifier external library
        * @param string $value String to purify 
        * @return string Purified string
        */
        private function purifyElement($value) {

            if ($this->purifier!= null) {

                return $this->purifier->filter($value);
            }
            
            return $value;
        }
        
        /**
        * Returns a filtered field
        * @param string $name Name of the field
        * @param mixed $value Value of the field
        * @return mixed Return the filtered value
        */
        private function getElementValue($name, $value) {
            
            $elemValue = "";
            
            if ($this->filter) {

                $elemValue = $this->sanitizeElement($name, $value);

            } else {

                $elemValue = $value;
            }
            
            return $elemValue;
        }
        
        /**
        * Sets the filtering option off
        */
        public function useNoSanitizer() {
            
            $this->filter = false;
        }
        
        /**
        * Creates a new instance of the Filter class, used when
        * we want to use the HTMLPurifier library
        */
        public function usePurifier() {
            
            $this->purifier = new Filter();
            $this->purifier->configure();
        }
        
        /**
        * Fills a Model based class as a Name-Pair Value using the 
        * input field names (must match with the Model property names)
        * @param array $array Array of the input fields
        * @param ref Model $model Reference to the Model object
        */
        public function toNPV($array, &$model) {
            
            foreach ($array as $value) {
                
                if (property_exists(get_class($model), $value['name'])) {
                    
                    $model->{$value['name']} = $this->getElementValue($value['name'], $value['value']);
                }
            }
            
            unset($value);
        }
        
        /**
        * Fills an array as a Key-Pair Value using the 
        * input field names 
        * @param array $array Array of the input fields
        * @param ref array $assoc Reference to the associative array
        */
        public function toKPV($array, &$assoc) {
            
            foreach ($array as $value) {
                
                $assoc[$value['name']] = $this->getElementValue($value['name'], $value['value']);
            }
            
            unset($value);
        }
        
        /**
        * Fills an array with all values of the input field
        * @param array $array Array of the input fields
        * @param ref array $toArray Reference to the single array
        */
        public function toArray($array, &$toArray) {
            
            foreach ($array as $value) {
                
                array_push($toArray, $this->getElementValue($value['name'], $value['value']));
            }
            
            unset($value);
        }
        
        /**
        * Sanitizes all the elements of an array and updates the 
        * values in the same array
        * @param ref array $array Array of elements to sanitize
        */
        public function sanitizeArray(&$array) {
            
            foreach ($array as $key => $value) {
                
                $array[$key] = $this->getElementValue($key, $value);
            }
        }
    }
}