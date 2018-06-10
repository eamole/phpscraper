<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace ORM;

use Core;
use Core\Util;
use Core\Db as Db;

/*
 * the model is one instance per table
 * if the entity had a class object we wouldn't have to use statics
 * maybe if we create an EntityModel class or something
 * we should probably at minimum read the structure from the underlying table
 * in addition, we should probably generate a YAML file to allow it to be customised.
 * Do a diff between current, and archived YAML to find what changes to affect
 * issue the SQL
 * archive the yaml
 * In this instance, we will use the actual models to customise the basic settings of each field
 * beyond the default
 */

class Model extends Core\Base
{

	public static $models=[];	// all Models stored by modelClass name

	// properties, boxes , nuts
	public $modelClass;     // name of the actual model
	public $staticClass;     // name of the static class - should be same as above
	public $entityClass;     // link to entityClass

	public $tableName;       // should be plural of entity
// must defer till Entity has been constructed - circular
// using plural guesses
	public $entityName;          //
	public $idField;              // id field name &entity_id
	public $fields = [];       // a list of field names types etc
	public $fks = [];            // link fields to other tables
	public $vlinks = [];         // these are virtual links - backlinks to table which link to us
	public $entityMappings = [];   // mappings from entity to fields
	public $fieldMappings = [];   // mappings from fields to entity

//    public static $dbMappings = [];  // mappings in and out of db

	// this should be model class, get table name from there
	// needed
	public function __construct($modelClass, $entityClass=null, $tableName=null, $fields = [], $args = [])
	{
		parent::__construct();

		// ??
		// this does not work!! - getting confused calli ng one model from another
//		static::$model = $this;   // make sure the model is accessible from toplevel class static
		self::setModel($modelClass,$this);

		$this->modelClass = $modelClass;		// static::class should do it
		$this->staticClass = static::class;
		if($this->modelClass !== $this->staticClass) self::warning("modelClass [$0] and staticClass [%1] are different",
				  $this->modelClass, $this->staticClass);

		$baseName = Util::baseName($modelClass);
		$singular = Util::getSingular($baseName);
		$this->entityClass = $entityClass ?? "Entity\\" . $singular;	// guess from Model - singular
		$this->entityName = $singular;

		//$this->tableName = $tableName ?? Util::getPlural(strtolower($this->entityName));
		$this->tableName = $tableName ?? strtolower($baseName);

		$this->idField = strtolower($singular) . "_id";   // this field needs to be mapped to entitySchema
		$this->addField($this->idField, ['pk' => true]);

		/*
		 * cannot use anything in the Entity as it may not have been constructed yet
		 */
//		$this->entityName = $entityClass::$entityName;
//		if(empty($this->entityName )) self::warning("Empty entity name from Entity in Model [%0]",$this->entityName);

		if (is_string($fields)) {
			$fields = explode(",", $fields);
		}
		foreach ($fields as $index => $field) {
			$this->addField($field);
		}

		/*
		 * we need to tell the entity stuff
		 * the db can be read for a structure
		 * so thats the fields here
		 * however, we need field ->prop mapping when different names
		 * we also need to create id columns in the entity (using a diff naming schema - camelcase for _
		 * - for any embedded entities
		 * therefore we need to establish a relationships table of FK's
		 *
		 */

	}

	public static function isDefined($modelClass){
		return isset(self::$models[$modelClass]);
	}
	/*
	 * can't must come from the entity or the actual model
	 */
	public static function getModel($modelClass=null) {
		// gotta detect if model initialised or fire
		$modelClass=$modelClass ?? static::class;
		if(!self::isDefined($modelClass)) {
			//try to create
			return new $modelClass($modelClass);	// TODO : fudge - class should kn ow itself!!
			// shouldn't get here
			self::error("Cannot retrieve or create Model for [$modelClass]");
		} else {
			return self::$models[$modelClass];
		}
	}
	public static function setModel($modelClass,$model) {
		if(self::isDefined($modelClass)) {
			self::error("Model [$modelClass] has already been created. Check why creating again");
		} else {
			self::$models[$modelClass]=$model;
		}
	}

	public function addField($name, $args = [])
	{
		$field = strtolower($name);
		if (isset($this->fields[$name])) {
			self::warning("Adding a field [$name] that already exists in Model [%0]", $this->tableName);
		} else {
			/*
			 * this needs an SQL/Model type field definition ...
			 */
			/*
				at the very least, I should read the structure from the table itself - if it exists
			*/
			$field = [
					  'name' => $name
			];
			foreach ($args as $key => $arg) {
				$field[$key] = $arg;
			}
			$this->fields[$name] = (object)$field;
		}
	}


