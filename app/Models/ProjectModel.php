<?php namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table      = 'projects';
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id", "user_id", "database", "project_name", "project_hash"];

    protected $useTimestamps = true;
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
        return ["ID", "User ID", "DB Name", "Project Name", "Project Hash"];
    }

    public function getProjectsForUser($user_id) {
        $result = $this->where(["user_id" => $user_id])->findAll();
        return $result;
    }

    public function checkProjectBelongsToUser($project_hash, $user_id) {
        // Both must match to get the project_hash, for security reasons
        $result = $this->select("id, project_hash, project_name")->where([
                            "project_hash" => $project_hash,
                            "user_id" => $user_id
                        ])->first();
        return $result;
    }
}