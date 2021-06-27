<?php namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class ModulesController extends HomeController {

    protected $current_project;
    
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
		
		$this->notifications[] = ["info", "Hello from modules 2 :)"];
		$this->session->set("notification", $this->notifications);
		return redirect()->to("/");
	}

    public function deleteModule() {
        $post = $this->request->getPost();

        $user_modules = new UserModuleModel();
        $tables_modules = new TableModuleModel();

        $user_modules->delete($post["module_id"]);
        $tables_modules->where(["user_module_id" => $post["module_id"]])->delete();

        return $this->response->setJSON($post);
    }    
}
