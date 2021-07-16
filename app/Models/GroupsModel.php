<?php namespace App\Models;

use CodeIgniter\Model;

class GroupsModel extends Model
{
    protected $table      = 'groups';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id","name","color_id"];

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
        return ["id","name","Color Name"];
    }

    public function getGroupsList() {
        $query = "SELECT 
                    groups.id, groups.name, colors.name AS `Color Name`
                FROM groups 
                     LEFT JOIN colors ON colors.id = groups.color_id ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    public function getAllColors() {
                $query = "SELECT `id`, 
                                 `name` 
                            FROM `colors`;";
                $result = $this->query($query)->getResultArray();
                return $result;
            }

    public function getPrimary() {
        return $this->primaryKey;
    }
}