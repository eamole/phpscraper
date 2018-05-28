<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 16:23
 */

namespace Core;

use Core;

class App extends Base
{
//	use _Base;

	public static $app;
	public static $name;
	public static $appClassName;
	public static $http;
	public static $root;
	public static $storage = "/storage/";

	public static $db;


//    public static $app;
//    public static $app;

	public static function init($appClassName, $name , $dbClassName = null)
	{
		self::$appClassName = $appClassName;
		self::$name = $name;

		self::$app = new $appClassName();
		(self::$app)->http = new Core\Http;
		self::$root = realpath(dirname(__FILE__) . '/../../');    // needs to be relative to this source code
		self::$storage = self::$root . self::$storage;
		mkdir(self::$storage, 0777, true);

		if($dbClassName)	$dbClassName::init();

	}

	public static function readFile($path)
	{
		$str = file_get_contents(self::storage . $path);
		return $str;
	}

	public static function writeFile($path, $content)
	{
		$path = self::$storage . $path;
		// TODO : fix to strip out file name
		mkdir(dirname($path), 0777, true);
		$str = file_put_contents($path, $content);
		return $str;
	}


}