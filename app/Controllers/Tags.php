<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\ProjectModel;
use App\Models\UserTableModel;

use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class Tags extends Home {

	protected $current_project;

	public function index($hash = null)	{
		// Should be moved in Home


		if ($this->auth->check()) {
			$projects = new \App\Models\ProjectModel();

			return $this->display_main("header", "tags", []);
		}

		// Not logged - Redirect to root
		return redirect()->to("/");
	}
}
