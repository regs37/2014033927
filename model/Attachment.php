<?php
	namespace Model;
	use Core\Query;
	class Attachment extends Query {
		public const TABLE = "user_post_attach";
		public const ID = "attach_id";
		public const POST_ID = "post_id";
		public const TYPE = "attach_type";
		public const URL = "attach_url";

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
		public function setType($type){
			$this->TYPE = $type;
			return $this;
		}
		public function setUrl($url){
			$this->URL = $url;
			return $this;
		}
		
		public function getId(){
			return $this->ID;
		}
		public function getPostId(){
			return $this->POST_ID;
		}
		public function getType(){
			return $this->TYPE;
		}
		public function getUrl(){
			return $this->URL;
		}
	}
