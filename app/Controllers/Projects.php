<?php

namespace App\Controllers;

class Projects extends Home{

	public function index()	{
		if ($this->auth->check()) {
			return $this->display_main("header", "projects");
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
		$processAllTables = $this->request->getPost("processAllTables");

		if (empty($name) || empty($data)) {
			return $this->response->setJSON(array(
				"status" => "error",
				"message" => "Something went wrong with the import"
			));
		}

		$projects = new \App\Models\ProjectModel();
		$rootConn = \Config\Database::connect("default");

		try {
			$importerConn = \Config\Database::connect("schemaImporter");
		} catch (\Exception $ex) {
			$response["status"] = "error";
			$response["code"] = 3481;
			$response["message"] = $ex->getMessage() ?? null;
			if (ENVIRONMENT === "Development") {
				$response["line"] = $ex->getLine() ?? null;
				$response["file"] = $ex->getFile() ?? null;
			}
			return $this->response->setJSON($response);
		}

		$userId = $this->user->id;
		$projectName = $name;
		$project_hash = "_".substr(uniqid(), -6);

		// Create database
		$result = null;
		$sql = "CREATE DATABASE `".$project_hash."`;";
		try {
			$result = $importerConn->query($sql);
		} catch (\Exception $ex)  {
			$response["status"] = "error";
			$response["code"] = 3481;
			$response["message"] = $ex->getMessage() ?? null;
			if (ENVIRONMENT === "Development") {
				$response["line"] = $ex->getLine() ?? null;
				$response["file"] = $ex->getFile() ?? null;
			}
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
				// Ignore line if DROP statement present
				$wtf = stripos($tempcommand, "drop");
				if (stripos($tempcommand, "drop") !== false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				// TODO: also USE DATABASE, but use in the first part, and can be user ie.

				// Ignore line if CREATE DATABASE statement present
				$wtf2 = stripos($tempcommand, "database");
				if (stripos($tempcommand, "database") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

				$wtf3 = stripos($tempcommand, "show");
				if (stripos($tempcommand, "show") != false) {
					$tempcommand = '';
					$line = strtok($separator);
					continue;
				}

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
					} catch (\Exception $e)  {
						if ($e->getCode() == 1142) {
							return $this->response->setJSON(array(
								"status" => "error",
								"message" => "You do not have permissions to run that command"
							));
						} else if ($e->getCode() == 1064) {
							return $this->response->setJSON(array(
								"status" => "error",
								"message" => "You have an error in your SQL script",
								"response" => $e->getMessage()
							));
						} else {
							return $this->response->setJSON(array(
								"status" => "error",
								"message" => $e->getMessage()
							));
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
			$resultId = $projects->insert([
				"id" => null,
				"user_id" => $userId,
				"project_name" => $projectName,
				"project_hash" => $project_hash,
				"database" => $project_hash]
			);
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
}
