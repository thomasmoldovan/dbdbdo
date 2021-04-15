<nav class="underbar navbar navbar-expand-sm navbar-dark bg-dark pr-2">
    <a class="navbar-brand" href="/">DbDbDo<small><?= $auth->check() ? ".online" : ".website"; ?></small></a>

    <? if (!$auth->check()) { ?>
        <a class="bold pr-3 mt-1" href="/status">Status</a>
    <? } else { ?>
        <? if (has_permission("Todo")) { ?>
            <a class="bold pr-3 mt-1" href="/todo">To Do</a>
        <? } ?>
        <a class="bold pr-3 mt-1" href="/projects">Projects</a>
        <a class="bold pr-3 mt-1" href="/contact">Contact</a>
    <? } ?>

    <!-- IF user IS LOGGED in and a PROJECT is SELECTED -->
    <div id="navbarNav" class="collapse navbar-collapse mt-1">
        <? if (isset($projectSelected)) { ?>
            <ul class="navbar-nav text-white">
                <? if (count($tables) > 0) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/projects">
                            <i class="fas fa-table pl-2 pr-2"></i>Tables<span class="badge badge-primary ml-2"><?= count($tables); ?></span>
                        </a>
                    </li>
                <? } ?>

                <li class="nav-item active">
                    <a class="nav-link" href="/modules">
                        <i class="fas fa-universal-access pl-2 pr-2"></i>Modules<span class="badge badge-primary ml-2"><?= $userModules[0]["modules_in_project"]; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/foreignkeys">
                        <i class="fas fa-key pl-2 pr-2"></i>Foreign Keys
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/projects/clearStuff">
                        <i class="fas fa-ban pl-2 pr-2"></i>Clear
                    </a>
                </li>
            </ul>
        <? } ?>
    </div>

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
                    <a class="dropdown-item" id="clearData" href="projects/clearStuff">Clear Data</a>
                    <a class="dropdown-item" href="/logout">Logout</a>
                </div>
            <? } else { ?>
                <a class="btn btn-sm btn-secondary" type="button" href="/login">
                    <?= lang("Auth.signIn"); ?>
                </a>                
            <? } ?>
        </div>
    </div>
    <? if ($auth->check()) { ?>
        <div class="float-right pr-2">
            <a href="home"><button class="btn btn-primary btn-sm">Goto FrontEnd</button></a>
        </div>
    <? } ?>
</nav>
