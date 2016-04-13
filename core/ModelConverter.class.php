<?php

namespace Niuware\WebFramework {
    
    final class ModelConverter  {
        
        private $filter;
        
        private $purifier;
        
        function __construct() {
            
            $this->filter = true;
            $this->purifier = null;
        }
        
        private function sanitizeElement($name, $value) {
            
            if (stripos($name, "content")!== false) {
                
                return $this->purifyElement($value);
            }
            
            if (stripos($name, "email")!== false) {
                
                return filter_var($value, FILTER_SANITIZE_EMAIL);
            }
            
            if (stripos($name, "url")!== false) {
                
                return filter_var($value, FILTER_SANITIZE_URL);
            }
                
            return filter_var($value, FILTER_SANITIZE_STRING, array('flags' => FILTER_FLAG_NO_ENCODE_QUOTES));
        }
        
        private function purifyElement($value) {

            if ($this->purifier!= null) {

                return $this->purifier->filter($value);
            }
            
            return $value;
        }
        
        private function getElementValue($name, $value) {
            
            $elemValue = "";
            
            if ($this->filter) {

                $elemValue = $this->sanitizeElement($name, $value);

            } else {

                $elemValue = $value;
            }
            
            return $elemValue;
        }
        
        public function useNoSanitizer() {
            
            $this->filter = false;
        }
        
        public function usePurifier() {
            
            $this->purifier = new Filter();
            $this->purifier->configure();
        }
        
        public function toNPV($array, &$model) {
            
            foreach ($array as $value) {
                
                if (property_exists(get_class($model), $value['name'])) {
                    
                    $model->{$value['name']} = $this->getElementValue($value['name'], $value['value']);
                }
            }
            
            unset($value);
        }
        
        public function toKPV($array, &$assoc) {
            
            foreach ($array as $value) {
                
                $assoc[$value['name']] = $this->getElementValue($value['name'], $value['value']);
            }
            
            unset($value);
        }
        
        public function toArray($array, &$toArray) {
            
            foreach ($array as $value) {
                
                array_push($toArray, $this->getElementValue($value['name'], $value['value']));
            }
            
            unset($value);
        }
        
        public function sanitizeArray(&$array) {
            
            foreach ($array as $key => $value) {
                
                $array[$key] = $this->getElementValue($key, $value);
            }
        }
    }
}