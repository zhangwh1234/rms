<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;查看操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="PaymentEditviewModule.update();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="PaymentEditviewModule.update();">保存订单<span>^9</span></a></li>

        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/checkOrder/name/<?php echo ($name); ?>/room_date/<?php echo ($custdate); ?>/room_ap/<?php echo ($custap); ?>');">返回列表</a><span>^4</span></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <div style="clear:both;"></div>
    </ul>
</div>

<style>
    .moduleOperater {
        clear: both;
        margin: 0px;
        padding: 0px;
        overflow: scroll;
    }

    .detailviewLeftLableTd {
        border-style: solid none none solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
        padding-top: 1px;
        padding-bottom: 1px;
        height: 30px;
    }

    .detailviewLeftTd {
        border-style: solid none none solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
        padding-top: 1px;
        padding-bottom: 1px;
        background-color: #F5F5F5;
    }

    .createFormRightTd {
        border-style: solid solid none solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
    }

    .createFormLeftBottomTd {
        border-style: solid none solid solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
        padding-top: 1px;
        padding-bottom: 1px;
    }

    .createFormRightBottomTd {
        border-style: solid solid solid solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
    }

    .detailviewcella {
        background-color: #F0F0F0;
        line-height: 32px;
        height: 32px;
        font-size: 16px;
    }

    .detailviewRightBottomTd {
        border-style: solid solid solid solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-left: 10px;
        background-color: #F5F5F5;
    }

    .detailviewForm {
        background: white;
    }

    /*订单主表*/
    #detailviewOrderFormBaseTable {
        width: 100%;
    }

    #detailviewOrderFormBaseTable td {
        height: 25px;
    }

    /*提示*/
    .detailviewLableSpan {
        font-size: 14px;
        margin-right: 10px;
    }

    /*显示值*/
    .detailviewInputSpan {
        font-size: 16px;
        margin-left: 4px;

    }


