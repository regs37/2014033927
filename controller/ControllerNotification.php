<?php 
	class ControllerNotification {
		private $database;
		private $helper;
		public function __construct(){
			$this->database = new Database();
			$this->helper = new Helper();
			$this->createEntity();
		}
		private function createEntity(){
			(new Schema)->table(Notification::TABLE)
			->col(Notification::NOTIF_ID)->int()->autoIncrement()->primary()
			->col(Notification::OWNER_ID)->int(11)->unsigned()->notNull()
			->col(Notification::ACTOR_ID)->int(11)->unsigned()->notNull()
			->col(Notification::POST_ID)->int(11)->unsigned()->notNull()
			->col(Notification::TYPE)->varchar(100)->notNull()
			->col(Notification::DATE)->varchar(100)->notNull()
			->col(Notification::STATUS)->boolean()
			->foreign(Notification::ACTOR_ID,User::TABLE,User::ID)->onDelete("cascade")
			->foreign(Notification::OWNER_ID,User::TABLE,User::ID)->onDelete("cascade")
			->foreign(Notification::POST_ID,Post::TABLE,Post::POST_ID)->onDelete("cascade")
			->create();
		}
		public function retrieve($data){
			if(sizeof($data) > 0){
				// if(sizeof($data) == 1){
				// 	return $this->notif($data[0]);
				// } else {
					$notifs = array();
					foreach ($data as $key => $value) {
						$notifs[] = $this->notif($value);
					}
					return $notifs;
				// }
			}
			return null;
		}
		public function insert($data){
			return Query::table(Notification::TABLE)->insert()
			->value(Notification::OWNER_ID,$data->getOwnerId())
			->value(Notification::ACTOR_ID,$data->getActorId())
			->value(Notification::POST_ID,$data->getPostId())
			->value(Notification::TYPE,$data->getType())
			->value(Notification::DATE,$data->getDate())
			->value(Notification::STATUS,$data->getStatus())
			->init()->apply();
		}
		public function notif($data){
			return (new Notification)
			->setNotifId($data[Notification::NOTIF_ID])
			->setOwnerId($data[Notification::OWNER_ID])
			->setActorId($data[Notification::ACTOR_ID])
			->setPostId($data[Notification::POST_ID])
			->setType($data[Notification::TYPE])
			->setDate($data[Notification::DATE])
			->setStatus($data[Notification::STATUS]);
		}
	}