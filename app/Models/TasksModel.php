<?php namespace App\Models;

use CodeIgniter\Model;

class TasksModel extends Model
{
    protected $table      = 'tasks';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id","group_id","taskname","color_id","complete"];

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
        return ["ID","Group","Task","Color","Complete"];
    }

    public function getTasksList() {
        $query = "SELECT 
                    tasks.id, groups.name AS `Group`, tasks.taskname, colors.name AS `Color`, tasks.complete
                FROM tasks 
                     LEFT JOIN groups ON groups.id = tasks.group_id  LEFT JOIN colors ON colors.id = tasks.color_id ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    

    public function getPrimary() {
        return $this->primaryKey;
    }
}