<? if (!isset($navigation) || $navigation !== false) { ?>
    <nav class="underbar navbar navbar-expand-sm navbar-dark bg-dark pr-2">
        <a class="navbar-brand" href="/">DbDbDo<small><?= $auth->check() ? ".online" : ".website"; ?></small></a>

        <? if (!$auth->check()) { ?>
            <a class="bold pr-3 mt-1" href="/status">Status</a>
        <? } else { ?>
            <? if (has_permission("Todo")) { ?>
                <a class="bold pr-3 mt-1" href="/tasks">Tasks</a>
            <? } ?>
            <a class="bold pr-3 mt-1" href="/projects">Projects</a>
            <a class="bold pr-3 mt-1" href="/tags">Tags</a>
            <a class="bold pr-3 mt-1" href="/imports">Imports</a>
        <? } ?>

        <!-- The space in the middle -->
        <div class="collapse navbar-collapse mt-1"></div>

        <!-- IF LOGGED -->
        <div class="text-white pr-2">
            <div class="dropdown">
                <? if ($auth->check()) { ?>
                    <?= isset($data["project"]->project_hash) ? $data["project"]->project_name." - ".$data["project"]->project_hash : ""; ?>
                    <button class="btn btn-sm btn-secondary no-outline <?= $auth->check() ? "dropdown-toggle" : ""; ?>" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= "Welcome ".$user->username; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="profile">Profile Settings</a>
                        <a class="dropdown-item" id="clearData" href="/projects/clearStuff">Clear Data</a>
                        <a class="dropdown-item" href="/logout">Logout</a>
                    </div>
                <? } else { ?>
                    <a class="btn btn-sm btn-primary" type="button" href="/login">
                        <?= lang("Auth.signIn"); ?>
                    </a>                
                <? } ?>
            </div>
        </div>
        <? if ($auth->check()) { ?>
            <div class="float-right pr-2">
                <a href="preview"><button class="btn btn-primary btn-sm">Goto FrontEnd</button></a>
            </div>
        <? } ?>
    </nav>
<? } ?>
