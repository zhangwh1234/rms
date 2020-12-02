<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    /*定义备注字段大小*/
    #beizhuOrderHandle .l-btn-text {
        font-size: 16px;
        color: #33338c;
        margin-top: 2px;
    }


    #orderHandleMonit {
        height: 18px;
        padding-left: 20px;
        margin-top: 2px;
        font-size: 16px;
    }

    /**
    .datagrid-row-over,
    td.datagrid-header-over {
        background: yellow;
        color: red;
        cursor: default;
    }
    **/


    .invoiceMgrListviewCancelOper {
        color: red;
    }


    a:hover.invoiceMgrListviewCancelOper {
        background: white;
        color: black;
    }


    a:hover.invoiceMgrListviewOpenOper {
        background: white;
        color: black;
    }
</style>


<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" class="moduleName" onclick="updateTab('__URL__/listview');">
                &nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;display:yes;"><a href="javascript:void(0);"
                onclick="ExhibitionInvoiceListviewModule.closeInterval();IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png"
                    alt="" title="" border="0"></a></li>
        <li style="display: yes;"><a href="javascript:void(0);" onclick="ExhibitionInvoiceListviewModule.createview();">自开发票<span></span></a></li>
        <li style="width: 50px;">&nbsp;</li>
        <li><a href="javascript:;" onMouseOver=""><img src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>

        <li><a href="javascript:void(0);" class="moduleName" onclick="ExhibitionInvoiceListviewModule.searchInvoiceView();">发票查询<span></span></a></li>
        <li style="margin-left: 100px;"><a href="javascript:;" onclick="ExhibitionInvoiceListviewModule.setupPrint();"><img src=".__PUBLIC__/Images/printerSetBtn.png" alt="" title="" border="0"></a>
        </li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="ExhibitionInvoiceListviewModule.setPrinter();" id="sayPrinterSet">打印机设置</a></li>
        <li><a href="javascript:void(0);" id="sayPrinterName" style="font-size: 10px;line-height: 30px;color: black;"><?php echo ($rmsPrinterName); ?></a></li>
        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName" onclick="ExhibitionInvoiceListviewModule.closeTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="ExhibitionInvoiceListviewModule.closeTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div id="invoiceMgrListviewDiv" class="easyui-layout" style="width:100%;height:400px;">
    <div data-options="region:'center',border:false" style="padding: 0px; background: #eee;">
        <table id="InvoiceMgrHandleTable" fit="true"></table>
    </div>
    <div data-options="region:'south',split:false,border:false" style="height: 23px;">
        <div class="pagestop">
            <div id="InvoiceMgrHandleMonit" align="center"></div>
        </div>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />

