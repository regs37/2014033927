<?php
	require('../import.php');
	header('Content-Type: application/json');
	if (isset($_GET['api_key'])) {
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
	        	if(isset($_GET["user_token"])){
	        		$limit = (isset($_GET["limit"]))? $_GET["limit"]:5;
	        		$page = (isset($_GET["page"]))? $_GET["page"]:5;
                        $offset = (($page-1)*$limit);
	        		$user = Query::table(User::TABLE)->select()->where(User::USER_TOKEN,"=",$_GET["user_token"])->get();
	        		if(sizeof($user) > 0){
	        			$user = $user[0];
	        			$notifs = Query::table(Notification::TABLE)->select()->where(Notification::OWNER_ID,"=",$user["id"])->order(Notification::NOTIF_ID,Query::DESC)->limit($offset,$limit)->get();
                              if(sizeof($notifs) > 0){
                              	$newNotifs = Query::table(Notification::TABLE)->select()->where(Notification::OWNER_ID,"=",$user["id"])->where(Notification::STATUS,"=",0)->order(Notification::NOTIF_ID,Query::DESC)->limit($offset,$limit)->get();
                              	$oldNotifs = Query::table(Notification::TABLE)->select()->where(Notification::OWNER_ID,"=",$user["id"])->where(Notification::STATUS,"=",1)->order(Notification::NOTIF_ID,Query::DESC)->limit($offset,$limit)->get();
                              	$notifs = $C_Notification->retrieve($notifs);
                              	$data = array();
                              	foreach ($notifs as $key => $notif) {
                              		$actor = Query::table(User::TABLE)->select()->where("id","=",$notif->getActorId())->get()[0];
                                          $post = Query::table(Post::TABLE)->select()->where(Post::POST_ID,"=",$notif->getPostId())->get()[0];
                                          $attachments = Query::table("user_post_attach")->select()->where("post_id","=",$notif->getPostId())->get(); 

                                          $action = "";
                                          if(sizeof($attachments) == 1){
                                                switch($attachments[0]["attach_type"]){
                                                      case "image":
                                                            $action = " photo";
                                                            break;
                                                      case "video":
                                                            $action = " video";
                                                            break;
                                                      default:
                                                            $action = " post";
                                                            break;
                                                }
                                          } else {
                                               $action = " post"; 
                                          }

                                          $action = (($notif->getType()=="like")?"liked":"commented on")." your".$action;
                                          
                              		$data[] = [
                              			"actor"=>[
                              				"name"=>ucwords(strtolower($actor["name"])),
                              				"picture"=>$actor["picture"],
                              				"user_token"=>$actor["user_token"]
                              			],
                                                "actor_action"=>$action,
                              			"post_id"=>$notif->getPostId(),
                              			"type"=>$notif->getType(),
                              			"date"=>$HELPER->time_ago($notif->getDate()),
                              			"status"=>(($notif->getStatus() == 1)?"old":"new")
                              		];
                              	}
                              	echo json_encode(["error"=>false,"message"=>"Successfully retrieved notifications","has_notif"=>true,"notifications"=>$data,"old_notif"=>(sizeof($oldNotifs)),"new_notif"=>(sizeof($newNotifs))]);
                              } else { echo json_encode(["error"=>false,"message"=>"No notifications","has_notif"=>false,"notifications"=>null,"old_notif"=>null,"new_notif"=>false]); }
	        		} else { echo json_encode(["error"=>true,"message"=>"Invalid user token"]); }
	        	} else { echo json_encode(["error"=>true,"message"=>"Invalid user token"]); }
	        } else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
		} else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
    } else { echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]); }
