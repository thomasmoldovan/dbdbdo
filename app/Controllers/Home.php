<?php

namespace App\Controllers;

class Home extends BaseController{
	protected $auth;
	/**
	 * @var Auth
	 */
	protected $config;

	/**
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	public function __construct()
	{
		// Most services in this controller require
		// the session to be started - so fire it up!
		$this->session = service('session');

		$this->config = config('Auth');
		$this->auth = service('authentication');
	}

	public function index()
	{
        // session()->setFlashdata('message', lang("Auth.userNotFound"));
		return view('header', [
			"auth" => $this->auth, 
			"config" => $this->config]);
	}

	public function register()
	{
        // session()->setFlashdata('error', lang("Pass.activationSuccess"));
		echo view('header', ["auth" => [], "projectList" => []]);
		echo view('pass/register', ["auth" => [], "projectList" => []]);
	}
}
