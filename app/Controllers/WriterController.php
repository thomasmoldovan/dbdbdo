<?php namespace App\Controllers;

use App\Models\SchemaModel;
use App\Models\UserModuleModel;
use App\Models\UserTableModel;
use App\Models\PropertiesModel;
use App\Models\ProjectModel;
use \Gajus\Dindent\Indenter;
use CodeIgniter\Controller;

class WriterController extends Home {

    protected $export_type = "internal";

    public function writeFromTemplate() {
        // $table_info = new UserTableModel();
        
        // Check if auth()
        // Check project hash
        
        // $projectId = $this->session->get("projectId");
        // $projectHash = $projects->checkProjectBelongsToUser($projectId, $this->user->id)->project_hash;
        if (!logged_in()) return redirect()->to('login');
        
        $modulesModel = new UserModuleModel();
        $projects = new ProjectModel();

        $post = $this->request->getPost();
        $post["project_type"] = $post["project_type"] == "true" ? "Internal" : "External";

        if (empty($post["module_name"])) return false;
        $project = $projects->getWhere(["user_id" => $this->user->id, "project_hash" => $post["project_hash"]])->getResultArray();
        $this->current_project = $project[0];

        $module = $modulesModel->getModuleColumns($post["module_name"]);
        // $addToRoutes = (bool) $module[0]["add_to_routes"];

        // Attaches the settings for each column
        // foreach ($module as $key => $value) {
        //     if ((int)$value["link_id"] > 0) {
        //         $module[$key]["settings"] = $table_info->getTableInfoSettings($value["user_table_id_display"]);
        //     } else {
        //         $module[$key]["settings"] = null;
        //     }
        // }

        $data = array(
            "uc_model_name" => ucwords($module[0]["module_name"]),
            "model_name" => $module[0]["module_name"],
            "table_name" => $module[0]["table_name"],
            "primary_key"  => null, // TODO: Maybe the primary columns is not the first, and does not contain ID
            "allowed_fields" => [],
            "field_labels" => [],
            "linked_fields" => []
        );

        $links = [];
        $data["linked_fields"] = [];
        foreach ($module as $key => $module_column) {

            if ($module_column["pk"] == "1") {
                $data["primary_key"] = $data["primary_key"] ? $data["primary_key"] : $module_column["column_name"]; // ???
            }

            if ($module_column["column_enabled"] !== "1") {}
            else {
                // If we have a link, then we remove that value from allowed fields and replace with a join
                if ((int) $module_column["link_id"] >= 1) {
                    $links[] = $module_column;

                    // We replace the data with the primary one
                    $data["linked_fields"][] = $module_column["display"]." AS `".$module_column["display_label"]."`";
                } else {
                    $data["linked_fields"][] = $module_column["table_name"].".".$module_column["column_name"];
                }

                if ($module_column["column_enabled"] == "1") {
                    $data["allowed_fields"][] = $module_column["column_name"];
                    $data["field_labels"][] = $module_column["display_label"];
                }
            }            
        }

        $data["joined_tables"] = "";
        $data["field_joins"] = "";
        $data["get_all_functions"] = "";
        foreach($links as $link) {
            $data["joined_tables"] .= "$"."data['join".ucwords(explode(".", $link["primary"])[0])."'] = $".explode(".", $link["foreign"])[0]."->getAll".ucwords(explode(".", $link["primary"])[0])."(); "; // {{joined_tables}}
                                       // $data['joinColors'] = $groups->getAllColors(); 
            $data["field_joins"] .= " LEFT JOIN ".explode(".", $link["primary"])[0]." ON ".$link["primary"]." = ".$link["foreign"]." ";

            $data["get_all_functions"] .= 'public function getAll'.ucwords(explode(".", $link["primary"])[0]).'() {
                $query = "SELECT `'.explode(".", $link["primary"])[1].'`, 
                                 `'.explode(".", $link["display"])[1].'` 
                            FROM `'.explode(".", $link["primary"])[0].'`;";
                $result = $this->query($query)->getResultArray();
                return $result;
            }';
        }
        // $query .= " JOIN colors ON colors.id = {{model_name}}.color_id ";

