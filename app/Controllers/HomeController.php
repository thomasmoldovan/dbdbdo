<?php

namespace App\Controllers;

class HomeController extends BaseController{
	public $notifications;
	
	protected $auth;
	protected $config;
	protected $user;
	protected $pages;
	protected $authorize;
	protected $session;
	protected $current_project;

	protected $signature;

	public function __construct()
	{
		helper('html');
		helper('auth');
		$this->session = service('session');
		$this->authorize = service('authorization');
		$this->auth = service('authentication');
		$this->config = config('Auth');
		$this->pages = config('Pages');
		$this->user = user();

		$this->signature = random_int(1, 10000);
	}

	public function index()	{
		$check = $this->auth->check();
		if ($check) {
			$redirectURL = session('redirect_url') ?? '/';
			unset($_SESSION['redirect_url']);
			if ($redirectURL !== '/') {
				return $this->display_main("header", $redirectURL);
			} else {
				return $this->display_main("header", "website");
			}
		} else {
			return $this->display_main("header", "website");
		}
	}

	public function checkIfLogged() {
		if (!$this->auth->check()) {
			$this->notifications[] = ["info", "Your session has expired"];
			$this->session->set("notification", $this->notifications);
			return redirect()->to("/");
		}
		return true;
	}

	public function display_main($header = "header", $content = "login", $data = []) {
		return view($header, [
			"auth" => $this->auth, 
			"config" => $this->config,
			"user" => $this->user,
			"page" => $this->pages->pages[$content]["view"],
			"data" => $data
		]);
	}

	public function display_internal_preview($header = "preview", $content = "login", $data = []) {
		return view($header, [
			"auth" => $this->auth, 
			"config" => $this->config,
			"user" => $this->user,
			"page" => $this->pages->pages[$content]["view"],
			"menuItems" => $data["menuItems"],
			"data" => $data
		]);
	}

	public function tried(\Exception $ex) {
		$response["status"] = "error" ?? null;
		$response["code"] = $ex->getCode() ?? null;
		$response["message"] = $ex->getMessage() ?? null;
		if (ENVIRONMENT === "Development") {
			$response["line"] = $ex->getLine() ?? null;
			$response["file"] = $ex->getFile() ?? null;
		}
		return $this->response->setJSON($response);
	}

	public function respond($status, $title, $message = null) {
		$response["status"] = $status ?? null;
		$response["title"] = $title ?? null;
		$response["message"] = $message ?? null;

		return $this->response->setJSON($response);
	}

	public function notify($type = "info", $title = null, $text = null) {
		$this->notifications[] = [$type, $text, $title];
		$this->session->set("notification", $this->notifications);
		return true;
	}

	public function generate_hash() {
		$color = dechex(rand(0x000000, 0xFFFFFF));
		return $color;
	}

	private function milliseconds() {
		$mt = explode(' ', microtime());
		return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
	}

	public function check_db_connections() {
		try {
            $userTables = $userTable->findAll();
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $code = $th->getCode();

            echo view("templates/header", array("title" => "Ooops"));

            // No such database
            if ($code === 1049) {
                echo view("system-wide/NoConnectionView", array(
                    "message" => $message,
                    "suggestion" => "Please check if the correct database name is present in .env file (database.default.database)"
                ));
            }

            // No database connection
            if ($code === 2002) {
                echo view("system-wide/NoConnectionView", array(
                    "message" => $message,
                    "suggestion" => "Please refer to the available documentation and check if an active database is currently running"
                ));
            }
            
            return false;
        }
	}
}
