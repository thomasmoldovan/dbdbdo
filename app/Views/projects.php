<? echo view("projects/menu"); ?>
<div class="h-100 pb-3">
    <div class="d-flex flex-wrap">
        <? foreach($data["project_list"] as $project) { ?>
            <div class="projectCard mb-3 ml-3 mr-3 p-3 card-normal" data-id="<?= $project->project_hash; ?>">
                <div class="media-body">
                    <strong><?= $project->project_name; ?></strong>
                    <small class="float-right"><?= $project->project_hash; ?></small>
                    <br>
                    <small class="bold"><?= $project->project_description; ?></small>
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
                            <button class="btn btn-sm btn-success"><span><i class="fa fa-pen text-white"></i></span></button>
                            <button class="btn btn-sm btn-danger"><span><i class="fa fa-trash text-white"></i></span></button>
                            <div class="small float-right"><b>Last Updated: </b><?= $project->updated_at; ?></div>
                        </div>
                    </div>
                </div>
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
        window.location = "http://localhost:8080/projects/" + $(this).data("id");
    });
});
</script>
