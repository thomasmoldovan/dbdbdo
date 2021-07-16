<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;
use App\Models\PropertiesModel;
use App\Models\LinksModel;

use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ProjectsController extends HomeController {

	protected $current_project;

	public function index($hash = null)	{
		$this->checkIfLogged();

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

					// The number of rows is not reported correctly in information_schema table
					// So we retrieve it our selfs
					$data["nr_rows"] = [];
					foreach ($data["tables"] as $table_info) {
						$data["nr_rows"][$table_info["TABLE_NAME"]] = $schema->getRowsNumber($table_info["TABLE_NAME"])[0]["rows"];
					}

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

					// For each table I get the number of rows, just for information
					$data["nr_rows"] = [];
					foreach ($data["tables"] as $table) {
						$data["nr_rows"][$table] = $schema->getRowsNumber($table)[0]["rows"];
					}
					
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
		$this->checkIfLogged();
		$rootConn = \Config\Database::connect("default");

		// DROP all the user databases
		$dbs = $rootConn->query("SELECT project_hash FROM projects;")->getResult();
		if (empty($dbs)) {
			$this->notify("info", "No projects to delete");
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
		
		return redirect()->to('/projects');
	}

	public function deleteProject() {
		$this->checkIfLogged();

		$post = $this->request->getPost();
		$projectHash = $post["project_hash"];


		// Check if project belongs to current user
		$projects = new ProjectModel();
		$project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $projectHash])->getResultArray();

		if (is_array($project)) {
			$this->current_project = $project[0];
		} else return false;

		$user_modules = new UserModuleModel();
		$user_table = new UserTableModel();
		$properties = new PropertiesModel();
		$links = new LinksModel();
		$tables_modules = new TableModuleModel();

		$user_table_ids = $user_table->getWhere(["project_id" => $this->current_project["id"]])->getResultArray();
		foreach ($user_table_ids as &$row) $row = $row["id"];
		
		$user_modules_ids = $user_modules->getWhere(["project_id" => $this->current_project["id"]])->getResultArray();
		foreach ($user_modules_ids as &$row) $row = $row["id"];

		// Delete from tables_modules where user_table_id OR user_module_id
		if (count($user_table_ids) > 0) $tables_modules->whereIn("user_table_id", $user_table_ids)->delete();
		if (count($user_modules_ids) > 0) $tables_modules->whereIn("user_module_id", $user_modules_ids)->delete();

		// Delete from properties where user_tables_id
		if (count($user_table_ids) > 0) $properties->whereIn("user_table_id", $user_table_ids)->delete();

		// Delete from links where user_table_id_primary, user_table_id_foreign, user_table_id_display = user_table_id
		if (count($user_table_ids) > 0) $links->whereIn("user_table_id_primary", $user_table_ids)->delete();
		if (count($user_table_ids) > 0) $links->whereIn("user_table_id_foreign", $user_table_ids)->delete();
		if (count($user_table_ids) > 0) $links->whereIn("user_table_id_display", $user_table_ids)->delete();

		$user_modules->where(["project_id" => $this->current_project["id"]])->delete();

		// Delete from projects where id
		$projects->where(["id" => $this->current_project["id"]])->delete();

		$user_table->where(["project_id" => $this->current_project["id"]])->delete();
		
		// Delete all files of project
			// Depends if external internal
			// INTERNAL -> DO NOT DELETE THE FILES
			// EXTERNAL -> DELETE EVERYTHING
			
		// Drop the database

		return $this->respond("Success", "Project deleted");
	}

}
