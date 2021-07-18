<? echo view("projects/ProjectNavigation"); ?>
<div class="h-100 pb-3">
    <div class="d-flex flex-wrap">
        <? if (count($data["project_list"])) { ?>
            <? foreach($data["project_list"] as $project) { ?>
                <div class="projectCard mb-3 ml-3 mr-3 p-3 card-normal" data-id="<?= $project->project_hash; ?>">
                    <div class="media-body">
                        <strong><?= $project->project_name; ?></strong>
                        <small class="float-right">
                            <?= $project->project_type == 1 ? '<i class="fa fa-ban text-danger"></i>' : ""; ?>
                            <?= $project->project_hash; ?>
                        </small>
                        <br>
                        <hr class="mt-2 mb-2 bg-success">
                        <div class="row d-flex">
                            <div class="col-6">
                                <div class="small pb-2"><i class="fa fa-table mr-2 text-success"></i><?= $project->count_table_name; ?> Tables</div>
                                <div class="small pb-2"><i class="fa fa-plus mr-2 text-success"></i><?= $project->count_modules; ?> Modules</div>
                            </div>
                            <div class="col-6">
                                <div class="small pb-2"><i class="fa fa-bars mr-2 text-success"></i><?= $project->count_column_name; ?> Columns</div>
                                <div class="small pb-2"><i class="fa fa-link mr-2 text-success"></i><?= $project->count_links; ?> Links</div>
                            </div>
                            <div class="col-12 pt-1">
                                <div class="btn btn-sm btn-danger deleteProject"><span><i class="fa fa-trash-o text-white"></i></span></div>
                                <div class="small float-right pt-2"><b>Last Updated: </b><?= $project->updated_at; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
        <? } else { ?>
            <div class="w-100 text-center">
                <h4>No projects found</h4>
                <h6>How about we create one</h6>
                <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
            </div>
        <? } ?>
        
    </div>
</div>
<script>
$(document).ready(function () {
    // Project Card actions
    $(".projectCard").hover(function () {
            $(this).removeClass("card-normal").addClass("card-hover");            
        }, function () {
            $(this).removeClass("card-hover").addClass("card-normal");
        }
    );
    $(".projectCard").click(function () {
        window.location = "/projects/" + $(this).data("id");
    });

    $(".deleteProject").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $.ajax({
            type: "post",
            url: "/projects/delete",
            data: {
                "project_hash": $(this).parents(".projectCard").data("id")
            },
            dataType: "dataType",
            success: function (response) {
                window.location = location;
            }
        });
    });
});
</script>
