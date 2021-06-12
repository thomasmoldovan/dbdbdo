<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\SchemaModel;
use App\Models\UserTableModel;
use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ImportController extends HomeController {

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
								"response" => ["error", "You do not have permissions to run that command"]
							));
						} else if ($ex->getCode() == 1064) {
							return $this->response->setJSON(array(
								"status" => "error",
								"code" => $ex->getCode(),
								"response" => ["error", "You have an error in your SQL script"]
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
				"project_hash" => $project_hash,
                "response" => ["success", "Project ".$this->current_project->project_hash." created."]
			]);
		} else {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong executing the SQL script"
			));
		}
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
			"message" => "Table reset succesfull",
            
		));
	}

	// Delete the table from DB
	public function deleteTable() {
		if (!$this->auth->check()) {
			$this->notifications[] = ["info", "Your session has expired"];
			$this->session->set("notification", $this->notifications);
			return redirect()->to("/");
		}

        if ($this->request->isAjax()) {
			$schema = new SchemaModel();
			$projects = new ProjectModel();

            $tableName = $this->request->getPost("table_name");
            $project_hash = $this->request->getPost("project_hash");

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

			$result = $schema->executeOuterQuery($this->current_project["project_hash"], "DROP TABLE ".$tableName);

			$this->notifications[] = ["success", "Table deleted succesfully"];
			$this->session->set("notification", $this->notifications);
			return $this->response->setJSON(array(
				"response" => ["error", "Table deleted succesfully"]
			));
        }

		$this->notifications[] = ["danger", "Only AJAX calls allowed"];
		$this->session->set("notification", $this->notifications);
        return $this->response->setJSON(array(
            "response" => ["error", "Only AJAX calls allowed"]
		));
	}

	public function getTableColumns($tableName = null, $project_hash = null) {
		if (!is_null($tableName) && !is_null($project_hash)) {
		} elseif ($this->request->isAjax()) {
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

		$this->notifications[] = ["success", "Saved ".count($saved)." columns in table ".$tableName];
		$this->session->set("notification", $this->notifications);

		return $this->response->setJSON(array(
			"tableName" => $tableName,
			"columns" => $columns,
            "response" => ["success", "Saved ".count($saved)." columns in table ".$tableName]
		));
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
        // $userTable->where(array("table_name" => $tableName))->delete();

        foreach ($columns as $column) {
            $result[] = $userTable->insert((Object)$column);
		}

        return $result;
    }

	public function linkTableToModule() {
        $userModules = new UserModuleModel();
        $tablesModules = new TableModuleModel();
        $project = new ProjectModel();

        // TODO: Review this function
        $post = $this->request->getPost();

        if ($this->current_project = $project->checkProjectBelongsToUser($post["project_hash"], $this->user->id)) {

        }

        if (empty($post["module_name"])) {
			$this->notifications[] = ["warning", "No module name provided so we'll use the table name"];
			$this->session->set("notification", $this->notifications);
		}



        $moduleData = array(
            "project_id" => (int)$this->current_project->id,
            "module_name" => $post["module_name"],
            "module_title" => ucwords($post["module_name"]),
            "module_type" => $post["module_name"],
            "module_route" => $post["module_name"],
			"show_on_menu" => 1
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
}
