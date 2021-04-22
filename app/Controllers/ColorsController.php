<?php namespace App\Controllers;

use App\Models\ColorsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class ColorsController extends Home {

    private $onoff = true; // true = ONLINE off = OFFLINE

    protected $session;

    public function __construct()
	{
		helper('html');
		helper('auth');
		$this->session = service('session');
	}

    public function index($project_hash = null) {
        // $project_hash = $this->url->segment(3);
        helper(['filesystem', 'form', 'url']);
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        // From own database
        $colors->setDatabase("_".$project_hash);
        $colorsItems = $colors->findAll();

        $data["colorsItems"] = $colorsItems;
        $data["view"] = "ColorsView";

        // From dbdbdo database
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules(user()->id);

        if (service("authentication")->check()) {
            return view("preview", [
                "navigation" => false,
                "auth" => service("authentication"),
                "user" => user(),
                "headers" => $colors->getAllowedFields(),
                "menuItems" => $menuItems]);
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

        $colors->setDatabase("_".$project_hash);

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

        $colors->setDatabase("_".$project_hash);

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

        $colors->setDatabase("_".$project_hash);

        if ($this->request->isAjax()) {
            $colors_id = (int) $post["id"];
            $colors->delete($colors_id);
        }

        return $this->response->setJSON(true);
    }
}
