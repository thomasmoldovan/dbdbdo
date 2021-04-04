<? echo view("projects/menu"); ?>
<style>
    .projectCard { transition: background-color 0.2s ease; }
    .card-normal { background: #ecf0f1; }
    .card-hover { background: #5ABDFF; cursor: pointer; }
</style>
<div class="h-100 pr-3 pl-3 pb-3">
    <div class="row h-100">
        <? foreach($data["project_list"] as $project) { ?>
            <div class="projectCard col-2 mb-3 ml-3 mr-3 p-3 card-normal">
                <div class="media-body">
                    <strong><?= $project->project_name; ?></strong>
                    <small class="float-right"><?= $project->project_hash; ?></small>
                    <br>
                    <small class="bold">The description of the project</small>
                    <hr class="mt-2 mb-2 bg-success">
                    <div class="row d-flex">
                        <div class="col-12">
                            <div class="small"><i class="fa fa-pen mr-2"></i>5 Tables</div>
                            <div class="small pb-2"><i class="fa fa-pen mr-2"></i>9 Pages</div>
                        </div>
                        <div class="col-12">
                            <div class="small float-right"><b>Updated: </b><?= $project->updated_at; ?></div>
                        </div>
                        <div class="col-12">
                            <div class="small float-right"><b>Created: </b><?= $project->created_at; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>
<script>
$(document).ready(function () {
    $(".projectCard").hover(function () {
            $(this).removeClass("card-normal").addClass("card-hover");            
        }, function () {
            $(this).removeClass("card-hover").addClass("card-normal");
        }
    );
});
</script>
