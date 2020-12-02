<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu" id="listviewMenu" >
	<ul>
		<li><?php echo (L("$navName")); ?></li>
		<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
		<li>&nbsp;&gt;<a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">列表操作</a></li>
		<li style="width: 50px;">&nbsp;</li>
		<li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
		<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">新建<span>^1</span></a></li>	
		<li>
        <table border="0" style="width:350px;">
            <tr>               
                <td align="left" width="50%"><input id="payment_name_input" class="easyui-textbox" data-options="iconCls:'icon-search'" style="width:200px;font-size: 14px;"></td>
                <td align="left" width="20%">
                    <select name="payment_name_select_shenhe" id="payment_name_select_shenhe" class="txtBox">
                        <option value='' ></option>
                        <option value='已审核'>审核</option>
                        <option value='未审核'>未审核</option>
                    </select>
                </td>
                <td align="right" width="30%">
                <a id="payment_name_search_button" class="easyui-linkbutton" data-options="iconCls:'icon-search'" style="margin-left:10px;font-size: 14px;" onclick="PaymentMgrListviewModule.search();">
                        查 询 </a>    
                </td>
            </tr>
        </table>       
        </li>
		<?php if($searchIntroduce != ''): ?><li style="margin-left: 20px;font-size:14px;font-family:'宋体';line-height: 30px;"><?php echo ($searchIntroduce); ?></li>
			<li style="width: 50px;">&nbsp;</li>
			<li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');" ><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
			<li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">返回列表<span>^4</span></a></li><?php endif; ?>
		<li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
		<li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>

	</ul>
</div>

<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
	<table id="paymentmgr_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />




<script type="text/javascript">
    var PaymentMgrListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#paymentmgr_index_datagrid',

        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="PaymentMgrListviewModule.detailview(' + rowData.paymentmgrid + ')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="PaymentMgrListviewModule.detailview(' + rowData.paymentmgrid + ')">明细</a>');
            if(rowData.is_use == 1){
                btn.push('<a href="javascript:void(0);" onclick="PaymentMgrListviewModule.editview(' + rowData.paymentmgrid + ')">编辑</a>');
            }else{
                btn.push('<a href="javascript:void(0);" onclick="PaymentMgrListviewModule.editview(' + rowData.paymentmgrid + ')">编辑</a>');
                btn.push('<a href="javascript:void(0);" onclick="PaymentMgrListviewModule.deleteRecord(' + rowData.paymentmgrid + ')">删除</a>');
            }
            if(rowData.is_shenhe == 0){
                if(rowData.revpartype == 'finance'){
                    btn.push('<a style="color:red;" href="javascript:void(0);" onclick="PaymentMgrListviewModule.audit(' + rowData.paymentmgrid + ')">审核</a>');
                }else{
                    btn.push('<a style="color:#660000;" href="javascript:void(0);" >未审核</a>');       
                    }
            }else{
                btn.push('<span style="color:black;text-decoration:underline;font-weight:bold;" href="javascript:void(0);" >已审核</span>');
                if(rowData.revpartype == 'finance'){
                    btn.push('<a style="color:red;" href="javascript:void(0);" onclick="PaymentMgrListviewModule.noAudit(' + rowData.paymentmgrid + ')">弃审</a>');
                }
            }
            return btn.join(' | ');
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "<?php echo U('PaymentMgr/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑
        editview: function (id) {
            var url = "<?php echo U('PaymentMgr/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },


        //删除
        deleteRecord: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post("<?php echo U('PaymentMgr/delete');?>", {record: id}, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    };
                }, 'json');
            });
        },

        //审核操作
        audit: function(paymentmgrid){
            var that = this;            
            $.messager.confirm('提示信息', '确定要审核吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('PaymentMgr/audit');?>", {
                    'paymentmgrid': paymentmgrid
                }, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    };
                }, 'json');
            });
        },

        //弃审操作
        noAudit: function(paymentmgrid){
            var that = this;            
            $.messager.confirm('提示信息', '确定要放弃吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('PaymentMgr/noAudit');?>", {
                    'paymentmgrid': paymentmgrid
                }, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    };
                }, 'json');
            });
        },


        //查询
        search: function(){
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            //$.each($(that).parent('form').serializeArray(), function () {
            //    queryParams[this['name']] = this['value'];
            //});
            var search_name = $('#payment_name_input').textbox('getValue');
            var search_shenhe = $('#payment_name_select_shenhe').val();

            queryParams['search_name'] = search_name;
            queryParams['search_shenhe'] = search_shenhe;
            $(this.datagrid).datagrid({
                pageNumber: 1,
                queryParams: queryParams,
                url: "__URL__/listview"
            });
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1', 'ctrl+f1', 'f1'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                }
                ;
            });

            // ctrl+3快捷键,查询公告
            Mousetrap.bind(['ctrl+3', 'ctrl+f3', 'f3'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.search('Notice', '<?php echo (L("Notice")); ?>');
                }
                ;
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');
                }
                ;
            });

            // ESC键
            Mousetrap.bind('esc', function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    $(NoticeListviewModule.dialog).dialog('close');
                }
            });
        }

    };

    $(function () {
         PaymentMgrListviewModule.init();
    })
</script>