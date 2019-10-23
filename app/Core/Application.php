<?php
namespace Matcha\Core;

class Application
{
	private $_url_controller = null;
	private $_url_action = null;
	private $_url_params = [];

    public function __construct() {
        $this->splitUrl();

        if (!$this->_url_controller) {
            $page = new \Matcha\Controller\HomeController();
            $page->index();
        } elseif (file_exists(APP . 'Controller/' . ucfirst($this->_url_controller) . 'Controller.php')) {
            $controller = "\\Matcha\\Controller\\" . ucfirst($this->_url_controller) . 'Controller';
            $this->_url_controller = new $controller();
            if (method_exists($this->_url_controller, $this->_url_action) &&
                is_callable(array($this->_url_controller, $this->_url_action))) {
                if (!empty($this->_url_params)) {
                    call_user_func_array(array($this->_url_controller, $this->_url_action), $this->_url_params);
                } else {
                    $this->_url_controller->{$this->_url_action}();
                }
            } else {
                if (strlen($this->_url_action) == 0) {
                    $this->_url_controller->index();
                } else {
                    $page = new \Matcha\Controller\ErrorController();
                    $page->index();
                }
            }
        } else {
            $page = new \Matcha\Controller\ErrorController();
            $page->index();
        }
    }

    private function splitUrl() {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $this->_url_controller = isset($url[0]) ? $url[0] : null;
            $this->_url_action = isset($url[1]) ? $url[1] : null;
            unset($url[0], $url[1]);
            $this->_url_params = array_values($url);

			//debug
            //echo 'Controller: ' . $this->_url_controller . '<br>';
            //echo 'Action: ' . $this->_url_action . '<br>';
            //echo 'Parameters: ' . print_r($this->_url_params, true) . '<br>';
        }
    }
}
