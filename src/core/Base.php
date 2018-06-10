<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:50
 */

namespace Core;

//class Base {
//    use _Base;
//    public static $className=__CLASS__;		// this will always be Core\Base - its this class!!
//	// delete this at some stage
//
//}

// allow classes to hold their own members as static
trait _List
{
	public static $members = [];

	public static function add($member, $name = null)
	{
		$key = null;
		if (is_null($name)) {
			if (isset($member->name)) {
				$key = $member->name;
			}
		} else {
			$key = $name;
		}
		$key = strtolower($key);
		if (isset(self::$members[$key])) {
			self::warning("resetting the member value with key [$key] ");
		}
		self::$members[$key] = $member;

	}

	public static function get($name)
	{
		$key = strtolower($name);
		if (!isset(self::$members[$key])) {
			self::error("cannot retrieve Member using key [$key]");
		}
		return self::$members[$key];


	}

}


//trait _Base{
class Base
{

	public static function getClassName()
	{
		return static::class;
	}

// this causes problems with redefinitions!!
//    public static $className=__CLASS__;

	public static function error($msg, ...$args)
	{
//
		if(class_exists("WP_Error")) new WP_Error('code',$msg,$args);
		self::out("Error", "red", $msg, $args);
		throw new \Exception(self::_msg("Error", "red", $msg, $args));
	}

	public static function warning($msg, ...$args)
	{
		self::out("Warning", "orange", $msg, $args);
	}

	public static function debug($msg, ...$args)
	{
		self::out("Debug", "green", $msg, $args);
	}

	/*
	 * expand the message
	 */
	public static function _msg($mode, $color, $msg, $args = [])
	{
		foreach ($args as $ord => $arg) {
			$msg = str_replace("%$ord", $arg, $msg);
		}
		$msg = "\n<p style='color:$color;'>$mode : " . static::class . " | $msg </p>";
		return $msg;
	}

	public static function out($mode, $color, $msg, $args = [])
	{
      $msg = self::_msg($mode,$color , $msg , $args );
//		echo("\n<p style='color:$color;'>$mode : " . static::class . " | $msg </p>");
		echo($msg);
	}

	public static function dump($var)
	{
		echo("\n<pre>");
		var_dump($var);
		echo "</pre>";
	}

	public function __construct(){
		self::debug("creating object Class: [%0]",static::class);
	}
	public function __destruct(){
		self::debug("destroying object Class: [%0]",static::class);
	}

}