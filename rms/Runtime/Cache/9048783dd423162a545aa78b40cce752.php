<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    /*查看*/
    .distributionDetailview{
        font-size: 16px;
        float: left;
        width: 35px;
        margin-left: 2px;
        line-height: 18px;
    }

    /*处理订单的输入框*/
    .distributionOrder {
        float: left;
        width: 40px;
    }

    /*定义备注字段*/
    #beizhuOrderDistribution .l-btn-text {
        font-size: 16px;
        color: red;
        margin-right: 0px;
    }

    #orderDistributionOtherInfo {
        height: 18px;
        padding-left: 20px;
        margin-top: 2px;
        font-size: 16px;
        border-bottom: 1px solid black;
    }

    #orderDistributionMonit {
        height: 18px;
        padding-left: 20px;
        margin-top: 2px;
        font-size: 16px;
    }

</style>

<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" class="moduleName" onclick="updateTab('__URL__/listview');">
            &nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="OrderDistributionListviewModule.addressSearchInput();">地址查询<span>⌃6</span></a>
        </li>
        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/sendnameBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName" onclick="OrderDistributionListviewModule.companySearchInput();">配送店查询<span>⌃7</span></a>
        </li>
        <li style="width: 20px;">&nbsp;</li>
        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="OrderDistributionListviewModule.otherSearchInput();">综合查询<span>⌃8</span></a>
        </li>
        <li style="width: 20px;">&nbsp;</li>
        <li>
            <a id="showordernumberap" onclick="OrderDistributionListviewModule.orderNumberAp();" style="color:red;cursor:pointer">下午订单</a>
        </li>
        <li style="float: right;margin-right: 40px;"><a href="javascript:;" class="moduleName"
                                                        onclick="OrderDistributionListviewModule.closeTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="OrderDistributionListviewModule.closeTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="orderDistributionListviewDiv" class="easyui-layout"  border="false" style="width:100%;">
    <div data-options="region:'west',split:true,border:true" style="width:100px;">
        <div id="goodsDistribuition" class="easyui-layout" fit="true">
            <div data-options="region:'center',border:false" style="padding:0px;background:#eee;">
                <table id="orderProductsDistributionTable" style="width: 100px;"></table>
            </div>
            <div data-options="region:'south',split:true,border:false" style="height: 30px;">
                <div>总金额</div>
            </div>
        </div>
    </div>
    <div data-options="region:'center',border:false" style="padding:0px;background:#eee;">
        <table id="orderFormDistributionTable" fit="true"></table>
    </div>
    <div data-options="region:'south',split:false,border:false" style="height:46px;">
        <div class="pagestop">
            <div id="orderDistributionOtherInfo" style="height: 22px;" align="center">
                接线员:
            </div>
            <div id="orderDistributionMonit" align="center"></div>
        </div>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />

<script>
    var OrderDistributionListviewModule = {
        dialog: '#globel-dialog-div',
        orderFormDistributionGrid: '#',      //订单处理表
        orderProductsDistributionGrid: '',   //订货表

        focusNumberOD: 0,  //定义光标的数组;OD是orderformDistribution的缩写
        focusOrderformidOD: 0, //定义光标的订单号
        ODrefreshOrder: true,  //定义刷新标志，默认是开启刷新
        refreshNumberVal: 0,
        firstOrderNumber: 0,  //刷新后得到的订单数量
        taskOrderNumber: 0,   //处理后的订单数量

        //定义分公司分配数组
        compayMgrName: {},

        //定义定时任务名称
        firstGetOrderInterval:0,
        slowGetOrderInterval:0,
        orderMonitInterval:0,

        //定义缓存分配的数据变量
        distributionData : new Array(),

        //初始化
        init: function () {
            this.orderFormDistributionGrid = $('#orderFormDistributionTable');
            this.orderProductsDistributionGrid = $('#orderProductsDistributionTable');
            //设置div的高度
            $('#orderDistributionListviewDiv').height(IndexIndexModule.operationHeight);
            //订单处理表格
            this.setDatagrid();
            this.dataPage();    //启动表格分页事件
            this.firstGetOrderForm();
            this.getCompany();          //获取分公司代码
            this.gridKeyboardMove();  //键盘移动
            //启动刷新订单进程
            this.quickRefreshOrderForm();
            this.slowkRefreshOrderForm();
            //显示 订单情况
            this.refreshOrderDistributionMonit();
            //启动选项卡点击事件
            //this.selectTab();
            this.quickKeyboardAction();

            IndexIndexModule.calculateMaxRows();

            this.showOrderNumberAp();
        },


        //设置订单分配的表格
        setDatagrid: function () {
            //定义订单orderform处理表
            this.orderFormDistributionGrid.datagrid({
                nowrap: false,
                fitColumns: true,
                singleSelect: true,
                autoRowHeight: true,
                striped: true,
                border: false,
                rownumbers: false,  //显示行号
                showFooter: true,
                pagination: true,
                pagePosition: 'bottom',
                toolbar: '#tb',
                columns: [[
                    {field: 'orderformid', title: 'id', hidden: 'true', width: 100},
                    {field: 'address', title: '地址', width: 100, align: 'left'},
                    {field: 'ordertxt', title: '数量规格', width: 80, align: 'center'},
                    {
                        field: "operation",
                        width: 44,
                        title: '操作',
                        formatter: function (value, rowData, rowIndex) {
                            var operation;
                            operation = "<input class='distributionOrder' id='orderTask" + rowIndex + "' name='orderTask" + rowIndex + "' type='text'  size='8' maxlength='1' onkeyup='OrderDistributionListviewModule.distributionOrder(event,this," + rowData.orderformid + "," + rowIndex + ");' onClick='OrderDistributionListviewModule.distributionClick(" + rowData.orderformid + ");' onfocus='OrderDistributionListviewModule.distributionFocus(" + rowData.orderformid + ");' tabindex='" + rowIndex + "'  >" + "";
                            operation += "<a href='javascript:void(0);' class='distributionDetailview' onclick='OrderDistributionListviewModule.detailview("+rowData.orderformid + ','+ rowIndex+");' style='margin-left:1px;'>查看</a>";
                            operation += "<a href='javascript:void(0);' class='distributionDetailview' onclick='OrderDistributionListviewModule.cancelview("+rowData.orderformid + ','+ rowIndex+ ");' style='margin-left:0px;'>作废</a>";
                            return operation;
                        }
                    },
                    {field: "totalmoney", width: 30, title: '金额'},
                    {field: "telphone", width: 50, title: '电话', align: 'center'},
                    {field: "custtime", width: 30, title: '要餐时间'},
                    {field: "state", width: 20, title: '状态'}
                ]],
                onClickRow: this.clickDataGridRow,   //鼠标点击行事件
                onSelect:this.selectDataGridRow      //选择行事件
            });


            //定义订单分页表
            var pager = $('#orderFormDistributionTable').datagrid().datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize : (IndexIndexModule.gridRowsNumber - 2),
                layout: ['sep','first','prev','manual','links','next','last'],
                buttons: [{
                    id: 'beizhuOrderDistribution',
                    text: '备注:'

                }]
            });

            //定义订货OrderGoods显示表
            this.orderProductsDistributionGrid.datagrid({
                nowrap: false,
                columns: [[
                    {field: 'number', title: '数量', width: 30},
                    {field: 'name', title: '名称', width: 50}
                ]]
            });
        },

        //单击订单表格一行
        clickDataGridRow: function (rowIndex, rowData) {
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                //缓存光标位置
                OrderDistributionListviewModule.focusNumberOD = rowIndex;
                OrderDistributionListviewModule.focusOrderformidOD = rowData.orderformid;
                //当前行输入框获得焦点
                $('#orderTask' + rowIndex).focus();
                //显示货物
                OrderDistributionListviewModule.orderProductsShow(OrderDistributionListviewModule.focusOrderformidOD);
            }
        },

        //选择行事件
        selectDataGridRow:function(rowIndex,rowData){
            //显示当前行订单的订货的内容
            if (rowData) { //初始化的时候，可能没有数据
                //显示备注
                $('#beizhuOrderDistribution').linkbutton({text: '备注:' + rowData.beizhu});
                $('#orderDistributionOtherInfo').html('录入员:' + rowData.telname + ' 录入时间:' + rowData.rectime
                + ' 催送次数: 催送时间: 更改人: 更改时间:');
            };
        },

        /**
         * 订单表获得焦点后，显示订货的内容
         */
        orderProductsShow: function (orderformid) {
            var that = this;
            $.ajax({
                type: "GET",
                url: "__URL__/showproducts/orderformid/" + orderformid,
                dataType: "json",
                success: function (data) {
                    if (!data) {
                        var orderProducts = new Array();
                        that.orderProductsDistributionGrid.datagrid('loadData', orderProducts);
                        return;
                    }
                    that.orderProductsDistributionGrid.datagrid('loadData', data);

                }
            });
        },

        /**
         * 表格的分页事件
         */
        dataPage: function () {
            var that = this;
            this.orderFormDistributionGrid.datagrid('getPager').pagination({
                onSelectPage: function (pageNumber, pageSize) {
                    var data = {'page':pageNumber};
                    $.ajax({
                        type: "POST",
                        url: "__URL__/listview",
                        data:data,
                        dataType: "json",
                        success: function (data) {
                            //选择第一行焦点
                            that.orderFormDistributionGrid.datagrid('loadData', data);
                            that.focusNumberOD = 0;  //初始定位
                            //缓存订单号
                            that.focusOrderformidOD = data.rows[0].orderformid;
                            //行选中
                            that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);
                            //显示焦点
                            $('#orderTask' + that.focusNumberOD).focus();
                            //显示货物
                            that.orderProductsShow(that.focusOrderformidOD);
                        }
                    })

                }
            });

        },

        /**
         * 单击，选中表格
         */
        distributionClick: function (orderformid) {
            //更新焦点订单号
            this.focusOrderformidOD = orderformid;
            //行选中
            this.orderFormDistributionGrid.datagrid('selectRow', this.focusNumberOD);
            //显示货物
            this.orderProductsShow(this.focusOrderformidOD);
        },

        /**
         * 获得焦点
         */
        distributionFocus:function(orderformid){
            //更新焦点订单号
            this.focusOrderformidOD = orderformid;

        },

        /**
         * 定义表格移动的键盘处理
         */
        gridKeyboardMove: function () {
            var that = this;
            this.orderFormDistributionGrid.datagrid('getPanel').panel('panel').attr('tabindex', 1).bind('keydown', function (e) {
                switch (e.keyCode) {
                    case 38: // up  上移动
                        if (that.focusNumberOD == 0) return;  //为0，就是到顶了，不用再移动了
                        that.focusNumberOD = that.focusNumberOD - 1;
                        that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);
                        $('#orderTask' + that.focusNumberOD).focus();
                        //显示货物
                        that.orderProductsShow(that.focusOrderformidOD);
                        break;
                    case 40: // down 下移动
                        var rowsObj = that.orderFormDistributionGrid.datagrid('getRows');  //返回当前页的记录
                        var rowLength = rowsObj.length - 1;
                        if (that.focusNumberOD == rowLength) return;  //到表格尾部了，就不用再移动了
                        that.focusNumberOD = that.focusNumberOD + 1;
                        that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);
                        $('#orderTask' + that.focusNumberOD).focus();
                        //显示货物
                        that.orderProductsShow(that.focusOrderformidOD);
                        break;
                }
            });
        },

        /**
         * 返回所有送餐公司
         */
        getCompany: function () {
            var that = this;
            //返回所有分公司的名称和分配代码
            $.ajax({
                type: "POST",
                url: '__APP__/OrderDistribution/getCompanyMgr',
                dataType: "json",
                success: function (data) {
                    if (!data)  return;
                    that.companyMgrName = data;
                }
            });
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
                    url: "__URL__/listview",
                    dataType: "json",
                    data:data,
                    success: function (data) {
                        if (data.total > 0) {
                            //选择第一行焦点
                            that.orderFormDistributionGrid.datagrid('loadData', data);
                            that.orderFormDistributionGrid.datagrid('getPager').pagination({'pageNumber':pageNumber});
                            that.focusNumberOD = rowIndex;  //初始定位
                            //缓存订单号
                            that.focusOrderformidOD = data.rows[rowIndex].orderformid;
                            //显示焦点
                            $('#orderTask' + that.focusNumberOD).focus();
                            //行选中
                            that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);
                            //初始化快速刷新订单的数量标志
                            that.firstOrderNumber = that.taskOrderNumber = data.total;

                        }
                    }
                });
            }, 300);
        },

        //启动启动，取得订单
        GetOrderFrom: function () {
            var that = this;
            //取得订单表页码
            var options = this.orderFormDistributionGrid.datagrid('getPager').pagination('options');
            var pageNumber = options.pageNumber; //页码
            //获取分配代码
            var json = {};
            for(var i=0;i<that.distributionData.length;i++)
            {
                json[i]=that.distributionData[i];
            }

            var companyCode =  JSON.stringify(json);
            //上传数据
            var data = {'page':pageNumber,'companyCode':companyCode};
            that.distributionData = new Array();

            $.ajax({
                type: "POST",
                url: "__URL__/listview/",
                data:data,
                dataType: "json",
                success: function (data) {
                    if (that.ODrefreshOrder == false) {
                        return false;
                    }
                    //选择第一行焦点
                    that.orderFormDistributionGrid.datagrid('loadData', data);
                    if (data.total == 0) {
                        //初始化快速刷新订单的数量标志
                        that.firstOrderNumber = that.taskOrderNumber = 0;
                        return false;
                    }

                    //循环判断定位光标订单号是否存在
                    for(i=0;i<data.rows.length;i++){
                        for(l=0;l<that.distributionData.length;l++){  //搜索分配类,是否有已经分配的订单
                            if(that.distributionData[l].orderformid == data.rows[i].orderformid){
                                $('#orderTask' + i).val(that.distributionData[l].company);
                            }
                        }
                        if(that.focusOrderformidOD == data.rows[i].orderformid){
                            that.focusNumberOD = i;
                            $('#orderTask' + that.focusNumberOD).focus();
                            //行选中
                            that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);
                            return true;
                        }
                    }
                    //定位光标订单号不存在,放在第一个光标处
                    that.focusNumberOD = 0;
                    $('#orderTask' + that.focusNumberOD).focus();
                    //行选中
                    that.orderFormDistributionGrid.datagrid('selectRow', that.focusNumberOD);

                    //初始化快速刷新订单的数量标志
                    that.firstOrderNumber = that.taskOrderNumber = data.total;
                }
            })
        },

        /**
         *  快速定时更新订单
         *  快速刷新，需要根据前台是否处理订单来定,如果前台处理了订单，就快速刷新订单
         **/
        quickRefreshOrderForm: function () {
            var that = this;
            this.firstGetOrderInterval = setInterval(function () {
                //判断前台是否处理订单
                //if (that.firstOrderNumber > 0 && that.taskOrderNumber >= 0 && that.firstOrderNumber > that.taskOrderNumber) {
                    if (that.ODrefreshOrder == false) {
                        return false;
                    }
                    that.GetOrderFrom();
                //}
            }, 1500);
        },

        /*慢速定时更新订单*/
        slowkRefreshOrderForm: function () {
            var that = this;
            this.slowGetOrderInterval =  setInterval(function () {
                if (that.ODrefreshOrder == false) {
                    return false;
                }
                //that.GetOrderFrom();
            }, 1000000);
        },


        /**
         * 定时刷新显示订单情况
         */
        refreshOrderDistributionMonit: function () {
            var that = this;
            this.orderMonitInterval =  setInterval(function () {
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
                            ordermonit += "网订量:" + data[0].web + '      ';
                            ordermonit += "总金额:" + data[0].totalmoney;
                            //alert(ordermonit);
                            $('#orderDistributionMonit').html(ordermonit);
                        }
                    }
                });
            }, 30000);
        },

        /**
         * 分配订单,要改写成从 orderformid找到订单，然后给订单改变值，这样，就不怕刷新了
         */
        distributionOrder: function (event, obj, orderformid, rowIndex) {
            //如果处理的不是焦点行，就不处理
            if (this.focusOrderformidOD != orderformid) return false;

            var findCompanyName = false;  //是否有分配名
            //定义根据输入的键，获得的分公司名称
            var event = event || window.event;
            var inputCode = event.which ? event.which : event.keyCode;
            inputValue = $(obj).val();
            inputValue = inputValue.substring(0,1);
            if(inputValue == '') return;  //如果例外是空,就不处理
            if ((inputCode >= 48 && inputCode <= 57) || (inputCode >= 65 && inputCode <= 90) || (inputCode >= 96 && inputCode <= 220)) {
                if(typeof this.companyMgrName[inputValue] == 'undefined') return false;  //如果例外是undefined,就退出
                this.ODrefreshOrder = false;
                $(obj).val(this.companyMgrName[inputValue]);
                this.taskOrderNumber = this.taskOrderNumber - 1;  //为了启动快速刷新，减1
                findCompanyName = true;  //分配名称存在
                rowIndex += 1;
                $('#orderTask' + rowIndex).focus(); //下一个节点获得焦点
                var url = "__URL__/orderDistributionByCode/orderformid/" + orderformid + "/code/" + inputValue;
                var companyObj = {
                    'orderformid' : orderformid,
                    'code' : inputValue,
                    'company':this.companyMgrName[inputValue]
                }
                this.distributionData.push(companyObj);
                //$.get(url);
                this.ODrefreshOrder = true; //恢复刷新
                this.focusNumberOD = this.focusNumberOD + 1;

                //*********************/
                return false;

                if (findCompanyName == false) {
                    $.messager.show({
                        title: '分配提示',
                        msg: '分配名称没有,代码输入有误!'+inputCode,
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
                    $(obj).val('');
                    this.ODrefreshOrder = true;  //恢复刷新
                }
            }

        },

        /**
         * 查看订单
         */
        detailview:function(orderformid,rowIndex){
            this.closeInterval();
            var url = "__URL__/detailview/record/" + orderformid + "/returnAction/listview"
                    + '/rowIndex/' + rowIndex+'/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        /**
         * 作废订单
         */
        cancelview:function(orderformid,rowIndex){
            this.closeInterval();
            var url = "__URL__/cancelview/record/" + orderformid + "/returnAction/listview"
                    + '/rowIndex/' + rowIndex+'/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        /**
         * 地址查询
         */
        addressSearchInput: function (event, obj) {
            this.ODrefreshOrder = false;   //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '分配地址查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderDistribution/searchAddressInput');?>",
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
                                    if((value.name == 'searchTextAddress') && (value.value == '')){
                                        value.value = '全部';
                                    }
                                    url = url + value.name + '/' + value.value;
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '分配地址查询');
                                that.ODrefreshOrder = true;   //开启刷新
                                $(that.dialog).dialog('close');
                                $('#orderTask' + that.focusNumberOD).focus();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        that.ODrefreshOrder = true;   //开启刷新
                        $(that.dialog).dialog('close');
                        $('#orderTask' + that.focusNumberOD).focus();

                    }
                }]
            });
        },

        /**
         * 配送门店查询的快捷键
         */
        companySearchInput: function (event, obj) {
            this.ODrefreshOrder = false;  //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '分配门店查询输入',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderDistribution/searchCompanyInput');?>",
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
                                var companyName = $('#orderDistributionListviewSearchInputCompanyName').val();
                                if(!companyName){
                                    alert('查询内容不能为空');
                                    return false;
                                }

                                var isValid = $(this).form('validate');
                                if (!isValid) return false;

                                var formArray = $(this).serializeArray();
                                var url = '__URL__/searchviewCompany/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value + '/';
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '分配配送店查询');
                                $(that.dialog).dialog('close');
                                that.ODrefreshOrder = true;   //开启刷新
                                $('#orderTask' + that.focusNumberOD).focus();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        that.ODrefreshOrder = true;   //开启刷新
                        $('#orderTask' + that.focusNumberOD).focus();
                    }
                }]
            });

        },

        //恢复刷新的功能
        setRefresh:function(){
            this.ODrefreshOrder= true;
        },


        /**
         * 其他查询
         */
        otherSearchInput: function (event, obj) {
            this.ODrefreshOrder = false;   //禁止刷新
            var that = this;
            $(that.dialog).dialog({
                title: '分配综合查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderDistribution/searchOtherInput');?>",
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
                                    if((value.name == 'searchTextOther') && (value.value == '')){
                                        value.value = '全部';
                                    }
                                    url = url + value.name + '/' + value.value;
                                });
                                url = encodeURI(url);
                                IndexIndexModule.openOperateTab(url, '分配综合查询');
                                that.ODrefreshOrder = true;   //开启刷新
                                $(that.dialog).dialog('close');
                                $('#orderTask' + that.focusNumberOD).focus();
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        that.ODrefreshOrder = true;   //开启刷新
                        $(that.dialog).dialog('close');
                        $('#orderTask' + that.focusNumberOD).focus();

                    }
                }]
            });
        },


        //其他查询功能
        doSearchOther: function (value) {
            var url = '__URL__/searchviewOther/searchTextOther/' + encodeURIComponent(value);
            if ($('#operation').tabs('exists', '分配其他查询')) {
                IndexIndexModule.updateOperateTab(url);
            } else {
                IndexIndexModule.openOperateTab(url, '分配其他查询');
            }

        },

        //下午订单显示
        orderNumberAp: function(){
            var url = '__URL__/orderNumberAp';
            if ($('#operation').tabs('exists', '下午订单')) {
                IndexIndexModule.openOperateTab(url, '下午订单');
            } else {
                IndexIndexModule.openOperateTab(url, '下午订单');
            }
        },

        //快捷操作
        quickKeyboardAction:function() {
            var that = this;
            // ctrl+6快捷键,
            Mousetrap.bind(['ctrl+6', 'ctrl+f6', 'f6'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单分配' && ($('#OrderDistributionAction').val() == 'Listview')) {
                    that.addressSearchInput();
                };
            });

            // ctrl+7快捷键,
            Mousetrap.bind(['ctrl+7', 'ctrl+f7', 'f7'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单分配' && ($('#OrderDistributionAction').val() == 'Listview')) {
                    that.companySearchInput();
                };
            });

            // ctrl+8快捷键,
            Mousetrap.bind(['ctrl+8', 'ctrl+f8', 'f8'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单分配' && ($('#OrderDistributionAction').val() == 'Listview')) {
                    that.otherSearchInput();
                };
            });

            // ESC键
            Mousetrap.bind('esc', function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订单分配') {
                    $(that.dialog).dialog('close');
                    $('#orderTask' + OrderDistributionListviewModule.focusNumberOD).val('');
                    $('#orderTask' + OrderDistributionListviewModule.focusNumberOD).focus();
                }
            });
        },

            //选项卡事件的代码
        selectTabFun:function(title,index){
            if(title == '订单分配'){
                $('#orderTask' + OrderDistributionModule.focusNumberOD).focus();
            }
        },

        //选项卡选择事件
        selectTab:function(){
            window.selectTabModule.addSelectTabFun(this.selectTabFun);
            $('#operation').tabs({
                onSelect:function(title,index){
                    window.selectTabModule.selectTab(title,index);
                }
            });
        },

        //显示下午的订单数量和金额
        showOrderNumberAp: function(){
            var orderNumber = '';
            var that = this;
            this.showOrderNumberApInterval =  setInterval(function () {
                var date = new Date();
                if(date.getHours() >= 17){
                    $('#showordernumberap').text('');
                    return;
                }
                $.ajax({
                    type: "GET",
                    url: "__URL__/getOrderNumberAp",
                    dataType: "json",
                    success: function (data) {
                        orderNumber = ''
                        orderNumber = "下午订单量:" + data.number + ' ';
                        orderNumber += "总金额:" + data.money + ' ';
                        $('#showordernumberap').text(orderNumber);
                    }
                });

            }, 10000);
        },
        //定义关不定时任务
        closeInterval:function(){
            //关闭定时任务
            clearInterval(this.firstGetOrderInterval);
            clearInterval(this.slowGetOrderInterval);
            clearInterval(this.orderMonitInterval);
            clearInterval(this.showOrderNumberApInterval);
        },

        //关闭选项卡
        closeTab: function () {
            //关闭定时显示订单状态的定时器
            //clearInterval(refreshOrderHandleMonit);
            //返回选项卡
            var tab = $('#operation').tabs('getSelected');
            //返回选项卡的index
            var index = $('#operation').tabs('getTabIndex', tab);
            //关闭选项卡
            $('#operation').tabs('close', index);

            this.closeInterval();
        }
    }

    $(function () {
        OrderDistributionListviewModule.init();
        //点击空白地方，要让select row获得焦点
        $('#orderFormDistributionTable').datagrid('getPanel').panel('panel').focus(
                function(){
                    $('#orderTask' +  OrderDistributionListviewModule.focusNumberOD).focus(); //输入框恢复空
                }
        );
    })
</script>