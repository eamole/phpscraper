<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;

class Db extends PDO{
    use _Base;

    public static $className=__CLASS__;
    public static $db;

    public $dbHost;
    public $dbName;
    public $userName;
    public $password;

    public static function fromWP($pathToWP){

        require($pathToWP. "/wp-config.php");

        $hostName = DB_HOST;
        $dbName = DB_NAME;
        $userName = DB_USER;
        $password = DB_PASSWORD;

        $db = new Db($hostName,$dbName,$userName,$password);

        self::$db = $db;
        return $db;

    }

    public function __construct($hostName,$dbName,$userName,$password,$driverOptions=[]) {

        $this->hostName = DB_HOST;
        $this->dbName = DB_NAME;
        $this->userName = DB_USER;
        $this->password = DB_PASSWORD;

        parent::__construct("mysql:host=" . $hostName . ";dbname=" . $dbName, $userName, $password, $driverOptions);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('QueryStatement', array($this)));

    }

}