<?php

namespace App\Controllers;

class Projects extends Home {

	protected $current_project;

	public function index()	{
		if ($this->auth->check()) {
			$projects = new \App\Models\ProjectModel();
			$project_list = $projects->getProjectsForUser($this->user->id);
			$data = [
				"project_list" => $project_list
			];
			return $this->display_main("header", "projects", $data);
		}
		return redirect()->to("/");
	}

	public function create() {
		if ($this->auth->check()) {
			return $this->display_main("header", "create");
		}
		return redirect()->to("/");
	}

	// Creates a DB with the provided schema
	public function importSchema($data = null, $name = null) {
		$name = $this->request->getPost("name");
		$data = $this->request->getPost("data");

		if (empty($name) || empty($data)) {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something values were empty"
			));
		}

		$projects = new \App\Models\ProjectModel();
		$rootConn = \Config\Database::connect("default");

		try {
			$importerConn = \Config\Database::connect("schemaImporter");
		} catch (\Exception $ex) {
			return $this->tried($ex);
		}

		$userId = $this->user->id;
		$projectName = $name;
		$project_hash = "_".substr(uniqid(), -6);

		// Add the project
		try {
			$this->current_project = $projects->insert([
				"id" => null,
				"user_id" => $userId,
				"project_name" => $projectName,
				"project_hash" => $project_hash,
				"database" => $project_hash]
			);
		} catch (\Exception $ex) {
			$project_hash = null;
			return $this->tried($ex);
		}

		// Create database
		$result = null;
		$sql = "CREATE DATABASE `".$project_hash."`;";
		try {
			$result = $importerConn->query($sql);
		} catch (\Exception $ex)  {
			return $this->tried($ex);
			return $this->response->setJSON($response);
		}

		// Script import
		$result = null;
		$importerConn->setDatabase($project_hash);
		$importerConn->query("SET FOREIGN_KEY_CHECKS = 0;");
		$importerConn->query("SET SQL_MODE = '';");

		$separator = "\r\n";
		$nrTables = 0;
		$tempcommand = "";

		$line = strtok($data, $separator);
		while ($line !== false) {
			// Skip if comment
			if (substr($line, 0, 2) == '--' || substr($line, 0, 2) == '/*' || substr($line, 0, 2) == '/*' || $line == '') continue;

			// Add this line to the current command
			$tempcommand .= $line;

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($tempcommand), -1, 1) == ';') {

				// TODO: Turn these import filters into a loop
				// $restricted_importer_keywords = ["drop", "database", "show", "privilege"];
				// foreach ($restricted_importer_keywords as $keyword) {
				// 	if (stripos($tempcommand, $keyword) !== false) {
				// 		$tempcommand = '';
				// 		$line = strtok($separator);
				// 		continue;
				// 	}
				// }

				// Ignore line if DATABASE statement present
				$wtf1 = stripos($tempcommand, "drop");
				if (stripos($tempcommand, "database") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf2 = stripos($tempcommand, "database");
				if (stripos($tempcommand, "database") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf3 = stripos($tempcommand, "show");
				if (stripos($tempcommand, "show") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Ignore line if DATABASE statement present
				$wtf4 = stripos($tempcommand, "privilege");
				if (stripos($tempcommand, "privilege") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// Perform the query
				if (!empty($tempcommand)) {
					$nrTables++;
					try {
						// No INSERTS
						$result = $importerConn->query($tempcommand);
					} catch (\Exception $ex)  {
						if ($ex->getCode() == 1142) {
							return $this->response->setJSON(array(
								"status" => "error",
								"code" => $ex->getCode(),
								"message" => "You do not have permissions to run that command"
							));
						} else if ($ex->getCode() == 1064) {
							return $this->response->setJSON(array(
								"status" => "error",
								"code" => $ex->getCode(),
								"message" => "You have an error in your SQL script",
							));
						} else {
							return $this->tried($ex);
						}
					}
				}

				$tempcommand = '';
			}

			$line = strtok($separator);
		}

		if (is_null($result) || $nrTables == 0) {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong executing the SQL script"
			));
		}

		if (!is_null($result) && $nrTables > 0) {
			return $this->response->setJSON([
				"project_hash" => $project_hash
			]);
		} else {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong executing the SQL script"
			));
		}
	}

	public function clearStuff() {
		if (is_null($this->user->id)) return false;
		$rootConn = \Config\Database::connect("default");

		// DROP all the user databases
		$dbs = $rootConn->query("SELECT GROUP_CONCAT(CONCAT('`', project_hash, '`')) AS projects FROM projects;")->getResultArray()[0]["projects"];

		// TODO: Delete files also

		// $result = $rootConn->query("TRUNCATE user_tables");
		// $result = $rootConn->query("TRUNCATE user_modules");
		// $result = $rootConn->query("TRUNCATE projects");
		// $result = $rootConn->query("TRUNCATE tables_modules");
		// $result = $rootConn->query("TRUNCATE properties");
		// $result = $rootConn->query("TRUNCATE links");
		if (!empty($dbs)) {
			$result = $rootConn->query("TRUNCATE projects");
			$result = $rootConn->query("DROP DATABASE ".$dbs.";");
		}		
		
		$this->session->set("notification", ["success" => "All your projects have been wiped out"]);
		return redirect()->to('/projects');
	}
}
