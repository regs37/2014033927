<?php 
	namespace Core;
	
	class Bundle{
		private $args = array();
		private $keys = array();
		
		public function __construct(){}

		public function put($key,$value){
			$this->args[$key] = $value;
			if(!in_array($key, $this->keys)){
				$this->keys[] = $key;
			}
			return $this;
		}

		public function get($key){
			if(!in_array($key, $this->keys)){
				return null;
			}
			if($this->args[$key] == null){
				return null;
			}
			return $this->args[$key];
		}
	}
	

?>