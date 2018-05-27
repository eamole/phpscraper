<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 01:02
 */

namespace Core;
use Core;
/*
 * Cannot be abstract
 */
class Db extends \PDO
{
	use _Base;

	public static $className = __CLASS__;
	public static $db;

	public $dbHost;
	public $dbName;
	public $userName;
	public $password;
	public $tablePrefix;

	// need to override for an app connection
	public static function init(){
		self::warning("This method should never be called directly - should calling the App version");
	}

	public static function connectToWP($pathToWP)
	{

		require($pathToWP . "wp-config.php");

		$hostName = DB_HOST;
		$dbName = DB_NAME;
		$userName = DB_USER;
		$password = DB_PASSWORD;

		$db = new Core\Db($hostName, $dbName, $userName, $password);
		$db->tablePrefix = $table_prefix;   // comes from WP

		self::$db = $db;
		return $db;

	}

	public static function getDb()
	{
		// this does not work
//		static::init(); // calling the wrong one
		return self::$db;
	}

	public function __construct($hostName, $dbName, $userName, $password, $driverOptions = [])
	{

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
	public function find($className, $id)
	{

	}

	public function findKeyOrAdd($table, $id, $keyField, $data)
	{
		$table = $this->tablePrefix . $table;   // !!!!
		//
		//		// avoid truncated values when treating strings like arrays!
		if(is_array($data)) {
			$value = $data[$keyField];
		} else{
			$value = $data;
		}
		$rows = $this->select($table, $keyField, $value);
		if (!$rows) {    // $data->count() == 0
			// insert required, get the id
			$data = $this->insert($table, $id, $data);

		} else {

			if (count($rows) > 1) {
				// error keyField should be unique
				self::error("keyfield [$keyField] value [ $value ] should be unique in table [ $table ]");
			} else {
				$row = $rows[0];
				// compare the two - row & data - before saving!!
				// update record with data
				$data = $this->updateKey($table, $id, $keyField, $data);  // hack - needs to get id col from Model pr from singular $table
			}
		}
		return $data;
	}

	public function findBy($table, $field, $data)
	{
		$table = $this->tablePrefix . $table;   // !!!!

		if(is_array($data)) {
			$value = $data[$field];
		} else{
			$value = $data;
		}

		$stmt = $this->prepare("SELECT * FROM $table WHERE $field=:value");
		$stmt->execute(['value' => $value]);
		$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $data;
	}

	public function select($table, $field, $value)
	{

		$table = $this->tablePrefix . $table;   // !!!!

		$stmt = $this->prepare("SELECT * FROM $table WHERE $field=:value");
		$stmt->execute(['value' => $value]);
		$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $data;

	}

	/*
	 * assumes all fields exist and only one data record (I think);
	 */
	public function insert($table, $idField, $data)
	{

		$table = $this->tablePrefix . $table;   // !!!!

		$fields = "";
		$values = "";
		foreach ($data as $key => $value) {
			$fields .= ((strlen($fields) > 0) ? "," : "") . $key;
			$values .= ((strlen($values) > 0) ? "," : "") . ":" . $key; // placeholders
		}

		$sql = "INSERT INTO $table ($fields) VALUES ($values)";

		$stmt = $this->prepare($sql);
		$stmt->execute($data);
		$data[$idField] = \PDO::lastInsertId(); // TODO - problem with knwing the field name of the id
		return $data;

	}


	/**
	 * @param $table
	 * @param $idField
	 * @param $keyField
	 * @param $data
	 * @return mixed
	 */
	public function updateKey($table, $idField, $keyField, $data)
	{

		$table = $this->tablePrefix . $table;   // !!!!

		$assigns = "";
		$values = "";
		foreach ($data as $key => $value) {
			if($key !== $idField ){  // don't update the key
				$assigns .= ((strlen($assigns) > 0) ? "," : "") . " $key = :$key ";
			}

		}
		// trying to get the id back from the updated row
		// $sql = "SET @id :=0 ; UPDATE $table SET $assigns , $idField = (SELECT @id := $idField)  WHERE $keyField = :$keyField ; SELECT @id ;";
		$sql = "UPDATE $table SET $assigns WHERE $keyField = :$keyField ; ";
		// last_insert_id can take a parameter, which it will return the next time its called
		// not sure why I would update the id
		//		$sql = "UPDATE $table SET $assigns , $idField = LAST_INSERT_ID($id)  WHERE $keyField = :$keyField ; ";

		$stmt = $this->prepare($sql);
		$rows = $stmt->execute($data);
//		$data[$id] = \PDO::lastInsertId(); // TODO - problem with knwing the field name of the id
		return $data;

	}

}

class DbStatement extends \PDOStatement
{
	public $db;

	protected function __construct($db)
	{
		$this->db = $db;
	}
}