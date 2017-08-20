<?php

	class ControllerGym {
		private $database;
		private $helper;
		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}
		private function createEntity(){
			(new Schema)->table(Gym::TABLE)
			->col(Gym::GYM_ID)->int()->autoIncrement()->primary()
			->col(Gym::NAME)->varchar(255)->notNull()
			->col(Gym::EMAIL)->varchar(100)->notNull()
			->col(Gym::PASSWORD)->longtext()->notNull()
			->col(Gym::ADDRESS)->longtext()
			->col(Gym::LONGITUDE)->longtext()
			->col(Gym::LATITUDE)->longtext()
			->col(Gym::ABOUT)->longtext()
			->col(Gym::CONTACT)->longtext()
			->col(Gym::LOGO)->longtext()
			->col(Gym::TOKEN)->longtext()
			->create();
		}
		public function retrieve($data){
			if(sizeof($data) > 0){
				if(sizeof($data) == 1){
					return $this->gym($data[0]);
				} else {
					$gyms = array();
					foreach ($data as $key => $value) {
						$gyms[] = $this->gym($value);
					}
					return $gyms;
				}
			}
			return null;
		}
		public function gym($data){
			return (new Gym)->setGymId($data[Gym::GYM_ID])
			->setName($data[Gym::NAME])
			->setEmail($data[Gym::EMAIL])
			->setPassword($data[Gym::PASSWORD])
			->setAddress($data[Gym::ADDRESS])
			->setLongitude($data[Gym::LONGITUDE])
			->setLatitude($data[Gym::LATITUDE])
			->setAbout($data[Gym::ABOUT])
			->setContact($data[Gym::CONTACT])
			->setLogo($data[Gym::LOGO])
			->setToken($data[Gym::TOKEN]);
		}
	}