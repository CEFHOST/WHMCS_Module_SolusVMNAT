<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$sql = Capsule::table('mod_SolusVMNAT_Plans')->where('id',$_REQUEST['id']);
if(!$sql->exists()){
    SolusVMNAT_PrintText(false,"套餐不存在!");
    return;
}
$info = $sql->first();
$select_protocol = json_decode($info->protocol,true);
$protocols = SolusVMNAT_AllProtocol();
$products = Capsule::table('tblproducts')->where('servertype','solusvmplus')->get();
?>
<h1>编辑</h1>
<input type="hidden" name="a" value="save">
<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody>
        <tr>
            <td class="fieldlabel">名称</td>
            <td class="fieldarea"><input type="text" name="name" value="<?php echo $info->name; ?>" class="form-control input-inline input-400" required></td>
        </tr>

        <tr>
            <td class="fieldlabel">产品</td>
            <td class="fieldarea">
                <select name="pid" class="form-control select-inline">
                    <?php foreach($products as $product){ ?>
                          <option value="<?php echo $product->id; ?>" <?php if($product->id == $info->pid){echo 'selected';} ?>><?php echo $product->id; ?>|<?php echo Capsule::table('tblproductgroups')->where('id',$product->gid)->first()->name; ?> - <?php echo $product->name; ?></option>
                    <?php } ?>
　　　　        </select>
　　　　    </td>
        </tr>

        <tr>
            <td class="fieldlabel">转发规则</td>
            <td class="fieldarea"><input type="number" name="rules" min="1" value="<?php echo $info->rules; ?>" placeholder="10" class="form-control input-inline input-400" required>条</td>
        </tr>

        <tr>
            <td width="20%" class="fieldlabel">可用协议</td>
            <td class="fieldarea">
                <?php foreach($protocols as $protocol => $value){ ?>
                <input type="checkbox" name="protocol[<?php echo $protocol; ?>]" <?php if($select_protocol[$protocol]){echo "checked";} ?>><?php echo $value; ?>
                <br>
                <?php } ?>
            </td>
        </tr>
    </tbody>
</table>

<div align="center">
<button type="submit" class="btn btn-success text-center"><i class="md md-assignment-turned-in"></i>提交</button>
<button type="button" class="btn btn-default text-center" onClick='window.location.href="?module=SolusVMNAT&page=<?php echo $_REQUEST['page']; ?>"'><i class="md md-assignment-turned-in"></i>取消</button>
</div>

<script type="text/javascript">
$(function () {

    $("#pid").fSelect();
});
</script>