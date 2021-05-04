<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;
use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class Projects extends Home {

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


		if ($this->auth->check()) {
			$projects = new \App\Models\ProjectModel();

			// Check if we have a hash in the URL
			if (!is_null($hash)) {
				/******** PROJECT VIEW ********/

				// If hash belongs to logged user, we should have a project
				$project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $hash])->getResultArray();

				if (!empty($project)) {
					// Project found
					$this->current_project = $project[0];
				} else {
					// No project with user_id and project_hash found
					// $this->auth->logout();
					if (ENVIRONMENT === "Development") $extra_info = __FILE__." ".(__LINE__ + 1)." ".__FUNCTION__;
					$this->notifications[] = ["error", "Project not found ".(isset($extra_info) ? $extra_info : "").""];
					$this->session->set("notification", $this->notifications);
					return redirect()->to("/");
				}

				if ($this->current_project) {
					// Project List View
					$schema = new SchemaModel();
					$data["project"] = $this->current_project;
					$data["tables"] = $schema->getTables($this->current_project["project_hash"]);
					$data["userTables"] = $schema->getTablesInfo($this->user->id, $this->current_project["id"]);
					$data["tablesProcessed"] = array_unique(array_column($data["userTables"], "table_name"));

					$this->session->set("notification", $this->notifications);
					return $this->display_main("header", "project", $data);
				} else {
					$this->notifications[] = ["error", "Don't do that"];
					$this->session->set("notification", $this->notifications);
					$this->auth->logout();
					return redirect()->to("/");
				}
			}

			// No hash - Return to projects page
			$project_list = $projects->getProjectsForUser($this->user->id);
			$data = ["project_list" => $project_list];
			$this->session->set("notification", $this->notifications);
			return $this->display_main("header", "projects", $data);
		}

		// Not logged - Redirect to root
		return redirect()->to("/");
	}

	public function create() {
		$this->notifications[] = ["info", "Hello from create :)"];
		if ($this->auth->check()) {
			$this->session->set("notification", $this->notifications);
			return $this->display_main("header", "create");
		}
		$this->notifications[] = ["info", "Hello from create 2 :)"];
		$this->session->set("notification", $this->notifications);
		return redirect()->to("/");
	}

	public function modules($project_hash = null) {
		if (!$this->auth->check()) {
			$this->notifications[] = ["info", "Your session has expired"];
			$this->session->set("notification", $this->notifications);
			return redirect()->to("/");
		}

		$schema = new SchemaModel();
		$projects = new ProjectModel();
		$modules = new UserModuleModel();
		if (!is_null($project_hash)) {
			$this->current_project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $project_hash])->getResultArray();
		}

		if (empty($this->current_project)) {
			// $this->auth->logout();
			$this->notifications[] = ["error" => "Project not found ".__FILE__." ".__LINE__." ".__FUNCTION__.""];
			$this->session->set("notification", $this->notifications);
			return redirect()->to("/");
		}

		$this->current_project = $this->current_project[0];

		$this->notifications[] = ["success", "Project ".$this->current_project["project_hash"]." loaded"];
		if ($this->auth->check()) {
			$this->session->set("project_hash", $this->current_project["project_hash"]);

			$moduleList = $modules->getModuleColumns();

			// Get's the settings for the link IF it has one
			foreach ($moduleList as $key => $value) {
				if ((int)$value["link_id"] > 0 && (int)$value["display"] > 0) {
					$moduleList[$key]["settings"] = $userTable->getTableInfoSettings($value["display"]);
				} else {
					$moduleList[$key]["settings"] = null;
				}
			}

			$moduleNames = array_unique(array_column($moduleList, "module_name"));
			$moduleData = [];

			// PREPARE DATA SO WE CAN USE IT IN A VIEW LIKE THE ONE IN THE TABLES VIEW
			foreach ($moduleList as $item) {
				// If this module is not in the array, we create that entry as an empty array
				if (empty($moduleData[$item["module_name"]])) {
					$moduleData[$item["module_name"]] = [];
				}
				// Now we push data to that array
				$moduleData[$item["module_name"]][] = $item;
			}

			if (isset($this->current_project["project_hash"]) && strlen(trim($this->current_project["project_hash"])) == 6) {
				$tables = $schema->getTables($this->current_project["project_hash"]);
			} else {
				return redirect()->to('/projects');
			}

			$userModules = $this->getModulesInfo();

			$data["project"] = $this->current_project;
			$data["userModules"] = $userModules;
			$data["moduleList"] = $moduleList;

			$data["modules"] = $moduleData;

			$this->session->set("notification", $this->notifications);
			return $this->display_main("header", "modules", $data);
		}
		$this->notifications[] = ["info", "Hello from modules 2 :)"];
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
		$projectName = $name;
		$project_hash = substr(uniqid(), -6);

		// Add the project
		try {
			$this->current_project = $projects->insert([
				"id" => null,
				"user_id" => $userId,
				"project_name" => $projectName,
				"project_hash" => $project_hash,
				"database" => $project_hash]
			);
		} catch (\Exception $ex) {
			$project_hash = null;
			return $this->tried($ex);
		}

		// Create database
		$result = null;
		$sql = "CREATE DATABASE `_".$project_hash."`;";
		try {
			$result = $importerConn->query($sql);
		} catch (\Exception $ex)  {
			return $this->tried($ex);
		}

		// Script import
		$result = null;
		$importerConn->setDatabase("_".$project_hash);
		$importerConn->query("SET FOREIGN_KEY_CHECKS = 0;");
		$importerConn->query("SET SQL_MODE = '';");

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

		$result = $rootConn->query("TRUNCATE user_tables");
		$result = $rootConn->query("TRUNCATE user_modules");
		$result = $rootConn->query("TRUNCATE projects");
		$result = $rootConn->query("TRUNCATE tables_modules");
		$result = $rootConn->query("TRUNCATE properties");
		$result = $rootConn->query("TRUNCATE links");

		// TODO: Delete files also
		
		$this->session->set("notification", $this->notifications); // Should run before and after every controller exit

		return redirect()->to('/projects');
	}

	// Resets the information gathered about a table
	public function resetTable() {
        $rootConn = \Config\Database::connect("default");

        if ($this->request->isAjax()) {
            $tablesInfo = new UserTableModel();

            $moduleName = $this->request->getPost("table_name");
            $result = $tablesInfo->getWhere(["table_name" => $moduleName])->getResultArray();

			if (count($result) == 0) 
				return $this->response->setJSON(array(
					"status" => "error",
					"message" => "Nothing to reset"
				));

            // Get all the columns IDs we need to delete
            $searchIn = [];
            foreach ($result as $key => $value) {
                $searchIn[] = $value["id"];
            }
            $searchIn = implode(", ", $searchIn);

            // Delete everything that exists regarding this table
            $result = [];
			$result[] = $rootConn->query("DELETE FROM user_tables WHERE id IN ({$searchIn})");
            // $result[] = $rootConn->query("DELETE FROM properties WHERE user_table_id IN ({$searchIn})");
            // $result[] = $rootConn->query("DELETE FROM tables_modules WHERE user_table_id IN ({$searchIn})");
            // $result[] = $rootConn->query("DELETE FROM links WHERE user_table_id_primary IN ({$searchIn})");
        }

        return $this->response->setJSON(array(
			"status" => "success",
			"message" => "Table reset succesfull"
		));
	}

	public function getTableColumns($tableName = null, $project_hash = null) {
		// AJAX calls have priority
		if ($this->request->isAjax()) {
			$tableName = $this->request->getPost("tableName");
			$project_hash = $this->request->getPost("project_hash");
		}
		if (is_null($tableName) || empty($tableName)) { return false; }
		if (is_null($project_hash)) { return false; }

		// Check to see if projectId belongs to current logged user
		$projects = new ProjectModel();
		$this->current_project = $projects->checkProjectBelongsToUser($project_hash, $this->user->id);

		if (!isset($this->current_project)) return false;
		if (empty($this->current_project)) return false;
		if (!strlen($this->current_project->project_hash) == 6) return false;

		// Read the info about the table so we can use it our way
        $schema = new SchemaModel();
        $columns = $schema->getColumns($this->current_project->project_hash, $tableName, $this->mapping);

		// We add some new properties to each column
		foreach ($columns as &$column) {
			// $checksum = base64_encode(json_encode($columns));
			// All columns enabled by default
			$column["enabled"] = 1; 

			// TODO: We add a checksum to each column, so we can detect if a columns data has changed and display this in the UI
			// $column["checksum"] = $checksum; 
		}

		// Data will be saved in table user_tables
        $saved = $this->saveTableInfo($tableName, $columns);

        if ($this->request->isAjax()) {
            return $this->response->setJSON(array(
                "tableName" => $tableName,
                "columns" => $saved
            ));
        }
    }

	public function saveTableInfo($tableName, $columns) {
        $columns = array_map(function($column) {
            $newColumn = [];
            foreach (array_keys($this->mapping) as $value) {
                switch ($value) {
                    case "pk": { $newColumn[$value] = $column[$this->mapping[$value]] === "PRI" ? 1 : 0; break; }
                    case "null": { $newColumn[$value] = $column[$this->mapping[$value]] === "YES" ? 1 : 0; break; }
                    case "ai": { $newColumn[$value] = $column[$this->mapping[$value]] === "auto_increment" ? 1 : 0; break; }
                    default: { $newColumn[$value] = $column[$this->mapping[$value]]; break; }
				}
				$newColumn["user_id"] = $this->user->id;
				$newColumn["project_id"] = $this->current_project->id;
            }

            return array_merge(array('id' => null), $newColumn, array("checksum" => sha1(json_encode($newColumn))));
        }, $columns);

        $userTable = new UserTableModel();
        $userTable->where(array("table_name" => $tableName))->delete();

        foreach ($columns as $column) {
            $result[] = $userTable->insert((Object)$column);
		}

        return $result;
    }

	public function linkTableToModule() {
        $userModules = new UserModuleModel();
        $tablesModules = new TableModuleModel();

        // TODO: Review this function
        $post = $this->request->getPost();
        if (empty($post["module_name"])) {
			$this->notifications[] = ["warning", "No module name provided so we'll use the table name"];
			$this->session->set("notification", $this->notifications);
		}

        $moduleData = array(
            "module_name" => $post["module_name"],
            "module_title" => ucwords($post["module_name"]),
            "module_type" => $post["module_name"],
            "module_route" => $post["module_name"]
        );

		// Check for user and projects also
		$check = $userModules->getWhere(array("module_name" => $post["module_name"]))->getResultArray();
        if (count($check) > 0) {
            $temp = $userModules->update($check[0]["id"], $moduleData);
            $userModuleId = $check[0]["id"];
        } else {
            $userModuleId = $userModules->insert($moduleData);
        }

        if (empty($userModuleId)) die("WTF just happened ?");

        $selectedColumns = $post["selectedColumns"];

        // Here is where the link between tables and modules is made
        if (count($selectedColumns)) {
            foreach ($selectedColumns as $userTableId) {
                $data[] = array(
                    "user_table_id" => $userTableId,
                    "user_module_id" => $userModuleId
                );
            }
        }

        if ($this->request->isAjax()) {
            // TODO: Module should not be deleted when creating a new one
            // In the feature, you should be able to use the same table more than once
            $tablesModules->where(array("user_module_id" => $userModuleId))->delete();
            foreach ($data as $value) {
                $result = $tablesModules->insert($value);

                $setIds = isset($post["setIds"]) && $post["setIds"] === "true" ? 1 : 0;
                $setNames = isset($post["setNames"]) && $post["setNames"] === "true" ? 1 : 0;
                $setClasses = isset($post["setClasses"]) && $post["setClasses"] === "true" ? 1 : 0;
                $setLabels = isset($post["setLabels"]) && $post["setLabels"] === "true" ? 1 : 0;

                $link = $tablesModules->saveTablesModulesLink($value["user_table_id"], $setIds, $setNames, $setClasses, $setLabels);
            }

            return $this->response->setJSON(["module" => $post["module_name"]]);
        } else {
            var_dump($result);
        }
    }

	public function autosave() {
        $post = $this->request->getPost();

        $params = base64_decode($post["param"]);
        $value = $post["value"];
        $data = json_decode($params);
        $id = $data->id;
        unset($data->id);
        foreach ($data as $table => $column) break;

		$schemaModel = new SchemaModel();
        $query = "UPDATE {$table} SET {$column} = '{$value}' WHERE id = {$id}";
        $result = $schemaModel->executeQuery2($_ENV["database.default.database"], $query);

        return $this->response->setJSON($result);
    }
}
