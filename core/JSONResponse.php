<?php
	namespace Core;

	class JSONResponse{
		public function __construct(){

		}
		public static function make($array){
			header("Content-Type: application/json; charset=UTF-8");
			return json_encode($array);
		}
	}
