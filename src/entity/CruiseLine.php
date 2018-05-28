<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 27/05/2018
 * Time: 15:42
 */

/*
 * I am having problems with needing the Model and EntityManager before an Entity is created
 * so I can use the Entity constructor
 * I need code to run to register the EntityClass along with Model stuff etc
 * Can I use exec code at head of class file?
 * The only variant part for the EM thus far is the Model, and that can be deduced and
 * overriden later
 * was going to use a trait but not customisable, and if invariant, do some other way
 * I could use the EM to call a method!! ie a std Init method
 * Lets start with deduce Model class
 */
namespace Entity;
use Core;
use Model;

class CruiseLine extends Core\Entity{

	/*
	 * can I provide em overrides here or somewhere in exec code?
	 */
//	public $name,$logo;	// to avoid warnings when accessing them - using data array now


	/**
	 * CruiseLine constructor.
	 * @param $entityClass
	 * @param $modelClass
	 * @param array $props
	 * @param array $args
	 */
	public function __construct(){
		parent::__construct();

	}


}