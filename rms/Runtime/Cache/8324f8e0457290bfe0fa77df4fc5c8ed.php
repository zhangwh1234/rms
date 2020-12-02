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


    .invoiceMgrListviewCancelOper{
        color:red;
    }


    a:hover.invoiceMgrListviewCancelOper {
        background: white;
        color:black;
    }


    a:hover.invoiceMgrListviewOpenOper{
        background: white;
        color:black;
    }


</style>
<script type="text/javascript" src=".__PUBLIC__/Js/bjinvoice.js"></script>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" class="moduleName" onclick="updateTab('__URL__/listview');">
            &nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;display:yes;"><a href="javascript:void(0);" onclick="InvoiceOper.closeCard();InvoiceMgrListviewModule.closeInterval();IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li style="display: yes;"><a href="javascript:void(0);"  onclick="InvoiceMgrListviewModule.createview();">自开发票<span></span></a></li>
        <li style="width: 50px;">&nbsp;</li>
        <li><a href="javascript:;" onMouseOver=""><img src=".__PUBLIC__/Images/addressBtn.jpg" alt=""
                                                       title="" border="0"></a></li>

        <li><a href="javascript:void(0);" class="moduleName"
               onclick="InvoiceMgrListviewModule.searchInvoiceView();">发票查询<span></span></a></li>
        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName"
                                                        onclick="InvoiceMgrListviewModule.closeTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="InvoiceMgrListviewModule.closeTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div id="invoiceMgrListviewDiv" class="easyui-layout" style="width:100%;height:400px;">
    <div data-options="region:'center',border:false"
         style="padding: 0px; background: #eee;">
        <table id="InvoiceMgrHandleTable" fit="true"></table>
    </div>
    <div data-options="region:'south',split:false,border:false"
         style="height: 23px;">
        <div class="pagestop">
            <div id="InvoiceMgrHandleMonit" align="center"></div>
        </div>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview"/>

