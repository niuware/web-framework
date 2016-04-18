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
    * Base class for all Model classes
    */
    abstract class Model  {
        
        protected $convert;
        protected $modelquery;
        
        function __construct(Database $dbObj = null) {
            
            $this->convert = new ModelConverter();
            $this->modelquery =  new ModelQuery($dbObj);
        }
        
        /**
        * Returns an instance of the ModelQuery class
        * @return ModelQuery 
        */
        public function modelQuery() {
            
            return $this->modelquery;
        }
        
        /**
        * Returns the current instance of the Database class
        * @return Database
        */
        public function sql() {
            
            return $this->modelquery->sql();
        }
        
        /**
        * Returns the current instance of the ModelConverter class
        * @return ModelConverter
        */
        public function converter() {
            
            return $this->convert;
        }
    }
}