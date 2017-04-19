<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Auth;

final class LoginAdmin extends Controller {

    public function login($params = []) {
        
        Auth::grantAuth('admin');
    }
    
    public function logout($params = []) {
        
        Auth::revokeAuth('admin');
    }
}