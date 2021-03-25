<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>DbDbDo.online</title>

    <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

    <script src="/js/jquery.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/bootstrap4-toggle.min.js"></script>
    <script src="/js/toastr.min.js"></script>
    
    <link href="/css/theme/bootstrap.min.css" rel="stylesheet"></link>
    <!-- <link href="/css/bootstrap.min.css" rel="stylesheet"></link> -->
    <link href="/css/bootstrap4-toggle.min.css" rel="stylesheet"></link>
    <link href="/css/toastr.min.css" rel="stylesheet"></link>
    <link href="/css/all.min.css" rel="stylesheet"></link>

</head>

<!-- This can only be seen in Development enviroment -->
<? if (ENVIRONMENT === "Development") { ?>
    <div class="small text-center">
        <b>_DIR_</b> - <?= __DIR__ ?> | <b>SYSTEMPATH</b> - <?= SYSTEMPATH ?> | <b>ENVIRONMENT</b> - <?= ENVIRONMENT ?> | <b>APPPATH</b> - <?= APPPATH ?> | <b>BASE_URL</b> - <?= base_url() ?> | <b>DB</b> - <?= $_ENV["database.default.database"] ?> | <b>DEBUG</b> - <?= CI_DEBUG ?>
    </div>
<? } else { echo "Nada"; } ?>
