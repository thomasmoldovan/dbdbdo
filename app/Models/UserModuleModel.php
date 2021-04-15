<?php namespace App\Models;

use CodeIgniter\Model;

class UserModuleModel extends Model
{
    protected $table      = 'user_modules';
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = ["id", "module_name", "module_title", "module_type", "module_route", "module_icon", "show_on_menu", "add_to_routes", "locked", "created_at", "updated_at", "deleted_at"];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $DBGroup = "default";

    public function getAllowedFields() {
        return $this->allowedFields;
    }

    public function getFieldLabels() {
        return false;
        return ["ID", "Module Name", "Module Title", "Type", "Route"];
    }

    public function getModuleColumns($name = null) {
		$where = "";
		if (!is_null($name)) {
			$where = " WHERE user_modules.module_name = '{$name}'";
		}

		$infosch = \Config\Database::connect("default");
		$query = "SELECT  user_tables.id AS user_table_id,
                            user_tables.table_name,
                            user_tables.column_name,
                            user_tables.pk,
                            user_tables.type,
                            user_tables.display_label AS display_label,
                            user_tables.display_as AS display_as,
                            user_tables.enabled AS column_enabled,
                            links.id AS link_id,
                            CONCAT(`primary`.table_name, ".'"."'.", `primary`.column_name) AS 'primary',
                            CONCAT(`foreign`.table_name, ".'"."'.", `foreign`.column_name) AS 'foreign',
                            CONCAT(`display`.table_name, ".'"."'.", `display`.column_name) AS 'display',
                            links.link_type,
                            links.enabled,
                            tables_modules.user_module_id,
                            user_modules.module_name
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

	public function getActiveModules($userId = null) {
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
									WHERE user_modules.show_on_menu = 1 AND user_tables.user_id = {$userId}
                                    GROUP BY user_modules.id;");

		return $result->getResultArray();
	}
}