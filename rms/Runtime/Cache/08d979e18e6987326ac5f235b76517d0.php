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
	<table id="role_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />


<input id="MessagesAction" type="hidden"  value="listview" />
<script>
    var RoleListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#role_index_datagrid',


        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },


        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="RoleListviewModule.detailview(' + rowData.id + ')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="RoleListviewModule.editview(' + rowData.id + ')">编辑</a>');
            btn.push('<a href="javascript:void(0);" onclick="RoleListviewModule.delete(' + rowData.id + ')">删除</a>');
            btn.push('<a href="javascript:void(0);" onclick="RoleListviewModule.editviewAccess(' + rowData.id + ')">配置权限</a>');
            return btn.join(' | ');
        },

        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "<?php echo U('Role/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?record=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑角色
        editview: function (id) {
            var url = "<?php echo U('Role/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?record=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //配置角色权限
        editviewAccess: function (id) {
            var url = "<?php echo U('Role/editviewAccess');?>";
            url += url.indexOf('?') != -1 ? '&rid=' + id : '?rid=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //删除角色
        delete: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;

                $.messager.progress({text: '处理中，请稍候...'});
                $.get("__URL__/delete", {record: id}, function (res) {
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
        },

        //新建的快捷操作
        quickKeyboardAction:function(){
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1','ctrl+f1','f1'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#RoleAction').val() == 'listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+3快捷键,查询公告
            Mousetrap.bind(['ctrl+3','ctrl+f3','f3'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#RoleAction').val() == 'listview')) {
                    IndexIndexModule.search('Messages','<?php echo (L("Messages")); ?>');
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#RoleAction').val() == 'listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');
                }
                ;
            });
        }

    };

    $(function(){
        RoleListviewModule.init();
    })
</script>