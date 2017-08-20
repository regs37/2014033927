<?php 
	require('../import.php');
	header('Content-Type: application/json');
	if($_SERVER['REQUEST_METHOD']=='POST'){
		if(!$HELPER->isEmpty(isset($_POST["email"]))){
			if(!$HELPER->isEmpty(isset($_POST["name"]))){
				if($HELPER->isEmail($_POST["email"])){
					$newUser = new User();
					$newUser->setEmail($_POST['email'])
					->setName($HELPER->cleanString($_POST['name']));
					if(!$C_User->isExist("email",$newUser->getEmail())){
						$C_User->insert($newUser);
						$userData = $C_User->where("email",$newUser->getEmail())->get();
						echo json_encode(["error"=>false,"user_status"=>"new","data"=>$userData[0],"message"=>"successfully created a new user"]);
					} else { 
						$userData = $C_User->where("email",$newUser->getEmail())->get();
						echo json_encode(["error"=>false,"user_status"=>"old","data"=>$userData[0],"message"=>"already registered"]);
					}
				} else { echo json_encode(["error"=>true,"message"=>"invalid email"]); }
			} else { echo json_encode(["error"=>true,"message"=>"please enter name"]); }
		} else { echo json_encode(["error"=>true,"message"=>"please enter email"]); }
	} else { echo json_encode(["error"=>true,"message"=>"invalid request"]); }


 ?>