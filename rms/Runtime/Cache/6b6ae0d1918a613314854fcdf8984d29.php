<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;堂口操作</li>
        <li style="width: 50px;">&nbsp;</li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png"
                    alt="" title="" border="0"></a></li>
    </ul>
</div>
<div style="clear: both;"></div>
<form id="DiningSaleCreateviewForm" name="<?php echo ($moduleName); ?>CreateViewForm" method="post">
    <div class="moduleoperator">
        <div id="diningsaleCreateForm" class="easyui-layout" style="height: 100%;">
            <div data-options="region:'east',split:false" style="width:30%;">
                <div style="width:100%;height: 100%;">
                    <table id="diningSaleTongjiTable" fit="true"></table>
                </div>

                <div style="position: fixed;bottom:20px;height:30px;width:30%;margin:0;background-color:#E0E0E0;border:1px solid #ADADAD;font-size: 14px;">
                    <label style="line-height: 30px;margin-left:10px; ">总金额:</label><input id="diningsaletotalmoney"
                        value="0" readonly style="font-size: 16px;">
                </div>
            </div>
            <div data-options="region:'center'" style="padding:5px;background:#eee;">
                <div style="width: 100%;text-align:center;font-weight: 700;">
                    <span style="font-size:20px;line-height: 25px;">堂口收银</span>
                </div>
                <div>
                    <style>

    .productsTableHeader {
        background-color: #D1D0CE;
        font-size: 14px;
        height: 20px;
    }

    #productsTable td {
        height: 25px;
    }

    #productsTable span {
        font-size: 16px;
    }

    #productsTable input {
        font-size: 16px;
    }

</style>

<table id="DiningSaleCreateviewproductsTable" style="BORDER-COLLAPSE: collapse" borderColor="" cellSpacing="0" width="100%"
    align="center" border="1">
    <tr class="productsTableHeader">
        <td width="3%" align="center" class="productsTableHeaderLeftTd">序号</td>
        <td width="5%" align="center" class="productsTableHeaderLeftTd">数量</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">产品代码</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">产品名称</td>
        <td width="10%" align="center" class="productsTableHeaderLeftTd">单价</td>
        <td width="10%" align="center" class="productsTableHeaderLeftTd">金额</td>
        <td width="10%" align="center" class="productsTableHeaderRightTd">操作</td>
    </tr>
    <?php $__FOR_START_243582400__=0;$__FOR_END_243582400__=12;for($key=$__FOR_START_243582400__;$key < $__FOR_END_243582400__;$key+=1){ ?><tr>
            <td width="5%" align="center"><?php echo ($key+1); ?></td>
            <td width="8%" align="center"> 
                <input id="DiningSaleCreateviewproductsNumber_<?php echo ($key+1); ?>" name="productsNumber_<?php echo ($key+1); ?>" type="text" size="5"
                    tabindex="1" onkeydown="DiningSaleCreateviewGoodsModule.keydownSumProductsMoney(<?php echo ($key+1); ?>,event,this)"
                    onblur="DiningSaleCreateviewGoodsModule.blurkeydownSumProductsMoney(<?php echo ($key+1); ?>,this)" value=""
                    AUTOCOMPLETE="off" style="font-size:16px;" />
            </td>
            <td width="15%" align="center"> 
                <input id="DiningSaleCreateviewproductsCode_<?php echo ($key+1); ?>" name="productsCode_<?php echo ($key+1); ?>" type="text" size="10"
                    tabindex="1" onkeydown="DiningSaleCreateviewGoodsModule.getProductsByCode(<?php echo ($key+1); ?>,event,this);"
                    AUTOCOMPLETE="off" style="font-size:16px;" />

                <img id="DiningSaleCreateviewsearchIcon1" title="产品选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                    align="absmiddle" onclick="DiningSaleCreateviewGoodsModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>');" /><a
                    href="javascript:DiningSaleCreateviewGoodsModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>')">选择</a>
            </td>
            <td width="15%" align="center"> 
                <input id="DiningSaleCreateviewproductsName_<?php echo ($key+1); ?>" name="productsName_<?php echo ($key+1); ?>" type="text" size="20"
                    readonly="readonly" style="font-size:16px;" />
                <input id="DiningSaleCreateviewproductsShortName_<?php echo ($key+1); ?>" name="productsShortName_<?php echo ($key+1); ?>" size="30"
                    type="hidden" style="font-size:16px;" />
            </td>
            <td width="10%" align="center"> 
                <input id="DiningSaleCreateviewproductsPrice_<?php echo ($key+1); ?>" name="productsPrice_<?php echo ($key+1); ?>" type="text" size="10"
                    readonly="readonly" tabindex="1" onblur="DiningSaleCreateviewGoodsModule.blurkeydownSumProductsMoney(<?php echo ($key+1); ?>,this)"
                    value="" style="font-size:16px;" />
            </td>
            <td width="10%" align="center"> 
                <input id="DiningSaleCreateviewproductsMoney_<?php echo ($key+1); ?>" name="productsMoney_<?php echo ($key+1); ?>" type="text" size="10"
                    readonly="readonly" tabindex="1" value="" style="font-size:16px;" />
            </td>
            <td width="10%" align="center"><a href="#" onclick="DiningSaleCreateviewGoodsModule.clearProducts(<?php echo ($key+1); ?>);">清空产品</a>
            </td>
        </tr><?php } ?>


