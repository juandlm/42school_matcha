<?php
namespace Matcha\Lib;

class Helper
{
	public static function get_class_name($classname) {
		if ($pos = strrpos($classname, '\\'))
			return (substr($classname, $pos + 1));
		return ($pos);
	}

	public static function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return (null);
        }
        return (array_keys($array)[count($array) - 1]);
	}

	public static function time_elapsed_string($datetime, $full = false) {
		$now = new \DateTime;
		$ago = new \DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k)
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			else
				unset($string[$k]);
		}
	
		if (!$full)
			$string = array_slice($string, 0, 1);
		return ($string ? implode(', ', $string) . ' ago' : 'just now');
	}

	public static function performCurl($url, $params = false, $headers = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($params) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		if ($headers)
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		curl_close ($ch);
		return ($result);
	}

	public static function include_all($type = 'css', $filename, $files = []) {
		if (file_exists(ROOT . "assets/$type/" . "$filename.$type"))
			$files[] = "$filename.$type";
		foreach ($files as $val)
			if ($type == 'css')
				echo "<link rel='stylesheet' type='text/css' href='" . URL . "assets/css/$val'>";
			elseif ($type == 'js')
				echo "<script type='text/javascript' src='" . URL . "assets/js/$val'></script>";
	}

	public static function getClientIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		  $ip = $_SERVER['REMOTE_ADDR'];
		}
		return ($ip);
	  }

	public static function ip_details(string $ip = null) {
		if (empty($ip))
			$ip = self::getClientIP();
		$ipinfoToken = "e8ffd6fa71eccd";
		$curl = self::performCurl("ipinfo.io/{$ip}", false, ["Authorization: Bearer {$ipinfoToken}"]);
		return (json_decode($curl));
	}
}
