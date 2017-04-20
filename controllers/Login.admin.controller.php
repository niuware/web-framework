<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Auth;

final class LoginAdmin extends Controller {

    public function getLogin($params = []) {
        
        Auth::grantAuth('admin');
    }
    
    public function getLogout($params = []) {
        
        Auth::revokeAuth('admin');
    }
}