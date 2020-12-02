<?php if (!defined('THINK_PATH')) exit();?><style>
    #orderHandleSearchviewAddressHelp {
        height: 18px;
        padding-left: 20px;
        margin-top: 2px;
        font-size: 16px;
    }

    /*定义备注字段大小*/
    #orderHandleSearchviewAddressTb .l-btn-text {
        font-size: 16px;
        color: red;
        margin-top: 2px;
    }

    #otherInfoOrderHandleAddressHelp {
        padding-left: 28px;
        font-size: 16px;
        border-bottom: 1px solid black;
    }

</style>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="updateTab('__URL__/listview');">
            &nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;<span style="background-color: #FF9797;font-size: 16px;"><?php echo ($operName); ?></span></li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="width: 50px;">&nbsp;</li>
        <li><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/addressBtn.jpg" alt=""
                                                                        title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName"
               id="orderHandleSearchviewmenuAddressSearchAClick" onclick="OrderHandleSearchviewAddressModule.searchAddressView();">地址查询<span>^3</span></a></li>
        <li style="width: 20px;">&nbsp;</li>
        <li style="margin-left: 20px;font-size:14px;font-family:'宋体';line-height: 30px;"><?php echo ($searchIntroduce); ?></li>
        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"
                                                        onclick="OrderHandleSearchviewAddressModule.closeTab();">关闭</a>
        </li>
        <li style="float:right;"><a href="javascript:;" onclick="OrderHandleSearchviewAddressModule.closeTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="orderHandleSearchviewAddressDiv" class="easyui-layout" border="false" style="width:100%;">
    <div data-options="region:'center',border:false"
         style="padding: 0px; background: #eee; clear: both;">
        <table id="OrderHandleSearchviewAddressTable" fit="true"></table>
    </div>
    <div data-options="region:'south',split:false,border:false"
         style="height: 46px;">
        <div class="pagestop">
            <div id="otherInfoOrderHandleAddressHelp" style="height: 22px;" align="center">
            </div>
            <div id="orderHandleSearchviewAddressHelp" align="center"></div>
        </div>
    </div>
</div>
<input id="OrderHandleSearchviewAddressAction" type="hidden"  value="" />

