<?php 

	/**
	 * System Core 
	 * API Key: cf6ae6fe0be2d88c228bab41528c4d9ae4ae75df030f4ebec569883a9649b565
	 * API Keyword: sportify_api_key
	 */

	/**
	 * Server Credentials
	 */
	date_default_timezone_set('Asia/Singapore');


	/**
	 * Database Credetials
	 */
	define('SERVER_NAME',"localhost");
	define('DATABASE_NAME',"sportify");
	define('USERNAME',"root");
	define('PASSWORD',"");
	define('APIKEY','1abdd08c349f4895b3f1162a7f2daa9d');
	define('ROOT','http://localhost/sportify');
	
	/**
	 * API access key for firebase cloud messaging (FCM)
	 * Key used in sending push notifications
	 */
	define('API_ACCESS_KEY','AAAAUPlwQBI:APA91bGl-fKSU_lruq0sLXmrb9qMUvDbUBjJonor0tjkOovvxOMDicnnPOm6dGO_tQ31okfBWIRxL3SB8J--XhShEUMmHgO1cX9TtaBGkAWpbUrP7Brydxo6vCIR3MnumlnhrC-PkseL');

	

	/**
	 * Time formatting 
	 */
	define( "TIMEBEFORE_NOW",         'Just now' );
    define( "TIMEBEFORE_MINUTE",      '{num} min' );
    define( "TIMEBEFORE_MINUTES",     '{num} mins' );
    define( "TIMEBEFORE_HOUR",        '{num} hr' );
    define( "TIMEBEFORE_HOURS",       '{num} hrs' );
    define( "TIMEBEFORE_YESTERDAY",   'Yesterday' );
    define( "TIMEBEFORE_FORMAT",      '%B %e' );
    define( "TIMEBEFORE_FORMAT_YEAR", '%B %e, %Y' );



    /**
     * Twig Configuration
     */
    define("LOADER_FILESYSTEM","view");
    