<?php namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\UserModuleModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;
use App\Models\LinksModel;

class ForeignKeysController extends Home
{
    protected $pk_list = [];
    protected $current_project;
    
	public function index($project_hash = null) {
		if (!$this->auth->check()) {
			$this->notifications[] = ["info", "Your session has expired"];
			$this->session->set("notification", $this->notifications);
			return redirect()->to("/");
		}

		$schema = new SchemaModel();
		$projects = new ProjectModel();
		$modules = new UserModuleModel();
        $table_info = new UserTableModel();

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

		// helper('html');

		// TODO: Read only from the read tables not from everything
		// $tables = $schema->readTables($_ENV["database.default.database"]);
		$tables = $table_info->getModulesColumns($this->current_project["id"]);

		$data["title"] = "Foreign Keys";

		$columns = array();
		$rows = array();

        // Get every table column, and how many rows it has
        $schema->setDatabase("_".$this->current_project["project_hash"]);
		foreach ($tables as $key => $table) {
			// TODO: I should only look for links between the tables (columns) I've scanned

			// Do I need this ? I already have the info
			$columns[$table["TABLE_NAME"]] = $schema->getColumns($this->current_project["project_hash"], $table["TABLE_NAME"]);

			//$sql = "SELECT count(*) AS `rows` FROM `{$table["TABLE_NAME"]}`";
			//$rows[$table["TABLE_NAME"]] = $schema->executeQuery($projectHash, $sql, "array");
		}

		// Get the primary key for every table
		foreach ($tables as $key => $table) {
            $temp = $this->getPrimaryKey($table['TABLE_NAME']);
            // If this table has a primary key, we add it to $this->pk_list
            if ($temp !== false && !isset($this->pk_list[$table['TABLE_NAME']])) {
                $this->pk_list[$table['TABLE_NAME']] = $temp;
            }
        }

		// Find the primary key of the table in other column names
		$foreign_keys = $this->findForeignKeysForTable($columns);

		// WHY 2 loops ?

		// Get the column details for every possible foreign
		$column_types = [];		
		$schema->setDatabase($_ENV["database.default.database"]);
		foreach ($foreign_keys as $key => $link) {
			if (!empty($link)) {
				if (!isset($column_types[$link["table_name"].".".$link["table_column"]])) {
					$column_types[$link["table_name"].".".$link["table_column"]] = $this->getColumnType($this->current_project["project_hash"], $link["table_name"], $link["table_column"]);
				}
				if (!isset($column_types[$link["key_table"].".".$link["key_column"]])) {
					$column_types[$link["key_table"].".".$link["key_column"]] = $this->getColumnType($this->current_project["project_hash"], $link["key_table"], $link["key_column"]);
				}

				// I need the display_link
				$schema->setDatabase($_ENV["database.default.database"]);
				$table_info->projectId = $this->current_project["id"];
				$columnsThatCanBeLinked = $table_info->getColumnsForTable($link["table_name"]);
				$foreign_keys[$key]["display"] = $this->isLinkOn($link);
				$foreign_keys[$key][$link["table_name"]] = $columnsThatCanBeLinked;

				// I need the columns that can be linked, the columns in tables_info so
				// I can have a dropdown with what can be displayed
				$t = $foreign_keys[$key];
				// $foreign_keys[$key]["columns"] = null;
				if (!isset($foreign_keys[$key]["columns"])) {
					// I i do not have the column i add it
					// $foreign_keys[$key]["columns"] = $link["table_name"];
					// $columnsThatCanBeLinked = $table_info->getColumnsForTable($link["table_name"]);
					// $foreign_keys["columns"][$link["table_name"]] = $columnsThatCanBeLinked;
				}
			}
		}

		// LINKS SAVED
		$links = new LinksModel();
		$links->setDatabase($_ENV["database.default.database"]);
		$data["links"] = $links->getAllLinks(); // ????????

		// STUFF WE NEED
		$data["tables"] = $tables;
		$data["foreign_keys"] = $foreign_keys;
		$data["columns"] = $columns;
		$data["column_types"] = $column_types;

		// We need the current project for the project menu to show
		$data["project"] = $this->current_project;

		$schema = new SchemaModel();

        return $this->display_main("header", "fk", $data);
	}

	public function isLinkOn($link) {
		$links = new LinksModel();
		$links->setDatabase($_ENV["database.default.database"]);
		$allLinks = $links->getAllLinks($link);
		if (!empty($allLinks)) {
			return $allLinks[0];
		} else {
			return false;
		}
	}

