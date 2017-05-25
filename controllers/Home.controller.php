<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HtmlResponse;

final class Home extends Controller {
    
    public function getHome(HttpRequest $request) {
        
        $this->title = "My Home Page";

        $this->styles = ["default" => ["main"]];
        
        HtmlResponse::getInstance()->render($this);
    }
}