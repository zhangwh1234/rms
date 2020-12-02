<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="YingshouRevparMgrListviewModule.createview();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a>
        </li>
        <li><a href="javascript:void(0);" onclick="YingshouRevparMgrListviewModule.createview();">结账操作</a></li>

        <?php if($revparType == 'finance'): ?><li style="margin-left: 50px;">
                <div class="easyui-panel" style="width:100%;max-width:400px;padding:2px 60px 0px 60px;">
                    <div style="margin-bottom:2px">
                        <input id="companyselect" class="easyui-combotree"
                            data-options="url:'__URL__/tree',method:'get',label:'Select Nodes:',labelPosition:'top',multiple:true,value:['总部'],onlyLeafCheck:true" style="width:100%">
                    </div>
                </div>
            </li><?php endif; ?>

        <li style="margin-left: 50px;"><input type="text" id="yingshouRevparMgrListviewDateInput" class="easyui-datebox" name="yingshouRevparMgrListviewDateInput" style="font-size: 16px;width:150px;"
                value="<?php echo ($getDate); ?>" /></li>

        <li style="margin-left: 20px;"><select name="yingshouRevparMgrListviewApInput" id="yingshouRevparMgrListviewApInput" class="txtBox" style="width:100px;font-size: 14px;margin-top: 5px;">
                <?php if($searchAp): ?><option value="<?php echo ($getAp); ?>"><?php echo ($getAp); ?></option><?php endif; ?>
                <option value="上午">上午</option>
                <option value="下午">下午</option>
                <option value="全天">全天</option>
            </select></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouRevparMgrListviewModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">查询</a></li>

        <li style="width: 30px;">&nbsp;</li>
        <?php if($revparType == 'finance'): ?><li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouRevparMgrListviewModule.delete();" class="easyui-linkbutton" iconCls="icons-table-table">删除结账</a></li>
            <li style="width: 20px;">&nbsp;</li>
            <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouRevparMgrListviewModule.revparList();" class="easyui-linkbutton" iconCls="icons-table-table">分公司结账情况</a></li><?php endif; ?>

        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="YingshouRevparMgrListviewDiv" style="height:400px;width:100%;clear:both;">
    <table id="yingshourevparmgr_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />

