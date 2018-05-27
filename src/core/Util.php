<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 19:10
 */

namespace Core;


class Util
{

	public static function baseName($fullClassName) {
		// PhpStom error '\' is being escaped!!
		$className = end($arr=explode("\\", $fullClassName));
		return $className;
	}
}