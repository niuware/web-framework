<?php 

namespace App\Controllers;
    
use Niuware\WebFramework\Controller;
use Niuware\WebFramework\Request;
use Niuware\WebFramework\Auth;

final class Login extends Controller {

    public function getLogin(Request $request) {
        
        $this->view = "login/index.twig";
        
        return $this->render();
    }

    public function postLogin(Request $request) {
        
        Auth::grantAuth();
    }
    
    public function getLogout(Request $request) {
        
        Auth::revokeAuth();

        return $this->render('login');
    }
}