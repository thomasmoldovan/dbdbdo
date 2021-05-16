<?php namespace App\Models;

use CodeIgniter\Model;

class SchemaModel extends Model {

	public $database = "dbdbdo";

    public function index($slug = false) {
        $tables = $this->getTables();
        return json_encode($tables);
    }

	// Get all tables from database
	// NOTE: This will also include the VIEWS
    public function getTables($database = null) {
		if (is_null($database)) return false;
		if ($database[0] != "_") $database = "_".$database;
		$infosch = \Config\Database::connect("informationSchema");
		$result = $infosch->query("SELECT * FROM tables WHERE table_schema = '{$database}' ORDER BY table_name");
		return $result->getResultArray();
    }

	// Get all the columns for a table
    public function getColumns($database = null, $table = null, $info = "*") {
		if (is_null($database) || is_null($table)) return false;
		if ($database[0] != "_") $database = "_".$database;
		$infosch = \Config\Database::connect("informationSchema");
		if (is_array($info)) $info = implode(", ", $info);
		$result = $infosch->query("SELECT {$info} FROM columns WHERE table_name = '{$table}' AND table_schema = '{$database}'");
		return $result->getResultArray();
	}

	// Get the number of rows for a table
	public function getRows($table = null) {
		if (is_null($table)) return false;
		$infosch = \Config\Database::connect("informationSchema");
		$result = $infosch->query("SELECT count(*) AS rows FROM {$table}");
		return $result->getResultArray();
	}

	// Get the info we have saved on the user tables
	public function getTablesInfo($user = null, $project_id = null) {
		if (is_null($user) || is_null($project_id)) return false;
		$dbConn = \Config\Database::connect("default");
		$result = $dbConn->table("user_tables")->select("*")
						 ->where(["user_id" => $user, 
							 	  "project_id" => $project_id]);
		return $result->get()->getResultArray();
	}

	// Executes a custom query on a database connection
	public function executeOuterQuery($database = null, $query = null, $returnType = "array") {
		if (is_null($database) || is_null($query)) return false;
		if ($database[0] != "_") $database = "_".$database;

		// $dbConn = \Config\Database::connect($database);
		$this->setDatabase($database);

		$result = $this->query($query);
		if ($returnType == "array") {
			return $result->getResultArray();
		} else {
			return $result->getResult();
		}
	}

	// Executes a custom query on a database name
	public function executeInnerQuery($database = null, $query = null, $returnType = "array") {
		if (is_null($database) || is_null($query)) return false;
		// $dbConn = \Config\Database::connect("userWorker");
		$this->setDatabase($database);
		$result = $this->query($query);

		if ($returnType == "array") {
			return $result->getResultArray();
		} else {
			return $result->getResult();
		}
	}
}
