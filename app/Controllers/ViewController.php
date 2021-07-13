<?php namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\PropertiesModel;

class ViewController extends HomeController {

	public function index($table = "groups")	{
		checkIfLogged();
		
        return "View";
	}
}
