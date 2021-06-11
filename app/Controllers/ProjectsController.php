<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;

use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ProjectsController extends HomeController {

	protected $current_project;

	private $mapping = array(
        "table_name" => "TABLE_NAME",
        "column_name" => "COLUMN_NAME",
        "type" => "COLUMN_TYPE",
        "pk" => "COLUMN_KEY",
        "default" => "COLUMN_DEFAULT",
        "null" => "IS_NULLABLE",
        "ai" => "EXTRA",
        "permissions" => "PRIVILEGES",
        "comment" => "COLUMN_COMMENT"
    );

	public function index($hash = null)	{
		// Should be moved in Home
		if ($this->checkIfLogged() !== true) {
			return redirect()->to("/");;
		}
		$projects = new \App\Models\ProjectModel();

		// Check if we have a hash in the URL
		if (isset($hash) && !is_null($hash)) {
			// If hash belongs to logged user, we should have a project
			$project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $hash])->getResultArray();

			if (!empty($project)) {
				// Project found
				$this->current_project = $project[0];

				// Tables List View
				$schema = new SchemaModel();
				$data["project"] = $this->current_project;
				$data["tables"] = $schema->getTables($this->current_project["project_hash"]);
				$data["userTables"] = $schema->getTablesInfo($this->user->id, $this->current_project["id"]);
				$data["tablesProcessed"] = array_unique(array_column($data["userTables"], "table_name"));

				$this->session->set("notification", $this->notifications);
				return $this->display_main("header", "project", $data);
			} else {
				// No project with user_id and project_hash found

				// $this->auth->logout();
				if (ENVIRONMENT === "Development") $extra_info = __FILE__." ".(__LINE__ + 1)." ".__FUNCTION__;
				$this->notifications[] = ["error", "Project not found ".(isset($extra_info) ? $extra_info : "").""];
				$this->session->set("notification", $this->notifications);
				return redirect()->to("/");
			}
		}

		// No hash in the URL
		// Display the projects page
		$project_list = $projects->getProjectsForUser($this->user->id);			

		$data = ["project_list" => $project_list];
		$this->session->set("notification", $this->notifications);
		return $this->display_main("header", "projects", $data);

		// Not logged - Redirect to root
		return redirect()->to("/");
	}

	public function create() {
		if ($this->auth->check()) {
			$this->session->set("notification", $this->notifications);
			return $this->display_main("header", "create");
		}
		$this->notifications[] = ["info", "Hello from create 2 :)"];
		$this->session->set("notification", $this->notifications);
		return redirect()->to("/");
	}

	public function getModulesInfo() {
		$rootConn = \Config\Database::connect("default");
		$result = $rootConn->query("SELECT  user_tables.project_id,
											projects.project_name,
											COUNT(DISTINCT tables_modules.user_module_id) AS modules_in_project
									FROM user_modules user_modules
										CROSS JOIN users users
										CROSS JOIN
										(user_tables user_tables
										INNER JOIN tables_modules tables_modules
											ON (user_tables.id = tables_modules.user_table_id))
										INNER JOIN projects projects
											ON (projects.project_hash = ".(int)$this->current_project["project_hash"].")");
		return $result->getResultArray();
	}

	// Creates a DB with the provided schema
	public function importSchema($data = null, $name = null) {
		$name = $this->request->getPost("name");
		$description = $this->request->getPost("description");
		$type = $this->request->getPost("type");
		$data = $this->request->getPost("data");

		if (empty($name) || empty($data)) {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something values were empty"
			));
		}

		$projects = new \App\Models\ProjectModel();
		$rootConn = \Config\Database::connect("default");

		try {
			$importerConn = \Config\Database::connect("schemaImporter");
		} catch (\Exception $ex) {
			return $this->tried($ex);
		}

		$userId = $this->user->id;
		$project_hash = substr(uniqid(), -6);

		// Add the project
		try {
			$this->current_project = $projects->insert([
				"id" => null,
				"user_id" => $userId,
				"project_hash" => $project_hash,
				"project_name" => $name,
				"project_description" => $description,
				"project_type" => $type,
				"database" => $project_hash]
			);
		} catch (\Exception $ex) {
			$project_hash = null;
			return $this->tried($ex);
		}

		$this->current_project = $projects->find($this->current_project);

		// EXTERNAL - project_type - 1
		if ($this->current_project->project_type == 0) {
			// Create sepparate database
			$result = null;
			$sql = "CREATE DATABASE {$this->current_project->project_hash};";
			try {
				$result = $importerConn->query($sql);
			} catch (\Exception $ex)  {
				return $this->tried($ex);
			}

			// Prepare for import
			$importerConn->setDatabase($project_hash);
			$importerConn->query("SET FOREIGN_KEY_CHECKS = 0;");
			$importerConn->query("SET SQL_MODE = '';");
		}

		// INTERNAL - project_type - 0 - this import will go to our database
		if ($this->current_project->project_type == 1) {
			$importerConn->setDatabase($_ENV["database.default.database"]);
			$importerConn->query("SET FOREIGN_KEY_CHECKS = 0;");
			$importerConn->query("SET SQL_MODE = '';");
		}		

		$separator = "\r\n";
		$nrTables = 0;
		$tempcommand = "";

		$line = strtok($data, $separator);
		while ($line !== false) {
			// Skip if comment
			if (substr($line, 0, 2) == '--' || substr($line, 0, 2) == '/*' || substr($line, 0, 2) == '/*' || $line == '') continue;

			// Add this line to the current command
			$tempcommand .= $line;

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($tempcommand), -1, 1) == ';') {

				// TODO: Turn these import filters into a loop
				// $restricted_importer_keywords = ["drop", "database", "show", "privilege"];
				// foreach ($restricted_importer_keywords as $keyword) {
				// 	if (stripos($tempcommand, $keyword) !== false) {
				// 		$tempcommand = '';
				// 		$line = strtok($separator);
				// 		continue;
				// 	}
				// }

				// Ignore line if DATABASE statement present
				$wtf1 = stripos($tempcommand, "drop");
				if (stripos($tempcommand, "database") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf2 = stripos($tempcommand, "database");
				if (stripos($tempcommand, "database") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf3 = stripos($tempcommand, "show");
				if (stripos($tempcommand, "show") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf4 = stripos($tempcommand, "privilege");
				if (stripos($tempcommand, "privilege") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Perform the query
				if (!empty($tempcommand)) {
					$nrTables++;
					try {
						// No INSERTS
						$result = $importerConn->query($tempcommand);
					} catch (\Exception $ex)  {
						if ($ex->getCode() == 1142) {
							return $this->response->setJSON(array(
								"status" => "error",
								"code" => $ex->getCode(),
								"message" => "You do not have permissions to run that command"
							));
						} else if ($ex->getCode() == 1064) {
							return $this->response->setJSON(array(
								"status" => "error",
								"code" => $ex->getCode(),
								"message" => "You have an error in your SQL script",
							));
						} else {
							return $this->tried($ex);
						}
					}
				}

				$tempcommand = '';
			}

			$line = strtok($separator);
		}

		if (is_null($result) || $nrTables == 0) {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong executing the SQL script"
			));
		}

		if (!is_null($result) && $nrTables > 0) {
			$schema = new SchemaModel();
        	$tables = $schema->getTables($this->current_project->project_hash);

			foreach ($tables as $table) {
				$this->getTableColumns($table["TABLE_NAME"], $this->current_project->project_hash);
			}

			return $this->response->setJSON([
				"project_hash" => $project_hash
			]);
		} else {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong executing the SQL script"
			));
		}
	}

	public function clearStuff() {
		if (is_null($this->user->id)) return false;
		$rootConn = \Config\Database::connect("default");

		// DROP all the user databases
		$dbs = $rootConn->query("SELECT project_hash FROM projects;")->getResult();
		if (empty($dbs)) {
			$this->notifications[] = ["info", "No projects to delete"];
		}

		$drop_db_query = "";
		if (is_array($dbs)) {			
			foreach ($dbs as $db) {
				$drop_db_query = "DROP DATABASE _".$db->project_hash.";";
				try {
					$dbs = $rootConn->query($drop_db_query);
				} catch (\Exception $ex) {
					$this->notifications[] = ["error", __FILE__." - Line:".__LINE__." - ".__FUNCTION__, $ex->getCode().":".$ex->getMessage()];
					$this->session->set("notification", $this->notifications);
				}
			}
		}

		$result = $rootConn->query("DELETE FROM user_tables WHERE project_id > 1");
		$result = $rootConn->query("TRUNCATE user_modules");
		$result = $rootConn->query("DELETE FROM projects WHERE id > 1");
		$result = $rootConn->query("TRUNCATE tables_modules");
		$result = $rootConn->query("TRUNCATE properties");
		$result = $rootConn->query("TRUNCATE links");

		// TODO: Delete files also
		
		$this->session->set("notification", $this->notifications); // Should run before and after every controller exit

		return redirect()->to('/projects');
	}

}
