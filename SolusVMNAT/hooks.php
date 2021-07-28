<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
require_once __DIR__.'/func.php';
use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('DailyCronJob', 1, function($vars) {
   $sql = Capsule::table('mod_SolusVMNAT_Plans')->get();
   foreach($sql as $value){
      if(!Capsule::table('tblproducts')->where('id',$value->pid)->exists()){
          Capsule::table('mod_SolusVMNAT_Plans')->where('id',$value->id)->delete();
      }
   }
   
   $sql = Capsule::table('mod_SolusVMNAT_Node')->get();
   foreach($sql as $value){
      if(!Capsule::table('tblservers')->where('id',$value->serverid)->exists()){
          Capsule::table('mod_SolusVMNAT_Node')->where('id',$value->id)->delete();
          Capsule::table('mod_SolusVMNAT_Rules')->where('node',$value->id)->delete();
      }
   }
   
   $sql = Capsule::table('mod_SolusVMNAT_Rules')->get();
   foreach($sql as $value){
      if(!Capsule::table('tblhosting')->where('id',$value->sid)->exists()){
          Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$value->sid)->update(['status' => 'Deleted']);
      }
   }
});

add_hook('PreModuleTerminate', 1, function($vars) {
    Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$vars['params']['serviceid'])->update(['status' => 'Deleted']);
});