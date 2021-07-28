<?php
use Illuminate\Database\Capsule\Manager as Capsule;

function SolusVMNAT_backup_database(){
$database = array('Node','Users','Rules','Plans','Services','Info','Setting');
foreach($database as $db){

$sql = Capsule::table('mod_SolusVMNAT_'.$db)->get();
foreach($sql as $index => $data){
    foreach($data as $name => $value){
        $json[$db][$index][$name] = $value;
    }
}

}
$file = json_encode($json);
//告诉浏览器这是一个文件流格式的文件    
    Header ( "Content-type: application/octet-stream" ); 
    //请求范围的度量单位  
    Header ( "Accept-Ranges: bytes" );  
    //Content-Length是指定包含于请求或响应中数据的字节长度    
    Header ( "Accept-Length: " . strlen($file) );  
    //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
    Header ( "Content-Disposition: attachment; filename=database.json");
    exit($file);

}

function SolusVMNAT_import_database(){
    $json = json_decode(file_get_contents('php://input'),true);
    if(!is_array($json)){
        exit(json_encode(['result' => 'error','error' => '错误的文件类型']));
    }
    foreach($json as $db => $sql){
       foreach($sql as $data){
           if(Capsule::table('mod_SolusVMNAT_'.$db)->where('id',$data['id'])->exists()){
           Capsule::table('mod_SolusVMNAT_'.$db)->where('id',$data['id'])->update($data);
           }else{
           Capsule::table('mod_SolusVMNAT_'.$db)->insert($data);    
           }
       }
    }
    exit(json_encode(['result' => 'success']));
}

function SolusVMNAT_rebuild_database(){
    
$database = array('Node','Users','Rules','Plans','Setting');
foreach($database as $db){

$sql = Capsule::table('mod_SolusVMNAT_'.$db)->get();
foreach($sql as $index => $data){
    foreach($data as $name => $value){
        $json[$db][$index][$name] = $value;
    }
}
}

if(!is_dir(__DIR__.'/../../backup/')){
    mkdir(__DIR__.'/../../backup/');
}
file_put_contents(__DIR__.'/../../backup/'.date("Y-m-d-H_i_s").'.json',json_encode($json));

SolusVMNAT_inittable();

    foreach($json as $db => $sql){
       foreach($sql as $data){
           if(Capsule::table('mod_SolusVMNAT_'.$db)->where('id',$data['id'])->exists()){
           Capsule::table('mod_SolusVMNAT_'.$db)->where('id',$data['id'])->update($data);
           }else{
           Capsule::table('mod_SolusVMNAT_'.$db)->insert($data);    
           }
       }
       SolusVMNAT_PrintText(true,"操作成功!");
}
}


if (isset($_REQUEST['do']) && function_exists("SolusVMNAT_".$_REQUEST['do']."_database")){
    $funcname = "SolusVMNAT_".$_REQUEST['do'].'_database';
    $funcname();
}else{
?>
<div class="text-center">
    <button type="button" class="btn btn-default" onClick="backup_load();"><i class="glyphicon glyphicon-upload"></i>导入数据库</button>
    <button type="button" class="btn btn-info" onClick="window.open('addonmodules.php?module=SolusVMNAT&page=database&do=backup','_blank');"><i class="glyphicon glyphicon-download"></i>导出数据库</button>
    <button type="button" class="btn btn-danger" onClick="window.open('addonmodules.php?module=SolusVMNAT&page=database&do=rebuild','_blank');"><i class="fas fa-sync"></i>重建数据库</button>
</div>
<script type="text/javascript">
var backup_load = function(obj){
    var html = '';
            
            $.confirm({
                title: '请选择要导入的文件',
                content: '' +
					'<form action="" class="formName" >' +
					'<div class="form-group">' +
					'<div class="input-group">' +
					'<input id="file" type="file" class="form-control" name="file"   enctype="multipart/form-data" accept="application/json">' +
					'</div></div></form>',
                buttons: {
                    formSubmit: {
                        text: '提交',
                        btnClass: 'btn-blue',
                        action: function () {
                           var formdata = this.$content.find('[name=file]')[0].files[0];
                           if(!formdata){
                               return false;
                           }
                            $.confirm({
                                content: function () {
                                    var self = this;
                                    return $.ajax({
                                        url: 'addonmodules.php?module=SolusVMNAT&page=database&do=import',
                                        type: 'POST',
                                    data: formdata,
                                        dataType: 'json',
                                        async: false,
                                        cache: false,
                                        processData: false,
                                    }).done(function (response) {
                                        if (response.result == "success") {
                                            self.setType('green')
                                            self.setTitle('成功');
                                            self.setContent('数据库导入成功！');
                                            
                                        } else {
                                            self.setType('red')
                                            self.setTitle('错误');
                                            self.setContent(response.error);
                                        }

                                    }).fail(function () {
                                        self.setType('red')
                                        self.setTitle('错误');
                                        self.setContent('与服务器通讯时出现错误, 请重试.');
                                    });
                                },
                            buttons: {
                                ok: {
                                text: "完成",
                                }
                            }
                            });
                        }
                    },
                    cancel: {
                        text: '取消',
                    } 
                },
                
                onContentReady: function () {
                    var jc = this;
                    this.$content.find('form').on('submit', function (e) {
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click');
                    });
                }
            });
}
</script>
<?php } ?>