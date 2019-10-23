<?php
namespace Matcha\Core;

use Matcha\Lib\Database;

class Model extends Controller
{
	public $db = null;

	function __construct() {
		try {
			self::openDatabaseConnection();
		} catch (\PDOException $e) {
			exit("Database connection could not be established: " . $e);
		}
	}

	private function openDatabaseConnection() {
		if ($this->db === null)
			$this->db = new Database(DB_DSN, DB_USER, DB_PASS);
	}
}