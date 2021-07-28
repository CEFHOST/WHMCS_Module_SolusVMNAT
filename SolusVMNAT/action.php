<?php
set_time_limit(0);
require_once __DIR__ . "/../../../init.php";
require_once __DIR__ . "/func.php";
require_once __DIR__ . "/version.php";

use Illuminate\Database\Capsule\Manager as Capsule;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Unsupport Method');
}

$config = SolusVMNAT_GetConfigModule();
$postdata = json_decode(file_get_contents("php://input"), true);

if ($postdata['Token'] != md5($config['key'])) {
    header("HTTP/1.1 503 Service Unavailable");
    exit('Wrong Key.');
}

if ($postdata['Version'] != $version) {
    header("HTTP/1.1 503 Service Unavailable");
    exit('The API does not support this version of the client, please update your api or slave.');
}

if ($postdata['Action'] == 'UpdateInfo') {
    $sql = Capsule::table('mod_SolusVMNAT_Node')->where('id', $postdata['NodeID']);

    if (!$sql->exists()) {
        header("HTTP/1.1 503 Service Unavailable");
        exit('Wrong NodeID');
    }

    $data = $sql->first();

    $conf = array(
        'EnableAPI' => (bool)$data->api,
        'DROPCN' => (bool)$data->dropcn,
        'OtherOpenPort' => $data->other_open_ports,
        'APIPort' => $data->apiport,
        'UpdateInfoCycle' => $data->update_cycle,
        'Eth' => $data->eth_device,
    );

    $protocols = json_decode($data->protocol, true);
    $conf['Listen']['Http']['Enable'] = $protocols['http'];
    $conf['Listen']['Https']['Enable'] = $protocols['https'];
    $conf['Listen']['Http']['Port'] = $data->http_port;
    $conf['Listen']['Https']['Port'] = $data->https_port;

    if ($protocols['http'] && $data->http_port != $data->http_port_2) {
        $conf['Listen']['Http_2']['Enable'] = true;
        $conf['Listen']['Http_2']['Port'] = $data->http_port_2;
    }

    if ($protocols['https'] && $data->https_port != $data->https_port_2) {
        $conf['Listen']['Https_2']['Enable'] = true;
        $conf['Listen']['Https_2']['Port'] = $data->https_port_2;
    }

    $sql->update(['updated' => date('Y-m-d H:i:s')]);

    $rules = Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->get();
    foreach ($rules as $rule) {
        $conf['Rules'][(string)$rule->id]['Status'] = $rule->status;
        $conf['Rules'][(string)$rule->id]['Protocol'] = $rule->protocol;
        $conf['Rules'][(string)$rule->id]['ProxyProtocolVersion'] = $rule->proxyprotocolversion;
        $conf['Rules'][(string)$rule->id]['Listen'] = $rule->port;
        $conf['Rules'][(string)$rule->id]['Forward'] = $rule->remoteip . ':' . $rule->remoteport;
    }

    Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->where('status', "Created")->update(['status' => 'Active']);
    Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->where('status', "Deleted")->delete();
    exit(json_encode($conf));
}

if ($postdata['Action'] == 'GetConfig') {
    $sql = Capsule::table('mod_SolusVMNAT_Node')->where('id', $postdata['NodeID']);

    if (!$sql->exists()) {
        header("HTTP/1.1 503 Service Unavailable");
        exit('Wrong NodeID');
    }

    $data = $sql->first();
    $sql->update(['updated' => date('Y-m-d H:i:s')]);

    $conf = array(
        'EnableAPI' => (bool)$data->api,
        'DROPCN' => (bool)$data->dropcn,
        'OtherOpenPort' => $data->other_open_ports,
        'APIPort' => $data->apiport,
        'UpdateInfoCycle' => $data->update_cycle,
        'Eth' => $data->eth_device,
    );
    $protocols = json_decode($data->protocol, true);
    $conf['Listen']['Http']['Enable'] = $protocols['http'];
    $conf['Listen']['Https']['Enable'] = $protocols['https'];
    $conf['Listen']['Http']['Port'] = $data->http_port;
    $conf['Listen']['Https']['Port'] = $data->https_port;

    if ($protocols['http'] && $data->http_port != $data->http_port_2) {
        $conf['Listen']['Http_2']['Enable'] = true;
        $conf['Listen']['Http_2']['Port'] = $data->http_port_2;
    }

    if ($protocols['https'] && $data->https_port != $data->https_port_2) {
        $conf['Listen']['Https_2']['Enable'] = true;
        $conf['Listen']['Https_2']['Port'] = $data->https_port_2;
    }

    Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->whereIn('status', ["Created", "Error"])->update(['status' => 'Active']);
    Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->where('status', "Deleted")->delete();

    $rules = Capsule::table("mod_SolusVMNAT_Rules")->where('node', $postdata['NodeID'])->get();
    foreach ($rules as $rule) {
        if (!$protocols[$rule->protocol]) {
            Capsule::table('mod_SolusVMNAT_Rules')->where('id', $rule->id)->update(['status' => 'Error']);
            continue;
        }

        $conf['Rules'][(string)$rule->id]['Status'] = $rule->status;
        $conf['Rules'][(string)$rule->id]['Protocol'] = $rule->protocol;
        $conf['Rules'][(string)$rule->id]['ProxyProtocolVersion'] = $rule->proxyprotocolversion;
        $conf['Rules'][(string)$rule->id]['Listen'] = $rule->port;
        $conf['Rules'][(string)$rule->id]['Forward'] = $rule->remoteip . ':' . $rule->remoteport;
    }
    exit(json_encode($conf));
}