<script>

    var OrderHandleSearchviewAddressModule = {
        dialog: '#globel-dialog-div',
        datagrid: $('#OrderHandleSearchviewAddressTable'),   //订单处理表

        focusNumberAddressOH: 0,  //定义光标，OH是OrderHandle的缩写
        sendnameMgr: new Array(), //定义处理的送餐员的数组

        init: function () {
            //设置div的高度
            $('#orderHandleSearchviewAddressDiv').height(IndexIndexModule.operationHeight);

            //设置
            this.setDatagrid();
            //返回送餐员代码
            this.getSendname();
            this.firstGetOrderForm();
            //启动分页事件
            //this.dataPage();
            this.datagridKeyMove();
            //启动快键键
            this.quickKeyboardAction();
        },

        setDatagrid: function () {
            var searchAddress = "<?php echo ($searchAddress); ?>";
            //定义查询操作表格
            this.datagrid.datagrid(
                    {
                        nowrap: false,
                        fitColumns: true,
                        singleSelect: true,
                        autoRowHeight: true,
                        striped: true,
                        border: false,
                        rownumbers: true, //显示行号
                        showFooter: true,
                        pagination: true,
                        pagePosition: 'bottom',
                        method:'post',
                        toolbar: '#orderHandleSearchviewAddressTb',
                        rowStyler: function (index, row) { //处理订单，状态改为已，就改变背景颜色，以便区别
                            var state = row.state;
                            if (state.indexOf('已执行') >= 0) {
                                return 'background-color:#6293BB;color:red;'; // return inline style
                            }
                        },
                        columns: [[
                            {
                                field: 'orderformid',
                                title: 'id',
                                hidden: 'true',
                                width: 100
                            },
                            {
                                field: 'address',
                                title: '地址',
                                width: 150,
                                align: 'left'
                            },
                            {
                                field: 'ordertxt',
                                title: '数量规格',
                                width: 50,
                                align: 'center'
                            },
                            {
                                field: "totalmoney",
                                width: 30,
                                title: '金额'
                            },
                            {
                                field: "telphone",
                                width: 38,
                                title: '电话',
                                align: 'center'
                            },
                            {
                                field: "custtime",
                                width: 25,
                                title: '要餐时间'
                            },
                            {
                                field: "state",
                                width: 20,
                                title: '状态',
                                styler: function (value, row, index) {
                                    if (value.indexOf('改单') >= 0) {
                                        return 'background-color:#ffee00;color:	#02C874;';
                                    }
                                    if (value.indexOf('打印改') >= 0) {
                                        return 'background-color:#ffee00;color:	#02C874;';
                                    }
                                    if (value.indexOf('催送') >= 0) {
                                        return 'background-color:#ffee00;color:#FF0000;';
                                    }
                                    if (value.indexOf('打印催') >= 0) {
                                        return 'background-color:#ffee00;color:#FF0000;';
                                    }
                                    if (value.indexOf('退餐') >= 0) {
                                        return 'background-color:#ffee00;color:#01B468;';
                                    }
                                }
                            },
                            {
                                field: "sendname",
                                width: 20,
                                title: '送餐员'
                            },
                            {
                                field: "operation",
                                width: 54,
                                title: '操作',
                                align: 'center',
                                formatter: function (value, rowData,
                                                     rowIndex) {
                                    var operation;
                                    operation = "<input class='orderHandleSearchviewAddressOperation' id='orderHandleSearchviewAddressOperation"
                                            + rowIndex
                                            + "'  name='orderHandleSearchviewAddressOperation"
                                            + rowIndex
                                            + "' type='text'  size='6' onkeyup='OrderHandleSearchviewAddressModule.orderHandle(event,this," + rowData.orderformid + "," + rowIndex + ")'  onfocus='OrderHandleSearchviewAddressModule.orderHandleFocus(" + rowData.orderformid + ");' >";
                                    operation += "<a href='javascript:void(0);' onclick='OrderHandleSearchviewAddressModule.detailview(" + rowData.orderformid + ','+ rowIndex + ")'" +
                                            " class='orderHandleDetailview'  style='margin-left:4px;'>查看</a>";
                                    operation += "<a href='javascript:void(0);' onclick='OrderHandleSearchviewAddressModule.orderPrintHandle("
                                            + rowData.orderformid
                                            + ","
                                            + rowIndex
                                            + ",2)' class='orderHandleDetailview' style='margin-left:4px;' >打印</a>";
                                    if (rowData.invoicetype == '电子票') {
                                        operation += "<a href='javascript:void(0);' onclick='OrderHandleListviewModule.printEticketQRcode(" + rowData.orderformid + ")'  class='orderHandleDetailview' style='margin-left:4px' >票</a>";
                                    };

                                    return operation;
                                }
                            },
                            {
                                field: "telname",
                                width: 30,
                                title: '接线员',
                                align: 'center'
                            }
                            ]],
                        onSelect : this.selectDataGridRow, //选择行
                        onClickRow : this.clickDataGridRow, //单击行事件
                        onLoadSuccess:this.loadSuccess
                    });

            //定义订单分页表
            this.datagrid.datagrid('getPager').pagination({
                showRefresh: false,
                pageSize :  (IndexIndexModule.gridRowsNumber - 2),
                layout: ['sep','first','prev','manual','links','next','last'],
                buttons: [{
                    id: 'orderHandleSearchviewAddressTb',
                    text: '备注:'
                }]
            });

            //表格的分页事件
            this.datagrid.datagrid('getPager').pagination({
                onSelectPage: function (pageNumber, pageSize) {
                    var data = {'page':pageNumber};
                    $.ajax({
                        type: "POST",
                        url: "__URL__/searchviewAddress" ,
                        dataType: "json",
                        data:data,
                        success: function (data) {
                            //选择第一行焦点
                            $('#OrderHandleSearchviewAddressTable').datagrid('loadData', data);
                            //行选中
                            $('#OrderHandleSearchviewAddressTable').datagrid('selectRow', 0);
                            //显示焦点
                            $('#orderHandleSearchviewAddressOperation0').focus();

                        }
                    })

                }
            });

            $('#orderHandleSearchviewAddressHelp').html('提示：0处理退餐;改单;催单,2订单备注,3转给其他公司,5地址查询,7订单返回,8打印订单,9转给分送点');

        },


        //选择行事件
        selectDataGridRow: function (rowIndex, rowData) {
            if (rowData) {
                //显示备注
                $('#orderHandleSearchviewAddressTb').linkbutton({
                    text: '备注:' + rowData.beizhu
                });
                OrderHandleSearchviewAddressModule.focusNumberAddressOH = rowIndex;
                var orderformOtherInfo = '录入员:' + rowData.telname + ' 录入时间:' + rowData.rectime
                        + ' 催送次数: 催送时间: 更改人: 更改时间:';
                $('#otherInfoOrderHandleAddressHelp').html(' ' + orderformOtherInfo);
            }
        },

        //订单表格行选择事件的处理函数
        clickDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                $('#orderHandleSearchviewAddressOperation' + rowIndex).focus();
            }
        },

        //订单数据再入完毕
        loadSuccess : function(data){
            return false;
            //获取分页参数
            var pageNumber = <?php echo ($pagenumber); ?>;
            var data = {'page':pageNumber};
            //获取行定位
            var rowIndex = <?php echo ($rowIndex); ?>;
            that.datagrid.datagrid('getPager').pagination({'pageNumber':pageNumber});
            //行选中
            that.datagrid.datagrid('selectRow', rowIndex);

        },

        //定义表格移动的键盘处理
        datagridKeyMove: function () {
            var that = this;
            //定义表格移动的键盘处理
            this.datagrid.datagrid('getPanel').panel('panel').attr('tabindex', 1).bind('keydown', function (e) {
                //当前选择的行
                var selectedRowObj = $(that.datagrid).datagrid('getSelected');
                //当前选择行的number
                var selectedRowIndex = $(that.datagrid).datagrid('getRowIndex', selectedRowObj);
                switch (e.keyCode) {
                    case 38: // up  上移动
                        if (that.focusNumberAddressOH == 0) return;  //为0，就是到顶了，不用再移动了
                        that.focusNumberAddressOH = that.focusNumberAddressOH - 1;
                        that.datagrid.datagrid('selectRow', that.focusNumberAddressOH);
                        $('#orderHandleSearchviewAddressOperation' + that.focusNumberAddressOH).focus();
                        break;
                    case 40: // down 下移动
                        var rowsObj = that.datagrid.datagrid('getRows');  //返回当前页的记录
                        var rowLength = rowsObj.length - 1;
                        if (that.focusNumberAddressOH == rowLength) return;  //到表格尾部了，就不用再移动了
                        that.focusNumberAddressOH = that.focusNumberAddressOH + 1;
                        that.datagrid.datagrid('selectRow', that.focusNumberAddressOH);
                        $('#orderHandleSearchviewAddressOperation' + that.focusNumberAddressOH).focus();
                        break;
                    case 13:
                        break;
                }

            });

        },

        //单击处理栏
        orderHandleClick: function (orderformid) {
            //更新焦点订单号
            this.focusOrderformidOH = orderformid;
            //行选中
            this.datagrid.datagrid('selectRow', this.focusNumberOH);
        },

        //处理栏活动焦点，开启从表显示
        orderHandleFocus: function (orderformid) {
            //更新焦点订单号
            this.focusOrderformidOH = orderformid;
        },


        //查看记录
        detailview: function (orderformid,rowIndex) {
            var url = "__URL__/detailview/record/" + orderformid + "/returnAction/searchviewAddress"
                    + '/rowIndex/' + rowIndex+'/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        //返回送餐员信息
        getSendname: function () {
            var that = this;
            //返回所有分公司送餐员的名称代码
            $.ajax({
                type: "POST",
                url: '__URL__/getSendnameMgr',
                dataType: "json",
                success: function (data) {
                    if (!data)  return;
                    that.sendnameMgr = data;
                }
            })

        },

        /**
         * 第一次启动
         */
        firstGetOrderForm: function () {
            //获取分页参数
            var pageNumber = <?php echo ($pagenumber); ?>;
            var data = {'page':pageNumber};
            //获取行定位
            var rowIndex = <?php echo ($rowIndex); ?>;
            var that = this;
            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url:  "<?php echo ($url); ?>",
                    dataType: "json",
                    data:data,
                    success: function (data) {
                        if (data.rows.length > 0) {
                            that.datagrid.datagrid('loadData', data);
                            that.datagrid.datagrid('getPager').pagination({'pageNumber':pageNumber});
                            //行选中
                            that.datagrid.datagrid('selectRow', rowIndex);
                            return false;
                            //选择第一行焦点
                            that.datagrid.datagrid('loadData', data);
                            that.focusNumberAddressOD = rowIndex;  //初始定位

                            //显示焦点
                            $('#orderDistributionAddressTask' + that.focusNumberAddressOD).focus();
                            $(that.datagrid).datagrid('getPager').pagination({'pageNumber':pageNumber});  //页定位
                            //行选中
                            $(that.datagrid).datagrid('selectRow', that.focusNumberAddressOD);

                        }
                    }
                })
            }, 100);
        },

        //处理订单 ,根据送餐员编码
        orderHandle: function (event, obj, orderformid, rowIndex) {
            //定义根据输入值，处理订单
            var inputCode = $(obj).val();
            //输入的键值
            var event = event || window.event;
            var keyCode = event.which ? event.which : event.keyCode;
            if (keyCode == 38) return; //上移动
            if (keyCode == 40) return; //下移动
            if (keyCode == 13) {   //订单处理
                switch (inputCode) {
                    case '0':  //对退餐的处理;对已经处理送餐员的订单的改单或者催单的处理
                        this.cancelchangehurryOrder(orderformid, rowIndex);
                        break;
                    case '2':  //对启动订单备注操作界面
                        this.beizhuOrderView(orderformid,rowIndex);
                        break;
                    case '3': //将订单转给其它分公司，不用再返回
                        this.distributeOrderOtherCompany(orderformid, rowIndex);
                        break;
                    case '4': //单独发送消息
                        this.sendAloneMessagesView(orderformid, rowIndex);
                        break;
                    case '5':  //根据地址查询的快捷键
                        this.searchAddressView(orderformid, rowIndex);
                        break;
                    case '7' :  //返回订单
                        this.backOrder(orderformid, rowIndex);
                        break;
                    case '8' : //订单打印
                        this.orderPrintHandle(orderformid, rowIndex);
                        break;
                    case '9': //将转发给分送点
                        this.distributeOrderSecondPoint(orderformid, rowIndex);
                        break;
                    default:    //对订单处理到送餐员身上
                        if (inputCode.length < 2) break;
                        this.orderHandleToSendname(inputCode, obj, orderformid, rowIndex);
                        break;
                }

            }
            //F8订单打印
            if (keyCode == 119) {
                this.orderPrintHandle(orderformid, rowIndex);
            }

            //预处理显示送餐员的产品规格信息
            if (inputCode.length >= 2) {  //输入的是送餐员的代码，才处理
                this.preproSendnameProduct(inputCode);
            }
        },

        //预处理显示装箱送餐员的产品规格信息
        preproSendnameProduct: function (inputCode) {
            $.ajax({
                type: "GET",
                url: "__URL__/getProperSendnameproductsByCode/code/" + inputCode,
                dataType: "json",
                success: function (returnData) {
                    $('#orderHandleSearchviewAddressHelp').html(returnData.content);
                }

            });
        },

        //对退餐，催单，改单的处理
        cancelchangehurryOrder: function (orderformid, rowIndex) {
            var that = this;
            //如果是退餐，就把订单置为已退餐；催送改为已催送，改单改为已改单
            $.ajax({
                type: "GET",
                url: "__URL__/cancelchangehurryOrderHandle/orderformid/" + orderformid,
                dataType: "json",
                success: function (returnData) {
                    if (returnData['error'] == 'error') {
                        $.messager.show({
                            title: '提示',
                            msg: returnData['msg'],
                            height: 70,
                            timeout: 3000,
                            showType: 'slide',
                            style: {
                                left: 0, right: '', top: '',
                                bottom: -document.body.scrollTop - document.documentElement.scrollTop
                            }
                        });
                        return false;
                    }
                    ;

                    if (returnData['success'] == 'success') {
                        that.datagrid.datagrid('updateRow', {
                            index: rowIndex,    //定位行
                            row: {
                                state: returnData['state']
                            }
                        });
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).val();
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).focus();
                    };
                }

            });
        },


        //订单备注操作
        beizhuOrderView: function (orderformid, rowIndex) {
            var that = this;
            $(that.dialog).dialog({
                title: '订单备注',
                iconCls: 'icons-application-application_add',
                width: 300,
                height: 440,
                cache: false,
                href: "__URL__/beizhuInput/className/OrderHandleSearchviewAddressModule/orderformid/"+orderformid+'/rowIndex/'+rowIndex,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false
            });
        },

        /**
         * 将订单转分配给其他分公司
         */
        distributeOrderOtherCompany: function (orderformid, rowIndex) {
            return false; //
            var that = this;
            $(that.dialog).dialog({
                title: '转其他分公司',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderHandle/distributeOtherCompanyInput');?>",
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
                                var url = '__URL__/setOtherCompany/orderformid/' + orderformid + '/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value+'/';
                                });
                                url = encodeURI(url);
                                //写入数据库
                                $.ajax({
                                    type: "GET",
                                    url: url,
                                    dataType: "json",
                                    success: function (data) {
                                        //更新状态
                                        OrderHandleSearchviewAddressModule.datagrid.datagrid('updateRow', {
                                            index: rowIndex,    //定位行
                                            row: {
                                                state: '转给其他分公司'
                                            }
                                        });
                                    }
                                })
                                $(that.dialog).dialog('close');
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                    }
                }]
            });
        },


        /**
         * 单独发送消息给送餐员
         */
        sendAloneMessagesView: function (orderformid, rowIndex) {
            var that = this;
            $(that.dialog).dialog({
                title: '单独发消息',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 260,
                cache: false,
                href: "<?php echo U('OrderHandle/sendAloneMessagesInput');?>",
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
                                sendname = $('#orderHandleListviewSendAloneMessageInputSendname').val();
                                msgcontent = $('#orderHandleListviewSendAloneMessageInputContent').val();
                                if (sendname == '' || msgcontent == '') {
                                    $.messager.show({
                                        title: '提示',
                                        msg: '输入不能为空！',
                                        showType: 'show',
                                        style: {
                                            left: 0,
                                            right: '',
                                            top: '',
                                            bottom: -document.body.scrollTop - document.documentElement.scrollTop
                                        }
                                    });
                                    return false;
                                }

                                var url = '__URL__/setAloneMessages/sendname/' + sendname + '/msgcontent/' + msgcontent;
                                url = encodeURI(url);
                                //写入数据库
                                $.ajax({
                                    type: "GET",
                                    url: url,
                                    dataType: "json",
                                    success: function (data) {
                                        $.messager.show({
                                            title: '提示',
                                            msg: data.msg,
                                            showType: 'show',
                                            style: {
                                                left: 0,
                                                right: '',
                                                top: '',
                                                bottom: -document.body.scrollTop - document.documentElement.scrollTop
                                            }
                                        });
                                    }
                                })
                                $(that.dialog).dialog('close');
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                    }
                }]
            });
        },

        /**
         * 送餐地址查询
         */
        searchAddressView: function (rowIndex) {
            var that = this;
            $(that.dialog).dialog({
                title: '送餐地址查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderHandle/searchAddressInput');?>",
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
                                    url = url + value.name + '/' + value.value+'/';
                                })
                                url = encodeURI(url);
                                IndexIndexModule.updateOperateTab(url);
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
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                        $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                    }
                }]
            });
        },

        /**
         * 返回订单
         */
        backOrder: function (orderformid, rowIndex) {
            var that = this;
            $.messager.confirm('确认', '是否真的需要返回订单?', function (r) {
                if (r) {
                    $.ajax({
                        type: "GET",
                        url: "__URL__/backOrderHandle/orderformid/" + orderformid,
                        dataType: "json",
                        success: function (returnData) {
                            if (returnData['error'] == 'error') {
                                $.messager.show({
                                    title: '提示',
                                    msg: returnData['msg'],
                                    height: 70,
                                    timeout: 5000,
                                    showType: 'slide',
                                    style: {
                                        left: 0,
                                        right: '',
                                        top: '',
                                        bottom: -document.body.scrollTop - document.documentElement.scrollTop
                                    }
                                });
                                return false;
                            }

                            if (returnData['success'] == 'success') {
                                OrderHandleSearchviewAddressModule.datagrid.datagrid('updateRow', {
                                    index: rowIndex,    //定位行
                                    row: {
                                        state: '返回'
                                    }
                                });
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                                $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                            }
                        }

                    });
                } else {
                    $('#orderHandleSearchviewAddressOperation' + rowIndex).val(''); //输入框恢复空
                    $('#orderHandleSearchviewAddressOperation' + rowIndex).focus(); //输入框恢复空
                }
            });
        },

        /**
         * 订单打印
         */
        orderPrintHandle: function (orderformid, rowIndex,accounttype) {
            //orderPrintData的程序在general.js中
            orderPrintData(orderformid, rowIndex,accounttype);
            //更新打印状态
            this.datagrid.datagrid('updateRow', {
                index: rowIndex,    //定位行
                row: {
                    state: '已打印'
                }
            });
            rowIndex = rowIndex + 1;
            $('#orderHandleSearchviewAddressOperation' + rowIndex).focus();  //下移动一行

            //写入备餐数据
            $.ajax({
                type: "POST",
                url: '__URL__/writeProductsPrepare/orderformid/'+orderformid,
                dataType: "json",
                success: function (data) {
                }
            });

            //电子发票的处理  *********************
            $.ajax({
                type: "GET",
                url: "__URL__/getInvoiceInfo/orderformid/" + orderformid,
                dataType: "json",
                success: function (data) {
                    if(data){  //判断是否有电子发票
                        //打印电子发票
                        var invoiceOperState = printEticketInvoice(data);  //打印电子发票
                    }
                }
            });

        },

        /**
         * 将订单转给分送店
         */
        distributeOrderSecondPoint: function (orderformid, rowIndex) {
            var that = this;
            $(that.dialog).dialog({
                title: '转分送点',
                iconCls: 'icons-application-application_add',
                width: 200,
                height: 440,
                cache: false,
                href: "__URL__/distributeOrderSecondPointInput/className/OrderHandleSearchviewAddressModule/orderformid/"+orderformid+'/rowIndex/'+rowIndex,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false
            });
        },

        /**
         * 将订单处理给送餐员
         */
        orderHandleToSendname: function (inputCode, obj, orderformid, rowIndex) {
            var selected = this.datagrid.datagrid('getSelected');
            if (selected) {
                var rowLength = this.datagrid.datagrid('getRows').length;
            }
            var findSendname = false;
            var that = this;
            $.each(that.sendnameMgr, function (key, value) {
                if (value.code == inputCode) { //送餐员代码相等
                    //更新订单状态和送餐员名字
                    that.datagrid.datagrid('updateRow', {
                        index: rowIndex,    //定位行
                        row: {
                            state: '已处理',
                            sendname: value.name  //送餐员
                        }
                    });
                    $('#orderHandleSearchviewAddressOperation' + rowIndex).val();
                    if(rowIndex == rowLength){

                    }else{
                        rowIndex = rowIndex + 1;
                    }
                    $('#orderHandleSearchviewAddressOperation' + rowIndex).focus();  //下移动一行
                    that.datagrid.datagrid('selectRow', rowIndex);
                    findSendname = true;    //标志已经处理
                    //上传处理结果
                    $.ajax({
                        type: "GET",
                        url: "__URL__/orderHandleByCode/orderformid/" + orderformid + "/code/" + inputCode,
                        dataType: "json",
                        success: function (returnData) {
                        }
                    });
                    return false;
                }
            })
            //输入送餐员代码不对，提示
            if (findSendname == false) {
                $.messager.show({
                    title: '提示',
                    msg: '输入代码输入有误!',
                    height: 70,
                    timeout: 5000,
                    showType: 'slide',
                    style: {
                        left: 0, right: '', top: '',
                        bottom: -document.body.scrollTop - document.documentElement.scrollTop
                    }
                });
                return false;
            }
        },


        /**
         * 页面快键键设置
         */
        quickKeyboardAction : function () {
            var that = this;
            // ctrl+3快捷键 f3是不能用的
            Mousetrap.bind(['ctrl+3', 'ctrl+f3','f3'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '配送地址查询') {
                    that.searchAddressView();
                }
            });

            // ESC键
            Mousetrap.bind('esc', function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '配送地址查询') {
                   if( $(that.dialog).parent().is(":hidden")  == true){ //隐藏
                       // 更新一个选项卡面板
                       $('#operation').tabs('select','订单配送');
                       $('#orderHandle' +  OrderHandleListviewModule.focusNumberOH).focus(); //输入框焦点
                   }else{  //对话框打开
                       $(that.dialog).dialog('close');
                       $('#orderHandleSearchviewAddressOperation' + OrderHandleSearchviewAddressModule.focusNumberOH).val('');
                       $('#orderHandleSearchviewAddressOperation' + OrderHandleSearchviewAddressModule.focusNumberOH).focus();
                   }
                }
            });
        },

        /**
         * 关闭页面
         */
        closeTab: function () {
            var tab = $('#operation').tabs('getSelected');
            //返回选项卡的index
            var index = $('#operation').tabs('getTabIndex', tab);
            //关闭选项卡
            $('#operation').tabs('close', index);
        }

    }


    $(function () {
        OrderHandleSearchviewAddressModule.init();
        setTimeout(function () {
            //要执行的代码
            $('#OrderHandleSearchviewAddressTable').datagrid('selectRow', 0);//初始选择第一行
            $('#orderHandleSearchviewAddressOperation0').focus();
        }, 500);

    })


</script>