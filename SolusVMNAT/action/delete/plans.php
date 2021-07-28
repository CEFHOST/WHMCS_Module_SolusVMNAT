<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(isset($_REQUEST['id'])){
    if(Capsule::table('mod_SolusVMNAT_Plans')->where('id',$_REQUEST['id'])->exists()){
        Capsule::table('mod_SolusVMNAT_Plans')->where('id',$_REQUEST['id'])->delete();
        SolusVMNAT_PrintText(true,'套餐删除成功');
    }else{
        SolusVMNAT_PrintText(false,'套餐不存在');
    }
}else{
    SolusVMNAT_PrintText(false,'请求参数缺失');
}