</style>
<div class="moduleOperater">
    <form id="PaymentEditviewForm" method="post">
        <input type="hidden" name="custdate" value="<?php echo ($custdate); ?>">
        <input type="hidden" name="custap" value="<?php echo ($custap); ?>">
        <input type="hidden" name="name" value="<?php echo ($name); ?>">
        <input type="hidden" name="record" value="<?php echo ($record); ?>">
        <table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="3" width="100%" class="small">
                        <tr>
                            <td class="dvtTabCache" style="width:20px" nowrap>&nbsp;</td>
                            <td class="dvtSelectedCell" align="center" nowrap> 查看</td>
                            <td class="dvtTabCache" style="width:80%">&nbsp;</td>
                        <tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td valign="top" align="center">
                    <div class="detailviewForm">
                        <table id="detailviewOrderFormBaseTable" style="BORDER-COLLAPSE: collapse" borderColor="#A9A9A9"
                            cellSpacing="0" width="100%" align="center" border="1">
                            <thead>
                <td colspan="4" class="tabBlockViewHeader">
                    订单基本信息
                </td>
                </thead>
            <tr>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">电话:</span>
                </td>
                <td width="50%" align="left" style="background: #F5F5F5;">
                    <!-- 电话 -->
                    <span class="detailviewInputSpan"><?php echo ($info["telphone"]); ?></span>
                </td>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">客户姓名:</span>
                </td>
                <td width="20%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["clientname"]); ?></span>
                </td>
            </tr>
            <tr style="background: #FFFFFF;">
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">地址:</span>
                </td>
                <td width="50%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["address"]); ?></span>
                </td>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">要餐时间:</span>
                </td>
                <td width="20%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["custtime"]); ?></span>
                </td>
            </tr>
            <tr style="background: #FFFFFF;">
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">备注:</span>
                </td>
                <td width="50%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["beizhu"]); ?></span>
                </td>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">送餐费:</span>
                </td>
                <td width="20%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["shippingmoney"]); ?></span>
                </td>
            </tr>
            <tr style="background: #FFFFFF;">
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">分公司:</span>
                </td>
                <td width="50%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["company"]); ?></span>
                </td>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">送餐员:</span>
                </td>
                <td width="20%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan" id="orderformsendname"><?php echo ($info["sendname"]); ?></span>
                    <a id="paymenteditsendname" onclick="PaymentEditviewModule.editSendname('<?php echo ($info["ordersn"]); ?>')">重新选择</a>
                </td>
            </tr>
            <tr style="background: #FFFFFF;">
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">订单金额:</span>
                </td>
                <td width="50%" align="left" style="background: #F5F5F5;">
                    <span class="detailviewInputSpan"><?php echo ($info["totalmoney"]); ?></span>
                    <span style="margin-left:10px; ">&nbsp;</span>
                    <span class="detailviewLableSpan">活动金额:</span>
                    <span class="detailviewInputSpan"><?php echo ($activitymoney); ?></span>
                    <span class="detailviewLableSpan">应收金额:</span>
                    <span class="detailviewInputSpan"><?php echo ($info["shouldmoney"]); ?></span>
                    <input id="paymenttotalmoney" name="paymenttotalmoney" hidden value="<?php echo ($info["totalmoney"]); ?>" />
                    <input id="paymentactivitymoney" name="paymentactivity}" hidden value="<?php echo ($activitymoney); ?>" />
                    <input id="paymentyingshoumoney" name="paymentyingshoumoney" hidden value="<?php echo ($info["shouldmoney"]); ?>" />
                </td>
                <td width="15%" align="right">
                    <span class="detailviewLableSpan">结账金额:</span>
                </td>
                <td width="20%" align="left" style="background: #F5F5F5;">
                    <span id="jiezhangmoney" class="detailviewInputSpan" style="color:red;font-size:18px;font-family: 黑体;"><?php echo ($info["jiezhangmoney"]); ?></span>
                    <input id="paymentjiezhangmoney" name="paymentjiezhangmoney" value="<?php echo ($info["jiezhangmoney"]); ?>" hidden />
                </td>
            </tr>
        </table>

        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px; border: 1px solid #e0dddd; ">
            <tr>
                <td colspan="4" class="tabBlockViewHeader">
                    支付输入
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
<table id="YingshouRoomServiceAccountsTable" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%" align="center" border="1">
    <tr class="accountsTableHeader">
        <td width="5%" align="center" class="accountsTableHeaderLeftTd">序号</td>
        <td width="10%" align="center" class="accountsTableHeaderLeftTd">编号</td>
        <td width="30%" align="center" class="accountsTableHeaderLeftTd">名称</td>
        <td width="15%" align="center" class="accountsTableHeaderLeftTd">金额</td>
        <td width="25%" align="center" class="accountsTableHeaderLeftTd">备注</td>
        <td width="10%" align="center" class="accountsTableHeaderRightTd">操作</td>
    </tr>
    <?php if(empty($roomserviceaccounts)): $__FOR_START_530373977__=0;$__FOR_END_530373977__=1;for($key=$__FOR_START_530373977__;$key < $__FOR_END_530373977__;$key+=1){ ?><tr>
                <td width="5%" align="center"><?php echo ($key+1); ?></td>
                <td width="10%" align="center"> 
                    <input id="YingshouRoomServiceAccountsCode_<?php echo ($key+1); ?>" name="accountsCode_<?php echo ($key+1); ?>" type="text" size="6" tabindex="1" onkeydown="YingshouRoomServiceAccountsEditViewModule.getAccountsByCode(<?php echo ($key+1); ?>,event,this);"
                        AUTOCOMPLETE="off" style="font-size:16px;" />

                    <img id="YingshouRoomServicesearchIcon1" title="选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;" align="absmiddle" onclick="YingshouRoomServiceAccountsEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');" /><a
                        href="javascript:YingshouRoomServiceAccountsEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
                </td>
                <td width="30%" align="center"> 
                    <input id="YingshouRoomServiceAccountsName_<?php echo ($key+1); ?>" name="accountsName_<?php echo ($key+1); ?>" type="text" size="30" readonly="readonly" style="font-size:16px;" />
                </td>
                <td width="15%" align="center"> 
                    <input id="YingshouRoomServiceAccountsMoney_<?php echo ($key+1); ?>" name="accountsMoney_<?php echo ($key+1); ?>" type="text" size="10" onkeydown="YingshouRoomServiceAccountsEditViewModule.keydownSumAccountsMoney('<?php echo ($key+1); ?>',event,this);"
                        onblur="YingshouRoomServiceAccountsEditViewModule.blurSumAccountsMoney();" tabindex="1" value="" style="font-size:16px;" />
                </td>
                <td width="25%" align="center"> 
                    <input id="YingshouRoomServiceAccountsNote_<?php echo ($key+1); ?>" name="accountsNote_<?php echo ($key+1); ?>" type="text" size="30" onkeydown="YingshouRoomServiceAccountsEditViewModule.keydownAccountsNote('<?php echo ($key+2); ?>',event);"
                        style="font-size:16px;" />
                </td>
                <td width="10%" align="center"><a href="#" onclick="YingshouRoomServiceAccountsEditViewModule.clearAccounts(<?php echo ($key+1); ?>);">清空</a>
                </td>
            </tr><?php } endif; ?>
    <?php if(is_array($roomserviceaccounts)): foreach($roomserviceaccounts as $key=>$vo): ?><tr>
            <td width="5%" align="center"><?php echo ($key+1); ?></td>
            <td width="10%" align="center"> 
                <input id="YingshouRoomServiceAccountsCode_<?php echo ($key+1); ?>" name="accountsCode_<?php echo ($key+1); ?>" type="text" size="6" tabindex="1" value="<?php echo ($vo["paymentid"]); ?>" onkeydown="YingshouRoomServiceAccountsEditViewModule.getAccountsByCode(<?php echo ($key+1); ?>,event,this);"
                    AUTOCOMPLETE="off" style="font-size:16px;" />
                <img id="YingshouRoomServiceAccountssearchIcon1" title="客户选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;" align="absmiddle" onclick="YingshouRoomServiceAccountsEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');" /><a
                    href="javascript:YingshouRoomServiceAccountsEditViewModule.accountsPickList('__URL__/popupPaymentMgrview/module/PaymentMgr/row/<?php echo ($key+1); ?>')">选择</a>
            </td>
            <td width="30%" align="center"> 
                <input id="YingshouRoomServiceAccountsName_<?php echo ($key+1); ?>" name="accountsName_<?php echo ($key+1); ?>" type="text" size="30" readonly="readonly" value="<?php echo ($vo["name"]); ?>" style="font-size:16px;" />
            </td>
            <td width="15%" align="center"> 
                <input id="YingshouRoomServiceAccountsMoney_<?php echo ($key+1); ?>" name="accountsMoney_<?php echo ($key+1); ?>" type="text" size="10" onkeydown="YingshouRoomServiceAccountsEditViewModule.keydownSumAccountsMoney();"
                    onblur="YingshouRoomServiceAccountsEditViewModule.blurSumAccountsMoney();" tabindex="1" value="<?php echo ($vo["money"]); ?>" style="font-size:16px;" />
            </td>
            <td width="25%" align="center"> 
                <input id="YingshouRoomServiceAccountsNote_<?php echo ($key+1); ?>" name="accountsNote_<?php echo ($key+1); ?>" type="text" size="30" value="<?php echo ($vo["note"]); ?>" style="font-size:16px;" />
            </td>
            <td width="10%" align="center"><a href="#" onclick="YingshouRoomServiceAccountsEditViewModule.clearAccounts('<?php echo ($key+1); ?>');">清空</a>
            </td>
        </tr><?php endforeach; endif; ?>
