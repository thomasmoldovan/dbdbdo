<?php namespace App\Controllers;

use App\Models\GroupsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class GroupsController extends HomeController {

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
        $groups = new GroupsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        $projectHash = $this->session->get("project_hash");
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules($this->user->id, $projectHash);

        $groupsItems = $groups->findAll();
        
        $data['joinColors'] = $groups->getAllColors(); 

        $data["auth"] = $this->auth->check();
        $data["user"] = $this->user;
        $data["session"] = $this->session;
        $data["headers"] = $groups->getAllowedFields();
        $data["groupsItems"] = $groupsItems;
        $data["menuItems"] = $menuItems;
        $data["page"] = "GroupsView";

        return $this->display_main("preview", "groups", $data);
    }

    public function test() {
        return $this->response->setJSON("Thomas");
    }

    public function create() {
        $groups = new GroupsModel();
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
        // POST method entry point
        $groups = new GroupsModel();
        $projects = new ProjectModel();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        $primary = $groups->getPrimary();  // The primary key
        $allLabels = $groups->getFieldLabels();
        $allColumns = $groups->getAllowedFields();

        $allGroups = $groups->getGroupsList();

        foreach ($allGroups as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["allColumns"] = $allColumns;
        $data["headers"] = $allLabels;
        $data["groupsItems"] = $allGroups;

        return $this->response->setJSON($data);
    }

    public function delete() {
        $groups = new GroupsModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        if ($this->request->isAjax()) {
            $groups_id = (int) $post["id"];
            $groups->delete($groups_id);
        }

        return $this->response->setJSON(true);
    }
}
