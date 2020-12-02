<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">

    /*定义备注字段大小*/
    #beizhuOrderHandle .l-btn-text {
        font-size: 16px;
        color: red;
        margin-top: 2px;
    }

    #otherInfoOrderHandle {
        padding-left: 28px;
        font-size: 16px;
        border-bottom: 1px solid black;
    }

    #orderHandleMonit {
        height: 18px;
        padding-left: 20px;
        margin-top: 2px;
        font-size: 16px;
    }

    /*显示总金额*/
    #totalMoney {
        font-size: 14px;
        margin-top: 4px;
    }

</style>

<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" class="moduleName" onclick="updateTab('__URL__/listview');">
            &nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>
        <li><a href="javascript:;" onMouseOver=""><img src=".__PUBLIC__/Images/addressBtn.jpg" alt=""
                                                       title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName"
               onclick="OrderHandleListviewModule.searchAddressView(0);">地址查询<span>^3</span></a></li>
        <li style="margin-left: 5px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/sendnameBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName"
               onclick="OrderHandleListviewModule.searchSendnameView(0);">送餐员查询<span>^6</span></a></li>
        <li style="margin-left: 5px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="OrderHandleListviewModule.searchOtherView();">综合查询<span>^7</span></a>
        </li>
        <li style="margin-left: 5px;"><a href="javascript:;"
                                          onclick="OrderHandleListviewModule.setupPrint();"><img
                src=".__PUBLIC__/Images/printerSetBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="OrderHandleListviewModule.setPrinter();"
               id="sayPrinterSet">打印机设置</a></li>
        <li><a href="javascript:void(0);" id="sayPrinterName"
               style="font-size: 10px;line-height: 30px;color: black;"><?php echo ($rmsPrinterName); ?></a></li>
        <li style="margin-left: 5px;"><a href="javascript:;"
                                          onclick="OrderHandleListviewModule.setupPrintPage();"><img
                src=".__PUBLIC__/Images/printPage.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="OrderHandleListviewModule.setPrintPage();"
               id="sayPageSet">打印纸张设置</a></li>
        <li><a href="javascript:void(0);" id="sayPageName" style="font-size: 10px;line-height: 30px;color: black;"><?php echo ($rmsPrintPageName); ?></a>
        </li>
        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
            src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
           onclick="OrderHandleListviewModule.orderPrintView();">打印派单<span>^8</span></a>
        </li>
        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName"
                                                        onclick="OrderHandleListviewModule.closeTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="OrderHandleListviewModule.closeTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div id="orderHandleListviewDiv" class="easyui-layout" style="width:100%;height:400px;">
    <div data-options="region:'west',split:true,border:true"
         style="width: 100px;">
        <div id="goods" class="easyui-layout" fit="true">
            <div
                    style="padding: 0px; background: #eee;height:100%;width: 89px;">
                <div style="background: #E0E0E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;line-height:25px;float:left;margin-bottom: 1px;">
                    <span>总订单:</span><span id="ordertotal" style="font-size:16px;">200</span>
                </div>

                <div onclick="alert('ok');"
                     style="background: #E0E0E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>未派单:</span><span id="ordernopai" style="font-size:16px;">200</span>
                </div>

                <div style="background: #E0E0E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>已完成:</span><span id="ordercomplete" style="font-size:16px;">200</span>
                </div>

                <div style="background:	#E0E0E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>未完成:</span><span id="ordernocomplete" style="font-size:16px;">200</span>
                </div>

                <div style="background: #FFC1E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>10分钟:</span><span id="orderten" style="font-size:16px;">200</span>
                </div>

                <div style="background: #FFC1E0;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>20分钟:</span><span id="ordertwenty" style="font-size:16px;">200</span>
                </div>

                <div style="background: #FF9797;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>30分钟:</span><span id="orderthirty" style="font-size:16px;">200</span>
                </div>

                <div style="background: #FF9797;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>40分钟:</span><span id="orderforty" style="font-size:16px;">200</span>
                </div>

                <div style="background: #ff7575;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>50分钟:</span><span id="orderfifty" style="font-size:16px;">200</span>
                </div>

                <div style="background: #ff7575;font-size: 14px;border: 1px solid #C4E1FF;width: 100%;height: 25px;float:left;margin-bottom: 1px;">
                    <span>60分钟:</span><span id="ordersixty" style="font-size:16px;">200</span>
                </div>
            </div>
            <div data-options="region:'south',split:true,border:false"
                 style="height: 30px;">
                <div id="totalMoney">总金额</div>
            </div>
        </div>
    </div>
    <div data-options="region:'center',border:false"
         style="padding: 0px; background: #eee;">
        <table id="OrderFormHandleTable" fit="true"></table>
    </div>
    <div data-options="region:'south',split:false,border:false"
         style="height: 46px;">
        <div class="pagestop">
            <div id="otherInfoOrderHandle" style="height: 22px;" align="center">
                接线员:
            </div>
            <div id="orderHandleMonit" align="center"></div>
        </div>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview"/>

