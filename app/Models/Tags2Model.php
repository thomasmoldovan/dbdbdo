<?php namespace App\Models;

use CodeIgniter\Model;

class Tags2Model extends Model
{
    protected $table      = 'tags2';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id","name","start_tag","end_tag","value_type","properties_id_list"];

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
        return ["id","name","start_tag","end_tag","value_type","properties_id_list"];
    }

    public function getTags2List() {
        $query = "SELECT 
                    tags2.id, tags2.name, tags2.start_tag, tags2.end_tag, tags2.value_type, tags2.properties_id_list
                FROM tags2 
                    ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    

    public function getPrimary() {
        return $this->primaryKey;
    }
}