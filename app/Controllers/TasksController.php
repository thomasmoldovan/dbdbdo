<?php namespace App\Controllers;

use App\Models\TasksModel;
use App\Models\ColorsModel;
use App\Models\ProjectModel;
use App\Models\UserModuleModel;
use CodeIgniter\Controller;

class TasksController extends HomeController {

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
        $tasks = new TasksModel();
        $colors = new ColorsModel();
        $projects = new ProjectModel();
        $userModule = new UserModuleModel();

        $projectHash = $this->session->get("project_hash");
        $userModule->setDatabase($_ENV["database.default.database"]);
        $menuItems = $userModule->getActiveModules($this->user->id, $projectHash);

        $tasksItems = $tasks->findAll();
        
        $data['joinGroups'] = $tasks->getAllGroups(); $data['joinColors'] = $tasks->getAllColors(); 

        $data["auth"] = $this->auth->check();
        $data["user"] = $this->user;
        $data["session"] = $this->session;
        $data["headers"] = $tasks->getAllowedFields();
        $data["tasksItems"] = $tasksItems;
        $data["colors"] = $colors->getColorsList();
        $data["menuItems"] = $menuItems;
        $data["page"] = "TasksView";

        return $this->display_main("preview", "tasks", $data);
    }

    public function test() {
        return $this->response->setJSON("Thomas");
    }

    public function create() {
        $tasks = new TasksModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        if ($this->request->isAjax() || $this->request->getMethod() == "post") {
            if (isset($post["id"])) {
                $update_id = $post["id"] == "" ? null : $post["id"];
            } else {
                $update_id = null;
            }

            // $validation = \Config\Services::validation();
            // if ($validation->run() == TRUE) { }

            if (!is_null($update_id)) {
                $tasks->update($update_id, $post);
            } else {
                $tasks->insert($post);
            }
        }

        $data["errors"] = false;
        // $data["errors"] = $validation->getErrors();

        if ($this->request->isAjax()) {
            var_dump($data);
        } else {
            return redirect()->to("/projects/tba284c/preview/tasks");            
        }
    }

    public function list() {
        // POST method entry point
        $tasks = new TasksModel();
        $projects = new ProjectModel();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        $primary = $tasks->getPrimary();  // The primary key
        $allLabels = $tasks->getFieldLabels();
        $allColumns = $tasks->getAllowedFields();

        $allTasks = $tasks->getTasksList();

        foreach ($allTasks as &$item) {
            $item["check"] = json_encode($item);
        }
        $data["primary"] = $primary;
        $data["allColumns"] = $allColumns;
        $data["headers"] = $allLabels;
        $data["tasksItems"] = $allTasks;

        return $this->response->setJSON($data);
    }

    public function delete() {
        $tasks = new TasksModel();
        $projects = new ProjectModel();
        $post = $this->request->getPost();

        $projectId = $this->session->get("project_hash");
        $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;

        if ($this->request->isAjax()) {
            $tasks_id = (int) $post["id"];
            $tasks->delete($tasks_id);
        }

        return $this->response->setJSON(true);
    }
}
