<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$protocol_list = SolusVMNAT_AllProtocol();
foreach ($protocol_list as $protocol => $value) {
    $protocols[$protocol] = false;
}

if (isset($_REQUEST['msg'])) {
    $msg = $_REQUEST['msg'];
} else {
    $msg = '';
}

foreach ($protocols as $name => $value) {
    $protocols[$name] = false;
    if ($_REQUEST['protocol'][$name] == 'on') {
        $protocols[$name] = true;
    }
}

if ($_REQUEST['dropcn'] == 'on') {
    $dropcn = true;
} else {
    $dropcn = false;
}

if ($_REQUEST['api'] == 'on') {
    $api = true;
} else {
    $api = false;
}


if ($_REQUEST['icp'] == 'on') {
    $icp = true;
} else {
    $icp = false;
}

$is_domain = preg_match("/^(?!:\/\/)(?!.{256,})(([a-z0-9][a-z0-9_-]*?)|([a-z0-9][a-z0-9_-]*?\.)+?[a-z]{2,6}?)$/i", $_REQUEST['addr']);
if (!filter_var(trim($_REQUEST['addr']), FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
    if (!$is_domain) {
        SolusVMNAT_PrintText(false, "无效的节点地址!");
        return;
    }
}

if ($_REQUEST['update_cycle'] < 60) {
    SolusVMNAT_PrintText(false, "更新速度过快!");
    return;
}

if ($_REQUEST['times'] < 0) {
    SolusVMNAT_PrintText(false, "无效的倍率值!");
    return;
}


if ($_REQUEST['apiport'] < 1 || $_REQUEST['apiport'] > 65535) {
    SolusVMNAT_PrintText(false, "无效的API端口!");
    return;
}

if ($_REQUEST['http_port'] < 1 || $_REQUEST['http_port'] > 65535) {
    SolusVMNAT_PrintText(false, "无效的HTTP端口!");
    return;
}


if ($_REQUEST['https_port'] < 1 || $_REQUEST['https_port'] > 65535) {
    SolusVMNAT_PrintText(false, "无效的HTTPS端口!");
    return;
}

if ($_REQUEST['http_port_2'] < 1 || $_REQUEST['http_port_2'] > 65535) {
    SolusVMNAT_PrintText(false, "无效的HTTP端口!");
    return;
}


if ($_REQUEST['https_port_2'] < 1 || $_REQUEST['https_port_2'] > 65535) {
    SolusVMNAT_PrintText(false, "无效的HTTPS端口!");
    return;
}

$plans = Capsule::table("mod_SolusVMNAT_Plans")->get();

if (isset($_REQUEST['id'])) {

    $sql = Capsule::table('mod_SolusVMNAT_Node')->where('id', $_REQUEST['id']);
    if ($sql->exists()) {
        $sql->update([
            'name' => $_REQUEST['name'],
            'serverid' => $_REQUEST['serverid'],
            'svm_node' => $_REQUEST['svm_node'],
            'eth_device' => $_REQUEST['eth_device'],
            'addr' => $_REQUEST['addr'],
            'dropcn' => $dropcn,
            'other_open_ports' => $_REQUEST['other_open_ports'],
            'api' => $api,
            'apiport' => $_REQUEST['apiport'],
            'protocol' => json_encode($protocols),
            'retain_port' => $_REQUEST['retain_port'],
            'update_cycle' => $_REQUEST['update_cycle'],
            'http_port' => $_REQUEST['http_port'],
            'http_port_2' => $_REQUEST['http_port_2'],
            'https_port' => $_REQUEST['https_port'],
            'https_port_2' => $_REQUEST['https_port_2'],
            'icp' => $icp,
            'msg' => $msg,
        ]);
        SolusVMNAT_PrintText(true, '保存成功!');
    } else {
        SolusVMNAT_PrintText(false, "节点不存在!");
    }
} else {
    if (Capsule::table('mod_SolusVMNAT_Node')->where('serverid', $_REQUEST['serverid'])->where('svm_node', $_REQUEST['svm_node'])->exists()) {
        SolusVMNAT_PrintText(false, "此节点已存在!");
        return;
    }
    $nodeid = Capsule::table('mod_SolusVMNAT_Node')->insertGetId([
        'name' => $_REQUEST['name'],
        'serverid' => $_REQUEST['serverid'],
        'svm_node' => $_REQUEST['svm_node'],
        'eth_device' => $_REQUEST['eth_device'],
        'addr' => $_REQUEST['addr'],
        'dropcn' => $dropcn,
        'other_open_ports' => $_REQUEST['other_open_ports'] ?? "",
        'api' => $api,
        'apiport' => $_REQUEST['apiport'],
        'protocol' => json_encode($protocols),
        'retain_port' => $_REQUEST['retain_port'],
        'update_cycle' => $_REQUEST['update_cycle'],
        'http_port' => $_REQUEST['http_port'],
        'http_port_2' => $_REQUEST['http_port_2'],
        'https_port' => $_REQUEST['https_port'],
        'https_port_2' => $_REQUEST['https_port_2'],
        'icp' => $icp,
        'msg' => $msg,
    ]);
    SolusVMNAT_PrintText(true, '保存成功! 节点ID: ' . $nodeid);
}
