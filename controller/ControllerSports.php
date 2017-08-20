<?php 

	class ControllerSports{
		private $helper; //helper [Object] for utility use such as string cleaning
		
		private $query = ""; //query [String] building
		
		private $database; //database [Object]
		
		private $updateCount = 0; //update column count [int]
		
		private $whereCount = 0; //query conditions count [int]
		
		private $table_name = "sports"; // table name [int]

		private $table_col = [
		"sport_id"		=>'sport_id',
		"name"			=>'name',
		"player"		=>'player'];

		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}

		public function createEntity(){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				$sql="CREATE TABLE IF NOT EXISTS ".$this->table_name."(
				".$this->table_col["sport_id"]." INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				".$this->table_col["name"]." VARCHAR(100) NOT NULL,
				".$this->table_col["player"]." INT(3) NOT NULL
				)";
				$connection->exec($sql);
				$this->database->closeConnection();
			}
		}


		public function delete($sport){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				$sql = "DELETE FROM ".$this->table_name." WHERE ".$this->table_col["sport_id"]."='".$sport->getId()."' ";
				$connection->exec($sql);
				$connection=null;
				$this->database->closeConnection();
				return ["error"=>true,"message"=>"successfully deleted a sport."];
			} else {
				return ["error"=>true,"message"=>"No Internet connection."];
			}
		}


		/**
		 * Insertion Query
		 * @var $user = data to be inserted
		 */
		public function insert($sport){
			$h = $this->helper;
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				$sql = "INSERT INTO ".$this->table_name." (name,player) 
				VALUES ('".$sport->getName()."','".$sport->getPlayer()."'	) ";
				$connection->exec($sql);
				$connection=null;
				$this->database->closeConnection();
				return ["error"=>false,"message"=>"successfully created a sport"];
			} else {
				return ["error"=>true,"message"=>"no internet connection"];
			}
		}

		/**
		 * Selection Queries
		 */
		public function select(){
			$this->whereCount = 0;
			$this->query = "SELECT * FROM ".$this->table_name." ";
			return $this;
		}
		public function where($column,$value){
			if($this->whereCount > 0){
				$this->query .= " AND ".$column."='".$value."' ";
			} else {
				$this->query .= " WHERE ".$column."='".$value."' ";
			}
			$this->whereCount++;
			return $this;
		}
		public function whereNot($column,$value){
			if($this->whereCount > 0){
				$this->query .= " AND ".$column."<>'".$value."' ";
			} else {
				$this->query .= " WHERE ".$column."<>'".$value."' ";
			}
			$this->whereCount++;
			return $this;
		}
		public function limit($startingpoint,$numberofitemsperpage){
			$this->query .= " LIMIT ".$numberofitemsperpage." OFFSET ".$startingpoint." ";
			return $this;
		}
		public function order($column,$order){
			$order_temp = "ASC";
			if($order === "DESC"){ $order_temp = "DESC"; }
			$this->query .= " ORDER BY ".$column." ".$order_temp;
			return $this;
		}
		public function get(){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				$stmt = $connection->prepare($this->query);
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				$connection=null;
				$this->database->closeConnection();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} 

		/**
		 * Validation Queries
		 * Params
		 * @var refColumn = reference column where the condition to be applied
		 * @var refValue = Value to be checked.
		 * @return boolean
		 */
		public function isExist($refColumn,$refValue){
			if($this->database->establish()){
				$conn = $this->database->getConnection();
				$stmt = $conn->prepare("SELECT * FROM ".$this->table_name." WHERE ".$refColumn."='".$refValue."' ");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if( !$row){
				    return false;
				} else {
					return true;
				}
				$connection=null;
				$this->database->closeConnection();
			}
		}


	}
 ?>