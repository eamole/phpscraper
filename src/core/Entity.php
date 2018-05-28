<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;

use Core;
use Core\EntityManager as EM;

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

	public $em;		// entity manager
	public $data;  // this is where the object data is stored in keyed array
	public $dirty = false; // only save if a value changed/set

	public static function EM() {

	}
	/*
	 * Can I now skip creating the EM in here
	 * I want to avoid params so I can auto create objects
	 * alt, I can create an EM in here just using static::class
	 * $entityClass, $modelClass,  $props = [], $args = []
	 * I've made them all optional and made guesses
	 */
	public function __construct($entityClass=null, $modelClass=null,  $props = [], $args = []) {
//		$this->em = EM::registerEntityClass($entityClass, $modelClass,  $props, $args);
		$entityClass = $entityClass ?? static::class;	// hopefully this works
		$this->em = EM::getEntityManager($entityClass,$modelClass);
		// register this object/entity with its manager
		$this->em->add($this);
	}

	/*
	 * props should auto come from Reflection!!
	 * only add a prop is a mapping is required
	 */

	public function id($id = null)
	{
		$name = $this->em->idName;
		if ($id) {
			$this->$name = $id;
		}
		return $this->$name;
	}

	function isProperty($name) {
		return $this->em->isProperty($name);
	}
	/*
	 * need a way to bypass the dirty check - write direct to obj
	 */
	public function _set_($name, $value)
	{
		if ($this->isProperty($name)) {
			$this->data[$name] = $value;
		} else {
			self::error("SET Reference to an undefined property [$name] in Class : " . static::class);
		}
		return $this;
	}

	public function __set($name, $value)
	{
		if ($this->isProperty($name)) {
			if ($this->$name !== $value) {
				$this->dirty = true;      // to allow setting dirty to false!!
				$this->data[$name] = $value;
			}
		} else {
			self::error("SET Reference to an undefined property [$name] in Class : " . static::class );
		}
		return $this;
	}

	public function __get($name)
	{
		if ($this->isProperty($name)) {
			return $this->data[$name];
		} else {
			self::error("GET Reference to an undefined property [$name] in Class : " . static::class);
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

		$this->em->model->save($this);
	}

	/*
	 * using the id
	 */
	public function saveOrAddKey($key)
	{
//		public static function findAddKey($table, $id , $keyField , $data)
//		Core\Db::findKeyOrAdd(self::)
		$this->em->model->saveKeyOrAdd($key, $this);
		return $this;
	}

	/*
	 * this should find ALL values in a QBE manner
	 * again working, with unoque
	 */
	public static function findUniqueBy($field, $value) {
		$em = EM::getEntityManager(static::class);
		return $em->model->findUniqueBy($field,$value);

	}


}

