<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 15:42
 */

namespace Entity;
use Core;
use Model;

class CruiseLine extends Core\Entity{

//	public $name,$logo;	// to avoid warnings when accessing them - using data array now

	public static function init()
	{
		parent::init(Model\CruiseLines::class, self::Class);
		
	}
	
}