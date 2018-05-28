<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 21:34
 */

namespace Core;
use Core;
use Core\EntityManagerManager as EMM;

/*
 * manages each Entity's entities
 * binds to Model
 * handles id allowaction and object retrieval
 *
 * This replaces all the statics in Entity
 *
 * the constructor also replaces init
 * avoid calling more than once for a given Entity/Class
 * the only way is to ask the EMM for an entity manager
 * 
 */
class EntityManager extends ClassStatic {

	public $entities = [];	// instances - in a keyed array
	public $index = [];	// if an entity has an ID or gets an ID, its stored here
	// ID should really be part of the constructor!!
	// going to be difficult to load from a row, and add it here after
	// may have to listen to the ID - it can't change!! must be readonly


	public $entityName;		// entity name
	public $entityClass;		// class name

	//ORM
	public $modelClass;		// model class
	public $model;  // each Entity has a model object
	public $idName;  			// the primaryKey
	public $unique = [];   // fields which must be unique - can be used to verify/match

	public $props = [];

	public static function registerEntityClass($entityClass, $modelClass = null, $props = [], $args = []) {
		if(!EMM::isRegisteredEntityClass($entityClass)) {
			/*
					Model namespace + plural of entity
			*/
			if(!$modelClass) {
				// not supplied - guess
				$_modelClass = "Model\\" . Core\Util::getPlural(Core\Util::baseName($entityClass));
				if(class_exists($_modelClass)) $modelClass=$_modelClass;
			}
			$em = new EntityManager($entityClass, $modelClass, $props, $args);
		} else {
			$em = EMM::getEntityManager($entityClass);
		}
		return $em;
	}
	public static function getEntityManager($entityClass,$modelClass=null) {
		if(!EMM::isRegisteredEntityClass($entityClass)) {
			// ask the entity to register itself - if defined
			if(method_exists($entityClass,'registerEntityManager'))
				return $entityClass::registerEntityManager();
			else {
				$em = self::registerEntityClass($entityClass,$modelClass);
				return $em;
			}
		}
		return EMM::getEntityManager($entityClass);
	}

	public function __construct($entityClass, $modelClass, $props = [], $args = []) {
		$this->entityClass = $entityClass;
		$this->entityName = self::entityName();

		// ORM
		$this->modelClass = $modelClass;  // to create objects from queries
		$this->model = new $modelClass;

		/*
		 * I  should interrogate the model for the default fields
		 * and mappings
		 */
		if (is_string($props)) $props = explode(",", $props);
		foreach ($props as $prop) {
			$this->props[$prop] = [];
		}

		if ($args) {
		}

		/*
		 * the mode should now update the entity
		 */
		self::updateEntityFromModel();

	}

	public function isProperty($name) {
		return isset($this->props[$name]);
	}
	public function entityName($className = null)
	{
		$name = $className ?? $this->entityClass;
		return Util::baseName($name);
	}

	public function updateEntityFromModel()
	{
		/*
		 * find all the fields , foreign keys and vlinks
		 */
		$model = Model::getModel($this->modelClass);
		/*
		 * need a name mapping schema!!
		 */
		foreach ($model->fields as $name => $field) {
			self::addProp($name);   // TODO : transform names into entity style names not underscore
		}

		foreach ($model->fks as $fk) {
			// each foreign key needs to create two entries in the Entity - the id and the object

			// fk already defined as a property!
			// self::addProp($fk->name);   // TODO : transform names into entity style names not underscore
			self::addProp($fk->model->entityName);   // should add cruiseline to ship
		}
		// add vlinks as methods? eg ships() for cruiseline
	}


	/**
	 * @param $name
	 * @param null $modelName
	 * @param null $args
	 */
	/*
	 * modelName is bad name
	 * it should be mapped name it mapping the property to the model
	 */
	public function addProp($name, $modelName = null, $args = [])
	{
		if (isset($this->props[$name])) {
			self::error("property name [$name] being redefined!");
		}
		$prop = [
				  'name' => $name,
				  'modelName' => ($modelName) ? $modelName : $name,
				  'args' => $args
		];

		foreach ($args as $key => $arg) {#
			$prop[$key] = $arg;
		}
		$this->props[$name] = (object) $prop;
	}

	public function add($entity){
		if($entity->id()) {
			$this->index[$entity->id()]=$entity;
		} else {
			$this->entities[] = $entity;
		}
	}

}