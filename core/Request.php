<?php 
	namespace Core;

	class Request{
		
		public const POST = "post";
		
		public const GET = "get";
		
		private static $errors = array();

		private static $values = array();	

		private static $rules = array("url","require","notempty","alpha","num","alphaNum","optional","email");

		public static function make($rules,$method){
			$method = self::getMethod($method);
			foreach ($rules as $key => $defindedRules) {
				if(isset($method[$key])){
					self::validate($key,$defindedRules,$method[$key]);
				} else {
					if(!in_array("optional",explode("|",$defindedRules))){
						self::$errors[] = strtoupper($key)." was not received.";
					}
				}
			}
			return new self();
		}
		public function get($key){
			return self::$values[$key]; 
		}
		public function replace($key,$value){
			self::$values[$key] = $value;
		}
		public function hasError(){
			return (sizeof(self::$errors) > 0);
		}
		public function getError(){
			return ["error"=>true,"stack_trace"=>self::$errors];
		}
		private function getMethod($method){
			if($method == self::POST){
				if(isset($_POST)){
					return $_POST;
				} else {
					self::$errors[] = "No values received from ".$method.".";
				}
			} else {
				if(isset($_GET)){
					return $_GET;
				} else {
					self::$errors[] = "No values received from ".$method.".";
				}
			}
			
		}
		private function validate($ParentKey,$defindedRules,$valueReceived){
			$userDefindeRules = explode("|",$defindedRules);
			foreach ($userDefindeRules as $key => $rule) {
				if(in_array($rule, self::$rules)){
					$rule = trim($rule);
					if($rule == "require"){
						if(empty(trim($valueReceived))){
							self::$errors[] = "Required ".strtoupper($ParentKey)." is empty.";
						}
					}
					if($rule == "alpha"){
						if (!preg_match("/^[a-zA-Z ]*$/",$valueReceived)) {
							self::$errors[] = strtoupper($ParentKey)." must be all letters.";
						}
					}
					if($rule == "num"){
						if(!ctype_digit($valueReceived)){
							self::$errors[] = strtoupper($ParentKey)." must be a numeric value.";
						}
					}
					if($rule == "alphaNum"){
						if(!ctype_alnum($valueReceived)){
							self::$errors[] = strtoupper($ParentKey)." must be an alphanumeric value.";
						}
					}
					if($rule == "email"){
						if (!filter_var($valueReceived, FILTER_VALIDATE_EMAIL)) {
							self::$errors[] = strtoupper($ParentKey)." Invalid email.";
						}
					}
					if($rule == "url"){
						if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$valueReceived)) {
							self::$errors[] = strtoupper($ParentKey)." Invalid email.";
						}
					}
				} else {
					self::$errors[] = strtoupper($rule)." in ".strtoupper($ParentKey)." is an invalid rule.";
				}
			}
			self::$values[$ParentKey] = filter_var($valueReceived, FILTER_SANITIZE_STRING);
		}

	}

 ?>