<?php 

	namespace Model;
	use Core\Query;
	class Like extends Query {
		public const TABLE = "user_post_like";
		public const ID = "id";
		public const POST_ID = "post_id";
		public const USER_ID = "user_id";

		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}
		public function setId($id){
			$this->ID = $id;
			return $this;
		}
		public function setPostId($id){
			$this->POST_ID = $id;
			return $this;
		}
		public function setUserId($id){
			$this->USER_ID = $id;
			return $this;
		}
		public function getId(){
			return $this->ID;
		}
		public function getPostId(){
			return $this->POST_ID;
		}
		public function getUserId(){
			return $this->USER_ID;
		}
	}
