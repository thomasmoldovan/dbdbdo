<?php namespace App\Models;

use CodeIgniter\Model;

class TagsModel extends Model
{
    protected $table      = 'tags';
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

    public function getTagsList() {
        $query = "SELECT 
                    tags.id, tags.name, tags.start_tag, tags.end_tag, tags.value_type, tags.properties_id_list
                FROM tags 
                    ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    

    public function getPrimary() {
        return $this->primaryKey;
    }
}