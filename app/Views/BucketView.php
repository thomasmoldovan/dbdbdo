<div class="btn-group mb-3">
    <a id="createBucketButton" href="#">
        <button type="button" class="createBucket btn btn-sm btn-danger mt-3" data-toggle="modal">
            Create 
            <?= singular("Bucket") ?>
        </button>
    </a>
</div>
<div class="row-fluid p-0">
    <table id="manageBucket" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="createBucketModal" tabindex="-1" role="dialog" aria-labelledby="createBucketModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createBucketForm" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createBucket">
                                Create bucket 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful bucket ever</small> </div>
                    </div>
                </div>
                <div class="error"></div>
                <div class="modal-body">
                    <div class="the-body">
                        <form>
                            <label for='id' class='w-100 mb-0 bold pr-3'>id</label>
                            <div id='id' name='id' class='form-control form-control-sm' readonly=''></div>
                            <label for='fCheckbox' class='w-100 mb-0 bold pr-3'>fCheckbox</label>
                            <input id='fCheckbox' name='fCheckbox' class='form-control form-control-sm' type='checkbox' data-toggle='toggle' data-size='sm'/>
                            <label for='fRadio' class='w-100 mb-0 bold pr-3'>fRadio</label>
                            <input id='fRadio' name='fRadio' class='form-control form-control-sm'/>
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
<div class="modal fade" id="deleteBucketModal" tabindex="0" role="dialog" aria-labelledby="deleteBucketModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteBucket">
                            Delete bucket 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteBucketForm" class="col-12" method="post" action="delete">
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
            url: "bucket/list",
            data: {
                "project_hash": "<?= $_SESSION["project_hash"]; ?>"
            },
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.bucketItems, function(index, row) {
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
                                    <button class="editBucket action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteBucket action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageBucket > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageBucket > tbody").html(body);
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
                $("#manageBucket").find("tbody").find("tr").each((index, item) => {
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
        $('.deleteBucket')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteBucketModal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteBucketModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();
                var button = $(e.relatedTarget);    // Button that triggered the action
                var id = $("#deleteID").val();      // ID of the item

                $.ajax({
                    type: "post",
                    url: "bucket/delete",
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
                            $('#deleteBucketModal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createBucket, .editBucket')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                jsonData = $(this).data("json");

                if (typeof jsonData == "undefined") {
                    // Create
                    bucketData = {};
                    $("div[name='id']").hide();
                    $("label[for='id']").hide();
                    $("input[id='id']").val("").change(); // This is the hidden ID field
                    $('#createBucket').text('Create');
                } else {
                    // Update
                    $("div[name='id']").show();
                    $("label[for='id']").show();
                    bucketData = JSON.parse(atob(jsonData));

                    $("input[id='id']").val(bucketData.id).change(); // This is the hidden ID field
                    $("div[name='id']").text(bucketData.id);
                    $('#createBucket').text('Edit');
                }            
                
                // This here -> FOREIGN KEY
                // $("input[name='group_id']").val(bucketData.id);

                // Rest I have
                $("input[id='id']").val(bucketData.id);
$("input[id='fCheckbox']").bootstrapToggle(bucketData.bucket_fCheckbox == 1 ? 'on' : 'off');
$("input[id='fRadio']").val(bucketData.fRadio);
                
                $('#createBucketModal').modal('show');
            });

        $('#createBucketModal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createBucketForm").serializeArray();
                $.ajax({
                    type: "post",
                    url: "bucket/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createBucketModal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>