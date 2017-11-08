<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;改单操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="OrderFormEditviewModule.update();"><img src=".__PUBLIC__/Images/newBtn.png"
                    alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="OrderFormEditviewModule.update();">保存订单<span>^9</span></a></li>

        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/newBtn.png"
                    alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>');">返回列表</a><span>^4</span></li>

        <li style="margin-left: 30px;"><a href="javascript:;" onclick="OrderFormEditviewModule.showTodayMenuview();"><img
                    src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="OrderFormEditviewModule.showTodayMenuview();">查看今日菜单<span>^0</span></a></li>

        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png"
                    alt="" title="" border="0"></a></li>
    </ul>
</div>

<style>
    .moduleOperater {
        clear: both;
        margin: 0px;
        padding: 0px;
        overflow: scroll;
    }
</style>
<div class="moduleOperater">
    <form id="OrderFormEditviewForm" method="post">
        <input type="hidden" name="returnAction" value="<?php echo ($returnAction); ?>" />
        <input type="hidden" name="record" value="<?php echo ($record); ?>" />
        <table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="3" width="100%" class="small">
                        <tr>
                            <td class="dvtTabCache" style="width:20px" nowrap>&nbsp;</td>
                            <td class="dvtSelectedCell" align="center" nowrap> 新建</td>
                            <td class="dvtTabCache" style="width:80%">&nbsp;</td>
                        <tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td valign="top" align="center">
                    <div class="BaseForm" style="border: 1px solid #e0dddd; background: white;">
                        <table style="BORDER-COLLAPSE: collapse;font-size:16px;" borderColor="#CCCCCC" cellSpacing="0"
                            width="100%" align="center" border="1">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    订单基本信息
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">电话:</span>
                                </td>
                                <td width="50%" align="left" class="createFormRightTd">
                                    <!-- 电话 -->
                                    <input type="text" name="telphone" style="width:50%;font-size:16px;" value="<?php echo ($info["telphone"]); ?>"
                                        autocomplete="off" />
                                </td>
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">客户姓名:</span>
                                </td>
                                <td width="20%" align="left" class="createFormLeftTd">
                                    <input type="text" class="easyui-validatebox" name="clientname" style="font-size:16px;width:150px;"
                                        value="<?php echo ($info["clientname"]); ?>" />
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">地址:</span>
                                </td>
                                <td width="50%" align="left" class="createFormLeftTd">
                                    <input type="text" class="easyui-number" name="address" data-options="required:true,validType:{length:[2,20]}"
                                        autocomplete="off" style="font-size:16px;width: 90%;" value="<?php echo ($info["address"]); ?>" />
                                </td>
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">要餐时间:</span>
                                </td>
                                <td width="20%" align="left" class="createFormRightTd">
                                    <table>
                                        <tr>
                                            <td><input type="text" name="custtime_1" id="custtime_1" value="<?php echo ($custtime_1); ?>"
                                                    size="2" maxlength="2" tabindex="<?php echo ($vt_tab); ?>" class="detailedViewTextBox"
                                                    AUTOCOMPLETE="off" style="width: 30px;" />
                                            </td>
                                            <td><label style="font-size: 14px;">:</label></td>
                                            <td><input type="text" name="custtime_2" id="custtime_2" value="<?php echo ($custtime_2); ?>"
                                                    size="2" maxlength="2" tabindex="<?php echo ($vt_tab); ?>" class="detailedViewTextBox"
                                                    AUTOCOMPLETE="off" style="width:30px;" />
                                            </td>
                                            <td><span style="font-size: 18px;">:00</span></td>
                                        </tr>
                                    </table>


                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">备注:</span>
                                </td>
                                <td width="50%" align="left" class="createFormLeftTd">
                                    <input name="beizhu" value="<?php echo ($info["beizhu"]); ?>" autocomplete="off" style="width:90%;font-size:16px;" />
                                </td>
                                <td width="15%" align="right" class="createFormLeftTd">
                                    <span style="font-size:14px;margin-right:10px;">分公司:</span>
                                </td>
                                <td width="20%" align="left" class="createFormRightTd">
                                    <select class="easyui-easyui-combobox" name="company" data-options="required:true,validType:{length:[2,20]}"
                                        style="width:50%;font-size:16px;">

                                    </select>
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right" class="createFormLeftBottomTd">
                                    <span style="font-size:14px;margin-right:10px;">订单金额:</span>
                                </td>
                                <td width="50%" align="left" class="createFormLeftBottomTd">
                                    <table width="100%">
                                        <tr>
                                            <td><span style="font-size:14px;">总金额:</span></td>
                                            <td>
                                                <input type="text" name="totalmoney" readonly style="font-size:16px;width:80px;"
                                                    value="<?php echo ($info["totalmoney"]); ?>" />
                                            </td>
                                            <td><span style="font-size:14px;">已收金额:</span></td>
                                            <td>
                                                <input type="text" name="moneypaid" readonly style="font-size:16px;width:80px;"
                                                    value="<?php echo ($info["paidmoney"]); ?>" />
                                            </td>
                                            <td><span style="font-size:14px;">应收金额:</span></td>
                                            <td>
                                                <input type="text" name="shouldmoney" readonly style="font-size:16px;width:80px;"
                                                    value="<?php echo ($info["shouldmoney"]); ?>" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="15%" align="right" class="createFormLeftBottomTd">
                                    <span style="font-size:14px;margin-right:10px;">送餐费:</span>
                                </td>
                                <td width="20%" align="left" class="createFormRightBottomTd">
                                    <input type="text" name="shippingmoney" style="font-size:16px;" value="<?php echo ($info["shippingmoney"]); ?>" />
                                    <input type="hidden" name="state" value="<?php echo ($info["state"]); ?>" />

                                </td>
                            </tr>
                        </table>

                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px; border: 1px solid #e0dddd; ">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    产品基本信息
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td colspan="4">
                                    <style>
    #productsTable {
        background-color: transparent;
    }

    .productsTableHeader {
        background-color: #008B00;
        font-size: 12px;
    }

    #productsTable td{
        height: 25px;
    }

    #productsTable span{
        font-size:16px;
    }

    #productsTable input{
        font-size : 16px;
    }

