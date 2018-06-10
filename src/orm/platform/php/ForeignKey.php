<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 10/06/2018
 * Time: 15:08
 */

namespace ORM\Platform\Php;

use ORM\EntityManager;
use ORM\Entity;

/*
 * model the FK on the basic Entity PropertyDef
 */
class ForeignKey extends PropertyDef
{
	public $name, $boundField, $fTable, $fId, $fModel , $propName;
	/*
	 * problem here is I need two "properties", one for the id, the other for the entity itself
	 */
	public function __construct(EntityManager $entityDef,$name, $fk) {

		parent::__construct($entityDef,$name,$fk);
		$this->name = $name;
		$this->boundField = $fk->name;
		$this->fTable=$fk->table;
		$this->fId = $fk->field;
		$this->fModel = $fk->model;
		$this->propName=$fk->propName;	// for mappings in future
	}

	/**
	 * @param Entity $entity
	 * @param $value
	 * @param bool $bypassDirty
	 * @return Entity
	 */
	public function setValue(Entity $entity, ?Entity $value,bool $bypassDirty=false) : Entity {
		self::debug("Setting entity value from PropertyDef for Entity [%0]",$this->entityDef->entityClass);
		$name = $this->name;
		$entity->__set__( $this , $value , $bypassDirty);

		// we also need to set the id
		if($value) {	// has an object
			// TODO : need to save the object first if it has no id!! or maybe only when the two are saved
			$id = $this->fId;
			if(!$value->$id ) {
				$value->save();
			}
			$entity->__set( $this->boundField , $value->$id );
		} else {
			$entity->__set( $this->boundField , null );	// clear the existing value
		}

		return $entity;
	}

}