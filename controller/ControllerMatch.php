<?php 




	class ControllerMatch {
		private $helper; //helper [Object] for utility use such as string cleaning
		
		private $query = ""; //query [String] building
		
		private $database; //database [Object]
		
		private $updateCount = 0; //update column count [int]
		
		private $whereCount = 0; //query conditions count [int]
		
		private $table_name = "matches"; // table name 

		private $table_col = [
		"match_id"		=>'match_id',
		"locality"		=>'locality',
		"location_long"	=>'location_long',
		"location_lat"	=>'location_lat',
		"gender"		=>'gender',
		"age"			=>'age',
		"sport_id"		=>'sport_id',
		"status" 		=>'status'];

		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}

		public function createEntity(){

			(new Schema)->table(Match::TABLE)
			->col(Match::ID)->int()->autoIncrement()->primary()
			->col(Match::LOCALITY)->varchar(255)->notNull()
			->col(Match::LONGITUDE)->varchar(100)->notNull()
			->col(Match::LATITUDE)->varchar(100)->notNull()
			->col(Match::GENDER)->varchar(20)->notNull()
			->col(Match::AGE)->int(3)->notNull()
			->col(Match::SPORT_ID)->int(11)->unsigned()->notNull()
			->col(Match::STATUS)->boolean()->notNull()
			->foreign(Sport::ID,"sports",Sport::ID)
			->create();
			
		}

		public function insert($match){
			if($this->database->establish()){
				$connection = $this->database->getConnection();
				$sql = "INSERT INTO ".$this->table_name." (locality,location_long,location_lat,gender,age,sport_id,status)
				VALUES ('".$match->getLocality()."','".$match->getLocationLong()."', '".$match->getLocationLat()."', '".$match->getGender()."',
				'".$match->getAge()."','".$match->getSportId()."','".$match->getStatus()."')";
				$stmt = $connection->prepare($sql);
				$stmt->execute();
				$connnection = null;
				$this->database->closeConnection();
				return true;
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
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

		public function isPlayerInGame($playerId,$gender){
			if($this->database->establish()){
				$conn = $this->database->getConnection();
				$sql = "SELECT matches.match_id,matches.locality,matches.location_long,matches.location_lat,matches.gender,matches.age,matches.sport_id,matches.status FROM ".$this->table_name." INNER JOIN match_player WHERE matches.match_id=match_player.match_id AND match_player.user_id='".$playerId."' AND matches.status=0 AND matches.gender='".$gender."' ";
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				$conn=null;
				$this->database->closeConnection();
				$matches = $stmt->fetchAll();
				if(sizeof($matches > 0)){
					if(isset($matches[0])){
						return ["result"=>true,"match_details"=>$matches[0]];
					}
				}
				return ["result"=>false,"match_details"=>null];
			}
		}

	}




 ?>