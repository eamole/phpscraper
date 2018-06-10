<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 17:58
 */

namespace App;
use Core;

class Db extends Core\Db
{
	public static function init() {
		if(!self::$db) {
//			self::connectToDb("src/");
			self::connectToDb("src/app/" , "db-config.php");
		}
	}

	public static function localSettings() {

		define('DB_NAME', 'cruiseworld');

		/** MySQL database username */
		define('DB_USER', 'root');

		/** MySQL database password */
		define('DB_PASSWORD', 'root');

		/** MySQL hostname */
		define('DB_HOST', '127.0.0.1');

		$table_prefix  = 'wp_';

	}
}