</table>
<table width="100%" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%" align="center" border="1">
    <tr>
        <td class="accountsTableXiaojiLeftTd" width="65%">
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_add'" onclick="YingshouRoomServiceAccountsEditViewModule.addAccounts();" style="font-size:16px;">添加项目</a>
            <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-car_cart_basket-basket_delete'" onclick="YingshouRoomServiceAccountsEditViewModule.delAccounts();" style="font-size:16px;margin-left:30px;">删除项目最后一行</a>
            <input id="YingshouRoomServiceAccountsLength" name="accountsLength" type="hidden" value="<?php echo ($key+1); ?>" />
        </td>

        <td style="font-size: 16px; display: none;">
        </td>
        <td class="accoutsTableXiaojiRightTd" style="font-size: 14px;"> 
            <span>小计</span>
            <input id="YingshouRoomServiceAccountsTotalMoney" name="paymentTotalMoney" type="text" size="10" readonly="readonly" style="border: 0px;font-size: 14px;" value="<?php echo ($orderpaymentmoney); ?>" />
            <input id="YingshouRoomServiceActivityMoney" name="activityTotalMoney" value="<?php echo ($activitymoney); ?>" hidden />
        </td>
    </tr>
</table>

<script type="text/javascript">
    var YingshouRoomServiceAccountsEditViewModule = {

        init: function () {},

        test: function () {
            alert('ok');
        },

        //添加产品
        addAccounts: function () {
            //取得表格行的长度
            var rowNum = $("#YingshouRoomServiceAccountsTable tr").length;

            var tableTrAppend = "<tr> ";
            tableTrAppend += "<td width='5%' align='center' class='accountsTableLeftTd'>" + rowNum + "</td> ";
            tableTrAppend += "<td width='15%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input class='easyui-numberbox' id='YingshouRoomServiceAccountsCode_" + rowNum +
                "' name='accountsCode_" + rowNum +
                "'  type='text' size='6' tabindex='1' onkeyup='YingshouRoomServiceAccountsEditViewModule.getAccountsByCode(" +
                rowNum + ",event,this)' style='font-size:16px;' />";
            tableTrAppend += "<img id='YingshouRoomServicesearchIcon1' title='客户选择' src='./" + PUBLIC +
                "/Images/products.gif' style='cursor: pointer;' align='absmiddle' onclick='YingshouRoomServiceAccountsEditViewModule.accountsPickList(\"__URL__/popupAccountsview/module/Accounts/row/" +
                rowNum + "\");' />";
            tableTrAppend +=
                "<a href='javascript:YingshouRoomServiceAccountsEditViewModule.accountsPickList(\"__URL__/popupAccountsview/module/Accounts/row/" +
                rowNum + "\");' >选择</a>";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='30%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='YingshouRoomServiceAccountsName_" + rowNum + "' name='accountsName_" +
                rowNum + "' type='text' size='30'  style='font-size:16px;' readonly />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='15%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='YingshouRoomServiceAccountsMoney_" + rowNum + "' name='accountsMoney_" +
                rowNum +
                "' type='text' size='10'   tabindex='1' value='' style='font-size:16px;' onkeydown=\"YingshouRoomServiceAccountsEditViewModule.keydownSumAccountsMoney();\"\n" +
                "                       onblur=\"YingshouRoomServiceAccountsEditViewModule.blurSumAccountsMoney();\" />";
            tableTrAppend += "</td>";
            tableTrAppend += "<td width='25%' align='center' class='accountsTableLeftTd'>";
            tableTrAppend += "<input id='YingshouRoomServiceAccountsNote_" + rowNum + "' name='accountsNote_" +
                rowNum + "' type='text' size='30'  style='font-size:16px;'  />";
            tableTrAppend += "</td>";
            tableTrAppend +=
                "<td width='10%' align='center' class='accountsTableLeftTd'><a href='#' onclick='YingshouRoomServiceAccountsEditViewModule.clearAccounts(" +
                rowNum + ");' >清空</a></td>";
            tableTrAppend += "</tr>";
            $("#YingshouRoomServiceAccountsTable").append(tableTrAppend);
            $("#YingshouRoomServiceAccountsLength").attr("value", rowNum + 1); //表格行数保存
        },

        /* 键盘回车计算产品金额 */
        keydownSumAccountsMoney: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                if ($("#YingshouRoomServiceAccountsName_" + rowNum).val() != '') { //如果不为空，才计算
                    this.sumAccountsMoney(rowNum);
                }
                $("#YingshouRoomServiceAccountsNote_" + rowNum).focus();
            }
            //向上移动一个
            if (key == 38) {
                var focusNum = rowNum - 1;
                if (focusNum > 0) {
                    $("#YingshouRoomServiceAccountsCode_" + focusNum).focus();
                }
            }
            //向下移动
            if (key == 40) {
                $("#YingshouRoomServiceAccountsCode_" + rowNum).focus();
            }
        },

        /**
         * 备注的键盘回车处理
         */
        keydownAccountsNote: function (rowNum, evt) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                $("#YingshouRoomServiceAccountsCode_" + rowNum).focus();
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
                $("#YingshouRoomServiceAccountsCode_" + rowNum).focus();
            }
            //向下移动
            if (key == 40) {
                var focusNum = rowNum + 1;
                $("#YingshouRoomServiceAccountsCode_" + focusNum).focus();
            }

        },

        /* 弹出窗口，选择产品 */
        //moduleName:产品名称  rowNum:行号 moduleName,rowNum
        accountsPickList: function (url) {
            url = url + '/returnModule/' + '<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>';
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
            var accountsCode = $("#YingshouRoomServiceAccountsCode_" + rowNum).val();
            var accountsName = $("#YingshouRoomServiceAccountsName_" + rowNum).val();

            if ((accountsCode == '') && (accountsName == '')) {
                return;
            }
            if (confirm("是否要清空内容")) {
                $("#YingshouRoomServiceAccountsCode_" + rowNum).val('');
                $("#YingshouRoomServiceAccountsName_" + rowNum).val('');
                $("#YingshouRoomServiceAccountsMoney_" + rowNum).val('');
                $("#YingshouRoomServiceAccountsNote_" + rowNum).val('');
                this.sumAccountsMoney(rowNum);
            }
        },

        /*  删除客户最后一行 */
        delAccounts: function () {
            //取得表格行的长度
            var rowNum = $("#YingshouRoomServiceAccountsTable tr").length;
            if (rowNum == 2) {
                alert("最后一行不能删除");
                return;
            }
            $("#YingshouRoomServiceAccountsTable tr:last").remove();
            $("#YingshouRoomServiceAccountsLength").attr("value", rowNum - 1); //表格行数保存
            this.sumAccountsMoney(rowNum);

        },


        //根据用户输入的客户代码，输出产品名称
        getAccounts: function (rowNum, code) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    var jiezhangmoney = 0; //结账金额
                    if (mydata) {
                        //首先检查父窗口表格是否有存在输入的代码和产品
                        var rowLength = $("#YingshouRoomServiceAccountsTable tr").length;
                        for (i = 1; i < rowLength; i++) {
                            accountsName = $("YingshouRoomServiceAccountsName_" + i).val();
                            if ((accountsName == mydata.name) && (i != rowNum)) {
                                alert('客户已经存在,不能添加！');
                                return;
                            }
                            if ($("#YingshouRoomServiceAccountsMoney_" + i).val() != '') {
                                jiezhangmoney = jiezhangmoney + parseFloat($("#YingshouRoomServiceAccountsMoney_" + i).val());
                            }
                            console.log(i);
                            console.log($("#YingshouRoomServiceAccountsMoney_" + i).val());
                        }
                        $("#YingshouRoomServiceAccountsName_" + rowNum).val(mydata.name);

                        jiezhangmoney = parseFloat($('#paymenttotalmoney').val()) - jiezhangmoney;
                        //跳转到下一行
                        $("#YingshouRoomServiceAccountsMoney_" + rowNum).val(jiezhangmoney);
                        $("#YingshouRoomServiceAccountsMoney_" + rowNum).focus();
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
            var rowLength = $("#YingshouRoomServiceAccountsTable tr").length;
            for (i = 1; i < rowLength; i++) {
                if ($("#YingshouRoomServiceAccountsMoney_" + i).val() > 0) {
                    totalMoney = totalMoney + parseFloat($("#YingshouRoomServiceAccountsMoney_" + i).val());
                }
            }
            $("#YingshouRoomServiceAccountsTotalMoney").val(totalMoney); //小计

            //活动金额
            activitymoney = $('#YingshouRoomServiceActivityMoney').val();
            //计算总金额
            if (activitymoney) {
                totalMoney = totalMoney + parseFloat(activitymoney);
            }

            //写入总的金额
            totalMoney = totalMoney.toFixed(2);
            $('#jiezhangmoney').html(totalMoney);
            $('#PaymentEditviewForm input[name=paymentjiezhangmoney]').val(totalMoney);

            $('#YingshouRoomServiceEditviewForm input[name=note]').val(totalMoney);

        },


        //产品代码失去焦点的计算金额
        blurSumAccountsMoney: function (rowNum, obj) {
            if ($("#YingshouRoomServiceAccountsName_" + rowNum).val() != '') { //如果不为空，才计算
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

        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top:5px; border: 1px solid #e0dddd; ">
            <tr>
                <td colspan="4" class="tabBlockViewHeader">
                    促销信息
                </td>
            </tr>
            <tr style="background: #FFFFFF;">
                <td colspan="4">
                    <style>
    #DetailOrderFormActivity td{
        height: 25px;
        border-style:solid none none solid;
        border-width:1px;
        border-color: #CCCCCC;
        font-size:14px;
    }


</style>
<table id="DetailOrderFormActivity" style="BORDER-COLLAPSE: collapse"  borderColor="white" cellSpacing="0" width="100%"
       align="center" border="1">
    <?php if(is_array($orderactivity)): foreach($orderactivity as $key=>$vo): ?><tr>
        <td width="10%"  align=right>
            <span style="margin-right:5px;">活动名称:</span></td>
        <td width="45%" align=left class="dvtCellInfo">
            <span style="background: #F5F5F5;"><?php echo ($vo["name"]); ?></span>
        </td>
        <td width="10%"  align=right>
            <span style="margin-right:5px;">活动金额:</span></td>
        <td width="45%" align=left >
            <span style="background: #F5F5F5;"><?php echo ($vo["money"]); ?></span>
        </td>
    </tr><?php endforeach; endif; ?>
</table>
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
    #productsTable{
        background-color: transparent;
    }


    .productsTableHeader{
        background-color: #008B00;
        font-size:12px;
    }

    .productsTableHeaderLeftTd{
        border-style:solid none none solid;
        border-width:1px;
        border-color: #CCCCCC;
        padding-top:2px;
        padding-bottom: 2px;
        font-size:14px;
    }


    .productsTableXiaojiLeftTd{
        border-style:solid none solid solid;
        border-width:1px;
        border-color: #CCCCCC;
        padding-top:1px;
        padding-bottom: 1px;
        padding-left:40px;
        height: 22px;
    }

    .productsTableXiaojiRightTd{
        border-style:solid solid solid solid;
        border-width:1px;
        border-color: #CCCCCC;
        padding-top:1px;
        padding-bottom: 1px;
        padding-left:20px;
    }

    #productsTable td{
        height: 25px;
        border-style:solid none none solid;
        border-width:1px;
        border-color: #CCCCCC;
    }

    #productsTable span{
        font-size:16px;
    }

