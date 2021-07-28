<?php
use Illuminate\Database\Capsule\Manager as Capsule;
require_once __DIR__.'/../func.php';
    if (!isset($_REQUEST["id"]) ||  !isset($_REQUEST["remoteport"]) || !isset($_REQUEST["port"]) || !isset($_REQUEST['protocol']) || !isset($_REQUEST['proxyprotocolversion'])){
        exit(json_encode([ "result" => "error" , "error" => "参数不合法"]));
    }
    
    $_REQUEST['port'] = strtolower($_REQUEST['port']);

    if ($_REQUEST["remoteport"] <= 0 || $_REQUEST["remoteport"] > 65535){
        exit(json_encode([ "result" => "error" , "error" => "内网端口不合法"]));
    }

    $allow_protocol = SolusVMNAT_GetAllowProtocol($data->packageid,$node->id);

    if (!$allow_protocol[$_REQUEST['protocol']]){
        exit(json_encode([ "result" => "error" , "error" => "不支持此协议"]));
    }

    if ($_REQUEST['protocol'] == 'http' || $_REQUEST['protocol'] == 'https'){
        $is_domain = preg_match("/^(?!:\/\/)(?!.{256,})(([a-z0-9][a-z0-9_-]*?)|([a-z0-9][a-z0-9_-]*?\.)+?[a-z]{2,15}?)$/i", $_REQUEST['port']);
        if (!$is_domain || is_numeric($_REQUEST['port'])) {
            exit(json_encode([ "result" => "error" , "error" => "绑定域名不合法"]));
        }
        
        if(!SolusVMNAT_VeifyDomainICP($node->id,$_REQUEST['port'])){
            exit(json_encode([ "result" => "error" , "error" => "绑定域名未备案"]));
        }
    }else{
    if (!preg_match("/^-?\d+$/",$_REQUEST['port']) || $_REQUEST["port"] <= 0 || $_REQUEST["port"] > 65535){
        exit(json_encode([ "result" => "error" , "error" => "外网端口不合法"]));
    }
    if (!SolusVMNAT_VeifyPort($node->id,$_REQUEST["port"])){
        exit(json_encode([ "result" => "error" , "error" => "此外网端口已被保留"]));
    }
    }
    
    $rules = Capsule::table('mod_SolusVMNAT_Rules')->where('sid', $data->id)->where('status','!=','Deleted')->count();
    $max = $plan->rules;

    if ($max <= $rules){
        exit(json_encode([ "result" => "error" , "error" => "规则分配已超过允许数量: ".$max]));
    }

    if (!SolusVMNAT_CheckPort($node->id,$_REQUEST['protocol'],$_REQUEST['port'])){
            exit(json_encode([ "result" => "error" , "error" => "该外网端口(域名)已被占用, 请更换"]));
    }
    
    $data = Capsule::table("mod_SolusVMNAT_Node")->where('id',$node->id)->first();
    $msg = '<br>外网端口(绑定域名)：'.$_REQUEST['port'].'<br>内网端口：'.$_REQUEST['remoteport'].'<br>协议：'.SolusVMNAT_AllProtocol()[$_REQUEST['protocol']]."<br>代理协议：".SolusVMNAT_AllProxyProtocolVersion()[$_REQUEST['proxyprotocolversion']].'<br>备注: '.$_REQUEST['msg']."<br><br>温馨提示：部分协议请查询节点信息获取服务端口<br>使用前请确认规则状态！！！";
    
    
   $ruleid = Capsule::table("mod_SolusVMNAT_Rules")->insertGetId([
        "sid" => $params["serviceid"],
        "port" => $_REQUEST["port"],
        "remoteip" => Capsule::table('tblhosting')->where('id',$_REQUEST['id'])->first()->dedicatedip,
        "remoteport" => $_REQUEST["remoteport"],
        "protocol" => $_REQUEST["protocol"],
        "proxyprotocolversion" => $_REQUEST['proxyprotocolversion'],
        "msg" => $_REQUEST['msg'],
        "node" => $node->id,
        "status" => "Created",
    ]);
    $postdata = array('ruleid' => $ruleid , 'nodeid' => $node->id , 'serviceid' => $data->id);
     if(SolusVMNAT_APICall('Rules',$postdata)){
          Capsule::table("mod_SolusVMNAT_Rules")->where("id",$ruleid)->update(['status' => 'Active']);
    }
        
    exit(json_encode(["result" => "success",'msg' => $msg]));