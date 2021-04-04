<?php

namespace App\Controllers;

class Projects extends Home{

	public function index()	{
		if ($this->auth->check()) {
			return $this->display_main("header", "projects");
		}
		return false;
	}

	public function create() {
		if ($this->auth->check()) {
			return $this->display_main("header", "create");
		}
		return false;
	}
}
