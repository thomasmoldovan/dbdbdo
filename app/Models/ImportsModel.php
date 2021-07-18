<?php namespace App\Models;

use CodeIgniter\Model;

class ImportsModel extends Model
{
    protected $table      = 'ddd_imports';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id","user_id","query","result","run_at","approved"];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getAllowedFields() {
        return $this->allowedFields;
    }

    public function getFieldLabels() {
        return ["id","user_id","query","result","run_at","approved"];
    }

    public function getImportsList() {
        $query = "SELECT 
                    ddd_imports.id, ddd_imports.user_id, ddd_imports.query, ddd_imports.result, ddd_imports.run_at, ddd_imports.approved
                FROM imports 
                    ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    

    public function getPrimary() {
        return $this->primaryKey;
    }
}