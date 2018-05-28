<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 15:37
 */

namespace Model;
use Core;
use Entity;

class CruiseLines extends Core\Model {

	/**
	 * CruiseLines constructor.
	 * @param $tableName
	 * @param $entityClass
	 * @param array $fields
	 * @param array $args
	 */
	public function __construct()
	{
		parent::__construct('cruiselines', Entity\CruiseLine::class,
				  "name,details,url,logo");
		// vlinks should be set by ships
		
	}
	
	
}