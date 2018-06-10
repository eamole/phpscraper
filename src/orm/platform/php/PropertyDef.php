<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 13:38
 */

namespace ORM\Platform\Php;
/*
 * this is the base class for all properties on an Entity
 * the info loaded into here will rob come from ORM/DB
 * subclasses of this will consume additional data
 */

use Core;
/*
 * this should probably extend a base data type - ie has basic type info
 * but attached to an entity
 * handle value validation, type , range checking etc
 */
use ORM\EntityManager;
use ORM\Entity;

class PropertyDef extends Core\Base
{

	public $entityDef;
	public $name;
	public $args = [];

	/**
	 * PropertyDef constructor.
	 * @param EntityManager $entityDef
	 * @param string $name
	 * @param array|null $args - might be an object or array!!
	 */
	public function __construct(EntityManager $entityDef, string $name, $args=null) {
		parent::__construct();
		// $parent is an EntityManager - really an EntityDef
		$this->entityDef = $entityDef;
		$this->name=$name;
		$this->args=$args;
	}

	public function setValue(Entity $entity, $value,bool $bypassDirty=false) : Entity {
		self::debug("Setting entity value from PropertyDef for Entity [%0]",$this->entityDef->entityClass);
		$name = $this->name;
		$entity->__set__( $this , $value , $bypassDirty);
		return $entity;
	}
	public function getValue(Entity $entity) {
		self::debug("Getting entity value from PropertyDef for Entity [%0]",$this->entityDef->entityClass);
		$name = $this->name;
		return $entity->__get__($this);
	}

}