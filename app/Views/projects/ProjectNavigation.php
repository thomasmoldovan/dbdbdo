<div class="d-flex">
    <div class="col-12 mt-3">
        <? if (isset($data["project"])) { ?>
            <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/tables"> <button class="btn btn-primary btn-sm"><i class="fa fa-table"></i>&nbsp;Tables</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/modules"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Modules</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/fk"> <button class="btn btn-primary btn-sm"><i class="fa fa-key"></i>&nbsp;Links</button></a>

            <label for="projectType" class="pl-5 small">Project Type</label>
            <input id="projectType" type="checkbox" 
                data-save="<?= handledata(array('projects' => 'project_type', 'id' => $data["project"]["id"])) ?>" 
                data-toggle="toggle" data-size="xs" data-on="Internal" data-off="External" data-onstyle="primary" data-offstyle="danger"
                <? if ($data["project"]["project_type"] == 1) echo "checked"; ?> disabled>
        <? } else { ?>
            <? if (isset($data["project_list"]) && count($data["project_list"])) { ?>
                <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
            <? } ?>
        <? } ?>

        <div class="d-flex float-right">
            <? if (isset($data["project"])) { ?>
                <a href="/projects/<?= $data["project"]["project_hash"]; ?>/settings"> <button class="btn btn-primary btn-sm"><i class="fa fa-cog"></i>&nbsp;Settings</button></a>
            <? } ?>
        </div>
    </div>
</div>
<hr>