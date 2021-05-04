<div class="btn-group mb-3">
    <a id="createUsersButton" href="#">
        <button type="button" class="createUsers btn btn-sm btn-danger mt-3" data-toggle="modal">
            Create 
            <?= singular("Users") ?>
        </button>
    </a>
</div>
<div class="row-fluid p-0">
    <table id="manageUsers" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="createUsersModal" tabindex="-1" role="dialog" aria-labelledby="createUsersModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createUsersForm" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createUsers">
                                Create users 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful users ever</small> </div>
                    </div>
                </div>
                <div class="error"></div>
                <div class="modal-body">
                    <div class="the-body">
                        <form>
                            <label for='id' class='w-100 mb-0 bold pr-3'>ID</label>
                            <div id='id' name='id' class='form-control form-control-sm' readonly=''></div>
                            <label for='email' class='w-100 mb-0 bold pr-3'>Email</label>
                            <input id='email' name='email' class='form-control form-control-sm'/>
                            <label for='username' class='w-100 mb-0 bold pr-3'>Username</label>
                            <input id='username' name='username' class='form-control form-control-sm'/>
                            <label for='activate_hash' class='w-100 mb-0 bold pr-3'>Activate Hash</label>
                            <input id='activate_hash' name='activate_hash' class='form-control form-control-sm'/>
                            <label for='active' class='w-100 mb-0 bold pr-3'>Active</label>
                            <input id='active' name='active' class='form-control form-control-sm'/>
                            <label for='force_pass_reset' class='w-100 mb-0 bold pr-3'>Force Pass Reset</label>
                            <input id='force_pass_reset' name='force_pass_reset' class='form-control form-control-sm'/>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit" class="btn btn-info" value="Submit">Save</button>
                    <button type="submit" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="hidden" id="id" name="id" value=""/>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteUsersModal" tabindex="0" role="dialog" aria-labelledby="deleteUsersModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteUsers">
                            Delete users 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteUsersForm" class="col-12" method="post" action="delete">
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
<script src="/js/common.js"></script>
<script>
    $(document).ready(function () {
        refresh();
    });

    function refresh() {
        $(document).trigger("showLoadingScreen");
        $.ajax({
            type: "post",
            url: "users/list",
            data: {
                "project_hash": "<?= $_SESSION["project_hash"]; ?>"
            },
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.usersItems, function(index, row) {
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
                                    <button class="editUsers action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteUsers action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageUsers > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageUsers > tbody").html(body);
                $(document).trigger("hideLoadingScreen");
            },
            error: function(response) {
                $(document).trigger("hideLoadingScreen");
            },
            complete: function(response) {
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
                $("#manageUsers").find("tbody").find("tr").each((index, item) => {
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
        $('.deleteUsers')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteUsersModal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteUsersModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();
                var button = $(e.relatedTarget);    // Button that triggered the action
                var id = $("#deleteID").val();      // ID of the item

                $.ajax({
                    type: "post",
                    url: "users/delete",
                    data: {
                        "id": id,
                        "project_hash": "<?= $_SESSION["project_hash"]; ?>"
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
                            $('#deleteUsersModal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createUsers, .editUsers')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                jsonData = $(this).data("json");

                if (typeof jsonData == "undefined") {
                    // Create
                    usersData = {};
                    $("div[name='id']").hide();
                    $("label[for='id']").hide();
                    $("input[id='id']").val("").change(); // This is the hidden ID field
                    $('#createUsers').text('Create');
                } else {
                    // Update
                    $("div[name='id']").show();
                    $("label[for='id']").show();
                    usersData = JSON.parse(atob(jsonData));

                    $("input[id='id']").val(usersData.id).change(); // This is the hidden ID field
                    $("div[name='id']").text(usersData.id);
                    $('#createUsers').text('Edit');
                }            
                
                // This here -> FOREIGN KEY
                // $("input[name='group_id']").val(usersData.id);

                // Rest I have
                $("input[id='id']").val(usersData.id);
$("input[id='email']").val(usersData.email);
$("input[id='username']").val(usersData.username);
$("input[id='activate_hash']").val(usersData.activate_hash);
$("input[id='active']").val(usersData.active);
$("input[id='force_pass_reset']").val(usersData.force_pass_reset);
                
                $('#createUsersModal').modal('show');
            });

        $('#createUsersModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createUsersForm").serializeArray();
                $.ajax({
                    type: "post",
                    url: "users/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createUsersModal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>