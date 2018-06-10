<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 13:34
 */

namespace ORM\Platform\Php\DataTypes;

/*
 * maybe should belong in Core\Platform\Php\ORM\Entity\
 */
use ORM\Platform\Php\PropertyDef;
use ORM\EntityManager;
/*
 * this is the cass for handling properties which are entities
 * for example cruiseline on ship
 * I'll assume all these are attached to EntityManagers
 */
class Entity extends PropertyDef
{

	public function __construct(EntityManager $entityDef, string $name, array $args = null)
	{
		parent::__construct($entityDef, $name, $args);
	}

}