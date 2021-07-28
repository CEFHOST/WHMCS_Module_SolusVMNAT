<?php

use Illuminate\Database\Capsule\Manager as Capsule;

require_once __DIR__ . '/../func.php';
if (!isset($_REQUEST["id"])) {
    exit(json_encode(["result" => "error", "error" => "参数不合法"]));
}
$data = Capsule::table('mod_SolusVMNAT_Node')->where('id', $node->id)->first();
$protocols = json_decode($data->protocol, true);

$title = '节点(' . $data->name . ')的信息';
$html = '<br>同步周期：' . $data->update_cycle . '秒<br>' . '上次同步：' . $data->updated . '<br><br>';
foreach ($protocols as $protocol => $status) {
    if ($status) {
        $html .= SolusVMNAT_AllProtocol()[$protocol] . '协议：<a style="color: #0C0">开放</a><br>';
    } else {
        $html .= SolusVMNAT_AllProtocol()[$protocol] . '协议：<a style="color: #F00">关闭</a><br>';
    }
}

if ($protocols['http']) {
    $html .= 'HTTP端口：' . $data->http_port;
    if ($data->http_port != $data->http_port_2) {
        $html .= ' ' . $data->http_port_2;
    }
    $html .= '<br>';
}

if ($protocols['https']) {
    $html .= 'HTTPS端口：' . $data->https_port;
    if ($data->https_port != $data->https_port_2) {
        $html .= ' ' . $data->https_port_2;
    }
    $html .= '<br>';
}

if (($protocols['http'] || $protocols['https']) && (bool)$data->icp) {
    $html .= '<strong>此节点使用HTTP(S)协议域名需ICP备案</strong><br>';
}


$html .= '<br>';

$retain_ports = explode(PHP_EOL, $data->retain_port);
if ($protocols['tcp'] || $protocols['udp']) {
    $html .= '保留端口列表:';

    if (!is_array($retain_ports)) {
        $retain_ports[0] = $data->retain_port;
    }

    array_push($retain_ports, $data->apiport);
    array_push($retain_ports, $data->http_port);
    array_push($retain_ports, $data->https_port);
    if ($data->http_port != $data->http_port_2) {
        array_push($retain_ports, $data->http_port_2);
    }
    if ($data->https_port != $data->https_port_2) {
        array_push($retain_ports, $data->https_port_2);
    }

    foreach ($retain_ports as $retain_port) {
        $html .= '<br>' . $retain_port;
    }
}

exit(json_encode(['result' => 'success', 'title' => $title, 'html' => $html]));