<script type="text/javascript">
    var YingshouRevparMgrListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshourevparmgr_index_datagrid',

        init: function () {
            //设置div的高度
            $('#YingshouRevparMgrListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            $('#yingshourevparmgr_index_datagrid').datagrid({
                rowStyler: YingshouRevparMgrListviewModule.rowStyler
            });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            if (rowData.date == '营收情况') {
                return [];
            }
            if (rowData.date == '贷方合计营收') {
                return [];
            }
            if (rowData.date == '借方合计营收') {
                return [];
            }
            if ((rowData.name == '外送收入') || (rowData.name == '工作餐金额') || (rowData.name == '门市金额')) {
                return [];
            }
            if (rowData.company == '多公司') {
                return [];
            }

            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouRevparMgrListviewModule.revparDetailview(\'' + rowData.name + '\',\'' + rowData.date + '\',\'' + rowData.ap +
                '\')">详细</a>');
            return btn.join(' | ');
            return [];
        },

        rowStyler: function (index, row) { //就改变背景颜色，以便区别     
            if (row.date == '营收情况') {
                return 'background-color:#99CCFF; color:black'; // return inline style
            }
            if (row.date == '贷方合计营收') {
                return 'background-color:#99CCFF; color:black'; // return inline style
            }
            if (row.date == '借方合计营收') {
                return 'background-color:#99CCFF; color:black'; // return inline style
            }
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "<?php echo U('YingshouRevparMgr/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑
        editview: function (id) {
            var url = "<?php echo U('YingshouRevparMgr/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },


        //删除
        delete: function () {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                //获取日期
                var revpar_date = $('#yingshouRevparMgrListviewDateInput').datebox('getValue');
                var revpar_ap = $('#yingshouRevparMgrListviewApInput').val();
                var data = {
                    'revpar_date': revpar_date,
                    'revpar_ap': revpar_ap
                };
                $.ajax({
                    type: "POST",
                    url: "__URL__/delete",
                    data: data,
                    dataType: "json",
                    success: function (res) {
                        $.messager.progress('close');
                        if (res.status) {
                            $.app.method.tip('提示信息', res.info, 'error');
                        } else {
                            $.app.method.tip('提示信息', res.info, 'info');
                            $.messager.progress('close');

                            var queryParams = $(that.datagrid).datagrid('options').queryParams;
                            queryParams['getDate'] = revpar_date;
                            queryParams['getAp'] = revpar_ap;
                            if ($('#companyselect').length > 0) {
                                var company = $('#companyselect').combotree('getValues');
                                var companyArr = '';
                                for (var i = 0; i < company.length; i++) {
                                    companyArr += company[i] + ',';
                                }
                                queryParams['companyArr'] = companyArr;
                            }
                            $(that.datagrid).datagrid({
                                pageNumber: 1,
                                queryParams: queryParams,
                                url: "__URL__/listview"
                            });
                        };
                    }
                });

            });
        },

        //生成结账单的对话框
        createview: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '营收结账',
                iconCls: 'icons-application-application_add',
                width: 400,
                height: 140,
                cache: false,
                href: "<?php echo U('YingshouRevparMgr/generalview');?>",
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
                        var revpar_date = $('#yingshouRevparMgrGeneralviewDateInput').datebox('getValue'); //
                        var revpar_ap = $('#yingshouRevparMgrGeneralviewApInput').val();
                        var data = {
                            'revpar_date': revpar_date,
                            'revpar_ap': revpar_ap
                        };
                        $.ajax({
                            type: "POST",
                            url: "__URL__/revparMgrCalculate",
                            data: data,
                            dataType: "json",
                            success: function (res) {
                                if (res.state == 0) { //0就是错误
                                    $.messager.progress('close');
                                    var url = '__URL__/resultview';
                                    IndexIndexModule.updateOperateTab(url);
                                } else { //state = 1就是success
                                    $.messager.progress('close');
                                    $('#yingshouRevparMgrListviewDateInput').datebox('setValue', revpar_date);
                                    $('#yingshouRevparMgrListviewApInput').val(revpar_ap);
                                    var queryParams = $(that.datagrid).datagrid('options').queryParams;
                                    queryParams['getDate'] = revpar_date;
                                    queryParams['getAp'] = revpar_ap;
                                    if ($('#companyselect').length > 0) {
                                        var company = $('#companyselect').combotree('getValues');
                                        var companyArr = '';
                                        for (var i = 0; i < company.length; i++) {
                                            companyArr += company[i] + ',';
                                        }
                                        queryParams['companyArr'] = companyArr;
                                    }
                                    $(that.datagrid).datagrid({
                                        pageNumber: 1,
                                        rowStyler: this.rowStyler,
                                        url: "__URL__/listview"
                                    });
                                }
                            }
                        });
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

        //根据日期查询显示销售汇总
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            var getDate = $('#yingshouRevparMgrListviewDateInput').datebox('getValue');
            var getAp = $('#yingshouRevparMgrListviewApInput').val();
            if ($('#companyselect').length > 0) {
                var company = $('#companyselect').combotree('getValues');
                var companyArr = '';
                for (var i = 0; i < company.length; i++) {
                    companyArr += company[i] + ',';
                }
                queryParams['companyArr'] = companyArr;
            }


            queryParams['getDate'] = getDate;
            queryParams['getAp'] = getAp;

            $(this.datagrid).datagrid({
                pageNumber: 1,
                queryParams: queryParams,
                url: "__URL__/listview"
            });
        },

        //显示详细的结账条目
        revparDetailview: function (name, date, ap) {
            var url = '__URL__/revparDetailview/name/' + name + '/date/' + date + '/ap/' + ap;
            IndexIndexModule.updateOperateTab(url);
        },

        //查看分公司结账完成的情况
        revparList: function () {
            //获取日期
            var revpar_date = $('#yingshouRevparMgrListviewDateInput').datebox('getValue');
            var revpar_ap = $('#yingshouRevparMgrListviewApInput').val();
            var url = '__URL__/revparList/revpar_date/' + revpar_date + '/revpar_ap/' + revpar_ap;
            IndexIndexModule.updateOperateTab(url);
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
        }

    };

    $(function () {
        YingshouRevparMgrListviewModule.init();
    })
</script>