</style>
<table id="productsTable" width="100%" border="0" cellspacing="0" cellpadding="0"  borderColor="white" border="1">
    <thead class="productsTableHeader">
        <td width="5%" align="center"  class="productsTableHeaderLeftTd">序号</td>
        <td width="12%" align="center" class="productsTableHeaderLeftTd">数量</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">产品代码</td>
        <td width="30%" align="center" class="productsTableHeaderLeftTd">产品名称</td>
        <td width="12%" align="center" class="productsTableHeaderLeftTd">单价</td>
        <td width="15%" align="center" class="productsTableHeaderLeftTd">金额</td>
    </thead>

    <?php if(is_array($orderproducts)): foreach($orderproducts as $key=>$vo): ?><tr  style="background-color: #F5F5F5;" >
            <td width="5%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
            <td width="15%" align="center" class="dvtCellLabel"> 
                <span> <?php echo ($vo["number"]); ?></span>
            </td>
            <td width="10%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["code"]); ?></span>
            </td>
            <td width="30%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["name"]); ?></span>
            </td>
            <td width="15%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["price"]); ?></span>
            </td>
            <td width="15%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["money"]); ?></span>
            </td>
        </tr><?php endforeach; endif; ?>

</table>
<table  width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="productsTableXiaojiLeftTd" width="65%">

        </td>
        <td style="font-size: 16px;" class="productsTableXiaojiRightTd">
            <span>份数:</span>
            <span id="productsTotalNumber"><?php echo ($productstotalnumber); ?></span>
        </td>

        <td class="productsTableXiaojiRightTd"> 
            <span style="font-size:14px;">小计</span>
            <span style="font-size:16px;"><?php echo ($info["goodsmoney"]); ?></span>
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
<input id="PaymentEditviewAction" type="hidden" value="Editview" />

