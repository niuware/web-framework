<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HtmlResponse;

final class Home extends Controller {
    
    public function home($params = []) {
        
        $this->title = "My Home Page";
        $this->template = "home.view.php";

        $this->styles = ["default" => ["main"]];
        
        HtmlResponse::getInstance()->render($this);
    }
}