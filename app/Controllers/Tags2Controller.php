<?php namespace App\Controllers;

use App\Models\Tags2Model;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class Tags2Controller extends HomeController {

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
        $tags2 = new Tags2Model();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        $projectHash = $this->session->get("project_hash");
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules($this->user->id, $projectHash);

        $tags2Items = $tags2->findAll();
        
        

        $data["auth"] = $this->auth->check();
        $data["user"] = $this->user;
        $data["session"] = $this->session;
        $data["headers"] = $tags2->getAllowedFields();
        $data["tags2Items"] = $tags2Items;
        $data["menuItems"] = $menuItems;
        $data["page"] = "Tags2View";

        return $this->display_main("preview", "tags2", $data);
    }

    public function test() {
        return $this->response->setJSON("Thomas");
    }

    public function create() {
        $tags2 = new Tags2Model();
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
                $tags2->update($update_id, $post);
            } else {
                $tags2->insert($post);
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
        $tags2 = new Tags2Model();
        $projects = new ProjectModel();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        $primary = $tags2->getPrimary();  // The primary key
        $allLabels = $tags2->getFieldLabels();
        $allColumns = $tags2->getAllowedFields();

        $allTags2 = $tags2->getTags2List();

        foreach ($allTags2 as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["allColumns"] = $allColumns;
        $data["headers"] = $allLabels;
        $data["tags2Items"] = $allTags2;

        return $this->response->setJSON($data);
    }

    public function delete() {
        $tags2 = new Tags2Model();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        if ($this->request->isAjax()) {
            $tags2_id = (int) $post["id"];
            $tags2->delete($tags2_id);
        }

        return $this->response->setJSON(true);
    }
}
