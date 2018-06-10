<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 10/06/2018
 * Time: 18:28
 */

namespace App\ORM\Models;

use App\ORM\Entities;
use ORM\Model;

class Cruises extends Model
{
	public function __construct()
	{
		// fields already in entity
		parent::__construct( self::class , Entities\Cruise::class, null ,
				  "title,cruiseline_id,ship_id,itinerary_url");
	}

}