<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:03
 */

namespace Entity;

use Core;
use Entity;

class Ship extends Core\Entity
{
	public $shipId;			// ship_id
	public $name;
	public $cruiseLine;		// this is actually an object!! need to link to entity
	public $cruiseLineId;	// cruiseline_id ; this should come from Model
	public $logo;				// url
	public $details;			//

	public static function init()
	{

		parent::init(Entity\Ships::class, self::class, [
			'shipId' , 'name' , 'cruiseLine' , 'cruiseLineId' , 'logo' , 'details'
		]);

	}

	public function __construct()
	{
		parent::__construct("ship");
		self::init(Ships::$className, []);
	}


}