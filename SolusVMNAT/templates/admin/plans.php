<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(!isset($_REQUEST['pages']) || empty($_REQUEST['pages'])){
    $_REQUEST['pages'] = 1;
}
if(isset($_REQUEST['id'])){
    $plans = Capsule::table('mod_SolusVMNAT_Plans')->where('id',$_REQUEST['id'])->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Plans')->where('id',$_REQUEST['id'])->count() / 50 );
}else{
    $plans = Capsule::table('mod_SolusVMNAT_Plans')->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Plans')->count() / 50);
}
$allprotocols = SolusVMNAT_AllProtocol();
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
			        	<col width="4%">
			        	<col width="20%"> 
			        	<col width="10%">
			        </colgroup>
		            <thead>
			            <tr>
			                <th>套餐ID</th>
			                <th>名称</th>
			                <th>规则数量</th>
			                <th>转发协议</th>
			                <th>操作</th>
			            </tr>
		            </thead>
		            <tbody>
		            <?php if(count($plans)!=0){ ?>
		                <?php foreach ($plans as $value){ ?>
		                    <tr id="<?php echo $_REQUEST['page']; ?>_<?php echo $value->id; ?>">
		                        <td><?php echo $value->id; ?></td>
		                        <td><?php echo $value->name; ?></td>
		                        <td><?php echo $value->rules; ?> 条</td>
		                        <?php 
		                        unset($protocol);
		                        $protocols = json_decode($value->protocol,true);
		                        foreach($protocols as $name => $bool){
                                  if($bool){
                                  $protocol .= $allprotocols[$name].'、';
                                              }
                                  }
                                  $protocol = rtrim($protocol,'、');
		                        ?>
		                        <td><?php echo $protocol; ?></td>
		                        <td>
		                        <a class="btn btn-info" href="?module=SolusVMNAT&a=edit&page=<?php echo $_REQUEST['page']; ?>&id=<?php echo $value->id; ?>"><i class="fas fa-pencil-alt" aria-hidden="true"></i>编辑</a>
		                        <button type="button" class="btn btn-danger" OnClick="Action('delete',<?php echo $value->id; ?>);"><i class="fas fa-trash-alt" aria-hidden="true"></i>删除</button>
		                        </td>
		                    </tr>
		            <?php } }else{ ?>
		                <tr id="message">
		                    <td colspan="5" class="text-center">
		                        尚未添加任何套餐,点击<a href="?module=SolusVMNAT&a=add&page=<?php echo $_REQUEST['page']; ?>">这里</a>添加套餐
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
    </div>
  </div>