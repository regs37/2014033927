<?php 
	require('../import.php');
	header('Content-Type: application/json');
	if (isset($_GET['api_key'])) {
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
	        	if(isset($_GET["user_token"])){
	        		$user = Query::table(User::TABLE)->select()->where(User::USER_TOKEN,"=",$_GET["user_token"])->get()[0];
	        		if(Query::table(Notification::TABLE)->update()->set(Notification::STATUS,1)->where(Notification::OWNER_ID,"=",$user["id"])->where(Notification::STATUS,"=",0)->apply()){
	        			echo json_encode(["error"=>false,"message"=>"Successfully updated new notifications"]); 
	        		} else {
	        			echo json_encode(["error"=>true,"message"=>"failed updating new notifications"]); 
	        		}
	        	}
	        } else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
		} else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
    } else { echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]); }