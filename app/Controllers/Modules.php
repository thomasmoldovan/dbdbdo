<?php namespace App\Controllers;

use App\Models\PropertiesModel;
use App\Models\UserModuleModel;
use App\Models\TableModuleModel;

class Modules extends Home {
    
    public function deleteModule() {
        $post = $this->request->getPost();

        $user_modules = new UserModuleModel();
        $tables_modules = new TableModuleModel();

        // user_modules
        // tables_modules
        $user_modules->delete($post["module_id"]);
        $tables_modules->where(["user_module_id" => $post["module_id"]])->delete();

        return $this->response->setJSON($post);
    }
    
}
