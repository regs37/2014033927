<?php
	namespace Core;

	use Core\Database;
	use Core\Helper;
	
	class Schema {
		
		private $tablename;
		private $query = "";
		private $database;
		private $helper;
		private $updateCount = 0;
		private $colCount = 0;
		private $hasPrimary = false;
		private $hasForeign = false;
		private $whereCount = 0;
		public const CASCADE = "cascade";
		public const SETNULL = "set null";

		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
		}
		public function table($tablename){
			$this->query.= $sql="CREATE TABLE IF NOT EXISTS ".$tablename."(";
			return $this;
		}
		public function col($col){
			if($this->colCount > 0){
				$this->query.= " , ";
			}
			$this->query.= $col;
			$this->colCount++;
			return $this;
		}
		public function int($size = null){
			$this->query .= " INT";
			if($size != null){
				$this->query .= "(".$size.")";
			}
			return $this;
		}
		public function varchar($size = null){
			$this->query .= " VARCHAR";
			if($size != null){
				$this->query .= "(".$size.")";
			}
			return $this;
		}
		public function boolean(){
			$this->query .= " BOOLEAN ";
			return $this;
		}
		public function longtext(){
			$this->query .= " LONGTEXT ";
			return $this;
		}
		public function unsigned(){
			$this->query .= " UNSIGNED ";
			return $this;
		}
		public function notNull(){
			$this->query .= " NOT NULL ";
			return $this;
		}
		public function null(){
			$this->query .= " NULL ";
			return $this;
		}
		public function autoIncrement(){
			$this->query .= " AUTO_INCREMENT ";
			return $this;
		}
		public function primary(){
			if(!$this->hasPrimary){
				$this->query .= " PRIMARY KEY";
				$this->hasPrimary = true;
			}
			return $this;
		}
		public function foreign($column,$referenceTable,$referenceCol){
			if($this->colCount > 0){
				$this->query.= ",";
			}
			$this->query.= " FOREIGN KEY (".$column.") REFERENCES ".$referenceTable."(".$referenceCol.") ";
			$this->colCount++;
			$this->hasForeign = true;
			return $this;
		}
		public function onDelete($action){
			$action = ($action=="cascade" || $action == "set null")?" ON DELETE ".$action:"";
			if($this->hasForeign){
		 		$this->query.= $action;
		 		$this->hasForeign = false;
			}
			return $this;
		}
		public function create(){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				//echo $this->getQuery()."<br><br>";
				$connection->exec($this->query.")");
				$this->database->closeConnection();
			}
		}
		public function getQuery(){
			return $this->query.")";
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
		public function whereCondition($column,$value,$operator){
			if($this->whereCount > 0){
				$this->query .= " AND ".$column.$operator."'".$value."' ";
			} else {
				$this->query .= " WHERE ".$column.$operator."'".$value."' ";
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
				return $stmt->fetchAll();
			}
		} 

		/**
		 * Updating Queries
		 * @var $user 
		 */
		public function update(){
			$this->updateCount = 0;
			$this->query = "UPDATE ".$this->table_name." SET ";
			return $this;
		}
		public function set($column,$value){
			if($this->updateCount > 0){
				$this->query .= ", ".$column." = '".$value."' ";
			} else {
				$this->query .= " ".$column." = '".$value."' ";
			}
			$this->updateCount++;
			return $this;
		}
		/**
		 * 
		 */
		public function apply(){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				try {
					$stmt = $connection->prepare($this->query);
				    // execute the query
				    $stmt->execute();
				    return true;
			    }
				catch(PDOException $e) {
					var_dump($e);
			    	return false;
			    }
				$connection=null;
				$this->database->closeConnection();
			}
		}
	}

?>