</style>
<table id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable" style="BORDER-COLLAPSE: collapse"  borderColor="#CCCCCC" cellSpacing="0" width="100%"
       align="center" border="1">
    <tr class="productsTableHeader">
        <td width="5%" align="center" class="productsTableHeaderLeftTd">序号</td>
        <td width="12%" align="center" class="productsTableHeaderLeftTd">数量</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">产品代码</td>
        <td width="30%" align="center" class="productsTableHeaderLeftTd">产品名称</td>
        <td width="12%" align="center" class="productsTableHeaderLeftTd">单价</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">金额</td>
        <td width="10%" align="center" class="productsTableHeaderRightTd">操作</td>
    </tr>
    <?php if(empty($orderproducts)): $__FOR_START_90126470__=0;$__FOR_END_90126470__=3;for($key=$__FOR_START_90126470__;$key < $__FOR_END_90126470__;$key+=1){ ?><tr>
                <td width="5%" align="center" ><?php echo ($key+1); ?></td>
                <td width="15%" align="center" > 
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_<?php echo ($key+1); ?>" name="productsNumber_<?php echo ($key+1); ?>" type="text" size="5" tabindex="1"
                           onkeydown="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.keydownSumProductsMoney(<?php echo ($key+1); ?>,event,this)"
                           onblur="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.blurkeydownSumProductsMoney(<?php echo ($key+1); ?>,this)" value=""
                           AUTOCOMPLETE="off"
                           style="font-size:16px;"/>
                </td>
                <td width="10%" align="center" "> 
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_<?php echo ($key+1); ?>" name="productsCode_<?php echo ($key+1); ?>" type="text" size="10" tabindex="1"
                           onkeydown="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.getProductsByCode(<?php echo ($key+1); ?>,event,this);" AUTOCOMPLETE="off"
                           style="font-size:16px;"/>

                    <img id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1" title="产品选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                         align="absmiddle"
                         onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>');"/><a
                            href="javascript:<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>')">选择</a>
                </td>
                <td width="30%" align="center" > 
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_<?php echo ($key+1); ?>" name="productsName_<?php echo ($key+1); ?>" type="text" size="30"
                           readonly="readonly" style="font-size:16px;"/>
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsShortName_<?php echo ($key+1); ?>" name="productsShortName_<?php echo ($key+1); ?>" size="30" type="hidden"
                           style="font-size:16px;"/>
                </td>
                <td width="15%" align="center" > 
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_<?php echo ($key+1); ?>" name="productsPrice_<?php echo ($key+1); ?>" type="text" size="10"
                           readonly="readonly" tabindex="1"
                           onblur="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.blurkeydownSumProductsMoney(<?php echo ($key+1); ?>,this)" value="" style="font-size:16px;"/>
                    <img id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1" title="产品选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                         align="absmiddle"
                         onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.setGoodsPrice('<?php echo ($key+1); ?>');"/>
                </td>
                <td width="15%" align="center" > 
                    <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_<?php echo ($key+1); ?>" name="productsMoney_<?php echo ($key+1); ?>" type="text" size="10"
                           readonly="readonly" tabindex="1" value="" style="font-size:16px;"/>
                </td>
                <td width="10%" align="center" ><a href="#" onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.clearProducts(<?php echo ($key+1); ?>);">清空产品</a>
                </td>
            </tr><?php } endif; ?>
    <?php if(is_array($orderproducts)): foreach($orderproducts as $key=>$vo): ?><tr>
            <td width="5%" align="center" ><?php echo ($key+1); ?></td>
            <td width="15%" align="center" > 
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_<?php echo ($key+1); ?>" name="productsNumber_<?php echo ($key+1); ?>" type="text" size="5" tabindex="1"
                       value="<?php echo ($vo["number"]); ?>" onkeydown="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.keydownSumProductsMoney(<?php echo ($key+1); ?>,event,this)"
                       onblur="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.sumProductsMoney(<?php echo ($key+1); ?>,this);" AUTOCOMPLETE="off"
                       style="font-size:16px;"/>
            </td>
            <td width="10%" align="center" > 
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_<?php echo ($key+1); ?>" name="productsCode_<?php echo ($key+1); ?>" type="text" size="10" tabindex="1"
                       value="<?php echo ($vo["code"]); ?>" onkeydown="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.getProductsByCode(<?php echo ($key+1); ?>,event,this);"
                        AUTOCOMPLETE="off" style="font-size:16px;"/>
                <img id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1" title="产品选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                     align="absmiddle"
                     onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>');"/><a
                        href="javascript:<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList('__URL__/popupProductsview/module/Products/row/<?php echo ($key+1); ?>')">选择</a>
            </td>
            <td width="30%" align="center" > 
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_<?php echo ($key+1); ?>" name="productsName_<?php echo ($key+1); ?>" type="text" size="30" readonly="readonly"
                       value="<?php echo ($vo["name"]); ?>"  style="font-size:16px;"/>
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsShortName_<?php echo ($key+1); ?>" name="productsShortName_<?php echo ($key+1); ?>" type="hidden"
                       value="<?php echo ($vo["shortname"]); ?>" style="font-size:16px;"/>
            </td>
            <td width="15%" align="center" > 
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_<?php echo ($key+1); ?>" name="productsPrice_<?php echo ($key+1); ?>" type="text" size="10"
                       readonly="readonly" tabindex="1" value="<?php echo ($vo["price"]); ?>" style="font-size:16px;"/>
            </td>
            <td width="15%" align="center" > 
                <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_<?php echo ($key+1); ?>" name="productsMoney_<?php echo ($key+1); ?>" type="text" size="10"
                       readonly="readonly" tabindex="1" value="<?php echo ($vo["money"]); ?>" style="font-size:16px;"/>
            </td>
            <td width="10%" align="center" ><a href="#" onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.clearProducts('<?php echo ($key+1); ?>');">清空产品</a>
            </td>
        </tr><?php endforeach; endif; ?>

</table>
<table width="100%" style="BORDER-COLLAPSE: collapse"  borderColor="#CCCCCC" cellSpacing="0" width="100%"
       align="center" border="1">
    <tr>
        <td class="productsTableXiaojiLeftTd" width="65%">
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_add'"
               onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.addProducts();" style="font-size:16px;">添加产品行</a>
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_delete'"
               onclick="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.delProducts();" style="font-size:16px;margin-left:30px;">删除产品最后一行</a>
            <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsLength" name="productsLength" type="hidden" value="<?php echo ($key+1); ?>"/>
        </td>

        <td style="font-size: 16px;">
            <span>份数:</span>
            <span id="productsTotalNumber"></span>
        </td>
        <td class="productsTableXiaojiRightTd" style="font-size: 14px;"> 
            <span>小计</span>
            <input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTotalMoney" name="productsTotalMoney" type="text" size="10" readonly="readonly"
                   style="border: 0px;font-size: 14px;" value="<?php echo ($info["goodsmoney"]); ?>"/>
        </td>
    </tr>
</table>

