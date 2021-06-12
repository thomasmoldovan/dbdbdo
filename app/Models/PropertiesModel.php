<?php namespace App\Models;

use CodeIgniter\Model;

class PropertiesModel extends Model
{
    protected $table      = 'properties';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["user_table_id", "property", "attributes"];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getFieldProperties($columnId) {
        $schema = new SchemaModel();
        $this->setDatabase("dbdbdo");
        $result = $schema->executeOuterQuery("dbdbdo", "SELECT property, attributes
                                        FROM properties
                                        WHERE properties.user_table_id = {$columnId};");

		return $result;
    }
}