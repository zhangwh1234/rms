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

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/invoiceEleParaview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title=""
                    border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/invoiceEleParaview');">参数设置<span></span></a></li>
        <li style="width: 5px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title=""
                    border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">发票汇总<span></span></a></li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="width: 200px;">发票余量：<span id="invoiceweb_fpyl"></span></li>

        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName" onclick="InvoiceElectronListviewModule.closeTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="InvoiceElectronListviewModule.closeTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div id="invoiceElectronListviewDiv" class="easyui-layout" style="width:100%;height:400px;">
    <div data-options="region:'north',split:false" style="padding: 0px; background: white;height: 35px;">
        <table width="100%" border="0">
            <tr>
                <td width="5%" style="font-size: 14px;">查询参数:</td>
                <td width="5%" align="right" style="font-size: 14px;">日期:</td>
                <td width="10%"><input id="invoicesearchdate" type="text" name="searchdate" class="easyui-datebox" style="font-size: 14px;" value="<?php echo ($cdate); ?>"></td>
                <td width="20%" align="right" style="font-size: 14px;">发票抬头或者订餐地址或者开票电话:</td>
                <td align="left" width="50%"><input id="invoicesearchheader" class="easyui-textbox" data-options="iconCls:'icon-search'" style="width:300px;font-size: 14px;">
                    <a id="invoiceSearchButton" class="easyui-linkbutton" data-options="iconCls:'icon-search'" style="margin-left:10px;font-size: 14px; ">
                        查 询 </a>
                    <a id="invoiceexeclButton" class="easyui-linkbutton" data-options="iconCls:'icon-search'" style="margin-left:15px;font-size: 14px; ">
                        导出excel文件 </a>

                </td>
                <td align="left"></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div data-options="region:'south',split:false" style="height:23px;"></div>

    <div data-options="region:'center'" style="padding:5px;background:#eee;">
        <table id="InvoiceElectronHandleTable" fit="true"></table>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />

