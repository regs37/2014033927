<?php
	namespace Core;
	use Twig_Loader_Filesystem;
	use Twig_Environment;
	use Core\Route;
	class View {
		/**
		 * @var $loader
		 * For loading templates from a resource such as the file system
		 */
		private static $loader;

		private static $twig;

		public function __construct(){
			self::init();
		}

		private static function init(){
			self::$loader = new Twig_Loader_Filesystem(LOADER_FILESYSTEM);
			//self::$twig = new Twig_Environment(self::$loader, array('debug' => true, 'cache'=>LOADER_FILESYSTEM.'/cache'));
			self::$twig = new Twig_Environment(self::$loader, array('debug' => true));
		}
		
		public static function render($temlate,$variables){
			self::init();
			$variables["ROOT"] = Route::$ROOT;
			return self::$twig->render($temlate, $variables);
		}

		
	}