<script type="text/javascript">
    var <?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule = {

        init: function () {

        },


        //添加产品
        addProducts: function () {
            //取得表格行的长度
            var rowNum = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable tr").length;

            var tableTrAppend = "<tr> ";
            tableTrAppend += "<td width='5%' align='center' class='productsTableLeftTd'>" + rowNum + "</td> ";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + rowNum + "' name='productsNumber_" + rowNum + "' type='text' size='5' value='' tabindex='1' onkeydown='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.keydownSumProductsMoney(" + rowNum + ",event,this)' onblur='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.blurkeydownSumProductsMoney(" + rowNum + ",event,this)' value='' AUTOCOMPLETE='off' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input class='easyui-numberbox' id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + rowNum + "' name='productsCode_" + rowNum + "'  type='text' size='10' tabindex='1' onkeyup='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.getProductsByCode(" + rowNum + ",event,this)' style='font-size:16px;' />";
            tableTrAppend += "<img id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1' title='产品选择' src='./" + PUBLIC + "/Images/products.gif' style='cursor: pointer;' align='absmiddle' onclick='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList(\"__APP__/OrderForm/popupview/module/Products/row/" + rowNum + "\");' />";
            tableTrAppend += "<a href='javascript:<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.productsPickList(\"__URL__/popupProductsview/module/Products/row/" + rowNum + "\");' >选择</a>";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum + "' name='productsName_" + rowNum + "' type='text' size='30' readonly='readonly' style='font-size:16px;'  />";
            tableTrAppend += "<input id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsShortName_" + rowNum + "' name='productsShortName_" + rowNum + "' type='hidden'  />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_" + rowNum + "' name='productsPrice_" + rowNum + "' type='text' size='10' readonly='readonly' tabindex='1' value='' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='productsTableLeftTd'>";
            tableTrAppend += "<input id='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_" + rowNum + "' name='productsMoney_" + rowNum + "' type='text' size='10' readonly='readonly'  tabindex='1' value='' style='font-size:16px;' />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='10%' align='center' class='productsTableLeftTd'><a href='#' onclick='<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>GoodsEditViewModule.clearProducts(" + rowNum + ");' >清空产品</a></td>";
            tableTrAppend += "</tr>";
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable").append(tableTrAppend);
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsLength").attr("value", rowNum + 1);  //表格行数保存
        },

        /* 键盘回车计算产品金额 */
        keydownSumProductsMoney: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                if ($("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum).val() != '') {    //如果不为空，才计算
                    this.sumProductsMoney(rowNum);
                }
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + rowNum).focus();
            }
            //向上移动一个
            if (key == 38) {
                var focusNum = rowNum - 1;
                if (focusNum > 0) {
                    $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + focusNum).focus();
                }
            }
            //向下移动
            if (key == 40) {
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + rowNum).focus();
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
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + rowNum).focus();
            }
            //向下移动
            if (key == 40) {
                var focusNum = rowNum + 1;
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + focusNum).focus();
            }

        },

        /* 弹出窗口，选择产品 */
        //moduleName:产品名称  rowNum:行号 moduleName,rowNum
        productsPickList: function (url) {
            url = url + '/returnModule/'+'<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>';
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
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;

                                var formArray = $(this).serializeArray();
                                var url = '__URL__/searchviewAddress/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订单地址查询');
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
            var productsCode = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + rowNum).val();
            var productsName = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum).val();

            if ((productsCode == '') && (productsName == '')) {
                return;
            }
            if (confirm("是否要清空内容")) {
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + rowNum).val('');
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsCode_" + rowNum).val('');
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum).val('');
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsShortName_" + rowNum).val('');
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_" + rowNum).val('');
                $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_" + rowNum).val(0);
                this.sumProductsMoney(rowNum);
            }
        },

        /*  删除产品最后一行 */
        delProducts: function () {
            //取得表格行的长度
            var rowNum = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable tr").length;
            if (rowNum == 2) {
                alert("最后一行不能删除");
                return;
            }
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable tr:last").remove();
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsLength").attr("value", rowNum - 1);  //表格行数保存
            this.sumProductsMoney(rowNum);

        },



        //根据用户输入的产品代码，输出产品名称和价格
        getProducts: function (rowNum, code) {
            var that = this;
            $.ajax({
                url: APP + '/Products' + "/getProductsByCode&code=" + code,
                async: true,
                beforeSend: function () {
                },
                success: function (mydata) {
                    var productsName = '';
                    if (mydata) {
                        //首先检查父窗口表格是否有存在输入的代码和产品
                        var rowLength = $("#productsTable tr").length;
                        for (i = 1; i < rowLength; i++) {
                            productsName = $("<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>#productsName_" + i).val();
                            if ((productsName == mydata.name) && (i != rowNum)) {
                                alert('产品已经存在,不能添加！');
                                return;
                            }
                        }
                        $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum).val(mydata.name);
                        $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsShortName_" + rowNum).val(mydata.shortname);
                        $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_" + rowNum).val(mydata.price);
                        //计算总的金额
                        that.sumProductsMoney(rowNum);
                        //跳转到下一行
                        var focusNum = rowNum + 1;
                        $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + focusNum).focus();
                    } else {
                        alert("输入的产品代码有错误，请重新输入！");
                        return;
                    }
                }

            })
        },


        /* 计算产品金额 */
        sumProductsMoney: function (rowNum) {
            var productsNumber = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + rowNum).val();  //数量
            //如果没有输入产品数量,就为零
            if(productsNumber == '') {
                productsNumber = 0;
            }
            else{
                if (this.IsNum(productsNumber)  == false){
                   alert('输入的产品数量不对!请检查');
                   return false;
                }
            }
            var productsPrice = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_" + rowNum).val();  //单价
            var productsMoney = 0;
            productsMoney = parseInt(productsNumber) * parseFloat(productsPrice);

            //写入
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_" + rowNum).val(productsMoney);
            //计算全部的金额
            var totalMoney = 0;
            var productsTotalNumber = 0;
            //取得表格行的长度
            var rowLength = $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTable tr").length;
            for (i = 1; i < rowLength; i++) {
                if ($("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_" + i).val() > 0) {
                    totalMoney = totalMoney + parseFloat($("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsMoney_" + i).val());
                }
                var productsNumberCount =   $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + i).val();
                if(productsNumberCount == ''){
                    productsNumberCount = 0;
                }
                if (productsNumberCount > 0) {
                productsTotalNumber = productsTotalNumber + parseInt($("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsNumber_" + i).val());
                }
            }

            //写入总的金额
            //totalMoney = totalMoney.toFixed(2);
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsTotalMoney").val(totalMoney);
            //下一个表格代码输入框显示焦点
            rowNum = rowNum + 0;
            //加上送餐费
            var shippingmoney = 0;
            var shippingmoneyVal = $('#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>Form input[name=shippingmoney]').val();
            if (shippingmoneyVal) {
                shippingmoney = parseFloat(shippingmoneyVal);
            }
            totalMoney = totalMoney + shippingmoney;
            totalMoney = parseFloat(totalMoney).toFixed(2);
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>FormTotalMoney").html(totalMoney+'元');  //订单总金额
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>Form input[name=totalmoney]").val(totalMoney);
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>FormShouldMoney").html(totalMoney+'元');    //应收金额
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>Form input[name=shouldmoney]").val(totalMoney);
            $('#productsTotalNumber').html(productsTotalNumber + '份');
        },


        //产品代码失去焦点的计算金额
        blurSumProductsMoney: function (rowNum, obj) {
            if ($("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsName_" + rowNum).val() != '') {    //如果不为空，才计算
                sumProductsMoney(rowNum);
            }
        },

        //把产品价格单元设置为可改写
        setGoodsPrice : function(row){
            $("#<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>productsPrice_" + row).attr('readonly',false);
        },


        //判断是否为数字
        IsNum :function (s)
        {
            if (s!=null && s!="")
            {
             return !isNaN(s);
            }
        return false;
        }

    }

    /*产品的js计算程序*/
    var productsAjax = false;  //判断是否查询过产品代码，防止在ajax中按回车，反复执行，有个alert的小bug


