<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;


class Entity extends Base{
    use _Base;

    public static $entities=[]; // each Entity class

    public static $_init=false;
    // orm mapping
    public static $fields=[];
    public static $modelClass;
    public static $model;  // each Entity has a static model
    public $_fields;    // a reference

    public static function init($modelClass,$fields=[]) {

        if(!self::$_init) {
            self::$modelClass=$modelClass;

//            self::$model = new $modelClass();
            // every entity will require
            self::addField("id" ,
                "integer" ,
                null ,
                [
                    'autoincrement' => "true" ,
                    'not null'=>true,
                    "primary key"=>true]);
            foreach ($fields as $field) {
                self::debug("adding field ",$field['name']);
                self::addField($field['name'],$field['type'],$field['len'],$field);
            }
            self::$_init=true;
        }

    }

    public static function addField($name,$type="string",$len=255,$args=null) {
        if(isset(self::$fields[$name])){
            self::error("field name [$name] being redefined!");
        }
        $field=['name'=>$name,'type'=>$type,'len'=>$len];
        foreach ($args as $key=>$arg) {#
            $field[$key]=$arg;
        }
        self::$fields[$name]=$field;
    }


    public function __construct($name) {
        self::$entities[$name] = $this;
//        $this->_fields = &self::$fields;
    }
    public function __set($name, $value) {
        if( isset(self::$fields[$name])) {
            $this->$name = $value;
        } else {
            self::error("SET Reference to an undefined property [$name] in Class : ".self::$className);
        }
        return $this;
    }

    public function __get($name) {
        if( isset(self::$fields[$name])) {
            return $this->$name;
        } else {
            self::error("GET Reference to an undefined property [$name] in Class : ".self::$className);
        }
    }

    public function __call($name, $args){
        $method=[$this,$name];
        if(!is_callable($method)) {
            self::error("method not callable [$name]");
            return;
        }
        $val = call_user_func_array($method,$args);
        return $val;
    }
}