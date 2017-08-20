<?php
	namespace Model;
	use Core\Query;
	class Notification extends Query{
		public const TABLE = "user_notification";
		public const NOTIF_ID = "notif_id";
		public const OWNER_ID = "owner_id";
		public const ACTOR_ID = "actor_id";
		public const POST_ID = "post_id";
		public const TYPE = "type";
		public const DATE = "date";
		public const STATUS = "status";

		public const COMMENT = "comment";
		public const LIKE = "like";
		
		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}
		
		public function setNotifId($notifId){
			$this->NOTIF_ID = $notifId;
			return $this;
		}
		public function setOwnerId($ownerId){
			$this->OWNER_ID = $ownerId;
			return $this;
		}
		public function setActorId($actorId){
			$this->ACTOR_ID = $actorId;
			return $this;
		}
		public function setPostId($postId){
			$this->POST_ID = $postId;
			return $this;
		}
		public function setType($type){
			$this->TYPE = $type;
			return $this;
		}
		public function setDate($date){
			$this->DATE = $date;
			return $this;
		}
		public function setStatus($status){
			$this->STATUS = $status;
			return $this;
		}

		public function getNotifId(){
			return $this->NOTIF_ID;
		}
		public function getOwnerId(){
			return $this->OWNER_ID;
		}
		public function getActorId(){
			return $this->ACTOR_ID;
		}
		public function getPostId(){
			return $this->POST_ID;
		}
		public function getType(){
			return $this->TYPE;
		}
		public function getDate(){
			return $this->DATE;
		}
		public function getStatus(){
			return $this->STATUS;
		}
	}