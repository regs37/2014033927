<?php 

	namespace Model;
	use Core\Query;
	class Sport extends Query{
		public const TABLE = "sports";
		public const ID = "sport_id";
		public const NAME = "name";
		public const PLAYER = "player";

		public function __construct(){
			parent::__construct();
			parent::table(self::TABLE);
		}

		public function setId($id){
			$this->ID = $id;
			return $this;
		}
		public function setName($name){
			$this->NAME = $name;
			return $this;
		}
		public function setPlayer($player){
			$this->PLAYER = $player;
		}

		public function getId(){
			return $this->ID;
		}
		public function getName(){
			return $this->NAME;
		}
		public function getPlayer(){
			return $this->PLAYER;
		}
	}

 ?>