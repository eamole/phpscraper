<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 13:32
 */

namespace ORM\Platform\Php\DataTypes;

/*
 * this is a class to represent classes
 * in Php classes are represented as namespace/class strings
 * std behaviours should include creating an object
 * this is only possible if no params
 *	this should possobly be a Php data type - not ORM
 */
use Core;
class ClassObject extends Core\Base
{

}