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

    <script src="/js/common.js"></script>
</head>

<style {csp-style-src}>
    html, body {
        color: rgba(33, 37, 41, 1);
        /* font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji"; */
        font-size: 16px;
        background: gainsboro;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        transition: all 0.3s ease-out ease-in;
    }

    input:-webkit-autofill::first-line {
        font-family: 'Helvetica Neue', 'Arial', Arial, sans-serif !important;
        font-size: 16px !important;
    }
</style>

<body>
    <?
    echo view('App\navigation');
    // echo view('App\debugbar');

    // echo in_groups(["Publisher"]) ? "TRUE" : "FALSE";
    // echo has_permission("Everything") ? "TRUE" : "FALSE";

    if (!empty($page)) echo view('App\\'.$page); ?>
</body>
