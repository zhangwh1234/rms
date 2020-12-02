<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;订单地址查询</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.addressSearchInput();"><img src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title=""
                    border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="OrderFormListviewModule.addressSearchInput();">地址查询<span>^6</span></a></li>


        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div class="moduleOperatert" style="height:300px;width:100%;clear:both;">
    <table id="orderform_searchviewaddress_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="OrderFormSearchviewAddressAction" type="hidden" value="" />
<script>
    var OrderFormSearchviewAddressModule = {
        dialog: '#globel-dialog-div',

        //初始化
        init: function () {
            $('.moduleOperatert').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //重新设置page
        setPagination: function () {
            //定义订单分页表
            var pager = $('#orderform_searchviewaddress_datagrid').datagrid().datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep', 'first', 'prev', 'manual', 'links', 'next', 'last'],
                buttons: [{
                    id: 'orderformOtherMsg',
                    text: '<?php echo ($orderformOtherMsg); ?>'

                }]
            });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            if (window.screen.availWidth < 1280) {
                var btn = [];
                btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.detailview(' + rowData.orderformid + ',' + rowIndex + ')">查看&nbsp;</a>');
                if (rowData['rolename'] == 'dispatcher') {

                } else {
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.editview(' + rowData.orderformid + ',' + rowIndex + ')">改单&nbsp;</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.hurry(' + rowData.orderformid + ',' + rowIndex + ')">催送&nbsp;</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.duplicateview(' + rowData.orderformid + ',' + rowIndex + ')">复制&nbsp;</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.returnorderformview(' + rowData.orderformid + ',' + rowIndex + ')">退餐</a>');
                }
                return btn.join('');
            } else {
                var btn = [];
                btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.detailview(' + rowData.orderformid + ',' + rowIndex + ')">查看</a>');
                if (rowData['rolename'] == 'dispatcher') {

                } else {
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.editview(' + rowData.orderformid + ',' + rowIndex + ')">改单</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.hurry(' + rowData.orderformid + ',' + rowIndex + ')">催送</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.duplicateview(' + rowData.orderformid + ',' + rowIndex + ')">复制</a>');
                    btn.push('<a href="javascript:void(0);" onclick="OrderFormSearchviewAddressModule.returnorderformview(' + rowData.orderformid + ',' + rowIndex + ')">退餐</a>');
                }
                return btn.join(' | ');
            }
        },

        //送餐员的格式化操作
        sendname: function (val, rowData, rowIndex) {
            if ((rowData.longitude) && (rowData.latitude) && (!rowData.sendname)) {
                var btn = [];
                btn.push(val +
                    '<a href="javascript:void(0);" onclick="OrderFormListviewModule.mapshowview(' + rowData.orderformid + ',' + rowIndex +
                    ')" ><img src=".__PUBLIC__/Images/lhkc/location.png" style="height: 20px;" /></a>');
                return btn.join('');
            } else if ((rowData.sendlongitude) && (rowData.sendlatitude) && (rowData.sendname)) {
                var btn = [];
                btn.push(val +
                    '<a href="javascript:void(0);" onclick="OrderFormListviewModule.sendmapshowview(' + rowData.orderformid + ',' + rowIndex +
                    ')" ><img src=".__PUBLIC__/Images/lhkc/sendlocation.png" style="height: 20px;" /></a>');
                return btn.join('');
            } else {
                var btn = [];
                btn.push(val);
                return btn.join(' | ');
            }
        },

        //初始返回,定位行操作,但是在翻页是,就不操作
        setRowSelect: function () {
            $('#orderform_searchviewaddress_datagrid').datagrid('selectRow', <?php echo ($rowIndex); ?>);
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
                href: "<?php echo U('OrderForm/searchAddressInput');?>",
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
                                IndexIndexModule.openOperateTab(url, '订餐地址查询');
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

        //查看订单
        detailview: function (orderformid, rowIndex) {
            url = '__URL__/detailview/record/' + orderformid + '/returnAction/<?php echo ($returnAction); ?>' +
                '/rowIndex/' + rowIndex + '/pagetype/searchviewaddress';
            IndexIndexModule.updateOperateTab(url);
        },

        //改单
        editview: function (orderformid, rowIndex) {
            url = '__URL__/editview/record/' + orderformid + '/returnAction/<?php echo ($returnAction); ?>' +
                '/rowIndex/' + rowIndex + '/pagetype/searchviewaddress';
            IndexIndexModule.updateOperateTab(url);
        },

        //催送订单
        hurry: function (orderformid, rowIndex) {
            var url = '__URL__/hurry/record/' + orderformid + '/returnAction/<?php echo ($returnAction); ?>' +
                '/rowIndex/' + rowIndex + '/pagetype/searchviewaddress';
            var that = this;
            $.messager.confirm('提示信息', '确定要催送订单吗？', function (result) {
                if (!result) return false;

                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post(url, {}, function (res) {
                    $.messager.progress('close');
                    $('#orderform_searchviewaddress_datagrid').datagrid('reload');
                    $.app.method.tip('提示信息', res.info, 'info');
                    setTimeout(function () {
                        $('#orderform_searchviewaddress_datagrid').datagrid('selectRow', rowIndex); //显示行定位
                    }, 200)
                }, 'json');
            });

        },

        //复制订单
        duplicateview: function (orderformid, rowIndex) {
            url = '__URL__/duplicateview/record/' + orderformid + '/returnAction/<?php echo ($returnAction); ?>' +
                '/rowIndex/' + rowIndex + '/pagetype/searchviewaddress';
            IndexIndexModule.updateOperateTab(url);
        },

        //退餐
        returnorderformview: function (orderformid, rowIndex) {
            url = '__URL__/returnorderformview/record/' + orderformid + '/returnAction/<?php echo ($returnAction); ?>' +
                '/rowIndex/' + rowIndex + '/pagetype/searchviewaddress';
            IndexIndexModule.updateOperateTab(url);
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            var that = this;

            // ctrl+6快捷键, 地址查询
            Mousetrap.bind(['ctrl+6', 'ctrl+f6', 'f6'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐地址查询') {
                    that.addressSearchInput();
                };
            });


            // ESC键
            Mousetrap.bind('esc', function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐地址查询') {
                    if ($(that.dialog).parent().is(":hidden") == true) { //隐藏
                        // 更新一个选项卡面板
                        $('#operation').tabs('select', '订餐单');
                    } else { //对话框打开
                        $(IndexIndexModule.dialog).dialog('close');
                    }
                }
            });
        }

    }

    $(function () {
        OrderFormSearchviewAddressModule.init();
        setTimeout(function () {
            OrderFormSearchviewAddressModule.setPagination();
        }, 100);

        setTimeout(function () {
            OrderFormSearchviewAddressModule.setRowSelect(); //显示行定位
        }, 600)
    })
</script>