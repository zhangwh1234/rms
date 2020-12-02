<?php if (!defined('THINK_PATH')) exit();?><!-- 分录底稿  -->
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>
        <?php if( $createon == 'ok' ): ?><li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title=""
                        border="0"></a></li>
            <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">新建<span>^1</span></a></li><?php endif; ?>
        <li style="margin-left: 50px;"><input type="text" id="yingshouIncomeMgrListviewStartDateInput" class="easyui-datebox" name="yingshouIncomeMgrListviewDateInput"
                style="font-size: 16px;width:150px;" value="cdate" />到
            <input type="text" id="yingshouIncomeMgrListviewEndDateInput" class="easyui-datebox" name="yingshouIncomeMgrListviewDateInput" style="font-size: 16px;width:150px;" value="cdate" /></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouIncomeMgrListviewModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">查询</a></li>
        <?php if($revparType != 'company'): ?><li style="width: 20px;">&nbsp;</li>
            <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouIncomeMgrListviewModule.audit(this);" class="easyui-linkbutton" iconCls="icons-table-table">全部审核</a></li>
            <li style="width: 20px;">&nbsp;</li>
            <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouIncomeMgrListviewModule.recover(this);" class="easyui-linkbutton" iconCls="icons-table-table">放弃审核</a></li>
            <li style="width: 30px;">&nbsp;</li>
            <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouIncomeMgrListviewModule.createview();" class="easyui-linkbutton" iconCls="icons-table-table">导出分录</a></li><?php endif; ?>

        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>
<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
    <table id="yingshouincomemgr_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />

<script type="text/javascript">
    var YingshouIncomeMgrListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouincomemgr_index_datagrid',

        init: function () {
            var that = this;
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            // $('#yingshouIncomeMgrListviewDateInput').datebox({
            //     onSelect: function (date) {
            //         that.search();
            //     }
            // });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {

            var btn = [];
            if (rowData.shenhe == 1) {
                btn.push('<span>已审核</span>')
            } else {
                if (rowData.shenhe == 0) {
                    btn.push("<span style='color:red;'>未审</span>")
                }
                if (rowData.revparType == 'company') {
                    btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.detailview(' + rowData.incomemgrid + ',\'' + rowData.date + '\')">查看</a>');
                    //btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.editview(' + rowData.incomemgrid + ',\'' + rowData.date + '\')">编辑</a>');
                    btn.push('<a href="javascript:void(0);" onclick="YingshouIncomeMgrListviewModule.deleteRecord(' + rowData.incomemgrid + ',\'' + rowData.date + '\')">删除</a>');
                }
            }
            return btn.join(' | ');
        },

        //生成生成分录底稿的对话框
        createview: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '分录底稿生成器',
                iconCls: 'icons-application-application_add',
                width: 400,
                height: 140,
                cache: false,
                href: "__URL__/generalview",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        $.messager.progress({
                            text: '处理中，请稍候...'
                        });
                        //获取日期
                        var start_date = $('#yingshouIncomeGeneralviewStartDateInput').datebox(
                            'getValue'); //
                        var end_date = $('#yingshouIncomeGeneralviewEndDateInput').datebox(
                            'getValue');
                        var data = {
                            'start_date': start_date,
                            'end_date': end_date
                        };
                        url = '__URL__/outputExcel/startDate/' + start_date + '/endDate/' + end_date;
                        window.location.href = url;
                        $.messager.progress('close');
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id, getDate) {
            var url = "<?php echo U('YingshouIncomeMgr/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate=' + getDate;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑
        editview: function (id, getDate) {
            var url = "<?php echo U('YingshouIncomeMgr/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate=' + getDate;
            IndexIndexModule.updateOperateTab(url);
        },

        //审核数据
        audit: function (that) {
            var that = this;
            startDate = $('#yingshouIncomeMgrListviewStartDateInput').datebox('getValue');
            endDate = $('#yingshouIncomeMgrListviewEndDateInput').datebox('getValue');
            $.messager.confirm('提示信息', '确定要审核吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('YingshouIncomeMgr/audit');?>", {
                    'startDate': startDate,
                    'endDate': endDate
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

        //恢复审核的数据
        recover: function (that) {
            var that = this;
            startDate = $('#yingshouIncomeMgrListviewStartDateInput').datebox('getValue');
            endDate = $('#yingshouIncomeMgrListviewEndDateInput').datebox('getValue');
            $.messager.confirm('提示信息', '确定要恢复数据吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('YingshouIncomeMgr/recover');?>", {
                    'startDate': startDate,
                    'endDate': endDate
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

        //导入分录底稿
        export: function (that) {
            var that = this;
            getDate = $('#yingshouIncomeMgrListviewDateInput').datebox('getValue');
            url = '__URL__/outputExcel/getDate/' + getDate;
            window.location.href = url;
        },


        //删除
        deleteRecord: function (id, getDate) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('YingshouIncomeMgr/delete');?>", {
                    record: id,
                    getDate: getDate
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

        //根据日期查询显示销售汇总
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            var startdate = $('#yingshouIncomeMgrListviewStartDateInput').datebox('getValue');
            var enddate = $('#yingshouIncomeMgrListviewEndDateInput').datebox('getValue');

            queryParams['startDate'] = startdate;
            queryParams['endDate'] = enddate;
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
                };
            });

            // ctrl+3快捷键,查询公告
            Mousetrap.bind(['ctrl+3', 'ctrl+f3', 'f3'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.search('Notice', '<?php echo (L("Notice")); ?>');
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');
                };
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
        getAccountsByCode: function (evt, obj, module) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            var code = $(obj).val();

            if ((key == 13) && (code.length > 0)) {
                this.getAccounts(code, module);
            }

        },

        //根据用户输入的客户代码，输出产品名称
        getAccounts: function (code, module) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        if (module == 'create') {
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
        setTimeout(function(){
            YingshouIncomeMgrListviewModule.search();
        },100)
    })
</script>