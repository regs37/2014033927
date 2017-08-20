<?php 	
	namespace Model;
	use Core\Query;
	class User extends Query {
		public const TABLE = "user";
		public const ID = "id";
		public const NAME = "name";
		public const EMAIL = "email";
		public const GENDER = "gender";
		public const BIRTHDATE = "birthdate";
		public const HEIGHT = "height";
		public const PROVIDER = "provider";
		public const PROVIDER_ID = "provider_id";
		public const USER_TOKEN = "user_token";
		public const ACCESS_TOKEN = "access_token";
		public const FOLLOWERS = "followers";
		public const FOLLOWING = "following";
		public const TOTAL_MATCHES = "total_matches";
		public const DATE_CREATED = "date_created";
		public const DATE_SIGNIN = "date_signin";
		public const PICTURE = "picture";
		public const MVP_RATING = "mvp_rating";
		public const IS_ONLINE = "is_online";
		
		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		/**
		 * Model Setters
		 */
		public function setId($id){
			$this->ID = $id;
			return $this;
		}
		public function setName($name){
			$this->NAME = $name;
			return $this;
		}
		public function setEmail($email){
			$this->EMAIL = $email;
			return $this;
		}
		public function setGender($gender){
			$this->GENDER = $gender;
			return $this;
		}
		public function setBirthDate($birthdate){
			$this->BIRTHDATE = $birthdate;
			return $this;
		}
		public function setHeight($height){
			$this->HEIGHT = $height;
			return $this;
		}
		public function setProvider($provider){
			$this->PROVIDER = $provider;
			return $this;
		}
		public function setProviderId($providerId){
			$this->PROVIDER_ID = $providerId;
			return $this;
		}
		public function setUserToken($token){
			$this->USER_TOKEN = $token;
			return $this;
		}
		public function setAccessToken($token){
			$this->ACCESS_TOKEN = $token;
			return $this;
		}
		public function setFollowers($followers){
			$this->FOLLOWERS = $followers;
			return $this;
		}
		public function setFollowing($following){
			$this->FOLLOWING = $following;
			return $this;
		}
		public function setTotalMatches($total_matches){
			$this->TOTAL_MATCHES = $total_matches;
			return $this;
		}
		public function setDateCreated($dateCreated){
			$this->DATE_CREATED = $dateCreated;
			return $this;
		}
		public function setDateSignIn($datesignin){
			$this->DATE_SIGNIN = $datesignin;
			return $this;
		}
		public function setPicture($picture){
			$this->PICTURE = $picture;
			return $this;
		}
		public function setMvpRating($mvp_rating){
			$this->MVP_RATING = $mvp_rating;
			return $this;
		}
		public function setIsOnline($online){
			$this->IS_ONLINE = $online;
			return $this;
		}


		/**
		 * Model Getters
		 */
		public function getId(){
			return $this->ID;
		}
		public function getName(){
			return $this->NAME;
		}
		public function getEmail(){
			return $this->EMAIL;
		}
		public function getGender(){
			return $this->GENDER;
		}
		public function getBirthDate(){
			return $this->BIRTHDATE;
		}
		public function getHeight(){
			return $this->HEIGHT;
		}
		public function getProvider(){
			return $this->PROVIDER;
		}
		public function getProviderId(){
			return $this->PROVIDER_ID;
		}
		public function getUserToken(){
			return $this->USER_TOKEN;
		}
		public function getAccessToken(){
			return $this->ACCESS_TOKEN;
		}
		public function getFollower(){
			return $this->FOLLOWERS;
		}
		public function getFollowing(){
			return $this->FOLLOWING;
		}
		public function getTotalMatches(){
			return $this->TOTAL_MATCHES;
		}
		public function getDateCreated(){
			return $this->DATE_CREATED;
		}
		public function getDateSignIn(){
			return $this->DATE_SIGNIN;
		}
		public function getPicture(){
			return $this->PICTURE;
		}
		public function getMvpRating(){
			return $this->MVP_RATING;
		}
		public function getIsOnline(){
			return $this->IS_ONLINE;
		}


	}

?>