<?php 
	namespace Model;
	use Core\Query;
	class Match extends Query{
		public const TABLE = "matches";
		public const ID = "match_id";
		public const LOCALITY = "locality";
		public const LONGITUDE = "location_long";
		public const LATITUDE = "location_lat";
		public const GENDER = "gender";
		public const AGE = "age";
		public const SPORT_ID = "sport_id";
		public const STATUS = "status";
		
		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setMatchId($match_id){
			$this->MATCH_ID = $match_id;
			return $this;
		}
		public function setLocality($locality){
			$this->LOCALITY = $locality;
			return $this;
		}
		public function setLocationLong($location_long){
			$this->LONGITUDE = $location_long;
			return $this;
		}
		public function setLocationLat($location_lat){
			$this->LATITUDE = $location_lat;
			return $this;
		}
		public function setGender($gender){
			$this->GENDER = $gender;
			return $this;
		}
		public function setAge($age){
			$this->AGE = $age;
			return $this;
		}
		public function setSportId($sport){
			$this->SPORT_ID = $sport;
			return $this;
		}
		public function setStatus($status){
			$this->STATUS = $status;
			return $this;
		}

		public function getMatchId(){
			return $this->MATCH_ID;
		}
		public function getLocality(){
			return $this->LOCALITY;
		}
		public function getLocationLong(){
			return $this->LONGITUDE;
		}
		public function getLocationLat(){
			return $this->LATITUDE;
		}
		public function getGender(){
			return $this->GENDER;
		}
		public function getAge(){
			return $this->AGE;
		}
		public function getSportId(){
			return $this->SPORT_ID;
		}
		public function getStatus(){
			return $this->STATUS;
		}
	}

?>