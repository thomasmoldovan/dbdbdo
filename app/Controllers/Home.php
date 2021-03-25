<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
        session()->setFlashdata('message', lang("Pass.userNotFound"));
		return view('header', ["auth" => [], "projectList" => []]);
	}

	public function register()
	{
        // session()->setFlashdata('error', lang("Pass.activationSuccess"));
		return view('header', ["auth" => [], "projectList" => []]);
	}
}
