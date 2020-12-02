<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="javascript.void(0);">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;查看操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>');" ><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>');">返回列表<span>^4</span></a></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
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
    <form id="OrderFormCreateviewForm" method="post">
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
                               cellSpacing="0" width="100%"
                               align="center" border="1">
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
                                    <span class="detailviewInputSpan"><?php echo ($info["sendname"]); ?></span>
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right">
                                    <span class="detailviewLableSpan">应收金额:</span>
                                </td>
                                <td width="50%" align="left" style="background: #F5F5F5;">
                                    <span class="detailviewInputSpan"><?php echo ($info["shouldmoney"]); ?></span>
                                    <span style="margin-left:10px; ">&nbsp;</span>
                                    <span class="detailviewLableSpan">已收金额:</span>
                                    <span class="detailviewInputSpan"><?php echo ($info["paidmoney"]); ?></span>
                                    <?php if($info["invoice_open"] == '已开'): ?><span class="detailviewInputSpan" style="color:red;">发票:<?php echo ($info["invoice_open"]); ?></span><?php endif; ?>
                                </td>
                                <td width="15%" align="right">
                                    <span class="detailviewLableSpan">订单总额:</span>
                                </td>
                                <td width="20%" align="left" style="background: #F5F5F5;">
                                    <span class="detailviewInputSpan"><?php echo ($info["totalmoney"]); ?></span>                                  
                                </td>
                               
                            </tr>
                        </table>

                        <table border="0" cellspacing="0" cellpadding="0" width="100%"
                               style="margin-top:5px; border: 1px solid #e0dddd; ">
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
            <span style="font-size:16px;"><?php echo ($orderproductsmoney); ?></span>
        </td>
    </tr>
</table>

                                </td>
                            </tr>
                        </table>

                        <?php if(!empty($info["invoiceheader"])): ?><table border="0" cellspacing="0" cellpadding="0" width="100%"
                                   style="margin-top:5px; border: 1px solid #e0dddd; ">
                                <tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        发票基本信息
                                    </td>
                                </tr>
                                <tr style="background: #FFFFFF;">
                                    <td colspan="4">
                                        <style>
    #DetailOrderFormInvoice td{
        height: 25px;
        border-style:solid none none solid;
        border-width:1px;
        border-color: #CCCCCC;
        font-size:14px;
    }

    #DetailOrderFormInvoice span{
        margin-left:4px;
        font-size:14px;
    }

    a:hover.copyinvoiceheaderbtn {
        background: white;
        color:black;
        cursor:pointer;
    }
    a:hover.copyinvoicebodybtn {
        background: white;
        color:black;
        cursor:pointer;
    }
    a:hover.copyinvoicensrsbhbtn {
        background: white;
        color:black;
        cursor:pointer;
    }
    a:hover.copyinvoicedzdhbtn {
        background: white;
        color:black;
        cursor:pointer;
    }
    a:hover.copyinvoiceyhzhbtn {
        background: white;
        color:black;
        cursor:pointer;
    }
</style>
<table id="DetailOrderFormInvoice" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="10%" align="right" >
            <span style="margin-right: 5px;">发票抬头:</span></td>
        <td width="45%"  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["invoiceheader"]); ?></span>
            <a style="float:right;margin-right: 10px;" class="copyinvoiceheaderbtn" data-clipboard-text="<?php echo ($info["invoiceheader"]); ?>" >复制</a>
        </td>
        <td width="10%" align="right">
            <span style="margin-right:5px;">发票内容:</span></td>
        <td  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["invoicebody"]); ?></span>
            <a style="float:right;margin-right: 10px;" class="copyinvoicebodybtn" data-clipboard-text="<?php echo ($info["invoicebody"]); ?>" >复制</a>
        </td>
    </tr>
    <tr>
        <td width="10%" align="right">
            <span style="margin-right:5px;">发票类型:</span></td>
        <td  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["invoicetype"]); ?></span>
        </td>
        <td width="10%" align="right">
            <span style="margin-right:5px;">纳税人识别号:</span></td>
        <td  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["gmf_nsrsbh"]); ?></span>
            <a style="float:right;margin-right: 10px;" class="copyinvoicensrsbhbtn" data-clipboard-text="<?php echo ($info["gmf_nsrsbh"]); ?>" >复制</a>
        </td>
    </tr>
    <tr>
        <td width="10%" align="right">
            <span style="margin-right:5px;">购买方电话地址:</span></td>
        <td  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["gmf_dzdh"]); ?></span>
            <a style="float:right;margin-right: 10px;" class="copyinvoicedzdhbtn" data-clipboard-text="<?php echo ($info["gmf_dzdh"]); ?>" >复制</a>
        </td>
        <td width="10%" align="right">
            <span style="margin-right:5px;">购买方银行账号:</span></td>
        <td  align="left" style="background: #F5F5F5;">
            <span><?php echo ($info["gmf_yhzh"]); ?></span>
            <a style="float:right;margin-right: 10px;" class="copyinvoiceyhzhbtn" data-clipboard-text="<?php echo ($info["gmf_yhzh"]); ?>" >复制</a>
        </td>
    </tr>