        helper(['filesystem', 'form', 'url']);

        $formInput = "<form>";
        $updateFields = [];
        $prepareFields = [];

        $isAjax = $this->request->isAjax();

        foreach($module as $column) {
            $id = $column["user_table_id"];
            $module_name = $column["module_name"];
            $table_name = $column["table_name"];
            $column_name = $column["column_name"];
            $label = $column["display_label"];
            $display_as = $column["display_as"];
            $pk = $column["pk"];
            $type = $column["type"];
            $column_enabled = $column["column_enabled"];

            if ($column_enabled != "1") continue; // ???

            // TODO: Label on/off option
            if ($label) {
                $formInput .= "<label for='{$column_name}' class='w-100 mb-0 bold pr-3'>{$column['display_label']}</label>";
            } else continue;

            $properties = new PropertiesModel();
            $fieldProperties = $properties->getFieldProperties($id);

            if ($pk == "1") {
                // $column is primary key
                $fieldProperties[] = [
                    "property" => "readonly",
                    "attributes" => ""
                ];
            }

            if ($pk != "1") {

                // CHECK IF FOREIGN
                if (!empty($column["link_id"])) {
                    // Get the data for this KEY                    
                    $schema = new SchemaModel();

                    $primary = $column["primary"];
                    $primary_table = explode(".", $column["primary"])[0];
                    $primary_column = explode(".", $column["primary"])[1];
                    $foreign = $column["foreign"];
                    $foreign_table = explode(".", $column["foreign"])[0];
                    $foreign_column = explode(".", $column["foreign"])[1];
                    $display = $column["display"];
                    $display_table = explode(".", $column["display"])[0];
                    $display_column = explode(".", $column["display"])[1];
                    
                    $dropdownJoinQuery = "SELECT {$primary},
                                                 {$foreign},
                                                 {$display}
                                          FROM {$primary_table}
                                          LEFT JOIN {$foreign_table} ON {$foreign} = {$primary}
                                          GROUP BY {$foreign} ORDER BY {$primary} ASC";

                    if ($post["project_type"] == "Internal") {
                        // Internal: The data for the table is taken from out database
                        $resultDropdownJoin = $schema->executeInnerQuery($_ENV["database.default.database"], $dropdownJoinQuery, "array");
                    } else {
                        // External: The data for the table is taken from the users database
                        $resultDropdownJoin = $schema->executeOuterQuery("_".$this->current_project["project_hash"], $dropdownJoinQuery, "array");
                    }

                    // CREATE THE OPTIONS LIST
                    $options = "";
                    
                    if ($column["link_type"] == 1) {
                        // STATIC
                        foreach ($resultDropdownJoin as $key => $value) {
                            $options .= $this->theSwitch(array(
                                array("property" => "value",
                                      "attributes" => $value[$primary_column])), "option", $value[$display_column]);
                        }
                    } elseif ($column["link_type"] == 1) {
                        // DYNAMIC

                    }

                    // CREATE THE SELECT
                    $test = $this->theSwitch($fieldProperties, "select", $options);
                    $formInput .= $test;

                    // This is javascript
                    $updateFields[] = "$(\"select[value='\" + {$foreign_table}Data.id + \"']\").attr(\"selected\", \"selected\");";
                    // $("select[value='\" + {$foreign_table}Data.id + \"']").attr("selected", "selected");
                } else {
                    // THE SWITCH WAS HERE
                    $formInput .= $this->theSwitch($fieldProperties, $display_as);

                    // These are jQuery lines that update the modal
                    if ($display_as == "checkbox") {
                        $updateFields[] = "$(\"input[id='{$column_name}']\").bootstrapToggle({$module_name}Data.{$module_name}_{$column_name} == 1 ? 'on' : 'off');";
                        $prepareFields[] = "if (preData['{$column_name}'] === undefined ) preData.push({ 'id' : '{$column_name}', 'value': $('input[id=\"{$column_name}\"]').prop('checked') ? 1 : 0 });";
                    } else {
                        $updateFields[] = "$(\"input[id='{$column_name}']\").val({$module_name}Data.{$column_name});";
                    }
                }
            } else {
                // We use this DIV for the KEY. Should not be anything else
                $start_tag = "<div ";
                $end_tag = "</div>";
                $formInput .= $start_tag;
                $formInput .= $this->propsToString($fieldProperties).">";
                $formInput .= $end_tag.PHP_EOL;

                // These are jQuery lines that update the modal
                $updateFields[] = "$(\"input[id='{$column_name}']\").val({$module_name}Data.{$column_name});";
            }
        }
        $updateFields = implode(PHP_EOL, $updateFields); // THIS
        $prepareFields = implode(PHP_EOL, $prepareFields); // THIS
        $formInput .= "</form>";

