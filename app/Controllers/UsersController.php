<?php namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class UsersController extends Home {

    protected $session;
    protected $database;

    public function __construct() {
		helper(['filesystem', 'form', 'url', 'html', 'inflector', 'auth']);
		$this->session = service('session');
		// $this->database = "_".$_SESSION["project_hash"];
		$this->database = $_ENV["database.default.database"];
	}

    public function index($project_hash = null) {
        $users = new UsersModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        // From dbdbdo database
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules(user()->id);

        // From own database
        $users->setDatabase($this->database);
        $usersItems = $users->findAll();

        $data["view"] = "UsersView";
        $data["navigation"] = false;
        $data["menuItems"] = $menuItems;
        $data["headers"] = $users->getAllowedFields();
        $data["usersItems"] = $usersItems;
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

        $users = new UsersModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $users->setDatabase($this->database);

        if ($this->request->isAjax()) {
            if (isset($post["id"])) {
                $update_id = $post["id"] == "" ? null : $post["id"];
            } else {
                $update_id = null;
            }

            $validation = \Config\Services::validation();
            if ($validation->run() == TRUE) { }

            if (!is_null($update_id)) {
                $users->update($update_id, $post);
            } else {
                $users->insert($post);
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

        $users = new UsersModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $users->setDatabase($this->database);

        $primary = $users->getPrimary();  // The primary key
        $allLabels = $users->getFieldLabels();
        $allColumns = $users->getAllowedFields();

        $allUsers = $users->getUsersList();

        foreach ($allUsers as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["headers"] = $allLabels;
        $data["allColumns"] = $allColumns;
        $data["usersItems"] = $allUsers;

        return $this->response->setJSON($data);
    }

    public function delete($project_hash = null) {
        $post = $this->request->getPost();
        if (empty($project_hash)) { 
            $this->notifications[] = ["error", "Invalid project value"];
            $this->session->set("notification", $this->notifications);
            return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $users = new UsersModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $users->setDatabase($this->database);

        if ($this->request->isAjax()) {
            $users_id = (int) $post["id"];
            $users->delete($users_id);
        }

        return $this->response->setJSON(true);
    }
}
