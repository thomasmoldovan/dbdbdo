<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		// $auth = $this->auth = service('authentication');
		return view('header', ["auth" => [], "projectList" => []]);
		// return view('welcome_message');
	}
}
