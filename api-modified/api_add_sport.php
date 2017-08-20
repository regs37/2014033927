<?php 
	require('../import.php');

	if (isset($_GET['api_key'])) {
		$newSport = new Sport();
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
    			if(isset($_POST['sport_name'])){
    				if(trim($_POST['sport_name']) !== ''){
    					if(isset($_POST["player"])){
    						if(trim($_POST['player']) !== ''){
			    				$newSport->setName($_POST['sport_name']);
			    				$newSport->setSport($_POST['player']);
			    				if(!$C_Sports->isExist("name",$newSport->getName())){	
			    					echo json_encode($C_Sports->insert($newSport));
			    				} else {
			    					echo json_encode(["error"=>true,"message"=>"Sport name ".$newSport->getName()." is already available"]);
			    				}
			    			}
    					}
    				} else {
    					echo json_encode(["error"=>true,"message"=>"Please enter sports name"]);
    				}
    			}
	        } else {
				echo json_encode(["error"=>true,"message"=>"Invalid API Key"]);
			}
		} else {
			echo json_encode(["error"=>true,"message"=>"Invalid API Key"]);
		}
    } else {
    	echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]);
    }



 ?>