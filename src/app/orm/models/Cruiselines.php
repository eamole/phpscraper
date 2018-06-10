<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 15:37
 */

namespace App\ORM\Models;
use Core;
use ORM\Entity;
use App\ORM\Entities;
use ORM\Model;

class Cruiselines extends Model {

	/**
	 * Cruiselines constructor.
	 * @param $tableName
	 * @param $entityClass
	 * @param array $fields
	 * @param array $args
	 */
	public function __construct()
	{
		parent::__construct( self::class , Entities\Cruiseline::class, null,
				  "name,details,url,logo");
		// vlinks should be set by ships

	}
	
	
}