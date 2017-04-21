<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HtmlResponse;

final class HomeAdmin extends Controller {
    
    public function getHome($params = []) {
        
        $this->title = "My Admin Home Page View";

        $this->styles = ["default" => ["main"]];
        
        HtmlResponse::getInstance()->render($this);
    }
}