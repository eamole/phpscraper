<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 18:22
 */
/*
 * this doesn't work - don't use
 */
namespace App;
use Core;

class App extends Core\App{
	static $app;

	public static function getInstance() {
		if(!self::$app) {
			self::init();
		}
	}

	public static function init() {
		parent::init();
		self::$db=MyApp\Db::init();
	}
}