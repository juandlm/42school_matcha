<?php
define("ENVIRONMENT", "dev");
define("WORK_OS", stristr(php_uname('s'), "win") ? "WIN" : "OTHER");

$DB["USER"] = 'root';
$DB["PASSWORD"] = WORK_OS == "WIN" ? 'root' : 'rootpass';
$DB["NAME"] = 'db_matcha';
$DB["HOST"] = WORK_OS == "WIN" ? 'localhost' : 'mysql';
$DB["DSN"] = 'mysql:dbname=' . $DB["NAME"] . ';host=' . $DB["HOST"] . ';charset=utf8mb4';
$DB["DSN_S"] = 'mysql:host=' . $DB["HOST"] . ';charset=utf8';

if (ENVIRONMENT == "dev") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

date_default_timezone_set("Europe/Paris");
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

$workDir = dirname($_SERVER["SCRIPT_NAME"]);
$workDir .= (substr($workDir, -1) === DIRECTORY_SEPARATOR) ? '' : '/';

define("URL_PROTOCOL", "//");
define("URL_DOMAIN", $_SERVER["HTTP_HOST"]);
define("URL_SUB_FOLDER", $workDir);
define("URL", URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);

define("DB_DSN", $DB["DSN"]);
define("DB_DSN_S", $DB["DSN_S"]);
define("DB_USER", $DB["USER"]);
define("DB_PASS", $DB["PASSWORD"]);
define("DB_NAME", $DB["NAME"]);