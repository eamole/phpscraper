<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 28/05/2018
 * Time: 02:05
 */

namespace Core;

/*
 * this is a class that any class can use
 * in place of using statics and passing Classes around as strings
 * could  have instance tracking and object factories
 * could extend this to include ORM features - ie mapping to Models
 * I'll quickly include it here, but tag it for exclusion/migration
 */

class ClassStatic extends Base {
//	use _Base;

	public $className;
	public $namespace;
	public $trackInstances=false;
	public $instances=[];

}