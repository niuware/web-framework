<?php 

namespace Niuware\WebFramework\Controllers\Admin;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HttpRequest;

final class Home extends Controller {
    
    public function getHome(HttpRequest $request) {
        
        return $this->render();
    }
}