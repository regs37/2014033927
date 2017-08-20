<?php 
	namespace Controller;
	use \PDO;
	use Core\Helper;
	use Core\Query;
	use Core\Database;
	use Core\Request;
	use Core\JSONResponse;
	use Core\Route;
	use Model\Follow;
	use Model\User;
	use Model\Notification;
	use Service\ServiceFCM;

	class ControllerUser {
		
		public function __construct(){
			$this->createEntity();
			$this->query = "SELECT * FROM ".User::TABLE." ";
		}

		public function createEntity(){
			(new Schema)->table(User::TABLE)
			->col(User::ID)->int(11)->unsigned()->autoIncrement()->notNull()->primary()
			->col(User::NAME)->varchar(100)->notNull()
			->col(User::EMAIL)->varchar(100)->notNull()
			->col(User::GENDER)->varchar(20)->notNull()
			->col(User::BIRTHDATE)->varchar(100)->notNull()
			->col(User::HEIGHT)->varchar(50)
			->col(User::PROVIDER)->varchar(50)->notNull()
			->col(User::PROVIDER_ID)->varchar(50)->notNull()
			->col(User::USER_TOKEN)->longtext()->notNull()
			->col(User::ACCESS_TOKEN)->longtext()->notNull()
			->col(User::FOLLOWERS)->int(9)
			->col(User::FOLLOWING)->int(9)
			->col(User::TOTAL_MATCHES)->int(9)
			->col(User::DATE_CREATED)->varchar(100)->notNull()
			->col(User::DATE_SIGNIN)->varchar(100)->notNull()
			->col(User::PICTURE)->longtext()
			->col(User::MVP_RATING)->int(9)
			->col(User::IS_ONLINE)->boolean()
			->create();
		}

		public function updateFollower($user_id){
			return (new User)->update()
			->set(User::FOLLOWING,(new Follow)->select("COUNT(*)")->where(Follow::FOLLOWER_ID,$user_id))
			->set(User::FOLLOWERS,(new Follow)->select("COUNT(*)")->where(Follow::FOLLOWING_ID,$user_id))
			->where(User::ID,$user_id)
			->apply();
		}

		/**
		 * =============
		 * API Methods
		 * =============
		 */
		public static function login(){
			$request = Request::make([
					"email" 	=> "require|email",
					"name" 		=> "optional|alpha",
					"gender" 	=> "optional|alpha",
					"date" 		=> "optional|alpha",
					"picture" 	=> "optional|url",
					"height" 	=> "optional",
					"provider"	=> "optional|alpha",
					"provider_id" => "optional|num"
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$status = "old";
			$user = (new User)->where(User::PROVIDER_ID,$request->get("provider_id"))->get();
			if(sizeof($user) === 0){
				(new User)->insert()
				->value(User::EMAIL,$request->get("email"))
				->value(User::NAME,$request->get("name"))
				->value(User::GENDER,$request->get("gender"))
				->value(User::DATE,$request->get("date"))
				->value(User::PICTURE,$requset->get("picture"))
				->value(User::HEIGHT,$request->get("height"))
				->value(User::PROVIDER,$request->get("provider"))
				->value(User::PROVIDER_ID,$request->get("provider_id"))
				->value(User::USER_TOKEN,md5($request->get("provider_id")))
				->value(User::FOLLOWERS,0)
				->value(User::FOLLOWING,0)
				->value(User::TOTAL_MATCHES,0)
				->value(User::DATE_CREATED,Helper::date())
				->value(User::DATE_SIGNIN,Helper::date())
				->value(User::MVP_RATING,0)
				->value(User::IS_ONLINE,1)
				->init()->apply();
				$status = "new";
				$user = (new User)->where(User::PROVIDER_ID,$request->get("provider_id"))->get();
			} else {
				(new User)->update()
				->set(User::DATE_SIGNIN,Helper::date())
				->set(User::IS_ONLINE,1)
				->where(User::PROVIDER_ID,$request->get("provider_id"))->apply();
				$user = (new User)->where(User::PROVIDER_ID,$request->get("provider_id"))->get();
			}
			return JSONResponse::make(["error"=>false,"user_status"=>$status,"data"=>$user[0]]);
		}
		


		//--------------------------------------------------------------


		public static function editProfile(){
			$request = Request::make([
					"email" 	=> "require|email",
					"name" 		=> "optional|alpha",
					"gender" 	=> "optional|alpha",
					"date" 		=> "optional|alpha",
					"picture" 	=> "optional|url",
					"height" 	=> "optional",
					"provider"	=> "optional|alpha",
					"provider_id" => "optional|num"
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			
		}


		//--------------------------------------------------------------

		public static function logout(){
			$request = Request::make([
					"user_token" 	=> "require|alphaNum",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) === 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user token"]);
			}
			if(!(new User)->update()->set(User::DATE_SIGNIN,Helper::date())
				->set(User::IS_ONLINE,1)->where(User::USER_TOKEN,$request->get("user_token"))->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to update user status"]);
			}
			return JSONResponse::make(["error"=>false,"message"=>"Successfully logged out."]);
		}
		
		//--------------------------------------------------------------


		public static function access_token($bundle){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"access_token" => "require"
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user_token"]);
			}
			if(!Query::table(User::TABLE)->update()->set("access_token",$request->get("access_token"))->where("user_token",$request->get("user_token"))->apply()){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to update user access token"]);
			}
			return JSONResponse::make(["error"=>false,"message"=>"Successfully updated user access token"]);
		}
		

		//--------------------------------------------------------------


		public static function check_user($bundle){
			$request = Request::make([
					"provider_id" => "require|num",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$user = (new User)->where("provider_id",$request->get('provider_id'))->get();
			if(sizeof($user) == 0){
				return JSONResponse::make(["error"=>false,"user_status"=>"new","email"=>null,"user_token"=>null]);
			}
			return JSONResponse::make(["error"=>false,"user_status"=>"old","email"=>$user[0]["email"],"user_token"=>$user[0]["user_token"],"data"=>$user[0]]);
		}
		

		//--------------------------------------------------------------


		public static function follow_user(){
			$request = Request::make([
					"following_id" => "require|num",
					"follower_id" => "require|num",
				],Request::POST);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$followingUser = (new User)->where(User::ID,$request->get("following_id"))->get();
			$followerUser = (new User)->where(User::ID,$request->get("follower_id"))->get();
			if(sizeof($followingUser) == 0 || sizeof($followerUser) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid following id or follower id"]);
			}
			$isFollowed = Query::table("user_follow")->where("following_id",$request->get("following_id"))->where("follower_id",$request->get("follower_id"))->get();
			if($isFollowed){
				(new Follow)->delete()->where(Follow::FOLLOWING_ID,$request->get("following_id"))->where(Follow::FOLLOWER_ID,$request->get("follower_id"))->apply();
			} else {
				Query::table("user_follow")->insert()
				->value("following_id",$request->get("following_id"))
				->value("follower_id",$request->get("follower_id"))
				->init()->apply();
			}
			if(!self::updateFollower($request->get("following_id"))){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to update the following user data"]);
			}
			if(!self::updateFollower($request->get("follower_id"))){
				return JSONResponse::make(["error"=>true,"message"=>"Failed to update the follower user data"]);
			}
			if($isFollowed){
				return JSONResponse::make(["error"=>false,"message"=>"Successfully unfollowed this user"]);
			}
			
			return JSONResponse::make(["error"=>false,"message"=>"Successfully followed this user"]);
		}
		
		
		//--------------------------------------------------------------


		public static function search_user(){
			$request = Request::make([
					"search" => "require|alpha",
					"page" => "require|num",
					"limit" => "require|num",
				],Request::GET);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$pageOffset = (($request->get("page")-1)*$request->get("limit"));
			$result = self::_search($request->get("search"),$request->get("limit"),$pageOffset);
			$hasNext = self::_search($request->get("search"),$request->get("limit"),($request->get("page")*$request->get("limit")));
			if(sizeof($result) == 0){
				return JSONResponse::make(["error"=>false,"has_result"=>false,"message"=>"No results found","result_count"=>sizeof($results),"has_next"=>null,"data"=>null]);
			}
			$nextUrl = Route::$ROOT."/api/search_user/?search=".$request->get("search")."&page=".($request->get("page")+1)."&limit=".$request->get("limit")."&api_key=".APIKEY;
			return JSONResponse::make(["error"=>false,"has_result"=>true,"message"=>"Successfully retrieved results","result_count"=>sizeof($result),"has_next"=>(sizeof($hasNext) > 0),"data"=>$result,"next"=>(sizeof($hasNext) > 0)?$nextUrl:null]);
		}


		//--------------------------------------------------------------


		private static function _search($string,$pageLimit,$pageOffset){
			$database = new Database();
			if($database->establish()){
				$conn = $database->getConnection();
				$sql =  "SELECT * FROM ".User::TABLE." WHERE (`name` LIKE '%".$string."%') OR (`email` LIKE '%".$string."%') ORDER BY name ASC  LIMIT ".$pageOffset.", ".$pageLimit." ";
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				$connection=null;
				$database->closeConnection();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		}


		//--------------------------------------------------------------


		public static function get_user(){
			$request = Request::make([
					"user_token" => "require|alphaNum",
				],Request::GET);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$result = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($result) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"User not found"]);
			}
			return JSONResponse::make(["error"=>false,"message"=>"successfully retrieved user data","data"=>$result[0]]);
		}
		

		//--------------------------------------------------------------


		public static function get_follower(){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"page" => "require|num",
					"limit" => "require|num"
				],Request::GET);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$pageOffset = (($request->get("page")-1)*$request->get("limit"));
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user token."]);
			}
			$result = ((new User)->select()->in(User::ID,(new Follow)->select(Follow::FOLLOWER_ID)->where(Follow::FOLLOWING_ID,$user[0]["id"])->getQuery())->limit($pageOffset,$request->get("limit"))->get());
			$hasNext = ((new User)->select()->in(User::ID,(new Follow)->select(Follow::FOLLOWER_ID)->where(Follow::FOLLOWING_ID,$user[0]["id"])->getQuery())->limit((($request->get("page"))*$request->get("limit")),2)->get());
			if(sizeof($result) == 0){
				return JSONResponse::make(["error"=>false,"message"=>"No followers","followers"=>null,"has_followers"=>false]);
			}
			$nextUrl = Route::get_full_path()["base"]."/api/get_follower/?user_token=".$request->get("user_token")."&page=".($request->get("page")+1)."&limit=".$request->get("limit")."&api_key=".APIKEY;
			return JSONResponse::make(["error"=>false,"message"=>"Successfully retrieved followers","followers"=>$result,"has_followers"=>true,"next"=>((sizeof($hasNext)>0)?$nextUrl:null)]);
		}
		

		//--------------------------------------------------------------


		public static function get_following(){
			$request = Request::make([
					"user_token" => "require|alphaNum",
					"page" => "require|num",
					"limit" => "require|num"
				],Request::GET);
			if($request->hasError()){ return JSONResponse::make($request->getError()); }
			$pageOffset = (($request->get("page")-1)*$request->get("limit"));
			$user = (new User)->where(User::USER_TOKEN,$request->get("user_token"))->get();
			if(sizeof($user) == 0){
				return JSONResponse::make(["error"=>true,"message"=>"Invalid user token."]);
			}
			$result = ((new User)->select()->in(User::ID,(new Follow)->select(Follow::FOLLOWING_ID)->where(Follow::FOLLOWER_ID,$user[0]["id"])->getQuery())->limit($pageOffset,$request->get("limit"))->get());
			$hasNext = ((new User)->select()->in(User::ID,(new Follow)->select(Follow::FOLLOWING_ID)->where(Follow::FOLLOWER_ID,$user[0]["id"])->getQuery())->limit((($request->get("page"))*$request->get("limit")),2)->get());
			if(sizeof($result) == 0){
				return JSONResponse::make(["error"=>false,"message"=>"You haven't followed anyone yet.","following"=>null,"has_following"=>false]);
			}
			$nextUrl = Route::get_full_path()["base"]."/api/get_following/?user_token=".$request->get("user_token")."&page=".($request->get("page")+1)."&limit=".$request->get("limit")."&api_key=".APIKEY;
			return JSONResponse::make(["error"=>false,"message"=>"Successfully retrieved following","following"=>$result,"has_following"=>true,"next"=>((sizeof($hasNext)>0)?$nextUrl:null)]);
		}


		//--------------------------------------------------------------


		
	}
?>