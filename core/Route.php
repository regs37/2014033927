<?php 
	namespace Core;
	use Core\Request;
	use Core\Bunlde;
	use Core\Helper;
	use Controller;
	/**
	 * Class Route
	 * A url route handler.
	 */
	class Route {

		public static $ROOT="";
		/**
		 * @var $routes Array of (String) Routes
		 */
		private static $routes = array();

		/**
		* @var $bundle Array of named routes
		*/
		private static $routeBundle = array();

		/**
		 * URL Path
		 * URL structure
		 */
		private static $path = array();

		/**
		 * Request Path
		 */
		private static $request_path;

		/**
		 * URL bundle
		 */
		private static $url_variables;

		/**
		 * Holder for used route instantiated
		 */
		private static $current_route;

		/**
		 * Holder for used route instantiated method
		 */
		private static $current_method;

		/**
		 * Holder for used route type being instantiated
		 */
		private static $current_type;
		
		/**
		 * A method that executes the passed callable method based on the
		 * specified route.
		 * @param $route Specific Route
		 * @param $bundle Necessary Arguments
		 * @param $method Callable variable
		 * @return _proccess
		 */
		public static function put($route,$method){
			if(!in_array($route, self::$routes)){
				self::$current_route =  $route;
				self::$current_method = $method;
				self::$current_type = "put";
				self::$routes[] = $route;
				self::generate_path();
				self::_process($route,new Bundle(),$method);
			}
			return new self();
		}
		/**
		 * An API method that executes the passed callable method based on the
		 * specified route. This method requires API KEY to execute.
		 * @param $route Specific Route
		 * @param $bundle Necessary Arguments
		 * @param $method Callable variable
		 * @return _proccess
		 */
		public static function api($route,$method){
			if(!in_array($route, self::$routes)){
				self::$current_route =  $route;
				self::$current_method = $method;
				self::$current_type = "api";
				self::$routes[] = $route;
				if(self::match_route(filter_var(rtrim($route,"/"), FILTER_SANITIZE_STRING))){
					if(self::_api_auth()){
						self::generate_path();
						self::_process($route,new Bundle(),$method);
					}else{
						echo JSONResponse::make(["error"=>true,"message"=>"Unauthorized access detected"]); 
					}
				}
			}
			return new self();
		}
		/**
		 * API authentication
		 * @return boolean
		 */
		public static function _api_auth(){
			if(!isset($_GET["api_key"])){
				return false;
			}		
			if(Helper::cleanString(trim(filter_var($_GET["api_key"], FILTER_SANITIZE_STRING))) != APIKEY){	
				return false;
			}
			return true;
		}
		
		/**
		 * A method for a named route
		 * @param $name Name of the route
		 */
		public static function name($name){
			self::$routeBundle[$name] = (new Bundle)
			->put("route",self::$current_route)
			->put("method",self::$current_method)
			->put("type",self::$current_type); 
		}
		
		/**
		 * Getting a route by name
		 * @param $name Name of the route
		 * @return Route Method
		 */
		public static function get($name){
			$routeData = self::$routeBundle[$name];
			if($routeData->get("type") == "put"){
				return self::put($routeData->get("route"),$routeData->get("method"));
			} else {
				return self::put($routeData->get("route"),$routeData->get("method"));
			}
		}

		public static function getNamedRoutes(){
			return self::$routeBundle;
		}
		/**
		 * Setting up default 404 Error handler
		 * 
		 */
		public static function error404($method){
			$route_invalid = true;
			$url = Helper::_route_cleanse(self::generate_path());
			foreach(self::getRoutes() as $key => $value){
				$route = Helper::_route_cleanse(explode("/",$value));
				if(implode("",$route) === implode("",$url)){
					$route_invalid = false;
				}
			}
			if($route_invalid){
				if(is_string($method)){
					$method = explode("@", $method);
					if(is_callable($method)){
						echo call_user_func($method,self::$url_variables); exit;
					} else {
						echo View::render('templates/error.html.twig', array('error' => strtoupper($error)." Not a callable method"));
						exit;
					}
				} else {
					echo call_user_func($method,self::$url_variables); exit;
				}
			}
		}
		/**
		 * Route processing
		 *
		 * Validation of the routes and callable methods to
		 * be executed.
		 * @param $route The name of the route
		 * @param $bundle Arguments to be passed in the method
		 * @param $method The method to be executed by the route specified
		 */
		public static function _process($route,$bundle,$method){
			//if($route === filter_var($bundle->get("route"), FILTER_SANITIZE_STRING)){
			self::$url_variables = new Bundle();
			if(self::match_route(filter_var(rtrim($route,"/"), FILTER_SANITIZE_STRING))){
				if(is_string($method)){
					$method = explode("@", $method);
					if(is_callable($method)){
						echo call_user_func($method,self::$url_variables); exit;
					} else {
						echo View::render('templates/error.html.twig', array('error' => strtoupper($error)." Not a callable method")); exit;
						exit;
					}
				} else {
					echo call_user_func($method,self::$url_variables); exit;
				}
			}
		}
		private static function match_route($route){
			$route = Helper::_route_cleanse(explode("/",$route));
			if(sizeof($route) != sizeof(self::$path["call_parts"])){ return false; }
			foreach ($route as $index => $value) {
				//if(ctype_alpha(self::$path["call_parts"][$index])){
				if(!preg_match("/({[a-z]+})/",$value)){
					if($value !== self::$path["call_parts"][$index]){
						return false;
					}
				} else {
					self::$url_variables->put(preg_replace("/[^a-zA-Z]+/", "", $value),self::$path["call_parts"][$index]);
				}

			}
			return true;
		}
		/**
		 * Get the list of routes created
		 * @return Array of routes
		 *
		 */
		public function getRoutes(){
			return Helper::_route_cleanse(self::$routes);
		}
		
		/**
		 * @return $path URL path
		 */
		private static function generate_path(){
			return self::get_full_path()["call_parts"];
		}
		public static function call_parts_to_string(){
			$string = "";
			foreach (self::generate_path()["call_parts"] as $key => $value) {
				$string.=$value;
			}
		}

		public static function get_full_path(){
			self::_route_init();
			return self::$path;
		}

		/**
		 * Initialize URL parsing
		 */
		private static function _route_init(){
			if (isset($_SERVER['REQUEST_URI'])) {
				self::$request_path = explode('?', rtrim($_SERVER['REQUEST_URI'],"/"));
				self::_route_base();
			}
		}

		/**
		 * Generate Base Path
		 */
		private static function _route_base(){
			self::$ROOT = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
			self::$path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
			self::_route_call_utf8();
		}

		/**
		 * Get the call UTF-8 from URL
		 */
		private static function _route_call_utf8(){
			self::$path['call_utf8'] = substr(urldecode(self::$request_path[0]), strlen(self::$path['base']) + 1);
			self::_route_call();
		}

		private static function _route_call(){
			self::$path['call'] = utf8_decode(self::$path['call_utf8']);
	    	if (self::$path['call'] == basename($_SERVER['PHP_SELF'])) {
	    		self::$path['call'] = '';
	   		}
	   		self::_route_call_parts();
		}

		/**
		 * URL division by "/" slashes
		 */
		private static function _route_call_parts(){
			self::$path['call_parts'] = explode('/', self::$path['call']);
			self::_route_query_utf8(self::$request_path[0]);
		}

		private static function _route_query_utf8($request){
			self::$path['query_utf8'] = urldecode(self::$request_path[0]);
			self::$path['query'] = utf8_decode(urldecode((!empty(self::$request_path[1])?self::$request_path[1]:"page")));
			self::_route_query_vars();
		}

		private static function _route_query_vars(){
			$vars = explode('&', self::$path['query']);
			foreach ($vars as $var) {
	      		$t = explode('=', $var);
	      		self::$path['query_vars'][$t[0]] = (!empty($t[1]))? $t[1] : 0;
	    	}
			self::$path["call_parts"] = Helper::_route_cleanse(self::$path["call_parts"]);
		}

		
	}



 ?>