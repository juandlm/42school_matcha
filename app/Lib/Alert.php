<?php
namespace Matcha\Lib;

class Alert
{
	public static function success_alert($message) {
		$_SESSION['success_alert'] = $message;
	}

	public static function warning_alert($message) {
		$_SESSION['warning_alert'] = $message;
	}

	public static function danger_alert($message) {
		$_SESSION['danger_alert'] = $message;
	}

	public static function js_alert($message) {
		echo '<script type="text/javascript">alert("' . $message . '")</script>';
	}
}