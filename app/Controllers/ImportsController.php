<?php namespace App\Controllers;

use App\Models\ImportsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class ImportsController extends HomeController {

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
        $imports = new ImportsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        $projectHash = $this->session->get("project_hash");
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules($this->user->id, $projectHash);

        $importsItems = $imports->findAll();
        
        

        $data["auth"] = $this->auth->check();
        $data["user"] = $this->user;
        $data["session"] = $this->session;
        $data["headers"] = $imports->getAllowedFields();
        $data["importsItems"] = $importsItems;
        $data["menuItems"] = $menuItems;
        $data["page"] = "ImportsView";

        return $this->display_main("preview", "imports", $data);
    }

    public function test() {
        return $this->response->setJSON("Thomas");
    }

    public function create() {
        $imports = new ImportsModel();
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
                $imports->update($update_id, $post);
            } else {
                $imports->insert($post);
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
        $imports = new ImportsModel();
        $projects = new ProjectModel();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        $primary = $imports->getPrimary();  // The primary key
        $allLabels = $imports->getFieldLabels();
        $allColumns = $imports->getAllowedFields();

        $allImports = $imports->getImportsList();

        foreach ($allImports as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["allColumns"] = $allColumns;
        $data["headers"] = $allLabels;
        $data["importsItems"] = $allImports;

        return $this->response->setJSON($data);
    }

    public function delete() {
        $imports = new ImportsModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        if ($this->request->isAjax()) {
            $imports_id = (int) $post["id"];
            $imports->delete($imports_id);
        }

        return $this->response->setJSON(true);
    }
}