<script>
    var PaymentEditviewModule = {
        dialog: '#globel-dialog-div',
        checkSubmitFlg: false,

        //初始化
        init: function () {
            $('.moduleOperater').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            $("#YingshouRoomServiceAccountsCode_1").focus();
        },

        //保存记录
        update: function () {
            //防止重复提交
            if (this.checkSubmitFlg == false) {
                this.checkSubmitFlg = true;
            } else {
                return;
            }
            $('#PaymentEditviewForm').form('submit', {
                url: '__URL__/update/returnAction/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>',
                onSubmit: function () {
                    //进行表单验证
                    if ($('#PaymentEditviewForm  input[name=jiezhangmoney]').val() == '') {
                        alert('结账金额不能为空!');
                        PaymentEditviewModule.checkSubmitFlg = false;
                        return false;
                    }
                    //金额验证
                    totalmoney = parseFloat($('#PaymentEditviewForm input[name=paymenttotalmoney]')
                        .val());
                    totalmoney = totalmoney.toFixed(2);
                    jiezhangmoney = parseFloat($('#PaymentEditviewForm input[name=paymentjiezhangmoney]').val());
                    jiezhangmoney = jiezhangmoney.toFixed(2);
                    if (totalmoney !== jiezhangmoney) {
                        alert('结账金额不等于订单金额!');
                        PaymentEditviewModule.checkSubmitFlg = false;
                        return false;
                    }

                },
                success: function (res) {
                    var data = eval('(' + res + ')');
                    if (!data.status) {
                        $.app.method.tip('提示信息', data.info, 'error');
                        PaymentEditviewModule.checkSubmitFlg = false;
                        return false;
                    } else {
                        $.app.method.tip('提示信息', data.info, 'info');
                        IndexIndexModule.updateOperateTab(data.url);
                    }
                }
            });
        },

        //对送餐员姓名编辑的操作
        editSendname: function (ordersn) {
            custdate = $('#PaymentEditviewForm input[name=custdate]').val();
            custap = $('#PaymentEditviewForm input[name=custap]').val();
            url = "__URL__/getSendnameView";
            $('#globel-dialog-div').dialog({
                title: '选择送餐员',
                iconCls: 'icons-application-application_add',
                width: 400,
                height: 200,
                cache: false,
                href: url,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: true,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        sendname = $(
                            '#PaymentEditformSelectSendname select[name=sendnameselect]'
                        ).val();
                        //设置送餐员
                        $("#orderformsendname").html(sendname);
                        $('#globel-dialog-div').dialog('close');
                        //写入数据库
                        url = "__URL__/setSendname/sendname/" + sendname + "/ordersn/" +
                            ordersn;
                        $.ajax({
                            type: "GET",
                            url: url,
                            dataType: "json",
                            success: function (data) {}
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
        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1', 'ctrl+f1', 'f1'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Detailview')) {
                IndexIndexModule.updateOperateTab(
                    '__URL__/createview/returnAction/<?php echo ($returnAction); ?>');
                //};
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' ) {
                IndexIndexModule.updateOperateTab(
                    "__URL__/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>");
                //};
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+5', 'ctrl+f5', 'f5'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' ) {
                IndexIndexModule.updateOperateTab(
                    "__URL__/editview/record/<?php echo ($record); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>"
                );
                //};
            });
        }
    }

    $(function () {
        PaymentEditviewModule.init();
    })
</script>