<script>

    var OrderHandleListviewModule = {
        dialog: '#globel-dialog-div',
        orderFormHandleGrid: '',   //订单处理表
        orderProductsHandleGrid: '',   //订货表

        focusNumberOH: 0,  //定义光标，OH是OrderHandle的缩写
        focusOrderformidOH: 0,  //定义光标订单号
        refreshOrder: true,  //定义刷新标志，默认是开启刷新
        sendnameMgr: new Array(), //定义处理的送餐员的数组
        //定义订单的其他信息缓存变量
        orderformOtherInfo: '',
        //定义订单情况的缓存变量
        monitInfo: '',
        //定义产品的缓存数组
        orderProductsArray: new Array(),
        //建立快速刷新和慢速刷新
        firstOrderNumber: 0,  //刷新后得到的订单数量
        taskOrderNumber: 0,   //处理后的订单数量
        fastKeyHelp: '',

        //定义定时任务名称
        firstGetOrderInterval: 0,
        slowGetOrderInterval: 0,
        orderMonitInterval: 0,
        orderHandleData: '',   //定义缓存处理的变量


        init: function () {
            this.orderFormHandleGrid = $('#OrderFormHandleTable');
            this.orderProductsHandleGrid = $('#OrderProductsHandleTable');
            //设置div的高度
            $('#orderHandleListviewDiv').height(IndexIndexModule.operationHeight);
            //订单处理表格
            this.setDatagrid();

            //初始化订单数据
            this.firstGetOrderform();
            //载入快捷键
            this.quickKeyboardAction();
            //键盘的移动
            this.datagridKeyMove();
            //返回送餐员代码
            this.returnSendname();
            //启动分页事件
            this.dataPage();
            //启动刷新订单进程
            this.quickRefreshOrderForm();
            //显示订单情况
            this.refreshOrderHandleMonit();

            //显示完成情况
            this.orderCompleteCondition();
            this.refreshOrderCompleteMonit();
        },

        //设置订单处理表格
        setDatagrid: function () {
            /*定义订单Orderform处理表*/
            this.orderFormHandleGrid.datagrid({
                nowrap: false,
                fitColumns: true,
                singleSelect: true,
                autoRowHeight: true,
                striped: true,
                border: true,
                rownumbers: true,  //显示行号
                showFooter: true,
                pagination: true,
                pagePosition: 'bottom',
                toolbar: '#tb',
                rowStyler: function (index, row) { //处理订单，状态改为已，就改变背景颜色，以便区别
                    state = row.state;
                    if (state.indexOf('已') >= 0) {
                        return 'background-color:#6293BB;color:red;'; // return inline style
                    }
                },
                columns: [[
                    {field: 'orderformid', title: 'id', hidden: 'true', width: 100},
                    {field: 'address', title: '地址', width: 110, align: 'left'},
                    {field: 'ordertxt', title: '数量规格', width: 60, align: 'center'},
                    {
                        field: "state", width: 15, title: '状态',
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
                    {field: "sendname", width: 20, title: '送餐员'},
                    {
                        field: "operation",
                        width: 40,
                        title: '操作',
                        align: 'center',
                        formatter: function (value, rowData, rowIndex) {
                            var operation;
                            operation = "<input class='orderHandleCls' id='orderHandle" + rowIndex + "'  name='orderHandle" + rowIndex + "' type='text'  size='6' onkeyup='OrderHandleListviewModule.orderHandle(event,this," + rowData.orderformid + "," + rowIndex + ")'  " +
                                    "   onclick='OrderHandleListviewModule.orderHandleClick(" + rowIndex + "," + rowData.orderformid + ");' " +
                                    "   onfocus='OrderHandleListviewModule.orderHandleFocus(" + rowIndex + "," + rowData.orderformid + ");' >";
                            operation += "<a href='javascript:void(0);' onclick='OrderHandleListviewModule.detailview(" + rowData.orderformid + ',' + rowIndex + ");' class='orderHandleDetailview'  style='margin-left:4px;'>查看</a>";
                            operation += "<a href='javascript:void(0);' onclick='OrderHandleListviewModule.orderPrintHandle(" + rowData.orderformid + "," + rowIndex + ",2)' class='orderHandleDetailview' style='margin-left:4px;' >印</a>";
                            if (rowData.invoicetype == '电子票') {
                                operation += "<a href='javascript:void(0);' onclick='OrderHandleListviewModule.printEticketQRcode(" + rowData.orderformid + ")'  class='orderHandleDetailview' style='margin-left:4px' >票</a>";
                            };
                            if (rowData.invoicetype == '普通票') {
                                operation += "<a href='javascript:void(0);' onclick='IndexIndexModule.openOperateTab(&apos;__APP__/InvoiceMgr/othereditview/record/" + rowData.ordersn + "/returnAction/listview&apos;,&apos;发票管理&apos;)' class='orderHandleDetailview' style='margin-left:4px' >普票</a>";
                            };
                            return operation;
                        }
                    },
                    {field: "totalmoney", width: 30, title: '金额'},
                    {field: "telphone", width: 35, title: '电话', align: 'center'},
                    {field: "custtime", width: 25, title: '要餐时间'}

                ]],
                onClickRow: this.clickDataGridRow,   //选择行事件
                onSelect: this.selectDataGridRow      //选择行事件
            });

            /*定义订单分页表*/
            var pager = $('#OrderFormHandleTable').datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: (IndexIndexModule.gridRowsNumber - 2),
                layout: ['sep', 'first', 'prev', 'manual', 'links', 'next', 'last'],
                buttons: [{
                    id: 'beizhuOrderHandle',
                    text: '备注:'

                }]
            });

            /*定义订货OrderGoods显示表*/
            this.orderProductsHandleGrid.datagrid({
                nowrap: false,
                columns: [[
                    {field: 'number', title: '数量', width: 30},
                    {field: 'name', title: '名称', width: 50}
                ]]
            });

            /*快捷代码帮助*/
            fastKeyHelp = '提示：0处理退餐;改单;催单,2订单备注,3转给其他公司,4单发消息,5地址查询,6送餐员查询,7订单返回,8打印订单,9转给分送点';
            /*显示快捷代码帮助*/
            $('#orderHandleMonit').html(fastKeyHelp);
        },

        //单击表格的处理函数
        clickDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                //缓存光标位置
                OrderHandleListviewModule.focusNumberOH = rowIndex;
                OrderHandleListviewModule.focusOrderformidOH = rowData.orderformid;
                OrderHandleListviewModule.orderProductsShow(OrderHandleListviewModule.focusOrderformidOH);
                $('#orderHandle' + rowIndex).focus();
            }
        },

        //选择行事件
        selectDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                //显示备注
                $('#beizhuOrderHandle').linkbutton({text: '备注:' + rowData.beizhu});
                var orderformOtherInfo = '录入员:' + rowData.telname + ' 录入时间:' + rowData.rectime
                        + ' 催送次数: 催送时间: 更改人: 更改时间:';
                $('#otherInfoOrderHandle').html(OrderHandleListviewModule.monitInfo + ' ' + orderformOtherInfo);
                //总金额
                $('#totalMoney').html('总金额:' + rowData.totalmoney);
            }
        },

        //定义表格移动的键盘处理
        datagridKeyMove: function () {
            var that = this;
            this.orderFormHandleGrid.datagrid('getPanel').panel('panel').attr('tabindex', 1).bind('keydown', function (e) {
                //当前选择的行
                var selectedRowObj = that.orderFormHandleGrid.datagrid('getSelected');
                //当前选择行的number
                var selectedRowIndex = that.orderFormHandleGrid.datagrid('getRowIndex', selectedRowObj);
                //处理输入框的值
                var inputCode = $('#orderHandle' + selectedRowIndex).val();

                switch (e.keyCode) {
                    case 38: // up  上移动
                        $('#orderHandle' + selectedRowIndex).val('');  //将原来的输入置空
                        if (selectedRowIndex == 0) return false;//为0，就是到顶了，不用再移动了
                        selectedRowIndex -= 1;
                        that.orderFormHandleGrid.datagrid('selectRow', selectedRowIndex);
                        $('#orderHandle' + selectedRowIndex).focus();  //设置焦点
                        OrderHandleListviewModule.focusNumberOH = selectedRowIndex;
                        OrderHandleListviewModule.orderHandleData = '';
                        that.ODrefreshOrder = true;
                        break;
                    case 40: // down 下移动
                        $('#orderHandle' + selectedRowIndex).val(''); //将原来的输入置空
                        var rowsObj = that.orderFormHandleGrid.datagrid('getRows');  //返回当前页的记录
                        var rowLength = rowsObj.length - 1;
                        if (selectedRowIndex == rowLength) return;  //到表格尾部了，就不用再移动了
                        selectedRowIndex += 1;
                        that.orderFormHandleGrid.datagrid('selectRow', selectedRowIndex);
                        $('#orderHandle' + selectedRowIndex).focus();
                        OrderHandleListviewModule.focusNumberOH = selectedRowIndex;
                        OrderHandleListviewModule.orderHandleData = '';
                        that.ODrefreshOrder = true;
                        break;
                    case 13: //回车，确认备注
                        that.ODrefreshOrder = true;
                        break;
                }
            });
        },

        //单击处理栏
        orderHandleClick: function (rowIndex, orderformid) {
            //订单序号
            this.focusNumberOH = rowIndex;
            //更新焦点订单号
            this.focusOrderformidOH = orderformid;
            //行选中
            //this.orderFormHandleGrid.datagrid('selectRow',0);
        },

        //处理栏活动焦点，开启从表显示
        orderHandleFocus: function (rowIndex, orderformid) {
            //更新焦点订单号
            this.focusOrderformidOH = orderformid;
            //订单序号
            this.focusNumberOH = rowIndex;
            //显示货物
            OrderHandleListviewModule.orderProductsShow(this.focusOrderformidOH);
        },

        /**
         * 表格的分页事件
         */
        dataPage: function () {
            var that = this;
            this.orderFormHandleGrid.datagrid('getPager').pagination({
                onSelectPage: function (pageNumber, pageSize) {
                    var data = {'page': pageNumber};
                    $.ajax({
                        type: "POST",
                        url: "__URL__/listview",
                        data: data,
                        dataType: "json",
                        success: function (data) {
                            //选择第一行焦点
                            that.orderFormHandleGrid.datagrid('loadData', data);
                            that.focusNumberOH = 0;  //初始定位
                            //缓存订单号
                            that.focusOrderformidOH = data.rows[0].orderformid;
                            //行选中
                            that.orderFormHandleGrid.datagrid('selectRow', that.focusNumberOH);
                            //显示焦点
                            $('#orderHandle' + that.focusNumberOH).focus();
                            //显示货物
                            that.orderProductsShow(that.focusOrderformidOH);
                        }
                    })

                }
            });

        },

        //启动启动，取得订单
        GetOrderFrom: function () {
            /*取得订单表页码*/
            var options = this.orderFormHandleGrid.datagrid('getPager').pagination('options');
            var pageNumber = options.pageNumber; //页码
            var data = {'page': pageNumber};
            /*当前选择的行*/
            var selectedRowObj = this.orderFormHandleGrid.datagrid('getSelected');
            if (selectedRowObj) {
                //当前选择行的index
                var selectedRowIndex = this.orderFormHandleGrid.datagrid('getRowIndex', selectedRowObj);
            } else {
                var selectedRowIndex = 0;
            }
            var that = this;
            $.ajax({
                type: "POST",
                url: "__URL__/listview",
                data: data,
                dataType: "json",
                success: function (data) {
                    //选择第一行焦点
                    that.orderFormHandleGrid.datagrid('loadData', data);
                    if (data.rows.length == 0) {
                        that.focusNumberOH = 0;
                        that.focusOrderformidOH = 0;
                        return false;
                    }
                    //根据订单好重新计算光标
                    var getFocusOrderformid = false;  //判断是否根据订单号取得光标

                    //循环判断定位光标订单号是否存在
                    for (i = 0; i < data.rows.length; i++) {
                        if (that.focusOrderformidOH == data.rows[i].orderformid) {
                            $('#orderHandle' + i).focus();
                            $('#orderHandle' + i).val(that.orderHandleData);
                            //行选中
                            that.orderFormHandleGrid.datagrid('selectRow', i);
                            getFocusOrderformid = true;

                            //延迟再刷新一下
                            setTimeout(function () {
                                if (that.orderHandleData.length > 0) {
                                    $('#orderHandle' + OrderHandleListviewModule.focusNumberOH).val(that.orderHandleData);
                                }
                            }, 100);

                            return false;  //退出循环
                        }
                    }

                    //原来的订单已经处理，需要根据规则重新制定光标位置
                    if (getFocusOrderformid == false) {
                        //原来的订单是在行尾，那焦点跟踪到行尾
                        if (that.focusNumberOH >= data.total) {
                            var rowFocusNumber = data.total - 1;
                            $('#orderHandle' + rowFocusNumber).focus();
                            //行选中
                            that.orderFormHandleGrid.datagrid('selectRow', rowFocusNumber);
                        }
                        //原来的焦点不在行尾，那就上继续在原来的位置
                        if ((that.focusNumberOH == data.rows.length) && (that.focusNumberOH > 0)) {
                            //that.focusNumberOH = that.focusNumberOH - 1;
                        }
                        $('#orderHandle' + that.focusNumberOH).focus();
                    }

                }
            })
        },

        //启动的时候，获取订单
        firstGetOrderform: function () {
            //获取分页参数
            var pageNumber = <?php echo ($pagenumber); ?>;
            var data = {'page': pageNumber};
            //获取行定位
            var rowIndex = <?php echo ($rowIndex); ?>;
            var that = this;
            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: "__URL__/listview",
                    dataType: "json",
                    data: data,
                    success: function (data) {
                        if (data.rows.length > 0) {
                            //选择第一行焦点
                            that.orderFormHandleGrid.datagrid('loadData', data);
                            that.orderFormHandleGrid.datagrid('getPager').pagination({'pageNumber': pageNumber});
                            that.focusNumberOH = rowIndex;  //初始定位
                            //缓存订单号
                            that.focusOrderformidOH = data.rows[rowIndex].orderformid;
                            //显示焦点
                            $('#orderHandle' + that.focusNumberOH).focus();
                            //行选中
                            that.orderFormHandleGrid.datagrid('selectRow', that.focusNumberOH);
                            //初始化快速刷新订单的数量标志
                            that.firstOrderNumber = that.taskOrderNumber = data.total;
                        }
                    }
                })
            }, 350);

        },

        /**
         *  快速定时更新订单
         *  快速刷新，需要根据前台是否处理订单来定,如果前台处理了订单，就快速刷新订单
         **/
        quickRefreshOrderForm: function () {
            var that = this;
            this.firstGetOrderInterval = setInterval(function () {
                //判断前台是否处理订单
                if (that.refreshOrder == false) {
                    return false;
                }
                that.GetOrderFrom();
            }, 2000);
        },

        //定时刷新显示订单情况
        refreshOrderHandleMonit: function () {
            this.orderMonitInterval = setInterval(function () {
                var ordermonit = '';
                $.ajax({
                    type: "GET",
                    url: "__URL__/getordermonit",
                    dataType: "json",
                    success: function (data) {
                        if (data.length > 0) {
                            ordermonit = "订单量:" + data[0].totalnumber + '      ';
                            ordermonit += "已处理:" + data[0].task + '     ';
                            ordermonit += "未处理:" + data[0].notask + '     ';
                            ordermonit += "催餐:" + data[0].fast + '     ';
                            ordermonit += "总金额:" + data[0].totalmoney;
                            monitInfo = ordermonit;
                            $('#orderHandleMonit').html(ordermonit + ' |  ' + fastKeyHelp)
                        }
                    }
                });
            }, 30000);
        },

        //显示订单完成情况
        orderCompleteCondition: function () {
            $.ajax({
                type: "GET",
                url: "__URL__/getOrderCondition",
                dataType: "json",
                success: function (data) {
                    if (data) {
                        $('#ordertotal').html(data.total);
                        $('#ordernopai').html(data.nopai);
                        $('#ordercomplete').html(data.complete);
                        $('#ordernocomplete').html(data.nocomplete);
                        $('#orderten').html(data.ten);
                        $('#ordertwenty').html(data.twenty); //20分钟
                        $('#orderthirty').html(data.thirty);
                        $('#orderforty').html(data.forty);
                        $('#orderfifty').html(data.fifty);
                        $('#ordersixty').html(data.sixty);
                    } else {
                        $('#ordertotal').html('0');
                        $('#ordernopai').html('0');
                        $('#ordercomplete').html('0');
                        $('#ordernocomplete').html('0');
                        $('#orderten').html('0');
                        $('#ordertwenty').html('0'); //20分钟
                        $('#orderthirty').html('0');
                        $('#orderforty').html('0');
                        $('#orderfifty').html('0');
                        $('#ordersixty').html('0');
                    }
                }
            })
        },

        //定时刷新显示订单王超
        refreshOrderCompleteMonit: function () {
            var that = this;
            this.orderCompleteInterval = setInterval(function () {
                that.orderCompleteCondition();
            }, 10000);
        },

        //订单表获得焦点后，显示订货的内容
        orderProductsShow: function (orderformid) {
            var orderProductsIsExits = false;  //产品缓存已经存在
            var that = this;
            $.each(this.orderProductsArray, function (key, value) {
                if (value.orderformid == orderformid) {
                    //存在缓存，直接写入
                    that.orderProductsHandleGrid.datagrid('loadData', [value]);
                    return;
                }
            })


            $.ajax({
                type: "GET",
                url: "__URL__/showproducts/orderformid/" + orderformid,
                dataType: "json",
                success: function (data) {
                    if (!data) {
                        var orderProducts = new Array();
                        that.orderProductsHandleGrid.datagrid('loadData', orderProducts);
                        return;
                    }

                    that.orderProductsHandleGrid.datagrid('loadData', data);
                    //保持到产品缓存区中
                    $.each(that.orderProductsArray, function (key, value) {
                        if (value.orderformid == data[0].orderformid) {
                            //如果已经存在，就不需要缓存
                            orderProductsIsExits = true;
                        }
                    })
                    if (!orderProductsIsExits) {
                        that.orderProductsArray.push(data[0]);
                    }
                }
            })
        }
        ,


        //处理订单 ,根据送餐员编码
        orderHandle: function (event, obj, orderformid, rowIndex) {
            //输入的键值
            var event = event || window.event;
            var keyCode = event.which ? event.which : event.keyCode;
            //定义根据输入值，处理订单
            var inputCode = $(obj).val();
            //如果输入数字,停止刷新
            if (keyCode >= 48 && keyCode <= 57) {
                //缓存输入值
                //this.orderHandleData = inputCode;   //this.orderHandleData + String.fromCharCode(keyCode)
            }
            ;
            //如果是删除键,另外换成值
            if (keyCode == 8) {
                this.orderHandleData = inputCode;
            }

            //缓存输入值
            this.orderHandleData = inputCode;

            if (keyCode == 38) return; //上移动
            if (keyCode == 40) return; //下移动
            if (keyCode == 13) {   //订单处理
                switch (inputCode) {
                    case '0':  //对退餐的处理;对已经处理送餐员的订单的改单或者催单的处理
                        this.cancelchangehurryOrder(orderformid, rowIndex);
                        break;
                    case '2':  //对启动订单备注操作界面
                        this.beizhuOrderView(orderformid, rowIndex);
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
                    case '6': //根据送餐员代码查询的快捷键
                        this.searchSendnameView();
                        break;
                    case '7' :  //返回订单
                        this.backOrder(orderformid, rowIndex);
                        break;
                    case '8' : //订单打印
                        this.orderPrintHandle(orderformid, rowIndex,1);
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
                this.orderPrintHandle(orderformid, rowIndex,1);
            }
            //预处理显示送餐员的产品规格信息
            if (inputCode.length >= 2) {  //输入的是送餐员的代码，才处理
                this.preproSendnameProduct(inputCode);
            }


        },

        //返回送餐员信息
        returnSendname: function () {
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

            setInterval(function () {
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
            }, 100000);
        },


        //对退餐，催单，改单的处理
        cancelchangehurryOrder: function (orderformid, rowIndex) {
            var that = this;
            this.refreshOrder = false;
            //如果是退餐，就把订单置为已退餐；催送改为已催送，改单改为已改单
            $.ajax({
                type: "GET",
                url: "__URL__/cancelchangehurryOrderHandle/orderformid/" + orderformid,
                dataType: "json",
                success: function (returnData) {
                    that.refreshOrder = true;
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
                    };

                    if (returnData['success'] == 'success') {
                        that.orderFormHandleGrid.datagrid('updateRow', {
                            index: rowIndex,    //定位行
                            row: {
                                state: returnData['state']
                            }
                        });
                        OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
                        $('#orderHandle' + rowIndex).val();
                        //根据订单长度,进行处理
                        var datagridtotal = that.orderFormHandleGrid.datagrid('getData').total;
                        if (datagridtotal == 1) {
                            //只有一个订单,不处理
                        } else {
                            rowIndex = rowIndex + 1;
                            if (datagridtotal == rowIndex) {  // 在订单尾部,不处理
                                rowIndex = rowIndex - 1;
                            } else {
                                //正常情况,处理
                            }
                        }
                        $('#orderHandle' + rowIndex).focus();
                    };
                }

            });
        },


        //订单备注操作
        beizhuOrderView: function (orderformid, rowIndex) {
            //停止刷新订单页面
            this.refreshOrder = false;
            var that = this;
            $(this.dialog).dialog({
                title: '订单备注',
                iconCls: 'icons-application-application_add',
                width: 300,
                height: 440,
                cache: false,
                href: "__URL__/beizhuInput/className/OrderHandleModule/orderformid/" +
                orderformid + "/rowIndex/" + rowIndex,
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
            return false;  //功能停止
            //停止刷新订单页面
            this.refreshOrder = false;
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
                                    url = url + value.name + '/' + value.value + '/';
                                });
                                url = encodeURI(url);
                                //写入数据库
                                $.ajax({
                                    type: "GET",
                                    url: url,
                                    dataType: "json",
                                    success: function (data) {
                                        //更新状态
                                        OrderHandleListviewModule.orderFormHandleGrid.datagrid('updateRow', {
                                            index: rowIndex,    //定位行
                                            row: {
                                                state: '转给其他分公司'
                                            }
                                        });
                                    }
                                })
                                $('#orderHandle' + rowIndex).val(''); //输入框恢复空
                                $('#orderHandle' + rowIndex).focus(); //输入框恢复空
                                $(that.dialog).dialog('close');
                                OrderHandleListviewModule.refreshOrder = true;
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
         * 单独发送消息给送餐员
         */
        sendAloneMessagesView: function (orderformid, rowIndex) {
            this.refreshOrder = false;
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

                                url = '__URL__/setAloneMessages/sendname/' + encodeURI(sendname) + '/msgcontent/' + encodeURI(msgcontent);
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
                                });
                                OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
                                $('#orderHandle' + rowIndex).val(''); //输入框恢复空
                                $('#orderHandle' + rowIndex).focus(); //输入框恢复空
                                $(that.dialog).dialog('close');
                                that.refreshOrder = true;
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }]
            });
        },

        /**
         * 送餐地址查询
         */
        searchAddressView: function (orderformid, rowIndex) {
            this.refreshOrder = false;
            var that = this;
            $(this.dialog).dialog({
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
                                    url = url + value.name + '/' + value.value;
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '配送地址查询');
                                $(OrderHandleListviewModule.dialog).dialog('close');
                                that.setRefresh();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }]
            });
        },

        /**
         * 送餐员查询
         */
        searchSendnameView: function () {
            this.refreshOrder = false;   //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '送餐员查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderHandle/searchSendnameInput');?>",
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
                                var url = '__URL__/searchviewSendname/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value + '/';
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '配送送餐员查询');
                                $(that.dialog).dialog('close');
                                that.setRefresh();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                        return false;
                    }
                }]
            });
        },

        /**
         * 其他查询
         */
        searchOtherView: function () {
            this.refreshOrder = false;   //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '打印派单',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderHandle/searchOtherInput');?>",
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
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '配送综合查询');
                                that.ODrefreshOrder = true;   //开启刷新
                                $(that.dialog).dialog('close');
                                that.setRefresh();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        that.refreshOrder = true;   //开启刷新
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }]
            });
        },

        /**
         * 打印派单操作
         */ 
        orderPrintView: function(){
            this.refreshOrder = false;   //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '配送综合查询',
                iconCls: 'icons-application-application_add',
                width: 900,
                height: 540,
                cache: false,
                href: "<?php echo U('OrderHandle/OrderPrintView');?>",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        that.refreshOrder = true;   //开启刷新
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        that.refreshOrder = true;   //开启刷新
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }]
            });
        },

        /**
         * 返回订单
         */
        backOrder: function (orderformid, rowIndex) {
            //关闭刷新订单页面
            this.refreshOrder = false;
            var that = this;
            $('#orderHandle' + rowIndex).blur();
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
                                that.refreshOrder = true;  //恢复刷新
                                return false;
                            }

                            if (returnData['success'] == 'success') {
                                //handleData = returnData['data'];
                                that.orderFormHandleGrid.datagrid('updateRow', {
                                    index: rowIndex,    //定位行
                                    row: {
                                        state: '返回'
                                    }
                                });
                                OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
                                $('#orderHandle' + rowIndex).val();
                                $('#orderHandle' + rowIndex).focus();  //下移动一行
                                that.orderFormHandleGrid.datagrid('selectRow', rowIndex);
                                that.refreshOrder = true;
                            }
                        }

                    });
                } else {
                    that.setRefresh();
                }
            });
        },

        /**
         * 订单打印
         * @param  accounttype是客户联选项，1：不需要，2：需要
         */
        orderPrintHandle: function (orderformid, rowIndex,accounttype) {
            //orderPrintData的程序在general.js中
            orderPrintData(orderformid, rowIndex,accounttype);

            // 设定订单状态为已打印
            $.ajax({
                type: "GET",
                url: "__URL__/setOrderPrinted/orderformid/" + orderformid,
                dataType: "json",
                success: function (data) {

                }
            });

            //更新打印状态
            this.orderFormHandleGrid.datagrid('updateRow', {
                index: rowIndex,    //定位行
                row: {
                    state: '已打印'
                }
            });

            OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
            $('#orderHandle' + rowIndex).val();
            //根据订单长度,进行处理
            var datagridtotal = this.orderFormHandleGrid.datagrid('getData').total;
            if (datagridtotal == 1) {
                //只有一个订单,不处理
            } else {
                rowIndex = rowIndex + 1;
                if (datagridtotal == rowIndex) {  // 在订单尾部,不处理
                    rowIndex = rowIndex - 1;
                } else {
                    //正常情况,处理
                }
            }
            $('#orderHandle' + rowIndex).focus();  //下移动一行
            this.orderFormHandleGrid.datagrid('selectRow', rowIndex);
            this.refreshOrder = true;

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
            })

        },

        /**
         * 打印电子票二维码
         **/
        printEticketQRcode: function(orderformid){
            console.log("__URL__/getInvoiceInfo/orderformid/" + orderformid);
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
            })
        },

        /**
         * 将订单转给分送店
         */
        distributeOrderSecondPoint: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '转分送点',
                iconCls: 'icons-application-application_add',
                width: 200,
                height: 440,
                cache: false,
                href: "<?php echo U('OrderHandle/distributeOrderSecondPointInput');?>",
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
            var findSendname = false;
            var that = this;
            this.refreshOrder = false;
            $.each(that.sendnameMgr, function (key, value) {
                if (value.code == inputCode) { //送餐员代码相等
                    //更新订单状态和送餐员名字
                    that.orderFormHandleGrid.datagrid('updateRow', {
                        index: rowIndex,    //定位行
                        row: {
                            state: '已处理',
                            sendname: value.name  //送餐员
                        }
                    });

                    //上传处理结果
                    $.ajax({
                        type: "GET",
                        url: "__URL__/orderHandleByCode/orderformid/" + orderformid + "/code/" + inputCode,
                        dataType: "json",
                        success: function (returnData) {
                        }
                    });
                    $('#orderHandle' + rowIndex).val();
                    //根据订单长度,进行处理
                    var datagridtotal = that.orderFormHandleGrid.datagrid('getData').total;
                    if (datagridtotal == 1) {
                        //只有一个订单,不处理
                    } else {
                        rowIndex = rowIndex + 1;
                        if (datagridtotal == rowIndex) {  // 在订单尾部,不处理
                            rowIndex = rowIndex - 1;
                        } else {
                            //正常情况,处理
                        }
                    }
                    $('#orderHandle' + rowIndex).focus();  //下移动一行
                    that.orderFormHandleGrid.datagrid('selectRow', rowIndex);
                    findSendname = true;
                    OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
                    that.refreshOrder = true; //恢复刷新
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
                OrderHandleListviewModule.refreshOrder = true;
                $('#orderHandle' + OrderHandleListviewModule.focusNumberOH).focus();
                return false;
            }
        },


        /**
         * 设置打印机
         */
        setPrinter: function () {
            var tmp_index;
            tmp_index = LODOP.SELECT_PRINTER();
            if (tmp_index != -1) {
                print_index = tmp_index;
                printname = LODOP.GET_PRINTER_NAME(print_index);
                $.cookie('rmsPrinterIndex', print_index, {expires: 700});  //保存打印
                $.cookie('rmsPrinterName', printname, {expires: 700});  //保存打印
                printname = '--(' + printname + ')';
                $('#sayPrinterName').html(printname);
                //保存选择的设置
                $.cookie('rmsPrinterindex', print_index, {expires: 700}); // 存储一个带700天期限的 cookie
            }
        },

        /**
         * 设置打印纸张
         */
        setPrintPage: function () {
            var that = this;
            this.refreshOrder = false;
            $(that.dialog).dialog({
                title: '打印机纸张设置',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderHandle/setPrintPage');?>",
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
                                //获取选择的打印纸张
                                selectPageVal = $('#selectprintpage').val();
                                selectPageName = $('#selectprintpage').find("option:selected").text();
                                if (selectPageVal == '') {
                                    alert('没有选择打印纸张');
                                    return false;
                                }
                                $.cookie('rmsPrintPage', selectPageVal, {expires: 700});  //保存打印纸张
                                $.cookie('rmsPrintPageName', selectPageName, {expires: 700});  //保存打印纸张
                                $('#sayPageName').text(selectPageName);
                                $(that.dialog).dialog('close');
                                that.refreshOrder = true;
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.setRefresh();
                    }
                }]
            });
        },

        //预处理显示装箱送餐员的产品规格信息
        preproSendnameProduct: function (inputCode) {
            $.ajax({
                type: "GET",
                url: "__URL__/getProperSendnameproductsByCode/code/" + inputCode,
                dataType: "json",
                success: function (returnData) {
                    $('#orderHandleMonit').html(returnData.content);
                }

            });
        },

        /**
         * 查看订单
         */
        detailview: function (orderformid, rowIndex) {
            this.closeInterval();
            var url = "__URL__/detailview/record/" + orderformid + "/returnAction/listview"
                    + '/rowIndex/' + rowIndex + '/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        /**
         * 页面快键键设置
         */
        quickKeyboardAction: function () {
            var that = this;
            // ctrl+3快捷键 f3是不能用的;地址查询
            Mousetrap.bind(['ctrl+3', 'ctrl+f3', 'f3'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单配送') {
                    that.searchAddressView();
                }
            });

            // ctrl+6快捷键 f6是不能用的;送餐员查询
            Mousetrap.bind(['ctrl+6', 'ctrl+f6', 'f6'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单配送') {
                    that.searchSendnameView();
                }
            });

            // ctrl+7快捷键 f7是不能用的;综合查询呢
            Mousetrap.bind(['ctrl+7', 'ctrl+f7', 'f7'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单配送') {
                    that.searchOtherView();
                }
            });

            // ctrl+8快捷键 打印派单输入
            Mousetrap.bind(['ctrl+8', 'ctrl+f7'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单配送') {
                    that.orderPrintView();
                }
            });

            // ESC键
            Mousetrap.bind('esc', function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单配送') {
                    $(that.dialog).dialog('close');
                    that.setRefresh();
                }
            });
        },

        /**
         * 开启刷新
         */
        setRefresh: function () {
            OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
            OrderHandleListviewModule.refreshOrder = true;
            $('#orderHandle' + OrderHandleListviewModule.focusNumberOH).val('');
            $('#orderHandle' + OrderHandleListviewModule.focusNumberOH).focus();
        },

        //定义关不定时任务
        closeInterval: function () {
            //关闭定时任务
            clearInterval(this.firstGetOrderInterval);
            clearInterval(this.orderMonitInterval);
            clearInterval(this.refreshOrderCompleteMonit);
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
            this.closeInterval();
        }


    }


    $(function () {
        OrderHandleListviewModule.init();
        //点击空白地方，要让select row获得焦点
        $('#OrderFormHandleTable').datagrid('getPanel').panel('panel').focus(
                function () {
                    $('#orderHandle' + OrderHandleListviewModule.focusNumberOH).focus(); //输入框恢复空
                }
        );
    })
</script>