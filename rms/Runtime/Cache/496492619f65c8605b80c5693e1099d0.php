<?php if (!defined('THINK_PATH')) exit();?>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="IndexIndexModule.openOperateTab('__URL__/createview/returnAction/<?php echo ($returnAction); ?>');"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="IndexIndexModule.updateOperateTab('__URL__/createview/returnAction/<?php echo ($returnAction); ?>');">新建预订单<span>^1</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="BookOrderListviewModule.addressSearchInput();"><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="BookOrderListviewModule.addressSearchInput();">地址查询<span>^6</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="BookOrderListviewModule.telphoneSearchInput();"><img
                src=".__PUBLIC__/Images/phone.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="BookOrderListviewModule.telphoneSearchInput();">电话号码查询<span>^7</span></a>
        </li>

        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="BookOrderListviewModule.otherSearchInput();">预订日期查询<span>⌃9</span></a>
        </li>

        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/newBtn.gif" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="BookOrderListviewModule.importOrder();">导入订单<span></span></a>
        </li>

        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="OrderBookListviewDiv" style="height:400px;width:100%;clear:both;">
    <table id="bookorder_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="BookOrderAction" type="hidden"  value="Listview" />

<script type="text/javascript">

    var BookOrderListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#bookorder_index_datagrid',

        init: function () {
            //设置div的高度
            $('#OrderBookListviewDiv').height(IndexIndexModule.operationHeight);
            var action_name = '<?php echo ($action_name); ?>';
            if(action_name  == 'searchviewaddress'){
                this.datagrid = '#bookorder_searchviewaddress_datagrid';
            }
            if(action_name  == 'searchviewtelphone'){
                this.datagrid = '#bookorder_searchviewtelphone_datagrid';
            }
            if(action_name  == 'searchviewother'){
                this.datagrid = '#bookorder_searchviewother_datagrid';
            }
            this.quickKeyboardAction();
        },

        //重新设置page
        setPagination:function(){
            //定义订单分页表
            var pager = $(this.datagrid).datagrid().datagrid('getPager');
            pager.pagination({
                showRefresh: false,
                pageSize : IndexIndexModule.gridRowsNumber,
                layout: ['sep','first', 'links', 'last']
            });
        },


        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="BookOrderListviewModule.detailview(' + rowData.bookorderid + ')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="BookOrderListviewModule.editview(' + rowData.bookorderid + ')">编辑</a>');
            btn.push('<a href="javascript:void(0);" onclick="BookOrderListviewModule.deleteRecord(' + rowData.bookorderid + ')">删除</a>');
            return btn.join(' | ');
        },

        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id) {
            var url = "__URL__/detailview/record/"+id+"";
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑用户记录
        editview: function (id) {
            var url = "__URL__/editview/record/"+id;
            IndexIndexModule.updateOperateTab(url);
        },

        //删除数据
        deleteRecord: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post("<?php echo U('BookOrder/delete');?>", {record: id}, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    }
                    ;
                }, 'json');
            });
        },

        //导入预订数据到orderform
        importOrder : function () {
            var that = this;
            $.messager.confirm('提示信息', '确定要导入吗？', function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.post("<?php echo U('BookOrder/importOrder');?>", {}, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    }
                    ;
                }, 'json');
            });
        },

        //订单地址输入
        addressSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐地址查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('BookOrder/searchAddressInput');?>",
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
                                IndexIndexModule.openOperateTab(url, '订单预订');
                                $(that.dialog).dialog('close');
                                return false;
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

        //电话查询
        telphoneSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐电话查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('BookOrder/searchTelphoneInput');?>",
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
                                var url = '__URL__/searchviewTelphone/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订单预订');
                                $(that.dialog).dialog('close');
                                return false;
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

        /**
         * 其他查询：综合查询，查询多个字段,或者叫普通查询
         */
        otherSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐综合查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('BookOrder/searchOtherInput');?>",
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
                                var url = '__URL__/searchviewOther/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订单预订');
                                $(that.dialog).dialog('close');
                                return false;
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

        //新建的快捷操作
        quickKeyboardAction:function(){
            var that = this;
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1','ctrl+f1','f1'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单预订' && ($('#BookOrderAction').val() == 'Listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+6快捷键, 地址查询
            Mousetrap.bind(['ctrl+6','ctrl+f6','f6'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单预订' && ($('#BookOrderAction').val() == 'Listview')) {
                    that.addressSearchInput();
                };
            });

            // ctrl+7快捷键,电话查询
            Mousetrap.bind(['ctrl+7', 'ctrl+f7', 'f7'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单预订' && ($('#BookOrderAction').val() == 'Listview')) {
                    that.telphoneSearchInput();
                };
            });

            // ctrl+8快捷键,预订日期查询
            Mousetrap.bind(['ctrl+8', 'ctrl+f8', 'f8'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单预订' && ($('#BookOrderAction').val() == 'Listview')) {
                    that.otherSearchInput();
                };
            });

            // ESC键
            Mousetrap.bind('esc', function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单预订') {
                    $(IndexIndexModule.dialog).dialog('close');
                }
            });
        }

    };

    $(function(){
        BookOrderListviewModule.init();
        setTimeout(function(){
            BookOrderListviewModule.setPagination();
        },100);

    })
</script>