</script>

                                </td>
                            </tr>
                        </table>

                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px; border: 1px solid #e0dddd; ">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    发票基本信息
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td colspan="4">
                                    
<table width="100%"  style="BORDER-COLLAPSE: collapse;font-size:16px;"  borderColor="#CCCCCC" cellSpacing="0" width="100%"
       align="center" border="1">
    <tr>
        <td width="15%" class="invoiceTableLeftTd" align="left">
            <label style="vertical-align:middle;display:inline-block;margin-left:30px;">来电发票历史记录:</label>
        </td>
        <td width="35">
            <select id="invoiceheaderselect"  style="width:50%;" >
            </select>
        </td>
        <td width="15%" class="invoceTableRightTd">
            <label style="margin-left:30px;">发票类型:</label>
        </td>
        <td width="35%">
            <select id="invoice_type" name="invoicetype" style="margin-left: 1px;width: 30%;">
             <?php if($info["invoicetype"] != ''): ?><option value="<?php echo ($info["invoicetype"]); ?>"><?php echo ($info["invoicetype"]); ?></option>
             <?php else: ?>
                 <option value=""></option><?php endif; ?>

             <?php if($invoiceelectronopen == '启用'): ?><option value="电子票">电子票</option><?php endif; ?>
                <option value="普通票">普通票</option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="10%" class="invoiceTableLeftTd" align="left">
            <span style="vertical-align:middle;display:inline-block;margin-left:30px;">发票抬头:</span>
        </td>
        <td>
            <input id="invoice_header" type="text" name="invoiceheader"  value="<?php echo ($info["invoiceheader"]); ?>"
                                           style="margin-left: 1px;width: 50%;float:left;"/></td>
        <td width="10%" class="invoceTableRightTd">
            <span style="margin-left:30px;">发票内容:</span>
        </td>
        <td>
            <select id="invoice_body" name="invoicebody"
                                            style="margin-left: 1px;width: 30%;">
                <?php if($info["invoicebody"] != ''): ?><option value="<?php echo ($info["invoicebody"]); ?>"><?php echo ($info["invoicebody"]); ?></option><?php endif; ?>
                        <option value="工作餐">工作餐</option>
                        <option value="盒饭">盒饭</option>
                        <option value="餐费">餐费</option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="10%" class="invoiceTableLeftTd" align="left">
            <span style="vertical-align:middle;display:inline-block;margin-left:30px;">购买方纳税人识别号:</span>
        </td>
        <td>
            <input id="invoice_gmf_nsrsbh" type="text" name="gmf_nsrsbh"  value="<?php echo ($info["gmf_nsrsbh"]); ?>"
                   style="margin-left: 1px;width: 50%;"/><i style="color: red;">*必填</i></td>
        <td width="10%" class="invoceTableRightTd">
            <span style="margin-left:30px;">购买方地址电话:</span>
        </td>
        <td>
            <input id="invoice_gmf_dzdh" type="text" name="gmf_dzdh"  value="<?php echo ($info["gmf_dzdh"]); ?>"
                   style="margin-left: 1px;width: 90%;"/>
        </td>
    </tr>
    <tr>
        <td width="10%" class="invoiceTableLeftTd" align="left">
            <span style="vertical-align:middle;display:inline-block;margin-left:30px;">购买方银行账号:</span>
        </td>
        <td>
            <input id="invoice_gmf_yhzh" type="text" name="gmf_yhzh"  value="<?php echo ($info["gmf_yhzh"]); ?>"
                   style="margin-left: 1px;width: 70%;"/></td>
        <td width="10%" class="invoceTableRightTd">
        </td>
        <td>

        </td>
    </tr>
</table>
<script>
    $(function(){
        $('#invoiceheaderselect').bind('change',function(){
            invoiceHeader = $('#invoiceheaderselect').find("option:selected").text();
            if(invoiceHeader){  //如果存在发票头
                $('#invoiceheaderselectset').val();
                //检搜发票其他信息
                $(telInvoiceArray).each(function (i, val) {
                    if(val.header == invoiceHeader){
                        $('#invoice_header').val($.trim(val.header));
                        $('#invoice_body').val($.trim(val.body));
                        $('#invoice_gmf_nsrsbh').val($.trim(val.gmf_nsrsbh));
                        $('#invoice_gmf_dzdh').val($.trim(val.gmf_dzdh));
                        $('#invoice_gmf_yhzh').val($.trim(val.gmf_yhzh));
                        return false;
                    }else{
                        $('#invoice_header').val('');
                        $('#invoice_body').val('');
                        $('#invoice_gmf_nsrsbh').val('');
                        $('#invoice_gmf_dzdh').val('');
                        $('#invoice_gmf_yhzh').val('');
                    }
                });
            }
        })
    })
</script>

                                </td>
                            </tr>
                        </table>

                        <?php if(!empty($orderactivity)): ?><table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin:1px; border: 1px solid #e0dddd; ">
                                <tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        营销活动信息
                                    </td>
                                </tr>
                                <tr style="background: #FFFFFF;">
                                    <td colspan="4">
                                        <style>
	#DetailOrderFormActivity td {
		height: 25px;
		border-style: solid none none solid;
		border-width: 1px;
		border-color: #CCCCCC;
		font-size: 14px;
	}
</style>
<table id="DetailOrderFormActivity" style="BORDER-COLLAPSE: collapse" borderColor="white" cellSpacing="0" width="100%"
 align="center" border="1">
	<?php if(is_array($orderactivity)): foreach($orderactivity as $key=>$vo): ?><tr>
			<td width="10%" align=right>
				<span style="margin-right:5px;">活动名称:</span></td>
			<td width="45%" align=left class="dvtCellInfo">
				<span style="background: #F5F5F5;"><?php echo ($vo["name"]); ?></span>
			</td>
			<td width="10%" align=right>
				<span style="margin-right:5px;">活动金额:</span></td>
			<td width="45%" align=left>
				<span style="background: #F5F5F5;"><?php echo ($vo["money"]); ?></span>
			</td>
		</tr><?php endforeach; endif; ?>
	<input id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>activityTotalMoney" name="activityTotalMoney" type="text" size="10"
	 readonly="readonly" style="border: 0px;font-size: 14px;" value="<?php echo ($orderactivitymoney); ?>11" />
</table>
                                    </td>
                                </tr>
                            </table><?php endif; ?>


                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:2px; border: 1px solid #e0dddd; ">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    支付方式
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td colspan="4">
                                    <style>
    .accountsTableHeader {
        background-color: #008B00;
        font-size: 12px;
    }

    #accountsTable td {
        height: 25px;
    }

    #accountsTable span {
        font-size: 16px;
    }

    #accountsTable input {
        font-size: 16px;
    }

