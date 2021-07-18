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

    public function getInnerProjectTables($project_id) {
        $temp = $this->query("SELECT user_tables.table_name FROM user_tables
                                LEFT JOIN projects ON projects.id = user_tables.project_id 
                                WHERE projects.project_type = 1 AND user_tables.project_id = {$project_id}
                                GROUP BY user_tables.table_name")->getResultArray();
        $result = [];
        foreach ($temp as $name) {
            $result[] = $name["table_name"];
        }

        return $result;
    }

    public function getProjectsForUser($user_id) {
        $query = "SELECT    user_tables.project_id,
                            projects.id,
                            projects.project_hash,
                            projects.project_name,
                            projects.project_type,
                            projects.project_description,
                            COUNT(user_tables.table_name) AS count_table_name,
                            tables_modules.id AS module_id,
                            tables_modules.enabled,
                            projects.updated_at,
                            tables_modules.user_table_id,
                            links_primary.user_table_id_primary AS primary_link,
                            links_foreign.user_table_id_foreign AS foreign_link
                    FROM (((user_tables user_tables
                            LEFT OUTER JOIN links links_primary
                                ON (user_tables.id = links_primary.user_table_id_primary))
                            RIGHT OUTER JOIN projects projects
                            ON (projects.id = user_tables.project_id))
                        LEFT OUTER JOIN tables_modules tables_modules
                            ON (user_tables.id = tables_modules.user_table_id))
                        LEFT OUTER JOIN links links_foreign
                            ON (tables_modules.user_table_id =
                                links_foreign.user_table_id_foreign)
                    WHERE projects.user_id = {$user_id}
                    GROUP BY projects.id, user_tables.table_name";
        // var_dump($query); die();
        $result = $this->query($query)->getResult();

        $projects_array = [];
        $project_list = [];
        foreach ($result as $project) {
            if (!in_array($project->project_hash, $projects_array)) {
                $projects_array[] = $project->project_hash;

                $project_list[$project->project_hash] = new \stdClass();
                $project_list[$project->project_hash]->id = $project->id;
                $project_list[$project->project_hash]->enabled = $project->enabled;
                $project_list[$project->project_hash]->count_table_name = (int)$project->count_table_name;
                $project_list[$project->project_hash]->count_column_name = $project->count_table_name;
                $project_list[$project->project_hash]->count_modules = 0;
                $project_list[$project->project_hash]->count_links = $project->primary_link || $project->foreign_link ? 1 : 0;
                $project_list[$project->project_hash]->project_description = $project->project_description;
                $project_list[$project->project_hash]->project_name = $project->project_name;
                $project_list[$project->project_hash]->project_type = $project->project_type;
                $project_list[$project->project_hash]->project_hash = $project->project_hash;
                $project_list[$project->project_hash]->project_id = $project->project_id;
                $project_list[$project->project_hash]->updated_at = $project->updated_at;

                if ((int)$project->module_id > 0) {
                    $project_list[$project->project_hash]->count_modules++;
                }
            } else {
                $project_list[$project->project_hash]->count_table_name++;
                $project_list[$project->project_hash]->count_column_name += $project->count_table_name;

                if ((int)$project->module_id > 0) {
                    $project_list[$project->project_hash]->count_modules++;
                }
                $project_list[$project->project_hash]->count_links += $project->primary_link || $project->foreign_link ? 1 : 0;
            }
        }

        return $project_list;
    }

    public function checkProjectBelongsToUser($project_hash = null, $user_id = null) {
        if (is_null($project_hash) || is_null($user_id)) return false;
        // Both must match to get the project_hash, for security reasons
        $result = $this->select("id, project_hash, project_name")->where([
                            "project_hash" => $project_hash,
                            "user_id" => $user_id
                        ])->first();
        return $result;
    }
}