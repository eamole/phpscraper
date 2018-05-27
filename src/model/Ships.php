<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:58
 */

namespace Model;

use \Core;
use Model;

class Ships extends Core\Model
{

	public static $className = __CLASS__;

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
		parent::__construct('ships', 'ship', 'name,details,url');
		// we now need to add foreign keys etc
		$this->fk(Model\Cruiselines::class);

	}

}