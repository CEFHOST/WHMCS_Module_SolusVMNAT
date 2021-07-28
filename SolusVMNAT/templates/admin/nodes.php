<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(!isset($_REQUEST['pages']) || empty($_REQUEST['pages'])){
    $_REQUEST['pages'] = 1;
}
if(isset($_REQUEST['id'])){
    $nodes = Capsule::table('mod_SolusVMNAT_Node')->where('id',$_REQUEST['id'])->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Node')->where('id',$_REQUEST['id'])->count() / 50);
}else{
    $nodes = Capsule::table('mod_SolusVMNAT_Node')->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Node')->count() / 50);
}
?>
<div class="row" >
    <div class="col-md-12">
	<a class="btn btn-info" href="?module=SolusVMNAT&a=add&page=<?php echo $_REQUEST['page']; ?>"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i>新增</a>
    </div>
    <div class="col-md-12">
	    <div class="block block-rounded block-bordered">
		    <div class="block-content" style="padding:0">
		        <table id="<?php echo $_REQUEST['page']; ?>" class="table table-hover js-dataTable-full" style="margin-bottom:0">
			        <colgroup>
			        	<col width="3%">
			        	<col width="8%">
			        	<col width="13%">
			        	<col width="3%">
			        	<col width="3%">
			        	<col width="8%">
			        	<col width="10%">
			        	<col width="15%">
			        </colgroup>
		            <thead>
			            <tr>
			                <th>节点ID</th>
			                <th>名称</th>
			                <th>地址</th>
			                <th>API通知</th>
			                <th>API端口</th>
			                <th>最后更新</th>
			                <th>备注</th>
			                <th>操作</th>
			            </tr>
		            </thead>
		            <tbody>
		            <?php if(count($nodes)!=0){ ?>
		                <?php foreach ($nodes as $value){ ?>
		                    <tr id="<?php echo $_REQUEST['page']; ?>_<?php echo $value->id; ?>">
		                        <td><?php echo $value->id; ?></td>
		                        <td><?php echo $value->name; ?></td>
		                        <td><?php echo $value->addr; ?></td>
		                        <td><?php if($value->api){echo '开';}else{echo '关';} ?></td>
		                        <td><?php echo $value->apiport; ?></td>
		                        <td><?php echo $value->updated; ?></td>
		                        <td><?php echo $value->msg; ?></td>
		                        <td>
		                        <a class="btn btn-info" href="?module=SolusVMNAT&a=edit&page=<?php echo $_REQUEST['page']; ?>&id=<?php echo $value->id; ?>"><i class="fas fa-pencil-alt" aria-hidden="true"></i>编辑</a>
		                        <button type="button" class="btn btn-warning" OnClick="Action('clean',<?php echo $value->id; ?>);"><i class="fas fa-broom" aria-hidden="true"></i>清理</button>
		                        <button type="button" class="btn btn-danger" OnClick="Action('delete',<?php echo $value->id; ?>);"><i class="fas fa-trash-alt" aria-hidden="true"></i>删除</button>
		                        </td>
		                    </tr>
		            <?php } }else{ ?>
		                <tr id="message">
		                    <td colspan="8" class="text-center">
		                        尚未添加任何端口转发节点,点击<a href="?module=SolusVMNAT&a=add&page=<?php echo $_REQUEST['page']; ?>">这里</a>创建新节点
		                    </td>
		                </tr>
		            <?php } ?>
		            </tbody>
		        </table>
		    </div>
		    <div class="text-center">
		    <ul class="pagination">
		        <?php if($_REQUEST['pages'] == 1){ ?>
		        <li class="previous disabled"><span class="page-selector">« 上一页</span></li>
		        <?php }else{ ?>
		        <li class="previous"><span onClick="window.location.href='addonmodules.php?module=SolusVMNAT&page=<?php echo 
		       $_REQUEST['page']; ?>&pages=<?php echo $_REQUEST['pages'] - 1; ?><?php if(isset($_REQUEST['id'])){echo '&id='.$_REQUEST['id'];}?>'" class="page-selector">« 上一页</span></li>
		        <?php } ?>
		        <?php for($i=1;$i<=$count;$i++){ 
		        if($i == $_REQUEST['pages']){
		        ?>
		        <li class="hidden-xs"><span class="page-selector active"><strong><?php echo $i; ?></strong></span></li>
		        <?php }else{ ?>
		        <li class="hidden-xs"><a onClick="window.location.href='addonmodules.php?module=SolusVMNAT&page=<?php echo 
		       $_REQUEST['page']; ?>&pages=<?php echo $i; ?><?php if(isset($_REQUEST['id'])){echo '&id='.$_REQUEST['id'];}?>'" class="page-selector"><?php echo $i; ?></a></li>
		        <?php }} ?>
		        <?php if($count == 0 || $_REQUEST['pages'] == $count){ ?>
		        <li class="next disabled"><a class="page-selector">下一页 »</a></li>
		        <?php }else{ ?>
		        <li class="next"><a class="page-selector" onClick="window.location.href='addonmodules.php?module=SolusVMNAT&page=<?php echo 
		       $_REQUEST['page']; ?>&pages=<?php echo $_REQUEST['pages'] + 1;?><?php if(isset($_REQUEST['id'])){echo '&id='.$_REQUEST['id'];}?>'">下一页 »</a></li>
		        <?php } ?>
		    </ul></div>
		</div>
		<div class="alert alert-info">
		    <h5>对接命令:</h5>
		    <small style="border-bottom: 1px dotted #000; ">bash <(curl -sSL "<?php echo SolusVMNAT_GetSystemURL(); ?>modules/addons/SolusVMNAT/install.php?do=install&key=<?php echo md5(SolusVMNAT_GetConfigModule()['key']); ?>")
		    </small>
		    <button type="button" id="install_command" class="btn btn-primary" style="float: right;" data-clipboard-text="bash <(curl -sSL '<?php echo SolusVMNAT_GetSystemURL(); ?>modules/addons/SolusVMNAT/install.php?do=install&key=<?php echo md5(SolusVMNAT_GetConfigModule()['key']); ?>')">复制</button>
		</div>
		
		<div class="alert alert-info">
		    <h5>升级命令(先升级前端，然后执行后端升级命令):</h5>
		    <small style="border-bottom: 1px dotted #000; ">bash <(curl -sSL "<?php echo SolusVMNAT_GetSystemURL(); ?>modules/addons/SolusVMNAT/install.php?do=update&key=<?php echo md5(SolusVMNAT_GetConfigModule()['key']); ?>")
		    </small>
		    <button type="button" id="update_command" class="btn btn-primary" style="float: right;" data-clipboard-text="bash <(curl -sSL '<?php echo SolusVMNAT_GetSystemURL(); ?>modules/addons/SolusVMNAT/install.php?do=update&key=<?php echo md5(SolusVMNAT_GetConfigModule()['key']); ?>')">复制</button>
		</div>
    </div>
  </div>