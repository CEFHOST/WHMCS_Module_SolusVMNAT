<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$protocols = SolusVMNAT_AllProtocol();

foreach ($protocols as $name => $value) {
    $protocols[$name] = false;
    if ($_REQUEST['protocol'][$name] == 'on') {
        $protocols[$name] = true;
    }
}

if ($_REQUEST['rules'] < 1) {
    SolusVMNAT_PrintText(false, '无效的规则数量值');
    return;
}

if (isset($_REQUEST['id'])) {

    $sql = Capsule::table('mod_SolusVMNAT_Plans')->where('id', $_REQUEST['id']);
    if ($sql->exists()) {
        $sql->update([
            'name' => $_REQUEST['name'],
            'pid' => $_REQUEST['pid'],
            'protocol' => json_encode($protocols),
            'rules' => $_REQUEST['rules'],
        ]);
        SolusVMNAT_PrintText(true, '保存成功!');
    } else {
        SolusVMNAT_PrintText(false, '套餐不存在!');
    }
} else {
    if(Capsule::table('mod_SolusVMNAT_Plans')->where('pid',$_REQUEST['pid'])->exists()){
       SolusVMNAT_PrintText(false, "此产品的套餐已存在!");
       return;
    }
    Capsule::table('mod_SolusVMNAT_Plans')->insert([
        'name' => $_REQUEST['name'],
        'pid' => $_REQUEST['pid'],
        'protocol' => json_encode($protocols),
        'rules' => $_REQUEST['rules'],
    ]);
    SolusVMNAT_PrintText(true, '保存成功!');
}