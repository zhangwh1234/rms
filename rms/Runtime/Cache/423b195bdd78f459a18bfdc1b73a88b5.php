<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;查看操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 15px;"><a href="javascript:;" id="showSubMenu" onMouseOver=""><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/checkOrder/name/<?php echo ($name); ?>/room_date/<?php echo ($custdate); ?>/room_ap/<?php echo ($custap); ?>');">返回列表</a><span>^4</span></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <div style="clear:both;"></div>
    </ul>
</div>

<style>

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
        font-size: 16px;
        margin-right: 10px;
    }

    /*显示值*/
    .detailviewInputSpan {
        font-size: 16px;
        margin-left: 4px;

    }


</style>

<div class="moduleOperater">
    <?php if(empty($info)): ?><div class="moduleOperator" style="border: 0px solid lightsteelblue;
         font-size: 26px;line-height: 20px;
         text-align: center;
         margin-top: 50px;color: red;">
            <span>没有需要收入的数据</span>
        </div>
        <?php else: ?>
        <form id="PaymentEditviewForm" method="post">
            <input type="hidden" name="custdate" value="<?php echo ($custdate); ?>">
            <input type="hidden" name="custap" value="<?php echo ($custap); ?>">
            <input type="hidden" name="name" value="<?php echo ($name); ?>">
            <input type="hidden" name="record" value="<?php echo ($record); ?>">
            <?php if(is_array($info)): foreach($info as $key=>$order): ?><table border="0" cellspacing="0" cellpadding="0" width="99%" align="center" style="border:1px solid #cccccc;">
                    <tr>
                        <td colspan="4" class="tabBlockViewHeader">
                            订单基本信息
                        </td>
                    <tr style="height: 30px;background-color:#6699CC;">
                        <td width="15%" align="right" style="font-size: 16px;">
                            <span class="detailviewLableSpan">订单内容:</span>
                        </td>
                        <td width="80%" align="left" style="font-size:16px;">
                            <span class="detailviewLabelSpan"><?php echo ($order["address"]); echo ($order["ordertxt"]); ?> = <?php echo ($order["totalmoney"]); ?></span>
                        </td>
                    </tr>
                </table>

                <!--   支付的输入 -->
                <table id="YingshouRoomServiceAccountsTable" style="BORDER-COLLAPSE: collapse;background-color:#F5F5F5;" cellSpacing="0" width="99%" align="center" border="1">
                    <tr style="height: 30px;">
                        <td width="5%" align="center"><?php echo ($key+1); ?></td>
                        <td width="10%" align="center"> 
                            <input id="YingshouRoomServiceAccountsCode_<?php echo ($key+1); ?>" name="accountsCode_<?php echo ($key+1); ?>" type="text" size="6" tabindex="1" onkeydown="BatchPaymentEditviewModule.getAccountsByCode(<?php echo ($key+1); ?>,event,this);"
                                AUTOCOMPLETE="off" style="font-size:16px;" />

                            <img id="YingshouRoomServicesearchIcon1" title="选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;" align="absmiddle" onclick="BatchPaymentEditviewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');" /><a
                                href="javascript:YingshouRoomServiceAccountsEditViewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
                        </td>
                        <td width="30%" align="center"> 
                            <input id="YingshouRoomServiceAccountsName_<?php echo ($key+1); ?>" name="accountsName_<?php echo ($key+1); ?>" type="text" size="30" readonly="readonly" style="font-size:16px;" />
                        </td>
                        <td width="15%" align="center"> 
                            <input id="YingshouRoomServiceAccountsMoney_<?php echo ($key+1); ?>" name="accountsMoney_<?php echo ($key+1); ?>" type="text" size="10" onkeydown="BatchPaymentEditviewModule.keydownSumAccountsMoney('<?php echo ($key+1); ?>',event,this);"
                                onblur="BatchPaymentEditviewModule.blurSumAccountsMoney('<?php echo ($key+1); ?>',event,this);" tabindex="1" value="" style="font-size:16px;" />
                        </td>

                        <td width="10%" align="center"><a href="#" onclick="BatchPaymentEditviewModule.clearAccounts(<?php echo ($key+1); ?>);">清空</a>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr style="line-height: 5px;">
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <input type="hidden" id="orderform_ordersn_<?php echo ($key+1); ?>" name="orderform_ordersn_<?php echo ($key+1); ?>" value="<?php echo ($order["ordersn"]); ?>" />
                <input type="hidden" id="orderformtotalmoney_<?php echo ($key+1); ?>" name="orderformtotalmoney_<?php echo ($key+1); ?>" value="<?php echo ($order["totalmoney"]); ?>" /><?php endforeach; endif; ?>
            <input type="hidden" id="orderform_date" value="<?php echo ($custdate); ?>" />
            <input type="hidden" id="orderform_ap" value="<?php echo ($custap); ?>" />
        </form><?php endif; ?>
