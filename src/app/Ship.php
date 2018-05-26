<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:03
 */


namespace App;

use Core;

class Ship extends Core\Entity {

    public function __construct() {
        parent::__construct("ship");
        self::init(Ships::$className,[]);
    }


}