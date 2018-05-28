<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:03
 */

namespace Entity;

use Core;
use Model;

class Ship extends Core\Entity
{

	public static $_init = false;

//	public $shipId;			// ship_id
//	public $name;
//	public $cruiseLine;		// this is actually an object!! need to link to entity
//	public $cruiseLineId;	// cruiseline_id ; this should come from Model
//	public $logo;				// url
//	public $details;			//

	public static function init()
	{
		// not used derived from model
		// [
		//			'shipId' , 'name' , 'cruiseLine' , 'cruiseLineId' , 'logo' , 'details'
		//		]
		parent::init(Model\Ships::class, self::class );

	}

	public function __construct()
	{
//		self::init();	// not required - parent::con will do
		parent::__construct();
	}


}