<?php

namespace App\Controllers;

class Ajax extends Home {

	public function index()	{
		$check = $this->auth->check();
		if ($this->auth->check()) {
			 var_dump($this->auth->user());
             return $this->display_main("header", "todo");
		}
        return $this->display_main("header", "todo");
	}
}
