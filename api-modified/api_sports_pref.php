<?php
	require('../import.php');
	header('Content-Type: application/json');
	if (isset($_GET['api_key'])) {
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
	        	if(isset($_POST["user_token"])){
	        		if(isset($_POST["sports_pref"])){
	        			$sports = Query::table(Sport::TABLE)->select()->get();
	        			$user = Query::table(User::TABLE)->select()->where("user_token","=",$_POST["user_token"])->get();
	        			if(sizeof($user) > 0){
	        				$C_SportsPref->delete($user[0]["id"]);
	        			}
	        			//$sportsPref = json_decode($_POST["sports_pref"]);
	        			$sportsPref = $_POST["sports_pref"];
	        			$sportsPref = trim($sportsPref);
	        			$sportsPref = explode(",",$sportsPref);
	        			//echo json_encode($sportsPref);
	        			//var_dump($_POST["sports_pref"]);
	        			//echo $sportsPref;
	        			$error = array();
	        			$sportsReceived = array();
	        			if(sizeof($sportsPref) > 0){
	        				foreach ($sportsPref as $key => $value) {
	        					$value = trim($value);
	        					$sportsReceived[] = $value;
	        					$result = Query::table(Sport::TABLE)->select()->where("name","=",ucwords(strtolower($value)))->get();
	        					if(sizeof($result) > 0){
	        						$C_SportsPref->insert($user[0]["id"],$result[0]["sport_id"]);
	        					} else {
	        						$error[] = $value." not sent";
	        					}
	        				}
	        				echo json_encode(["error"=>false,"message"=>"Successfully added sports preference","error_list"=>$error,"sports_received"=>$sportsReceived]);
	        			} else { echo json_encode(["error"=>true,"message"=>"Please select atleast 1 sport"]); }
	        		} else { echo json_encode(["error"=>true,"message"=>"No sports preference received"]); }
	        	} else { echo json_encode(["error"=>true,"message"=>"Please provide user token"]); }
	        } else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
		} else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
    } else { echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]); }