<script>
    var ExhibitionInvoiceListviewModule = {
        dialog: '#globel-dialog-div',
        invoiceMgrHandleGrid: '', //订单处理表

        focusNumberOH: 0, //定义光标，OH是OrderHandle的缩写
        focusInvoiceidOH: 0, //定义光标订单号
        refreshOrder: true, //定义刷新标志，默认是开启刷新

        //定义发票情况的缓存变量
        monitInfo: '',

        referInvoice: 0, //刷新变量

        init: function () {
            this.invoiceMgrHandleGrid = $('#InvoiceMgrHandleTable');
            //设置div的高度
            $('#invoiceMgrListviewDiv').height(IndexIndexModule.operationHeight);
            //订单处理表格
            this.setDatagrid();

            //初始化订单数据
            this.firstGetInvoice();
            //载入快捷键
            this.quickKeyboardAction();
            //启动分页事件
            this.dataPage();
            //this.quickRefreshInvoice();
            //显示订单情况
            this.refreshOrderHandleMonit();

        },

        //设置订单处理表格
        setDatagrid: function () {
            /*定义订单invoice处理表*/
            this.invoiceMgrHandleGrid.datagrid({
                nowrap: "true",
                fitColumns: "true",
                singleSelect: "true",
                autoRowHeight: "true",
                striped: "true",
                border: "false",
                rownumbers: "false", //显示行号
                showFooter: 'true',
                pagination: true,
                pagePosition: 'bottom',
                toolbar: '#tb',
                columns: [
                    [{
                            field: 'invoiceid',
                            title: 'id',
                            hidden: 'true',
                            width: 100
                        },
                        {
                            field: 'header',
                            title: '发票抬头',
                            width: 60,
                            align: 'left'
                        },
                        {
                            field: 'body',
                            title: '发票内容',
                            width: 20,
                            align: 'center'
                        },
                        {
                            field: 'orderformtxt',
                            title: '订单情况',
                            width: 65,
                            align: 'left'
                        },
                        {
                            field: "ordermoney",
                            width: 15,
                            title: '金额',
                            align: 'center'
                        },
                        {
                            field: "gmf_nsrsbh",
                            width: 25,
                            title: '纳税人识别号',
                            align: 'left'
                        },
                        {
                            field: "type",
                            width: 15,
                            title: '发票类型',
                            align: 'center',
                            formatter: function (value, rowData, rowIndex) {
                                var invoicetype;
                                if (value == '3') {
                                    invoicetype = '<span style="color: 	#FF0000">电子票</span>';
                                    return invoicetype;
                                }

                            }
                        },
                        {
                            field: "state",
                            width: 15,
                            title: '状态',
                            align: 'left'
                        },
                        {
                            field: "operation",
                            width: 40,
                            title: '操作',
                            align: 'center',
                            formatter: function (value, rowData, rowIndex) {
                                var operation;
                                operation = '';
                                operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex + "' onclick='ExhibitionInvoiceListviewModule.editview(" +
                                    rowData.invoiceid + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-right:2px;' >编辑</a>|";

                                if (rowData.type == 3) {
                                    operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex +
                                        "' onclick='ExhibitionInvoiceListviewModule.openInvoice(" + rowData.invoiceid + "," + rowIndex +
                                        ")' class='invoiceMgrListviewOpenOper' style='margin-left:4px;' >打印 | </a>";
                                }
                                operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex + "' onclick='ExhibitionInvoiceListviewModule.detailview(" +
                                    rowData.invoiceid + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-left:4px;' >查看</a>";

                                return operation;
                            }
                        }
                    ]
                ],
                onClickRow: this.clickDataGridRow, //选择行事件
                onSelect: this.selectDataGridRow //选择行事件
            });

            /*定义订单分页表*/
            var pager = $('#InvoiceMgrHandleTable').datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep', 'first', 'prev', 'manual', 'links', 'next', 'last']
            });

            /*快捷代码帮助*/
            fastKeyHelp = '';
            /*显示快捷代码帮助*/
            $('#InvoiceMgrHandleMonit').html(fastKeyHelp);
        },

        //单击表格的处理函数
        clickDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
            }
        },

        //选择行事件
        selectDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                //显示备注
            }
        },


        /**
         * 表格的分页事件
         */
        dataPage: function () {
            var that = this;
            this.invoiceMgrHandleGrid.datagrid('getPager').pagination({
                onSelectPage: function (pageNumber, pageSize) {
                    var data = {
                        'page': pageNumber
                    };
                    $.ajax({
                        type: "POST",
                        url: "__URL__/listview",
                        data: data,
                        dataType: "json",
                        success: function (data) {
                            //选择第一行焦点
                            that.invoiceMgrHandleGrid.datagrid('loadData', data);
                        }
                    })

                }
            });

        },

        //启动启动，取得订单
        GetInvoice: function () {
            /*取得订单表页码*/
            var options = this.invoiceMgrHandleGrid.datagrid('getPager').pagination('options');
            var pageNumber = options.pageNumber; //页码
            var data = {
                'page': pageNumber
            };
            var that = this;
            $.ajax({
                type: "POST",
                url: "__URL__/listview",
                data: data,
                dataType: "json",
                success: function (data) {
                    //选择第一行焦点
                    that.invoiceMgrHandleGrid.datagrid('loadData', data);

                    if (data.rows.length == 0) {
                        return false;
                    }

                }
            })
        },

        //启动的时候，获取订单
        firstGetInvoice: function () {
            var that = this;
            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: "__URL__/listview",
                    dataType: "json",
                    success: function (data) {
                        if (data.rows.length > 0) {
                            //选择第一行焦点
                            that.invoiceMgrHandleGrid.datagrid('loadData', data);
                        }
                    }
                })
            }, 350);

        },

        /**
         *  快速定时更新订单
         *  快速刷新，需要根据前台是否处理订单来定,如果前台处理了订单，就快速刷新订单
         **/
        quickRefreshInvoice: function () {
            var that = this;
            setTimeout(function () {
                //判断前台是否处理订单
                if (that.refreshOrder == false) {
                    return false;
                }
                that.GetInvoice();

            }, 1500);
        },

        //定时刷新显示订单情况
        refreshOrderHandleMonit: function () {
            var that = this;
            this.referInvoice = setInterval(function () {
                //判断前台是否处理订单
                if (that.refreshOrder == false) {
                    return false;
                }
                that.GetInvoice();

            }, 30000);
        },


        /**
         * 发票查询
         */
        searchInvoiceView: function () {
            this.refreshOrder = false;
            var that = this;
            $(this.dialog).dialog({
                title: '发票查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('InvoiceMgr/searchOtherInput');?>",
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
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '发票查询');
                                $(InvoiceMgrListviewModule.dialog).dialog('close');
                                InvoiceMgrListviewModule.refreshOrder = true;
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.refreshOrder = true;
                    }
                }]
            });
        },


        /**
         * 发票开具
         */
        openInvoice: function (invoiceid, rowIndex) {
            this.refreshOrder = false;
            $.messager.progress({
                text: '处理中，请稍候...'
            });
            var that = this;
            //获取发票所需要的信息
            $.ajax({
                type: "GET",
                url: "__URL__/getInvoiceInfo/invoiceid/" + invoiceid,
                dataType: "json",
                success: function (data) {
                    if (!data) {
                        alert('没有开票数据!');
                        $.messager.progress('close');
                    }
                    if (data.type == 3) { //电子票
                        var invoiceOperState = that.printEticketInvoice(data); //打印电子发票
                    }


                    if (invoiceOperState) {
                        $.messager.progress('close');
                        //更新打印状态
                        that.invoiceMgrHandleGrid.datagrid('updateRow', {
                            index: rowIndex, //定位行
                            row: {
                                state: '已开票'
                            }
                        });
                        /**
                        // 设定订单状态为已打印
                        $.ajax({
                            type: "GET",
                            url: "__URL__/setInvoiceOpened/invoiceid/" + invoiceid,
                            dataType: "json",
                            success: function (data) {}
                        })
                        **/

                        that.invoiceMgrHandleGrid.datagrid('selectRow', rowIndex);
                        that.taskOrderNumber = this.taskOrderNumber - 1; //为了启动快速刷新，减1
                        that.refreshOrder = true;

                    } else {
                        alert('最后开票失败 !');
                        $.messager.progress('close');
                    }
                }
            })
            return false;
        },


        /**
         * 放弃开具发票
         */
        cancelOpenInvoice: function (invoiceid, invoicetxt) {
            var that = this;
            $.messager.confirm('提示信息', invoicetxt + '<center><font color="red">确定要不开发票吗？</font></center>', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.get("<?php echo U('InvoiceMgr/cancelInvoice');?>", {
                    invoiceid: invoiceid
                }, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.quickRefreshInvoice();
                    }
                }, 'json');
            });

        },


        /**
         * 页面快键键设置
         */
        quickKeyboardAction: function () {

        },

        createview: function () {
            
            this.closeInterval();
            IndexIndexModule.updateOperateTab('__URL__/createview');
        },

        //编辑
        editview: function (id, rowIndex) {
           
            this.closeInterval();
            var url = "__URL__/editview";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //查看
        detailview: function (id, rowIndex) {
            
            this.closeInterval();
            var url = "__URL__/detailview";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },


        /**
         * 开启刷新
         */
        setRefresh: function () {
            this.refreshOrder = true;
        },

        //定义关不定时任务
        closeInterval: function () {
            //关闭定时任务
            clearInterval(this.referInvoice);
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
        },

        /**
         *  使用POS打印出电子票
         */
        printEticketInvoice: function (data) {
            var print_index = $.cookie('rmsPrinterIndex'); // 读取 cookie,打印机类型
            if (print_index < 0) {
                alert('请设置打印机');
                return false;
            }
            // 定义行号
            var linespacing = 14;
            var row = 1; // 循环变量
            // 重新设置打印机的设备

            LODOP.PRINT_INIT("printorder");
            LODOP.SET_PRINTER_INDEX(print_index);


            // ********** 发票联 *****************
            // 发票标题
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            row = row;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '电子发票提取码');

            // 日期;时间,分公司名称
            var orderformid = '日期:' + data['date'];
            ' 打印:' + data['printman'];

            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 16;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

            orderformid = '打印:北京龙城丽华餐饮管理有限公司' ;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

            //提示:
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '仿宋');
            var fapiaointro = "* 根据相关税法规定,电子发票的开票请务必在消费当日登录网上申请电子发票,此提取码  30天有效,扫码请保持票面平整.";

            var fapiaotmp_1 = fapiaointro.subCHStr(0, 40);
            var fapiaotmp_2 = fapiaointro.subCHStr(40, 80);
            var fapiaotmp_3 = fapiaointro.subCHStr(80, 120);
            var fapiaotmp_4 = fapiaointro.subCHStr(120, 160);

            if (fapiaotmp_1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
            }
            if (fapiaotmp_2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
            }
            if (fapiaotmp_3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
            }
            if (fapiaotmp_4.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_4);
            }


            var fapiaointro = "(1)请登录:http://invoice.lihua.com ;根据步骤开具增值税普通发票.";
            fapiaotmp_1 = fapiaointro.subCHStr(0, 40);
            fapiaotmp_2 = fapiaointro.subCHStr(40, 80);
            fapiaotmp_3 = fapiaointro.subCHStr(120, 160);
            if (fapiaotmp_1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
            }
            if (fapiaotmp_2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
            }
            if (fapiaotmp_3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
            }

            //LODOP.SET_PRINT_STYLE("FontSize", 10);
            //LODOP.SET_PRINT_STYLE('FontName', '黑体');
            //LODOP.SET_PRINT_STYLE('Bold', 0);
            //fapiaointro = "--------------------------";
            //row = row + 1;
            //LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            fapiaointro = "提取码:";
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

            LODOP.SET_PRINT_STYLE("FontSize", 17);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            fapiaointro = data['eticketno'];
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            fapiaointro = "--------------------------";
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '仿宋');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            fapiaointro = "(2)也可以扫描下方二维码,根据步骤开具您的电子普通增值税发票.";
            fapiaotmp_1 = fapiaointro.subCHStr(0, 30);
            var fapiaotmp_2 = fapiaointro.subCHStr(30, 30);
            var fapiaotmp_3 = fapiaointro.subCHStr(60, 30);
            if (fapiaotmp_1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
            }
            if (fapiaotmp_2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
            }
            if (fapiaotmp_3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
            }

            //打印二维码
            row = row + 1;
            LODOP.ADD_PRINT_BARCODE(linespacing * row, 40, 644, 146, 'QRCode', 'http://invoice.lihua.com?n=' + data['eticketno']);


            row = row + 6;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 10, 644, 62, '.');


            LODOP.PRINT();

            for (i = 0; i < 10000; i++) {

            }

            

            return true;

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
                $.cookie('rmsPrinterIndex', print_index, {
                    expires: 700
                }); //保存打印
                $.cookie('rmsPrinterName', printname, {
                    expires: 700
                }); //保存打印
                printname = '--(' + printname + ')';
                $('#sayPrinterName').html(printname);
                //保存选择的设置
                $.cookie('rmsPrinterindex', print_index, {
                    expires: 700
                }); // 存储一个带700天期限的 cookie
            }
        },

    }

    $(function () {
        ExhibitionInvoiceListviewModule.init();
    })
</script>


<script type="text/javascript" src=".__PUBLIC__/Js/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<script type="text/javascript">
    var LODOP;
    setTimeout(function () {
        LODOP = getLodop(document.getElementById('LODOP_OB'), document.getElementById('LODOP_EM'));
        //显示所先的打印机名称
        LODOP.SET_LICENSES("北京龙城丽华快餐有限公司", "653625970697469919278901905623", "", "");
    }, 100);
</script>