<?php

namespace App\Controllers;

class Home extends BaseController{
	protected $auth;
	/**
	 * @var Auth
	 */
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
		helper('auth');
		$this->authorize = service('authorization');
		$this->session = service('session');
		$this->auth = service('authentication');
		$this->config = config('Auth');
		$this->pages = config('Pages');
		$this->user = user();
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
}