</table>
<table width="100%" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%" align="center"
    border="1">
    <tr>

        <td style="font-size: 16px;">
            <span>份数:</span>
            <span id="productsTotalNumber"></span>
        </td>
        <td class="productsTableXiaojiRightTd" style="font-size: 14px;"> 
            <span>小计</span>
            <input id="DiningSaleCreateviewproductsTotalMoney" name="DiningSaleCreateviewproductsTotalMoney" type="text"
                size="10" readonly="readonly" style="border: 0px;font-size: 14px;" value="<?php echo ($info["goodsmoney"]); ?>" />
        </td>
        <input id="DiningSaleCreateviewproductsLength" name="productsLength" type="hidden" value="<?php echo ($key+1); ?>" />
    </tr>
    <tr>
        <td style="font-size: 16px;">收银类型</td>
        <td style="font-size: 14px;">
            <select style="width:100px;" name="diningpaymenttypeone" id="diningpaymenttypeone">
                <?php if(is_array($paymentmgr)): foreach($paymentmgr as $key=>$vo): ?><option><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
            </select>
        </td>
        <td style="font-size: 14px;">
            <label>收银金额</label>
        </td>
        <td>
            <input id="diningpaymentmoneyone" name="diningpaymentmoneyone" style="font-size: 16px;" />
        </td>
    </tr>
    <tr>
        <td style="font-size: 16px;">收银类型</td>
        <td style="font-size: 14px;">
            <select style="width:100px;" name="diningpaymenttypetwo" id="diningpaymenttypetwo">
                <option></option>
                <?php if(is_array($paymentmgr)): foreach($paymentmgr as $key=>$vo): ?><option><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
            </select>
        </td>
        <td style="font-size: 14px;">
            <label>收银金额</label>
        </td>
        <td>
            <input id="diningpaymentmoneytwo" name="diningpaymentmoneytwo" style="font-size: 16px;" />
        </td>
    </tr>
</table>

