<?php 

namespace Niuware\WebFramework\Views {
    
    use \Niuware\WebFramework\View;
    
    final class Home extends View implements \Niuware\WebFramework\IView {
        
        function __construct() {
            
            parent::__construct();
            
            $this->title = "Niuware WebFramework Home";
            $this->template = "home-template.html";
        }
        
        public function render() {
            
            parent::render();
        }
    } 
}