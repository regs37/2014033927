<?php 
	

	/**
	 * System Core 
	 */
	require_once('core/Config.php');

	require_once dirname(__FILE__).'/vendor/Twig/Autoloader.php';
    Twig_Autoloader::register(true);
	spl_autoload_register(function ($class_name) {
		if(file_exists($class_name . '.php')){
    		require_once($class_name . '.php');
    	}
	});
	


	

