<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 12:08
 */

namespace Core\Platform\Php\DataType;
/*
 * this class is responsible for handling primitive data types
 * one of these objects will be created for each property in entity
 * subclasses of this could be created for different behaviour
 */

use ORM\Platform\Php\PropertyDef;
/*
 * this should possibly be a Php Lang data type
 */
class Scalar extends PropertyDef
{

}