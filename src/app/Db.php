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
			self::connectToWP("src/");
		}
	}
}