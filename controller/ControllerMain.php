<?php
	namespace Controller;
	use Core\View;
	class ControllerMain {
		
		public function __construct(){

		}

		public static function homePage(){
			return View::render('templates/home.html.twig', array('the' => "", 'go' => 'here'));
		}
	}