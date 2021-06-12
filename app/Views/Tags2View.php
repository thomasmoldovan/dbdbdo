<div class="btn-group mb-3">
    <a id="createTags2Button" href="#">
        <button type="button" class="createTags2 btn btn-sm btn-danger mt-3" data-toggle="modal"> Create Tags2</button>
    </a>
</div>
<div class="row-fluid p-0">
    <table id="manageTags2" class="table table-light table-hover">
        <thead class="thead-light">
            <tr></tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="modal fade" id="createTags2Modal" tabindex="-1" role="dialog" aria-labelledby="createTags2Modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createTags2Form" class="col-12" method="post" action="create">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <div class="row">
                            <h5 class="modal-title" id="createTags2">
                                Create tags2 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                        </div>
                        <div class="row"> <small id="smallTitle" class="form-text text-muted">These are the most beautiful tags2 ever</small> </div>
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
                            <label for='start_tag' class='w-100 mb-0 bold pr-3'>start_tag</label>
                            <input id='start_tag' name='start_tag' class='form-control form-control-sm'/>
                            <label for='end_tag' class='w-100 mb-0 bold pr-3'>end_tag</label>
                            <input id='end_tag' name='end_tag' class='form-control form-control-sm'/>
                            <label for='value_type' class='w-100 mb-0 bold pr-3'>value_type</label>
                            <input id='value_type' name='value_type' class='form-control form-control-sm'/>
                            <label for='properties_id_list' class='w-100 mb-0 bold pr-3'>properties_id_list</label>
                            <input id='properties_id_list' name='properties_id_list' class='form-control form-control-sm'/>
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
<div class="modal fade" id="deleteTags2Modal" tabindex="0" role="dialog" aria-labelledby="deleteTags2Modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <div class="row">
                        <h5 class="modal-title" id="deleteTags2">
                            Delete tags2 
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                    </div>
                </div>
            </div>
            <div class="error"></div>
            <form id="deleteTags2Form" class="col-12" method="post" action="delete">
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
            url: "<?= base_url() ?>/tags2/manageTags2",
            data: $("#formAddTags2").serialize(),
            dataType: "json",
            success: function (response) {
                var body = "";
                if (response.empty == true) return false;
                $.each(response.tags2Items, function(index, row) {
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
                                    <button class="editTags2 action btn btn-success btn-sm mr-2" title="Edit" data-id="` + row.id + `"
                                        data-json="` + btoa(check) + `">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="deleteTags2 action btn btn-danger btn-sm" title="Delete" data-id="` + row.id + `">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                });
                
                $("#manageTags2 > thead > tr").html('<th><input type="checkbox" id="select_all"></th><th>' + response.headers.join('</th><th>') + '</th><th style="text-align: right;">Actions</th>');
                $("#manageTags2 > tbody").html(body);
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
                $("#manageTags2").find("tbody").find("tr").each((index, item) => {
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
        $('.deleteTags2')
            .off()
            .on('click', function (e) {
                e.preventDefault();
                id = $(this).data("id");
                $("#deleteID").val(id);
                $('#deleteTags2Modal').modal('show');
            });

        // SUBMIT Delete modal
        $('#deleteTags2Modal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();
                var button = $(e.relatedTarget);    // Button that triggered the action
                var id = $("#deleteID").val();      // ID of the item

                $.ajax({
                    type: "post",
                    url: "tags2/delete",
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
                            $('#deleteTags2Modal').modal("hide");
                            $(document).trigger("hideLoadingScreen");
                            return true;
                        }
                    }
                });
            });

        // CREATE or UPDATE
        $('.createTags2, .editTags2').on('click', function (e) {
            e.preventDefault();
            jsonData = $(this).data("json");

            if (typeof jsonData == "undefined") {
                // Create
                tags2Data = {};
                $("div[name='id']").hide();
                $("label[for='id']").hide();
                $("input[id='id']").val("").change(); // This is the hidden ID field
                $('#createTags2').text('Create');
            } else {
                // Update
                $("div[name='id']").show();
                $("label[for='id']").show();
                tags2Data = JSON.parse(atob(jsonData));

                $("input[id='id']").val(tags2Data.id).change(); // This is the hidden ID field
                $("div[name='id']").text(tags2Data.id);
                $('#createTags2').text('Edit');
            }            
            
            // This here -> FOREIGN KEY
            // $("input[name='group_id']").val(tags2Data.id);

            // Rest I have
            $("input[id='id']").val(tags2Data.id);
$("input[id='name']").val(tags2Data.name);
$("input[id='start_tag']").val(tags2Data.start_tag);
$("input[id='end_tag']").val(tags2Data.end_tag);
$("input[id='value_type']").val(tags2Data.value_type);
$("input[id='properties_id_list']").val(tags2Data.properties_id_list);
            
            $('#createTags2Modal').modal('show');
        });

        $('#createTags2Modal')
            .off()
            .on('submit.bs.modal', function (e) {
                e.preventDefault();

                preData = $("#createTags2Form").serializeArray();
                

                $.ajax({
                    type: "post",
                    url: "tags2/create",
                    data: $.param(preData),
                    dataType: "json",
                    success: function (response) {
                        if (response.errors != "") {
                            $(".error").html('<div class="alert alert-danger m-0">' + response.errors + '</div>');
                            return false;
                        } else {
                            refresh();
                            $('#createTags2Modal').modal("hide");
                            return true;
                        }
                    }
                });
            });
    }
</script>