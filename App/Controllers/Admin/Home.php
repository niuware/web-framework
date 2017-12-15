<?php 

namespace App\Controllers\Admin;
    
use Niuware\WebFramework\Application\Controller;
use Niuware\WebFramework\Http\HttpRequest;

final class Home extends Controller {
    
    public function getIndex(HttpRequest $request) {
        
        return $this->render();
    }
}