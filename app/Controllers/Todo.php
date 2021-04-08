<?php

namespace App\Controllers;

class Todo extends Home {

	public function index()	{
		$check = $this->auth->check();
		if ($this->auth->check()) {
             return $this->display_main("header", "todo");
		}
        return $this->display_main("header", "todo");
	}
}
