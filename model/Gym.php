<?php
	namespace Model;
	use Core\Query;
	class Gym extends Query{
		public const TABLE = "gym";
		public const GYM_ID = "gym_id";
		public const NAME = "name";
		public const EMAIL = "email";
		public const PASSWORD = "password";
		public const ADDRESS = "address";
		public const LONGITUDE = "longitude";
		public const LATITUDE = "latitude";
		public const ABOUT = "about";
		public const CONTACT = "contact";
		public const LOGO = "logo";
		public const TOKEN = "token";

		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setGymId($gymid){
			$this->GYM_ID = $gymid;
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
		public function setPassword($password){
			$this->PASSWORD = $password;
			return $this;
		}
		public function setAddress($address){
			$this->ADDRESS = $address;
			return $this;
		}
		public function setLongitude($longitude){
			$this->LONGITUDE = $longitude;
			return $this;
		}
		public function setLatitude($latitude){
			$this->LATITUDE = $latitude;
			return $this;
		}
		public function setAbout($about){
			$this->ABOUT = $about;
			return $this;
		}
		public function setContact($contact){
			$this->CONTACT = $contact;
			return $this;
		}
		public function setLogo($logo){
			$this->LOGO = $logo;
			return $this;
		}
		public function setToken($token){
			$this->TOKEN = $token;
			return $this;
		}

		public function getGymId(){
			return $this->GYM_ID;
		}
		public function getName(){
			return $this->NAME;
		}
		public function getEmail(){
			return $this->EMAIL;
		}
		public function getPassword(){
			return $this->PASSWORD;
		}
		public function getAddress(){
			return $this->ADDRESS;
		}
		public function getLogitude(){
			return $this->LONGITUDE;
		}
		public function getLatitude(){
			return $this->LATITUDE;
		}
		public function getAbout(){
			return $this->ABOUT;
		}
		public function getContact(){
			return $this->CONTACT;
		}
		public function getLogo(){
			return $this->LOGO;
		}
		public function getToken(){
			return $this->TOKEN;
		}

	}