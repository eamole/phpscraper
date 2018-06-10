<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 19:10
 */

namespace Core;


class Util extends Base
{
	// "es" => "", - hits cruiselines - intended to catch boxes - use a bloody dict
	public static $plurals = ["ies" => "y",  "s" => "", "a" => "um"];
								// monkey(s) , funny(ies), datum(a) , box(es)
	public static $singulars = [ 'ey' => 's' ,'y' => "ies" , 'um' => "a" , 'x' => 'xes' ];

	public static function baseName($fullClassName) {
		// PhpStom error '\' is being escaped!!
		$arr=explode("\\", $fullClassName);
		$className = end($arr);
		return $className;
	}
	/*
	 * use common english plural endings to get singular
	 */
	public static function getSingular($word) {
		$ret = null;
		foreach (self::$plurals as $plural => $singular) {
			// compare
			if (substr($word, 0 - strlen($plural)) == $plural) {
				// strip off ending
				$ret = substr($word, 0, 0-strlen($plural)) . $singular;
				break;
			}
		}
		return $ret;
	}

	public static function getPlural($word) {
		$ret = null;
		$found = false;
		foreach (self::$singulars as $singular => $plural ) {
			// compare word ending with sing
			if (substr($word, 0 - strlen($singular)) == $singular) {
				// strip off ending
				$ret = substr($word, 0, strlen($singular)) . $plural;
				$found = true;
				break;
			}
		}

		if(!$found) $ret = $word . "s";	// default english plural

		return $ret;
	}
}