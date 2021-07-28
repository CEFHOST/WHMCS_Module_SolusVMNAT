<link href="modules/addons/SolusVMNAT/static/css/jquery-confirm.css" rel="stylesheet" type="text/css">
<script src="modules/addons/SolusVMNAT/static/js/jquery-confirm.js" type="text/javascript"></script>
<div class="row">
    <div class="nat-body">
        <div class="col-xs-15">
            <div class="row">
                <div class="col-md-12 col-xs-15">
                        <div class="panel panel-default" id="service-panel">
                            <div class="panel-heading">
                                <h3 class="panel-title"><center>端口转发</center></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10 col-xs-12">
                                        <label>转发规则 ({$info['now_rule']}/{$plan->rules}条)</label>
                                        <div class="progress" style="margin-bottom: 0px;">
                                            <div class="progress-bar" role="progressbar" style="width: {math equation="now / max * 100" now=$info['now_rule'] max=$plan->rules format="%d"}%;">
                                             {math equation="now / max * 100" now=$info['now_rule'] max=$plan->rules format="%d"}%
                                            </div>
                                        </div>
                                    <label>操作</label>
                                    <div class="col-xs-16">
                                        <button type="button" class="btn btn-primary" onClick="add_rule()"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i>添加规则</button>
                                        <button type="button" class="btn btn-primary" onClick="get_node_info()"><i class="fa fa-search" aria-hidden="true"></i>查询节点信息</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="table-responsive">
                                <table id="rules" class="table table-hover table-bordered table-striped">
                                    <colgroup>
                                        <col width="3%">
			        	                <col width="8%">
			                        	<col width="8%">
			                        	<col width="8%">
			                        	<col width="8%">
			                        	<col width="15%">
			        	                <col width="8%"> 
			        	                <col width="15%">
			                        </colgroup>
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>内网端口</th>
                                        <th>外网端口(绑定域名)</th>
                                        <th>协议</th>
                                        <th>代理协议<i class="glyphicon glyphicon-question-sign" OnClick="ProxyProtocolAbout()"></i></th>
                                        <th>备注</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {foreach from=$rules item=rule}
                                    <tr id="rules_{$rule->id}">
                                        <td>{$rule->id}</td>
                                        <td>{$rule->remoteport}</td>
                                        <td>{$rule->port}</td>
                                        <td>{$all_protocols[$rule->protocol]}</td>
                                        <td>{$proxyprotocolversions[$rule->proxyprotocolversion]}</td>
                                        <td>{$rule->msg}</td>
                                        {if $rule->protocol != "http" && $rule->protocol != "https" && $node->dropcn && !isset($other_open_ports[$port])}
                                        <td>{$status[$rule->status]}(已屏蔽大陆方向访问)</td>
                                        {else}
                                        <td>{$status[$rule->status]}</td>
                                        {/if}
                                        <td>
                                            <button type="button" class="btn btn-danger" onClick="delete_rule({$rule->id},this)"><i class="fas fa-trash-alt" aria-hidden="true"></i>删除</button>
                                        </td>
                                    </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12 foot text-center">
                             <p> Copyright ZeroTime Team. All Rights Reserved. </p>
                             <p> Based On <a href="https://github.com/CoiaPrant/SolusVMNAT" style="color: #999;">SolusVMNAT</a></p>
                             <p><a href="https://shop.zeroteam.top/submitticket.php?step=2&deptid=2" style="color: #999;">Report a Bug</a></p>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<style>
    .nat-body,
    .nat-body button {
        font-family: Microsoft YaHei Light, Microsoft YaHei;
    }
    
    .foot {
    font-size: 12px;
    color: #999;
    }
</style>

