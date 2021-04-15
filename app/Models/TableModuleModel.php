<?php namespace App\Models;

use CodeIgniter\Model;

class TableModuleModel extends Model
{
    protected $table      = 'tables_modules';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id', 'user_table_id', 'user_module_id'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    public function saveTablesModulesLink($userTableId, $setIds, $setNames, $setClasses, $setLabels) {
        $infosch = \Config\Database::connect("default");
        $propertiesModel = new PropertiesModel();
        $userTables = new UserTableModel();

        if (empty($userTableId))

        $column = null;
        $table = $userTables->getWhere(array("id" => $userTableId))->getResultArray()[0];

        // These go into properties table
        $properties = [];
        if (isset($setIds) && $setIds == true) $properties[] = ["id" => $table["column_name"]];
        if (isset($setNames) && $setNames == true) $properties[] = ["name" => $table["column_name"]];
        if (isset($setClasses) && $setClasses == true) $properties[] = ["class" => "form-control form-control-sm"];

        // This goes into tables_info.label
        if (isset($setLabels) && $setLabels == true) $userTables->update($userTableId, array("display_label" => $table["column_name"]));

        // Take the old values out
        $userTableId = (int) $userTableId;
        $propertiesModel->where(array("user_table_id" => $userTableId))->delete();

        // Prepare for batch insert
        $data = [];
        foreach ($properties as $index => $property) {
            foreach ($property as $key => $value) {
                $data[] = array(
                    "user_table_id" => $userTableId,
                    "property" => $key,
                    "attributes" => $value
                );
            }
        }

        // In with the new
        $result = $propertiesModel->insertBatch($data);
		return $result;
    }
}
