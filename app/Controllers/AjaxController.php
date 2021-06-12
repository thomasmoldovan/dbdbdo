<?php

namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\PropertiesModel;

class AjaxController extends HomeController {

	public function index()	{
		$check = $this->auth->check();
		if ($check) {
			//  var_dump($this->auth->user());
            //  return $this->display_main("header", "todo");
            return $this->response->setJSON(array("post" => $this->request->getPost()));
			// return "AJAX";
		}
        // return $this->display_main("header", "todo");
		return "AJAX";
	}

	public function ajaxSaveProperties() {
        $post = $this->request->getPost();
        if ($this->request->isAjax() && isset($post["columnId"])) {
            // UPDATE properties table with columnId
            $columnId = $post["columnId"];
            unset($post["columnId"]);

            $data = [];
            foreach($post as $key => $value) {
                $data[] = array(
                    "id" => null,
                    "user_table_id" => $columnId,
                    "property" => $key,
                    "attributes" => $value
                );
            }

            $properties = new PropertiesModel();
            $properties->where(array("user_table_id" => $columnId))->delete();
            if (!empty($data)) {
                $result = $properties->insertBatch($data);
                return $this->response->setJSON(array("saved" => $result));
            } else {
                return $this->response->setJSON(array("cleared" => true));
            }
        } else {
            echo "Another type of save";
            die();
        }
    }

    public function ajaxLoadProperties() {
        $post = $this->request->getPost();
        if ($this->request->isAjax() && isset($post["columnId"])) {
            // READ properties for columnId
            $columnId = $post["columnId"];
            unset($post["columnId"]);

            $properties = new PropertiesModel();
            $props = $properties->getFieldProperties($columnId);

            $results = [];
            foreach($props as $key => $value) {
                $results[$value["property"]] = $value["attributes"];
            }

            return $this->response->setJSON(array("properties" => array($results)));
        }
    }

    public function autosave() {
        $post = $this->request->getPost();

        $params = base64_decode($post["param"]);
        $value = $post["value"];
        $data = json_decode($params);
        $id = $data->id;
        unset($data->id);
        foreach ($data as $table => $column) break;

		$schemaModel = new SchemaModel();
        $query = "UPDATE {$table} SET {$column} = '{$value}' WHERE id = {$id}";
        $result = $schemaModel->executeInnerQuery($_ENV["database.default.database"], $query);

        return $this->response->setJSON($result);
    }
}
