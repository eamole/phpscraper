<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;

use Core;

/*
 * the model is one instance per table
 * if the entity had a class object we wouldn't have to use statics
 * maybe if we create an EntityModel class or something
 *
 */

class Model
{
	use _Base;

	public static $model;   // singleton
	// properties, boxes , nuts
	public static $plurals = ["ies" => "y", "es" => "", "s" => "", "a" => "um"];
	public $tableName;       // should be plural of entity
	public $entityClass;     // link to entityClass
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
	public function __construct($tableName, $entityClass, $fields = [], $args = [])
	{
		// ??
		static::$model = $this;   // make sure the model is accessible from toplevel class static
		$this->tableName = $tableName;
		$this->entityClass = $entityClass;
		$this->entityName = $this->entityName();
		$this->idField = strtolower($this->entityName) . "_id";   // this field needs to be mapped to entitySchema
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

	public function addField($name, $args = [])
	{
		$field = strtolower($name);
		if (isset($this->fields[$name])) {
			self::warning("Adding a field [$name] that already exists in Model [%0]", $this->tableName);
		} else {
			$field = [
					  'name' => $name
			];
			foreach ($args as $key => $arg) {
				$field[$key] = $args;

			}
			$this->fields[$name] = (object)$field;
		}
	}

	/*
	 * can't must come from the entity or the actual model
	 */
	static function getModel($modelClass)
	{
		if (!$modelClass::$model) {
			self::$model = new $modelClass();
		}
		return self::$model;
	}

	function isField($name)
	{
		return isset($this->fields[$name]);
	}

	public function entityName($className = null)
	{
		$name = $className ?? $this->entityClass;
		$name = strtolower(Util::baseName($name));
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
	public function getSingular($word)
	{
		$ret = null;
		foreach (self::$plurals as $plural => $singular) {
			// compare
			if (substr($word, 0 - len($plural)) == $plural) {
				// strip off ending
				$ret = substr($word, 0, len($plural)) . $singular;
				break;
			}
		}
		return $ret;
	}
	/*
	 * assume a class exists - really should have an ORM class extending the model
	 */
	/**
	 * modelClass : this is the field we point into
	 * the local key refers to the remote primary key
	 */
	public function fk($modelClass, $fieldName = null, $fkFieldName = null, $type = "ManyToOne", $vlinkName = null)
	{

		$fkModel = self::getModel($modelClass);   // get the remote/foreign model
		$localName = $fieldName ?? $fkModel->idField;   // determine local field name - id

		$fkFieldName = $fkFieldName ?? $localName;   //  remote field name - same as local
		if (!$fkModel->isField($fkFieldName)) {
			self::error("Invalid remote foreign key [%0].[$fkFieldName]", $fkModel->tableName);
		}
		if ($this->isField($localName)) {
			// really I should just update the field with FK info
			self::warning("Attempting to overwrite a field def [%0].[$localName]", $this->tableName);
		} else {
			// assume link to id
			// TODO : validate fhFieldName against the model
			$fk_table = $fkModel->tableName;
			/** @var TYPE_NAME $fkModel */
			$fk = (object)[
					  'name' => $localName,
					  'type' => 'int',
					  'fk_table' => $fkModel->tableName,
					  'fk_field' => $fkFieldName,
					  'fk_model' => $fkModel
			];
			$this->fields[$localName] = $fk;
			$fks[$localName] = $fk;
			// vlinks
			// the back link will be known as this table name - ie select all ships in cruiseline
			$vlinkName = $vlinkName ?? $this->tableName;
			$fkModel->vlink($vlinkName, $this, $fk);

		}
	}

	public function vlink($name, $model, $fk)
	{
		if ($this->isField) {
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
		if ($entity->$idField) {      // no id
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
		$_entity = (array)$entity;
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