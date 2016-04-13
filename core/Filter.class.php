<?php

namespace Niuware\WebFramework {
    
    require_once 'lib/htmlpurifier/HTMLPurifier.auto.php';
    
    class Filter  {
        
        private $config;
        
        private $purifier;
        
        function __construct() {
            
            $this->purifier = null;
            $this->config = \HTMLPurifier_Config::createDefault();
            $this->config->set('Core.Encoding', 'UTF-8');
        }
        
        public function configure() {
            
            $this->purifier = new \HTMLPurifier($this->config);
        }
        
        public function filter($html) {
            
            return $this->purifier->purify($html);
        }
    }
}