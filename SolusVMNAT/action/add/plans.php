<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$products = Capsule::table('tblproducts')->where('servertype','solusvmplus')->get();
$protocols = SolusVMNAT_AllProtocol();
?>
<h1>添加</h1>
<input type="hidden" name="a" value="save">
<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody>
        <tr>
            <td class="fieldlabel">名称</td>
            <td class="fieldarea"><input type="text" name="name" class="form-control input-inline input-400" required></td>
        </tr>
        
        <tr>
            <td class="fieldlabel">产品</td>
            <td class="fieldarea">
                <select name="pid" class="form-control select-inline">
                    <?php foreach($products as $product){ ?>
                          <option value="<?php echo $product->id; ?>"><?php echo $product->id; ?>|<?php echo Capsule::table('tblproductgroups')->where('id',$product->gid)->first()->name; ?> - <?php echo $product->name; ?></option>
                    <?php } ?>
　　　　        </select>
　　　　    </td>
        </tr>
        
        <tr>
            <td class="fieldlabel">转发规则</td>
            <td class="fieldarea"><input type="number" min="1" name="rules" class="form-control input-inline input-400" placeholder="10" required>条</td>
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