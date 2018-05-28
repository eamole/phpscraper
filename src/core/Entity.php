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
 * this uses a very mixed model, talking direct to DV
 * we will change this to talk to Model
 * we badly need an EntityManager or higher level object
 * using the statics is extremely messy
 * Each entity class has an EM - all stored in App!
 * Is there an Manager object ? Possibly statics
 * It should track the "static" props of the Entity Class
 * it should also track every instance of an Entity to ensure it is
 * saved, and shared (same id)
 * created by Entity::init for a given entity
 */

/*
 * have a small problem
 * because I'm creating unbound objects eg the Hybrid
 * the binding should really come from oustide the Entity - entity events for example
 * in addition, an unbound object can be verified (like a lookup)
 * by using the value of a field as a lookup, and assigning an ID if found, or a new record
 * if not found
 * this behaviour needs to be turned on/off - on save?
 */

class Entity extends Base
{
	// lets see if putting in top level makes a diff
//	public static $_init = false;

	use _Base;
	public static $entityName;
	// ideally these would be Class objects!! might use a trait - bind class static to an object
	public static $entityClass;

	public static $modelClass;

	public static $idName;  // ???
	public static $entities = []; // each Entity class
	// orm mapping

	public static $props = [];
	public static $model;  // each Entity has a static model object

	public static $unique = [];   // fields which must be unique - can be used to verify/match if an entity already exists
	/*
	 * have a small problem :
	 */


//    public $_fields;    // a reference - not required I think dbg shows statics


	public $data;    // this is where the object data is stored in keyed array

	public $dirty = false; // only save if a value changed/set

	/*
	 * props should auto come from Reflection!!
	 * only add a prop is a mapping is required
	 */
	public static function init($modelClass, $entityClass, $props = [], $args = [])
	{
		// I'm thinking there might be only one _init flag for all Entity classes
		if (!static::$_init) {
			self::$modelClass = $modelClass;  // to create objects from queries
			self::$model = new $modelClass;
			self::$entityClass = $entityClass;
			self::$entityName = self::entityName();

			/*
			 * I  should interrogate the model for the default fields
			 * and mappings
			 */
			if (is_string($props)) $props = explode(",", $props);
			foreach ($props as $prop) {
				self::$props[$prop] = [];
			}

			if ($args) {
			}

			/*
			 * the mode should now update the entity
			 */
			self::updateEntityFromModel();
			// strip off namespaces
//            self::$model = new $modelClass();
			// every entity will require
//            self::$id = self::$entity . "_id" ;
//            self::addProp(self::$table . "_id" ,
//                "integer" ,
//                null ,
//                [
//                    'autoincrement' => "true" ,
//                    'not null'=>true,
//                    "primary key"=>true]);
//            foreach ($fields as $field) {
//                self::debug("adding field ",$field['name']);
//                self::addField($field['name'],$field['type'],$field['len'],$field);
//            }
			static::$_init = true;
		}

	}

	public static function entityName($className = null)
	{
		$name = $className ?? self::$entityClass;
		return Util::baseName($name);
	}

	public static function updateEntityFromModel()
	{
		/*
		 * find all the fields , foreign keys and vlinks
		 */
		$model = Core\Model::getModel(self::$modelClass);
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


	public function id($id = null)
	{
		$name = self::$id;
		if ($id) {
			$this->$name = $id;
		}
		return $this->$name;
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
	public static function addProp($name, $modelName = null, $args = [])
	{
		if (isset(self::$props[$name])) {
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
		self::$props[$name] = (object) $prop;
	}


	public function __construct() {
		static::init();   // hopefully late bound!!

	}

	/*
	 * need a way to bypass the dirty check - write direct to obj
	 * from the Model
	 * doesn't work!! gotta store values in an array not on the object
	 */
	public function _set_($name, $value)
	{
		if (isset(self::$props[$name])) {
			$this->data[$name] = $value;
		} else {
			self::error("SET Reference to an undefined property [$name] in Class : " . self::$className);
		}
		return $this;
	}

	public function __set($name, $value)
	{
		if (isset(self::$props[$name])) {
			if ($this->$name !== $value) {
				$this->dirty = true;      // to allow setting dirty to false!!
				$this->data[$name] = $value;
			}
		} else {
			self::error("SET Reference to an undefined property [$name] in Class : " . self::$className);
		}
		return $this;
	}

	public function __get($name)
	{
		if (isset(self::$props[$name])) {
			return $this->data[$name];
		} else {
			self::error("GET Reference to an undefined property [$name] in Class : " . self::$className);
		}
	}

	public function __call($name, $args)
	{
		$method = [$this, $name];
		if (!method_exists($this, $name)) {// this does not detect an undefined method!! looping!!
			self::error("method not callable [$name]");
			return;
		}
		$val = call_user_func_array($method, $args);
		return $val;
	}

	/*
	 * work with the DB
	 */

	/*
	 *
	 */
	public function save() {

		self::$model->save($this);
	}

	/*
	 * using the id
	 */
	public function saveOrAddKey($key)
	{
//		public static function findAddKey($table, $id , $keyField , $data)
//		Core\Db::findKeyOrAdd(self::)
		self::$model->saveKeyOrAdd($key, $this);
		return $this;
	}

	/*
	 * this should find ALL values in a QBE manner
	 * again working, with unoque
	 */
	public static function findUniqueBy($field, $value) {
		return self::$model->findUniqueBy($field,$value);

	}


}