<script>

    var InvoiceMgrListviewModule = {
        dialog: '#globel-dialog-div',
        invoiceMgrHandleGrid: '',   //订单处理表

        focusNumberOH: 0,  //定义光标，OH是OrderHandle的缩写
        focusInvoiceidOH: 0,  //定义光标订单号
        refreshOrder: true,  //定义刷新标志，默认是开启刷新

        //定义发票情况的缓存变量
        monitInfo: '',

        referInvoice:0,  //刷新变量

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
            this.quickRefreshInvoice();
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
                rownumbers: "false",  //显示行号
                showFooter: 'true',
                pagination: true,
                pagePosition: 'bottom',
                toolbar: '#tb',
                columns: [[
                    {field: 'invoiceid', title: 'id', hidden: 'true', width: 100},
                    {field: 'header', title: '发票抬头', width: 60, align: 'left'},
                    {field: 'body', title: '发票内容', width: 20, align: 'center'},
                    {field: 'orderformtxt', title: '订单情况', width: 65, align: 'left'},
                    {field: "ordermoney", width: 15, title: '金额',align:'center'},
                    {field: "gmf_nsrsbh", width: 25, title: '纳税人识别号',align:'left'},
                    {field: "type", width: 15, title: '发票类型',align:'center',
                        formatter: function (value, rowData, rowIndex) {
                            var invoicetype;
                            if(value == '3'){
                                invoicetype = '<span style="color: 	#FF0000">电子票</span>';
                                return invoicetype;
                            }
                            if(value == '2'){
                                invoicetype = '普通票';
                                return invoicetype;
                            }

                        }
                    },
                    {field: "state", width: 15, title: '状态',align:'left'},
                    {
                        field: "operation",
                        width: 40,
                        title: '操作',
                        align: 'center',
                        formatter: function (value, rowData, rowIndex) {
                            var operation;
                            operation = '';
                            operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex +"' onclick='InvoiceMgrListviewModule.editview(" + rowData.invoiceid  + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-right:2px;' >编辑</a>|";
                            operation += "<a href='javascript:void(0);' onclick='InvoiceMgrListviewModule.cancelOpenInvoice(" + rowData.invoiceid + ",\""+rowData.header + rowData.body+ " </br> " +rowData.orderformtxt+"</br>" +"\")' class='invoiceMgrListviewCancelOper'  style='margin-right:4px;margin-left:4px;'>放弃开票</a>|";
                            if(rowData.type == 2) {
                                operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex + "' onclick='InvoiceMgrListviewModule.openInvoice(" + rowData.invoiceid + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-left:4px;' >开票</a>";
                            }
                            if(rowData.type == 3) {
                                operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex + "' onclick='InvoiceMgrListviewModule.openInvoice(" + rowData.invoiceid + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-left:4px;' >打印</a>";
                            }
                            operation += "<a href='javascript:void(0);' id='InvoiceMgrListviewOper" + rowIndex +"' onclick='InvoiceMgrListviewModule.detailview(" + rowData.invoiceid  + "," + rowIndex + ")' class='invoiceMgrListviewOpenOper' style='margin-left:4px;' >查看</a>";

                            return operation;
                        }
                    }
                ]],
                onClickRow: this.clickDataGridRow,   //选择行事件
                onSelect:this.selectDataGridRow      //选择行事件
            });

            /*定义订单分页表*/
            var pager = $('#InvoiceMgrHandleTable').datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep','first','prev','manual','links','next','last']
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
        selectDataGridRow:function(rowIndex,rowData){
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
                    var data = {'page': pageNumber};
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
            var data = {'page': pageNumber};
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
        }
        ,

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

        }
        ,

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
        openInvoice: function (invoiceid,rowIndex ) {
            this.refreshOrder = false;
            $.messager.progress({text: '处理中，请稍候...'});
            var that = this;
            //获取发票所需要的信息
            $.ajax({
                type: "GET",
                url: "__URL__/getInvoiceInfo/invoiceid/"+invoiceid,
                dataType: "json",
                success: function (data) {
                    if(!data){
                        alert('没有开票数据!');
                        $.messager.progress('close');
                    }
                    if(data.type == 3){ //电子票
                        var invoiceOperState = printEticketInvoice(data);  //打印电子发票
                    }
                    if(data.type == 2){ //普通票
                        if(data.city == 'bj'){  //北京用的是航天金税的发票系统
                            var invoiceOperState = InvoiceOper.openInvoice(data);
                        }
                    }

                    if(invoiceOperState){
                        $.messager.progress('close');
                        //更新打印状态
                        that.invoiceMgrHandleGrid.datagrid('updateRow', {
                            index: rowIndex,    //定位行
                            row: {
                                state: '已开票'
                            }
                        });
                        // 设定订单状态为已打印
                        $.ajax({
                            type: "GET",
                            url: "__URL__/setInvoiceOpened/invoiceid/"+invoiceid,
                            dataType: "json",
                            success: function (data) {
                            }
                        })

                        that.invoiceMgrHandleGrid.datagrid('selectRow', rowIndex);
                        that.taskOrderNumber = this.taskOrderNumber - 1;  //为了启动快速刷新，减1
                        that.refreshOrder = true;

                    }else{
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
        cancelOpenInvoice :function(invoiceid,invoicetxt){
            var that = this;
            $.messager.confirm('提示信息', invoicetxt + '<center><font color="red">确定要不开发票吗？</font></center>', function (result) {
                if (!result) return false;
                $.messager.progress({text: '处理中，请稍候...'});
                $.get("<?php echo U('InvoiceMgr/cancelInvoice');?>", {invoiceid: invoiceid}, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that. quickRefreshInvoice();
                    }
                }, 'json');
            });

        },


        /**
         * 页面快键键设置
         */
        quickKeyboardAction : function () {

        },

        createview : function () {
            InvoiceOper.closeCard();
            this.closeInterval();
            IndexIndexModule.updateOperateTab('__URL__/createview');
        },

        //编辑
        editview: function (id,rowIndex) {
            InvoiceOper.closeCard();
            this.closeInterval();
            var url = "<?php echo U('InvoiceMgr/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },

        //查看
        detailview: function (id,rowIndex) {
            InvoiceOper.closeCard();
            this.closeInterval();
            var url = "<?php echo U('InvoiceMgr/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            IndexIndexModule.updateOperateTab(url);
        },


        /**
         * 开启刷新
         */
        setRefresh : function(){
            this.refreshOrder = true;
        },

        //定义关不定时任务
        closeInterval:function(){
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
        InvoiceMgrListviewModule.init();
    })
</script>