<?php
$setting = SolusVMNAT_GetConfigModule();
?>
<input type="hidden" name="a" value="save" />
<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tbody>
        <tr>
            <td class="fieldlabel">节点通讯Key</td>
            <td class="fieldarea"><input type="text" value="<?php echo $setting['key'] ?>" name="key" class="form-control input-inline input-400" required></td>
        </tr>
    </tbody>
</table>

<div align="center">
   <button type="submit" class="btn btn-success text-center"><i class="glyphicon glyphicon-ok"></i>提交</button>
</div>