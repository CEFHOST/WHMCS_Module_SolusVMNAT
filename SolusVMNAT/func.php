<?php
set_time_limit(0);
require_once __DIR__ . "/../../../init.php";

use Illuminate\Database\Capsule\Manager as Capsule;

function SolusVMNAT_GetConfigModule()
{
    $AddonModuleConfig = Capsule::table('mod_SolusVMNAT_Setting')->get();
    $config            = [];

    for ($i = 0; $i < count($AddonModuleConfig); $i++) //var_dump($vars);
    {
        $config[$AddonModuleConfig[$i]->name] = $AddonModuleConfig[$i]->value;
    }

    return $config;
}

function SolusVMNAT_GetSystemURL()
{
    return rtrim(Capsule::table('tblconfiguration')->where('setting', '=', 'SystemURL')->first()->value, '/') . '/';
}

function SolusVMNAT_AllProtocol()
{
    return [
        'tcp' => 'TCP',
        'udp' => 'UDP',
        'http' => 'HTTP',
        'https' => 'HTTPS',
    ];
}

function SolusVMNAT_AllProxyProtocolVersion()
{
    return array(
        0 => "关闭",
        1 => "v1",
        2 => "v2",
    );
}

function SolusVMNAT_PrintText(bool $is_success, $text)
{
    if ($is_success) {
        echo '<div class="col-sm-6 col-sm-offset-3"><div class="alert alert-success">' . $text . '</div></div>';
    } else {
        echo '<div class="col-sm-6 col-sm-offset-3"><div class="alert alert-danger">' . $text . '</div></div>';
    }
}

function SolusVMNAT_inittable()
{
    SolusVMNAT_droptable();
    try {
        Capsule::schema()->create("mod_SolusVMNAT_Setting", function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value')->default("");
        });
    } catch (\Exception $e) {
        return [
            'status'        =>  'error',
            'description'   =>  "无法创建表 'mod_SolusVMNAT_Setting' : {$e->getMessage()}"
        ];
    }

    try {
        Capsule::schema()->create("mod_SolusVMNAT_Node", function ($table) {
            $table->increments('id');
            $table->integer('serverid')->default(0);
            $table->string('svm_node')->default('localhost');
            $table->string('eth_device')->default('eth0');
            $table->string('name')->default("");
            $table->string('addr')->default("");
            $table->boolean('dropcn')->default(false);
            $table->string('other_open_ports')->default("");
            $table->boolean('api')->default(false);
            $table->string('apiport')->default("0");
            $table->string('retain_port')->default("");
            $table->string('protocol')->default("{}");
            $table->integer('update_cycle')->default(300);
            $table->string('http_port')->default("80");
            $table->string('http_port_2')->default("8080");
            $table->string('https_port')->default("443");
            $table->string('https_port_2')->default("8443");
            $table->boolean('icp')->default(false);
            $table->datetime('updated')->default("0000-00-00 00:00:00");
            $table->string('msg')->default("");
        });
    } catch (\Exception $e) {
        return [
            'status'        =>  'error',
            'description'   =>  "无法创建表 'mod_SolusVMNAT_Node' : {$e->getMessage()}"
        ];
    }

    try {
        Capsule::schema()->create("mod_SolusVMNAT_Rules", function ($table) {
            $table->increments('id');
            $table->biginteger('sid')->default(0);
            $table->string('protocol')->default("");
            $table->integer('proxyprotocolversion')->default(0);
            $table->string('remoteip')->default("");
            $table->string('remoteport')->default("");
            $table->string('port')->default("");
            $table->integer('node')->default(0);
            $table->string('msg')->default("");
            $table->string('status')->default("Created");
        });
    } catch (\Exception $e) {
        return [
            'status'        =>  'error',
            'description'   =>  "无法创建表 'mod_SolusVMNAT_Rules' : {$e->getMessage()}"
        ];
    }

    try {
        Capsule::schema()->create("mod_SolusVMNAT_Plans", function ($table) {
            $table->increments('id');
            $table->integer('pid')->default(0);
            $table->string('name')->default("");
            $table->string('protocol')->default("{}");
            $table->integer('rules')->default(0);
        });
    } catch (\Exception $e) {
        return [
            'status'        =>  'error',
            'description'   =>  "无法创建表 'mod_SolusVMNAT_Plans' : {$e->getMessage()}"
        ];
    }

    try {
        Capsule::table('mod_SolusVMNAT_Setting')->insert(['name' => 'key', 'value' => '']);
    } catch (\Exception $e) {
        return [
            'status'        =>  'error',
            'description'   =>  "{$e->getMessage()}"
        ];
    }
}

