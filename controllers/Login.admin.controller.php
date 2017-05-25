<?php 

namespace Niuware\WebFramework\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Auth;

final class LoginAdmin extends Controller {

    public function getLogin(HttpRequest $request) {
        
        Auth::grantAuth('admin');
    }
    
    public function getLogout(HttpRequest $request) {
        
        Auth::revokeAuth('admin');
    }
}