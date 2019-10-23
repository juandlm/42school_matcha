<?php
session_name("matcha");
session_start();

define("ROOT", dirname(__FILE__) . '/');
define("APP", ROOT . "app" . '/');

require (APP . "config/config.php");
require (APP . "Core/Autoloader.php");

new Matcha\Core\Autoloader();
use Matcha\Core\Application;

if (empty($_SESSION["ip_info"])) {
	$_SESSION["ip_info"] = (array)Matcha\Lib\Helper::ip_details("78.153.241.117");
	$loc = explode(',', $_SESSION["ip_info"]["loc"]);
	$_SESSION["ip_info"]["lat"] = $loc[0];
	$_SESSION["ip_info"]["long"] = $loc[1];
}

$app = new Application();