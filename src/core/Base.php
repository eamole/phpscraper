<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:50
 */

namespace Core;

//class Base {
//    use _Base;
//    public static $className=__CLASS__;		// this will always be Core\Base - its this class!!
//	// delete this at some stage
//
//}

// allow classes to hold their own members as static
trait _List {
    public static $members=[];

    public static function add($member,$name=null) {
        $key = null;
        if(is_null($name)) {
            if(isset($member->name)) {
                $key = $member->name;
            }
        } else {
            $key = $name;
        }
        $key=strtolower($key);
        if(isset(self::$members[$key])) {
            self::warning("resetting the member value with key [$key] ");
        }
        self::$members[$key] = $member;

    }

    public static function get($name) {
        $key=strtolower($name);
        if(!isset(self::$members[$key])) {
            self::error("cannot retrieve Member using key [$key]");
        }
        return self::$members[$key];


        }

}


//trait _Base{
class Base{

	public static function getClassName() {
		return static::class;
	}

// this causes problems with redefinitions!!
//    public static $className=__CLASS__;

    public static function error($msg,...$args) {
//        new WP_Error('code',$msg,$args);
        self::out("Error" , "red" , $msg , $args);
    }
    public static function warning($msg,...$args) {
        self::out("Warning" , "orange" , $msg , $args);
    }
    public static function debug($msg,...$args) {
        self::out("Debug" , "green" , $msg , $args);
    }
    public static function out($mode,$color,$msg,$args=[]) {
        foreach ($args as $ord=>$arg) {
            $msg = str_replace("%$ord",$arg,$msg);
        }
        echo("\n<p style='color:$color;'>$mode : ".static::class." | $msg </p>");
    }
    public static function dump($var) {
        echo("\n<pre>");
        var_dump($var);
        echo "</pre>";
    }

}