        $file  = file_get_contents("../App/Templates/{$this->export_type}/CodeIgniter4/ci_4_model.raw");
        $file2 = file_get_contents("../App/Templates/{$this->export_type}/CodeIgniter4/ci_4_view.raw");
        $file3 = file_get_contents("../App/Templates/{$this->export_type}/CodeIgniter4/ci_4_controller.raw");
        $file4 = file_get_contents("../App/Templates/{$this->export_type}/CodeIgniter4/ci_4_route.raw");

        if ($post["project_type"] == "External") $file3 = str_replace("// {{external}} ", "", $file3);

        // if ($column["link_type"] == 1) $file3 = str_replace("// {{static}} ", "", $file3);
        // if ($column["link_type"] == 2) $file3 = str_replace("// {{dynamic}} ", "", $file3);

        // Constructing the joined columns, the ones that are set in display
        $linked_fields =  implode(", ", $data["linked_fields"]);
        $file = str_replace("{{linked_fields}}", $linked_fields, $file);
        $file = str_replace("{{field_joins}}", $data["field_joins"], $file);
        if (count($data["linked_fields"]) > 0) {
            // TODO: Needs to be able to handle more than one join on a table
            // foreach ($data["joined_tables"])            
            $file3 = str_replace("{{joined_tables}}", $data["joined_tables"], $file3);
        } else {
            $file3 = str_replace("{{joined_tables}}", "", $file3);
        }
        unset($data["linked_fields"]);

        foreach ($data as $key => $value) {
            if (is_array($value)) $array = implode('","', $value);
            
            $result[$key] = strpos($file, $key);
            $file  = str_replace("{{".$key."}}", is_array($value) ? '"'.(string)$array.'"' : $value, $file);

            $file2 = str_replace("{{".$key."}}", is_array($value) ? '"'.(string)$array.'"' : $value, $file2);
            $file2 = str_replace("{{form_body}}", $formInput, $file2);
            $file2 = str_replace("{{update_fields}}", (string)$updateFields, $file2);
            $file2 = str_replace("{{prepare_fields}}", (string)$prepareFields, $file2);

            $file3 = str_replace("{{".$key."}}", is_array($value) ? '"'.(string)$array.'"' : $value, $file3);

            $file4 = str_replace("{{".$key."}}", is_array($value) ? '"'.(string)$array.'"' : $value, $file4);
        }

        $export_prepath = $this->export_type == "internal" ? "/" : "public/preview/";
        $modalFilename = $data["uc_model_name"]."Model";
        if (!write_file("../{$export_prepath}App/Models/".$modalFilename.".php", $file)) {
            $result["success"] = false;
        } else {
            $result["success"] = true;
        }

        $indenter = new \Gajus\Dindent\Indenter();
        $file2 = $indenter->indent($file2);
        $viewFilename = $data["uc_model_name"]."View";
        if (!write_file("../{$export_prepath}App/Views/".$viewFilename.".php", $file2)) {
            $result["success"] = false;
        } else {
            $result["success"] = true;
        }

