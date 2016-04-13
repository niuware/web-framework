<?php 

namespace Niuware\WebFramework {

    class Router {

        private $path;

        private $view;

        private $error = true;

        private $admin = false;

        function __construct() {

            $this->initialize();

            $this->redirectFail();
        }

        private function initialize() {

            if (BASE_PATH == "/") {

                $currentUri = substr(filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL), 1);
            } else {

                $currentUri = str_replace(BASE_PATH, "", filter_input(SERVER_ENV_VAR, 'REQUEST_URI', FILTER_SANITIZE_URL));
            }

            $this->path = explode('/', $currentUri);

            if (isset(Settings::$views['main'][$this->path[0]])) {

                $this->redirectMain();

                $this->error = false;

            } else {

                $this->redirectTask($this->path[0]);
            }
        }

        private function redirectMain() {

            if (!$this->requireLogin()) {

                $this->view = Settings::$views['main'][$this->path[0]][0];

            } else {

                $this->view = "Login";
            }
        }

        private function requireLogin() {

            $requireLogin = false;
            
            if (isset(Settings::$views['main'][$this->path[0]][1])) {

                $requireLogin = Settings::$views['main'][$this->path[0]][1];
            }

            return $requireLogin;
        }

        private function redirectTask($action) {

            if ($action == "api") {

                include 'core/HttpInput.class.php';
                
                exit;
                
            } else if ($action == "admin") {

                return $this->setAdminMode();
            }
        }

        private function setAdminMode() {

            $this->admin = true;

            $_SESSION['nwf_admin'.session_id()] = 1;

            $this->redirectAdmin();
        }

        private function redirectFail() {

            if ($this->error) {

                if (!$this->admin) {

                    header("Location: " . BASE_URL.HOMEPAGE);

                } else {

                    header("Location: " . BASE_URL_ADMIN.HOMEPAGE_ADMIN);
                }

                exit;
            }
        }

        private function redirectAdmin() {

            if (!isset($_SESSION['nfw_admin_login' . SESSION_ID])) {

                $this->view = "Login.admin";

            } else {

                $this->view = Settings::$views['admin'][$this->path[1]][0] . ".admin";
            }

            $this->error = false;
        }
        
        public function getViewInstance() {
            
            $path = "\Niuware\WebFramework\Views\\" . $this->view;
            
            return new $path;
        }

        public function getViewName() {

            return $this->view;
        }

        public function isAdmin() {

            return $this->admin;
        }
    }
}