<script type="text/javascript">
    DiningSaleCreateviewGoodsModule = {

        init: function () {

        },


        //添加产品
        addProducts: function () {
            //取得表格行的长度
            var rowNum = $("#DiningSaleCreateviewproductsTable tr").length;

            var tableTrAppend = "<tr> ";
            tableTrAppend += "<td width='5%' align='center' class='productsTableLeftTd'>" + rowNum + "</td> ";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='DiningSaleCreateviewproductsNumber_" + rowNum +
                "' name='productsNumber_" + rowNum +
                "' type='text' size='5' value='' tabindex='1' onkeydown='DiningSaleCreateviewGoodsEditViewModule.keydownSumProductsMoney(" +
                rowNum +
                ",event,this)' onblur='DiningSaleCreateviewGoodsEditViewModule.blurkeydownSumProductsMoney(" +
                rowNum + ",event,this)' value='' AUTOCOMPLETE='off' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input class='easyui-numberbox' id='DiningSaleCreateviewproductsCode_" + rowNum +
                "' name='productsCode_" + rowNum +
                "'  type='text' size='10' tabindex='1' onkeyup='DiningSaleCreateviewGoodsEditViewModule.getProductsByCode(" +
                rowNum + ",event,this)' style='font-size:16px;' />";
            tableTrAppend += "<img id='DiningSaleCreateviewsearchIcon1' title='产品选择' src='./" + PUBLIC +
                "/Images/products.gif' style='cursor: pointer;' align='absmiddle' onclick='DiningSaleCreateviewGoodsEditViewModule.productsPickList(\"__APP__/OrderForm/popupview/module/Products/row/" +
                rowNum + "\");' />";
            tableTrAppend +=
                "<a href='javascript:DiningSaleCreateviewGoodsModule.productsPickList(\"__URL__/popupProductsview/module/Products/row/" +
                rowNum + "\");' >选择</a>";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='DiningSaleCreateviewproductsName_" + rowNum + "' name='productsName_" +
                rowNum + "' type='text' size='30' readonly='readonly' style='font-size:16px;'  />";
            tableTrAppend += "<input id='DiningSaleCreateviewproductsShortName_" + rowNum +
                "' name='productsShortName_" + rowNum + "' type='hidden'  />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='DiningSaleCreateviewproductsPrice_" + rowNum +
                "' name='productsPrice_" + rowNum +
                "' type='text' size='10' readonly='readonly' tabindex='1' value='' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='DiningSaleCreateviewproductsMoney_" + rowNum +
                "' name='productsMoney_" + rowNum +
                "' type='text' size='10' readonly='readonly'  tabindex='1' value='' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend +=
                "<td width='10%' align='center' class='productsTableLeftTd'><a href='#' onclick='DiningSaleCreateviewGoodsModule.clearProducts(" +
                rowNum + ");' >清空产品</a></td>";
            tableTrAppend += "</tr>";
            $("#DiningSaleCreateviewproductsTable").append(tableTrAppend);
            $("#DiningSaleCreateviewproductsLength").attr("value", rowNum + 1); //表格行数保存
        },

        /* 键盘回车计算产品金额 */
        keydownSumProductsMoney: function (rowNum, evt, obj) {

            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                productsNumber = $("#DiningSaleCreateviewproductsNumber_" + rowNum).val();
                if ($("#DiningSaleCreateviewproductsNumber_" + rowNum).val() != '') { //如果不为空，才计算
                    if (productsNumber <= 0) {
                        alert('数量不能为负!');
                        $("#DiningSaleCreateviewproductsNumber_" + rowNum).val('');
                        return false;
                    }
                    this.sumProductsMoney(rowNum);
                }
                $("#DiningSaleCreateviewproductsCode_" + rowNum).focus();
            }
            //向上移动一个
            if (key == 38) {
                var focusNum = rowNum - 1;
                if (focusNum > 0) {
                    $("#DiningSaleCreateviewproductsCode_" + focusNum).focus();
                }
            }
            //向下移动
            if (key == 40) {
                $("#DiningSaleCreateviewproductsCode_" + rowNum).focus();
            }
        },

        //产品数量输入框失去焦点
        blurkeydownSumProductsMoney: function (rowNum, obj) {
            this.sumProductsMoney(rowNum);
        },

        /* 通过产品代码取得产品 */
        // rowNum 是行号，evt是firefox下的event事件，obj是this指针
        getProductsByCode: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            var code = $(obj).val();

            if ((key == 13) && (code.length > 0)) {
                this.getProducts(rowNum, code);
            }
            //向上移动
            if (key == 38) {
                $("#DiningSaleCreateviewproductsNumber_" + rowNum).focus();
            }
            //向下移动
            if (key == 40) {
                var focusNum = rowNum + 1;
                $("#DiningSaleCreateviewproductsNumber_" + focusNum).focus();
            }

        },

        /* 弹出窗口，选择产品 */
        //moduleName:产品名称  rowNum:行号 moduleName,rowNum
        productsPickList: function (url) {
            url = url + '/returnModule/' + 'DiningSaleCreateview';
            $('#globel-dialog-div').dialog({
                title: '选择产品',
                iconCls: 'icons-application-application_add',
                width: 900,
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
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {

                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
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

        /* 清空产品上的内容 */
        clearProducts: function (rowNum) {
            var productsCode = $("#DiningSaleCreateviewproductsCode_" + rowNum).val();
            var productsName = $("#DiningSaleCreateviewproductsName_" + rowNum).val();

            if ((productsCode == '') && (productsName == '')) {
                return;
            }
            if (confirm("是否要清空内容")) {
                $("#DiningSaleCreateviewproductsNumber_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsCode_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsName_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsShortName_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsPrice_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsMoney_" + rowNum).val(0);
                this.sumProductsMoney(rowNum);
            }
        },

        /*  删除产品最后一行 */
        delProducts: function () {
            //取得表格行的长度
            var rowNum = $("#DiningSaleCreateviewproductsTable tr").length;
            if (rowNum == 2) {
                alert("最后一行不能删除");
                return;
            }
            $("#DiningSaleCreateviewproductsTable tr:last").remove();
            $("#DiningSaleCreateviewproductsLength").attr("value", rowNum - 1); //表格行数保存
            this.sumProductsMoney(rowNum);

        },


        //根据用户输入的产品代码，输出产品名称和价格
        getProducts: function (rowNum, code) {
            var that = this;
            $.ajax({
                url: APP + '/Products' + "/getProductsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var productsName = '';
                    if (mydata) {
                        //首先检查父窗口表格是否有存在输入的代码和产品
                        var rowLength = $("#productsTable tr").length;
                        for (i = 1; i < rowLength; i++) {
                            productsName = $("DiningSaleCreateview#productsName_" + i).val();
                            if ((productsName == mydata.name) && (i != rowNum)) {
                                alert('产品已经存在,不能添加！');
                                return;
                            }
                        }
                        $("#DiningSaleCreateviewproductsName_" + rowNum).val(mydata.name);
                        $("#DiningSaleCreateviewproductsShortName_" + rowNum).val(mydata.shortname);
                        $("#DiningSaleCreateviewproductsPrice_" + rowNum).val(mydata.price);

                        var productsNumber = $("#DiningSaleCreateviewproductsNumber_" + rowNum).val(); //数量
                        //如果没有输入产品数量,就为零
                        if (productsNumber == '') {
                            $("#DiningSaleCreateviewproductsNumber_" + rowNum).focus();
                            return false;
                        } else {
                            if (that.IsNum(productsNumber) == false) {
                                alert('输入的产品数量不对!请检查');
                                $("#DiningSaleCreateviewproductsNumber_" + rowNum).focus();
                                return false;
                            }
                            if (productsNumber <= 0) {
                                alert('输入的产品数量不能为负!请检查');
                                $("#DiningSaleCreateviewproductsNumber_" + rowNum).val('');
                                $("#DiningSaleCreateviewproductsNumber_" + rowNum).focus();
                                return false;
                            }
                        }

                        //计算总的金额
                        that.sumProductsMoney(rowNum);
                        //跳转到下一行
                        var focusNum = rowNum + 1;
                        $("#DiningSaleCreateviewproductsNumber_" + focusNum).focus();
                    } else {
                        alert("输入的产品代码有错误，请重新输入！");
                        return;
                    }
                }

            })
        },


        /* 计算产品金额 */
        sumProductsMoney: function (rowNum) {
            var productsNumber = $("#DiningSaleCreateviewproductsNumber_" + rowNum).val(); //数量
            //如果没有输入产品数量,就为零
            if (productsNumber == '') {
                productsNumber = 0;
            } else {
                if (this.IsNum(productsNumber) == false) {
                    alert('输入的产品数量不对!请检查');
                    return false;
                }
                if (productsNumber <= 0) {
                    alert('输入的产品数量不能为负!请检查');
                    $("#DiningSaleCreateviewproductsNumber_" + rowNum).val('');
                    return false;
                }
            }
            var productsPrice = $("#DiningSaleCreateviewproductsPrice_" + rowNum).val(); //单价
            var productsMoney = 0;
            productsMoney = parseInt(productsNumber) * parseFloat(productsPrice);

            if (!isNaN(productsMoney)) {
                //写入
                $("#DiningSaleCreateviewproductsMoney_" + rowNum).val(productsMoney);
            }


            //计算全部的金额
            var totalMoney = 0;
            var productsTotalNumber = 0;
            //取得表格行的长度
            var rowLength = $("#DiningSaleCreateviewproductsTable tr").length;
            for (i = 1; i < rowLength; i++) {
                if ($("#DiningSaleCreateviewproductsMoney_" + i).val() > 0) {
                    totalMoney = totalMoney + parseFloat($("#DiningSaleCreateviewproductsMoney_" + i).val());
                }
                var productsNumberCount = $("#DiningSaleCreateviewproductsNumber_" + i).val();
                if (productsNumberCount == '') {
                    productsNumberCount = 0;
                }
                if (productsNumberCount > 0) {
                    productsTotalNumber = productsTotalNumber + parseInt($(
                        "#DiningSaleCreateviewproductsNumber_" + i).val());
                }
            }

            //写入总的金额
            //totalMoney = totalMoney.toFixed(2);
            $("#DiningSaleCreateviewproductsTotalMoney").val(totalMoney);
            $('#diningpaymentmoneyone').val(totalMoney);
            //下一个表格代码输入框显示焦点
            rowNum = rowNum + 0;
            //加上送餐费
            var shippingmoney = 0;
            var shippingmoneyVal = $('#DiningSaleCreateviewForm input[name=shippingmoney]').val();
            if (shippingmoneyVal) {
                shippingmoney = parseFloat(shippingmoneyVal);
            }
            totalMoney = totalMoney + shippingmoney;
            totalMoney = parseFloat(totalMoney).toFixed(2);
            $("#DiningSaleCreateviewFormTotalMoney").html(totalMoney + '元'); //订单总金额
            $("#DiningSaleCreateviewForm input[name=totalmoney]").val(totalMoney);
            $("#DiningSaleCreateviewFormShouldMoney").html(totalMoney + '元'); //应收金额
            $("#DiningSaleCreateviewForm input[name=shouldmoney]").val(totalMoney);
            $('#productsTotalNumber').html(productsTotalNumber + '份');
        },


        //产品代码失去焦点的计算金额
        blurSumProductsMoney: function (rowNum, obj) {
            if ($("#DiningSaleCreateviewproductsName_" + rowNum).val() != '') { //如果不为空，才计算
                sumProductsMoney(rowNum);
            }
        },

        //把产品价格单元设置为可改写
        setGoodsPrice: function (row) {
            $("#DiningSaleCreateviewproductsPrice_" + row).attr('readonly', false);
        },


        //判断是否为数字
        IsNum: function (s) {
            if (s != null && s != "") {
                return !isNaN(s);
            }
            return false;
        }

    }

    /*产品的js计算程序*/
    var productsAjax = false; //判断是否查询过产品代码，防止在ajax中按回车，反复执行，有个alert的小bug
</script>
                </div>
                <div class="" style="width:69%;height: 40px;line-height: 40px;text-align: center;
                    border:0px solid black;position: fixed;bottom: 25px;margin: 0px; "
                    onclick="diningsaleCreateviewModule.insert();">
                    <a class="easyui-linkbutton" style="width:100%;height: 40px;">
                        <label style="font-size: 20px;">出单</label>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    var diningsaleCreateviewModule = {
        diningSaleTongjiGrid: '', //收银统计表

        //初始化
        init: function () {
            this.diningSaleTongjiGrid = $('#diningSaleTongjiTable');
            $('.moduleoperator').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            this.saleList();
            this.setDatagrid();
        },


        //设置订单处理表格
        setDatagrid: function () {
            var that = this;
            /*定义订单Orderform处理表*/
            this.diningSaleTongjiGrid.datagrid({
                nowrap: false,
                fitColumns: true,
                singleSelect: true,
                autoRowHeight: true,
                striped: true,
                border: true,
                rownumbers: true, //显示行号
                showFooter: true,
                columns: [
                    [{
                            field: 'diningsaleid',
                            title: 'id',
                            hidden: 'true',
                            width: 100
                        },
                        {
                            field: "saletime",
                            width: 25,
                            title: '时间'
                        },
                        {
                            field: "money",
                            width: 25,
                            title: '金额'
                        },
                        {
                            field: 'productstxt',
                            title: '数量规格',
                            width: 60,
                            align: 'center'
                        }
                    ]
                ],
                onDblClickRow: function (index, row) {
                    that.dbClickPrint(row.diningsaleid);
                }
            });
        },

        //双击打印单据
        dbClickPrint: function (id) {
            var that = this;
            var data = {
                'id': id
            }
            $.ajax({
                type: "POST",
                url: "__URL__/getDiningSaleOrder",
                data: data,
                dataType: "json",
                success: function (res) {
                    //var res = eval('(' + res + ')');
                    //打印
                    that.print_80(res.data);
                }
            })
        },


        //保存记录
        insert: function () {
            if (this.checkProductstable() == false) {
                alert('没有金额,不能出单');
                return false;
            }
            var that = this;
            $('#DiningSaleCreateviewForm').form('submit', {
                url: '__URL__/insert',
                type: 'post',
                onSubmit: function () {
                    //对金额进行校验
                    productsMoney = $('#DiningSaleCreateviewproductsTotalMoney').val(); //产品金额
                    if (!productsMoney) {
                        productsMoney = 0;
                    } else {
                        productsMoney = parseFloat(productsMoney);
                    }
                    paymentOneMoney = $('#diningpaymentmoneyone').val(); //活动金额
                    if (!paymentOneMoney) {
                        paymentOneMoney = 0;
                    } else {
                        paymentOneMoney = parseFloat(paymentOneMoney);
                    }
                    paymentTwoMoney = $("#diningpaymentmoneytwo").val(); //客户支付金额
                    if (!paymentTwoMoney) {
                        paymentTwoMoney = 0;
                    } else {
                        paymentTwoMoney = parseFloat(paymentTwoMoney);
                    }
                    checkactivitypaymentMoney = paymentOneMoney + paymentTwoMoney;
                    if (productsMoney !== checkactivitypaymentMoney) {
                        alert('输入金额和支付金额不等于堂扣金额，请检查！');
                        return false;
                    }
                },
                success: function (res) {

                    var res = eval('(' + res + ')');
                    if (!res.status) {
                        $.app.method.tip('提示信息', data.info, 'error');
                    } else {
                        that.saleList();
                        //清除内容
                        that.clearProductsTable();

                        //打印
                        that.print_80(res.data);
                    }
                }
            });
        },

        //检查表格是否有内容
        checkProductstable: function () {
            var productsTableMoney = $('#DiningSaleCreateviewproductsTotalMoney').val();
            if (productsTableMoney > 0) {
                return true;
            } else {
                return false;
            }

        },

        //清除产品表格内容
        clearProductsTable: function () {
            for (var i = 0; i < 13; i++) {
                rowNum = i;
                $("#DiningSaleCreateviewproductsNumber_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsCode_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsName_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsShortName_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsPrice_" + rowNum).val('');
                $("#DiningSaleCreateviewproductsMoney_" + rowNum).val(0);
            }
            $('#diningpaymentmoneyone').val('');
            $('#diningpaymentmoneytwo').val('');
            $('#diningpaymenttypetwo').val('');
        },

        //放弃保存
        cancel: function () {
            var url = '__URL__/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+9快捷键,保存公告
            Mousetrap.bind(['ctrl+9', 'ctrl+f9', 'f9'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#MessagesAction').val() == 'Createview')) {
                    MessagesCreateviewModule.insert();
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#MessagesAction').val() == 'Createview')) {
                    IndexIndexModule.updateOperateTab('__URL__/listview');
                };
            });
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
            var row = 0; // 循环变量
            // 重新设置打印机的设备
            LODOP.PRINT_INIT("printorder");
            LODOP.SET_PRINTER_INDEX(print_index);


            // ********** 领餐联 *****************
            // 领餐单标题
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            row = row;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '堂口领餐联');

            // 出单号
            var diningsaleorder = '堂口:' + data['diningsale'].diningname + '  ' +
                ' 序号:' + data['diningsale'].sequence;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, diningsaleorder);

            var diningsaletime = '日期:' + data['diningsale'].date +
                ' 午别:' + data['diningsale'].ap + ' ' +
                data['diningsale'].saletime;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, diningsaletime);


            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '宋体');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            linespacing = 19;
            // 商品打印
            var productsTitle = '名称          数量     单价    金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
            if (data['diningsaleproducts']) {
                $.each(data['diningsaleproducts'], function (key, value) {
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
            var totalmoney = '总金额:' + data['diningsale'].money;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);

            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

            LODOP.PRINT();

            for (i = 0; i < 10000; i++) {

            }

            // ********** 存根联 *****************
            // 重新设置打印机的设备
            LODOP.PRINT_INIT("printorder");
            LODOP.SET_PRINTER_INDEX(print_index);
            // 领餐单标题
            LODOP.SET_PRINT_STYLE("FontSize", 12);
            row = 0;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '堂口存根联');

            // 出单号
            var diningsaleorder = '堂口:' + data['diningsale'].diningname + ': ' +
                ' 序号:' + data['diningsale'].sequence;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, diningsaleorder);

            var diningsaletime = '日期:' + data['diningsale'].date +
                ' 午别:' + data['diningsale'].ap + ' ' +
                data['diningsale'].saletime;
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 20;
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, diningsaletime);


            LODOP.SET_PRINT_STYLE("FontSize", 10);
            LODOP.SET_PRINT_STYLE('FontName', '宋体');
            LODOP.SET_PRINT_STYLE('Bold', 0);
            linespacing = 19;
            // 商品打印
            var productsTitle = '名称          数量     单价    金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
            if (data['diningsaleproducts']) {
                $.each(data['diningsaleproducts'], function (key, value) {
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
            // 金额类型
            LODOP.SET_PRINT_STYLE("FontSize", 8);
            linespacing = 19;
            row = row + 1;
            var shippingmoney = '支付类型:' + data['diningsale'].paytype;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);

            // 总金额
            LODOP.SET_PRINT_STYLE("FontSize", 10);
            linespacing = 19;
            row = row + 1;
            var totalmoney = '总金额:' + data['diningsale'].money;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);

            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

            LODOP.PRINT();

            for (i = 0; i < 10000; i++) {

            }


        },


        //获取堂口销售列表
        saleList: function () {
            var that = this;
            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: "__URL__/listview",
                    dataType: "json",
                    success: function (data) {

                        if (data.data.rows.length > 0) {
                            //选择第一行焦点
                            that.diningSaleTongjiGrid.datagrid('loadData', data.data);
                            $('#diningsaletotalmoney').val(data.totalmoney);
                        }
                    }
                })
            }, 350);
        }

    }

    $(function () {
        diningsaleCreateviewModule.init();
        setTimeout(function () {
            $("#DiningSaleCreateviewproductsNumber_1").focus();
        }, 200);

    })
</script>