<link href="../modules/addons/SolusVMNAT/static/css/jquery-confirm.css" rel="stylesheet" type="text/css">
<link href="../modules/addons/SolusVMNAT/static/css/info.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../modules/addons/SolusVMNAT/static/css/select.css">

<script type="text/javascript" src="../modules/addons/SolusVMNAT/static/js/jquery-confirm.js"></script>
<script type="text/javascript" src="../modules/addons/SolusVMNAT/static/js/clipboard.js"></script>
<script type="text/javascript" src="../modules/addons/SolusVMNAT/static/js/echarts.js"></script>
<script type="text/javascript">
    var copy = new ClipboardJS(".btn");
    copy.on('success', function(e) {
        $.alert("复制成功");
    });
    copy.on('error', function(e) {
        $.alert("复制失败,请手动复制");
    });

    function Action(a, id) {
        if (!a || !id) {
            console.log('参数缺失');
            return false;
        }
        if (confirm('你确定吗?')) {
            window.location.replace('?module=<?php echo $_REQUEST['module']; ?>&a=' + a + '&page=<?php echo $_REQUEST['page']; ?>&id=' + id);
        }
    }

    function ProxyProtocolAbout() {
        $.alert("<h1>相关说明</h1>Proxy Protocol(代理协议) 是HaProxy的作者Willy Tarreau于2010年开发和设计的一个Internet协议, 通过为TCP添加一个很小的头信息, 来方便的传递客户端信息（协议栈,源IP,目的IP,源端口,目的端口等), 在网络情况复杂又需要获取用户真实IP时非常有用。其本质是在三次握手结束后由代理在连接中插入了一个携带了原始连接四元组信息的数据包。 <br><br> 目前 Proxy Protocol有两个版本, v1仅支持Human-Readable报头格式（ASCIII码）, v2需同时支持Human-Readable和二进制格式, 即需要兼容v1格式 <br><br> Proxy Protocol的接收端必须在接收到完整有效的 Proxy Protocol 头部后才能开始处理连接数据。因此对于服务器的同一个监听端口, 不存在兼容带Proxy Protocol包的连接和不带Proxy Protocol包的连接。如果服务器接收到的第一个数据包不符合Proxy Protocol的格式, 那么服务器会直接终止连接 <br><br> 对于一般人来说不需要开启此功能，若有需求可查询相关应用文档");
    }
</script>
<form method="POST" action="?module=SolusVMNAT">
    <nav class="navbar navbar-default">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="true">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">NAT端口映射</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="<?php echo $active['view']; ?>"><a href="?module=SolusVMNAT&page=view">统计信息</a></li>
                <li class="<?php echo $active['rules']; ?>"><a href="?module=SolusVMNAT&page=rules">转发规则</a></li>
                <li class="<?php echo $active['nodes']; ?>"><a href="?module=SolusVMNAT&page=nodes">节点管理</a></li>
                <li class="<?php echo $active['plans']; ?>"><a href="?module=SolusVMNAT&page=plans">套餐管理</a></li>
                <li class="<?php echo $active['setting']; ?>"><a href="?module=SolusVMNAT&page=setting">相关设置</a></li>
                <li class="<?php echo $active['database']; ?>"><a href="?module=SolusVMNAT&page=database">数据库信息</a></li>
            </ul>
        </div>
    </nav>
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