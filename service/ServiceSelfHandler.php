<?php 
	if(isset($_POST["btn_delete_sport"])){
		if(isset($_POST["sport_id"])){
			$sportSelected = new Sport();
			$sportSelected->setId($_POST["sport_id"]);
			$response = $C_Sports->delete($sportSelected);

		}
	}
	if(isset($_POST["btn_add_sport"])){
		$newSport = new Sport();
		if(isset($_POST['sport_name'])){
			if($_POST['sport_name'] !== ''){
				$newSport->setName($_POST['sport_name']);
				if(!$C_Sports->isExist("name",$newSport->getName())){	
					$RESPONSE = $C_Sports->insert($newSport);
				} else {
					$RESPONSE = ["error"=>true,"message"=>"Sport is aready available"];
				}
			} else {
				$RESPONSE = ["error"=>true,"message"=>"Please enter sport name"];
			}
		}
	}
	/**
	 * Update Sport Preference
	 */
	if(isset($_POST["btn_sports_pref"])){
		if(isset($_POST["sports"])){
			if(isset($_POST['userid'])){
				$userid = $_POST['userid'];
				$C_SportsPref->delete($userid);
				foreach($_POST["sports"] as $sport){
					if(!$C_SportsPref->isExist($userid,$sport)){
						$RESPONSE = $C_SportsPref->insert($userid,$sport);
					}
				}
			}
		}
	}

	// if(isset($_POST["btn_follow"])){
	// 	if(isset($_POST['id'])){
	// 		if(isset($_POST['follower_id'])){
 //                $follower = $_POST['follower_id'];
 //                if(!$C_Follow->isExist($_POST['id'],$follower)){
 //                	$RESPONSE = $C_Follow->insert($_POST['id'],$follower);

            		/**
                	 * Updating the user's [followers]
                	 * @var $followingUser = the user data of the person to be followed
                	 */
//                	$C_User->incFollower($follower);

                	/**
                	 * Updating the follower's [following] column
                	 * @var $followerUser = the follower's user data
                	 */
 //                	$C_User->incFollowing($_POST['id']);

 //            	} else {
 //            		$RESPONSE= ["error"=>true,"message"=>"Already followed this user"];
 //            	}

 //            } else {
	// 			$RESPONSE= ["error"=>true,"message"=>"Please provide the ID of the follower"];
	// 		}
	// 	} else {
	// 		$RESPONSE=["error"=>true,"message"=>"Please provide the ID of the user to be followed"];
	// 	}
	// }

	// if(isset($_POST["btn_unfollow"])){
	// 	if(isset($_POST['id'])){
	// 		if(isset($_POST['follower_id'])){
 //                $follower = $_POST['follower_id'];
 //                if($C_Follow->isExist($_POST['id'],$follower)){
                    /**
                	 * Updating the user's [followers]
                	 * @var $followingUser = the user data of the person to be followed
                	 */
//                	$C_User->decFollower($_POST['id']);

                	/**
                	 * Updating the follower's [following] column
                	 * @var $followerUser = the follower's user data
                	 */
