<?php namespace App\Controllers;

use App\Models\ColorsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class ColorsController extends HomeController {

    protected $onoff = true; // true = ONLINE off = OFFLINE
    protected $auth;
    protected $user;
    protected $session;
    protected $pages;
    protected $projectId;
    protected $projectHash;

    public function __construct() {
        helper('auth');
        
        $this->auth = service("authentication");
        $this->user = user();
        $this->session = service('session');
        $this->pages = config('Pages');
    }

    public function index() {
        helper(['filesystem', 'form', 'url']);
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        $projectHash = $this->session->get("project_hash");
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules($this->user->id, $projectHash);

        $colorsItems = $colors->findAll();
        
        

        $data["auth"] = $this->auth->check();
        $data["user"] = $this->user;
        $data["session"] = $this->session;
        $data["headers"] = $colors->getAllowedFields();
        $data["colorsItems"] = $colorsItems;
        $data["menuItems"] = $menuItems;
        $data["page"] = "ColorsView";

        return $this->display_main("preview", "colors", $data);
    }

    public function test() {
        return $this->response->setJSON("Thomas");
    }

    public function create() {
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

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
        // POST method entry point
        $colors = new ColorsModel();
        $projects = new ProjectModel();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        $primary = $colors->getPrimary();  // The primary key
        $allLabels = $colors->getFieldLabels();
        $allColumns = $colors->getAllowedFields();

        $allColors = $colors->getColorsList();

        foreach ($allColors as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["allColumns"] = $allColumns;
        $data["headers"] = $allLabels;
        $data["colorsItems"] = $allColors;

        return $this->response->setJSON($data);
    }

    public function delete() {
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        if ($this->request->isAjax()) {
            $colors_id = (int) $post["id"];
            $colors->delete($colors_id);
        }

        return $this->response->setJSON(true);
    }
}