</style>
<table id="OrderFormAccountPaymentTable" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%"
    align="center" border="1">
    <tr class="accountsTableHeader">
        <td width="5%" align="center" class="accountsTableHeaderLeftTd">序号</td>
        <td width="10%" align="center" class="accountsTableHeaderLeftTd">编号</td>
        <td width="30%" align="center" class="accountsTableHeaderLeftTd">名称</td>
        <td width="15%" align="center" class="accountsTableHeaderLeftTd">金额</td>
        <td width="25%" align="center" class="accountsTableHeaderLeftTd">备注</td>
        <td width="10%" align="center" class="accountsTableHeaderRightTd">操作</td>
    </tr>
    <?php if(empty($orderaccountpayment)): $__FOR_START_1037222897__=0;$__FOR_END_1037222897__=1;for($key=$__FOR_START_1037222897__;$key < $__FOR_END_1037222897__;$key+=1){ ?><tr>
                <td width="5%" align="center"><?php echo ($key+1); ?></td>
                <td width="10%" align="center"> 
                    <input id="OrderFormAccountsCode_<?php echo ($key+1); ?>" name="accountpaymentCode_<?php echo ($key+1); ?>" type="text" size="6"
                        tabindex="1" onkeydown="OrderFormAccountPaymentEditViewModule.getAccountsByCode(<?php echo ($key+1); ?>,event,this);"
                        AUTOCOMPLETE="off" style="font-size:16px;" />

                    <img id="OrderFormsearchIcon1" title="选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                        align="absmiddle" onclick="OrderFormAccountPaymentEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');" /><a
                        href="javascript:OrderFormAccountPaymentEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
                </td>
                <td width="30%" align="center"> 
                    <input id="OrderFormAccountsName_<?php echo ($key+1); ?>" name="accountpaymentName_<?php echo ($key+1); ?>" type="text" size="30"
                        readonly="readonly" style="font-size:16px;" />
                </td>
                <td width="15%" align="center"> 
                    <input id="OrderFormAccountsMoney_<?php echo ($key+1); ?>" name="accountpaymentMoney_<?php echo ($key+1); ?>" type="text" size="10"
                        onkeydown="OrderFormAccountPaymentEditViewModule.keydownSumAccountsMoney('<?php echo ($key+1); ?>',event,this);"
                        onblur="OrderFormAccountPaymentEditViewModule.blurSumAccountsMoney();" tabindex="1" value=""
                        style="font-size:16px;" />
                </td>
                <td width="25%" align="center"> 
                    <input id="OrderFormAccountsNote_<?php echo ($key+1); ?>" name="accountpaymentNote_<?php echo ($key+1); ?>" type="text" size="30"
                        onkeydown="OrderFormAccountPaymentEditViewModule.keydownAccountsNote('<?php echo ($key+2); ?>',event);" style="font-size:16px;" />
                </td>
                <td width="10%" align="center"><a href="#" onclick="OrderFormAccountPaymentEditViewModule.clearAccounts(<?php echo ($key+1); ?>);">清空</a>
                </td>
            </tr><?php } endif; ?>
    <?php if(is_array($orderaccountpayment)): foreach($orderaccountpayment as $key=>$vo): ?><tr>
            <td width="5%" align="center"><?php echo ($key+1); ?></td>
            <td width="10%" align="center"> 
                <input id="OrderFormAccountsCode_<?php echo ($key+1); ?>" name="accountpaymentCode_<?php echo ($key+1); ?>" type="text" size="6"
                    tabindex="1" value="<?php echo ($vo["paymentid"]); ?>" onkeydown="OrderFormAccountPaymentEditViewModule.getAccountsByCode(<?php echo ($key+1); ?>,event,this);"
                    AUTOCOMPLETE="off" style="font-size:16px;" />
                <img id="OrderFormAccountssearchIcon1" title="客户选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
                    align="absmiddle" onclick="OrderFormAccountPaymentEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');" /><a
                    href="javascript:OrderFormAccountPaymentEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
            </td>
            <td width="30%" align="center"> 
                <input id="OrderFormAccountsName_<?php echo ($key+1); ?>" name="accountpaymentName_<?php echo ($key+1); ?>" type="text" size="30"
                    readonly="readonly" value="<?php echo ($vo["name"]); ?>" style="font-size:16px;" />
            </td>
            <td width="15%" align="center"> 
                <input id="OrderFormAccountsMoney_<?php echo ($key+1); ?>" name="accountpaymentMoney_<?php echo ($key+1); ?>" type="text" size="10"
                    onkeydown="OrderFormAccountPaymentEditViewModule.keydownSumAccountsMoney();" onblur="OrderFormAccountPaymentEditViewModule.blurSumAccountsMoney();"
                    tabindex="1" value="<?php echo ($vo["money"]); ?>" style="font-size:16px;" />
            </td>
            <td width="25%" align="center"> 
                <input id="OrderFormAccountsNote_<?php echo ($key+1); ?>" name="accountpaymentNote_<?php echo ($key+1); ?>" type="text" size="30"
                    value="<?php echo ($vo["note"]); ?>" style="font-size:16px;" />
            </td>
            <td width="10%" align="center"><a href="#" onclick="OrderFormAccountPaymentEditViewModule.clearAccounts('<?php echo ($key+1); ?>');">清空</a>
            </td>
        </tr><?php endforeach; endif; ?>
</table>
<table width="100%" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%" align="center"
    border="1">
    <tr>
        <td class="accountsTableXiaojiLeftTd" width="65%">
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_add'" onclick="OrderFormAccountPaymentEditViewModule.addAccounts();"
                style="font-size:16px;">添加项目</a>
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_delete'" onclick="OrderFormAccountPaymentEditViewModule.delAccounts();"
                style="font-size:16px;margin-left:30px;">删除项目最后一行</a>
            <input id="OrderFormAccountPaymentLength" name="accountpaymentLength" type="hidden" value="<?php echo ($key+1); ?>" />
        </td>

        <td style="font-size: 16px;">

        </td>
        <td class="accoutsTableXiaojiRightTd" style="font-size: 14px;"> 
            <span>小计</span>
            <input id="OrderFormAccountPaymentTotalMoney" name="accountpaymentTotalMoney" type="text" size="10"
                readonly="readonly" style="border: 0px;font-size: 14px;" value="<?php echo ($orderpaymentmoney); ?>" />
        </td>
    </tr>
</table>

