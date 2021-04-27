<?php namespace App\Models;

use CodeIgniter\Model;

class BucketModel extends Model
{
    protected $table      = 'bucket';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id","fCheckbox","fRadio"];

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
        return ["id","fCheckbox","fRadio"];
    }

    public function getBucketList() {
        $query = "SELECT 
                    bucket.id, bucket.fCheckbox, bucket.fRadio
                FROM bucket 
                    ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    

    public function getPrimary() {
        return $this->primaryKey;
    }
}