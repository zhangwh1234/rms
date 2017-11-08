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
	<table id="yingshouincomemgr_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />


<script type="text/javascript">
    var YingshouIncomeMgrListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouaccounts_index_datagrid',

        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.detailview(' + rowData.incomemgrid + ',\''+ rowData.date + '\')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.editview(' + rowData.incomemgrid + ',\''+ rowData.date + '\')">编辑</a>');
            btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.deleteRecord(' + rowData.incomemgrid + ',\''+ rowData.date + '\')">删除</a>');
            return btn.join(' | ');
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id,getDate) {
            var url = "<?php echo U('YingshouIncomeMgr/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate='+getDate;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑
        editview: function (id,getDate) {
            var url = "<?php echo U('YingshouIncomeMgr/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate='+getDate;
            IndexIndexModule.updateOperateTab(url);
        },


        //删除
        deleteRecord: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post("<?php echo U('YingshouIncomeMgr/delete');?>", {record: id}, function (res) {
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
        },

        //获取支付客户
        getAccountsByCode: function (evt,obj,module) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            var code = $(obj).val();

            if ((key == 13) && (code.length > 0)) {
                this.getAccounts(code,module);
            }

        },

        //根据用户输入的客户代码，输出产品名称
        getAccounts: function (code,module) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {
                },
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        if(module == 'create'){
                            $("#YingshouIncomeMgrCreateviewForm input[name=name]").val(mydata.name);
                            //跳转到下一行
                            $("#YingshouIncomeMgrCreateviewForm input[name=money]").focus();
                            $("#accountbilldetailview").html(mydata.accounts);
                        }

                    } else {
                        alert("输入的客户代码有错误，请重新输入！");
                        return;
                    }
                }

            })
        },

        accountsPickList: function (url) {
            //url = url + '/returnModule/' + '<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>';
            var that = this;
            $('#globel-dialog-div').dialog({
                title: '选择工作餐',
                iconCls: 'icons-application-application_add',
                width: 900,
                height: 540,
                cache: false,
                href: url,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;

                                var formArray = $(this).serializeArray();
                                var url = '__URL__/searchviewAddress/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订单地址查询');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $('#globel-dialog-div').dialog('close');
                    }
                }]
            });

        },

    };

    $(function () {
         YingshouIncomeMgrListviewModule.init();
    })
</script>