<?php
use Illuminate\Database\Capsule\Manager as Capsule;
require_once(__DIR__.'/../func.php');

    if (!isset($_REQUEST["id"]) || !isset($_REQUEST["ruleid"])){
        exit(json_encode([ "result" => "error" , "error" => "参数不合法"]));
    }
    
    $sql = Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$data->id)->where('id',$_REQUEST['ruleid'])->where('status','!=','Deleted');
    if(!$sql->exists()){
        exit(json_encode([ "result" => "error" , "error" => '规则不存在!']));
    }
    
    if($sql->first()->status == 'Created'){
        $sql->delete();
    }else{
    $sql->update(['status' => 'Deleted']);
    $sql = Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$data->id)->where('id',$_REQUEST['ruleid']);
    $postdata = array('ruleid' => $_REQUEST['ruleid'] , 'nodeid' => $node->id, 'serviceid' => $data->id);
     if(SolusVMNAT_APICall('Rules',$postdata)){
          $sql->delete();
        }
    }
    exit(json_encode([ "result" => "success",'msg' => '映射规则已删除']));
    