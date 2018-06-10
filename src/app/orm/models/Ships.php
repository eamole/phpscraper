<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:58
 */

namespace App\ORM\Models;

use \Core;
use App\ORM\Entities\Ship;
use ORM\Model;
use ORM\Entity;

/*
 * what effect does working with FK's have on the system
 * firstly, in the Entity, the cruiseline is a "virtual" field that accepts an object
 *
 *
 */

class Ships extends Model
{


	/**
	 * Ships constructor.
	 * @param $tableName
	 * @param $entityClass
	 * @param array $fields
	 * @param array $args
	 */
	public function __construct()
	{
		// fields are the basic simple fields
		parent::__construct(self::class,Ship::class , null , 'name,details,url');
		// we now need to add foreign keys etc
		// might consider 4 funcs - ManyToOne, OneToMany, OneToOne and ManyToMany
		$this->fk(Cruiselines::class);

	}

}