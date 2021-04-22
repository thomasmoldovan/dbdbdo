<?= view("header", []); ?>

<?= $this->include("front", ['auth' => $auth, 'user' => $user]); ?>
<div class="">
    <div class="sidebarDiv"><?= view("sidebar", []); ?></div>
    <div class="rightDiv"><?= view("right", []); ?></div>
</div>
