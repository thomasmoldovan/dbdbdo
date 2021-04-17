<?php

namespace App\Controllers;

class Home extends BaseController{
	public $notifications;
	
	protected $auth;
	protected $config;
	protected $user;
	protected $pages;
	protected $authorize;

	/**
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	public function __construct()
	{
		// Most services in this controller requires
		// the session to be started - so fire it up!
		helper('html');
		helper('auth');
		$this->session = service('session');
		$this->authorize = service('authorization');
		$this->auth = service('authentication');
		$this->config = config('Auth');
		$this->pages = config('Pages');
		$this->user = user();

		if (isset($_SESSION["notification"]) && is_array($_SESSION["notification"])) {
			$this->notifications = $_SESSION["notification"];
		}
	}

	public function showHome() {
		return $this->response->setStatusCode(200)
							  ->setBody(true);
	}

	public function index()	{
		$check = $this->auth->check();
		if ($this->auth->check()) {
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

	public function display_main($header = "header", $content = "login", $data = []) {
		return view($header, [
			"auth" => $this->auth, 
			"config" => $this->config,
			"user" => $this->user,
			"page" => $this->pages->pages[$content]["view"],
			"data" => $data
		]);
	}

	public function tried(\Exception $ex) {
		$response["status"] = "error";
		$response["code"] = 3481;
		$response["message"] = $ex->getMessage() ?? null;
		if (ENVIRONMENT === "Development") {
			$response["line"] = $ex->getLine() ?? null;
			$response["file"] = $ex->getFile() ?? null;
		}
		return $this->response->setJSON($response);
	}
}
