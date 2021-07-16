<? echo view("projects/ProjectNavigation"); ?>
<style>
    /* same thing from modules view */
    #manage-table>tbody>tr>td {
        padding-top: 0px;
        padding-bottom: 0px;
    }
    .background-light {
        background: #1d1a18;
    }
    table {
        margin: 0rem !important;
    }
    .height-limit-40 {
        height: 40px !important;
    }
    .form-control-sm {
        height: calc(1.0em + 1.0rem + 2px);
        padding: .15rem .5rem;
        font-size: .800rem;
        font-weight: bold;
        line-height: 2;
        border-radius: 0rem;
    }
    .fa-2x {
        font-size: 2.2em;
    }
    option {
        font-weight: bolder !important;
    }
    .modulesViewColour {
        background: #1c4046;
    }
    .status-danger {
        border: 2px solid #990;
    }
    .status-success {
        border: 2px solid #090;
    }
    .status-error {
        border: 2px solid #900;
    }
    .form-checkbox {
        margin: auto;
        display: block;
    }
    .custom-control-input:checked ~ .custom-control-indicator {
        background-color: #ffa500;
    }
    .roll-over:hover {
        background: #343a40;
        cursor: pointer;
    }
    .checkbox-menu li label {
        display: block;
        padding: 3px 10px;
        clear: both;
        font-weight: normal;
        line-height: 1.42857143;
        color: #333;
        white-space: nowrap;
        margin:0;
        transition: background-color .4s ease;
    }
    .checkbox-menu li input {
        margin: 0px 5px;
        top: 2px;
        position: relative;
    }

    .checkbox-menu li.active label {
        background-color: #cbcbff;
        font-weight:bold;
    }

    .checkbox-menu li label:hover,
    .checkbox-menu li label:focus {
        background-color: #f5f5f5;
    }

    .checkbox-menu li.active label:hover,
    .checkbox-menu li.active label:focus {
        background-color: #b8b8ff;
    }
</style>

<section class="col d-flex">
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <i class="fa fa-cog"></i>
            user.id -> info.user_id
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">  
            <li><label><input type="checkbox" checked>&nbsp;user.id -> info.user_id</label></li>
            <li><label><input type="checkbox">&nbsp;user.id -> info.userId</label></li>
            <li><label><input type="checkbox">&nbsp;user.id -> info.userID</label></li>
            <li><label><input type="checkbox">&nbsp;user.id -> info.userid</label></li>
            <li><label><input type="checkbox">&nbsp;user.user_id -> info.user_id</label></li>
         </ul>
    </div>
    <input type="text" placeholder="Prefix" class="form-control-sm mt-1 ml-3 mr-3">
    <button class="btn btn-default btn-primary" type="button" id="refreshLinks">
        <i class="fa fa-search"></i>
    </button>
</section>

