<div class="btn-group mb-3">
    <a id="createImportsButton" href="#">
        <button type="button" class="createImports btn btn-sm btn-danger mt-3" data-toggle="modal"> Create Imports</button>
    </a>
</div>
<div class="row-fluid p-0">
    <table id="manageImports" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="createImportsModal" tabindex="-1" role="dialog" aria-labelledby="createImportsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createImportsForm" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createImports">
                                Create imports 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful imports ever</small> </div>
                    </div>
                </div>
                <div class="error"></div>
                <div class="modal-body">
                    <div class="the-body">
                        <form>
                            <label for='id' class='w-100 mb-0 bold pr-3'>id</label>
                            <div id='id' name='id' class='form-control form-control-sm' readonly=''></div>
                            <label for='user_id' class='w-100 mb-0 bold pr-3'>user_id</label>
                            <input id='user_id' name='user_id' class='form-control form-control-sm'/>
                            <label for='query' class='w-100 mb-0 bold pr-3'>query</label>
                            <input id='query' name='query' class='form-control form-control-sm'/>
                            <label for='result' class='w-100 mb-0 bold pr-3'>result</label>
                            <input id='result' name='result' class='form-control form-control-sm'/>
                            <label for='run_at' class='w-100 mb-0 bold pr-3'>run_at</label>
                            <input id='run_at' name='run_at' class='form-control form-control-sm'/>
                            <label for='approved' class='w-100 mb-0 bold pr-3'>approved</label>
                            <input id='approved' name='approved' class='form-control form-control-sm'/>
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
<div class="modal fade" id="deleteImportsModal" tabindex="0" role="dialog" aria-labelledby="deleteImportsModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteImports">
                            Delete imports 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteImportsForm" class="col-12" method="post" action="delete">
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
        refresh();
    });

    function refresh() {
        $(document).trigger("showLoadingScreen");
        $.ajax({
            type: "post",
            url: "/projects/tcaaa50/preview/imports/list",
            data: $("#formAddImports").serialize(),
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.importsItems, function(index, row) {
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
                                    <button class="editImports action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteImports action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageImports > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageImports > tbody").html(body);
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
                $("#manageImports").find("tbody").find("tr").each((index, item) => {
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
        $('.deleteImports')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteImportsModal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteImportsModal')
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
                            $('#deleteImportsModal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createImports, .editImports').on('click', function (e) {
            e.preventDefault();
            jsonData = $(this).data("json");

            if (typeof jsonData == "undefined") {
                // Create
                importsData = {};
                $("div[name='id']").hide();
                $("label[for='id']").hide();
                $("input[id='id']").val("").change(); // This is the hidden ID field
                $('#createImports').text('Create');
            } else {
                // Update
                $("div[name='id']").show();
                $("label[for='id']").show();
                importsData = JSON.parse(atob(jsonData));

                $("input[id='id']").val(importsData.id).change(); // This is the hidden ID field
                $("div[name='id']").text(importsData.id);
                $('#createImports').text('Edit');
            }            
            
            // This here -> FOREIGN KEY
            // $("input[name='group_id']").val(importsData.id);

            // Rest I have
            $("input[id='id']").val(importsData.id);
$("input[id='user_id']").val(importsData.user_id);
$("input[id='query']").val(importsData.query);
$("input[id='result']").val(importsData.result);
$("input[id='run_at']").val(importsData.run_at);
$("input[id='approved']").val(importsData.approved);
            
            $('#createImportsModal').modal('show');
        });

        $('#createImportsModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createImportsForm").serializeArray();
                

                $.ajax({
                    type: "post",
                    url: "imports/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createImportsModal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>