<script type="text/javascript">
    var OrderFormAccountPaymentEditViewModule = {

        init: function () {},

        test: function () {
            alert('ok');
        },

        //添加产品
        addAccounts: function () {
            //取得表格行的长度
            var rowNum = $("#OrderFormAccountPaymentTable tr").length;

            var tableTrAppend = "<tr> ";
            tableTrAppend += "<td width='5%' align='center' class='accountsTableLeftTd'>" + rowNum + "</td> ";
            tableTrAppend += "<td width='15%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input class='easyui-numberbox' id='OrderFormAccountsCode_" + rowNum +
                "' name='accountpaymentCode_" + rowNum +
                "'  type='text' size='6' tabindex='1' onkeyup='OrderFormAccountPaymentEditViewModule.getAccountsByCode(" +
                rowNum + ",event,this)' style='font-size:16px;' />";
            tableTrAppend += "<img id='OrderFormsearchIcon1' title='客户选择' src='./" + PUBLIC +
                "/Images/products.gif' style='cursor: pointer;' align='absmiddle' onclick='OrderFormAccountPaymentEditViewModule.accountsPickList(\"__URL__/popupAccountsview/module/Accounts/row/" +
                rowNum + "\");' />";
            tableTrAppend +=
                "<a href='javascript:OrderFormAccountPaymentEditViewModule.accountsPickList(\"__URL__/popupAccountsview/module/Accounts/row/" +
                rowNum + "\");' >选择</a>";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='30%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='OrderFormAccountsName_" + rowNum + "' name='accountpaymentName_" +
                rowNum +
                "' type='text' size='30'  style='font-size:16px;' readonly />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='OrderFormAccountsMoney_" + rowNum + "' name='accountpaymentMoney_" +
                rowNum +
                "' type='text' size='10'   tabindex='1' value='' style='font-size:16px;' onkeydown=\"OrderFormAccountPaymentEditViewModule.keydownSumAccountsMoney();\"\n" +
                "                       onblur=\"OrderFormAccountPaymentEditViewModule.blurSumAccountsMoney();\" />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='25%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='OrderFormAccountsNote_" + rowNum + "' name='accountpaymentNote_" +
                rowNum +
                "' type='text' size='30'  style='font-size:16px;'  />";
            tableTrAppend += "</td>";
            tableTrAppend +=
                "<td width='10%' align='center' class='accountsTableLeftTd'><a href='#' onclick='OrderFormAccountPaymentEditViewModule.clearAccounts(" +
                rowNum + ");' >清空</a></td>";
            tableTrAppend += "</tr>";
            $("#OrderFormAccountsTable").append(tableTrAppend);
            $("#OrderFormAccountsLength").attr("value", rowNum + 1); //表格行数保存
        },

        /* 键盘回车计算产品金额 */
        keydownSumAccountsMoney: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                if ($("#OrderFormAccountsName_" + rowNum).val() != '') { //如果不为空，才计算
                    this.sumAccountsMoney(rowNum);
                }
                $("#OrderFormAccountsNote_" + rowNum).focus();
            }
            //向上移动一个
            if (key == 38) {
                var focusNum = rowNum - 1;
                if (focusNum > 0) {
                    $("#OrderFormAccountsCode_" + focusNum).focus();
                }
            }
            //向下移动
            if (key == 40) {
                $("#OrderFormAccountsCode_" + rowNum).focus();
            }
        },

        /**
         * 备注的键盘回车处理
         */
        keydownAccountsNote: function (rowNum, evt) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                $("#OrderFormAccountsCode_" + rowNum).focus();
            }
        },

        //产品数量输入框失去焦点
        blurkeydownSumProductsMoney: function (rowNum, obj) {
            this.sumProductsMoney(rowNum);
        },

        /* 通过产品代码取得产品 */
        // rowNum 是行号，evt是firefox下的event事件，obj是this指针
        getAccountsByCode: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            var code = $(obj).val();

            if ((key == 13) && (code.length > 0)) {
                this.getAccounts(rowNum, code);
            }
            //向上移动
            if (key == 38) {
                $("#OrderFormAccountsCode_" + rowNum).focus();
            }
            //向下移动
            if (key == 40) {
                var focusNum = rowNum + 1;
                $("#OrderFormAccountsCode_" + focusNum).focus();
            }

        },

        /* 弹出窗口，选择产品 */
        //moduleName:产品名称  rowNum:行号 moduleName,rowNum
        accountsPickList: function (url) {
            //url = url + '/returnModule/' + '<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>';
            $('#globel-dialog-div').dialog({
                title: '选择客户',
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
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;

                                var formArray = $(this).serializeArray();
                                var url = '__URL__/searchviewAddress/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' +
                                        value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订单地址查询');
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
        clearAccounts: function (rowNum) {
            var accountsCode = $("#OrderFormAccountsCode_" + rowNum).val();
            var accountsName = $("#OrderFormAccountsName_" + rowNum).val();

            if ((accountsCode == '') && (accountsName == '')) {
                return;
            }
            if (confirm("是否要清空内容")) {
                $("#OrderFormAccountsCode_" + rowNum).val('');
                $("#OrderFormAccountsName_" + rowNum).val('');
                $("#OrderFormAccountsMoney_" + rowNum).val('');
                $("#OrderFormAccountsNote_" + rowNum).val('');
                this.sumAccountsMoney(rowNum);
            }
        },

        /*  删除客户最后一行 */
        delAccounts: function () {
            //取得表格行的长度
            var rowNum = $("#OrderFormAccountsTable tr").length;
            if (rowNum == 2) {
                alert("最后一行不能删除");
                return;
            }
            $("#OrderFormAccountsTable tr:last").remove();
            $("#OrderFormAccountsLength").attr("value", rowNum - 1); //表格行数保存
            this.sumAccountsMoney(rowNum);

        },


        //根据用户输入的客户代码，输出产品名称
        getAccounts: function (rowNum, code) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountPaymentByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        //首先检查父窗口表格是否有存在输入的代码和产品
                        var rowLength = $("#OrderFormAccountsPaymentTable tr").length;
                        for (i = 1; i < rowLength; i++) {
                            accountsName = $("OrderFormAccountsName_" + i).val();
                            if ((accountsName == mydata.name) && (i != rowNum)) {
                                alert('客户已经存在,不能添加！');
                                return;
                            }
                        }
                        $("#OrderFormAccountsName_" + rowNum).val(mydata.name);
                        //将支付内容加在订单地址上，临时做法
                        if ($('#OrderFormAction').val() == 'Createview') {
                            address = $('#OrderFormCreateviewForm input[name=address]').val();
                            address = '(' + mydata.name + ')' + address;
                            $('#OrderFormCreateviewForm input[name=address]').val(address);
                        } else {
                            address = $('#OrderFormEditviewForm input[name=address]').val();
                            address = '(' + mydata.name + ')' + address;
                            $('#OrderFormEditviewForm input[name=address]').val(address);
                        }

                        //跳转到下一行
                        $("#OrderFormAccountsMoney_" + rowNum).focus();
                    } else {
                        alert("输入的客户代码有错误，请重新输入！");
                        return;
                    }
                }

            })
        },


        /* 计算产品金额 */
        sumAccountsMoney: function (rowNum) {

            //计算全部的金额
            var totalMoney = 0;
            //取得表格行的长度
            var rowLength = $("#OrderFormAccountPaymentTable tr").length;
            for (i = 1; i < rowLength; i++) {
                if ($("#OrderFormAccountsMoney_" + i).val() > 0) {
                    totalMoney = totalMoney + parseFloat($("#OrderFormAccountsMoney_" + i).val());
                }
            }
            $("#OrderFormAccountPaymentTotalMoney").val(totalMoney); //小计

        },


        //产品代码失去焦点的计算金额
        blurSumAccountsMoney: function (rowNum, obj) {
            if ($("#OrderFormAccountsName_" + rowNum).val() != '') { //如果不为空，才计算
                this.sumAccountsMoney(rowNum);
            }
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
    var accountsAjax = false; //判断是否查询过产品代码，防止在ajax中按回车，反复执行，有个alert的小bug
</script>
                                </td>
                            </tr>
                        </table>


                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:2px; border: 1px solid #e0dddd; ">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    订单状态
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td colspan="4">
                                    <table border="0" cellpadding="0" cellspacing="0" width="80%">
    <tr>
        <?php if($orderstate["create"] == 1): ?><td>
                <img src=".__PUBLIC__/Images/lhkc/mid.png" alt="">
            </td>
            <td>
                <img src=".__PUBLIC__/Images/lhkc/narr.png" alt="">
            </td><?php endif; ?>
        <?php if($orderstate["distribution"] == 1): ?><td>
                <img src=".__PUBLIC__/Images/lhkc/mid.png" alt="">
            </td>
            <td>
                <img src=".__PUBLIC__/Images/lhkc/narr.png" alt="">
            </td><?php endif; ?>
        <?php if($orderstate["handle"] == 1): ?><td>
                <img src=".__PUBLIC__/Images/lhkc/mid.png" alt="">
            </td>
            <td>
                <img src=".__PUBLIC__/Images/lhkc/narr.png" alt="">
            </td><?php endif; ?>
        <?php if($orderstate["success"] == 1): ?><td>
                <img src=".__PUBLIC__/Images/lhkc/mid.png" alt="">
            </td>
            <td> 
                <img src=".__PUBLIC__/Images/lhkc/narr.png" alt="">
            </td><?php endif; ?>
        <?php if($orderstate["cancel"] == 1): ?><td>
                <img src=".__PUBLIC__/Images/lhkc/mid.png" alt="">
            </td>
            <td>
                <img src=".__PUBLIC__/Images/lhkc/narr.png" alt="">
            </td><?php endif; ?>
        <td align="right">
            <span>订单位置</span>
        </td>
        <td align="left">
            <a onclick="alert('ok');" href="#">
                <img src=".__PUBLIC__/Images/lhkc/map.png" alt="" style="width:30px;heigth:20px;">
            </a>
        </td>


    </tr>
    <tr>
        <?php if($orderstate["create"] == 1): ?><td colspan="2">
                订单生成 <?php echo (substr($orderstate["createtime"],11,9)); echo ($orderstate["createcontent"]); ?>
            </td><?php endif; ?>
        <?php if($orderstate["distribution"] == 1): ?><td colspan="2">
                订单分配 <?php echo (substr($orderstate["distributiontime"],11,9)); echo ($orderstate["distributioncontent"]); ?>
            </td><?php endif; ?>
        <?php if($orderstate["handle"] == 1): ?><td colspan="2">
                订单配送 <?php echo (substr($orderstate["handletime"],11,9)); echo ($orderstate["handlecontent"]); ?>
            </td><?php endif; ?>
        <?php if($orderstate["success"] == 1): ?><td colspan="2">
                配送完毕 <?php echo (substr($orderstate["successtime"],11,9)); echo ($orderstate["successcontent"]); ?>
            </td><?php endif; ?>
        <?php if($orderstate["cancel"] == 1): ?><td colspan="2">
                <label style="color: red;">退餐 <?php echo (substr($orderstate["canceltime"],11,9)); echo ($orderstate["cancelcontent"]); ?> </label>
            </td><?php endif; ?>
    </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="100%" class="dvtCellLabel" align=center colspan="4">
            <table style="border: 1px solid #BEBEBE;" width="95%">
                <?php if(is_array($orderaction)): foreach($orderaction as $key=>$vo): ?><tr>
                        <td width="20%"><?php echo ($vo["logtime"]); ?></td>
                        <td><?php echo ($vo["action"]); ?></td>
                    </tr><?php endforeach; endif; ?>
            </table>
        </td>
    </tr>
</table>
                                </td>
                            </tr>
                        </table>

            <tr style="line-height: 5px;">
                <td>&nbsp;</td>
            </tr>

            <tr style="line-height: 5px;">
                <td colspan="4" align="center">
                    <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-other-tick'" onclick="OrderFormEditviewModule.update();"
                        style="width:100px;margin-right:40px;">确认</a>
                    <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-arrow-cross'" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>');"
                        style="width:100px;">放弃</a>
                </td>
            </tr>
