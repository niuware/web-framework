<?php 

/**
* This interface is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {

    /**
    * Interface implemented by all Model classes
    */
    interface IView {
        
        /**
        * Method for rendering the View's associated template
        */
        public function render();
    }
    
}