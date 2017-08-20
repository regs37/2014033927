<?php
	namespace Model;
	use Core\Query;
	class Comment extends Query {
		public const TABLE = "user_post_comment";
		public const ID = "comment_id";
		public const USER_ID = "user_id";
		public const POST_ID = "post_id";
		public const COMMENT = "comment";
		public const DATE_POSTED = "date_posted";
		public const DATE_MODIFIED = "date_modified";

		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setCommentId($commentId){
			$this->COMMENT_ID = $commentId;
			return $this;
		}
		public function setUserId($userId){
			$this->USER_ID = $userId;
			return $this;
		}
		public function setPostId($postId){
			$this->POST_ID = $postId;
			return $this;
		}
		public function setComment($comment){
			$this->COMMENT = $comment;
			return $this;
		}
		public function setDatePosted($datePosted){
			$this->DATE_POSTED = $datePosted;
			return $this;
		}
		public function setDateModified($dateModified){
			$this->DATE_MODIFIED = $dateModified;
			return $this;
		}

		public function getCommentId(){
			return $this->COMMENT_ID;
		}
		public function getUserId(){
			return $this->USER_ID;
		}
		public function getPostId(){
			return $this->POST_ID;
		}
		public function getComment(){
			return $this->COMMENT;
		}
		public function getDatePosted(){
			return $this->DATE_POSTED;
		}
		public function getDateModified(){
			return $this->DATE_MODIFIED;
		}
	}

?>