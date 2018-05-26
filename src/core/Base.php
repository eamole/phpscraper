<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:50
 */

namespace Core;

class Base {
    use _Base;
    public static $className=__CLASS__;

}

// allow classes to hold their own member as static
trait _List {
    public static $members=[];

    public function add($member,$name=null) {
        $key = null;
        if(is_null($name)) {
            if(isset($member->name)) {
                $key = $member->name;
            }
        } else {
            $key = $name;
        }
        if(isset(self::$members[$key])) {
            self::warning("resetting the member value with key [$key] ");
        }
        self::$members[$key] = $member;

    }

}


trait _Base {


    public static function error($msg,$args=[]) {
//        new WP_Error('code',$msg,$args);
        self::out("Error" , "red" , $msg , $args);
    }
    public static function warning($msg,$args=[]) {
        self::out("Warning" , "orange" , $msg , $args);
    }
    public static function debug($msg,$args=[]) {
        self::out("Debug" , "green" , $msg , $args);
    }
    public static function dump($var) {
        echo("\n<pre>");
        var_dump($var);
        echo "</pre>";
    }
    public static function out($mode,$color,$msg,$args) {
        echo("\n<p style='color:$color;'>$mode : ".self::$className." | $msg </p>");
    }

}