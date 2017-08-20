<?php 
	require('../import.php');
	header('Content-Type: application/json');
	if (isset($_GET['api_key'])) {
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
				if(isset($_POST["user_token"])){
					if(isset($_POST["caption"])){
						$post = new Post();
						$user = Query::table(User::TABLE)->select()->where("user_token","=",$_POST["user_token"])->get()[0];
						$post->setUserId($user["id"])
						->setCaption(addslashes($_POST["caption"]))
						->setDatePosted($HELPER->date())
						->setDateModified($HELPER->date());
						$RESPONSE = $C_Post->insert($post);

						$attachments = array();
						if(isset($_POST["attachment"])){
							if(sizeof($_POST["attachment"])){
								$jsonData = json_decode($_POST["attachment"]);
								foreach($jsonData as $jsonItem){
									$C_PostAttachment->insert($RESPONSE["post_id"],$jsonItem->type,$jsonItem->url);
									
								}
							}
						}
						echo json_encode(["error"=>false,"message"=>"Successfully added post"]); exit;
						// if(isset($_POST["attachment_url"])){
						// 	for($x = 0; $x < sizeof($_POST["attachment_url"]); $x++){
						// 		$attachments[] = [$_POST["attachment_url"][$x],$_POST["attachment_type"][$x]];
						// 	}
						// }
						//echo json_encode(["error"=>false,"message"=>"successfully created an new post"]);
					} else { echo json_encode(["error"=>true,"message"=>"What's in your mind?"]); }
				} else { echo json_encode(["error"=>true,"message"=>"User token not found"]); }
	        } else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
		} else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
    } else { echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]); }


	// {
	// 	"user_token":"", //user_token not user_id
	// 	"caption":"", //caption of the post
	// 	"attachment":[
	// 		{
	// 			"type":"" //type [image/video]
	// 			"url":""
	// 		},
	// 		{
	// 			"type":"" //type [image/video]
	// 			"url":""
	// 		},
	// 	]
	// }