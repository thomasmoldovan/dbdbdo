<? echo view("projects/menu"); ?>
<div class="mt-2">
    <ul class="nav flex-column">
        <? foreach ($data["tables"] as $key => $table) {
        // Move this in PHP Controller
        $base64 = sha1(json_encode($table)); ?>
        <div id="table<?= $key; ?>" class="container-fluid tableContainer">
            <div class="header">
                <div class="p-0 height-limit-40" href="#<?= $table["TABLE_NAME"] ?>"
                    data-toggle="collapse" aria-expanded="false" aria-controls="<?= $table["TABLE_NAME"] ?>">
                    <div class="f-row flex-row flex row align-self-center m-0 background-light">

                        <div class="col-2 align-self-center w-100 pl-0">
                            <div class="d-flex d-row">
                                <? if (!in_array($table["TABLE_NAME"], $data["tablesProcessed"])) {
                                    echo '<button class="action btn btn-sm btn-primary m-1 no-outline" data-id="'.$table["TABLE_NAME"].'" data-json="'.$base64.'"><i class="fab fa-readme"></i></button>';
                                } else {
                                    echo '<button class="action btn btn-sm btn-success m-1 no-outline" data-id="'.$table["TABLE_NAME"].'" data-json="'.$base64.'"><i class="fas fa-eye"></i></button>';
                                } ?>
                                <div class="pl-3 align-self-center">
                                    <div class="bold text-white"><?= $table["TABLE_NAME"] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 align-self-center bold text-white">Rows: <?= $table["TABLE_ROWS"] ?></div>
                        <div class="col-2 align-self-center">
                            <div class="form-group pt-1 mb-1 text-white">
                                <?= $table["ENGINE"] ?>
                            </div>
                        </div>

                        <div class="col-2 align-self-center">
                            <div class="form-group pt-1 mb-1 text-white">
                                <?= $table["TABLE_COLLATION"] ?>
                            </div>
                        </div>
                        <div class="col-2 align-self-center bold text-white small"><?= $table["TABLE_COMMENT"] ?></div>
                        <div class="col-2 align-self-center bold text-white small pl-0 pr-0">
                            <? if (in_array($table["TABLE_NAME"], $data["tablesProcessed"])) { ?>
                            <button type="submit"
                                class="resetTableButton btn btn-danger btn-sm float-right mr-1 no-outline"
                                data-name="<?= $table["TABLE_NAME"] ?>">Reset</button>
                            <? } else { ?>
                            <button type="submit"
                                class="deleteTableButton btn btn-secondary btn-sm float-right mr-1 no-outline"
                                data-name="<?= $table["TABLE_NAME"] ?>">Delete</button>
                            <? } ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="content">
                <div class="flex-row">
                    <div class="collapse <?= (in_array($table["TABLE_NAME"],  $data["tablesProcessed"])) ? "" : "show"; ?>"
                        id="<?= $table["TABLE_NAME"] ?>">
                        <table id="<?= $table["TABLE_NAME"] ?>Details" name="<?= $table["TABLE_NAME"] ?>"
                            class="table table-dark table-hover table-sm <?= (in_array($table["TABLE_NAME"],  $data["tablesProcessed"])) ? "" : "d-none"; ?>">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="pl-2"><input type="checkbox" class="selectAllColumns"
                                            id="selectAllColumns" name="selectAllColumns"
                                            data-table="<?= $table["TABLE_NAME"] ?>"></th>
                                    <th>ID</th>
                                    <th>Column Name</th>
                                    <th>Type</th>
                                    <th>PK</th>
                                    <th>Default</th>
                                    <th>Null</th>
                                    <th>AI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <? if (in_array($table["TABLE_NAME"],  $data["tablesProcessed"])) {
                                    foreach ($data["userTables"] as $currentTable) {
                                        $id = $currentTable["id"];
                                        $tableName = $currentTable["table_name"];
                                        $columnName = $currentTable["column_name"];

                                        $currentTable["default"] = empty($currentTable["default"]) ? "<i class='fa fa-ban text-muted'></i>" : $currentTable["default"];
                                        $currentTable["null"] = $currentTable["null"] == 1 ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-ban text-muted'></i>";
                                        $currentTable["ai"] = $currentTable["ai"] == 1 ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-ban text-muted'></i>";

                                        if ($tableName == $table["TABLE_NAME"]) { ?>
                                <tr>
                                    <td class="pl-2" style="width: 32px;"><input class="<?= $tableName ?>Columns"
                                            type="checkbox" data-id="<?= $id ?>" data-table="<?= $tableName ?>"
                                            data-column="<?= $columnName ?>"
                                            <?= $currentTable["pk"] == 1 ? "checked disabled" : "" ?>></td>
                                    <td><?= $currentTable["id"] ?></td>
                                    <td><?= $currentTable["column_name"] ?></td>
                                    <td><?= $currentTable["type"] ?></td>
                                    <td><?= $currentTable["pk"] == 1 ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-ban text-muted'></i>" ?></td>
                                    <td><?= $currentTable["default"] ?></td>
                                    <td><?= $currentTable["null"] ?></td>
                                    <td><?= $currentTable["ai"] ?></td>
                                </tr>
                                <? }
                                    }
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr class="push-right">
                                    <th colspan="12">
                                        <div class="col-12 row flex">
                                            <div class="p-0">
                                                <button name="<?= $table["TABLE_NAME"] ?>" type="button" class="addToModules btn btn-sm btn-primary w-100">Create Module</button>
                                            </div>
                                            <div class="pl-5 d-flex justify-align-center">
                                                <div class="pl-4 btn btn-dark btn-sm">
                                                    <input type="checkbox" class="setIds pl-1 form-check-input"
                                                        data-table="<?= $table["TABLE_NAME"] ?>"
                                                        id="setIds<?= $table["TABLE_NAME"] ?>" name="setIds" checked>
                                                    <label class="form-check-label" for="setIds"> Set ids</label>
                                                </div>
                                                <div class="pl-4 btn btn-dark btn-sm">
                                                    <input type="checkbox" class="setNames pl-1 form-check-input"
                                                        data-table="<?= $table["TABLE_NAME"] ?>"
                                                        id="setNames<?= $table["TABLE_NAME"] ?>" name="setNames" checked>
                                                    <label class="form-check-label" for="setNames"> Set names</label>
                                                </div>
                                                <div class="pl-4 btn btn-dark btn-sm">
                                                    <input type="checkbox" class="setClasses pl-1 form-check-input"
                                                        data-table="<?= $table["TABLE_NAME"] ?>"
                                                        id="setClasses<?= $table["TABLE_NAME"] ?>" name="setClasses" checked>
                                                    <label class="form-check-label" for="setClasses"> Set classes</label>
                                                </div>
                                                <div class="pl-4 btn btn-dark btn-sm">
                                                    <input type="checkbox" class="setLabels pl-1 form-check-input"
                                                        data-table="<?= $table["TABLE_NAME"] ?>"
                                                        id="setLabels<?= $table["TABLE_NAME"] ?>" name="setLabels" checked>
                                                    <label class="form-check-label" for="setLabels"> Set labels</label>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <? } ?>
    </ul>
</div>

<script>
    var savedTables = [];
    var savedTablesRaw = $.parseJSON('<?= json_encode( $data["tablesProcessed"]) ?>');

    $(document).ready(function () {
        $(".selectAllColumns").click(function (e) {
            checked = $(this).prop("checked");
            tableName = $(this).data("table");
            tableRows = $("#" + tableName + "Details tbody tr");
            $.each(tableRows, function (index, row) {
                currentRow = $(row).find("input");
                if (currentRow.prop("disabled") == false) {
                    currentRow.prop("checked", checked);
                }
            });
        });

        $(".resetTableButton").click(function (e) {
            e.preventDefault();
            $(document).trigger("showLoadingScreen");
            $.ajax({
                type: "post",
                url: "/projects/resetTable",
                data: {
                    "table_name": $(this).data("name")
                },
                dataType: "json",
                success: function (response) {
                    toastr.success(response.message);
                    location = window.location;
                },
                error: function (response) {
                    toastr.error(response.responseJSON.message, response.responseJSON.type);
                    $(document).trigger("hideLoadingScreen");
                }
            });
        });

        $(".deleteTableButton").click(function (e) {
            e.preventDefault();
            $(document).trigger("showLoadingScreen");
            $.ajax({
                type: "post",
                url: "/projects/deleteTable",
                data: {
                    "table_name": $(this).data("name"),
                    "project_hash": "<?= $data["project"]["project_hash"]; ?>"
                },
                dataType: "json",
                success: function (response) {
                    toastr.success(response.message);
                    location = window.location;
                },
                error: function (response) {
                    toastr.error(response.responseJSON.message, response.responseJSON.type);
                    $(document).trigger("hideLoadingScreen");
                }
            });
        });

        $(".addToModules").click(function (e) {
            e.preventDefault();
            tableName = $(this).attr("name");
            if ($("#selectModule" + tableName).val() == 0) {
                $("#addModule" + tableName + "Error").html("Please select a module");
                return false;
            }

            selectedColumns = [];
            columns = $("#" + tableName + "Details tbody").find("." + tableName + "Columns");
            $.each(columns, function (key, obj) {
                record = columns[key];
                id = $(obj).data("id");
                if (id > 0 && $(obj).prop("checked")) {
                    selectedColumns.push(id);
                }
            });

            if (selectedColumns.length <= 1) {
                $("#addModule" + tableName + "Error").html("Please select atleast one column from the table");
                toastr.error("You must select atleast 1 more column, except the ID column", "Error");
                return false;
            }

            $(document).trigger("showLoadingScreen");
            // module/create
            $.ajax({
                type: "post",
                url: "/projects/linkTableToModule",
                data: {
                    "module_name": tableName,
                    "selectedColumns": selectedColumns,
                    "setIds": $("#setIds" + tableName).prop("checked"),
                    "setNames": $("#setNames" + tableName).prop("checked"),
                    "setClasses": $("#setClasses" + tableName).prop("checked"),
                    "setLabels": $("#setLabels" + tableName).prop("checked")
                },
                dataType: "json",
                success: function (response) {
                    // window.location = "<?= base_url() ?>" + "modules";
                },
                complete: function (response) {
                    $(document).trigger("hideLoadingScreen");
                    var res = JSON.parse(response.responseText);
                    toastr.success("Module '" + res.module + "' has been successfully created");
                },
                error: function (error) {
                    toastr.error("Error: " + error.message, "Create Module");
                }
            });
        });

        $.each(savedTablesRaw, function (index, value) {
            savedTables.push(value); // IMPORTANT
        })

        // We block the default bootstrap collapse, because we will trigger it ourselfs
        $("[data-toggle='collapse']").on("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
        });

        $(".action").click(function (element) {
            table = $(this).data("id");
            $("#" + table).collapse("toggle");
            if (savedTables.indexOf(table) < 0) {
                // This is the first time this table has been opened
                $(this).children().removeClass().addClass("fas fa-sync fa-spin");
                $(this).removeClass("btn-primary btn-warning btn-danger btn-success");
                $(this).addClass("btn-warning");
                toggleTable($(this));
            } else {
                if (!$(this).hasClass("btn-success")) {
                    $(this).removeClass("btn-primary btn-warning btn-danger btn-success");
                    $(this).addClass("btn-warning");
                } else {
                    $(this).removeClass("btn-primary btn-warning btn-danger btn-success");
                    $(this).addClass("btn-success");
                }
            }
        });
    });

    function toggleTable(element) {
        $(document).trigger("showLoadingScreen");
        currentTable = element.data("id");
        element.removeClass("btn-primary btn-warning btn-danger btn-success").addClass("btn-warning");
        $.ajax({
            type: "post",
            url: "<?= base_url() ?>/projects/getTableColumns",
            data: {
                "tableName": currentTable,
                "project_hash": "<?= $data["project"]["project_hash"]; ?>"
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                $(element).children().removeClass().addClass("fas fa-eye");
                $(element).removeClass("btn-primary btn-warning btn-danger btn-success");
                $(element).addClass("btn-success");
                location = window.location;
            },
            error: function (response) {
                console.log(response);
                element.removeClass("btn-warning").addClass("btn-danger");
            },
            complete: function (response) {
                console.log(response);
                //location = window.location;
            }
        });
    }
</script>