<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\HttpRequest;
use Niuware\WebFramework\Auth;

final class Login extends Controller {

    public function getLogin(HttpRequest $request) {
        
        Auth::grantAuth();
    }
    
    public function getLogout(HttpRequest $request) {
        
        Auth::revokeAuth();
    }
}