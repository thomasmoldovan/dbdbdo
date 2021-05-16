<div class="d-flex">
    <div class="col-12 mt-3">
        <? if (isset($data["project"])) { ?>
            <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/tables"> <button class="btn btn-primary btn-sm"><i class="fa fa-table"></i>&nbsp;Tables</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/modules"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Modules</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/fk"> <button class="btn btn-primary btn-sm"><i class="fa fa-key"></i>&nbsp;Links</button></a>
        <? } else { ?>
            <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
        <? } ?>

        <label for="projectType" class="pl-5 small">Project Type</label>
        <input id="projectType" type="checkbox" data-toggle="toggle" data-size="xs" data-on="Common" data-off="System" data-onstyle="primary" data-offstyle="danger">

        <label for="previewType" class="pl-5 small">Preview Type</label>
        <input id="previewType" type="checkbox" checked data-toggle="toggle" data-size="xs" data-on="Internal" data-off="External" data-onstyle="primary" data-offstyle="danger">

        <div class="d-flex float-right">
            <div class="pr-3">8 Modules left to use</div>
            <div>24 Columns left to use</div>
            <? if (isset($data["project"])) { ?>
                <a href="/projects/<?= $data["project"]["project_hash"]; ?>/settings"> <button class="btn btn-primary btn-sm"><i class="fa fa-cog"></i>&nbsp;Settings</button></a>
            <? } ?>
        </div>
    </div>
</div>
<hr>