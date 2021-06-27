<?php namespace App\Models;

use CodeIgniter\Model;

class LinksModel extends Model
{
    protected $table      = 'links';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id", "user_table_id_primary", "user_table_id_foreign", "user_table_id_display", "link_type", "enabled"];

    protected $useTimestamps = false;
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

    public function getAllLinks($link = null, $returnType = "array") {
        $dbConn = \Config\Database::connect("default");
        $query = "SELECT links.id AS link_id,
                        links.user_table_id_primary,
                        links.user_table_id_foreign,
                        links.user_table_id_display,
                        links.link_type,
                        user_tables.id,
                        user_tables.table_name,
                        user_tables.column_name,
                        user_tables.type,
                        links.enabled
                    FROM ((links links
                        LEFT OUTER JOIN user_tables ON (links.user_table_id_display = user_tables.id))
                        INNER JOIN user_tables `primary` ON (`primary`.id = links.user_table_id_primary))
                        INNER JOIN user_tables `foreign` ON (`foreign`.id = links.user_table_id_foreign) ";

        if (!is_null($link)) $query .= " WHERE (`primary`.table_name = '{$link['table_name']}')
                                            AND (`primary`.column_name = '{$link['table_column']}')
                                            AND (`foreign`.table_name = '{$link['key_table']}')
                                            AND (`foreign`.column_name = '{$link['key_column']}')
                                            AND (`primary`.id = '{$link['table_id']}')
                                            AND (`foreign`.id = '{$link['key_id']}')";

        $result = $dbConn->query($query);

        if ($returnType == "array") {
            return $result->getResultArray();
        } else {
            return $result->getResult();
        }
    }
}
