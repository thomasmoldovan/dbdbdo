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
        return ["id","group_id","taskname","color_id","complete"];
    }

    public function getTasksList() {
        $query = "SELECT 
                    tasks.id, groups.name AS `group_id`, tasks.taskname, colors.name AS `color_id`, tasks.complete
                FROM tasks 
                     LEFT JOIN groups ON groups.id = tasks.group_id  LEFT JOIN colors ON colors.id = tasks.color_id ";

        $result = $this->query($query)->getResultArray();
        // TODO: WHERE and GROUP and ORDER

        return $result;
    }

    public function getAllGroups() {
                $query = "SELECT `id`, 
                                 `name` 
                            FROM `groups`;";
                $result = $this->query($query)->getResultArray();
                return $result;
            }public function getAllColors() {
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