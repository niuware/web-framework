<?php 

namespace App\Controllers\Admin;
    
use Niuware\WebFramework\Application\Controller;
use Niuware\WebFramework\Http\Request;
use Niuware\WebFramework\Auth\Auth;

final class Login extends Controller {

    public function getLogin(Request $request) {
        
        $this->view = "admin/login/index.twig";
        
        return $this->render();
    }

    public function postLogin(Request $request) {
        
        Auth::grantAuth('admin');
    }
    
    public function getLogout(Request $request) {
        
        Auth::revokeAuth('admin');

        return $this->render('login');
    }
}