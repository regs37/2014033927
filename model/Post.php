<?php
	namespace Model;
	use Core\Query;
	class Post extends Query{
		public const TABLE = "user_post";
		public const POST_ID = "post_id";
		public const USER_ID = "user_id";
		public const CAPTION = "caption";
		public const DATE_POSTED = "date_posted";
		public const DATE_MODIFIED = "date_modified";
		
		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setPostId($post_id){
			$this->POST_ID = $post_id;
			return $this;
		}
		public function setUserId($user_id){
			$this->USER_ID = $user_id;
			return $this;
		}
		public function setCaption($caption){
			$this->CAPTION = $caption;
			return $this;
		}
		public function setDatePosted($date){
			$this->DATE_POSTED = $date;
			return $this;
		}
		public function setDateModified($date){
			$this->DATE_MODIFIED = $date;
			return $this;
		}
	

		public function getPostId(){
			return $this->POST_ID;
		}
		public function getUserId(){
			return $this->USER_ID;
		}
		public function getCaption(){
			return $this->CAPTION;
		}
		public function getDatePosted(){
			return $this->DATE_POSTED;
		}
		public function getDateModified(){
			return $this->DATE_MODIFIED;
		}


	}

?>