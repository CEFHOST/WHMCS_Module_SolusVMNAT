<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$info['count']['rules'] = Capsule::table('mod_SolusVMNAT_Rules')->where('status','!=','Deleted')->count();
$info['count']['nodes'] = Capsule::table('mod_SolusVMNAT_Node')->count();
$info['count']['plans'] = Capsule::table('mod_SolusVMNAT_Plans')->count();
$sql = Capsule::table('mod_SolusVMNAT_Node')->get();
foreach ($sql as $value){
    $nodes['name'][] = $value->name;
    $nodes['count'][$value->name] = Capsule::table('mod_SolusVMNAT_Rules')->where('node',$value->id)->where('status','!=','Deleted')->count();
}
unset($sql);
$array = SolusVMNAT_AllProtocol();
foreach ($array as $name => $value){
    $rules['name'][] = $value;
    $rules['count'][$value] = Capsule::table('mod_SolusVMNAT_Rules')->where('protocol',$name)->where('status','!=','Deleted')->count();
}
unset($array);
?>

<div class="el-col el-col-24 el-col-md-12">
   <div class="source">
    <form class="el-form server_info el-form--label-left">
     <div class="el-form-item">
      <label class="el-form-item__label">当前版本</label>
      <div class="el-form-item__content">
       <span><?php echo $version; ?></span>
       
      </div>
     </div> 
     
     <div class="el-form-item">
      <label class="el-form-item__label">规则总数</label>
      <div class="el-form-item__content">
       <span><?php echo $info['count']['rules'] ?></span>
       
      </div>
     </div> 

     <div class="el-form-item">
      <label class="el-form-item__label">节点总数</label>
      <div class="el-form-item__content">
       <span><?php echo $info['count']['nodes'] ?></span>
       
      </div>
     </div> 
    
    
     <div class="el-form-item">
      <label class="el-form-item__label">套餐总数</label>
      <div class="el-form-item__content">
       <span><?php echo $info['count']['plans'] ?></span>
       
      </div>
     </div> 
    
    </form>
   </div>
  </div>
 
  <div class="el-col el-col-24 el-col-md-12">
   <div id="rules" style="width: 450px; height: 500px; margin-bottom: 30px; -webkit-tap-highlight-color: transparent; user-select: none; position: relative; background: transparent;">
    </div>
   <div id="nodes" style="width: 450px; height: 500px; -webkit-tap-highlight-color: transparent; user-select: none; position: relative; background: transparent;">
    </div>
  </div>
  
<script type="text/javascript">
    var nodeChart = echarts.init(document.getElementById('nodes')); 
    var nodeoption = {
    title: {
        text: '节点数据',
        subtext: '规则分布统计',
        left: 'center',
        textStyle: { 
            color: '#58b7ff',
        },
    },
    
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)'
    },
    legend: {
        bottom: 10,
        left: 'center',
        data: <?php echo json_encode($nodes['name']);?>
    },
    series: [
        {
            name: '规则数',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [ 
                <?php foreach($nodes['name'] as $value){echo json_encode(array('value' => $nodes['count'][$value] , 'name' => $value)).','.PHP_EOL;}?>
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};
    nodeChart.setOption(nodeoption); 
    
    
    var ruleChart = echarts.init(document.getElementById('rules')); 
    var ruleoption = {
    title: {
        text: '协议数据',
        subtext: '协议分布统计',
        left: 'center',
        textStyle: { 
            color: '#58b7ff',
        },
    },
    
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)'
    },
    legend: {
        bottom: 10,
        left: 'center',
        data: <?php echo json_encode($rules['name']);?>
    },
    series: [
        {
            name: '规则数',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [ 
                <?php foreach($rules['name'] as $value){echo json_encode(array('value' => $rules['count'][$value] , 'name' => $value)).','.PHP_EOL;}?>
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};
    ruleChart.setOption(ruleoption); 
    </script>