<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="Passor" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>DbDbDo.online</title>

    <script src="/js/jquery.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/bootstrap4-toggle.min.js"></script>
    <script src="/js/toastr.min.js"></script>
    
    <link href="/css/theme/bootstrap.min.css" rel="stylesheet"></link>
    <link href="/css/bootstrap4-toggle.min.css" rel="stylesheet"></link>
    <link href="/css/toastr.min.css" rel="stylesheet"></link>
    <link href="/css/all.min.css" rel="stylesheet"></link>
    
    <!-- <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"></link> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"></link> -->
    <script src="/js/common.js"></script>
</head>

<style {csp-style-src}>
    body { background: #aaaaaa; }
    html { background: #aaaaaa; }
    main { background: #aaaaaa; }

    .hide { display: none; }
    .bold { font-weight: bold; }

    .background-light { background: #1d1a18; }
    .height-limit-40 { height: 40px; }

    .status-danger { border: 2px solid #990; }
    .status-success { border: 2px solid #090; }
    .status-error { border: 2px solid #900; }

    .outline-danger { border: 2px solid #990; }
    .outline-success { border: 2px solid #090; }
    .outline-error { border: 2px solid #900; }

    .txt-danger { color: #990; }
    .txt-success { color: #090; }
    .txt-error { color: #900; }

    .projectCard { transition: background-color 0.2s ease; min-width: 290px; max-width: 345px; }
    .card-normal { background: #ecf0f1; }
    .card-hover { background: #91d3ff; cursor: pointer; }

    .no-outline { outline: none !important; box-shadow: none !important; }

    i { color: white; }
    .nav-link { color: white !important; }
    .nav-item { color: white; }
    .nav-link:hover { color: #ffaf36 !important; text-decoration: underline; }

    .loading-container {
        position: fixed;
        top:0px;
        left:0px;
        right:0px;
        bottom: 0px;
        z-index:10000;
        display: flex;
        justify-content: center!important;
        align-items: center!important;
    }
    .loading-container div {
        background-color: #000;
        padding: 15px;
        border-radius: 5px;
        color:#fff0f0;
        opacity: .8;
    }
    .loading-overlay {
        background-color: #000;
        opacity: .5;
    }
    input:focus,
    select:focus,
    textarea:focus,
    button:focus, button:active {
        box-shadow: none !important;
        outline: none !important;
    }
</style>

<body>
    <? echo view('App\WebsiteNavigation'); ?>
    <? //echo view('App\debugbar'); ?>

    <? // echo in_groups(["Publisher"]) ? "TRUE" : "FALSE"; ?>
    <? // echo has_permission("Everything") ? "TRUE" : "FALSE"; ?>

    <div id="loading-main" style="display: none;" tabindex="-1">
        <div class="col-12 loading-container text-white text-center">
            <div><i id="generic-spinner" class="fa fa-cog fa-spin mr-2"></i> 
                <span>Processing</span>
            </div>
        </div>
        <div class="loading-container loading-overlay">
        </div>
    </div>
    <div class="">
        <div class="sidebarDiv"><?= view("sidebar", $data); ?></div>
        <div class="rightDiv"><?= view("right", []); ?></div>
    </div>
</body>
<script>
    $(document).ready(function () {
        // Show notifications that came through $_SESSION
        notifications = <?= json_encode(isset($_SESSION["notification"]) ? $_SESSION["notification"] : ""); ?>;
        if (notifications.length > 0) {
            <? //unset($_SESSION["notification"]); ?>
            $.each(notifications, function(index, value) {
                toastr[value[0]](value[1]);
            });
        }

        $("select").change(function () { 
            $(this).blur();
        });
    });
</script>