<script>
    var InvoiceElectronListviewModule = {
        dialog: '#globel-dialog-div',
        invoiceElectronHandleGrid: '', //订单处理表

        focusNumberOH: 0, //定义光标，OH是OrderHandle的缩写
        focusInvoiceidOH: 0, //定义光标订单号
        refreshOrder: true, //定义刷新标志，默认是开启刷新

        //定义发票情况的缓存变量
        monitInfo: '',

        referInvoice: 0, //刷新变量

        init: function () {
            this.invoiceElectronHandleGrid = $('#InvoiceElectronHandleTable');
            //设置div的高度
            $('#invoiceElectronListviewDiv').height(IndexIndexModule.operationHeight);

            //订单处理表格
            this.setDatagrid();
            //初始化订单数据
            this.firstGetInvoice();
            //载入快捷键
            this.quickKeyboardAction();
            //启动分页事件
            this.dataPage();
            //启动发票余量显示
            this.fpylSearch();

        },

        //设置订单处理表格
        setDatagrid: function () {
            /*定义订单invoice处理表*/
            this.invoiceElectronHandleGrid.datagrid({
                nowrap: true,
                fitColumns: false,
                singleSelect: true,
                autoRowHeight: true,
                striped: false,
                border: false,
                rownumbers: true, //显示行号
                showFooter: true,
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
                            field: 'eticketno',
                            title: '提取码',
                            width: 200,
                            align: 'left',
                            formatter: function (value, row) {
                                if ((row.state == 2) || (row.state == 3)) {
                                    return '<a target="view_window" href="http://invoice.lihua.com/image.php?s=/InvoiceWeb/verify/number/' + value + '">' + value + '</a>';
                                } else {
                                    return '<span>' + value + '</span>';
                                }
                            }
                        },
                        {
                            field: 'header',
                            title: '发票抬头',
                            width: 300,
                            align: 'left'
                        },
                        {
                            field: 'body',
                            title: '发票内容',
                            width: 60,
                            align: 'center'
                        },
                        {
                            field: 'money',
                            title: '金额',
                            width: 60,
                            align: 'left'
                        },
                        {
                            field: "state",
                            width: 40,
                            title: '状态',
                            styler: function (value, row, index) {
                                if (value == 0) {
                                    return 'background-color:white;color:	#02C874;';
                                }
                                if (value == 1) {
                                    return 'background-color:green;color: white;';
                                }
                                if (value == 2) {
                                    return 'background-color:yellow;color:#FF0000;';
                                }
                                if (value == 3) {
                                    return 'background-color:red;color:black;';
                                }
                                if (value == 4) {
                                    return 'background-color:red;color:black;';
                                }
                            },
                            formatter: function (value, rowData, rowIndex) {
                                if (value == 0) {
                                    return '录入';
                                }
                                if (value == 1) {
                                    return '未开';
                                }
                                if (value == 2) {
                                    return '已开';
                                }
                                if (value == 3) {
                                    return '退票';
                                }
                                if (value == 34) {
                                    return '错误';
                                }
                            }
                        },
                        {
                            field: "operation",
                            width: 120,
                            title: '操作',
                            align: 'center',
                            formatter: function (value, rowData, rowIndex) {
                                var operation = '';
                                if (rowData.state == 2) {                  
                                    operation = "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.sendSms(" + rowData.invoicewebid +
                                        ");' class='orderHandleDetailview'  style='margin-left:4px;'>短信</a>";
                                    if (rowData.download_state == 2) {
                                        if (rowData.domain == 'bj.lihuaerp.com') {
                                            operation += "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.openImage(\"" + rowData.ordersn +
                                                "\");' class='orderHandleDetailview' style='margin-left:4px;'>看票</a>";
                                        } else {
                                            operation += "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.openPDFUrl(\"" + rowData.pdf_url +
                                                "\");' class='orderHandleDetailview' style='margin-left:4px;'>看票</a>";
                                        }

                                        if (rowData.canceloper == 'IS') {
                                            operation += "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.doubleDownload(\"" + rowData.ordersn +
                                                "\");' class='orderHandleDetailview'  style='margin-left:4px;'>重载</a>";
                                            operation += "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.cancelInvoice(\"" + rowData.eticketno +
                                                "\");' class='orderHandleDetailview'  style='margin-left:4px;'>退票</a>";
                                        };
                                    }
                                    return operation;
                                }
                                if(rowData.cancel_state == 2){
                                    if (rowData.download_state == 2) {
                                        if (rowData.domain == 'bj.lihuaerp.com') {
                                            operation = "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.openCancelImage(\"" + rowData.ordersn +
                                                "\");' class='orderHandleDetailview' style='margin-left:4px;'>看退票</a>";
                                        } else {
                                            operation = "<a href='javascript:void(0);' onclick='InvoiceElectronListviewModule.openPDFUrl(\"" + rowData.pdf_url +
                                                "\");' class='orderHandleDetailview' style='margin-left:4px;'>看退票</a>";
                                        }
                                    }
                                    return operation;
                                } 
                                return '';
                            }
                        },
                        {
                            field: 'ordertxt',
                            title: '订单情况',
                            width: 395,
                            align: 'left'
                        },
                        {
                            field: "telphone",
                            width: 55,
                            title: '电话',
                            align: 'center',
                            resizable: true
                        },
                        {
                            field: 'KPR',
                            title: '开票人',
                            width: 110,
                            align: 'left'
                        },
                        {
                            field: 'opendate',
                            title: '开票日期',
                            width: 110,
                            align: 'left',
                            resizable: true
                        },
                        {
                            field: 'fp_hm',
                            title: '发票号码',
                            width: 110,
                            align: 'left'
                        },
                        {
                            field: 'gmf_nsrsbh',
                            title: '识别号',
                            width: 150,
                            align: 'left'
                        },
                        {
                            field: 'company',
                            title: '分公司',
                            width: 110,
                            align: 'left'
                        },
                        {
                            field: 'gmf_dzdh',
                            title: '发票地址',
                            width: 50,
                            align: 'left'
                        },
                        {
                            field: 'gmf_yhzh',
                            title: '银行账号',
                            width: 50,
                            align: 'left'
                        },
                        {
                            field: 'sendmail',
                            title: '邮件状态',
                            width: 10,
                            align: 'left'
                        },
                    ]
                ],
                onClickRow: this.clickDataGridRow, //选择行事件
                onSelect: this.selectDataGridRow //选择行事件
            });

            /*定义订单分页表*/
            var pager = $('#InvoiceElectronHandleTable').datagrid('getPager');
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep', 'first', 'prev', 'manual', 'links', 'next', 'last']
            });

            /*快捷代码帮助*/
            fastKeyHelp = '';
            /*显示快捷代码帮助*/
            $('#InvoiceElectronHandleMonit').html(fastKeyHelp);
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
            this.invoiceElectronHandleGrid.datagrid('getPager').pagination({
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
                            that.invoiceElectronHandleGrid.datagrid('loadData', data);
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
                            that.invoiceElectronHandleGrid.datagrid('loadData', data);
                        }
                    }
                })
            }, 350);

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
         * 发送短信通知
         */
        sendSms: function (id) {
            var r = confirm("是否要发送短信?");
            if (r == true) {

            } else {
                return;
            }
            var data = {
                'id': id
            };
            $.ajax({
                type: "POST",
                url: "__URL__/sendSms",
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.success == 'ok') {
                        $.messager.alert('通知', '短信发送完毕！过1分钟左右就会收到。');
                    } else {
                        $.messager.alert('通知', data.info);
                    }
                }
            })
        },

        /**
         * 退票操作
         */
        cancelInvoice: function (eticketno) {
            var r = confirm("发票提取码:" + eticketno + "  是否要退票?");
            if (r == true) {

            } else {
                return;
            }
            var data = {
                'id': eticketno
            };
            $.ajax({
                type: "POST",
                url: "__URL__/cancelInvoice",
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.success == 'ok') {
                        $.messager.alert('通知', '发票已退，过一点时间会改变:');
                    } else {
                        $.messager.alert('通知', data.info);
                    }
                }
            });
        },

        /**
         * 重新下载发票
         */
        doubleDownload: function (eticketno) {
            var r = confirm("订单号:" + eticketno + "  是重新下载发票吗?");
            if (r == true) {

            } else {
                return;
            }
            var data = {
                'id': eticketno
            };
            $.ajax({
                type: "POST",
                url: "__URL__/doubleDownload",
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.success == 'ok') {
                        $.messager.alert('通知', '已经重新下载，请稍后:');
                    } else {
                        $.messager.alert('通知', data.info);
                    }
                }
            });
        },

        /**
         * 发票图形查看
         */
        openImage: function (ordersn) {
            window.open("http://invoice.lihua.com/index.php?s=/InvoiceWeb/getpdf/ordersn/" + ordersn, '发票')
        },

        /**
         * 查看退票图形
         */
        openCancelImage: function (ordersn) {
            window.open("http://invoice.lihua.com/index.php?s=/InvoiceWeb/getCancelpdf/ordersn/" + ordersn, '发票')
        },

        /**
         * 直接查看PDF发普，是国信电子的看法
         */
        openPDFUrl: function(pdfurl){
            window.open(pdfurl, '发票')
        },  



        /**
         * 发票余量查询
         */
        fpylSearch: function () {

            $.ajax({
                type: "POST",
                url: "__URL__/fpyl",
                dataType: "json",
                success: function (data) {
                    if (data) {
                        //发票余量显示
                        $('#invoiceweb_fpyl').html(data.number);
                    }
                }
            });

            setInterval(function () {
                $.ajax({
                    type: "POST",
                    url: "__URL__/fpyl",
                    dataType: "json",
                    success: function (data) {
                        if (data) {
                            //发票余量显示
                            $('#invoiceweb_fpyl').html(data.number);
                        }
                    }
                })
            }, 30000);
        },


        /**
         * 页面快键键设置
         */
        quickKeyboardAction: function () {

        },

        //编辑
        editview: function (id, rowIndex) {
            InvoiceOper.closeCard();
            this.closeInterval();
            var url = "<?php echo U('InvoiceMgr/editview');?>";
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
        }


    }


    $(function () {
        InvoiceElectronListviewModule.init();

    })

    //查询
    $('#invoiceSearchButton').bind('click', function () {
        var invoicesearchdate = $('#invoicesearchdate').datebox('getValue');
        var invoicesearchheader = $('#invoicesearchheader').textbox('getValue');
        var invoicesearchcompany = $('#invoicesearchcompany').val();
        var invoicesearchstate = $('#invoicesearchstate').val();

        $.ajax({
            type: "POST",
            url: "__URL__/listview",
            data: {
                'invoicesearchdate': invoicesearchdate,
                'invoicesearchheader': invoicesearchheader,
                'invoicesearchcompany': invoicesearchcompany,
                'invoicesearchstate': invoicesearchstate
            },
            dataType: "json",
            success: function (data) {
                $('#InvoiceElectronHandleTable').datagrid('loadData', data);
                if (data.rows.length > 0) {
                    //选择第一行焦点

                }
            }
        });

    });

    //导出execl
    $('#invoiceexeclButton').bind('click', function () {
        var param = '';
        var invoicesearchdate = $('#invoicesearchdate').datebox('getValue');
        if (!invoicesearchdate) {
            alert('日期不能为空!');
            return false;
        } else {
            param = 'invoicesearchdate/' + invoicesearchdate;
        }
        var invoicesearchheader = $('#invoicesearchheader').textbox('getValue');
        if (invoicesearchheader) {
            param = param + "/invoicesearchheader/" + invoicesearchheader;
        }
        var invoicesearchcompany = $('#invoicesearchcompany').val();
        if (invoicesearchcompany) {
            param = param + "/invoicesearchcompany/" + invoicesearchcompany;
        }
        var invoicesearchstate = $('#invoicesearchstate').val();
        if (invoicesearchstate) {
            param = param + "/invoicesearchstate/" + invoicesearchstate;
        }

        alert("__URL__/outFapiao/?" + param);
        document.location.href = "__URL__/outFapiao/?" + param;
    });


    $(function () {

        setTimeout(function () {
            InvoiceElectronListviewModule.init();
        }, 1000);

        $('#InvoiceElectronHandleTable').datagrid({
            view: detailview,
            detailFormatter: function (index, row) {
                return '<div style="padding:2px;position:relative;"><table class="ddv"></table></div>';
            },
            onExpandRow: function (index, row) {
                var ddv = $('#InvoiceElectronHandleTable').datagrid('getRowDetail', index).find('table.ddv');
                var url = "__URL__/getInvoiceWebLog/ordersn/" + row.ordersn;
                ddv.datagrid({
                    url: url,
                    fitColumns: true,
                    singleSelect: true,
                    rownumbers: true,
                    loadMsg: '',
                    height: 'auto',
                    columns: [
                        [{
                                field: 'invoiceweblogid',
                                title: 'id',
                                width: 200,
                                hidden: 'true',
                            },
                            {
                                field: 'date',
                                title: '时间',
                                align: 'left'
                            },
                            {
                                field: 'log',
                                title: '日志',
                                width: 50,
                                align: 'left'
                            }
                        ]
                    ],
                    onResize: function () {
                        $('#InvoiceElectronHandleTable').datagrid('fixDetailRowHeight', index);
                    }
                });
                $('#InvoiceElectronHandleTable').datagrid('fixDetailRowHeight', index);
            }
        });
    });
</script>