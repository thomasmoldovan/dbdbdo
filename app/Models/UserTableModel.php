<?php namespace App\Models;

use CodeIgniter\Model;

class UserTableModel extends Model
{
    protected $table      = 'user_tables';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id', 'user_id', 'project_id', 'table_name', 'column_name', 'display_label', 'display_as', 'type', 'pk', 'default', 'null', 'ai', 'permissions', 'comment', 'checksum'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
	protected $skipValidation     = true;
	
	protected $database = "online";
  
  	public function getModulesColumns() {
    	$infosch = \Config\Database::connect("default");
		$result = $infosch->query("SELECT user_tables.id,
										  user_tables.table_name AS 'TABLE_NAME',
                                      	  user_tables.column_name AS 'COLUMN_NAME'
											FROM
												user_tables
												LEFT JOIN tables_modules 
													ON tables_modules.user_table_id = user_tables.id
												LEFT JOIN user_modules 
													ON user_modules.id = tables_modules.user_module_id
											WHERE user_modules.module_name IS NOT NULL");

		return $result->getResultArray();
	}

	public function getIdByTableAndColulmn($tableName = null, $columnName = null) {
		$infosch = \Config\Database::connect("default");
		$result = $infosch->query("SELECT id
                                        FROM user_tables
                                   WHERE table_name = '{$tableName}' AND column_name='{$columnName}'");

		return $result->getResult()[0]->id;
    }
    
    public function getColumnsForTable($table) {
        $infosch = \Config\Database::connect("default");
		$result = $infosch->query("SELECT user_tables.id, user_tables.table_name AS 'TABLE_NAME', user_tables.column_name AS 'COLUMN_NAME'
                                        FROM user_tables
                                    WHERE TABLE_NAME = '".$table."'");

		return $result->getResultArray();
	}

	public function getTableInfoSettings($user_table_id = null) {
		if (is_null($user_table_id) || empty($user_table_id)) return false;
			$result = $this->getWhere(array("id" => $user_table_id));
			return $result->getResultArray()[0];
		}
}
