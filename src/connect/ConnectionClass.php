<?php
namespace pumast3r\api\connect;

use Dotenv\Dotenv;
use PDO;
use pumast3r\api\helpers\DotenvClass;

DotenvClass::loadDotenv();

class ConnectionClass {
	private string $DB_HOST;
	private string $DB_PORT;
	private string $DB_NAME;
	private string $DB_USER;
	private string $DB_PASSWORD;
	public string $DSN;

	public function __construct() {

		$this->DB_HOST = $_ENV['SERVER_IP'];
		$this->DB_PORT = $_ENV['DB_PORT'];
		$this->DB_NAME = $_ENV['DB_NAME'];
		$this->DB_USER = $_ENV['DB_USER'];
		$this->DB_PASSWORD = $_ENV['DB_PASSWORD'];

		$this->DSN = 'mysql:host=localhost:' . $this->DB_PORT . ';dbname=' . $this->DB_NAME;
	}

	public function getPDO(): PDO {
		return new PDO($this->DSN, $this->DB_USER, $this->DB_PASSWORD);
	}
}