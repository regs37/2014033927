<?php
	namespace Model;
	class MatchPlayer {
		public const TABLE = "match_player";
		public const ID = "id";
		public const MATCH_ID = "match_id";
		public const PLAYER_ID = "player_id";
		public const TEAM = "team";
		public const TYPE = "type";
		private $attrib = [
		"id"			=>'',
		"match_id"		=>'',
		"player_id"		=>'',
		"team"			=>'',
		"type"			=>''];
		public function __construct(){
			
		}

		/** ========================
		 *
		 * 			GETTERS
		 *
		 =========================== */

		public function getId(){
			return $this->attrib["id"];
		}
		public function getMatchId(){
			return $this->attrib["match_id"];
		}
		public function getPlayerId(){
			return $this->attrib["player_id"];
		}
		public function getTeam(){
			return $this->attrib["team"];
		}
		public function getType(){
			return $this->attrib["type"];
		}
		/** ========================
		 *
		 * 			SETTERS
		 *
		 =========================== */

		public function setId($id){
			$this->attrib["id"] = $id;
			return $this;
		}
		public function setMatchId($match_id){
			$this->attrib["match_id"] = $match_id;
			return $this;
		}
		public function setPlayerId($player_id){
			$this->attrib["player_id"] = $player_id;
			return $this;
		}
		/**
		 * @var $team = [0,1]
		 */
		public function setTeam($team){
			$this->attrib["team"] = $team;
			return $this;
		}
		/**
		 * @var $type = [leader,member]
		 */
		public function setType($type){
			$this->attrib["type"] = $type;
			return $this;
		}

	}

?>