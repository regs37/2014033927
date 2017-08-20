<?php 

	class ServiceRegister {
		public function validator($email){
			$user = new User();
			$user->setEmail($email);
			return $this->_validate($user);
		}
		public function register($user){
			return $this->_validate($user);
		}
		public function _validate($user){
			$u_cntrlr = new ControllerUser();
			if($u_cntrlr->isExist("email",$user->getEmail())){
				$userData = $u_cntrlr->where("email",$user->getEmail())->get();
				return json_encode(["error"=>false,"user_status"=>"old","data"=>$userData]);
			} else {
				//if($login_type == "facebook"){
					return json_encode(["error"=>false,"user_status"=>"new","login_type"=>"facebook"]);
				//} else {
				//	return json_encode(["error"=>false,"user_status"=>"new","login_type"=>"google"]]);
				//}
				$u_cntrlr->insert($user);	
				$userData = $u_cntrlr->where("email",$user->getEmail())->get();
				return json_encode(["error"=>false,"user_status"=>"new","data"=>$userData]);
			}
		}
	}

 ?>