</div>
</td>
</tr>
</table>
</form>
</div>
<input id="OrderFormAction" type="hidden" value="Editview" />
<script>
    var OrderFormEditviewModule = {
        dialog: '#globel-dialog-div',
        checkSubmitFlg: false,

        //初始化
        init: function () {
            $('.moduleOperater').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            this.keyboardMove();
        },

        //保存记录
        update: function () {
            //防止重复提交
            if (this.checkSubmitFlg == false) {
                this.checkSubmitFlg = true;
            } else {
                return;
            }
            $('#OrderFormEditviewForm').form('submit', {
                url: '__URL__/update/returnAction/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>',
                onSubmit: function () {
                    //进行表单验证
                    if ($('#OrderFormEditviewForm  input[name=address]').val() == '') {
                        alert('地址不能为空!');
                        OrderFormEditviewModule.checkSubmitFlg = false;
                        return false;
                    }

                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_1]').val();
                    if (custtime_1 > 24 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-24之间的数字!');
                        OrderFormEditviewModule.checkSubmitFlg = false;
                        return false;
                    };

                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_2]').val();
                    if (custtime_1 > 60 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-60之间的数字!');
                        OrderFormEditviewModule.checkSubmitFlg = false;
                        return false;
                    }

                    //对金额进行校验
                    productsMoney = $('#OrderFormEditviewproductsTotalMoney').val(); //产品金额
                    if (!productsMoney) {
                        productsMoney = 0;
                    } else {
                        productsMoney = parseFloat(productsMoney);
                    }

                    activityMoney = $('#OrderFormEditviewactivityTotalMoney').val(); //活动金额  
                    if (!activityMoney) {
                        activityMoney = 0;
                    } else {
                        activityMoney = parseFloat(activityMoney);
                    }

                    accountpaymentMoney = $("#OrderFormAccountPaymentTotalMoney").val(); //客户支付金额
                    if (!accountpaymentMoney) {
                        accountpaymentMoney = 0;
                    } else {
                        accountpaymentMoney = parseFloat(accountpaymentMoney);
                    }

                    if (accountpaymentMoney > 0) {
                        checkactivitypaymentMoney = activityMoney + accountpaymentMoney;
                        if (productsMoney !== checkactivitypaymentMoney) {
                            alert('活动金额和支付金额不等于订餐金额，请检查！');
                            OrderFormEditviewModule.checkSubmitFlg = false;
                            return false;
                        }
                    }

                    /**
                    var invoice_invoicetype = $('#invoice_type').val();
                    if(invoice_invoicetype == '电子票'){
                        var telphone = $('#OrderFormEditviewForm input[name=telphone]').val();
                        var telReg = !!telphone.match(/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57]|19[0-9])[0-9]{8}$/);
                        //如果手机号码不能通过验证
                        if(telReg == false){
                            alert('输入的不是电话号码');
                            OrderFormEditviewModule.checkSubmitFlg = false;
                            return false;
                        }
                    }

                    var invoice_header = $('#invoice_header').val();
                    if(invoice_header){
                        var invoice_nsrsbh = $('#invoice_gmf_nsrsbh').val();
                        if(!invoice_nsrsbh){
                            alert('购买方纳税人识别号(税号)不能为空!');
                            OrderFormEditviewModule.checkSubmitFlg = false;
                            return false;
                        }
                    }
                     **/

                },
                success: function (res) {
                    var data = eval('(' + res + ')');
                    if (!data.status) {
                        $.app.method.tip('提示信息', data.info, 'error');
                        OrderFormEditviewModule.checkSubmitFlg = false;
                        return false;
                    } else {
                        $.app.method.tip('提示信息', data.info, 'info');
                        IndexIndexModule.updateOperateTab(data.url);
                    }
                }
            });
        },

        //键盘移动方案
        keyboardMove: function () {
            //定制键盘移动方案
            $('#OrderFormEditviewForm input[name=clientname]').bind('keydown', function (event) { //联系人
                if ((event.which == 13) || (event.which == 40)) {
                    $('#OrderFormEditviewForm input[name=telphone]').focus();
                }
                if (event.which == 38) {

                }
            })
            //电话移动
            $('#OrderFormEditviewForm input[name=telphone]').bind('keydown', function (event) {
                if ((event.which == 13) || (event.which == 40)) { //下移
                    var address = $('#address').val();
                    $('#OrderFormEditviewForm input[name=address]').focus();
                    $('#OrderFormEditviewForm input[name=address]').val();
                    $('#OrderFormEditviewForm input[name=address]').val(address);

                }
                if (event.which == 38) { //上移
                    $('#OrderFormEditviewForm input[name=clientname]').focus();
                }
            })

            //地址移动
            $('#OrderFormEditviewForm input[name=address]').bind('keydown', function (event) {
                if ((event.which == 13) || (event.which == 40)) { //下移
                    $('#OrderFormEditviewForm input[name=custtime_1]').focus();
                }
                if (event.which == 38) { //上移
                    $('#OrderFormEditviewForm input[name=telphone]').focus();
                }

            })

            //要餐时间1移动
            $('#OrderFormEditviewForm input[name=custtime_1]').bind('keydown', function (event) {
                if ((event.which == 13) || (event.which == 40)) { //下移
                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_1]').val();
                    if (custtime_1 > 24 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-24之间的数字!');
                        return false;
                    }
                    $('#OrderFormEditviewForm input[name=custtime_2]').focus();
                }
                if (event.which == 38) { //上移
                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_1]').val();
                    if (custtime_1 > 24 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-24之间的数字!');
                        return false;
                    }
                    var address = $('#OrderFormEditviewForm input[name=address]').val();
                    $('#OrderFormEditviewForm input[name=address]').focus();
                    $('#OrderFormEditviewForm input[name=address]').val('');
                    $('#OrderFormEditviewForm input[name=address]').val(address);
                }
            })

            //要餐时间2移动
            $('#OrderFormEditviewForm input[name=custtime_2]').bind('keydown', function (event) {
                if ((event.which == 13) || (event.which == 40)) { //下移
                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_2]').val();
                    if (custtime_1 > 60 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-60之间的数字!');
                        return false;
                    }
                    $('#OrderFormEditviewForm input[name=beizhu]').focus();
                }
                if (event.which == 38) { //上移
                    var custtime_1 = $('#OrderFormEditviewForm input[name=custtime_2]').val();
                    if (custtime_1 > 60 || custtime_1 < 0) {
                        alert('时间输入不正确!请输入1-60之间的数字!');
                        return false;
                    }
                    $('#OrderFormEditviewForm input[name=custtime_1]').focus();
                }
            })

            //备注的移动
            $('#OrderFormEditviewForm input[name=beizhu]').bind('keydown', function (event) {
                if ((event.which == 13) || (event.which == 40)) { //下移
                    $('#OrderFormEditviewForm input[name=productsNumber_1]').focus();

                }
                if (event.which == 38) { //上移
                    $('#OrderFormEditviewForm input[name=custtime_2]').focus();
                }

            })

            //产品1的移动
            $('#OrderFormEditviewForm input[name=productsNumber_1]').bind('keydown', function (event) {
                if (event.which == 38) { //上移
                    $('#OrderFormEditviewForm input[name=beizhu]').focus();
                }

            })

            //送餐费的计算
            $('#OrderFormEditviewForm input[name=shippingmoney]').bind('keydown', function (event) {
                if (event.which == 13) {
                    //计算全部的金额
                    var totalMoney = 0;
                    //取得表格行的长度
                    var rowLength = $("#OrderFormEditviewproductsTable tr").length;
                    for (i = 1; i < rowLength; i++) {
                        if ($("#OrderFormEditviewproductsMoney_" + i).val() > 0) {
                            totalMoney = totalMoney + parseFloat($(
                                "#OrderFormEditviewproductsMoney_" +
                                i).val());
                        }
                    }

                    //加上送餐费
                    var shippingmoney = 0;
                    var shippingmoneyVal = $('#OrderFormEditviewForm input[name=shippingmoney]')
                        .val();
                    if (shippingmoneyVal) {
                        shippingmoney = parseFloat(shippingmoneyVal);
                    }
                    totalMoney = totalMoney + shippingmoney;
                    totalMoney = parseFloat(totalMoney).toFixed(2);
                    $('#OrderFormEditviewFormTotalMoney').html(totalMoney + '元'); //订单总金额
                    $('#OrderFormEditviewForm input[name=totalmoney]').val(totalMoney);
                    $('#OrderFormEditviewFormShouldMoney').html(totalMoney + '元'); //应收金额
                    $('#OrderFormEditviewForm input[name=shouldmoney]').val(totalMoney);
                    //
                    $('#OrderFormEditviewForm input[name=productsNumber_1]').focus();

                }
            })
        },


        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+9快捷键,新建公告
            Mousetrap.bind(['ctrl+9', 'ctrl+f9', 'f9'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Editview')) {
                OrderFormEditviewModule.update();
                //};
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Editview')) {
                IndexIndexModule.updateOperateTab("__URL__/<?php echo ($returnAction); ?>");
                //};
            });

            // ctrl+0快捷键,查看今日菜单
            Mousetrap.bind(['ctrl+0', 'ctrl+f10', 'f10'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Duplicateview')) {
                OrderFormEditviewModule.showTodayMenuview();
                //};
            });
        },

        showTodayMenuview: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '今日菜单查询',
                iconCls: 'icons-application-application_add',
                width: 600,
                height: 540,
                cache: false,
                href: "<?php echo U('OrderForm/showToaymenuview');?>",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '关闭',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        }
    }

    $(function () {
        OrderFormEditviewModule.init();
    })
</script>