</table>

<script>
    $(document).ready(function(){

        var clipboard_invoiceheader = new Clipboard('.copyinvoiceheaderbtn');
        clipboard_invoiceheader.on('success', function(e) {
            $.messager.show({
                title: '消息提示',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });
        clipboard_invoiceheader.on('error', function(e) {
            $.messager.show({
                title: '复制失败',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });
        var clipboard_invoicebody = new Clipboard('.copyinvoicebodybtn');
        clipboard_invoicebody.on('success', function(e) {
            $.messager.show({
                title: '消息提示',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });


        var clipboard_invoicensrsbh = new Clipboard('.copyinvoicensrsbhbtn');
        clipboard_invoicensrsbh.on('success', function(e) {
            $.messager.show({
                title: '消息提示',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });
        clipboard_invoicensrsbh.on('error', function(e) {
            $.messager.show({
                title: '复制失败',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });
        var clipboard_invoicedzdh = new Clipboard('.copyinvoicedzdhbtn');
        clipboard_invoicedzdh.on('success', function(e) {
            $.messager.show({
                title: '消息提示',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });
        var clipboard_invoiceyhzh = new Clipboard('.copyinvoiceyhzhbtn');
        clipboard_invoiceyhzh.on('success', function(e) {
            $.messager.show({
                title: '消息提示',
                msg: e.text,
                showType: 'show',
                timeout: 1000
            });
        });

    })
</script>

                                    </td>
                                </tr>
                            </table><?php endif; ?>

                        <?php if(!empty($orderactivity)): ?><table border="0" cellspacing="0" cellpadding="0" width="100%"
                                   style="margin:1px; border: 1px solid #e0dddd; ">
                                <tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        营销活动信息
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
                            </table><?php endif; ?>

                        <?php if(!empty($orderaccountpayment)): ?><table border="0" cellspacing="0" cellpadding="0" width="100%"
                                   style="margin-top:2px; border: 1px solid #e0dddd; ">
                                <tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        客户支付
                                    </td>
                                </tr>
                                <tr style="background: #FFFFFF;">
                                    <td colspan="4">
                                        
                                    </td>
                                </tr>
                            </table><?php endif; ?>

                        <table border="0" cellspacing="0" cellpadding="0" width="100%"
                               style="margin-top:2px; border: 1px solid #e0dddd; ">
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
                                <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-arrow-cross'"
                                   onclick="IndexIndexModule.updateOperateTab('__URL__/<?php echo ($returnAction); ?>');"
                                   style="width:100px;">返回列表</a>
                            </td>
                        </tr>

                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<input id="OrderDistributionAction" type="hidden" value="Detailview"/>
<script>
    var OrderDistributionDetailviewModule = {
        //初始化
        init:function(){
            $('.moduleOperatert').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },


        //新建的快捷操作
        quickKeyboardAction: function () {

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                //if (tabOptions.title == '订餐单' ) {
                IndexIndexModule.updateOperateTab("__URL__/<?php echo ($returnAction); ?>/pagetype/<?php echo ($pagetype); ?>/rowIndex/<?php echo ($rowIndex); ?>");
                //};
            });
        }
    }

    $(function(){
        OrderDistributionDetailviewModule.init();
    })
</script>