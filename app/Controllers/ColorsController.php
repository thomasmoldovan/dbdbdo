<?php namespace App\Controllers;

use App\Models\ColorsModel;
use App\Models\ProjectModel;
// {{dynamic}} use App\Models\{{uc_join_model}}Model;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class ColorsController extends HomeController {

    protected $session;
    protected $database;

    public function __construct() {
		helper(['filesystem', 'form', 'url', 'html', 'inflector', 'auth']);
		$this->session = service('session');
	}

    public function index($project_hash = null) {
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        // {{dynamic}} ${{join_model}} = new {{uc_join_model}}Model();
        $userModule = new UserModuleModel();

        // From dbdbdo database
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules(user()->id, $project_hash);

        // From separate database
        $this->database = $project_hash;
        $colors->setDatabase($this->database);
        $colorsItems = $colors->findAll();

        $data["view"] = "ColorsView";
        $data["navigation"] = false;
        $data["menuItems"] = $menuItems;
        $data["headers"] = $colors->getAllowedFields();
        $data["colorsItems"] = $colorsItems;
        // {{dynamic}} $data["{{join_model}}Items"] = ${{join_model}}Items;
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

        $colors = new ColorsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = $project_hash;
        $colors->setDatabase($this->database);

        if ($this->request->isAjax()) {
            if (isset($post["id"])) {
                $update_id = $post["id"] == "" ? null : $post["id"];
            } else {
                $update_id = null;
            }

            $validation = \Config\Services::validation();
            if ($validation->run() == TRUE) { }

            if (!is_null($update_id)) {
                $colors->update($update_id, $post);
            } else {
                $colors->insert($post);
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

        $colors = new ColorsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = $project_hash;
        $colors->setDatabase($this->database);

        $primary = $colors->getPrimary();  // The primary key
        $allLabels = $colors->getFieldLabels();
        $allColumns = $colors->getAllowedFields();

        $allColors = $colors->getColorsList();

        foreach ($allColors as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["headers"] = $allLabels;
        $data["allColumns"] = $allColumns;
        $data["colorsItems"] = $allColors;

        return $this->response->setJSON($data);
    }

    public function delete($project_hash = null) {
        $post = $this->request->getPost();
        if (empty($project_hash)) { 
            $this->notifications[] = ["error", "Invalid project value"];
            $this->session->set("notification", $this->notifications);
            return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $colors = new ColorsModel();
        $projects = new ProjectModel();

        $this->current_project = $projects->checkProjectBelongsToUser($project_hash, user()->id);
        if (empty($this->current_project)) {
            $this->notifications[] = ["error", "Project not found"];
            $this->session->set("notification", $this->notifications);
			return $this->response->setJSON(["notification" => $this->notifications]);
        }

        $this->database = $project_hash;
        $colors->setDatabase($this->database);

        if ($this->request->isAjax()) {
            $colors_id = (int) $post["id"];
            $colors->delete($colors_id);
        }

        return $this->response->setJSON(true);
    }
}
