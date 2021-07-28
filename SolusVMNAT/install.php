<?php
require_once(__DIR__ . '/func.php');
$config = SolusVMNAT_GetConfigModule();

if ($_REQUEST['key'] != md5($config['key'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit('Wrong Key.');
}

if ($_REQUEST['do'] == 'json' && isset($_REQUEST['id'])) {
    $json = json_encode(array(
        "APIAddr" => SolusVMNAT_GetSystemURL() . 'modules/addons/SolusVMNAT/action.php',
        "APIToken" => $config['key'],
        "NodeID" => (int)$_REQUEST['id'],
    ));
    exit($json);
}

if ($_REQUEST['do'] == 'install') {
    exit(require_once(__DIR__ . '/static/file/install.php'));
}

if ($_REQUEST['do'] == 'update') {
    exit(require_once(__DIR__ . '/static/file/update.php'));
}
