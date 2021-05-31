<?php namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table      = 'projects';
    protected $primaryKey = 'id';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = ["id", "user_id", "database", "project_name", "project_description", "project_type", "project_hash"];

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
        return ["ID", "User ID", "DB Name", "Project Name", "Project Description", "Project Type", "Project Hash"];
    }

    public function getProjectsForUser($user_id) {
        $result = $this->query("SELECT user_tables.project_id,
                                        projects.id,
                                        projects.project_hash,
                                        projects.project_name,
                                        projects.project_description,
                                        COUNT(user_tables.table_name) AS count_table_name,
                                        tables_modules.id AS module_id,
                                        tables_modules.enabled,
                                        projects.updated_at,
                                        tables_modules.user_table_id,
                                        links_primary.user_table_id_primary AS primary_link,
                                        links_foreign.user_table_id_foreign AS foreign_link
                                FROM (((dbdbdo.user_tables user_tables
                                        LEFT OUTER JOIN dbdbdo.links links_primary
                                            ON (user_tables.id = links_primary.user_table_id_primary))
                                        RIGHT OUTER JOIN dbdbdo.projects projects
                                        ON (projects.id = user_tables.project_id))
                                    LEFT OUTER JOIN dbdbdo.tables_modules tables_modules
                                        ON (user_tables.id = tables_modules.user_table_id))
                                    LEFT OUTER JOIN dbdbdo.links links_foreign
                                        ON (tables_modules.user_table_id =
                                            links_foreign.user_table_id_foreign)
                                GROUP BY projects.id, user_tables.table_name")->getResult();
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