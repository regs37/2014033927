<?php
namespace Service;

class ServiceFCM{
	private static $title = "";
	private $message = "";
	private $receiverid = "";
	private $picture = "";
	private $url = 'https://fcm.googleapis.com/fcm/send';

	public function __construct(){
		
	}

	public static function title($title){
		self::$title = $title;
		return new self();
	}
	public function message($message){
		$this->message = $message;
		return $this;
	}
	public function picture($picture){
		$this->picture = $picture;
		return $this;
	}
	public function send($id){
		$fields = array (
	        'to' => $id,
	        'notification' => array (
	                "body" => $this->message,
	                "title" => self::$title,
	                "icon" => $this->picture
	        )
		);
		$fields = json_encode ( $fields );
		$headers = array (
	        'Authorization: key=' . API_ACCESS_KEY,
	        'Content-Type: application/json'
		);

		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $this->url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

		$result = curl_exec ( $ch );
		curl_close ( $ch );
	}
}