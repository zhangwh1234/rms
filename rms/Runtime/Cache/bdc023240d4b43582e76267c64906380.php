<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="updateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;历史订单查询操作</li>
        <li style="width: 50px;">&nbsp;</li>


        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName"
                                                        onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
    <table id="orderhistory_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>

<div id="orderhistory-searchview-datagrid-toolbar" style="padding:5px;height:auto">
    <form>
        查询选项
        <select name="searchOption" id="searchOption" class="txtBox" style="width:100px">
            <?php if($searchOptionValue): ?><option value="<?php echo ($searchOptionValue); ?>"><?php echo (L("$searchOptionValue")); ?></option>
                <?php else: ?>
                <option>全部</option><?php endif; ?>
            <?php if(is_array($searchOption)): foreach($searchOption as $key=>$value): ?><option value="<?php echo ($value); ?>"><?php echo (L("$value")); ?></option><?php endforeach; endif; ?>
        </select>
        查询内容
        <input id="searchText" name="searchText" type="text" style="width:240px" value="<?php echo ($searchTextValue); ?>">
        开始日期:
        <input id="startDate" name="startDate" type="text" class="easyui-datebox" required="required"
               value="<?php echo ($startDate); ?>" style="width:100px">
        结束日期:
        <input id="endDate" name="endDate" type="text" class="easyui-datebox" required="required" value="<?php echo ($endDate); ?>"
               style="width:100px">
        午别：
        <select name="searchAp" id="searchAp" class="txtBox" style="width:150px">
            <?php if($searchAp): ?><option value="<?php echo ($searchAp); ?>"><?php echo ($searchAp); ?></option><?php endif; ?>
            <option value="全天">全天</option>
            <option value="上午">上午</option>
            <option value="下午">下午</option>
        </select>
        <a href="javascript:;" onclick="OrderHistorySearchviewModule.search(this);" class="easyui-linkbutton"
           iconCls="icons-table-table">查询</a>
    </form>
</div>