        $controllerFilename = $data["uc_model_name"]."Controller";
        if (!write_file("../{$export_prepath}App/Controllers/".$controllerFilename.".php", $file3)) {
            $result["success"] = false;
        } else {
            $result["success"] = true;
        }

        if (!file_exists("../{$export_prepath}App/Config/Generated")) {
            mkdir("../{$export_prepath}App/Config/Generated", 0777, true);
            // TODO: On general jQuery event, if toastr key is present, display toastr in front end
            $result["toastr"] = "Directory Generated created";
        }

        $routesFilename = $data["uc_model_name"]."Routes";
        // ROUTES
        $addToRoutes = true;
        if (true || $addToRoutes) {
            if (!write_file("../{$export_prepath}App/Config/Generated/".$routesFilename.".php", $file4)) {
                $result["success"] = false;
            } else {
                $result["success"] = true;
            }
        } else {
            if (file_exists("../{$export_prepath}App/Config/Generated/".$routesFilename.".php")) {
                unlink("../{$export_prepath}App/Config/Generated/".$routesFilename.".php");
            }
        }

        $result["response"][] = ["info", "Export type: ".$this->export_type];
        $result["response"][] = ["info", "Link type: ".$column["link_type"]];

        if ($this->request->isAjax()) {
            return $this->response->setJSON($result);
        } else {
            var_dump($result);
        }
    }

    function propsToString($props = []) {
        if (empty($props)) return "";

        $propString = [];
        foreach ($props as $element) {
            $propString[] = $element["property"]."='".$element["attributes"]."'";
        }

        return implode(" ", $propString);
    }

    function theSwitch($fieldProperties, $display_as = "div", $content = null) {
        // TODO: Check when null and stuff

        // THE SWITCH
        $close_tag = "";
        switch ($display_as) {
            case "checkbox": {
                $start_tag = "<input ";
                $end_tag = "/>";
                // Extra properties for checkbox
                $fieldProperties[] = array("property" => "type", "attributes" => "checkbox");
                $fieldProperties[] = array("property" => "data-toggle", "attributes" => "toggle");
                $fieldProperties[] = array("property" => "data-size", "attributes" => "sm");
                break; }
            case "color": {
                $start_tag = "<input ";
                $end_tag = "/>";
                // Extra properties for color
                $fieldProperties[] = array("property" => "type", "attributes" => "color");
                break; }
            case "div": {
                $start_tag = "<div ";
                $close_tag = ">";
                $end_tag = "</div>";
                break; }
            case "select": {
                $start_tag = "<select ";
                $close_tag = ">";
                $end_tag = "</select>"; // You end this tag yourself after adding options
                break; }
            case "option": {
                $start_tag = "<option ";
                $close_tag = ">";
                $end_tag = "</option>";
                break; }
            case "date": {
                $start_tag = "<input ";
                $close_tag = "";
                $end_tag = "/>";
                $fieldProperties[] = array("property" => "type", "attributes" => "date");
                break; }
            case "time": {
                $start_tag = "<input ";
                $close_tag = "";
                $end_tag = "/>";
                $fieldProperties[] = array("property" => "type", "attributes" => "time");
                break; }
            case "datetime-local": {
                $start_tag = "<input ";
                $close_tag = "";
                $end_tag = "/>";
                $fieldProperties[] = array("property" => "type", "attributes" => "datetime-local");
                break; }
            default: {
                $start_tag = "<input ";
                $close_tag = "";
                $end_tag = "/>";
                break; }
        }
        
        // These are the attr and props -> class="form-control"
        $formInput = $start_tag;
        $formInput .= $this->propsToString($fieldProperties).$close_tag;
        if (!empty($content)) $formInput .= $content;
        $formInput .= $end_tag.PHP_EOL;

        return $formInput;
    }
    
}
