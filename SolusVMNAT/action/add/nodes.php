<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$servers = Capsule::table('tblservers')->where('type','solusvmplus')->get();
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
            <td class="fieldlabel">SolusVM主控</td>
            <td class="fieldarea">
                <select name="serverid" class="form-control select-inline">
                    <?php foreach($servers as $server){ ?>
                          <option value="<?php echo $server->id; ?>"><?php echo $server->id; ?>|<?php echo $server->name; ?></option>
                    <?php } ?>
　　　　        </select>
　　　　    </td>
        </tr>

        <tr>
            <td class="fieldlabel">SolusVM节点名称</td>
            <td class="fieldarea"><input type="text" name="svm_node" class="form-control input-inline input-400" placeholder="localhost" required>SolusVM管理员后台 -> Nodes</td>
　　　　    </td>
        </tr>

        <tr>
            <td class="fieldlabel">外网网卡</td>
            <td class="fieldarea"><input type="text" name="eth_device" placeholder="eth0" class="form-control input-inline input-400" required>外网网卡设备名称</td>
        </tr>
        
        <tr>
            <td class="fieldlabel">节点地址</td>
            <td class="fieldarea"><input type="text" name="addr" class="form-control input-inline input-400" required placeholder="example.com" onkeyup="this.value=this.value.toLowerCase()">域名或IP</td>
        </tr>
        
        <tr><td width="20%" class="fieldlabel">屏蔽大陆</td>
            <td class="fieldarea">
            <label class="checkbox-inline">
                <input type="checkbox" name="dropcn"> 屏蔽大陆非HTTP(S)共享端口的入站流量
            </label>
            </td>
        </tr>

        <tr>
            <td class="fieldlabel">白名单端口</td>
            <td class="fieldarea"><input type="text" name="other_open_ports" placeholder="233 或 233,234,245" class="form-control input-inline input-400">22端口已自动过白，屏蔽大陆功能未开启时此设置无效 </td>
        </tr>

        <tr><td width="20%" class="fieldlabel">API即时通知</td>
            <td class="fieldarea">
            <label class="checkbox-inline">
                <input type="checkbox" name="api"> 通过API主动通知节点
            </label>
            </td>
        </tr>
        
        
        <tr>
            <td class="fieldlabel">API端口</td>
            <td class="fieldarea"><input type="number" min="1" max="65535" name="apiport" class="form-control input-inline input-400" placeholder="233" required></td>
        </tr>
        
        <tr>
            <td class="fieldlabel">HTTP端口</td>
            <td class="fieldarea"><input type="number" min="1" max="65535" name="http_port" class="form-control input-inline input-400" placeholder="80" required></td>
        </tr>
        
        <tr>
            <td class="fieldlabel">HTTP端口2</td>
            <td class="fieldarea"><input type="number" min="1" max="65535" name="http_port_2" class="form-control input-inline input-400" placeholder="8080" required>和HTTP端口一样则无视此配置</td>
        </tr>
        
        <tr>
            <td class="fieldlabel">HTTPS端口</td>
            <td class="fieldarea"><input type="number" min="1" max="65535" name="https_port" class="form-control input-inline input-400" placeholder="443" required></td>
        </tr>
        
        <tr>
            <td class="fieldlabel">HTTPS端口2</td>
            <td class="fieldarea"><input type="number" min="1" max="65535" name="https_port_2" class="form-control input-inline input-400" placeholder="8443" required>和HTTPS端口一样则无视此配置</td>
        </tr>
        
        <tr>
            <td width="20%" class="fieldlabel">域名ICP备案检测</td>
            <td class="fieldarea">
                <input type="checkbox" name="icp"> 提示: 仅HTTP/HTTPS转发有效
            </td>
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
        
        <tr>
            <td class="fieldlabel">保留端口</td>
            <td class="fieldarea"><textarea name="retain_port" class="form-control input-inline input-400" placeholder="1-1024&#10;65535"></textarea></td>
        </tr>
        
        <tr>
            <td class="fieldlabel">更新周期</td>
            <td class="fieldarea"><input type="number" name="update_cycle" min="60" class="form-control input-inline input-400" placeholder="300" required>秒</td>
        </tr>
        
        <tr>
            <td class="fieldlabel">备注</td>
            <td class="fieldarea"><input type="text" name="msg" class="form-control input-inline input-400"></td>
        </tr>
    </tbody>
</table>

<div align="center">
<button type="submit" class="btn btn-success text-center"><i class="md md-assignment-turned-in"></i>提交</button>
<button type="button" class="btn btn-default text-center" onClick='window.location.href="?module=SolusVMNAT&page=<?php echo $_REQUEST['page']; ?>"'><i class="md md-assignment-turned-in"></i>取消</button>
</div>