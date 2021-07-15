<style>
    body {
        font-family: "Open Sans", sans-serif;
        line-height: 1.6;
    }

    .add-todo-input,
    .edit-todo-input {
        outline: none;
    }

    .add-todo-input:focus,
    .edit-todo-input:focus {
        border: none !important;
        box-shadow: none !important;
    }

    .view-opt-label,
    .date-label {
        font-size: 0.8rem;
    }

    .edit-todo-input {
        font-size: 1.7rem !important;
    }

    .todo-actions {
        visibility: hidden !important;
    }

    .todo-item:hover .todo-actions {
        visibility: visible !important;
    }

    .todo-item.editing .todo-actions .edit-icon {
        display: none !important;
    }

</style>
<div class="m-5 p-2 rounded mx-auto bg-light shadow">

    <!-- Create todo section -->
    <div class="row m-1 p-3">
        <div class="col col-11 mx-auto">
            <div class="row bg-white rounded shadow-sm p-2 add-todo-wrapper align-items-center justify-content-center">
                <div class="col">
                    <form action="/projects/tba284c/preview/tasks/create/" method="post">
                        <input class="form-control form-control-lg add-todo-input" type="text" placeholder="Add new ..">
                        <!-- <label for='id' class='w-100 mb-0 bold pr-3'>id</label>
                        <div id='id' name='id' class='form-control form-control-sm' readonly=''></div> -->
                        <label for='group_id' class='w-100 mb-0 bold pr-3'>group_id</label>
                        <select id='group_id' name='group_id' class='form-control form-control-sm'>
                            <option value='16'>DbDbDo Dev</option>
                        </select>
                        <label for='taskname' class='w-100 mb-0 bold pr-3'>taskname</label>
                        <input id='taskname' name='taskname' class='form-control form-control-sm'/>
                        <label for='color_id' class='w-100 mb-0 bold pr-3'>color_id</label>
                        <select id='color_id' name='color_id' class='form-control form-control-sm'>
                            <? foreach($colors as $color) { ?>
                                <option value="<?= $color["id"] ?>"><?= $color["name"] ?></option>
                            <? } ?>
                            <option value='22'>Black</option>
                        </select>
                        <label for='complete' class='w-100 mb-0 bold pr-3'>complete</label>
                        <input id='complete' name='complete' class='form-control form-control-sm' type='checkbox' data-toggle='toggle' data-size='sm'/>

                        <div id="createTasksButton" class="col-auto px-0 mx-0 mr-2">
                            <button type="submit" name="submit" class="btn btn-info" value="Submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="p-2 mx-4 border-black-25 border-bottom"></div>
    <!-- View options section -->
    <div class="row m-1 p-3 px-5 justify-content-end">
        <div class="col-auto d-flex align-items-center">
            <label class="text-secondary my-2 pr-2 view-opt-label">Filter</label>
            <select class="custom-select custom-select-sm btn my-2">
                <option value="all" selected>All</option>
                <option value="completed">Completed</option>
                <option value="active">Active</option>
                <option value="has-due-date">Has due date</option>
            </select>
        </div>
        <div class="col-auto d-flex align-items-center px-1 pr-3">
            <label class="text-secondary my-2 pr-2 view-opt-label">Sort</label>
            <select class="custom-select custom-select-sm btn my-2">
                <option value="added-date-asc" selected>Added date</option>
                <option value="due-date-desc">Due date</option>
            </select>
            <i class="fa fa fa-sort-amount-asc text-info btn mx-0 px-0 pl-1" data-toggle="tooltip" data-placement="bottom" title="Ascending"></i>
            <i class="fa fa fa-sort-amount-desc text-info btn mx-0 px-0 pl-1 d-none" data-toggle="tooltip" data-placement="bottom" title="Descending"></i>
        </div>
    </div>
    <!-- Todo list section -->
    <div class="row mx-1 px-5 pb-3 w-80">
        <div class="col mx-auto">
            <!-- Todo Item 1 -->

            <? foreach ($tasksItems as $item) { ?>
                <div class="row px-3 align-items-center todo-item rounded">
                    <div class="col-auto m-1 p-0 d-flex align-items-center">
                        <h2 class="m-0 p-0">
                            <i class="fa fa-square-o text-primary btn m-0 p-0 d-none" data-toggle="tooltip" data-placement="bottom" title="Mark as complete"></i>
                            <i class="fa fa-check-square-o text-primary btn m-0 p-0" data-toggle="tooltip" data-placement="bottom" title="Mark as todo"></i>
                        </h2>
                    </div>
                    <div class="col px-1 m-1 d-flex align-items-center">
                        <input type="text" class="form-control form-control-lg border-0 edit-todo-input bg-transparent rounded px-3" readonly value="<?= $item["taskname"]; ?>" title="<?= $item["taskname"]; ?>" />
                        <input type="text" class="form-control form-control-lg border-0 edit-todo-input rounded px-3 d-none" value="<?= $item["taskname"]; ?>" />
                    </div>
                    <div class="col-auto m-1 p-0 px-3 d-none">
                    </div>
                    <div class="col-auto m-1 p-0 todo-actions">
                        <div class="row d-flex align-items-center justify-content-end">
                            <h5 class="m-0 p-0 px-2">
                                <i class="fa fa-pencil text-primary btn m-0 p-0" data-toggle="tooltip" data-placement="bottom" title="Edit todo"></i>
                            </h5>
                            <h5 class="m-0 p-0 px-2">
                                <i class="fa fa-trash-o text-danger btn m-0 p-0" data-toggle="tooltip" data-placement="bottom" title="Delete todo"></i>
                            </h5>
                        </div>
                        <div class="row todo-created-info">
                            <div class="col-auto d-flex align-items-center pr-2">
                                <i class="fa fa-info-circle my-2 px-2 text-black-50 btn" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Created date"></i>
                                <label class="date-label my-2 text-black-50">28th Jun 2020</label>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>            
        </div>
    </div>
</div>

<div class="row-fluid p-0">
    <table id="manageTasks" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="createTasksModal" tabindex="-1" role="dialog" aria-labelledby="createTasksModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createTasksForm" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createTasks">
                                Create tasks 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful tasks ever</small> </div>
                    </div>
                </div>
                <div class="error"></div>
                <div class="modal-body">
                    <div class="the-body">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="hidden" id="id" name="id" value=""/>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteTasksModal" tabindex="0" role="dialog" aria-labelledby="deleteTasksModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteTasks">
                            Delete tasks 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteTasksForm" class="col-12" method="post" action="delete">
                <div class="modal-body">
                    <div class="the-body mb-3"> Are you sure you want to delete this entry ? </div>
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
<script src="<?=base_url()?>js/common.js"></script>
<script>
    $(document).ready(function () {
        applyEvents();

        function formatDate(date) {
            return (
                date.getDate() +
                "/" +
                (date.getMonth() + 1) +
                "/" +
                date.getFullYear()
            );
        }

        var currentDate = formatDate(new Date());

        // $(".due-date-button").datepicker({
        //     format: "dd/mm/yyyy",
        //     autoclose: true,
        //     todayHighlight: true,
        //     startDate: currentDate,
        //     orientation: "bottom right"
        // });

        $(".due-date-button").on("click", function (event) {
            $(".due-date-button")
                .datepicker("show")
                .on("changeDate", function (dateChangeEvent) {
                    $(".due-date-button").datepicker("hide");
                    $(".due-date-label").text(formatDate(dateChangeEvent.date));
                });
        });
    });

    function refresh() {
        $(document).trigger("showLoadingScreen");
        $.ajax({
            type: "post",
            url: "/projects/tba284c/preview/tasks/list",
            data: $("#formAddTasks").serialize(),
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.tasksItems, function(index, row) {
                    var values = [];
                    check = row.check;
                    delete row.check;
                    $.each(row, (index, value) => { values.push(value); })

                    // $("#tableID").find('tbody')
                    //     .append($('<tr>')
                    //         .append($('<td>')
                    //             .append($('<img>')
                    //                 .attr('src', 'img.png')
                    //                 .text('Image cell')
                    //             )
                    //         )
                    //     );

                    body +=`<tr>
                                <td><input type="checkbox" class="item" data-id="` + row.id + `"></td>
                                <td>` + values.join("</td><td>") + `</td>
                                <td class="d-flex justify-content-end">
                                    <button class="editTasks action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteTasks action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageTasks > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageTasks > tbody").html(body);
            },
            complete: function() {
                applyEvents();
                $(document).trigger("hideLoadingScreen");
            }
        });
    }

    function applyEvents() {
        // Select all items in the list
        $("#select_all")
            .off()
            .on("click", function (e) {
                var selected = $("#select_all").is(":checked");
                $("#manageTasks").find("tbody").find("tr").each((index, item) => {
                    var current = $(item).find("td").first().find("input");
                    if (!$(current).prop("checked")) {
                        $(current).click();
                    } else if (!selected) {
                        $(current).click();
                    }
                })
            });

        // Select individual item
        $(".item")
            .off()
            .on("click", function (e) {
                var checked = $(this).is(":checked");
                $(this).parent().parent().find("td").last().find("button").each((index, action) => {
                    if ($(this).is(":checked")) {
                        $(action).removeClass("btn-danger").addClass("btn-secondary");
                    } else {
                        $(action).removeClass("btn-secondary").addClass("btn-danger");
                    }
                });
            });

        // SHOW Delete modal
        $('.deleteTasks')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteTasksModal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteTasksModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();
                var button = $(e.relatedTarget);    // Button that triggered the action
                var id = $("#deleteID").val();      // ID of the item

                $.ajax({
                    type: "post",
                    url: "delete",
                    data: {
                        "id": id
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response != true) {
                            // Cannot Delete
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            $(document).trigger("hideLoadingScreen");
                            return false;
                        } else {
                            // Deleted
                            refresh();
                            $('#deleteTasksModal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createTasks, .editTasks').on('click', function (e) {
            e.preventDefault();
            jsonData = $(this).data("json");

            if (typeof jsonData == "undefined") {
                // Create
                tasksData = {};
                $("div[name='id']").hide();
                $("label[for='id']").hide();
                $("input[id='id']").val("").change(); // This is the hidden ID field
                $('#createTasks').text('Create');
            } else {
                // Update
                $("div[name='id']").show();
                $("label[for='id']").show();
                tasksData = JSON.parse(atob(jsonData));

                $("input[id='id']").val(tasksData.id).change(); // This is the hidden ID field
                $("div[name='id']").text(tasksData.id);
                $('#createTasks').text('Edit');
            }            
            
            // This here -> FOREIGN KEY
            // $("input[name='group_id']").val(tasksData.id);

            // Rest I have
            $("input[id='id']").val(tasksData.id);
            $("select[value='" + tasksData.id + "']").attr("selected", "selected");
            $("input[id='taskname']").val(tasksData.taskname);
            $("select[value='" + tasksData.id + "']").attr("selected", "selected");
            $("input[id='complete']").bootstrapToggle(tasksData.tasks_complete == 1 ? 'on' : 'off');
            
            $('#createTasksModal').modal('show');
        });

        $('#createTasksModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createTasksForm").serializeArray();
                if (preData['complete'] === undefined ) preData.push({ 'id' : 'complete', 'value': $('input[id="complete"]').prop('checked') ? 1 : 0 });

                $.ajax({
                    type: "post",
                    url: "tasks/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createTasksModal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>