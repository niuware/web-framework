<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Auth;

final class Login extends Controller {

    public function getLogin($params = []) {
        
        Auth::grantAuth();
    }
    
    public function getLogout($params = []) {
        
        Auth::revokeAuth();
    }
}