<?php
use Illuminate\Database\Capsule\Manager as Capsule;
if(!isset($_REQUEST['pages']) || empty($_REQUEST['pages'])){
    $_REQUEST['pages'] = 1;
}
if(isset($_REQUEST['sid'])){
    $array = Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$_REQUEST['sid'])->where('status','!=','Deleted')->orderBy('id', 'desc')->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Rules')->where('sid',$_REQUEST['sid'])->where('status','!=','Deleted')->count() / 50 );
}else{
    $array = Capsule::table('mod_SolusVMNAT_Rules')->where('status','!=','Deleted')->orderBy('id', 'desc')->offset(($_REQUEST['pages'] - 1) * 50)->limit(50)->get();
    $count = ceil(Capsule::table('mod_SolusVMNAT_Rules')->where('status','!=','Deleted')->count() / 50 );
}
$protocols = SolusVMNAT_AllProtocol();
$proxyprotocolversion = SolusVMNAT_AllProxyProtocolVersion();

?>
<div class="row" >
    <div class="col-md-12">
	    <div class="block block-rounded block-bordered">
		    <div class="block-content" style="padding:0">
		        <table id="<?php echo $_REQUEST['page']; ?>" class="table table-hover js-dataTable-full" style="margin-bottom:0">
			        <colgroup>
			        	<col width="3%">
			        	<col width="3%">
			        	<col width="8%">
			        	<col width="3%">
			        	<col width="3%">
			        	<col width="3%">
			        	<col width="3%">
			        	<col width="10%">
			        	<col width="10%">
			        	<col width="5%"> 
			        	<col width="10%">
			        </colgroup>
		            <thead>
			            <tr>
			                <th>规则ID</th>
			                <th>服务ID</th>
			                <th>内网地址</th>
			                <th>内网端口</th>
			                <th>外网端口(绑定域名)</th>
			                <th>协议</th>
			                <th>代理协议<i class="glyphicon glyphicon-question-sign" OnClick="ProxyProtocolAbout()"></i></th>
			                <th>备注</th>
			                <th>节点</th>
			                <th>状态</th>
			                <th>操作</th>
			            </tr>
		            </thead>
		            <tbody>
		            <?php if(count($array)!=0){ ?>
		                <?php foreach ($array as $value){ ?>
		                    <tr id="<?php echo $_REQUEST['page']; ?>_<?php echo $value->id; ?>">
		                        <td><?php echo $value->id; ?></td>
		                        <td><a href="clientsservices.php?id=<?php echo $value->sid; ?>"><?php echo $value->sid; ?></a></td>
		                        <td><?php echo Capsule::table('tblhosting')->where('id',$value->sid)->first()->dedicatedip; ?></td>
		                        <td><?php echo $value->remoteport; ?></td>
		                        <td><?php echo $value->port; ?></td>
		                        <td><?php echo $protocols[$value->protocol]; ?></td>
		                        <td><?php echo $proxyprotocolversion[$value->proxyprotocolversion]; ?></td>
		                        <td><?php echo $value->msg; ?></td>
		                        <td><a href="?module=SolusVMNAT&page=nodes&id=<?php echo $value->node; ?>"><?php echo Capsule::table('mod_SolusVMNAT_Node')->where('id',$value->node)->first()->name; ?></a></td>
		                        <td><?php echo SolusVMNAT_StatusArray()[$value->status]; ?></td>
		                        <td>
		                        <button type="button" class="btn btn-danger" OnClick="Action('delete',<?php echo $value->id; ?>);"><i class="fas fa-trash-alt" aria-hidden="true"></i>删除</button>
		                        </td>
		                    </tr>
		            <?php } }else{ ?>
		                <tr id="message">
		                    <td colspan="11" class="text-center">
		                        无
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