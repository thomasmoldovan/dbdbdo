<? echo view("projects/ProjectNavigation"); ?>
<style>
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
        line-height: 2;
        border-radius: 0rem;
    }
    .fa, .far, .fas {
        font-family: "Font Awesome 5 Free";
    }
    .fa-2x {
        font-size: 2.2em;
    }
    option {
        font-weight: bolder !important;
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
</style>
<div class="mt-2">
    <ul class="nav flex-column">
        <? if (isset($data["modules"]) && count($data["modules"])) { ?>
            <? foreach ($data["modules"] as $key => $module) {
                $base64 = sha1(json_encode($module)); ?>
                <div class="container-fluid">
                    <div class="header">
                        <div id="<?= $module[0]['module_name'] ?>Row" class="p-0 height-limit-40" href="#<?= $module[0]['module_name'] ?>" aria-expanded="false" aria-controls="<?= $module[0]['module_name'] ?>">
                            <div class="row align-self-center m-0 background-light">

                                <div class="action align-self-center" data-id="<?= trim(strtolower($module[0]['module_name'])) ?>">
                                    <button class="btn btn-sm btn-primary m-1" data-toggle="collapse"><i class="fa fa-arrow-right"></i></button>
                                </div>
                                <div class="col-4 align-self-center">
                                    <div class="text-white"><b><?= $module[0]['module_name'] ?></b></div>
                                </div>
                                <div class="col-2 d-flex align-self-center text-white">
                                    <input type="text" name="routeName" id="routeName<?= $module[0]['module_route'] ?>" 
                                           data-save="<?= handledata(array('user_modules' => 'module_route', 'id' => $module[0]['id'])) ?>"
                                           value="<?= $module[0]['module_route'] ?>"
                                           class="form-control form-control-sm" placeholder="Route Alias">
                                </div>
                                <div class="d-flex pr-2 small text-white w-auto ml-auto align-self-center">
                                        Last build: 22 May 2021 (outdated)
                                </div>
                                <div class="btn-group ml-2">
                                    <button data-module_name="<?= $module[0]['module_name'] ?>" class="settingsModal btn btn-primary btn-sm btn-block m-1">
                                        Settings
                                    </button>
                                    <div data-module_name="<?= $module[0]['module_name'] ?>" class="fileWriter btn btn-primary btn-sm btn-block m-1">
                                        Build
                                    </div>
                                    <button data-module_name="<?= $module[0]['module_name'] ?>" class="fileViewer btn btn-primary btn-sm btn-block m-1">
                                        View
                                    </button>
                                    <button onclick="deleteModule('<?= $module[0]['user_module_id'] ?>')" data-module_name="<?= $module[0]['module_name'] ?>" class="deleteModule btn btn-danger btn-sm btn-block m-1">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <div class="flex-row">
                            <div class="collapse" id="<?= $module[0]['module_name'] ?>">
                                <!-- COLUMNS -->
                                <table id="<?= $module[0]['module_name'] ?>Details" name="<?= $module[0]['module_name'] ?>" class="table table-dark table-hover table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th></th>
                                            <th>Column Name</th>
                                            <th>PK</th>
                                            <th>Link</th>
                                            <th>Label</th>
                                            <th>Type</th>
                                            <th>Display As</th>
                                            <th>Properties</th>
                                            <th>Enabled</th>
                                        </tr>
                                    </thead>

                                    <tbody>                                
                                        <? foreach ($module as $component) { ?>
                                            <tr>
                                                <td class="align-self-center m-0 p-2"><?= $component["user_table_id"] ?></td>
                                                <td class="align-self-center m-0 p-2"><?= $component["column_name"] ?></td>
                                                <td class="align-self-center m-0 p-2"><?= $component["pk"] == 1 ? '<i class="fas fa-key text-warning"></i>' : '' ?></td>
                                                <td class="align-self-center m-0 p-2">
                                                    <? if ((int)$component['link_id'] > 0) { ?>
                                                        <div class="d-flex">
                                                            <i class="fa fa-link pt-1"></i>&nbsp;
                                                            <? if ($component['primary']) { ?>
                                                                Linked to&nbsp;<div class="text-primary bold"><?= $component["primary"].' -> showing '.$component["display"]; ?></div>
                                                            <? } ?>
                                                        </div>
                                                    <? } ?>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                        data-save="<?= handledata(array('user_tables' => 'display_label', 'id' => $component['user_table_id'])) ?>"
                                                        class="col-10 form-control form-control-sm" 
                                                        value="<?= $component['display_label'] ?>">
                                                </td>
                                                <td class="align-self-center m-0 p-2">
                                                    <? if ($component['settings'] && !is_null($component['settings'])) { ?>
                                                        <div class="text-warning"><?= $component["settings"]["type"] ?></div>
                                                    <? } else { ?>
                                                        <?= $component["type"] ?>
                                                    <? } ?>
                                                </td>
                                                <td>
                                                    <select data-save="<?= handledata(array('user_tables' => 'display_as', 'id' => $component['user_table_id'])) ?>"
                                                            id="selectFormat" class="form-control form-control-sm" name="format"
                                                            <?= $component['link_type'] == 2 ? "disabled" : "" ?>>

                                                        <? if ($component['link_type'] != 2) { ?>
                                                            <option value="">Input</option>
                                                            <option value="checkbox" <?= $component["display_as"] == "checkbox" ? "selected" : "" ?>>Checkbox</option>
                                                            <option value="color" <?= $component["display_as"] == "color" ? "selected" : "" ?>>Color</option>
                                                            <option value="date" <?= $component["display_as"] == "date" ? "selected" : "" ?>>Date</option>
                                                            <option value="datetime-local" <?= $component["display_as"] == "datetime-local" ? "selected" : "" ?>>Datetime Local</option>
                                                            <option value="email" <?= $component["display_as"] == "email" ? "selected" : "" ?>>Email</option>
                                                            <option value="hidden" <?= $component["display_as"] == "hidden" ? "selected" : "" ?>>Hidden</option>
                                                            <option value="image" <?= $component["display_as"] == "image" ? "selected" : "" ?>>Image</option>
                                                            <option value="number" <?= $component["display_as"] == "number" ? "selected" : "" ?>>Number</option>
                                                            <option value="password" <?= $component["display_as"] == "password" ? "selected" : "" ?>>Password</option>
                                                            <option value="radio" <?= $component["display_as"] == "radio" ? "selected" : "" ?>>Radio</option>
                                                            <option value="tel" <?= $component["display_as"] == "tel" ? "selected" : "" ?>>Tel</option>
                                                            <option value="text" <?= $component["display_as"] == "text" ? "selected" : "" ?>>Text</option>
                                                            <option value="time" <?= $component["display_as"] == "time" ? "selected" : "" ?>>Time</option>
                                                            <option value="url" <?= $component["display_as"] == "url" ? "selected" : "" ?>>Url</option>
                                                        <? } else { ?>
                                                            <option value="">Select</option>
                                                        <? }?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button id="fieldProperties-<?= $component['user_table_id'] ?>"
                                                        data-id="<?= $component['user_table_id'] ?>"
                                                        data-column="<?= $component['column_name'] ?>"
                                                        class="fieldProperties btn btn-sm btn-primary" data-toggle="collapse"><i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input <?= $component["column_enabled"] == "1" ? "checked" : "" ?> name='disabled' value="isDisabled" 
                                                            type='checkbox' data-toggle='toggle' data-size='sm'
                                                            data-save="<?= handledata(array('tables_modules' => 'enabled', 'id' => $component['user_table_id'])) ?>"
                                                            class="btn btn-success btn-sm"
                                                            />
                                                </td>
                                            </tr>
                                        <? } ?>
                                    </tbody>
                                </table>
                                <!-- END COLUMNS -->
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
        <? } else { ?>
            <div class="w-100 text-center">
                <h4>No modules found</h4>
                <h6>How about we create one</h6>
                <a href="/projects/<?= $data["project"]["project_hash"]; ?>/tables/"> <button class="btn btn-primary btn-sm"><i class="fa fa-table"></i>&nbsp;Tables</button></a>
            </div>
        <? } ?>    
    </ul>
</div>

<div class="modal fade" id="settingsModal" tabindex="0" role="dialog" aria-labelledby="settingsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="settingsTitle">
                            Settings for
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="settingsForm" class="col-12" method="post" action="delete">
                <div class="modal-body pr-0 pl-0">
                    <!-- If column is linked, then we can have it hardcoded or dynamic -->
                    <label for='toggleStaticDynamic' class='w-100 mb-0 bold pr-3'>Link type</label>
                    <input id='toggleStaticDynamic' name='toggleStaticDynamic' class='form-control form-control-sm hide' type='checkbox' data-toggle='toggle' data-size='sm' 
                           data-on="Static" data-off="Dynamic"
                           data-onstyle="primary" data-offstyle="primary"
                           data-width="100px" />

                    <label for='toggleRremoteDisplay' class='w-100 mb-0 bold pr-3'>Remote display</label>
                    <input id='toggleRremoteDisplay' name='toggleRremoteDisplay' class='form-control form-control-sm hide' type='checkbox' data-toggle='toggle' data-size='sm' 
                           data-on="Static" data-off="Dynamic"
                           data-onstyle="primary" data-offstyle="primary"
                           data-width="100px" />

                    <div class="the-body mb-3">
                        <select id="exportType" name="exportType" class="form-control form-control-sm">
                            <option value="">Codeigniter 4</option>
                            <option value="">Pure PHP (>=7)</option>
                            <option value="">Node API</option>
                        </select>
                    </div>
                    <div class="d-flex">
                        <div class="col-6 pb-3 pl-1">
                            <div class="">
                                <input type="checkbox" class="form-check-input">
                                <label for="" class="form-check-label" >Entity</label>
                            </div>
                        </div>

                        <div class="col-6 pb-3 pl-1">
                            <div class="">
                                <input type="checkbox" class="form-check-input">
                                <label for="" class="form-check-label" >Show on menu</label>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="modal-footer text-center">
                        <button type="submit" name="submit" class="btn btn-success" value="Submit">Yes</button>
                        <button type="submit" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <input type="hidden" id="deleteID" name="deleteID" value=""/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<input id="myId" type="hidden" value="">
<div class="modal fade" id="propertiesListModal" tabindex="0" role="dialog" aria-labelledby="propertiesListModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="propertiesList">
                            Properties for
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="propertiesListForm" class="col-12">
                <div class="modal-body pr-0 pl-0">
                    <div class="the-body">
                        <!-- START PROPERTIES MODAL -->
                        <div class="">
                            <label for="tagTemplateDropdown" class="bold">Select template:</label>
                            <div class="w-100">
                                <? if (isset($component)) { ?>
                                    <select data-save="<?= handledata(array('user_tables' => 'display_as', 'id' => $component['user_table_id'])) ?>"
                                            id="selectFormat" class="form form-control form-control-sm mb-2" name="format"
                                            <?= $component['link_type'] == 2 ? "disabled" : "" ?>>
                                        </hr>
                                        <? if ($component['link_type'] != 2) { ?>
                                            <option value="">Input</option>
                                            <option value="checkbox" <?= $component["display_as"] == "checkbox" ? "selected" : "" ?>>Checkbox</option>
                                            <option value="color" <?= $component["display_as"] == "color" ? "selected" : "" ?>>Color</option>
                                            <option value="date" <?= $component["display_as"] == "date" ? "selected" : "" ?>>Date</option>
                                            <option value="datetime-local" <?= $component["display_as"] == "datetime-local" ? "selected" : "" ?>>Datetime Local</option>
                                            <option value="email" <?= $component["display_as"] == "email" ? "selected" : "" ?>>Email</option>
                                            <option value="hidden" <?= $component["display_as"] == "hidden" ? "selected" : "" ?>>Hidden</option>
                                            <option value="image" <?= $component["display_as"] == "image" ? "selected" : "" ?>>Image</option>
                                            <option value="number" <?= $component["display_as"] == "number" ? "selected" : "" ?>>Number</option>
                                            <option value="password" <?= $component["display_as"] == "password" ? "selected" : "" ?>>Password</option>
                                            <option value="radio" <?= $component["display_as"] == "radio" ? "selected" : "" ?>>Radio</option>
                                            <option value="tel" <?= $component["display_as"] == "tel" ? "selected" : "" ?>>Tel</option>
                                            <option value="text" <?= $component["display_as"] == "text" ? "selected" : "" ?>>Text</option>
                                            <option value="time" <?= $component["display_as"] == "time" ? "selected" : "" ?>>Time</option>
                                            <option value="url" <?= $component["display_as"] == "url" ? "selected" : "" ?>>Url</option>
                                        <? } else { ?>
                                            <option value="">Select</option>
                                        <? }?>
                                    </select>
                                <? } ?>
                            </div>
                            <div id="propContainer" class="hide">
                                <div class="properties d-flex pt-2 pb-2">
                                    <input type="text" class="property form-control form-control-sm mr-2">
                                    <b class="text-black">=</b>
                                    <input type="text" class="attribute form-control form-control-sm ml-2 mr-2">
                                    <button type="button" name="deleteProp" class="deleteProp btn btn-danger btn-sm"><i class="fa fa-trash-o text-white"></i></button>
                                </div>
                            </div>
                            <div id="newPropContainer" class="">
                                <div class="addProperties d-flex pt-2 pb-2">
                                    <input id="newProperty" type="text" class="form-control form-control-sm mr-2">
                                    <b class="text-black">=</b>
                                    <input id="newAttribute" type="text" class="form-control form-control-sm ml-2 mr-2">
                                    <button type="button" id="addProp" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div id="previewProperties" class="float-right"><button class="btn btn-primary">Preview</button></div>
                            <div id="propResult" class="">
                                <h6 class="text-center mb-0 pt-3"><b>Preview</b></h6>
                                <div class="pt-2">
                                    <div id="result" class="w-100 pb-2 text-center"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END PROPERTIES MODAL -->
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button id="saveProperties" name="submit" class="btn btn-success" value="Submit">Save</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var opened = "";
    var properties = {};
    var propArray = [];

    function updatePropertiesList() {
        $(".properties").remove();
        $("#propContainer").empty();
        for (var key in propArray) {
            var props = [];
            for (var prop in propArray[key]) {
                tempprop = properties.clone();
                tempprop.appendTo($("#propContainer"));
                tempprop.find(".property").val(prop);
                tempprop.find(".attribute").val(propArray[key][prop]);
                props.push(prop + '="' + propArray[key][prop] + '"');
            }
            $("#result").empty().append('<button ' + props.join(" ") + '>');
        }
        $("#propContainer").removeClass("hide");

        $(".deleteProp").click(function (e) {
            e.preventDefault();
            $(this).parent().remove();
            delete propArray[0][$(this).parent().find(".property").val()];
            updatePropertiesList();
        });
    }

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        properties = $(".properties");

        $(".settingsModal").click(function(e) {
            e.preventDefault();
            $("#settingsModal").modal("show");

            $("#myId").val($(this).data("id"));
            $("#settingsTitle").html("Settings for column <b>" + $(this).data("column") + "</b>");
        });

        $(".fieldProperties").click(function(e) {
            e.preventDefault();
            $(document).trigger("showLoadingScreen");

            $("#myId").val($(this).data("id"));
            $("#propertiesList").html("Properties for column <b>" + $(this).data("column") + "</b>");

            $.ajax({
                type: "post",
                url: "/ajax/ajaxLoadProperties",
                data:  {
                    "columnId": $(this).data("id")
                },
                dataType: "json",
                success: function (response) {
                    propArray = response.properties;

                    updatePropertiesList();
                    $("#propertiesListModal").modal("show");
                    $(document).trigger("hideLoadingScreen");
                }
            });
        });

        // Openes and closes the module panel
        $(".action").click(function (e) {
            e.preventDefault();
            table = $(this).data("id");
            $("#" + table).collapse("toggle");
            if ($(this).find("i").hasClass("fa-arrow-right")) {
                opened = $(this).find("i").removeClass("fa fa-arrow-right").addClass("fa fa-arrow-down");
            } else {
                opened = $(this).find("i").removeClass("fa fa-arrow-down").addClass("fa fa-arrow-right");
            }
        });

        // Builds the module files
        $(".fileWriter").click(function (e) {
            e.preventDefault();
            $(document).trigger("showLoadingScreen");
            var module_name = $(e.currentTarget).data("module_name");
            console.log("Writing module" + <?= $data["project"]["project_type"]; ?> + ": " + module_name);
            if ("<?= $data["project"]["project_type"]; ?>" == "0") {
                url = "/buildExternalFiles";
            } else {
                url = "/buildInternalFiles";
            }
            if (module_name == "") return false;
            $.ajax({
                type: "post",
                url: url,
                async: true,
                data: {
                    "module_name": module_name
                },
                dataType: "json",
                success: function (response) {
                    console.log("success");
                    console.log(response);
                    $(document).trigger("hideLoadingScreen");
                },
                error: function (response) {
                    console.log("error");
                    console.log(response);
                },
                complete: function (response) {
                    console.log("complete");
                    console.log(response);
                }
            });
        });

        // Redirects to FRONT END view
        $(".fileViewer").click(function (e) {
            e.preventDefault();
            var module_name = $(e.currentTarget).data("module_name");
            console.log(module_name);

            if (<?= $data["project"]["project_type"]; ?> == 1) {
                // Internal
                window.open('/projects/<?= strtolower($data["project"]["project_hash"]); ?>/preview/' + module_name, '_blank');
            } else if (<?= $data["project"]["project_type"]; ?> == 0) {
                // External
                window.open('/preview/public/<?= strtolower($data["project"]["project_hash"]); ?>/' + module_name, '_blank');
            }            
        });

        $("#saveProperties").click(function (e) {
            e.preventDefault();

            propertiesList = {};
            for (var i = 0; i < $(".properties").length; i++) {
                property = $($(".properties")[i]).find(".property").val();
                attributes = $($(".properties")[i]).find(".attribute").val();
                propertiesList[property] = attributes;
            }

            propertiesList["columnId"] = $("#myId").val();
            $.ajax({
                type: "post",
                url: "/ajax/ajaxSaveProperties",
                data: propertiesList,
                dataType: "json",
                success: function (response) {
                    // $("#propertiesListModal").modal("hide");
                    updatePropertiesList();
                }
            });
        });

        $("#previewProperties").click(function (e) {
            e.preventDefault();

            propertiesList = {};
            for (var i = 0; i < $(".properties").length; i++) {
                property = $($(".properties")[i]).find(".property").val();
                attributes = $($(".properties")[i]).find(".attribute").val();
                propertiesList[property] = attributes;
            }
// debugger;
            propertiesList["columnId"] = $("#myId").val();
            updatePropertiesList();
        });

        $("#addProp").click(function (e) {
            e.preventDefault();
            if ($(this).parent().find("#newProperty") == "") {
                return false;
            }
            propArray[0][$(this).parent().find("#newProperty").val()] = $(this).parent().find("#newAttribute").val();
            $(this).parent().find("#newProperty").val("");
            $(this).parent().find("#newAttribute").val("");
            updatePropertiesList();
        });

        $("button.deleteModal").on("click", function (e) { 
            e.preventDefault();
            console.log("Delete ", $(this).data("module_name"));
        });
    });

    function deleteModule(module_id) {
        showLoadingScreen();
        $.ajax({
            type: "post",
            url: "/modules/deleteModule",
            data: {
                "module_id": module_id
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                location = window.location;
            }
        });
    }
</script>