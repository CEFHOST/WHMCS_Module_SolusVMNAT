<?php
use Illuminate\Database\Capsule\Manager as Capsule;

if(isset($_REQUEST['key'])){
    Capsule::table('mod_SolusVMNAT_Setting')->where('name','key')->update(['value' => $_REQUEST['key']]);
}

SolusVMNAT_PrintText(true,'保存成功！');