	function isField($name)
	{
		return isset($this->fields[$name]);
	}
	/*
	 * this should possibly be a static method on Entity!!
	 */
	public function getEntityName($modelClass = null)
	{
		$name = $modelClass ?? $this->modelClass;
		$name = Util::getSingular(Util::baseName($name));
		// bloody hell, entity name is already singular!!
		//		$name = $this->getSingular($name);
		return $name;
	}


//	public function entityName() {
//		$entityClass=$this->entityClass;
//		return $entityClass::$entityName;
//	}
//	public function idField() {
//		return strtolower($this->entityName()) . "_id";
//	}
	/*
	 * use lowercase singular + _id
	 */

	/*
	 * assume a class exists - really should have an ORM class extending the model
	 */
	/**
	 * modelClass : this is the field we point into
	 * the local key refers to the remote primary key
	 */
	public function fk($modelClass, $fieldName = null, $fkFieldName = null, $type = "ManyToOne", $vlinkName = null)
	{

		// $fkModel =  static::getModel($modelClass);   // get the remote/foreign model
		$fkModel =  $modelClass::getModel($modelClass);   // get the remote/foreign model
		$localName = $fieldName ?? $fkModel->idField;   // determine local field name - id

		$fkFieldName = $fkFieldName ?? $localName;   //  remote field name - same as local
		if (!$fkModel->isField($fkFieldName)) {
			self::error("Invalid remote foreign key [%0].[$fkFieldName]", $fkModel->tableName);
		}

		$field=[];
		if ($this->isField($localName)) {
			// really I should just update the field with FK info
			self::debug("Merging a FK def [%0].[$localName]", $this->tableName);
			$field = (array) $this->fields[$localName];
		}
		// merge fk data with existing field data
		// assume link to id
		$fk_table = $fkModel->tableName;
		$fk = [
				  'name' => $localName,
				  'type' => 'int',
				  'table' => $fkModel->tableName,
				  'field' => $fkFieldName,
				  'model' => $fkModel
		];
		//TODO : need to be careful if we move to Field Objects
		// need to convert or upgrade a Field Object to a Foreign Key
		$field = (object) array_merge($field, $fk );
		// don't forget to convert to object - after array merging above.
		$fk = (object) $fk;
		$this->fields[$localName] = $fk;
		$this->fks[$localName] = $fk;	// TODO : check already warning
		// vlinks
		// the back link will be known as this table name - ie select all ships in cruiseline
		$vlinkName = $vlinkName ?? $this->tableName;
		$fkModel->vlink($vlinkName, $this, $fk);


	}

	public function vlink($name, $model, $fk)
	{
		if ($this->isField($name)) {
			self::warning("Trying to create a virtual link [$name] from [%3] with same name as real field [%0].[%1]",
					  $this->tableName, $name, $model->tableName);
		} else {
			$this->vlinks[$name] = ['model' => $model,
					  'rel' => $fk
			];
		}
	}

	/*
	 * This should probable be a specialised form of findBy
	 */
	public function find($id)
	{
		$entity = $this->findUniqueBy(self::$idField, $id);
		return $entity;
	}

	public function findUniqueBy($fieldName, $value)
	{

		$rows = Db::$db->findBy($this->tableName, $fieldName, $value);
		// copy of code from DB
		if (!$rows) {    // $data->count() == 0
			self::warning("did not find unique [$value] on [%0.%1]", $this->tableName, $fieldName);
			return $rows;

		} else {

			if (count($rows) > 1) {
				// error keyField should be unique
				self::error("unique field [%1] value [ $value ] should be unique in table [ %0 ]. Returned [%2] objects",
						  $this->tableName, $fieldName, count($rows));
				return null;
			} else {
				$row = $rows[0]; //single record
				$entity = new $this->entityClass();
				$entity = $this->rowToEntityMapping($row, $entity);
				return $entity;
			}
		}

	}

	/*
	 * INSERT
	 * must not have ID (otherwise it becomes a copy on the table, not in memory
	 * encapsulate the insert logic
	 */
	private function insert($entity)
	{
		// no guards
		if (!$entity->dirty) self::warning("creating(saving) a non-dirty entity");
		$row = $this->entityToRowMapping($entity);
		$row = Db::$db->insert($this->tableName, $this->idField, $row);   // $table, $id, $keyField, $data
		$this->rowToEntityMapping($row, $entity);   // hopefully ext entity updated
		return $row;
	}


