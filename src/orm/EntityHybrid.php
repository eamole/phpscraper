<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace ORM;

use Core;

class EntityHybrid extends Core\Base{
//    use _Base;

    public static $entity;
    public static $table;
    public static $id;

    public static $entities=[]; // each Entity class

    public static $_init=false;
    // orm mapping
    public static $fields=[];
    public static $modelClass;
    public static $model;  // each Entity has a static model
//    public $_fields;    // a reference - not required I think dbg shows statics


    public static $data;    // holds array of entity - maybe not auto!! - really needs the id

    public $dirty=false; // only save if a value changed/set

    public static function init($modelClass,$fields=[],$args=[]) {

        if(!self::$_init) {
            self::$modelClass=$modelClass;  // to create objects from queries
            if($args) {
                self::$table = $args['table'];
                self::$entity = $args['entity'];
            }
            // strip off namespaces
//            self::$model = new $modelClass();
            // every entity will require
            self::$id = self::$entity . "_id" ;
            self::addField(self::$table . "_id" ,
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
    public function id($id=null) {
        $name = self::$id;
        if($id) {
            $this->$name = $id;
        }
        return $this->$name ;
    }
    public static function addField($name,$type="string",$len=255,$args=null) {
        if(isset(self::$props[$name])){
            self::error("field name [$name] being redefined!");
        }
        $field=['name'=>$name,'type'=>$type,'len'=>$len];
        foreach ($args as $key=>$arg) {#
            $field[$key]=$arg;
        }
        self::$props[$name]=$field;
    }


    public function __construct($name) {
//		 parent::__construct();
        self::$entities[$name] = $this;
//        $this->_fields = &self::$fields;
    }
    public function __set($name, $value) {
        if( isset(self::$props[$name])) {
            if($this->$name !== $value) {
                $this->$name = $value;
                $this->dirty = true;
            }
        } else {
            self::error("SET Reference to an undefined property [$name] in Class : ".self::$className);
        }
        return $this;
    }

    public function __get($name) {
        if( isset(self::$props[$name])) {
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

    /*
     * work with the DB
     */



    /*
     * using the id
     */
    public function findOrAdd() {
        if(!isset($this->id)) {

        }
    }
    /*
     * this should find ALL values in a QBE manner
     */
    public function findBy($field) {

    }



}