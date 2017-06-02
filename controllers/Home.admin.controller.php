<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HttpRequest;
use Niuware\WebFramework\HtmlResponse;

final class HomeAdmin extends Controller {
    
    public function getHome(HttpRequest $request) {
        
        HtmlResponse::getInstance()->render($this);
    }
}