</div>

<input id="PaymentEditviewAction" type="hidden" value="BatchPaymentEditView" />


<script>
    var BatchPaymentEditviewModule = {
        dialog: '#globel-dialog-div',
        checkSubmitFlg: false,

        //初始化
        init: function () {
            $('.moduleOperater').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            $("#YingshouRoomServiceAccountsCode_1").focus();
        },

        /* 通过代码取得支付 */
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

        //根据用户输入的客户代码，输出产品名称
        getAccounts: function (rowNum, code) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        $("#YingshouRoomServiceAccountsName_" + rowNum).val(mydata.name); //跳转到下一行
                        var jiezhangmoney = $('#orderformtotalmoney_' + rowNum).val();
                        jiezhangmoney = parseFloat(jiezhangmoney);
                        $("#YingshouRoomServiceAccountsMoney_" + rowNum).val(jiezhangmoney);
                        $("#YingshouRoomServiceAccountsMoney_" + rowNum).focus();
                    } else {
                        alert("输入的客户代码有错误，请重新输入！");
                        return;
                    }
                }
            })
        },
        /* 键盘回车计算产品金额 */
        keydownSumAccountsMoney: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            if (key == 13) {
                if ($("#YingshouRoomServiceAccountsName_" + rowNum).val() != '') { //如果不为空，才计算
                    this.sumAccountsMoney(rowNum);
                }
                $("#YingshouRoomServiceAccountsCode_" + rowNum).focus();
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


        //产品数量输入框失去焦点
        blurkeydownSumProductsMoney: function (rowNum, obj) {
            this.sumProductsMoney(rowNum);
        },

        /* 计算产品金额 */
        sumAccountsMoney: function (rowNum) {
            //获取代码，名称
            code = $('#YingshouRoomServiceAccountsCode_' + rowNum).val();
            name = $('#YingshouRoomServiceAccountsName_' + rowNum).val();
            //获取输入的金额              
            totalMoney = parseFloat($("#YingshouRoomServiceAccountsMoney_" + rowNum).val());
            //写入总的金额
            totalMoney = totalMoney.toFixed(2);
            //获取订单号，日期，午别
            ordersn = $('#orderform_ordersn_' + rowNum).val();
            date = $('#orderform_date').val();
            ap = $('#orderform_ap').val();
            data = {
                'record': ordersn,
                'custdate': date,
                'custap': ap,
                'name': name,
                'paymentjiezhangmoney': totalMoney,
                'accountsLength': 1,
                'accountsCode_1': code,
                'accountsName_1': name,
                'accountsMoney_1': totalMoney,
                'accountsNote_1': '',
            }
            //上传金额数据到订单
            $.ajax({
                url: "__URL__/update",
                type: 'post',
                data: data,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {

                }
            });
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

        }
    }

    $(function () {
        BatchPaymentEditviewModule.init();
    })
</script>