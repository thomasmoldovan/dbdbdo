<nav class="underbar navbar navbar-expand-sm navbar-dark bg-dark pr-2">
    <a class="navbar-brand" href="/">DbDbDo<small><?= $auth ? ".online" : ".website"; ?></small></a>

    <div class="text-white pr-2">
        <? if ($auth) { ?>
            <? if (isset($_SESSION["project_hash"]) && $_SESSION["project_hash"] != "") { ?>
                <a href="/projects" class="nav-link"><div class="pt-1 pl-2"><i class="fas fa-arrow-left text-success pr-2"></i>Back</div></a>
            <? } else { ?>
                <? redirect()->to('/projects/unload'); ?>
            <? } ?>
        <? } ?>
    </div> 
</nav>
