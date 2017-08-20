<?php 
	namespace Core;
	/**
	 * Database Credentials
	 */
	use \PDO;
	class Database {
		private $connection;
		private $established = false;
		private $error = "no error";

		public function __construct(){

		}	
		public function establish(){
			try {
				$this->connection = new PDO("mysql:host=".SERVER_NAME.";dbname=".DATABASE_NAME, USERNAME, PASSWORD);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return true;
			}
			catch(PDOException $e){
				$this->error = $e->getMessage();
				return false;
			}
		}	
		public function getConnection(){
			return $this->connection;
		}
		public function closeConnection(){
			$this->connection = null;
		}
		public function getError(){
			return $this->error;
		}
	}
 ?>