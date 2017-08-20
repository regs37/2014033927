<?php 
	namespace Core;
	use \PDO;
	class Query{

		public const ASC = "ASC";

		public const DESC = "DESC";

		private $query;
		
		private $database;
		
		private $insertCol = array();

		private $insertVal = array();

		private $updateCount;
		
		private $whereCount;

		private $starting = false;
		
		private $error = "";

		private static $tableName;

		protected function __construct(){
			$this->database = new Database();
			$this->reset();
		}
		public static function table($tableName){
			self::$tableName = $tableName;
			return new self();
		}
		public function select($columns = "*"){
			$this->reset();
			$this->starting = true;
			$this->query = "SELECT ".$columns." FROM ".self::$tableName."";
			return $this;
		}
		public function insert(){
			$this->reset();
			$this->starting = true;
			$this->query = "INSERT INTO ".self::$tableName."";
			return $this;
		}
		public function value($col,$value){
			$this->insertVal[] = $value;
			$this->insertCol[] = $col;
			return $this;
		}
		public function init(){
			if(sizeof($this->insertVal) > 0){
				$this->query .=" ( ";
				foreach ($this->insertCol as $key => $value) {
					if($key != (sizeof($this->insertCol) - 1)){
						$this->query .= $value.",";
					} else {
						$this->query .= $value;
					}
				}
				$this->query .=") VALUES (";
				foreach ($this->insertVal as $key => $value) {
					if($key != (sizeof($this->insertVal)-1)){
						$this->query .= "'".$value."',";
					} else {
						$this->query .= "'".$value."'";
					}
				}
				$this->query .=" )";
			}
			return $this;
		}
		public function where($column,$value,$condition="="){
			if(!$this->starting){
				$this->select();
			}
			$this->query .= (($this->whereCount > 0)?" AND ":" WHERE ").$column." ".$condition." '".$value."' ";
			$this->whereCount++;
			return $this;
		}
		public function limit($offset,$limit){
			$this->query .= " LIMIT ".$offset.",".$limit;
			return $this;
		}
		public function order($column,$order){
			$this->query .= " ORDER BY ".$column." ".$order;
			return $this;
		}
		public function get(){
			if($this->database->establish()){
				$conn = $this->database->getConnection();
				$stmt = $conn->prepare($this->query);
				$stmt->execute();
				$conn = null;
				$this->database->closeConnection();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		}
		public function update(){
			$this->reset();
			$this->starting = true;
			$this->query = "UPDATE ". self::$tableName . " SET ";
			return $this;
		}
		public function in($column,$query){
			if(!$this->starting){
				$this->select();
			}
			$this->query .= (($this->whereCount == 0)?" WHERE ":" AND ").$column." IN (".$query.")";
			$this->whereCount++;
			return $this;
		}
		public function set($column,$value){
			if(is_object($value)){
				$value = "(".$value->getQuery().")";
			} else {
				$value = " '".$value."' ";
			}
			$this->query .= (($this->updateCount > 0)?", ":" ").$column."=".$value;
			$this->updateCount++;
			return $this;
		}
		public function apply(){
			if($this->database->establish()){
				$conn = $this->database->getConnection();
				try{
					$stmt = $conn->prepare($this->query);
					$stmt->execute();
					return true;
				} catch(PDOException $ex){
					$this->error = $ex;
				} finally {
					$conn = null;
					$this->database->closeConnection();
				}
			}
			return false;
		}
		public function delete(){
			$this->reset();
			$this->starting = true;
			$this->query .= "DELETE FROM ".self::$tableName." ";
			return $this;
		}
		public function getError(){
			return $this->error;
		}

		public function getQuery(){
			return $this->query;
		}

		private function reset(){
			$this->query = "";
			$this->whereCount = 0;
			$this->updateCount = 0;
			$this->starting = false;
		}
	}




?>