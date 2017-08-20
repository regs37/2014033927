<?php 
	namespace Controller;
	use \PDO;
	use Core\Helper;
	use Core\Query;
	use Core\Database;
	use Core\Request;
	use Core\JSONResponse;
	use Model\Comment;
	use Model\Post;
	use Model\Notification;
	use Model\User;
	use Service\ServiceFCM;

	class ControllerComment {

		public function __construct(){
			$this->createEntity();
		}

		private function createEntity(){
			(new Schema)->table(Comment::TABLE)
			->col(Comment::ID)->int(11)->unsigned()->autoIncrement()->notNull()->primary()
			->col(Comment::USER_ID)->int(11)->unsigned()->notNull()
			->col(Comment::POST_ID)->int(11)->unsigned()->notNull()
			->col(Comment::COMMENT)->longtext()->notNull()
			->col(Commnet::DATE_POSTED)->varchar(100)->notNull()
			->col(Comment::DATE_MODIFIED)->varchar(100)->notNull()
			->foreign(Comment::USER_ID,User::TABLE,User::ID)->onDelete(Schema::CASCADE)
			->foreign(Comment::POST_ID,"user_post","post_id")->onDelete(Schema::CASCADE)
			->create();
		}
		
		public function addComment($bundle){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"post_id" => "require|num",
					"comment" => "require",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			$post = (new Post)->where(Post::POST_ID,$request->get("post_id"))->get();
			if(sizeof($user) == 0 || sizeof($post) == 0){ 
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user token or post id"]);
			}
			if((new Comment)->insert()
				->value(Comment::USER_ID,$user[0]["id"])
				->value(Comment::POST_ID,$request->get("post_id"))
				->value(Comment::COMMENT,$request->get("comment"))
				->value(Comment::DATE_POSTED,Helper::date())
				->value(Comment::DATE_MODIFIED,Helper::date())
				->init()->apply()){
				if($post[0]["user_id"] != $user[0]["id"]){
					$owner = (new User)->where(User::ID,$post[0]["user_id"])->get()[0];
					$notif = (new Notification)->insert()
					->value(Notification::OWNER_ID,$post[0]["user_id"])
					->value(Notification::ACTOR_ID,$user[0]["id"])
					->value(Notification::POST_ID,$post[0]["post_id"])
					->value(Notification::TYPE,Notification::COMMENT)
					->value(Notification::DATE,Helper::date())
					->value(Notification::STATUS,"0")
					->init()->apply();
					if($notif){
						ServiceFCM::title("Sportify")
						->message(ucwords(strtolower($user[0]["name"]))." commented on your post")
						->picture($user[0]["picture"])
						->send($owner["access_token"]);
						return JSONResponse::make(["error"=>false,"message"=>"Successfully liked the post and sent a notification","owner_access_token"=>$owner["access_token"]]);
					} else {
						return JSONResponse::make(["error"=>true,"message"=>"Failed to send notification"]); 
					}
				}
				return JSONResponse::make(["error"=>false,"message"=>"Successfully added new comment."]);
			} else {
				return JSONResponse::make(["error"=>true,"message"=>"Adding comment failed."]);
			}
		}

		public static function deleteComment($bundle){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"comment_id" => "require|num",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$comment = (new Comment)->where(Comment::ID,$request->get("comment_id"))->get();
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($comment) == 0 || sizeof($user) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user_token or comment_id"]);
			}
			if($comment[0]["user_id"] != $user[0]["id"]){
				return JSONResponse::make(["error"=>true,"message"=>"Unauthorized deletion of comment"]);
			}
			if((new Comment)->delete()->where(Comment::ID,$request->get("comment_id"))->where(Comment::USER_ID,$user[0]["id"])->apply()){
    			(new Notification)->delete()
				->where(Notification::ACTOR_ID,$user[0]["id"])
				->where(Notification::POST_ID,$comment[0]["post_id"])
				->where(Notification::TYPE,Notification::COMMENT)
				->apply();
    			return json_encode(["error"=>false,"message"=>"successfully deleted comment"]);
    		} else {
    			return json_encode(["error"=>false,"message"=>"Failed to delete comment"]);
    		}
		}

		public static function getComment($bundle){
			$request = Request::make([
					"post_id" => "require|num",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$post = (new Post)->where(Post::POST_ID,$request->get("post_id"))->get();
			if(sizeof($post) == 0){ return JSONResponse::make(["error"=>true,"message"=>"Invalid post_id"]); }
			$comments =  (new Comment)->where(Comment::POST_ID,$post[0]["post_id"])->get();
			if(sizeof($comments) > 0){
				$data = array();
				foreach ($comments as $key => $comment) {
					$user = Query::table(User::TABLE)->select()->where(User::ID,$comment["user_id"])->get();
					$data[] = [
						"comment_id"=>$comment["comment_id"],
						"user_data"=>[
							"name"=>$user[0]["name"],
							"user_token"=>$user[0]["user_token"],
							"picture"=>$user[0]["picture"]
						],
						"comment"=>$comment["comment"],
						"date_posted"=>Helper::time_ago($comment["date_posted"]),
						"date_modified"=>Helper::time_ago($comment["date_modified"]),
					];
				}
				return JSONResponse::make(["error"=>false,"message"=>"Successfully retrieved comments","comment_count"=>sizeof($comments),"has_comment"=>((sizeof($comments)>0)?true:false),"data"=>$data]);
			}
			return JSONResponse::make(["error"=>false,"message"=>"No comments","comment_count"=>sizeof($comments),"has_comment"=>false,"data"=>false]);
		}

	}



?>