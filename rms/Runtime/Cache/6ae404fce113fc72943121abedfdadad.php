<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu" id="listviewMenu" >
	<ul>
		<li><?php echo (L("$navName")); ?></li>
		<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
		<li>&nbsp;&gt;<a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">列表操作</a></li>
		<li style="width: 50px;">&nbsp;</li>
		<li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
		<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">新建<span>^1</span></a></li>
		<li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.search('<?php echo ($moduleName); ?>','<?php echo (L("$moduleName")); ?>');"><img src=".__PUBLIC__/Images/searchBtn.png" alt="" title="" border="0"></a></li>
		<li><a href="javascript:void(0);"  onclick="IndexIndexModule.search('<?php echo ($moduleName); ?>','<?php echo (L("$moduleName")); ?>');">查询<span>^3</span></a></li>
		<?php if($searchIntroduce != ''): ?><li style="margin-left: 20px;font-size:14px;font-family:'宋体';line-height: 30px;"><?php echo ($searchIntroduce); ?></li>
			<li style="width: 50px;">&nbsp;</li>
			<li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');" ><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
			<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">返回列表<span>^4</span></a></li><?php endif; ?>
		<li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
		<li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>

	</ul>
</div>

<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
	<table id="sendnamemgr_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />



<script type="text/javascript">

    var SendnameMgrListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#sendnamemgr_index_datagrid',


        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
        },


        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="SendnameMgrListviewModule.detailview(' + rowData.sendnamemgrid + ')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="SendnameMgrListviewModule.editview(' + rowData.sendnamemgrid + ')">编辑</a>');
            btn.push('<a href="javascript:void(0);" onclick="SendnameMgrListviewModule.deleteRecord(' + rowData.sendnamemgrid + ')">删除</a>');
            return btn.join(' | ');
        },

        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "__URL__/detailview/record/"+id;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑用户记录
        editview: function (id) {
            var url = "__URL__/editview/record/"+id;
            IndexIndexModule.updateOperateTab(url);
        },

        //删除用户记录
        deleteRecord : function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;

                $.messager.progress({text: '处理中，请稍候...'});
                $.get("<?php echo U('SendnameMgr/delete');?>", {record: id}, function (res) {
                    $.messager.progress('close');

                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    }
                }, 'json'
                )
                ;
            });
        }

    };

    $(function(){
        SendnameMgrListviewModule.init();
    })
</script>