<?php namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ModulesController extends HomeController {

    protected $current_project;

	public function __construct() {
		// TODO: Have this in a base class that all controllers will be based on
        helper('auth');
        helper('general');
        helper('html');
		$this->session = service('session');
		$this->config = config('Auth');
		$this->auth = service('authentication');
		$this->pages = config('Pages');
		$this->user = user();
		$this->projectHash = $this->session->get("project_hash");

		if (is_null($this->user)) {
			$this->respond("error", "Your session has expired");
		}
	}
    
    public function index($project_hash = null) {
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

		// We have the project and the user is logged
		$this->current_project = $this->current_project[0];
		$this->notifications[] = ["success", "Project ".$this->current_project["project_hash"]." loaded"];
		$this->session->set("project_hash", $this->current_project["project_hash"]);

		// Get all the modules
		$modules->projectId = $this->current_project["id"];
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

		if (isset($this->current_project["project_hash"]) && strlen(trim($this->current_project["project_hash"])) == 7) {
			$tables = $schema->getTables($this->current_project["project_hash"]);
		} else {
			return redirect()->to('/projects');
		}

		// $userModules = $this->getModulesInfo();

		$data["project"] = $this->current_project;
		$data["preview_link"] = strtolower($this->current_project["project_hash"]);
		// $data["userModules"] = $userModules;
		$data["moduleList"] = $moduleList;

		$data["modules"] = $moduleData;

		$this->session->set("notification", $this->notifications);
		return $this->display_main("header", "modules", $data);
	}

    public function deleteModule() {
		$this->checkIfLogged();

		$post = $this->request->getPost();
		
		if (isset($post["module_id"]) && (int)$post["module_id"] > 0) {
			$module_id = $post["module_id"];
		} else {
			return $this->respond("error", "Invalid module");
		}

		// Get project_id from where this module is
		$user_modules = new UserModuleModel();
		$project_id = $user_modules->getWhere(["id" => $module_id])->getResultArray();
		
		if (is_array($project_id)) {
			$project_id = $project_id[0]["project_id"];
		} else {
			return $this->respond("error", "Invalid Module", "There are no projects for this module");
		}

		// Check if project belongs to current user
		$projects = new ProjectModel();
		$project = $projects->getWhere(["user_id" => $this->user->id, "id" => $project_id])->getResultArray();
		if (is_array($project)) {
			$this->current_project = $project[0];
		} else {
			return $this->respond("error", "Different Project", "That module does not belong to you :|");
		};

		// Check if project the same as session, if the same project is loaded
		$project_hash = $this->session->get("project_hash");
		if ($project_hash !== $this->current_project["project_hash"]) {
			return $this->respond("error", "Different Project", "The module belongs to another project of yours");
		}

        $tables_modules = new TableModuleModel();

		// Delete the data
        $user_modules->delete($module_id);
        $tables_modules->where(["user_module_id" => $module_id])->delete();

		// Delete the files if project external
		if ($this->current_project["project_type"] == 0) {

		}

        return $this->response->setJSON($post);
    }    
}