//                	$C_User->decFollowing($follower);

 //                    $RESPONSE = $C_Follow->delete($_POST['id'],$follower);
 //                } else {
 //            		$RESPONSE =["error"=>true,"message"=>"Already unfollowed this user"];
 //            	}
 //            }  else {
 //        		$RESPONSE =["error"=>true,"message"=>"Please provide the ID of the follower"];
 //        	}
	// 	} else {
	// 		$RESPONSE =["error"=>true,"message"=>"Please provide the ID of the user to be unfollowed"];
	// 	}
	// }
	if(isset($_POST["btn_register"])){
		if(!$HELPER->isEmpty($_POST["email"])){
			if(!$HELPER->isEmpty(isset($_POST["name"]))){
				if(!$HELPER->isEmpty(isset($_POST["gender"]))){
					if(!$HELPER->isEmpty(isset($_POST["date"]))){
						if(!$HELPER->isEmpty(isset($_POST["picture"]))){
							if(!$HELPER->isEmpty(isset($_POST["height"]))){
								if(!$HELPER->isEmpty(isset($_POST["provider"]))){
									if($HELPER->isEmail($_POST["email"])){
										if($HELPER->isAlpha($_POST["name"])){
											$newUser = new User();
											$newUser->setEmail($_POST['email'])
											->setName($_POST['name'])
											->setGender($_POST['gender'])
											->setBirthDate($_POST["date"])
											->setPicture($_POST["picture"])
											->setHeight($_POST["height"])
											->setProvider($_POST["provider"])
											->setDateCreated($HELPER->date())
											->setDateSignIn($HELPER->date())
											->setUserToken(md5($_POST["email"]));
											if(!$C_User->isExist("email",$newUser->getEmail())){
					    						$C_User->insert($newUser);
					    						$RESPONSE = ["error"=>false,"message"=>"Successfully created new user"];
					    					} else { $RESPONSE = ["error"=>true,"message"=>"Already registered"]; }
										} else { $RESPONSE = ["error"=>true,"message"=>"invalid name"]; }
									} else { $RESPONSE = ["error"=>true,"message"=>"invalid email"]; }
								} else { $RESPONSE = ["error"=>true,"message"=>"please enter provider"]; }
							} else { $RESPONSE = ["error"=>true,"message"=>"please enter height"]; }
						} else { $RESPONSE = ["error"=>true,"message"=>"please enter picture"]; }
					} else { $RESPONSE = ["error"=>true,"message"=>"please enter birthdate"]; }
				} else { $RESPONSE = ["error"=>true,"message"=>"please enter gender"]; }
			} else { $RESPONSE = ["error"=>true,"message"=>"please enter name"]; }
		} else { $RESPONSE = ["error"=>true,"message"=>"please enter email"]; }
	}

		function insertNewMatch($newMatch,$user,$message){
			if($GLOBALS['C_Match']->insert($newMatch)){
				$matches = $GLOBALS['C_Match']->select()->where("gender",$newMatch->getGender())->where("locality",$newMatch->getLocality())->where("sport_id",$newMatch->getSportId())->where("status",0)->whereCondition("age",$newMatch->getAge()+3,"<")->whereCondition("age",$newMatch->getAge()-3,">")->order("match_id","DESC")->get();
				$match = $matches[0];
				$newMatchPlayer = new MatchPlayer();
				$playerCount = $GLOBALS['C_MatchPlayer']->getPlayerCount($match["match_id"]);
				$team = 1;
				if((6/2) > $playerCount){
					$team = 0;
				}
				$GLOBALS['C_MatchPlayer']->insert($newMatchPlayer
					->setMatchId($match["match_id"])
					->setPlayerId($user)
					->setTeam($team)
					->setType("leader"));
				$playerCount = $GLOBALS['C_MatchPlayer']->getPlayerCount($match["match_id"]);
	    		$matchReady = false;
	    		if($playerCount == 5){
					$matchReady = true;
				}
		    	$GLOBALS['RESPONSE'] = [
		    	"error"			=>false,
				"message"		=>$message,
				"ingame"		=>true,
				"match_ready"	=>$matchReady,
				"player_count"	=>$playerCount,
				"data"			=>$match];
		    } else {
		    	$GLOBALS['RESPONSE'] = [
		    	"error"			=>true,
				"message"		=>"Failed to create a new game room.",
				"ingame"		=>true,
				"match_ready"	=>false,
				"player_count"	=>0,
				"data"			=>null];
			}
		}

	//if(isset($_POST["btn_insert_match"])){
	if(isset($_POST["temp"])){
		$locality = $_POST["locality"];
		$sport = $_POST["sport_id"];
		$long = $_POST["long"];
		$lat = $_POST["lat"];
		$gender = $_POST["gender"];
		$age = $_POST["age"];
		$user = $_POST["user_token"];
		$helper = new Helper();
		$match = new Match();
		$user = $C_User->select()->where("user_token",$HELPER->cleanString($user))->get()[0]["id"];
		$isInGame = $C_Match->isPlayerInGame($user,$gender);
		if($isInGame["result"] == true){
			$playerCount = $C_MatchPlayer->getPlayerCount($isInGame["match_details"]["match_id"]);
			$matchReady = false;
			if($playerCount == 6){
				$matchReady = true;
			}
			$RESPONSE = ["error"=>false,
			"message"		=>"You have a pending game",
			"ingame"		=>true,
			"match_ready"	=>$matchReady,
			"player_count"	=>$playerCount,
			"data"			=>$isInGame["match_details"]
			];
		} else {
			$matches = $C_Match->select()->where("gender",$gender)->where("locality",$locality)->where("sport_id",$sport)->where("status",0)->whereCondition("age",$age+3,"<=")->whereCondition("age",$age-3,">=")->order("match_id","DESC")->get();
			if(sizeof($matches) > 0){
				$match = $matches[0];
				$distance = $helper->distance($lat, $long, $match["location_lat"], $match["location_long"], "M");
				$playerCount = $C_MatchPlayer->getPlayerCount($match["match_id"]);
				$matchReady = false;
				if($playerCount == 6){
					$matchReady = true;
				}
				$distance = $helper->distance($lat, $long, $match["location_lat"], $match["location_long"], "M");
				if($distance < 500){
					if($playerCount < 6){
						//$RESPONSE =["error"=>false,"message"=>"LIST OF MATCHES AVAILABLE","data"=>$match,"ingame"=>false];
						$RESPONSE = ["error"=>false,
						"message"		=>"Congrats! There is a room available for you.",
						"ingame"		=>true,
						"match_ready"	=>$matchReady,
						"player_count"	=>$playerCount,
						"data"			=>$match];
						$team = 1;
						if((6/2) > $playerCount){
							$team = 0;
						}
						$newMatchPlayer = new MatchPlayer();
						$C_MatchPlayer->insert($newMatchPlayer
							->setMatchId($match["match_id"])
							->setPlayerId($user)
							->setTeam($team)
							->setType("member"));

					} else {
						//$C_Match->update()->set("status",1)->where("match_id",$match["match_id"])->apply();
						$newMatch = new Match();
						insertNewMatch($newMatch
						->setLocality($locality)
				    	->setLocationLong($long)
				    	->setLocationLat($lat)
				    	->setGender($gender)
				    	->setAge($age)
				    	->setSportId($sport)
				    	->setStatus(0),$user,"Created your own match room. The available match rooms are already full.");
					}
				} else {
					$newMatch = new Match();
					insertNewMatch($newMatch
					->setLocality($locality)
			    	->setLocationLong($long)
			    	->setLocationLat($lat)
			    	->setGender($gender)
			    	->setAge($age)
			    	->setSportId($sport)
			    	->setStatus(0),$user, "Created your own match room. No match rooms available near your location.");
				}
			} else {
				$newMatch = new Match();
				insertNewMatch($newMatch
				->setLocality($locality)
		    	->setLocationLong($long)
		    	->setLocationLat($lat)
		    	->setGender($gender)
		    	->setAge($age)
		    	->setSportId($sport)
		    	->setStatus(0),$user,"Created a new match room since there is no match room available.");
			}
		}
	}


	if(isset($_POST["btn_post_own"])){
		if(isset($_POST["user_token"])){
			if(isset($_POST["caption"])){
				$post = new Post();
				$user = $C_User->select()->where("user_token",$_POST["user_token"])->get()[0];
				$post->setUserId($user["id"])
				->setCaption($_POST["caption"])
				->setDatePosted($HELPER->date())
				->setDateModified($HELPER->date());
				$RESPONSE = $C_Post->insert($post);
				if(isset($_POST["attachment_type"])){
					if(isset($_POST["attachment_url"])){
						$C_PostAttachment->insert($RESPONSE["post_id"],$_POST["attachment_type"],$_POST["attachment_url"]);
					}
				}
			} else { $RESPONSE = ["error"=>true,"message"=>"What's in your mind?"]; }
		} else { $RESPONSE = ["error"=>true,"message"=>"User token not found"]; }
	}

	if(isset($_POST["btn_like"])){
		if(isset($_POST["post_id"])){
			if(isset($_POST["user_id"])){
				$C_PostLike->insert($_POST["post_id"],$_POST["user_id"]);
			}
		}
	}
	if(isset($_POST["btn_set_current_user"])){
		$user = $_POST["current_user"];
		$tab = $_POST["tab"];
		HEADER("?tab=".$tab."&current_user=".$user);
	}
	function getCarouselPicture($images){
		?>
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
		  <ol class="carousel-indicators">
		  	<?php for($x = 0; $x < sizeof($images); $x++){
		  		if($images[$x]["attach_type"] == "image"){
			  	 	$class=($x==0)?"active":""; 

			  	 	?><li data-target="#myCarousel" data-slide-to="<?php echo $x ?>" class="<?php echo $class; ?>"></li><?php 

				}
		    	} 
		    ?>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner">
		  	<?php foreach($images as $key=>$image){ 
		  		if($image["attach_type"] =="image"){
		  		$class=($key==0)?"active":"";
		  		?>
				    <div class="item <?php echo $class; ?>">
				      <img src="<?php echo $image["attach_url"]; ?>" alt="<?php echo $image["attach_type"]; ?>">
				    </div>
		    <?php }
		    } ?>
		  </div>
		  <!-- Left and right controls -->
		  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left"></span>
		    <span class="sr-only">Previous</span>
		  </a>
		  <a class="right carousel-control" href="#myCarousel" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right"></span>
		    <span class="sr-only">Next</span>
		  </a>
		</div>
		<?php
	}

 ?>