<div class="btn-group mb-3">
    <a id="createColorsButton" href="#">
        <button type="button" class="createColors btn btn-sm btn-danger mt-3" data-toggle="modal">
            Create 
            <?= singular("Colors") ?>
        </button>
    </a>
</div>
<div class="row-fluid p-0">
    <table id="manageColors" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="createColorsModal" tabindex="-1" role="dialog" aria-labelledby="createColorsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createColorsForm" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createColors">
                                Create colors 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful colors ever</small> </div>
                    </div>
                </div>
                <div class="error"></div>
                <div class="modal-body">
                    <div class="the-body">
                        <form>
                            <label for='id' class='w-100 mb-0 bold pr-3'>id</label>
                            <div id='id' name='id' class='form-control form-control-sm' readonly=''></div>
                            <label for='name' class='w-100 mb-0 bold pr-3'>name</label>
                            <input id='name' name='name' class='form-control form-control-sm'/>
                            <label for='value' class='w-100 mb-0 bold pr-3'>value</label>
                            <input id='value' name='value' class='form-control form-control-sm'/>
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
<div class="modal fade" id="deleteColorsModal" tabindex="0" role="dialog" aria-labelledby="deleteColorsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteColors">
                            Delete colors 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteColorsForm" class="col-12" method="post" action="delete">
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
<script>
    $(document).ready(function () {
        refresh();
    });

    function refresh() {
        $(document).trigger("showLoadingScreen");
        $.ajax({
            type: "post",
            url: "colors/list",
            data: {
                "project_hash": "<?= $_SESSION["project_hash"]; ?>"
            },
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.colorsItems, function(index, row) {
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
                                    <button class="editColors action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteColors action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageColors > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageColors > tbody").html(body);
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
                $("#manageColors").find("tbody").find("tr").each((index, item) => {
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
        $('.deleteColors')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteColorsModal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteColorsModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();
                var button = $(e.relatedTarget);    // Button that triggered the action
                var id = $("#deleteID").val();      // ID of the item

                $.ajax({
                    type: "post",
                    url: "colors/delete",
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
                            $('#deleteColorsModal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createColors, .editColors')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                jsonData = $(this).data("json");

                if (typeof jsonData == "undefined") {
                    // Create
                    colorsData = {};
                    $("div[name='id']").hide();
                    $("label[for='id']").hide();
                    $("input[id='id']").val("").change(); // This is the hidden ID field
                    $('#createColors').text('Create');
                } else {
                    // Update
                    $("div[name='id']").show();
                    $("label[for='id']").show();
                    colorsData = JSON.parse(atob(jsonData));

                    $("input[id='id']").val(colorsData.id).change(); // This is the hidden ID field
                    $("div[name='id']").text(colorsData.id);
                    $('#createColors').text('Edit');
                }            
                
                // This here -> FOREIGN KEY
                // $("input[name='group_id']").val(colorsData.id);

                // Rest I have
                $("input[id='id']").val(colorsData.id);
$("input[id='name']").val(colorsData.name);
$("input[id='value']").val(colorsData.value);
                
                $('#createColorsModal').modal('show');
            });

        $('#createColorsModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createColorsForm").serializeArray();
                $.ajax({
                    type: "post",
                    url: "colors/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createColorsModal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>