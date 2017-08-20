<?php 
	namespace Controller;

	use \PDO;
	use Core\Helper;
	use Core\Query;
	use Core\Database;	
	use Core\Request;
	use Core\Route;
	use Core\JSONResponse;
	use Model\Like;
	use Model\Attachment;
	use Model\Comment;
	use Model\Notification;
	use Model\Post;
	use Model\User;
	use Service\ServiceFCM;


	class ControllerPost{
		private static $helper;
		
		private  static $database;

		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}
		
		public function createEntity(){
			(new Schema)->table(Post::TABLE)
			->col(Post::POST_ID)->int(11)->unsigned()->autoIncrement()->notNull()->primary()
			->col(Post::USER_ID)->int(11)->unsigned()->notNull()
			->col(Post::CAPTION)->longtext()->notNull()
			->col(Post::DATE_POSTED)->varchar(100)->notNull()
			->col(Post::DATE_MODIFIED)->varchar(100)->notNull()
			->foreign(Post::USER_ID,User::TABLE,User::ID)->onDelete(Schema::CASCADE)
			->create();
		}


		/**
		 * API Methods
		 */
		public static function getPost(){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"page" => "optional|num",
					"limit" => "optional|num",
					"category" => "require|alpha"
				],Request::GET);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user token"]);
			}
			return self::_get_post_category($request,$user);
		}

		private function _get_post_category($request,$user){
			$pageOffset = (($request->get("page")-1)*$request->get("limit"));
			$posts = null; $nextPosts = null;
			switch($request->get("category")){
				case "profile":
					$posts = (new Post)->where(Post::USER_ID,$user[0]["id"])->order(Post::POST_ID,Query::DESC)->limit($pageOffset,$request->get("limit"))->get();
					$nextPosts = (new Post)->where(Post::USER_ID,$user[0]["id"])->order(Post::POST_ID,Query::DESC)->limit((($request->get("page"))*$request->get("limit")),2)->get();
					break;
				case "newsfeed":
					$posts = self::_getNewsFeedPost($user,$pageOffset,$request->get("limit"));
					$nextPosts = self:: _getNewsFeedPost($user,(($request->get("page"))*$request->get("limit")),2);
					break;
				default:
					return JSONResponse::make(["error"=>false,"message"=>"Invalid category"]);
					break;
			}
			return self::_get_post_construct($posts,$nextPosts,$request,$user);
		}

		private function _get_post_construct($posts,$nextPosts,$request,$user){
			$data = array();
			foreach($posts as $post){
				$userPosted = (new User)->where(User::ID,$post["user_id"])->get()[0];
				$attachments = (new Attachment)->where(Attachment::POST_ID,$post["post_id"])->get();
				$likes = (new Like)->select("COUNT(*)")->where(Like::POST_ID,$post["post_id"])->get();
				$comments = (new Comment)->select("COUNT(*)")->where(Comment::POST_ID,$post["post_id"])->get();
				$checkLiked = (new Like)->where(Like::USER_ID,$user[0]["id"])->where(Like::POST_ID,$post["post_id"])->get();
				if(sizeof($attachments) == 0) { $attachments=null;}
				$data[] = [
					"user_data"=>[
						"name"=>$userPosted["name"],
						"picture"=>$userPosted["picture"],
						"user_token"=>$userPosted["user_token"]
					],
					"post_id"=>$post["post_id"],
					"caption"=>$post["caption"],
					"date_posted"=>Helper::time_ago($post["date_posted"]),
					"date_modified"=>Helper::time_ago($post["date_modified"]),
					"attachments"=>$attachments,
					"total_likes"=>$likes[0]["COUNT(*)"],
					"total_comments"=>$comments[0]["COUNT(*)"],
					"total_attachments"=>sizeof($attachments),
					"is_liked"=>((sizeof($checkLiked) > 0)?true:false)
				];
			}
			$nextUrl = Route::get_full_path()["base"]."/api/get_post/?user_token=".$request->get("user_token")."&page=".($request->get("page")+1)."&limit=".$request->get("limit")."&category=".$request->get("category")."&api_key=".APIKEY;
			return JSONResponse::make(["error"=>false,"message"=>"Successfully retrieved posts from user","post_count"=>sizeof($posts),"data"=>$data,"has_next"=>((sizeof($nextPosts) > 0)?true:false),"next"=>((sizeof($nextPosts) > 0)?$nextUrl:null)]);
		}

		private function _getNewsFeedPost($user,$pageOffset,$limit){
			$database = new Database();
			if($database->establish()){
				$conn =  $database->getConnection();
				$sql = "SELECT * FROM user_post WHERE user_id='".$user[0]["id"]."' OR user_id IN (SELECT following_id FROM user_follow WHERE follower_id='".$user[0]["id"]."') ORDER BY date_posted DESC LIMIT ".$pageOffset.", ".$limit." ";
				$stmt = $conn->prepare($sql);
				$stmt->execute();

				$database->closeConnection();
				$conn = null;
				return $stmt->fetchAll();
			} else {
				return JSONResponse::make(["error"=>false,"message"=>"Connection Failed"]);
			}
		}

		public static function addPost(){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"caption" => "require",
					"attachment" => "optional"
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			self::$database = new Database();
			if(self::$database->establish()){
				$connection = self::$database->getConnection();
				$sql = Query::table(Post::TABLE)->insert()
				->value(Post::USER_ID,$user[0]["id"])
				->value(Post::CAPTION,$request->get("caption"))
				->value(Post::DATE_POSTED,Helper::date())
				->value(Post::DATE_MODIFIED,Helper::date())
				->init()->getQuery();
				$connection->exec($sql);
				$last_id = $connection->lastInsertId();
				
				if(sizeof($request->get("attachment"))){
					$jsonData = json_decode($request->get("attachment"));
					foreach($jsonData as $jsonItem){
						(new Attachment)->insert()
						->value(Attachment::POST_ID,$last_id)
						->value(Attachment::TYPE,$jsonItem->type)
						->value(Attachment::URL,$jsonItem->url)
						->init()->apply();
					}
				}
				$connection=null;
				self::$database->closeConnection();
				return JSONResponse::make(["erorr"=>false,"message"=>"Successfully posted a new status"]);
			}
		}
		public static function deletePost(){
			$request = Request::make([
					"post_id" => "require|num",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$post = (new Post)->where(Post::POST_ID,$_POST["post_id"])->get();
			if(sizeof($post) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid post id or the post is already been deleted."]);
			}
			if(!(new Like)->delete()->where(Like::POST_ID,$post[0]["post_id"])->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to delete likes"]);
			}
			if(!(new Comment)->delete()->where(Comment::POST_ID,$post[0]["post_id"])->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to delete comments"]);
			}
			if(!(new Attachment)->delete()->where(Attachment::POST_ID,$post[0]["post_id"])->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to delete attachmentsx"]);
			}
			if(!(new Notification)->delete()->where(Notification::POST_ID,$post[0]["post_id"])->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to delete notifications"]);
			}
			if(!(new Post)->delete()->where(Post::POST_ID,$post[0]["post_id"])->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to delete post"]);
			}
			return JSONResponse::make(["error"=>false,"message"=>"Sucessfully delete post"]);
		}
		public static function likePost(){
			$request = Request::make([
					"post_id" => "require|num",
					"user_token"=> "require|alphaNum"
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$post = (new Post)->where(Post::POST_ID,$request->get("post_id"))->get();
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) == 0 || sizeof($post) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid post id or user token."]);
			}
			$result = (new Like)->where(Like::POST_ID,$post[0]["post_id"])->where(Like::USER_ID,$user[0]["id"])->get();
			if(sizeof($result) == 0){
				(new Like)->insert()
				->value(Like::POST_ID,$post[0]["post_id"])
				->value(Like::USER_ID,$user[0]["id"])
				->init()->apply();
				if($post[0]["user_id"] != $user[0]["id"]){
					$owner = (new User)->where(User::ID,$post[0]["user_id"])->get()[0];
					$notif = (new Notification)->insert()
					->value(Notification::OWNER_ID,$post[0]["user_id"])
					->value(Notification::ACTOR_ID,$user[0]["id"])
					->value(Notification::POST_ID,$post[0]["post_id"])
					->value(Notification::TYPE,Notification::LIKE)
					->value(Notification::DATE,Helper::date())
					->value(Notification::STATUS,"0")
					->init()->apply();
					if($notif){
						ServiceFCM::title("Sportify")
						->message(ucwords(strtolower($user[0]["name"]))." liked your post")
						->picture($user[0]["picture"])
						->send($owner["access_token"]);
						return JSONResponse::make(["error"=>false,"message"=>"Successfully liked the post and sent a notification","owner_access_token"=>$owner["access_token"]]);
					} else {
						return JSONResponse::make(["error"=>true,"message"=>"Failed to send notification"]); 
					}
				}
			} else {
				if(!(new Notification)->delete()->where(Notification::POST_ID,$post[0]["post_id"])->where(Notification::ACTOR_ID,$user[0]["id"])->where(Notification::TYPE,Notification::LIKE)->apply()){
					return JSONResponse::make(["error"=>true,"message"=>"Failed to delete notifications"]);
				}
				if(!(new Like)->delete()->where(Like::USER_ID,$user[0]["id"])->where(Like::POST_ID,$post[0]["post_id"])->apply()){
					return JSONResponse::make(["error"=>true,"message"=>"Connection Failed"]);
				}
			}
			return JSONResponse::make(["error"=>false,"message"=>"Successfully unliked the post"]);
		}
		
	}

?>