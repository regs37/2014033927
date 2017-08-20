<?php 
	require('../import.php');
	header('Content-Type: application/json');
	$RESPONSE = ["error"=>false,"message"=>""];
	function insertMatch($newMatch,$user,$message){
		if($GLOBALS['C_Match']->insert($newMatch)){

			$matches = Query::table(Match::TABLE)->select()
			->where(Match::GENDER,"=",$newMatch->getGender())
			->where(Match::LOCALITY,"=",$newMatch->getLocality())
			->where(Match::SPORT_ID,"=",$newMatch->getSportId())
			->where(Match::STATUS,"=",0)
			->where(Match::AGE,"<=",$newMatch->getAge()+3)
			->where(Match::AGE,">=",$newMatch->getAge()-3)
			->order(Match::ID,Query::DESC)->get();

			// $matches = $GLOBALS['C_Match']->select()
			// ->where("gender",$newMatch->getGender())
			// ->where("locality",$newMatch->getLocality())
			// ->where("sport_id",$newMatch->getSportId())
			// ->where("status",0)
			// ->whereCondition("age",$newMatch->getAge()+3,"<")
			// ->whereCondition("age",$newMatch->getAge()-3,">")
			// ->order("match_id","DESC")->get();

			$match = $matches[0];
			// get the sport info
			$sport = Query::table(Sport::TABLE)->select()->where(Sport::ID,"=",$match["sport_id"] 	)->get()[0];
    		
			// get the current player count of the match room
			$playerCount = $GLOBALS['C_MatchPlayer']->getPlayerCount($match["match_id"]);
			// assigning the player to a team
			$team = (($sport["player"]/2) > $playerCount)?0:1;
			// insert the player to the match room that just created
			$GLOBALS['C_MatchPlayer']->insert((new MatchPlayer)->setMatchId($match["match_id"])->setPlayerId($user)->setTeam($team)->setType("leader"));
			// get the updated player count of the match room
			$playerCount = $GLOBALS['C_MatchPlayer']->getPlayerCount($match["match_id"]);
			// determine if the game is ready
			$matchReady = ($playerCount == $sport["player"])?true:false;
			// create a response
	    	return json_encode(["error"=>false,"message"=>$message,"ingame"=>true,"match_ready"=>$matchReady,"player_count"=>$playerCount,"data"=>$match]);
	    }
    	return json_encode(["error"=>true,"message"=>"Failed to create a new game room.","ingame"=>true,"match_ready"=>false,"player_count"=>0,"data"=>null]);
	}

	if (isset($_GET['api_key'])) {
		if($HELPER->validateString($_GET['api_key'])){
	        if(strcmp($_GET['api_key'],APIKEY)==0){
				if($HELPER->isEmpty(isset($_POST["locality"]))){
					echo json_encode(["error"=>true,"message"=>"please provide locality"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["sport_id"]))){
					echo json_encode(["error"=>true,"message"=>"please provide sports"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["long"]))){
					echo json_encode(["error"=>true,"message"=>"please provide longitude"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["lat"]))){
					echo json_encode(["error"=>true,"message"=>"please provide latitude"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["gender"]))){
					echo json_encode(["error"=>true,"message"=>"please provide gender"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["age"]))){
					echo json_encode(["error"=>true,"message"=>"please provide age"]); exit;
				}
				if($HELPER->isEmpty(isset($_POST["user_token"]))){
					echo json_encode(["error"=>true,"message"=>"please provide user token"]); exit;
				}
				$locality = $_POST["locality"];
				$sport = $_POST["sport_id"];
				$long = $_POST["long"];
				$lat = $_POST["lat"];
				$gender = $_POST["gender"];
				$age = $_POST["age"];
				$user = $_POST["user_token"];

				$findMatchData = new Match();
				$findMatchData->setLocality($locality)->setLocationLong($long)->setLocationLat($lat)->setGender($gender)->setAge($age)->setSportId($sport)->setStatus(0);
				
				$user = $C_User->select()->where("user_token",$HELPER->cleanString($user))->get()[0]["id"];
				$isInGame = $C_Match->isPlayerInGame($user,$gender);
				if($isInGame["result"] == true){
					//get the current number of players in the match room
					$playerCount = $C_MatchPlayer->getPlayerCount($isInGame["match_details"]["match_id"]);
					//get sports data for the player limit
					$sport = $C_Sports->select()->where("sport_id",$isInGame["match_details"]["sport_id"])->get()[0];
					//check if the players is already occupied
					$matchReady = ($playerCount == $sport["player"])?true:false;
					$RESPONSE = ["error"=>false,"message"=>"You have a pending game","ingame"=>true,"match_ready"=>$matchReady,"player_count"=>$playerCount,"data"=>$isInGame["match_details"]];

				    if($matchReady){
				    	$listPlayers = Query::table(MatchPlayer::TABLE)->select("user_id")->where(MatchPlayer::MATCH_ID,"=",$isInGame["match_details"]["match_id"])->getQuery();
				    	$listPlayerDetails = Query::table(User::TABLE)->select()->in(User::ID,$listPlayers)->get();
					    foreach($listPlayerDetails as $player){
			                ServiceFCM::title("Sportify")
			                ->message("game_is_ready")
			                ->picture(null)
			                ->send($player["access_token"]); 
			            }
			            echo json_encode(["error"=>false,"message"=>"Your game is now ready","match_ready"=>$matchReady,"player_count"=>$playerCount,"data"=>$isInGame["match_details"]);
			        } else {
						echo json_encode($RESPONSE); exit;
			        }
				
				} else {
					$matches = $C_Match->select()->where("gender",$gender)->where("locality",$locality)->where("sport_id",$sport)->where("status",0)->whereCondition("age",$age+3,"<=")->whereCondition("age",$age-3,">=")->order("match_id","DESC")->get();
					if(sizeof($matches) > 0){
						$match = $matches[0];
						// compute the distance of the current user
						$distance = $HELPER->distance($lat, $long, $match["location_lat"], $match["location_long"], "M");
						//get the current number of players in the match room
						$playerCount = $C_MatchPlayer->getPlayerCount($match["match_id"]);
						//get sports data for the player limit
						$sport = $C_Sports->select()->where("sport_id",$match["sport_id"])->get()[0];
						//check if the players is already occupied
						$matchReady = ($playerCount == $sport["player"])?true:false;
						if($distance < 500){
							if($playerCount < $sport["player"]){
								$team = (($sport["player"]/2) > $playerCount)?0:1;
								$C_MatchPlayer->insert((new MatchPlayer)
									->setMatchId($match["match_id"])
									->setPlayerId($user)
									->setTeam($team)
									->setType("member"));
								$RESPONSE = ["error"=>false,"message"=>"Congrats! There is a room available for you.","ingame"=>true,"match_ready"=>$matchReady,"player_count"=>$playerCount,"data"=>$match];
								
								echo json_encode($RESPONSE); exit;

							} else { echo insertMatch($findMatchData, $user, "Created your own match room. The available match rooms are already full."); exit; }
						} else { echo insertMatch($findMatchData, $user, "Created your own match room. No match rooms available near from your location."); exit; }
					} else { echo insertMatch($findMatchData, $user, "Created a new match room since there is no match room available."); exit; }
				}
	        }  else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
	    } else { echo json_encode(["error"=>true,"message"=>"Invalid API Key"]); }
	} else { echo json_encode(["error"=>true,"message"=>"No API key, Illegal access"]); }
?>
