<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(isset($_REQUEST['id'])){
    $sql = Capsule::table('mod_SolusVMNAT_Rules')->where('id',$_REQUEST['id']);
    if($sql->exists()){
        if($sql->first()->status == "Created"){
            $sql->delete();
        }else{
      $sql->update(['status' => 'Deleted']);
      if(SolusVMNAT_APICall('Rules',array('ruleid' => $_REQUEST['id'] , 'nodeid' => $sql->first()->node , 'serviceid' => $sql->first()->sid))){
          $sql->delete();
        }
        }
        SolusVMNAT_PrintText(true,'规则删除成功');
    }else{
        SolusVMNAT_PrintText(false,'规则不存在');
    }
}else{
    SolusVMNAT_PrintText(false,'请求参数缺失');
}