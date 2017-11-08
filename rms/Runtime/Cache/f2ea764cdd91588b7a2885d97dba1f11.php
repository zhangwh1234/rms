<?php if (!defined('THINK_PATH')) exit();?><!-- 分录底稿  -->
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="YingshouFinanceListviewModule.createview();"><img
                    src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="YingshouFinanceListviewModule.createview();">生成分录</a></li>

        <li style="margin-left: 50px;"><input type="text" id="yingshouRevparMgrListviewDateInput" class="easyui-datebox"
                name="yingshouRevparMgrListviewDateInput" style="font-size: 16px;width:150px;" value="cdate" /></li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouFinanceListviewModule.search(this);"
                class="easyui-linkbutton" iconCls="icons-table-table">查询</a></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouFinanceMgrListviewModule.allExport(this);"
                class="easyui-linkbutton" iconCls="icons-table-table">导出分录</a></li>


        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png"
                    alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="YingshouRevparMgrListviewDiv" style="height:400px;width:100%;clear:both;">
    <table id="yingshoufinance_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />

<script type="text/javascript">
    var YingshouFinanceListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshoufinance_index_datagrid',

        init: function () {
            //设置div的高度
            $('#YingshouRevparMgrListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouFinanceListviewModule.detailview(' +
                rowData.financeid + ')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="YingshouFinanceListviewModule.singleExport(' +
                rowData.financeid + ')">导出</a>');
            return btn.join(' | ');
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "<?php echo U('YingshouFinance/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
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
                href: "<?php echo U('YingshouFinance/generalview');?>",
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
                        var finance_date = $('#yingshouFinanceGeneralviewDateInput').datebox(
                            'getValue'); //
                        var data = {
                            'finance_date': finance_date
                        };
                        $.ajax({
                            type: "POST",
                            url: "__URL__/financeCalculate",
                            data: data,
                            dataType: "json",
                            success: function (res) {
                                if (res.state == 0) { //0就是错误
                                    $.messager.progress('close');
                                    var url =
                                        '__URL__/resultview/module/finance/getdate/' +
                                        finance_date;
                                    IndexIndexModule.updateOperateTab(url);
                                } else { //state = 1就是success
                                    $.messager.progress('close');
                                    //$('#roomservicebillCreateviewDateInput').datebox('setValue',finance_date);
                                    //$('#roomservicebillCreateviewApInput').val(room_ap);
                                    $(that.datagrid).datagrid('reload');
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


        //导出单个分公司
        singleExport: function () {

        },

        //导出全部分公司的分录
        allExport: function () {

        },


        //根据日期查询显示销售汇总
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            var cdate = $('#yingshouRevparMgrListviewDateInput').datebox('getValue');
            var cap = $('#yingshouRevprMgrListviewApInput').val();

            queryParams['cdate'] = cdate;
            queryParams['cap'] = cap;
            $(this.datagrid).datagrid({
                pageNumber: 1
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
        }

    };

    $(function () {
        YingshouFinanceListviewModule.init();
    })
</script>