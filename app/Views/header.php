<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="Passor" content="">
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

    <script src="/js/common.js"></script>
</head>

<!-- This can only be seen in Development enviroment -->
<? if (ENVIRONMENT === "Development") { ?>
    <div class="small text-center">
        <b>_DIR_</b> - <?= __DIR__ ?> | <b>SYSTEMPATH</b> - <?= SYSTEMPATH ?> | <b>ENVIRONMENT</b> - <?= ENVIRONMENT ?> | <b>APPPATH</b> - <?= APPPATH ?> | <b>BASE_URL</b> - <?= base_url() ?> | <b>DB</b> - <?= $_ENV["database.default.database"] ?> | <b>DEBUG</b> - <?= CI_DEBUG ?>
    </div>
<? } ?>

<?= view('alert'); ?>

<div class="container pt-5">
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="card">
                <h2 class="card-header"><?=lang('Pass.register')?></h2>
                <div class="card-body">

                    <form action="<?= route_to('register') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="email"><?=lang('Pass.email')?></label>
                            <input type="email" class="form-control <?php if(session('errors.email')) : ?>is-invalid<?php endif ?>"
                                   name="email" aria-describedby="emailHelp" placeholder="<?=lang('Pass.email')?>" value="<?= old('email') ?>">
                            <small id="emailHelp" class="form-text text-muted"><?=lang('Pass.weNeverShare')?></small>
                        </div>

                        <div class="form-group">
                            <label for="username"><?=lang('Pass.username')?></label>
                            <input type="text" class="form-control <?php if(session('errors.username')) : ?>is-invalid<?php endif ?>" name="username" placeholder="<?=lang('Pass.username')?>" value="<?= old('username') ?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?=lang('Pass.password')?></label>
                            <input type="password" name="password" class="form-control <?php if(session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?=lang('Pass.password')?>" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="pass_confirm"><?=lang('Pass.repeatPassword')?></label>
                            <input type="password" name="pass_confirm" class="form-control <?php if(session('errors.pass_confirm')) : ?>is-invalid<?php endif ?>" placeholder="<?=lang('Pass.repeatPassword')?>" autocomplete="off">
                        </div>

                        <br>

                        <button type="submit" class="btn btn-primary btn-block"><?=lang('Pass.register')?></button>
                    </form>

                    <hr>

                    <p><?=lang('Pass.alreadyRegistered')?> <a href="<?= route_to('login') ?>"><?=lang('Pass.signIn')?></a></p>
                </div>
            </div>
        </div>
    </div>
</div>
