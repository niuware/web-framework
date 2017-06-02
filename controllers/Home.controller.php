<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HttpRequest;
use Niuware\WebFramework\HtmlResponse;

final class Home extends Controller {
    
    public function getHome(HttpRequest $request) {
        
        HtmlResponse::getInstance()->render($this);
    }
}