	/*
	 * UPDATE
	 * must have ID or an alt key
	 * keyField : an alt key for updates - must/should be unique
	 */
	private function update($entity, $keyField = null)
	{
		// no guard - check for ID
		$keyField = $keyField ?? $this->idField;
		$row = $this->entityToRowMapping($entity, []);
		$row = Db::$db->updateKey($this->tableName, $this->idField, $keyField, $row);   // $table, $id, $keyField, $data
		// should really read the row - as it is stored not as we think it is
		$this->rowToEntityMapping($row, $entity);   // hopefully ext entity updated
		return $row;
	}

	/*
	 * $keyField : alternate unique key for update eg unique names
	 */
	public function save($entity, $keyField = null)
	{
		/*
		 * test if has an id
		 * if not INSERT
		 * else
		 * UPDATE
		 */
		$idField = $this->idField;
		$keyField = $keyField ?? $idField;
		if (!$entity->$idField) {      // no id
			$row = $this->insert($entity);
			return $row;
		} else {
			/*
			 * UPDATE
			 */
			$row = $this->update($entity, $keyField);
			return $row;   // $entity???


		}

	}

	/*
	 * called with entities
	 * assumes the prop field is unique!! therefore can add if not found
	 */
	/**
	 * @param $keyProp
	 * @param $entity
	 * @return mixed|null
	 */
	public function saveKeyOrAdd($keyProp, $entity)
	{

		/*
		 * shit we need a mapping from property field name to field name
		 */
		$keyField = $keyProp;
		$value = $entity->$keyProp;

		$rows = Db::$db->findBy($this->tableName, $keyField, $value);
		// copy of code B
		if (!$rows) {    // $data->count() == 0
			/*			// ADDD

						$row = $this->entityToRowMapping($entity);
						$row = Db::$db->insert($this->tableName,$this->idField,$row);	// $table, $id, $keyField, $data

						// we should probably maps the row values back - not too sure if we should read them first
						// to get table based defaults etc
						$this->rowToEntityMapping($row,$entity);	// hopefully ext entity updated*/
			$row = $this->insert($entity);
			return $row;

		} else {

			if (count($rows) > 1) {
				// error keyField should be unique
				self::error("key field [%1] value [ $value ] should be unique in table [ %0 ]", self::$tableName, $keyField);
				return null;
			} else {
				$row = $rows[0]; //single record

				//overwrite the row data with the object
				/*$row = $this->entityToRowMapping($entity, $row);

				$row = Db::$db->updateKey($this->tableName,$this->idField,$keyField,$row);	// $table, $id, $keyField, $data
				// should really read the row - as it is stored not as we think it is
				$this->rowToEntityMapping($row,$entity);	// hopefully ext entity updated
				*/
				$row = $this->update($entity, $keyField);   // use an alt key other than id
				return $entity;
			}
		}

	}

	/*
		 * need to ensure only props with fields
		 */
	public function entityToRowMapping($entity, $row = [])
	{
//		$_entity = (array)$entity;
		$_entity = $entity->data;	// this is really a violation of security
		// we need a list of field names and properties
		foreach ($_entity as $prop => $value) {
			// only map fields that exist on the row
			$field = (isset($this->entityMappings[$prop])) ? $this->entityMappings[$prop] : $prop;

			// maps value as well
			$value = $_entity[$prop];   //might be better using the originla entity->__get()
			/*
			 * only use property values who map to a field
			 */
			if ($this->isField($field) && ($field !== $this->idField))
				$row[$field] = $value;
		}
		return $row;
	}

	public function rowToEntityMapping($row, $entity)
	{
		// we need a list of field names and properties
//		foreach ($row as $field => $value) {
//			$prop = self::$entityMappings[$field];
//			$entity->$prop = $value;
//		}

		// $_entity = (array)$entity;
		// we need a list of field names and properties
		foreach ($row as $field => $value) {
			// only map fields that exist on the row
			$prop = (isset($this->fieldMappings[$field])) ? $this->fieldMappings[$field] : $field;

			// allow fo remapped values as well
			$value = $row[$field];   //might be better using the originla entity->__get()
			/*
			 * only use property values who map to a field
			 */
//			if($this->isField($field) && ($field !== $this->idField))

			// bypass dirty
			$entity->_set_($prop, $value);

		}


		return $entity;
	}


}