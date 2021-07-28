<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(isset($_REQUEST['id'])){
    if(Capsule::table('mod_SolusVMNAT_Node')->where('id',$_REQUEST['id'])->exists()){
      Capsule::table('mod_SolusVMNAT_Rules')->where('node',$_REQUEST['id'])->update(['status' => 'Deleted']);
      
        SolusVMNAT_PrintText(true,'节点规则清理成功');
    }else{
        SolusVMNAT_PrintText(false,'节点不存在');
    }
}else{
    SolusVMNAT_PrintText(false,'请求参数缺失');
}