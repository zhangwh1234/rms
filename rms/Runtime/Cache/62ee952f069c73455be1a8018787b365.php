<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;送餐订单交账</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="width: 20px;">&nbsp;</li>
        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/batchpay.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="YingshouRoomServiceCheckOrderModule.batchPayment('<?php echo ($sendname); ?>','<?php echo ($custdate); ?>','<?php echo ($custap); ?>');">批量交账</a><span></span></li>
        <li style="width: 20px;">&nbsp;</li>

        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/allpay.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="YingshouRoomServiceCheckOrderModule.payable('<?php echo ($sendname); ?>','<?php echo ($custdate); ?>','<?php echo ($custap); ?>');">应交账款</a><span></span></li>

        <li style="width: 20px;">&nbsp;</li>
        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/batchpay.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="YingshouRoomServiceCheckOrderModule.detailPayment('<?php echo ($sendname); ?>','<?php echo ($custdate); ?>','<?php echo ($custap); ?>');">支付详情</a><span></span></li>
        <li style="width: 20px;">&nbsp;</li>

        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>/getDate/<?php echo ($custdate); ?>/getAp/<?php echo ($custap); ?>');">返回列表</a><span>^4</span></li>


        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div class="moduleOperatert" style="height:300px;width:100%;clear:both;">
    <table id="yingshouroomservice_checkorder_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="YingshouRoomServiceCheckOrderAction" type="hidden" value="" />
<script>
    var YingshouRoomServiceCheckOrderModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouroomservice_checkorder_datagrid',

        //初始化
        init: function () {
            $('.moduleOperatert').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            if (rowData.isjiezhang == 1) {
                btn.push('<label>已结账</label>');
                btn.push(
                    '<a href="javascript:void(0);" style="color:blue;" onclick="YingshouRoomServiceCheckOrderModule.detail(' +
                    "'" + rowData.ordersn + "'" + ',' + rowIndex + ',\'' + rowData.custdate + '\',\'' +
                    rowData.ap +
                    '\')">查看</a>');
            } else {
                if (rowData.totalmoney != rowData.jiezhangmoney) {
                    btn.push(
                        '<a href="javascript:void(0);" style="color:red;" onclick="YingshouRoomServiceCheckOrderModule.payment(' +
                        "'" + rowData.ordersn + "'" + ',' + rowIndex + ',\'' + rowData.custdate + '\',\'' +
                        rowData.ap +
                        '\')">结账</a>');
                } else {
                    btn.push(
                        '<a href="javascript:void(0);" onclick="YingshouRoomServiceCheckOrderModule.payment(' +
                        "'" + rowData.ordersn + "'" + ',' + rowIndex + ',\'' + rowData.custdate + '\',\'' +
                        rowData.ap +
                        '\')">结账</a>');
                }
            }
            return btn.join(' | ');
        },

        //编辑支付功能
        payment: function (id, rowIndex, date, ap) {
            var url = '__URL__/paymentEditview/returnAction/<?php echo ($returnAction); ?>/record/' + id +
                '/rowIndex/' + rowIndex + '/pagetype/listview/room_date/' + date + '/room_ap/' + ap;
            IndexIndexModule.updateOperateTab(url);
        },

        //查看结账结果
        detail: function (id, rowIndex, date, ap) {
            var url = '__URL__/payment_detailview/returnAction/<?php echo ($returnAction); ?>/record/' + id +
                '/rowIndex/' + rowIndex + '/pagetype/listview/room_date/' + date + '/room_ap/' + ap;
            IndexIndexModule.updateOperateTab(url);
        },

        //批量编辑支付功能
        batchPayment: function (sendname, date, ap) {
            var url = '__URL__/batchPaymentEditview/sendname/' + sendname + '/room_date/' + date + '/room_ap/' + ap;
            IndexIndexModule.updateOperateTab(url);
        },

        //显示送餐员的结账详情
        detailPayment: function (sendname, date, ap) {
            var url = '__URL__/paymentdetailview/sendname/' + sendname + '/room_date/' + date + '/room_ap/' + ap;
            IndexIndexModule.updateOperateTab(url);
        },

        //应交账款
        payable: function (sendname, date, ap) {
            url = '__URL__/payable/sendname/' + sendname + '/room_date/' + date + '/room_ap/' + ap;

            $('#globel-dialog-div').dialog({
                title: '显示应交账款',
                iconCls: 'icons-application-application_add',
                width: 400,
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
                        $('#globel-dialog-div').dialog('close');
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
        YingshouRoomServiceCheckOrderModule.init();

        $('#yingshouroomservice_checkorder_datagrid').datagrid({
            view: detailview,
            detailFormatter: function (index, row) {
                return '<div style="padding:2px;position:relative;"><table class="ddv"></table></div>';
            },
            onExpandRow: function (index, row) {
                var ddv = $('#yingshouroomservice_checkorder_datagrid').datagrid('getRowDetail',
                    index).find('table.ddv');
                url = "__URL__/checkOrderGetFinance/ordersn/" + row.ordersn +
                    "/room_date/" + row.custdate + "/room_ap/" + row.ap;
                ddv.datagrid({
                    url: url,
                    fitColumns: true,
                    singleSelect: true,
                    rownumbers: true,
                    loadMsg: '',
                    height: 'auto',
                    columns: [
                        [{
                                field: 'id',
                                title: 'Order ID',
                                width: 200,
                                hidden: 'true',
                            },
                            {
                                field: 'code',
                                title: '编码',
                                width: 100,
                                align: 'center'
                            },
                            {
                                field: 'name',
                                title: '名称',
                                width: 100,
                                align: 'center'
                            },
                            {
                                field: 'money',
                                title: '金额',
                                width: 100,
                                align: 'center'
                            }
                        ]
                    ],
                    onResize: function () {
                        $('#yingshouroomservice_checkorder_datagrid').datagrid(
                            'fixDetailRowHeight', index);
                    }
                });
                $('#yingshouroomservice_checkorder_datagrid').datagrid('fixDetailRowHeight', index);
            }
        });
    });
</script>