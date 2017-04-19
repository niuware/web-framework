<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Auth;

final class Login extends Controller {

    public function login($params = []) {
        
        Auth::grantAuth();
    }
    
    public function logout($params = []) {
        
        Auth::revokeAuth();
    }
}