	public function findPrimaryKeysForTable($table_we_search_for, $all_the_tables_and_columns) {
		$prefix = "";
		$table_we_search_for = str_replace($prefix, "", $table_we_search_for);

		$ids = array();

		foreach ($all_the_tables_and_columns as $table_name => $every_column) {
			if ($table_we_search_for !== $table_name) {
				foreach ($every_column as $key => $column) {
					$handeled = $this->handleTableName($table_we_search_for);
					if ($this->handleTableName($table_we_search_for) === $column['COLUMN_NAME']) {
						$id = [];
						$id["table_name"] = $table_we_search_for;
						$id["table_column"] = $this->pk_list[$table_we_search_for];
						$id["key_table"] = $table_name;
						$id["key_column"] = $column['COLUMN_NAME'];
						$ids[] = $id;
					}
				}
			}
		}
		return $ids;
	}

	public function findForeignKeysForTable($all_the_tables_and_columns) {
		$ids = [];

		// Find all links like color -> color_id
		$primaryNames = array_keys($all_the_tables_and_columns);
		foreach ($primaryNames as $primary_table) {
			for ($i = 1; $i <= 4; $i++) {
				$handeled = $this->handleTableName($primary_table, $i);

				foreach ($all_the_tables_and_columns as $table_name => $every_column) {
					foreach ($every_column as $key => $column) {
						if ($handeled === $column['COLUMN_NAME']) {
							$id = [];
							$id["table_name"] = $primary_table;
							$id["table_column"] = $this->pk_list[$primary_table];
							$id["key_table"] = $table_name;
							$id["key_column"] = $column['COLUMN_NAME'];
							$ids[] = $id;
						}
					}
				}
			}			
		}

		// Find all the links that have the same column name
		// $mirrored = [];
		// foreach ($all_the_tables_and_columns as $key => $table) {
		// 	foreach ($table as $column) {
		// 		if (!isset($mirrored[$column["COLUMN_NAME"]])) $mirrored[$column["COLUMN_NAME"]] = [];
		// 		$mirrored[$column["COLUMN_NAME"]][] = $key;
		// 	}
		// }
		// $mirrored = $mirrored;
		// foreach ($mirrored as $key => $found) {
		// 	if (count($found) > 1) {
		// 		$id = [];
		// 		$id["table_name"] = $found[0];
		// 		$id["table_column"] = $key;
		// 		$id["key_table"] = $found[1];
		// 		$id["key_column"] = $key;
		// 		$ids[] = $id;
		// 	}
		// }

		// Find all the links like color -> colorId
		// Find all the links like color -> colorID
		
		return $ids;
	}

	public function handleTableName($table_name, $method = 1) {
		if ($method == 1) {
			if (substr($table_name, strlen($table_name) - 1, strlen($table_name)) === "s") {
				return substr($table_name, 0, -1)."_id";
			} else {
				return $table_name."_id";
			}
		}

		if ($method == 2) {
			if (substr($table_name, strlen($table_name) - 1, strlen($table_name)) === "s") {
				return substr($table_name, 0, -1)."Id";
			} else {
				return $table_name."Id";
			}
		}

		if ($method == 3) {
			if (substr($table_name, strlen($table_name) - 1, strlen($table_name)) === "s") {
				return substr($table_name, 0, -1)."ID";
			} else {
				return $table_name."ID";
			}
		}

		if ($method == 4) {
			return $table_name;
		}
	}

	public function getPrimaryKey($table) {
		$schema = new SchemaModel();
		$result = $schema->executeQuery($this->current_project["project_hash"], "SHOW INDEX FROM `{$table}`", "array");
        if (count($result) > 0) {
            return $result[0]['Column_name'];
        } else {
            return false;
        }
	}

	public function getColumnType($projectHash, $table, $column) {
		$schema = new SchemaModel();
		$schema->setDatabase("_".$projectHash);
		$result = $schema->executeQuery($this->current_project["project_hash"], "SHOW COLUMNS FROM `{$table}` WHERE `field` = '{$column}'", "array");
        if (count($result) > 0) {
            return $result[0];
        } else {
            return false;
		}
	}

	public function changePKTypeQuery($table, $column) {
        $wanted_id_column = "BIGINT(20)";
        return "ALTER TABLE `{$table}` CHANGE `{$column}` `{$column}` ".$wanted_id_column." UNSIGNED NOT NULL AUTO_INCREMENT;";
    }

