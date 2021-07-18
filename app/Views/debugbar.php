<!-- This can only be seen in Development enviroment -->
<? if (ENVIRONMENT === "Development") { ?>
    <div class="bg-white small text-center d-block text-center">
        <b>_DIR_</b> - <?= __DIR__ ?> | 
        <b>SYSTEMPATH</b> - <?= SYSTEMPATH ?> | 
        <b>ENVIRONMENT</b> - <?= ENVIRONMENT ?> | 
        <b>APPPATH</b> - <?= APPPATH ?> | 
        <b>BASE_URL</b> - <?= base_url() ?> | 
        <b>DB</b> - <?= $_ENV["database.default.database"] ?> | 
        <b>THRESHOLD</b> - <?= $_ENV["logger.threshold"] ?> |
        <b>DEBUG</b> - <?= CI_DEBUG ?> |
        <b>HASH</b> - <?= $_SESSION["project_hash"] ?? "???"; ?>
    </div>
<? } ?>
