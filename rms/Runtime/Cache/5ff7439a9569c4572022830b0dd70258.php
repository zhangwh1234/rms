<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>



        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="OrderFormListviewModule.addressSearchInput();"><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="OrderFormListviewModule.addressSearchInput();">发货查询<span>^6</span></a></li>


        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div id="orderDeliverListviewDiv" class="easyui-layout" style="width:100%;height:400px;">
    <div data-options="region:'west',split:true,border:true"
         style="width: 300px;">
        <div id="goods" class="easyui-layout" fit="true">
            <div data-options="region:'center',border:false"
                 style="padding: 0px; background: #eee;">
                <table id="OrderDeliverProductsHandleTable" style="width: 300px;">
                </table>
            </div>
            <div data-options="region:'south',split:true,border:false"
                 style="height: 30px;">
                <div id="orderDeliverTotalNumber">总金额</div>
            </div>
        </div>
    </div>
    <div data-options="region:'center',border:false"
         style="padding: 0px; background: #eee;">
        <table id="OrderDeliverFormHandleTable" fit="true"></table>
    </div>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview"/>

<script>

    var OrderDeliverListviewModule = {


        init: function () {
            this.orderDeliverFormHandleGrid = $('#OrderDeliverFormHandleTable');
            this.orderDeliverProductsHandleGrid = $('#OrderDeliverProductsHandleTable');
            //设置div的高度
            $('#orderDeliverListviewDiv').height(IndexIndexModule.operationHeight);
            //订单处理表格
            this.setDatagrid();
            //初始化订单数据
        },

        //设置订单处理表格
        setDatagrid: function () {
            /*定义订单Orderform处理表*/
            this.orderDeliverFormHandleGrid.datagrid({
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
                rowStyler: function (index, row) { //处理订单，状态改为已，就改变背景颜色，以便区别
                    state = row.state;
                    if (state.indexOf('已') >= 0) {
                        return 'background-color:#6293BB;color:red;'; // return inline style
                    }
                },
                columns: [[
                    {field: 'orderformid', title: 'id', hidden: 'true', width: 100},
                    {field: 'address', title: '地址', width: 210, align: 'center'},
                    {
                        field: "operation",
                        width: 30,
                        title: '操作',
                        align: 'center',
                        formatter: function (value, rowData, rowIndex) {
                            var operation;
                            operation = "<input class='orderHandleCls' id='orderHandle" + rowIndex + "'  name='orderHandle" + rowIndex + "' type='text'  size='6' onkeyup='OrderHandleListviewModule.orderHandle(event,this," + rowData.orderformid + "," + rowIndex + ")'  " +
                                    "   onclick='OrderHandleListviewModule.orderHandleClick(" +rowIndex+","+ rowData.orderformid + ");' " +
                                    "   onfocus='OrderHandleListviewModule.orderHandleFocus(" + rowData.orderformid + ");' >";
                            operation += "<a href='javascript:void(0);' onclick='IndexIndexModule.updateOperateTab(&apos;__URL__/detailview/record/" + rowData.orderformid + "/returnAction/listview&apos;)' class='orderHandleDetailview'  style='margin-left:4px;'>发货</a>";
                            return operation;
                        }
                    }
                ]],
                onClickRow: this.clickDataGridRow,   //选择行事件
                onSelect:this.selectDataGridRow      //选择行事件
            });

            /*定义订单分页表*/
            var pager = $('#OrderDeliverFormHandleTable').datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize : (IndexIndexModule.gridRowsNumber - 2),
                layout: ['sep','first','prev','manual','links','next','last']
            });

            /*定义订货OrderGoods显示表*/
            this.orderDeliverProductsHandleGrid.datagrid({
                nowrap: false,
                columns: [[
                    {field: 'number', title: '数量', align:'center', width: 80},
                    {field: 'name', title: '名称', align:'center', width: 220}
                ]]
            });
        }
    }


    $(function(){
        OrderDeliverListviewModule.init();
    })
</script>