    public function changeFKTypeQuery($table, $column) {
        $wanted_id_column = "BIGINT(20)";
        return "ALTER TABLE `{$table}` CHANGE `{$column}` `{$column}` ".$wanted_id_column." UNSIGNED NULL DEFAULT NULL;";
	}

	public function deleteUselessEntries($st, $sc, $dt, $dc) {
        return "DELETE FROM `$st` WHERE `$sc` NOT IN (SELECT `$dc` FROM `$dt` GROUP BY `$dc`);";
        //return "UPDATE `$st` SET `$sc` = NULL WHERE (`$sc` NOT IN (SELECT `$dc` FROM `$dt`) AND `$sc` IS NOT NULL);";
    }

    public function showAlterQuery($st, $sc, $dt, $dc) {
        return "ALTER TABLE `$st` ADD CONSTRAINT `".substr("fk_".$st."_".$sc, 0, 64)."` FOREIGN KEY (`{$sc}`) REFERENCES `{$dt}`(`{$dc}`) ON DELETE RESTRICT;";
	}

	public function saveForeignKey() {
		$post = $this->request->getPost();

		if (empty($post)) {
			return $this->response->setJSON(array("error" => "No POST data"));
		}

		if ($this->request->isAJAX()) {
			if (!empty($post["user_table_id_primary"]) && !empty($post["user_table_id_foreign"])) {

				// Find their ID in table_info
				$tableInfo = new UserTableModel();
				
				$user_table_id_primary = $post["user_table_id_primary"];
				$user_table_id_foreign = $post["user_table_id_foreign"];
				$user_table_id_display = isset($post["user_table_id_display"]) ? $post["user_table_id_display"] : 0;
				$enabled = 1;
				
				$pkId = $tableInfo->getIdByTableAndColulmn(explode(".", $user_table_id_primary)[0], explode(".", $user_table_id_primary)[1]);
				$fkId = $tableInfo->getIdByTableAndColulmn(explode(".", $user_table_id_foreign)[0], explode(".", $user_table_id_foreign)[1]);
				$dkId = $user_table_id_display;
				
				// TODO: Check if exists and update, DO NOT DELETE
				$links = new LinksModel();
				
				$newLink = $links->where(array(
					"user_table_id_primary" => (int) $pkId,
					"user_table_id_foreign" => (int) $fkId
				))->findAll();
				
				if (count($newLink) > 0) {
					$insertId = $links->update(
						(int) $newLink[0]["id"], 
						array(
							"user_table_id_primary" => (int) $pkId,
							"user_table_id_foreign" => (int) $fkId,
							"user_table_id_display" => (int) $dkId,
							"enabled" => (int) $enabled
						)
					);
				} else {
					$insertId = $links->insert(array(
						"user_table_id_primary" => (int) $pkId,
						"user_table_id_foreign" => (int) $fkId,
						"user_table_id_display" => (int) $dkId,
						"enabled" => (int) $enabled
					));
				}				

				// If found show toaster
				return $this->response->setJSON(array("success" => $insertId));
			} else {
				// If not found show toaster
				return $this->response->setJSON(array("error" => "Not enough data"));
			}
		} else {
			return $this->response->setJSON(array("error" => "Not AJAX"));
		}
	}

	public function getTablesInfo() {
		$rootConn = \Config\Database::connect("default");
		$result = $rootConn->query("SELECT user_tables.* FROM user_tables WHERE `user_id`=".$this->user->id." AND `project_id`=".$this->current_project["id"]);
		$final = $result->getResultArray();
		return $result->getResultArray();
	}

	public function getModulesInfo() {
		$rootConn = \Config\Database::connect("default");
        $query = "SELECT  user_tables.project_id,
                            projects.project_name,
                            COUNT(DISTINCT tables_modules.user_module_id) AS modules_in_project
                    FROM user_modules user_modules
                        CROSS JOIN users users
                        CROSS JOIN
                        (user_tables user_tables
                        INNER JOIN tables_modules tables_modules
                            ON (user_tables.id = tables_modules.user_table_id))
                        INNER JOIN projects projects
                            ON (projects.project_hash = ".$this->current_project["project_hash"].")";
        //echo $query; die();
		$result = $rootConn->query($query);
		return $result->getResultArray();
	}
}