<script type="text/javascript">
var add_rule = function (obj) {
            var serviceid = $('#service-panel').data('serviceid')
            var html = ''
            
            $.confirm({
                title: '添加规则',
                content: '' +
					'<form action="" class="formName" >' +
					'<div class="form-group">' +
				    '<label>内网端口</label><input type="number" class="form-control" name="remoteport" min="1" max="65535" placeholder="80" />' +
					'<label>外网端口(绑定域名)</label><input type="text" class="form-control" name="port" placeholder="80 或 example.com (视协议而定)" onkeyup="this.value=this.value.toLowerCase()" />' +
					'<label>协议</label><select name="protocol" class="form-control">{foreach from=$protocols item=value key=protocol}<option value="{$protocol}">{$value}</option>{/foreach}</select>' +
					'<label>代理协议<i class="glyphicon glyphicon-question-sign" OnClick="ProxyProtocolAbout()"></i></label><select name="proxyprotocolversion" class="form-control">{foreach from=$proxyprotocolversions item=value key=version}<option value="{$version}">{$value}</option>{/foreach}</select>' +
					'<label>备注</label><input type="text" maxlength="25" class="form-control" name="msg" />' +
					'</div>' +
					'</form>',
                buttons: {
                    formSubmit: {
                        text: '提交',
                        btnClass: 'btn-blue',
                        action: function () {
                            var remoteport = this.$content.find('[name=remoteport]').val();
                            var port = this.$content.find('[name=port]').val();
                            var protocol = this.$content.find('[name=protocol]').val();
                            var proxyprotocolversion = this.$content.find('[name=proxyprotocolversion]').val();
                            var msg = this.$content.find('[name=msg]').val();
                            if (!remoteport || !port || !protocol || !proxyprotocolversion) {
                                $.alert('请将所有项目输入完成');
                                return false;
                            }


                            $.confirm({
                                content: function () {
                                    var self = this;
                                    return $.ajax({
                                        url: 'index.php?m=SolusVMNAT&id={$serviceid}',
                                        type: "POST",
                                        data: {
                                               "a":"add_rule",
                                               "remoteport": remoteport,
                                               "port": port,
                                               "protocol": protocol,
                                               "proxyprotocolversion": proxyprotocolversion,
                                               "msg": msg
                                               },
                                        dataType: 'json',
                                    }).done(function (response) {
                                        if (response.result == "success") {
                                            self.setType('green')
                                            self.setTitle('成功');
                                            self.setContent(response.msg);
                                            
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
                                action: function(){ window.location.reload(); }
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
        
		var delete_rule = function(ruleid,obj){
        	$.confirm({
            title: '询问',
            content: '' +
                '你真的要删除这个规则?',
            buttons: {
                formSubmit: {
                    text: '确认',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.confirm({
                            content: function () {
                                var self = this;
                                return $.ajax({
                                    url: 'index.php?m=SolusVMNAT&id={$serviceid}',
                                    type: "POST",
                                    data: {
                                           "a": "delete_rule",
                                           "ruleid": ruleid
                                           },
                                    dataType: 'json',
                                }).done(function (response) {
                                    if (response.result == "success"){
                                        var tr=obj.parentNode.parentNode;  
                                        var tbody=tr.parentNode;  
                                        tbody.removeChild(tr); 
                                        self.setType('green')
                                        self.setTitle('成功');
                                        self.setContent(response.msg);
                                    } else {
                                        self.setType('red')
                                        self.setTitle('错误');
                                        self.setContent(response.error);
                                    }

                                }).fail(function(){
                                    self.setType('red')
                                    self.setTitle('错误');
                                    self.setContent('与服务器通讯时出现错误, 请重试.');
                                });
                            },
                            buttons: {
                                ok: {
                                text: "完成"
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

var get_node_info = function(obj){
        	$.confirm({
            title: '查询节点信息',
            content: '' +
                '你确定要查询节点信息吗?',
            buttons: {
                formSubmit: {
                    text: '查询',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.confirm({
                            content: function () {
                                var self = this;
                                return $.ajax({
                                    url: 'index.php?m=SolusVMNAT&id={$serviceid}',
                                    type: "POST",
                                    data: {
                                           "a": "get_node_info",
                                           },
                                    dataType: 'json',
                                }).done(function (response) {
                                    if (response.result == "success"){
                                        self.setType('green')
                                        self.setTitle(response.title);
                                        self.setContent(response.html);
                                    } else {
                                        self.setType('red')
                                        self.setTitle('错误');
                                        self.setContent(response.error);
                                    }

                                }).fail(function(){
                                    self.setType('red')
                                    self.setTitle('错误');
                                    self.setContent('与服务器通讯时出现错误, 请重试.');
                                });
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
        
var ProxyProtocolAbout = function(obj){
    $.alert("<h4>相关说明</h4>Proxy Protocol(代理协议) 是HaProxy的作者Willy Tarreau于2010年开发和设计的一个Internet协议, 通过为TCP添加一个很小的头信息, 来方便的传递客户端信息（协议栈,源IP,目的IP,源端口,目的端口等), 在网络情况复杂又需要获取用户真实IP时非常有用。其本质是在三次握手结束后由代理在连接中插入了一个携带了原始连接四元组信息的数据包。 <br><br> 目前 Proxy Protocol有两个版本, v1仅支持Human-Readable报头格式（ASCIII码）, v2需同时支持Human-Readable和二进制格式, 即需要兼容v1格式 <br><br> Proxy Protocol的接收端必须在接收到完整有效的 Proxy Protocol 头部后才能开始处理连接数据。因此对于服务器的同一个监听端口, 不存在兼容带Proxy Protocol包的连接和不带Proxy Protocol包的连接。如果服务器接收到的第一个数据包不符合Proxy Protocol的格式, 那么服务器会直接终止连接 <br><br> 对于一般人来说不需要开启此功能，若有需求可查询相关应用文档<br><strong>当前插件仅HTTP/HTTPS协议支持此功能</strong>");
}

</script>