function SolusVMNAT_droptable()
{
    Capsule::schema()->dropIfExists('mod_SolusVMNAT_Setting');
    Capsule::schema()->dropIfExists('mod_SolusVMNAT_Node');
    Capsule::schema()->dropIfExists('mod_SolusVMNAT_Rules');
    Capsule::schema()->dropIfExists('mod_SolusVMNAT_Plans');
}


function SolusVMNAT_GetAllowProtocol(int $planid, int $nodeid)
{
    $protocols = json_decode(Capsule::table('mod_SolusVMNAT_Node')->where('id', $nodeid)->first()->protocol, true);
    $plans = json_decode(Capsule::table('mod_SolusVMNAT_Plans')->where('id', $planid)->first()->protocol, true);
    foreach ($plans as $protocol => $value) {
        if (!$value) {
            $protocols[$protocol] = false;
        }
    }
    return $protocols;
}


function SolusVMNAT_CheckPort(int $nodeid, string $protocol, string $port)
{
    $port = strtolower($port);
    if ($protocol == 'http' || $protocol == 'https') {
        $addr = Capsule::table('mod_SolusVMNAT_Node')->where('id', $nodeid)->first()->addr;
        if ($port == strtolower($addr)) {
            return false;
        }
    }

    return !Capsule::table('mod_SolusVMNAT_Rules')->where('node', $nodeid)->where('protocol', $protocol)->where('port', $port)->exists();
}

function SolusVMNAT_VeifyPort(int $nodeid, int $port)
{
    $nodeinfo = Capsule::table('mod_SolusVMNAT_Node')->where('id', $nodeid)->first();
    $retain_ports = explode(PHP_EOL, $nodeinfo->retain_port);

    if (!is_array($retain_ports)) {
        $retain_ports[0] = $nodeinfo->retain_port;
    }

    array_push($retain_ports, $nodeinfo->apiport);
    array_push($retain_ports, $nodeinfo->http_port);
    array_push($retain_ports, $nodeinfo->https_port);
    if ($nodeinfo->http_port != $nodeinfo->http_port_2) {
        array_push($retain_ports, $nodeinfo->http_port_2);
    }
    if ($nodeinfo->https_port != $nodeinfo->https_port_2) {
        array_push($retain_ports, $nodeinfo->https_port_2);
    }
    foreach ($retain_ports as $retain_port) {
        if (strpos($retain_port, '-') != false) {
            $firstport = explode('-', $retain_port)[0];
            $lastport = explode('-', $retain_port)[1];
            if ($port >= $firstport && $port <= $lastport) {
                return false;
            }
        } else {
            if ($port == $retain_port) {
                return false;
            }
        }
    }
    return true;
}

function SolusVMNAT_StatusArray()
{
    return [
        "Created" => '<a style="color:blue;">创建中...</a>',
        "Active" => '<a style="color:green;">正常</a>',
        "Deleted" => '<a style="color:red;">删除中...</a>',
        "Error" => '<a style="color:red;">不可用</a>',
    ];
}

function SolusVMNAT_httprequest($url, $data = null, $header = null)
{
    $curl = curl_init();
    if (!empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 0); //返回response头部信息
    }
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

function SolusVMNAT_APICall(string $type, array $params)
{
    if ($type == 'Rules') {
        $sql = Capsule::table('mod_SolusVMNAT_Node')->where('id', $params['nodeid'])->first();
        if (!$sql->api) {
            return false;
        }

        $rule = Capsule::table('mod_SolusVMNAT_Rules')->where('id', $params['ruleid'])->first();

        $postdata['Rules'][(string)$params['ruleid']]['Status'] = $rule->status;
        $postdata['Rules'][(string)$params['ruleid']]['Protocol'] = $rule->protocol;
        $postdata['Rules'][(string)$params['ruleid']]['ProxyProtocolVersion'] = $rule->proxyprotocolversion;
        $postdata['Rules'][(string)$params['ruleid']]['Listen'] = $rule->port;
        $postdata['Rules'][(string)$params['ruleid']]['Forward'] = $rule->remoteip . ':' . $rule->remoteport;

        $path =  md5(Capsule::table('mod_SolusVMNAT_Setting')->where('name', 'key')->first()->value);

        $url = 'http://' . $sql->addr . ':' . $sql->apiport . '/' . $path;
        return json_decode(SolusVMNAT_httprequest($url, json_encode($postdata)), true)['Result'];
    }
}

function SolusVMNAT_VeifyDomainICP(int $nodeid, string $dm)
{
    $data = Capsule::table("mod_SolusVMNAT_Node")->where('id', $nodeid)->first();
    if (!(bool)$data->icp) {
        return true;
    }
    $json = json_decode(SolusVMNAT_httprequest("http://api.btstu.cn/icp/api.php?domain=" . $dm), true);
    if ($json['code'] == "200") {
        return true;
    } else {
        return false;
    }
    return false;
}
