<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 10/06/2018
 * Time: 21:47
 */

namespace App\ORM\Models;
use App\ORM\Entities;
use ORM\Model;

class Ports extends Model
{

	public function __construct()
	{
		parent::__construct( self::class , Entities\Port::class, null,
				  "name,details,url");
		// vlinks should be set by ships

	}


}