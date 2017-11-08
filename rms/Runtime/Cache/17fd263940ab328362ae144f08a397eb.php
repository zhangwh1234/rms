<?php if (!defined('THINK_PATH')) exit();?><style>
    #accountsTable {
        background-color: transparent;
    }

    .accountsTableHeader {
        background-color: #95CACA;
        font-size: 12px;
    }

    .accountsTableHeaderLeftTd {
        border-style: solid none none solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-top: 2px;
        padding-bottom: 2px;
        font-size: 14px;
    }

    .accountsTableXiaojiLeftTd {
        border-style: solid none solid solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-top: 1px;
        padding-bottom: 1px;
        padding-left: 40px;
        height: 22px;
    }

    .accountsTableXiaojiRightTd {
        border-style: solid solid solid solid;
        border-width: 1px;
        border-color: #CCCCCC;
        padding-top: 1px;
        padding-bottom: 1px;
        padding-left: 20px;
    }

    #accountsTable td {
        height: 25px;
        border-style: solid none none solid;
        border-width: 1px;
        border-color: #CCCCCC;
    }

    #accountsTable span {
        font-size: 16px;
    }

</style>
<table id="accountsTable" width="100%" border="0" cellspacing="0" cellpadding="0" borderColor="white" border="1">
    <thead class="accountsTableHeader">
    <td width="5%" align="center" class="accountsTableHeaderLeftTd">序号</td>
    <td width="15%" align="center" class="accountsTableHeaderLeftTd">支付代码</td>
    <td width="30%" align="center" class="accountsTableHeaderLeftTd">支付名称</td>
    <td width="25%" align="center" class="accountsTableHeaderLeftTd">金额</td>
    <td width="25%" align="center" class="accountsTableHeaderLeftTd">备注</td>
    </thead>

    <?php if(is_array($worklunchaccounts)): foreach($worklunchaccounts as $key=>$vo): ?><tr style="background-color: #F5F5F5;">
            <td width="5%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
            <td width="10%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["code"]); ?></span>
            </td>
            <td width="30%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["name"]); ?></span>
            </td>
            <td width="25%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["money"]); ?></span>
            </td>
            <td width="25%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["note"]); ?></span>
            </td>
        </tr><?php endforeach; endif; ?>

</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="accountsTableXiaojiLeftTd" width="65%">

        </td>
        <td style="font-size: 16px;" class="accountsTableXiaojiRightTd">
        </td>

        <td class="accountsTableXiaojiRightTd"> 
            <span style="font-size:14px;">小计</span>
            <span style="font-size:16px;"><?php echo ($info["money"]); ?></span>
        </td>
    </tr>
</table>