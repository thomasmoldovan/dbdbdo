<?php namespace App\Controllers;

use App\Models\PropertiesModel;

// TODO: to have a label or not
// TODO: to show error message or not

// Feature: Autobuild
// Feature: Auto refresh view

class Modules extends Home {
    // Probably shoud be moved in PropertiesController
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
}
