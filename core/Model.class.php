<?php

namespace Niuware\WebFramework {
    
    abstract class Model  {
        
        protected $convert;
        protected $modelquery;
        
        function __construct(Database $dbObj = null) {
            
            $this->convert = new ModelConverter();
            $this->modelquery =  new ModelQuery($dbObj);
        }
        
        public function modelQuery() {
            
            return $this->modelquery;
        }
        
        public function sql() {
            
            return $this->modelquery->sql();
        }
        
        public function converter() {
            
            return $this->convert;
        }
    }
}