<div class="d-flex">
    <div class="col-12 mt-3">
        <? if (isset($data["project"])) { ?>
            <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/tables"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Tables</button></a>
            <a href="/projects/<?= $data["project"]["project_hash"]; ?>/modules"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;Modules</button></a>
        <? } else { ?>
            <a href="/projects/create"> <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;New Project</button></a>
        <? } ?>
        <div class="d-flex float-right">
            <div class="pr-3">8 Modules left to use</div>
            <div>24 Columns left to use</div>
        </div>
    </div>
</div>
<hr>