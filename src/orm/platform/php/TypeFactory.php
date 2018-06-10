<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 12:19
 */

namespace ORM\Platform\Php;
/*
 * we use this to select which type to implement
 * this is of primary interest to
 * ORM mappings and MVC form builders
 * in an ORM situation, we may wel build a data dictionary, so this
 * system needs to take and store inputs used by external components
 */
/*
 * this is a wrapper for the Php primitive/sc alar types
 * 	string,
 *  	float/double,
 * 	integer
 * 		(combine both as number)
 * 	boolean
 * There are additional types which are stored as scalars
 * date!!
 *
 * The other data types are
 * compound
 * 	array
 * 	object
 * 	callable/callback
 * 	iterable
 *special
 * 	resource
 * 	null : a var with the value NULL, unset() or unassigned.
 * additional pseudo "types" used for type hinting include
 * 	mixed
 * 	number
 * 	callback
 * 	void
 * 	callback (5.3) / callable (5.4+)
 *
 *	Bear in mind that we may have to map these types to a stricter system
 * all of these objects will be attached to entities!!
 * or more specifically, EntityManagers
 * Might have to move the namesapce around to map it into an ORM / Entity namespace
 * rather than Core
 */
use Core;

class TypeFactory extends Core\Base
{
	// a list of data type mappings to handlers
	public static $datatypes = [];
	public static $ns = __NAMESPACE__;

	public function __construct()
	{
		parent::__construct();

	}
	/*
	 * a handler class
	 * a list of type strings
	 * expand list
	 */
	public function addHandlers($handlerClass , ...$types) {

	}
	public static function addHandler($type,$handler) {
		$oldType=null;
		if(isType($type)) {
			self::warning("overriding handler for Php datatype [$type]");
			$oldType = self::$datatypes[$type];
		}

		$class = self::$ns.$handler;
		self::$datatypes[$type] = new $class();
	}

	public static function isType($type)
	{
		return isset(self::$datatypes[$type]);
	}
}


