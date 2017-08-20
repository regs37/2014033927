<?php 
	namespace Core;
	
	class Helper{
		
		public function splitRoute($string){
			return  $string.split("/");
		}

		public static function authAPI($method){
			if(!isset($_GET["api_key"])){
				echo JSONResponse::make(["error"=>true,"message"=>"Unauthorized access detected"]); 
				exit;
			}		
			if(self::cleanString(trim($_GET["api_key"])) != APIKEY){	
				echo JSONResponse::make(["error"=>true,"message"=>"Unauthorized access detected"]); 
				exit;
			}
			if(is_callable($method)){
				call_user_func($method);
			}
		}
		public static function _route_cleanse($array){
			$newCallParts = array();
			foreach ($array as $key => $value) {
				if(!empty(trim($value))){
					$newCallParts[] = $value;
				}
			}
			return $newCallParts;
		}
		public static function clean($string){
			return self::cleanString($string);
		}

		// public function clean($string){
		// 	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  //  			$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  //  			return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
		// }
		
		public function cleanString($string){
			$array = ["!","@","#","$","%","^","&","*","(",")","_","-","=","+","<",">",",","{","}","[","]","/","\\",":",";","?","`","'",'"',"."];
			foreach($array as $char){
				$string = str_replace($char, "", $string);
			}
			return htmlspecialchars($string);
		}

		public static function date(){
			return date("Y-m-d H:i:s");
		}

		public static function time_ago( $time ){
			
			$time = strtotime($time);
	        $out    = ''; // what we will print out
	        $now    = time(); // current time
	        $diff   = $now - $time; // difference between the current and the provided dates

	        if( $diff < 60 ) // it happened now
	            return TIMEBEFORE_NOW;

	        elseif( $diff < 3600 ) // it happened X minutes ago
	            return str_replace( '{num}', ( $out = round( $diff / 60 ) ), $out == 1 ? TIMEBEFORE_MINUTE : TIMEBEFORE_MINUTES );

	        elseif( $diff < 3600 * 24 ) // it happened X hours ago
	            return str_replace( '{num}', ( $out = round( $diff / 3600 ) ), $out == 1 ? TIMEBEFORE_HOUR : TIMEBEFORE_HOURS );

	        elseif( $diff < 3600 * 24 * 2 ) // it happened yesterday
	            return TIMEBEFORE_YESTERDAY;

	        else // falling back on a usual date format as it happened later than yesterday
	            return strftime( date( 'Y', $time ) == date( 'Y' ) ? TIMEBEFORE_FORMAT : TIMEBEFORE_FORMAT_YEAR, $time );
    	}
		/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
		/*::                                                                         :*/
		/*::  This routine calculates the distance between two points (given the     :*/
		/*::  latitude/longitude of those points). It is being used to calculate     :*/
		/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
		/*::                                                                         :*/
		/*::  Definitions:                                                           :*/
		/*::    South latitudes are negative, east longitudes are positive           :*/
		/*::                                                                         :*/
		/*::  Passed to function:                                                    :*/
		/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
		/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
		/*::    unit = the unit you desire for results                               :*/
		/*::           where: 'M' is statute miles (default)                         :*/
		/*::                  'K' is kilometers                                      :*/
		/*::                  'N' is nautical miles                                  :*/
		/*::  Worldwide cities and other features databases with latitude longitude  :*/
		/*::  are available at http://www.geodatasource.com                          :*/
		/*::                                                                         :*/
		/*::  For enquiries, please contact sales@geodatasource.com                  :*/
		/*::                                                                         :*/
		/*::  Official Web site: http://www.geodatasource.com                        :*/
		/*::                                                                         :*/
		/*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
		/*::                                                                         :*/
		/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
		public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			$unit = strtoupper($unit);
		  	if ($unit == "K") {
		    	return ($miles * 1.609344);
		  	} else if ($unit == "N") {
		      	return ($miles * 0.8684);
		    } else {
		        return $miles;
		    }
		}

	}
 ?>