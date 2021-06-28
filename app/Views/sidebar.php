<style>
    .nav-side-menu {
        overflow: auto;
        font-family: verdana;
        font-size: 12px;
        font-weight: 200;
        background-color: #2e353d;
        position: fixed;
        width: 300px;
        height: 100%;
        color: #e1ffff;
    }

    .nav-side-menu .brand {
        background-color: #23282e;
        line-height: 50px;
        display: block;
        text-align: center;
        font-size: 14px;
    }

    .nav-side-menu .toggle-btn {
        display: none;
    }

    .nav-side-menu ul,
    .nav-side-menu li {
        list-style: none;
        padding: 0px;
        margin: 0px;
        line-height: 35px;
        cursor: pointer;
    }

    .nav-side-menu ul :not(collapsed) .arrow:before,
    .nav-side-menu li :not(collapsed) .arrow:before {
        font-family: FontAwesome;
        content: "\f078";
        display: inline-block;
        padding-left: 10px;
        padding-right: 10px;
        vertical-align: middle;
    }

    .nav-side-menu ul .active,
    .nav-side-menu li .active {
        border-left: 3px solid #d19b3d;
        background-color: #4f5b69;
    }

    .nav-side-menu ul .sub-menu li.active,
    .nav-side-menu li .sub-menu li.active {
        color: #d19b3d;
    }

    .nav-side-menu ul .sub-menu li.active a,
    .nav-side-menu li .sub-menu li.active a {
        color: #d19b3d;
    }

    .nav-side-menu ul .sub-menu li,
    .nav-side-menu li .sub-menu li {
        background-color: #181c20;
        border: none;
        line-height: 28px;
        border-bottom: 1px solid #23282e;
        margin-left: 0px;
    }

    .nav-side-menu ul .sub-menu li:hover,
    .nav-side-menu li .sub-menu li:hover {
        background-color: #020203;
    }

    .nav-side-menu ul .sub-menu li:before,
    .nav-side-menu li .sub-menu li:before {
        font-family: Fontawesome;
        content: "\f105";
        display: inline-block;
        padding-left: 20px;
        padding-right: 10px;
        vertical-align: middle;
    }

    .nav-side-menu li {
        padding-left: 0px;
        border-left: 3px solid #2e353d;
        border-bottom: 1px solid #23282e;
    }

    .nav-side-menu li a {
        text-decoration: none;
        color: #e1ffff;
    }

    .nav-side-menu li a i {
        padding-left: 10px;
        width: 20px;
        padding-right: 20px;
    }

    .nav-side-menu li:hover {
        border-left: 3px solid #d19b3d;
        background-color: #4f5b69;
        -webkit-transition: all 1s ease;
        -moz-transition: all 1s ease;
        -o-transition: all 1s ease;
        -ms-transition: all 1s ease;
        transition: all 1s ease;
    }

    @media (max-width: 767px) {
        .nav-side-menu {
            position: relative;
            width: 100%;
            margin-bottom: 10px;
        }

        .nav-side-menu .toggle-btn {
            display: block;
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            z-index: 10 !important;
            padding: 3px;
            background-color: #ffffff;
            color: #000;
            width: 40px;
            text-align: center;
        }

        .brand {
            text-align: left !important;
            font-size: 22px;
            padding-left: 20px;
            line-height: 50px !important;
        }
    }

    @media (min-width: 767px) {
        .nav-side-menu .menu-list .menu-content {
            display: block;
        }
    }

    .nav-side-menu ul .sub-menu ul .sub-line li.active,
    .nav-side-menu li .sub-menu li .sub-line li.active {
        color: #d19b3d;
    }

    .nav-side-menu ul .sub-menu li .sub-line li.active a,
    .nav-side-menu li .sub-menu li .sub-line li.active a {
        color: #d19b3d;
    }

    .nav-side-menu ul .sub-menu li .sub-line li,
    .nav-side-menu li .sub-menu li .sub-line li {
        background-color: #181c20;
        border: none;
        line-height: 28px;
        border-bottom: 1px solid #23282e;
        margin-left: 0px;
    }

    .nav-side-menu ul .sub-menu li .sub-line li:hover,
    .nav-side-menu li .sub-menu li .sub-line li:hover {
        background-color: #020203;
    }

    .nav-side-menu ul .sub-menu li .sub-line li:before,
    .nav-side-menu li .sub-menu li .sub-line li:before {
        font-family: FontAwesome;
        content: "\f105";
        display: inline-block;
        padding-left: 100px;
        padding-right: 10px;
        vertical-align: middle;
    }

    .nav-side-menu .sub-menu li {
        padding-left: 20px;
        border-left: 3px solid #2e353d;
        border-bottom: 1px solid #23282e;
    }

    .nav-side-menu .sub-menu li a {
        text-decoration: none;
        color: #e1ffff;
    }

    .nav-side-menu li .sub-menu li:hover {
        border-left: 3px solid #d19b3d;
        background-color: #4f5b69;
        -webkit-transition: all 1s ease;
        -moz-transition: all 1s ease;
        -o-transition: all 1s ease;
        -ms-transition: all 1s ease;
        transition: all 1s ease;
    }

    @media (max-width: 767px) {
        .nav-side-menu .sub-menu {
            position: relative;
            width: 100%;
            margin-bottom: 10px;
        }

        .nav-side-menu .sub-menu .toggle-btn {
            display: block;
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            z-index: 10 !important;
            padding: 3px;
            background-color: #ffffff;
            color: #000;
            width: 40px;
            text-align: center;
        }        
    }

    .sub-menu li a i {
        padding-left: 10px;
        width: 20px;
        padding-right: 20px;
    }
</style>
<div class="nav-side-menu">
    <div class="menu-list">
        <div class="brand">Project Name</div>
        <ul id="menu-content" class="menu-content collapse out pl-2">
            <? foreach($menuItems as $item) { ?>
                <li class="active">
                    <a href="/projects/<?= $_SESSION['project_hash']; ?>/preview/<?= strtolower($item['module_route']); ?>">
                        <i class="fa fa-eye fa-lg"></i> <?= $item["module_title"]; ?></span>
                    </a>
                </li>
            <? } ?>
        </ul>
    </div>
</div>