<script>
    var OrderHistorySearchviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#orderhistory_index_datagrid',
        //初始化
        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
        },

        //重新设置page
        setPagination: function () {
            //定义订单分页表
            var pager = $('#orderhistory_index_datagrid').datagrid().datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep', 'first', 'links', 'last']
            });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="OrderHistorySearchviewModule.detailview(' + rowData.ordersn + ')">查看</a>');
            return btn.join(' | ');
        },

        //查看记录
        detailview: function (id) {
            var that = this;
            $(that.dialog).dialog({
                title: '历史订单详情',
                iconCls: 'icons-application-application_add',
                width: 1000,
                height: 540,
                cache: false,
                href: "__URL__/detailview/ordersn/" + id,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '打印历史订单',
                    iconCls: 'icons-printer-printer',
                    handler: function () {
                        $.messager.progress({text: '打印中，请稍候...'});
                        that.orderPrintData(id);
                        setTimeout(function () {
                            $.messager.progress('close');
                        }, 3000);

                    }
                }, {
                    text: '关闭',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        //搜索
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            $(this.datagrid).datagrid({pageNumber: 1});
        },

        // 调度主界面打印程序
        orderPrintData: function (ordersn) {
            var that = this;
            // 取得打印的内容
            $.ajax({
                type: "POST",
                url: APP + "/OrderHistory/getPrintOrder/ordersn/" + ordersn,
                dataType: "json",
                success: function (data) {
                    that.printOrderForm(data);
                }

            })
        },

        // 实际打印
        printOrderForm: function (data) {
            var printPage = $.cookie('rmsPrintPage');  //localStorage['printPage'];  // 取得打印纸张类型
            if (printPage == '') {
                alert('请设置打印纸张类型');
            }  else if (printPage == '80hot') {
                this.print_80(data);
            }  else {
                alert('没有这样的打印纸张类型');
            }

        },


        // 80宽热敏的打印代码
        print_80: function (data) {
            var print_index = $.cookie('rmsPrinterIndex'); // 读取 cookie,打印机类型
            if (print_index < 0) {
                alert('请设置打印机');
                return;
            }
            // 定义行号
            var linespacing = 14;
            var row = 0;  // 循环变量
            // 重新设置打印机的设备
            LODOP.SET_PRINTER_INDEX(print_index);
            LODOP.PRINT_INIT("printorder");


            // ********** 送餐联 *****************
            // 送餐单标题
            LODOP.SET_PRINT_STYLE("FontSize", 16);
            row = row;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '送餐单');

            // 订单日期号
            var orderformid = '日期:' + data['orderform'].custdate +
                    ' 打印:' + data['orderform'].printnumber +
                    ' 订单:' + data['orderform'].orderformid;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

            // 打印条码
            LODOP.SET_PRINT_STYLEA, (0, "FontSize", 10);
            linespacing = 17;
            row = row + 1;
            LODOP.ADD_PRINT_BARCODE(linespacing * row, 0, 260, 40, '128Auto', data['orderform'].orderformid);

            // 地址
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            row = row + 1;
            address = '地址:' + data['orderform'].address;
            address1 = address.subCHStr(0, 30);
            address2 = address.subCHStr(30, 30);
            address3 = address.subCHStr(60, 30);
            address4 = address.subCHStr(90, 30);

            if (address1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
            }
            if (address2.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
            }
            if (address3.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
            }
            if (address4.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
            }


            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            // 电话
            var telphone = '电话:' + data['orderform'].telphone;
            // 客户
            var clientname = '客户:' + data['orderform'].clientname;
            // 要餐时间
            var custtime = '要餐时间:' + data['orderform'].custtime;
            // 订餐时间
            var teltime = '下单时间:' + data['orderform'].rectime;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + custtime);
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, clientname + '  ' + teltime);
            // 备注
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            var beizhu = '备注:' + data['orderform'].beizhu;
            beizhu1 = beizhu.subCHStr(0, 30);
            beizhu2 = beizhu.subCHStr(30, 30);
            beizhu3 = beizhu.subCHStr(60, 30);
            beizhu4 = beizhu.subCHStr(90, 30);
            if (beizhu1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
            }
            if (beizhu2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
            }
            if (beizhu3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
            }
            if (beizhu4.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
            }

            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '宋体');
            linespacing = 19;
            // 商品打印
            var productsTitle = '名称        数量    单价   金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
            if (data['orderproducts']) {
                $.each(data['orderproducts'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
                    // 产品数量
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
                    // 单价
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
                })
            }
            // 送餐费金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            var shippingmoney = '送餐费:' + data['orderform'].shippingmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);

            // 总金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            var totalmoney = '总金额:' + data['orderform'].totalmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, totalmoney);
            //活动打印
            if (data['orderactivity']) {
                var activityTitle = '营销活动:    名称          金额';
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, activityTitle);
                $.each(data['orderactivity'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
                })
            }
            //支付打印
            if (data['orderpayment']) {
                var paymentTitle = '订单支付:     名称           金额';
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, paymentTitle);
                $.each(data['orderpayment'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
                })
            }
            // 客户还需付款金额
            LODOP.SET_PRINT_STYLE("FontSize", 14);
            linespacing = 19;
            row = row + 1;
            var shouldmoney = '客户还需付款金额:' + data['orderform'].shouldmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shouldmoney);

            LODOP.SET_PRINT_STYLE("FontSize", 10);
            // 发票抬头和发票内容
            if (data['orderform'].invoiceheader.length > 0) {
                var invoice = '发票:' + data['orderform'].invoiceheader + ' 内容:'; // +
                                                                                // data['orderform'].invoicebody;
                invoice1 = invoice.subCHStr(0, 30);
                invoice2 = invoice.subCHStr(30, 30);
                if (invoice1.length > 0) {
                    row = row + 1;
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
                }
                if (invoice2.length > 0) {
                    row = row + 1;
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
                }
            }


            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

            LODOP.PRINT();


            // ********** 领餐联 *****************
            // 送餐单标题
            LODOP.SET_PRINT_STYLE("FontSize", 16);
            row = 0;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '领餐单');

            //打印订单号
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

            // 地址
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            address = '地址:' + data['orderform'].address;
            address1 = address.subCHStr(0, 30);
            address2 = address.subCHStr(30, 30);
            address3 = address.subCHStr(60, 30);
            address4 = address.subCHStr(90, 30);

            if (address1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
            }
            if (address2.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
            }
            if (address3.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
            }
            if (address4.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
            }


            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            // 电话
            telphone = '电话:' + data['orderform'].telphone;
            // 客户
            clientname = '客户:' + data['orderform'].clientname;
            // 要餐时间
            custtime = '要餐时间:' + data['orderform'].custtime;
            // 订餐时间
            teltime = '下单时间:' + data['orderform'].rectime;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + custtime);
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, clientname + '  ' + teltime);
            // 备注
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            beizhu = '备注:' + data['orderform'].beizhu;
            beizhu1 = beizhu.subCHStr(0, 30);
            beizhu2 = beizhu.subCHStr(30, 30);
            beizhu3 = beizhu.subCHStr(60, 30);
            beizhu4 = beizhu.subCHStr(90, 30);
            if (beizhu1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
            }
            if (beizhu2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
            }
            if (beizhu3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
            }
            if (beizhu4.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
            }

            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '宋体');
            linespacing = 19;
            // 商品打印
            var productsTitle = '名称        数量    单价   金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
            if (data['orderproducts']) {
                $.each(data['orderproducts'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
                    // 产品数量
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
                    // 单价
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
                })
            }
            // 总金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            totalmoney = '总金额:' + data['orderform'].totalmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);

            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

            LODOP.PRINT();


            // ********** 调度联 *****************
            // 送餐单标题
            LODOP.SET_PRINT_STYLE("FontSize", 16);
            row = 0;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '调度联');


            //打印订单号
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

            // 打印条码
            LODOP.SET_PRINT_STYLEA, (0, "FontSize", 10);
            linespacing = 17;
            row = row + 1;
            LODOP.ADD_PRINT_BARCODE(linespacing * row, 0, 260, 40, '128Auto', data['orderform'].orderformid);

            // 地址
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            row = row + 1;
            address = '地址:' + data['orderform'].address;
            address1 = address.subCHStr(0, 30);
            address2 = address.subCHStr(30, 30);
            address3 = address.subCHStr(60, 30);
            address4 = address.subCHStr(90, 30);

            if (address1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
            }
            if (address2.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
            }
            if (address3.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
            }
            if (address4.length > 0) {
                row = row + 1;
                linespacing = 19;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
            }


            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            // 电话
            telphone = '电话:' + data['orderform'].telphone;
            // 客户
            clientname = '客户:' + data['orderform'].clientname;
            // 要餐时间
            custtime = '要餐时间:' + data['orderform'].custtime;
            // 订餐时间
            teltime = '下单时间:' + data['orderform'].rectime;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + custtime);
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, clientname + '  ' + teltime);
            // 备注
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '黑体');
            linespacing = 19;
            beizhu = '备注:' + data['orderform'].beizhu;
            beizhu1 = beizhu.subCHStr(0, 30);
            beizhu2 = beizhu.subCHStr(30, 30);
            beizhu3 = beizhu.subCHStr(60, 30);
            beizhu4 = beizhu.subCHStr(90, 30);
            if (beizhu1.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
            }
            if (beizhu2.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
            }
            if (beizhu3.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
            }
            if (beizhu4.length > 0) {
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
            }

            LODOP.SET_PRINT_STYLE("FontSize", 12);
            LODOP.SET_PRINT_STYLE('FontName', '宋体');
            linespacing = 19;
            // 商品打印
            var productsTitle = '名称        数量    单价   金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
            if (data['orderproducts']) {
                $.each(data['orderproducts'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
                    // 产品数量
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
                    // 单价
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
                })
            }
            // 送餐费金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            var shippingmoney = '送餐费:' + data['orderform'].shippingmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);
            // 总金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            totalmoney = '总金额:' + data['orderform'].totalmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, totalmoney);

            //活动打印
            if (data['orderactivity']) {
                var activityTitle = '营销活动:    名称          金额';
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, activityTitle);
                $.each(data['orderactivity'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
                })
            }
            //支付打印
            if (data['orderpayment']) {
                var paymentTitle = '订单支付:     名称           金额';
                row = row + 1;
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, paymentTitle);
                $.each(data['orderpayment'], function (key, value) {
                    linespacing = 19;
                    row = row + 1;
                    // 产品名称
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                    // 金额
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
                })
            }
            // 客户还需付款金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            var shouldmoney = '客户还需付款金额:' + data['orderform'].shouldmoney;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shouldmoney);


            // 发票抬头和发票内容
            if (data['orderform'].invoiceheader.length > 0) {
                invoice = '发票:' + data['orderform'].invoiceheader + ' 内容:'; // +
                                                                            // data['orderform'].invoicebody;
                invoice1 = invoice.subCHStr(0, 30);
                invoice2 = invoice.subCHStr(30, 30);
                if (invoice1.length > 0) {
                    row = row + 1;
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
                }
                if (invoice2.length > 0) {
                    row = row + 1;
                    LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
                }
            }


            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

            LODOP.PRINT();

        }


    }

    $(function () {
        OrderHistorySearchviewModule.init();
        setTimeout(function () {
            OrderHistorySearchviewModule.setPagination();
        }, 100);

    })
</script>