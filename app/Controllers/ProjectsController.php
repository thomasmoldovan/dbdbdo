<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;

use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ProjectsController extends HomeController {

	protected $current_project;

	public function index($hash = null)	{
		// Should be moved in Home
		if ($this->checkIfLogged() !== true) {
			return redirect()->to("/");;
		}
		$projects = new ProjectModel();

		// Check if we have a hash in the URL
		if (isset($hash) && !is_null($hash)) {
			// If hash belongs to logged user, we should have a project
			$project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $hash])->getResultArray();

			if (!empty($project)) {
				// Project found
				$this->current_project = $project[0];

				// Tables List View
				if ((int)$this->current_project["project_type"] == 0) {
					// EXTERNAL
					$schema = new SchemaModel();
					$data["project"] = $this->current_project;
					$data["tables"] = $schema->getTables($this->current_project["project_hash"]); 
					$data["userTables"] = $schema->getTablesInfo($this->user->id, $this->current_project["id"]);
					$data["tablesProcessed"] = array_unique(array_column($data["userTables"], "table_name"));
	
					$this->session->set("notification", $this->notifications);
					return $this->display_main("header", "project", $data);
				} else if ((int)$this->current_project["project_type"] == 1) {
					// INTERNAL
					$schema = new SchemaModel();
					$data["project"] = $this->current_project;

					// I need the exact tables of this project so it will not show everything
					$data["tables"] = $projects->getInnerProjectTables($this->current_project["id"]);
					// Now we overwrite the tables with our info from information_schema, only for those ids
					$data["tables"] = $schema->getTables($this->current_project["database"], $data["tables"]);

					$data["userTables"] = $schema->getTablesInfo($this->user->id, $this->current_project["id"]);
					$data["tablesProcessed"] = array_unique(array_column($data["userTables"], "table_name"));
	
					$this->session->set("notification", $this->notifications);
					return $this->display_main("header", "project", $data);

					if ($this->current_project->project_type == 0) {
						$tables = $schema->getTables($this->current_project->project_hash);
					} else {
						$tables = [["TABLE_NAME" => $this->current_project->project_hash]];
					}
				}				
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
				$drop_db_query = "DROP DATABASE ".$db->project_hash.";";
				try {
					$dbs = $rootConn->query($drop_db_query);
				} catch (\Exception $ex) {
					$this->notifications[] = ["error", __FILE__." - Line:".__LINE__." - ".__FUNCTION__, $ex->getCode().":".$ex->getMessage()];
					$this->session->set("notification", $this->notifications);
				}
			}
		}

		// $result = $rootConn->query("DELETE FROM user_tables WHERE project_id > 1");
		$result = $rootConn->query("TRUNCATE user_modules");
		$result = $rootConn->query("TRUNCATE user_tables");
		// $result = $rootConn->query("DELETE FROM projects WHERE id > 1");
		$result = $rootConn->query("TRUNCATE projects");
		$result = $rootConn->query("TRUNCATE tables_modules");
		$result = $rootConn->query("TRUNCATE properties");
		$result = $rootConn->query("TRUNCATE links");

		// TODO: Delete files also
		
		$this->session->set("notification", $this->notifications); // Should run before and after every controller exit

		return redirect()->to('/projects');
	}

}
