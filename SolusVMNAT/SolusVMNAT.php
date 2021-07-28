<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/../../servers/solusvmplus/lib/Curl.php';
require_once __DIR__ . '/../../servers/solusvmplus/lib/CaseInsensitiveArray.php';
require_once __DIR__ . '/../../servers/solusvmplus/lib/SolusVM.php';
require_once __DIR__ . '/func.php';
require_once __DIR__ . '/version.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use SolusVMPlus\SolusVM as SolusVM;

function SolusVMNAT_config()
{
    global $version;
    return [
        "name" => "SolusVMNAT",
        "description" => "SolusVMNAT - 适用于SolusvmPlus的自选端口插件",
        "version" => $version,
        "author" => "ZeroTime Team",
    ];
}

function SolusVMNAT_activate()
{
    SolusVMNAT_inittable();
}

function SolusVMNAT_deactivate()
{
    SolusVMNAT_droptable();
}


function SolusVMNAT_output($vars)
{
    global $version;

    if (isset($_REQUEST['do']) && file_exists(__DIR__ . '/templates/admin/' . $_REQUEST['page'] . '.php')) {
        include(__DIR__ . '/templates/admin/' . $_REQUEST['page'] . '.php');
        exit();
    }
    if (isset($_REQUEST['page']) && file_exists(__DIR__ . '/templates/admin/' . $_REQUEST['page'] . '.php')) {
        $active[$_REQUEST['page']] = 'active';
    } else {
        $active['view'] = 'active';
    }



    include_once(__DIR__ . '/templates/admin/header.php');
    if (isset($_REQUEST['page']) && file_exists(__DIR__ . '/templates/admin/' . $_REQUEST['page'] . '.php')) {
        if (isset($_REQUEST['a']) && file_exists(__DIR__ . '/action/' . $_REQUEST['a'] . '/' . $_REQUEST['page'] . '.php')) {
            include_once(__DIR__ . '/action/' . $_REQUEST['a'] . '/' . $_REQUEST['page'] . '.php');
            if (in_array($_REQUEST['a'], ['delete', 'save', 'clean'])) {
                echo '<meta http-equiv="refresh" content="5;url=?module=SolusVMNAT&page=' . $_REQUEST['page'] . '">';
            }
        } else {
            include_once(__DIR__ . '/templates/admin/' . $_REQUEST['page'] . '.php');
        }
    } else {
        include_once(__DIR__ . '/templates/admin/view.php');
    }
    include_once(__DIR__ . '/templates/admin/footer.php');
}


function SolusVMNAT_clientarea($vars)
{
    if (!isset($_SESSION["uid"])) {
        return array(
            'pagetitle' => "SolusVM VPS NAT端口映射",
            'requirelogin' => true,
        );
    }

    if (!isset($_REQUEST['id'])) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '请从VPS管理页面进入',
            ),
        );
    }

    $sql = Capsule::table('tblhosting')->where('id', $_REQUEST['id'])->where('userid', $_SESSION['uid']);
    if (!$sql->exists()) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '服务不存在！',
            )
        );
    } else {
        $data = $sql->first();
    }

    if ($data->domainstatus != 'Active') {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '您的服务未激活',
            ),
        );
    }

    $sql = Capsule::table('mod_SolusVMNAT_Plans')->where('pid', $data->packageid);
    if (!$sql->exists()) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '您的服务暂不支持端口映射',
            ),
        );
    } else {
        $plan = $sql->first();
    }

    $sql = Capsule::table("tblcustomfields")->where('type', 'product')->where('relid', $data->packageid)->where('fieldname', 'vserverid');
    if (!$sql->exists()) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => 'vserverid 参数缺失',
            ),
        );
    } else {
        $vserverid = Capsule::table("tblcustomfieldsvalues")->where('fieldid', $sql->first()->id)->where('relid', $data->id)->first()->value;
    }

    $params = SolusVM::getParamsFromServiceID($data->id, $data->userid);
    if ($params == false) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '参数缺失',
            ),
        );
    }
    if ($params['vserver'] != $vserverid || $params['serverid'] != $data->server) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '参数错误',
            ),
        );
    }

    $solusvm = new SolusVM($params);
    $callArray = array("vserverid" => $vserverid, 'nostatus' => true, 'nographs' => true);
    $solusvm->apiCall('vserver-infoall', $callArray);
    $r = $solusvm->result;

    $sql = Capsule::table('mod_SolusVMNAT_Node')->where('serverid', $data->server)->where('svm_node', $r['node']);
    if (!$sql->exists()) {
        return array(
            'pagetitle' => 'SolusVM VPS NAT端口映射',
            'templatefile' => 'templates/clientarea/error.tpl',
            'requirelogin' => true,
            'vars' => array(
                'text' => '您的服务暂不支持端口映射',
            ),
        );
    } else {
        $node = $sql->first();
    }

    if (isset($_REQUEST['a']) && file_exists(__DIR__ . '/client_action/' . $_REQUEST['a'] . '.php')) {
        require_once(__DIR__ . '/client_action/' . $_REQUEST['a'] . '.php');
        exit();
    }

    $rules = Capsule::table('mod_SolusVMNAT_Rules')->where('sid', $data->id)->where('status', '!=', 'Deleted')->get();
    $info = array(
        'now_rule' => Capsule::table('mod_SolusVMNAT_Rules')->where('sid', $params['serviceid'])->where('status', '!=', 'Deleted')->count(),
    );

    $all_protocols = SolusVMNAT_AllProtocol();
    $support_protocols = $all_protocols;
    $avaiilable_protocols_plan = json_decode($plan->protocol, true);
    $avaiilable_protocols_node = json_decode($node->protocol, true);
    foreach ($all_protocols as $name => $value) {
        if (!$avaiilable_protocols_plan[$name]) {
            unset($support_protocols[$name]);
            continue;
        }
        if (!$avaiilable_protocols_node[$name]) {
            unset($support_protocols[$name]);
            continue;
        }
    }

    return array(
        'pagetitle' => 'SolusVM VPS NAT端口映射',
        'templatefile' => 'templates/clientarea/clientarea.tpl',
        'requirelogin' => true,
        'vars' => array(
            'serviceid' => $data->id,
            'info' => $info,
            'rules' => $rules,
            'plan' => $plan,
            'node' => $node,
            'all_protocols' => $all_protocols,
            'protocols' => $support_protocols,
            'other_open_ports' => explode(",", $node->other_open_ports),
            'proxyprotocolversions' => SolusVMNAT_AllProxyProtocolVersion(),
            'status' => SolusVMNAT_StatusArray(),
        ),
    );
}
