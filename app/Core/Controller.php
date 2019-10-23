<?php
namespace Matcha\Core;

use Matcha\Lib\Helper;
use Matcha\Lib\Alert;

class Controller
{
	public $vars = [];
	public $layout = "default";
	protected $_isLoggedIn;

	public function __construct() {
		if ($this->_isLoggedIn = (!empty($_SESSION["user_id"]) && !empty($_SESSION["user_username"]) && !empty($_SESSION["token"])))
			$this->checkCompleteProfile();
	}

	protected function set($array) {
		$this->vars = array_merge($this->vars, $array);
	}

	protected function render($filename) {
		$route = strtolower(str_replace('Controller', '', Helper::get_class_name(get_class($this))));
		$view = 'view/' . $route . '/';
		extract($this->vars, EXTR_OVERWRITE);
		ob_start();
		require(APP . $view . $filename . '.php');
		$content_for_layout = ob_get_clean();
		if ($this->layout == false)
			$content_for_layout;
		else
			require(APP . 'view/_templates/' . $this->layout . '.php');
	}

	protected function secureInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return ($data);
	}

	protected function secureForm(&$form) {
		foreach ($form as $key => $value)
			$form[$key] = $this->secureInput($value);
	}

	protected function checkAccess(string $message, string $redirect, bool $alreadyLoggedIn = false) {
		if ($alreadyLoggedIn === false) {
			if (!$this->_isLoggedIn) {
				Alert::danger_alert($message);
				$this->redirect($redirect);
			}
		}
		elseif ($alreadyLoggedIn === true && $this->_isLoggedIn) {
			Alert::warning_alert($message);
			$this->redirect($redirect);
		}
	}

	private function checkCompleteProfile() {
		if (!$this->isAjaxRequest()) {
			if (empty($_SESSION["fullprofile"])) {
				$userManager = new \Matcha\Model\UserManager();
				$_SESSION["fullprofile"] = boolval($userManager->checkIfCompleteProfile($_SESSION['user_id']));
				if ($_SESSION["fullprofile"] === false
					&& strcmp("url=profile/edit", $_SERVER["QUERY_STRING"]) != 0
					&& strcmp("url=account/disconnect", $_SERVER["QUERY_STRING"]) != 0) {
					Alert::warning_alert("You need to complete your profile in order to use the website.");
					$this->redirect(URL . "profile/edit");
				}
			}
		}
	}

	private function initUser(string $username = null) {
		$user = new \Matcha\Model\User([
			'usr_id' => (isset($_SESSION['user_username']) && $username === $_SESSION['user_username']) ? $_SESSION['user_id'] : null,
			'usr_login' => $username
		]);
		return ($user);
	}

	protected function fetchUserData(string $username = null) {
		$user = $this->initUser($username);
		$userManager = new \Matcha\Model\UserManager();
		$userData = $userManager->getUser($user);
		return ($userData);
	}

	protected function redirect($url) {
		exit (header('Location: ' . $url));
	}

	public function createUserSession($loginData, bool $remember = false) {
		$landing = URL;
		$sessionInfo = [
			'ses_usr_id' => $loginData->get_usr_id(),
			'ses_token' => session_id(),
			'ses_date' => date("Y-m-d H:i:s"),
			'ses_ip'   => $_SERVER['REMOTE_ADDR']
		];
		$userManager = new \Matcha\Model\UserManager();
		if (!$userManager->existsUserSession($loginData->get_usr_id()))
			$landing = URL . "profile/edit";
		$userManager->newUserSession($sessionInfo);
		$this->saveUserSession($loginData);
		return ($landing);
	}

	public static function saveUserSession($loginData) {
		if (isset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['token']))
			unset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['token']);
		$_SESSION['user_id'] = $loginData->get_usr_id();
		$_SESSION['user_username'] = $loginData->get_usr_login();
		$_SESSION['token'] = session_id();
	}

	private function clearUserSession() {
		if (isset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['token'])) {
			if (isset($_SESSION['FBRLH_state']))
				unset($_SESSION['FBRLH_state']);
			unset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['token']);
		}
	}

	public function disconnect($redirect = URL) {
		$this->clearUserSession();
		session_destroy();
		setcookie('auth', '', time() - 3600, null, null, false, true);
		self::redirect($redirect);
	}

	protected function isAjaxRequest() {
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && stristr($_SERVER['HTTP_X_REQUESTED_WITH'], "xmlhttprequest"));
	}
}
