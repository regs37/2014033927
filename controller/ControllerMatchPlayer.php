<?php 




	class ControllerMatchPlayer {
		private $helper; //helper [Object] for utility use such as string cleaning
		
		private $query = ""; //query [String] building
		
		private $database; //database [Object]
		
		private $updateCount = 0; //update column count [int]
		
		private $whereCount = 0; //query conditions count [int]
		
		private $table_name = "match_player"; // table name [int]

		private $table_col = [
		"id"			=>'id',
		"match_id"		=>'match_id',
		"player_id"		=>'user_id',
		"team"			=>'team',
		"type"			=>'type'];

		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}

		public function createEntity(){
			if($this->database->establish()){
				try{
					$connection = $this->database->getConnection();
					$sql="CREATE TABLE IF NOT EXISTS ".$this->table_name."(
					".$this->table_col["id"]." INT AUTO_INCREMENT PRIMARY KEY,
					".$this->table_col["match_id"]." INT NOT NULL,
					".$this->table_col["player_id"]." INT(11) UNSIGNED NOT NULL,
					".$this->table_col["team"]." VARCHAR(1) NOT NULL,
					".$this->table_col["type"]." VARCHAR(100) NOT NULL,
					FOREIGN KEY (".$this->table_col["match_id"].") REFERENCES matches(match_id),
					FOREIGN KEY (".$this->table_col["player_id"].") REFERENCES user(id)
					)";	
					$connection->exec($sql);
					$this->database->closeConnection();
				} catch(PDOException $e){
					echo $e->getMessage();
				}
			} else {
				echo $this->database->getError();
			}
		}
		public function insert($match){
			if($this->database->establish()){
				try{
					$connection = $this->database->getConnection();
					$sql = "INSERT INTO ".$this->table_name." (match_id,user_id,team,type)
					VALUES ('".$match->getMatchId()."','".$match->getPlayerId()."','".$match->getTeam()."','".$match->getType()."')";
					$stmt = $connection->prepare($sql);
					$stmt->execute();
					$connnection = null;
					$this->database->closeConnection();
					return true;
				} catch(PDOException $e){
					echo $e->getMessage();
				}
			}
			return false;
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


		public function getPlayerCount($match_id){
			if($this->database->establish()){
				$conn = $this->database->getConnection();
				$sql = "SELECT * FROM ".$this->table_name." WHERE ".$this->table_col["match_id"]."='".$match_id."' ";
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				$this->database->closeConnection();
				$players = $stmt->fetchAll();
				return sizeof($players);
			}
		}

	}




 ?>