<section class="container-fluid invisible pt-3">
    <div class="row">
        <div class="col-12">
            <div id="tabsContent" class="tab-content">
                <div id="detect" class="tab-pane fade active show">
                    <table id="pk_fk_pairs" class="table table-dark table-hover table-sm">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Foreign Table Name</th>
                                <th scope="col">Foreign Column</th>
                                <th scope="col">(FK) Type</th>
                                <th scope="col">Primary Table Name</th>
                                <th scope="col">Primary Column</th>
                                <th scope="col">(PK) Type</th>
                                <th scope="col">Display Column</th>
                                <th scope="col">Link Type</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?  $index = 0;
                            foreach($data["foreign_keys"] as $key) {
                                if (!empty($key)) {
                                    $index++;
                                    $pk_type = $data["column_types"][$key["table_name"].".".$key["table_column"]]["Type"];
                                    $fk_type = $data["column_types"][$key["key_table"].".".$key["key_column"]]["Type"];
                                    $pk_correct = $pk_type === $fk_type ? true : false;
                                    $fk_correct = $fk_type === $pk_type ? true : false;
                                    $can_link = $pk_correct === true && $fk_correct === true ? true : false;
                                    ?>
                                    <tr data-pk="<?= $key["table_name"].".".$key["table_column"]; ?>" data-fk="<?= $key["key_table"].".".$key["key_column"]; ?>">
                                        <td><?= is_array($key["display"]) ? $key["display"]["link_id"] : "<button class='addLink btn btn-xs btn-primary'><i class='fa fa-plus'></i></button>" ?></td>
                                        <td><?= $key["key_table"] ?></td>
                                        <td><?= $key["key_column"] ?></td>
                                        <td><?= $data["column_types"][$key["key_table"].".".$key["key_column"]]["Type"] ?><?= $fk_correct === true ? '&nbsp;<i class="fa fa-check text-success"></i>' : '&nbsp;<i class="fa fa-ban text-danger"></i>' ?></td>
                                        <td><?= $key["table_name"] ?></td>
                                        <td><?= $key["table_column"] ?></td>
                                        <td><?= $data["column_types"][$key["table_name"].".".$key["table_column"]]["Type"] ?><?= $pk_correct === true ? '&nbsp;<i class="fa fa-check text-success"></i>' : '&nbsp;<i class="fa fa-ban text-danger"></i>' ?></td>
                                        <td>
                                            <? if (is_array($key["display"])) { ?>
                                                <!-- Columns of the foreign key table -->
                                                <!-- The trick here is to have the table_info_id of the display table so they can be linked -->
                                                <select data-save="<?= handledata(array('links' => 'user_table_id_display', 'id' => $key["display"]["link_id"])) ?>"
                                                    name="display_column_<?= $key["display"]["link_id"]?>" id="display_column_<?= $key["display"]["link_id"] ?>" class="form-control form-control-sm">
                                                    <option value="0">None</option>
                                                    <? foreach ($key[$key["table_name"]] as $column) { ?>
                                                        <option value="<?= $column["id"] ?>" <?= $column["id"] == $key["display"]["user_table_id_display"] ? "selected" : ""; ?>>
                                                            <?= $column["TABLE_NAME"].".".$column["COLUMN_NAME"] ?>
                                                        </option>
                                                    <? } ?>
                                                </select>
                                            <? } ?>
                                        </td>
                                        <td>
                                            <? if (isset($key["display"]["link_id"])) { ?>
                                                <select data-save="<?= handledata(array('links' => 'link_type', 'id' => $key["display"]["link_id"])) ?>"
                                                    name="link_type_<?= $key["display"]["link_type"]?>" id="link_type_<?= $key["display"]["link_type"] ?>" class="form-control form-control-sm">
                                                    <option value="0" <?= $key["display"]["link_type"] == 0 ? "selected" : ""; ?>>Please Select</option>
                                                    <option value="1" <?= $key["display"]["link_type"] == 1 ? "selected" : ""; ?>>Static</option>
                                                    <option value="2" <?= $key["display"]["link_type"] == 2 ? "selected" : ""; ?>>Dynamic</option>
                                                </select>
                                            <? } ?>
                                        </td>
                                        <td>              
                                            <? if ($can_link === true && is_array($key["display"])) { ?>                              
                                                <input <?= $key["display"]["enabled"] == "1" ? "checked" : "" ?> 
                                                        data-save="<?= handledata(array('links' => 'enabled', 'id' => $key["display"]["link_id"])) ?>"
                                                        name='complete' value="isComplete" class='btn btn-success btn-xs' type='checkbox' data-toggle='toggle' data-size='xs'/>
                                            <? } ?>
                                        </td>
                                    </tr>
                            <? } ?>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?=base_url()?>js/common.js"></script>

<script>
    $(document).ready(function () {
        $(".addLink").on("click", function (e) {
            e.preventDefault();
            // debugger;
            $.ajax({
                type: "post",
                url: "fk/saveForeignKey",
                data: {
                    "user_table_id_primary": $(this).parent().parent().data("pk"),
                    "user_table_id_foreign": $(this).parent().parent().data("fk"),
                },
                dataType: "json",
                success: function (response) {
                    // toastr.info(response.success);
                },
                error: function (response) {
                    // toastr.error(response.error);               
                },
                complete: function () {

                }
            });
        });
        $(".container-fluid").removeClass("invisible");

        $(".checkbox-menu").on("change", "input[type='checkbox']", function() {
            $(this).closest("li").toggleClass("active", this.checked);
        });

        $(document).on('click', '.allow-focus', function (e) {
            e.stopPropagation();
        });
    });
</script>