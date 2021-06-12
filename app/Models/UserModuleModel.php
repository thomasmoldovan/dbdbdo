<?php namespace App\Models;

use CodeIgniter\Model;

class UserModuleModel extends Model
{
    protected $table      = 'user_modules';
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id", "project_id", "module_name", "module_title", "module_type", "module_route", "module_icon", "show_on_menu", "add_to_routes", "locked", "last_build", "created_at", "updated_at", "deleted_at"];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $DBGroup = "default";
    public $projectId = "";

    public function getAllowedFields() {
        return $this->allowedFields;
    }

    public function getFieldLabels() {
        return false;
        return ["ID", "Project ID", "Module Name", "Module Title", "Type", "Route", "Last Build"];
    }

    public function getModuleColumns($name = null) {
		$where = "";
		if (!is_null($name)) {
			$where = " WHERE user_modules.module_name = '{$name}'";
		}

        if ($this->projectId != "") {
			$where = " WHERE user_tables.project_id = '{$this->projectId}'";
		}

		$infosch = \Config\Database::connect("default");
		$query = "SELECT  user_tables.id AS user_table_id,
                            user_tables.project_id AS project_id,
                            user_tables.table_name,
                            user_tables.column_name,
                            user_tables.pk,
                            user_tables.type,
                            user_tables.display_label AS display_label,
                            user_tables.display_as AS display_as,
                            CONCAT(`primary`.table_name, ".'"."'.", `primary`.column_name) AS 'primary',
                            CONCAT(`foreign`.table_name, ".'"."'.", `foreign`.column_name) AS 'foreign',
                            CONCAT(`display`.table_name, ".'"."'.", `display`.column_name) AS 'display',
                            links.id AS link_id,
                            links.link_type AS link_type,
                            links.enabled AS link_enabled,
                            tables_modules.user_module_id,
                            tables_modules.enabled AS column_enabled,
                            user_modules.id,
                            user_modules.module_name,
                            user_modules.module_route,
                            user_modules.locked
                    FROM (((tables_modules tables_modules
                            INNER JOIN user_modules user_modules ON (tables_modules.user_module_id = user_modules.id))
                            INNER JOIN user_tables user_tables ON (user_tables.id = tables_modules.user_table_id))
                            LEFT OUTER JOIN links links ON (user_tables.id = links.user_table_id_foreign))
                            LEFT JOIN user_tables `primary` ON primary.id = links.user_table_id_primary
                            LEFT JOIN user_tables `foreign` ON foreign.id = links.user_table_id_foreign
                            LEFT JOIN user_tables `display` ON display.id = links.user_table_id_display

                            {$where}
                    ORDER BY user_table_id ASC";

		$result = $infosch->query($query);
		return $result->getResultArray();
	}

	public function getActiveModules($user_id = null, $project_hash = null) {
        if (is_null($user_id) || is_null($project_hash)) return false;
		$infosch = \Config\Database::connect("default");
        $result = $infosch->query("SELECT 
                                    user_modules.id,
                                    user_modules.module_title,
                                    user_modules.module_route,
                                    user_modules.module_icon,
                                    user_modules.show_on_menu 
                                FROM
                                    user_tables 
                                    LEFT JOIN tables_modules ON tables_modules.user_table_id =  user_tables.id
                                    LEFT JOIN user_modules ON user_modules.id = tables_modules.user_module_id
                                    LEFT JOIN projects ON projects.id = user_tables.project_id
									WHERE user_modules.show_on_menu = 1 
                                        AND user_tables.user_id = {$user_id}
                                        AND projects.project_hash = '{$project_hash}'
                                GROUP BY user_modules.id;");

		return $result->getResultArray();
	}
}