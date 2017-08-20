<?php 
	namespace Model;
	use Core\Query;
	class Follow extends Query {
		public const TABLE = "user_follow";
		public const ID = "id";
		public const FOLLOWING_ID = "following_id";
		public const FOLLOWER_ID = "follower_id";

		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setId($id){
			$this->ID = $id;
			return $this;
		}
		public function setFollowingId($id){
			$this->FOLLOWING_ID = $id;
			return $this;
		}
		public function setFollowerId($id){
			$this->FOLLOWER_ID = $id;
			return $this;
		}

		public function getId(){
			return $this->ID;
		}
		public function getFollowingId(){
			return $this->FOLLOWING_ID;
		}
		public function getFollowerId(){
			return $this->FOLLOWER_ID;
		}
	}

 ?>