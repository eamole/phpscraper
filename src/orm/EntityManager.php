<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 21:34
 */

namespace ORM;
use Core;
use Core\Util;
use ORM;
use ORM\EntityManagerManager as EMM;
use ORM\Platform\Php\PropertyDef;
/*
 * This should probably be split into two classes
 * EntityDef which defines the Entity and Entitymanager to manage and map them
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
class EntityManager extends Core\ClassStatic {

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
//	public $idName;  			// the primaryKey from Model
	public $unique = [];   // fields which must be unique - can be used to verify/match

	public $props = [];

	public static function registerEntityClass($entityClass, $modelClass = null, $props = [], $args = []) {
		if(!EMM::isRegisteredEntityClass($entityClass)) {
			/*
					Model namespace + plural of entity
			*/
			if(!$modelClass) {
				// not supplied - guess
				// TODO need to auto map these paths!!
				$_modelClass = "App\\ORM\\Models\\" . Core\Util::getPlural(Core\Util::baseName($entityClass));
				if(class_exists($_modelClass)) $modelClass=$_modelClass;
			}
			$em = new EntityManager($entityClass, $modelClass, $props, $args);
			EMM::registerEntityManager($em);
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

	/**#
	 * EntityManager constructor.
	 * @param $entityClass - the name of an entity class 	- can't use Entity
	 * @param $modelClass	- the name of a m model class - can't use Model
	 * @param array $props
	 * @param array $args
	 *
	 */
	public function __construct($entityClass, $modelClass, $props = [], $args = []) {
		parent::__construct();
		$this->entityClass = $entityClass;
		$this->entityName = self::entityName();

		// ORM
		$this->modelClass = $modelClass;  // to create objects from queries
		$this->model = Model::getModel($modelClass);//new $modelClass;

		/*
		 * I  should interrogate the model for the default fields
		 * and mappings
		 */
		if (is_string($props)) $props = explode(",", $props);
		/*
		 * this is using basic Entity struff (ie not ORM)
		 */
		foreach ($props as $prop) {
			$this->props[$prop] = [];
		}

		if ($args) {
		}

		/*
		 * the mode should now update the entity
		 */
		self::updateEntityFromModel();

		// now see if there is a a static init() method on the entity
		if(method_exists($entityClass,"init")) {
			call_user_func([$entityClass,"init"],$this);
		}

	}

	public function idName() {
		return $this->model->idField;	// TODO : needs to be translated from Model to Entity
	}
	// should really be getPropertyDef
	public function isProperty($name) : ?PropertyDef {	//  	- might be null/not defined
//		return isset($this->props[$name]);
		return isset($this->props[$name]) ? $this->props[$name] : null ;
	}
	/*
	 * this retruns the entity name (how it is referred to in specs) from the actual class name
	 */
	public function entityName($className = null) : string
	{
		$name = $className ?? $this->entityClass;
		return Util::baseName($name);
	}
	/*
	 * THIS is the ORM bit. This links the Entity to the underlying ORM system
	 */
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
			self::ormAddProp($name,$field);
		}

		foreach ($model->fks as $fk) {
			// each foreign key needs to create two entries in the Entity - the id and the object

			// fk already defined as a property!
			// self::addProp($fk->name);   // TODO : transform names into entity style names not underscore
			//	$name = strtolower($fk->model->entityName);	// need a netter strategy than this - there may ne multiple fk links!!
//			self::addProp($name);   // should add cruiseline to ship
			self::addFk($fk);   // should add cruiseline to ship
		}
		// add vlinks as methods? eg ships() for cruiseline
	}

	public function addProps($props) {
		if(is_string(($props))){
			$props=explode(",",$props);
		}
		foreach ($props as $prop) {
			$this->addProp($prop);
		}
	}
	/**
	 * @param $name
	 * @param null $modelName
	 * @param null $args
	 */
	/*
	 * modelName is bad name
	 * it should be mapped name it mapping the property to the model
	 * TODO : do not use - must use propertyDefs
	 */
	public function addProp($name, $fieldName = null, $args = [])
	{
		if (isset($this->props[$name])) {
			self::error("property name [$name] being redefined!");
		}
		$prop = new ORM\Platform\Php\PropertyDef($this, $name, $args);

		$this->props[$name] = $prop;
		return $prop;

		/*
				$prop = [
						  'name' => $name,
						  'fieldName' => $fieldName ?? $name,
						  'args' => $args
				];

		foreach ($args as $key => $arg) {#
			$prop[$key] = $arg;
		}
				$this->props[$name] = (object) $prop;*/

	}

	public function addFk($fk) {
		$name = strtolower($fk->model->entityName);	// need a netter strategy than this - there may ne multiple fk links!!
		if (isset($this->props[$name])) {
			self::error("property name [$name] being redefined on Entity !",$this->entityClass);
		}

		$prop = new ORM\Platform\Php\ForeignKey($this,$name,$fk);

		$propName=$name;

		$this->props[$propName] = $prop;

	}
	/*
	 * This is an extended version of addProp - It should be overriden by the ORM\Entity vs the Core\Entity!!
	 */

	public function ormAddProp($name, $field , $args = [])
	{
		if (isset($this->props[$name])) {
			self::error("property name [$name] being redefined on Entity !",$this->entityClass);
		}
		/*
		 * create ORM properties!! Don't use the factory or anything yet - call directly
		 */
		/*
		 * need a field to propery mapping
		 */
		// there may already be a mapping in olace // TODO : process args including any manual field->property mappings

		//		$propName=$this->mapFieldName($name);
		// TODO worry about mapping names anon
		// if a name s mapped, hard to map it back!!
		// need a straight 2way coupling between prop and field where the names are mapped to each other or coupled objects
		$propName=$name;

		$field->propName = $propName;
//		$field['propName']=$propName;
		// add args to field def
//		foreach ($args as $key => $arg) {#
//			$field[$key] = $arg;
//		}
		$field->args=$args;
		/*
		 * we may do a type selector from the factory class if we read data from db or manually define
		 */
		$prop = new PropertyDef($this,$propName,$field);

		$this->props[$propName] = $prop;
	}
	/*
	 * apply standard transformstion between field and property names
	 * a) camel case
	 * b) remove underscores, us as explode - recombine with upercase first letters
	 */
	public function mapFieldName($name) {
		$parts = explode("_",$name);
		$propName='';
		foreach ($parts as $part) {
			$propName .= (strlen($propName)>0) ? ucfirst($part) : $part;
		}
		return $propName;
	}

	public function add($entity){
		if($entity->id()) {
			$this->index[$entity->id()]=$entity;
		} else {
			$this->entities[] = $entity;
		}
	}

}