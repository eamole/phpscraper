<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;

class Db extends \PDO{
    use _Base;

    public static $className=__CLASS__;
    public static $db;

    public $dbHost;
    public $dbName;
    public $userName;
    public $password;
    public $tablePrefix;

    public static function connectToWP($pathToWP){

        require($pathToWP. "wp-config.php");

        $hostName = DB_HOST;
        $dbName = DB_NAME;
        $userName = DB_USER;
        $password = DB_PASSWORD;

        $db = new Db($hostName,$dbName,$userName,$password);
        $db->tablePrefix = $table_prefix;   // comes from WP

        self::$db = $db;
        return $db;

    }

    public function __construct($hostName,$dbName,$userName,$password,$driverOptions=[]) {

        $this->hostName = DB_HOST;
        $this->dbName = DB_NAME;
        $this->userName = DB_USER;
        $this->password = DB_PASSWORD;

        parent::__construct("mysql:host=" . $hostName . ";dbname=" . $dbName, $userName, $password, $driverOptions);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array(DbStatement::class, array($this)));

    }
    /*
     * work directly with Entity - really this should be out in the Model
     */
    public function find($className,$id) {

    }
    public static function findAddKey($table, $id , $keyField , $data)
    {
        $value = $data[$keyField];
        $rows = self::$db->select($table, $keyField, $value );
        if (!$rows){    // $data->count() == 0
            // insert required, get the id
            $data = self::$db->insert($table,$id,$data);

        } else {

            if (count($rows) > 1) {
                // error keyField should be unique
                self::error("keyfield [$keyField] value [ $value ] should be unique in table [ $table ]");
            } else {
                $row = $rows[0];
                // compare the two - row & data - before saving!!
                // update record with data
                $data = self::$db->updateKey($table,$id,$keyField,$data);  // hack - needs to get id col from Model pr from singular $table
            }
        }
        return $data;
    }
    public static function findBy($table,$field,$data) {

        $stmt = self::$pdo->prepare("SELECT * FROM $table WHERE $field=:value");
        $stmt->execute(['value' => $value]);
        $data = $stmt->fetch();
        return $data;
    }
    public function select($table,$field,$value) {

        $table=$this->tablePrefix.$table;   // !!!!

        $stmt = self::$db->prepare("SELECT * FROM $table WHERE $field=:value");
        $stmt->execute(['value' => $value]);
        $data = $stmt->fetchAll();
        return $data;

    }
    /*
     * assumes all fields exist and only one data record (I think);
     */
    public function insert($table,$id,$data) {

        $table=$this->tablePrefix.$table;   // !!!!

        $fields = "";
        $values = "";
        foreach ($data as $key => $value ) {
            $fields .= ((strlen($fields)>0)? "," : "") . $key;
            $values .= ((strlen($values)>0)? "," : "") . ":" . $key; // placeholders
        }

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";

        $stmt = self::$db->prepare($sql);
        $stmt->execute($data);
        $data[$id] = \PDO::lastInsertId(); // TODO - problem with knwing the field name of the id
        return $data;

    }

    public function updateKey($table,$id,$keyField,$data) {

        $table=$this->tablePrefix.$table;   // !!!!


        $assigns = "";
        $values = "";
        foreach ($data as $key => $value ) {
//            if($key !== $keyField ){  // don't update the key
//            }
            $assigns .= ((strlen($assigns)>0)? "," : "") . " $key = :$key ";
        }
        // trying to get the id back from the updated row
        $sql = "SET @id :=0 ; UPDATE $table SET $assigns , $id = (SELECT @id := $id)  WHERE $keyField = :$keyField ; SELECT @id ;";
        // last_insert_id can take a parameter, which it will return the next time its called
        $sql = "UPDATE $table SET $assigns , $id = LAST_INSERT_ID($id)  WHERE $keyField = :$keyField ; ";

        $stmt = self::$db->prepare($sql);
        $rows = $stmt->execute($data);
        $data[$id] = \PDO::lastInsertId(); // TODO - problem with knwing the field name of the id
        return $data;

    }

}

class DbStatement extends \PDOStatement {
    public $db;
    protected function __construct($db) {
        $this->db = $db;
    }
}