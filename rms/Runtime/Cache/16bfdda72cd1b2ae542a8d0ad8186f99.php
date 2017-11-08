<?php if (!defined('THINK_PATH')) exit();?><style>
    /*提示*/
    .detailviewLableSpan {
        font-size: 16px;
        margin-right: 10px;
    }

    .detailviewForm {
        background: white;
    }

    /*显示值*/
    .detailviewInputSpan {
        font-size: 16px;
        margin-left: 4px;
    }
    
</style>

<table id="detailviewOrderFormBaseTable" style="BORDER-COLLAPSE: collapse" borderColor="#A9A9A9" cellSpacing="0"
    width="100%" align="center" border="1">
    <thead>
        <td colspan="4" class="tabBlockViewHeader">
            已支付信息
        </td>
    </thead>
    <?php if(is_array($payment[0])): foreach($payment[0] as $k=>$vo): ?><tr>
            <td width="35%" align="right">
                <span class="detailviewLableSpan"><?php echo ($k); ?></span>
            </td>
            <td width="45%" align="left" style="background: #F5F5F5;">
                <span class="detailviewInputSpan"><?php echo ($vo); ?></span>
            </td>
        </tr><?php endforeach; endif; ?>
</table>
</br>
</br>
<table id="detailviewOrderFormBaseTable" style="BORDER-COLLAPSE: collapse" borderColor="#A9A9A9" cellSpacing="0" width="100%" align="center" border="1">
    <thead>
        <td colspan="4" class="tabBlockViewHeader">
            应交账信息
        </td>
    </thead>
    <?php if(is_array($payment[1])): foreach($payment[1] as $k=>$vo): ?><tr>
            <td width="35%" align="right">
                <span class="detailviewLableSpan"><?php echo ($k); ?></span>
            </td>
            <td width="45%" align="left" style="background: #F5F5F5;">
                <span class="detailviewInputSpan"><?php echo ($vo); ?></span>
            </td>
        </tr><?php endforeach; endif; ?>

</table>