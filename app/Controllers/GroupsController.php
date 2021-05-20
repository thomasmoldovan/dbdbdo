<?php namespace App\Controllers;

use App\Models\GroupsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class GroupsController extends Home {

    protected $session;
    protected $database;

    public function __construct() {
		helper(['filesystem', 'form', 'url', 'html', 'inflector', 'auth']);
		$this->session = service('session');
	}

    public function index($project_hash = null) {
        $groups = new GroupsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        // From dbdbdo database
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules(user()->id);

        // From separate database
        $this->database = "_".$project_hash;
        $groups->setDatabase($this->database);
        $groupsItems = $groups->findAll();

        $data["view"] = "GroupsView";
        $data["navigation"] = false;
        $data["menuItems"] = $menuItems;
        $data["headers"] = $groups->getAllowedFields();
        $data["groupsItems"] = $groupsItems;
        $data["auth"] = service("authentication");
        $data["user"] = user();

        if (service("authentication")->check()) {
            return view("preview", $data);
		}
        return view("header", "projects");
    }

    public function create($project_hash = null) {
        $post = $this->request->getPost();
        
        if (empty($project_hash)) { 
            $this->notifications[] = ["error", "Invalid project value"];
            $this->session->set("notification", $this->notifications);
            return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $groups = new GroupsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = "_".$project_hash;
        $groups->setDatabase($this->database);

        if ($this->request->isAjax()) {
            if (isset($post["id"])) {
                $update_id = $post["id"] == "" ? null : $post["id"];
            } else {
                $update_id = null;
            }

            $validation = \Config\Services::validation();
            if ($validation->run() == TRUE) { }

            if (!is_null($update_id)) {
                $groups->update($update_id, $post);
            } else {
                $groups->insert($post);
            }
        }

        $data["errors"] = false;
        $data["errors"] = $validation->getErrors();

        if ($this->request->isAjax()) {
            return $this->response->setJSON($data);
        } else {
            var_dump($data);
        }
    }

    public function list() {
        $post = $this->request->getPost();
        $project_hash = $post["project_hash"];

        if (empty($project_hash)) { 
            $this->notifications[] = ["error", "Invalid project value"];
            $this->session->set("notification", $this->notifications);
            return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $groups = new GroupsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = "_".$project_hash;
        $groups->setDatabase($this->database);

        $primary = $groups->getPrimary();  // The primary key
        $allLabels = $groups->getFieldLabels();
        $allColumns = $groups->getAllowedFields();

        $allGroups = $groups->getGroupsList();

        foreach ($allGroups as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["headers"] = $allLabels;
        $data["allColumns"] = $allColumns;
        $data["groupsItems"] = $allGroups;

        return $this->response->setJSON($data);
    }

    public function delete($project_hash = null) {
        $post = $this->request->getPost();
        if (empty($project_hash)) { 
            $this->notifications[] = ["error", "Invalid project value"];
            $this->session->set("notification", $this->notifications);
            return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $groups = new GroupsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = "_".$project_hash;
        $groups->setDatabase($this->database);

        if ($this->request->isAjax()) {
            $groups_id = (int) $post["id"];
            $groups->delete($groups_id);
        }

        return $this->response->setJSON(true);
    }
}
