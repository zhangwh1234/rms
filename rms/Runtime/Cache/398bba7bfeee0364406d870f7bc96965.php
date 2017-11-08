<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName"
               onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;新建操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 20px;"><a href="javascript:;" onclick="<?php echo ($moduleName); ?>CreateviewModule.insert();"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="<?php echo ($moduleName); ?>CreateviewModule.insert();">保存<span>^9</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:;"
                                          onclick="IndexIndexModule.updateOperateTab('__URL__/listview');"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">放弃新建,返回列表<span>^4</span></a></li>

        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"
                                                        onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div class="moduleoperator">
    <form id="YingshouIncomeMgrCreateviewForm" name="<?php echo ($moduleName); ?>CreateviewForm" method="post"
          style="border:1px solid white;margin-top: 0px;">
        <input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Createview"/>
        <table border="0" cellspacing="0" cellpadding="0" width="99%" align="center" bgcolor="">
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="3" width="100%" class="small">
                        <tr style="line-height: 15px;">
                            <td class="dvtTabCache" style="width:20px" nowrap>&nbsp;</td>
                            <td class="dvtSelectedCell" align="center" nowrap> 新建</td>
                            <td class="dvtTabCache" style="width:80%">&nbsp;</td>
                        <tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td valign="top" align="center">
                    <div id="basicTab" style="border: 1px solid #e0dddd; background: white;">
                        <table border="0" cellspacing="0" cellpadding="0" width="98%" class="small"
                               style="padding-top: 10px;">
                            <?php if(is_array($blocks)): $i = 0; $__LIST__ = $blocks;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        <?php echo (L("$key")); ?>
                                    </td>
                                </tr>

                                <!-- Here we should include the uitype handlings-->
                                <?php if(is_array($data)): $label = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subdata): $mod = ($label % 2 );++$label;?><tr style="border: 1px solid black;background: #F0F0F0;">
                                        <?php if(is_array($subdata)): $mainlabel = 0; $__LIST__ = $subdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$maindata): $mod = ($mainlabel % 2 );++$mainlabel;?><style type="text/css">
    .dvtCellLabel, .cellLabel {
        background: #F7F7F7 url(./__PUBLIC__/Images/testsidebar.jpg) repeat-y scroll right center;
        border-bottom: 1px solid #DEDEDE;
        border-left: 1px solid #DEDEDE;
        border-right: 1px solid #DEDEDE;
        color: #545454;
        padding-left: 10px;
        padding-right: 10px;
        white-space: nowrap;
        font-size: 14px;
    }

    .dvtCellInfo, .cellInfo {
        background: #FFFFFF;
        padding-left: 10px;
        padding-right: 10px;
        border-bottom: 1px solid #dedede;
        border-right: 1px solid #dedede;
        border-left: 1px solid #dedede;
    }

    .detailedViewTextBox {
        font-size: 16px;
    }

    .detailedViewTextBoxOn {
        font-size: 16px;
    }

    lable {
        font-size: 16px;
    }

    /*对RBAC节点状态的设置*/
    .nodestatus {
        float: left;
        font-size: 16px;
        line-height: 28px;
    }
</style>

<script language="JavaScript">
    //指定当前组模块URL地址
    var URL = '__URL__';
    var APP = '__APP__';
    var PUBLIC = '__PUBLIC__';
    var MODULE_NAME = '<?php echo ($module_name); ?>';
</script>

<?php $label = L("$maindata[name]"); ?>        
<?php $uitype = $maindata[uitype]; ?>                
<?php $name = $maindata[name]; ?>                      
<?php $value = $maindata[value]; ?>                    
<?php $length = $maindata[length]; ?>                  
<?php if($maindata[readonly] == 0): $readonly = 'readonly'; ?>
    
    <?php else: ?>
    <?php $readonly = ''; endif; ?>


<?php if(($uitype == 0)): ?><td width=20% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?></td>
    <td width=30% align=left class="dvtCellInfo"><input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" <?php echo ($readonly); ?>
                                                        value="<?php echo ($fldvalue); ?>" class=detailedViewTextBox
                                                        onFocus="this.className='detailedViewTextBoxOn'"
                                                        onBlur="this.className='detailedViewTextBox'"
                                                        AUTOCOMPLETE="off">
    </td>

    
    <?php elseif($uitype == 1): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" AUTOCOMPLETE="off">
    </td>

    
    <?php elseif($uitype == 2): ?>
    <td width=15% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" class="easyui-numberbox" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?>
               size="<?php echo ($length); ?>" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" AUTOCOMPLETE="off"></td>

    
    <?php elseif($uitype == 3): ?>
    <td width=15% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" AUTOCOMPLETE="off"/>
    </td>

    
    <?php elseif($uitype == 4): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" onkeyup="" AUTOCOMPLETE="off"/>
    </td>

    
    <?php elseif($uitype == 5): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <input name="<?php echo ($name); ?>" tabindex="<?php echo ($vt_tab); ?>" id="<?php echo ($name); ?>" class="easyui-datebox" type="text"
               style="border:1px solid #bababa;" size="20" maxlength="10" value="<?php echo ($value); ?>">
    </td>

    
    <?php elseif($uitype == 6): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <input name="<?php echo ($fldname); ?>" id="jscal_field_<?php echo ($fldname); ?>" type="text" style="border:1px solid #bababa;" size="11"
               maxlength="10" value="<?php echo ($time_val); ?>">
        <img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_<?php echo ($fldname); ?>"> <br><font size=1><em
            old="(hh:ii:ss)">(<?php echo ($dateStr); ?>)</em></font>
    </td>

    
    <?php elseif($uitype == 7): ?>
    <td width=15% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo"><input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>"
                                                        <?php echo ($readonly); ?> size="<?php echo ($length); ?>" class=detailedViewTextBox
                                                        onFocus="this.className='detailedViewTextBoxOn'"
                                                        onBlur="this.className='detailedViewTextBox'"
                                                        AUTOCOMPLETE="off">
    </td>

    
    <?php elseif($uitype == 9): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font>
        <?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <?php $select = $company[select]; ?>
        
        <select name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" class="detailedViewTextBox" style="width:120px;">
            <assing name="ccc" value="$currentName"/>
            <?php if($value): ?><option value="<?php echo ($value); ?>"><?php echo ($value); ?></option>
                <?php else: ?>
                <option value="  ">&nbsp;</option><?php endif; ?>
            <?php if(is_array($$name)): foreach($$name as $key=>$pick): ?><option value="<?php echo ($pick["name"]); ?>"><?php echo ($pick["name"]); ?></option><?php endforeach; endif; ?>
        </select>
    </td>

    
    <?php elseif($uitype == 10): ?>
    <td width=10% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=40% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               tabindex="<?php echo ($vt_tab); ?>" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'"/>
    </td>

    
    <?php elseif($uitype == 11): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font> <?php echo ($label); ?>
    </td>
    <td width="35%" align="left" class="dvtCellInfo">
        <textarea name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" <?php echo ($readonly); ?> data-options='required:true' class="detailedViewTextBox"
                  onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"
                  rows="2" style="width:100%;"><?php echo ($value); ?></textarea>
    </td>

    
    <?php elseif($uitype == 12): ?>
    <td width=15% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo"><input type="password" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>"
                                                        <?php echo ($readonly); ?> size="<?php echo ($length); ?>" class=detailedViewTextBox
                                                        onFocus="this.className='detailedViewTextBoxOn'"
                                                        onBlur="this.className='detailedViewTextBox'"></td>

    
    <?php elseif($uitype == 17): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
        &nbsp;&nbsp;http://
        <input style="width:74%;" class='detailedViewTextBox' type="text" tabindex="<?php echo ($vt_tab); ?>" name="<?php echo ($fldname); ?>"
               style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" onkeyup="validateUrl('<?php echo ($fldname); ?>');" value="<?php echo ($fldvalue); ?>"/>
    </td>

    
    <?php elseif($uitype == 26): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
        <select name="<?php echo ($fldname); ?>" tabindex="<?php echo ($vt_tab); ?>" class="small">
            <?php if(is_array($fldvaluee)): foreach($fldvaluee as $k=>$v): endforeach; endif; ?>
            <option value="<?php echo ($k); ?>"><?php echo ($v); ?></option>

        </select>
    </td>

    
    
    <?php elseif($uitype == 21): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" value="<?php echo ($value); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" size="<?php echo ($length); ?>"
               onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"
               onkeyup="$(this).val($(this).val().replace(/[^0-9]/g,''));" AUTOCOMPLETE="off"/>
    </td>

    
    <?php elseif($uitype == 22): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($value); ?>" class="detailedViewTextBox mousetrap"
               data-options="
	required:true" size="<?php echo ($length); ?>" AUTOCOMPLETE="off"/>

        <div id="searchResultPanel" style="border:0px solid #C0C0C0;width:50px;height:auto; display:none;"></div>
    </td>

    
    <?php elseif($uitype == 23): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>_1" id="<?php echo ($name); ?>_1" value="<?php echo ($custtime_1); ?>" size="2" maxlength="2"
               tabindex="<?php echo ($vt_tab); ?>" class="detailedViewTextBox" AUTOCOMPLETE="off"/>
        <input type="text" name="<?php echo ($name); ?>_2" id="<?php echo ($name); ?>_2" value="<?php echo ($custtime_2); ?>" size="2" maxlength="2"
               tabindex="<?php echo ($vt_tab); ?>" class="detailedViewTextBox" AUTOCOMPLETE="off"/>
        <em>*</em>
    </td>

    
    <?php elseif($uitype == 30): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($shippingname); ?>" size="<?php echo ($length); ?>" readonly
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'"/>
    </td>
    
    <?php elseif($uitype == 30): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" value="<?php echo ($shippingmoney); ?>11" size="<?php echo ($length); ?>" readonly
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'" onKeydown="alert('ik');"/>
    </td>

    
    <?php elseif($uitype == 50): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <table id="telAddressTable" width="90%" border=0 style="border: 1px solid #e0dddd; margin-top: 2px;"
               class="small">
            <tr class="detailedViewHeader">
                <td width="10%" align="center">序号</td>
                <td width="70%" align="center">地址</td>
                <td width="10%" align="center">所属分公司</td>
                <td width="10%" align="center">操作</td>
            </tr>
            <?php if(is_array($teladdress)): foreach($teladdress as $key=>$vo): ?><tr style="height:25px;border: 1px solid black;background: #F0F0F0;" class="CaseRow">
                    <td width="10%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
                    <td width="70%" align="left" class="dvtCellLabel"><input id="telAddress_<?php echo ($key+1); ?>"
                                                                             name="telAddress_<?php echo ($key+1); ?>" type="text"
                                                                             size="80" value="<?php echo ($vo["address"]); ?>"/></td>
                    <td width="1%" align="center" class="dvtCellLabel"><input id="telCompany_<?php echo ($key+1); ?>"
                                                                              name="telCompany_<?php echo ($key+1); ?>" type="text"
                                                                              size="10" value="<?php echo ($vo["company"]); ?>"/></td>
                    <td width="10%" align="center" class="dvtCellLabel"><a href="javascript:void(0);"
                                                                           onclick="clearTelAddress('<?php echo ($key+1); ?>');">清空地址</a>
                    </td>
                </tr><?php endforeach; endif; ?>
            <?php if(empty($teladdress)): $__FOR_START_545301626__=1;$__FOR_END_545301626__=4;for($key=$__FOR_START_545301626__;$key < $__FOR_END_545301626__;$key+=1){ ?><tr style="height:25px;border: 1px solid black;background: #F0F0F0;" class="CaseRow">
                        <td width="10%" align="center" class="dvtCellLabel"><?php echo ($key); ?></td>
                        <td width="70%" align="left" class="dvtCellLabel"><input id="telAddress_<?php echo ($key); ?>"
                                                                                 name="telAddress_<?php echo ($key); ?>" type="text"
                                                                                 size="80" value="<?php echo ($vo["address"]); ?>"/></td>
                        <td width="70%" align="center" class="dvtCellLabel"><input id="telCompany_<?php echo ($key); ?>"
                                                                                   name="telCompany_<?php echo ($key); ?>" type="text"
                                                                                   size="10" value="<?php echo ($vo["company"]); ?>"/>
                        </td>
                        <td width="10%" align="center" class="dvtCellLabel"><a href="#"
                                                                               onclick="clearTelAddress('<?php echo ($key); ?>');">清空地址</a>
                        </td>
                    </tr><?php } endif; ?>
        </table>
        <table width="90%">
            <tr>
                <td>
                    <input type="button" value="添加地址行" class="crmbutton small save"
                           style="width:100px;margin-right: 10px;" onclick="addTelAddress();">
                    <input type="button" value="删除最后一行" class="crmbutton small save" onclick="delTelAddress();">
                    <input id="addressLength" name="addressLength" type="hidden" value="<?php echo ($key+1); ?>"/>
                </td>
            </tr>
        </table>
    </td>

    
    <?php elseif($uitype == 51): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
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
    <?php if(empty($orderproducts)): $__FOR_START_1733148172__=0;$__FOR_END_1733148172__=3;for($key=$__FOR_START_1733148172__;$key < $__FOR_END_1733148172__;$key+=1){ ?><tr>
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

    
    <?php elseif($uitype == 52): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <table style="border: 1px solid #BEBEBE;" width="95%">
            <?php if(is_array($orderaction)): foreach($orderaction as $key=>$vo): ?><tr>
                    <td width="20%"><?php echo ($vo["logtime"]); ?></td>
                    <td><?php echo ($vo["action"]); ?></td>
                </tr><?php endforeach; endif; ?>
        </table>
    </td>

    
    <?php elseif($uitype == 53): ?>
    <td width=10% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font>是否开启
    </td>
    <td width=40% align="left" class="dvtCellInfo" id="nodestatus">
        <input type="radio" name="<?php echo ($name); ?>" value="1" checked='checked' class="nodestatus"/><label class="nodestatus">
        &nbsp;开启&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input type="radio" name="<?php echo ($name); ?>" value="0" class="nodestatus"/><label class="nodestatus">
        &nbsp;关闭&nbsp;</label>
    </td>

    
    <?php elseif($uitype == 54): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font>
        <?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <?php $select = $company[select]; ?>
        
        <select name="role_id" id="<?php echo ($name); ?>" class="detailedViewTextBox" style="width:200px">
            <?php if($roleCurrent): ?><option value="<?php echo ($roleCurrent["id"]); ?>"><?php echo ($roleCurrent["name"]); ?>(<?php echo ($roleCurrent["remark"]); ?>)</option>
                <?php else: ?>
                <option value="  ">请选择用户的角色</option><?php endif; ?>
            <?php if(is_array($role)): foreach($role as $key=>$vo): ?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo["name"]); ?>(<?php echo ($vo["remark"]); ?>)</option><?php endforeach; endif; ?>
        </select>
    </td>

    
    <?php elseif($uitype == 55): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="35%" align="left" class="dvtCellInfo1">
        <input name="<?php echo ($name); ?>" tabindex="<?php echo ($vt_tab); ?>" id="todaymenudate" class="easyui-datebox" type="text"
               style="border:1px solid black;font-size:16px;" size="11" maxlength="10" value="<?php echo ($todaymenudate); ?>">
    </td>

    
    <?php elseif($uitype == 56): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <textarea name="<?php echo ($name); ?>" class="detailedViewTextBox" style="border:1px solid blcak;width: 98%;" rows="20"><?php echo ($value); ?></textarea>
    </td>

    
    <?php elseif($uitype == 57): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font>
        <?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <select id="invoiceheaderselect" class="detailedViewTextBox" style="width:200px">
            <option value="  ">&nbsp;</option>
        </select>
        <input name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" type="text" value="" size="35" style="margin-left: 10px;"/>
    </td>
    <script type="text/javascript">
        //select的选择发票
        $('#invoiceheaderselect').click(function () {
            var header = $("option:selected", this);
            $('#invoiceheader').val(header.text());
        })
    </script>
    
    <?php elseif($uitype == 58): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
    </td>

    
    <?php elseif($uitype == 60): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <style>
    #orderPrintHandleTable {
        background-color: transparent;
    }

    #orderPrintHandleTable td {
        height: 25px;
    }

    #productsTable span {
        font-size: 16px;
    }

    #orderPrintHandleTable input {
        font-size: 16px;
    }

</style>
<table id="orderPrintHandleTable" style="BORDER-COLLAPSE: collapse" borderColor="#CCCCCC" cellSpacing="0" width="100%"
       align="center" border="1">
    <tr class="orderPrintHandleTableHead">
        <td width="5%" align="center">序号</td>
        <td width="10%" align="center">打印编号</td>
        <td width="75%" align="center">打印订单内容</td>
        <td width="10%" align="center">操作</td>
    </tr>
    <?php if(empty($orderPrintHandle)): $__FOR_START_1370225775__=0;$__FOR_END_1370225775__=30;for($key=$__FOR_START_1370225775__;$key < $__FOR_END_1370225775__;$key+=1){ ?><tr>
                <td width="5%" align="center"><?php echo ($key+1); ?></td>
                <td width="10%" align="center"><input id="orderPrintHandleid_<?php echo ($key+1); ?>" name="orderPrintHandleid_<?php echo ($key+1); ?>"
                                                      type="text" size="10" value="" autocomplete="off"
                                                      onkeyup="OrderPrintHandleViewModule.getOrderTxtByid(<?php echo ($key+1); ?>,event,this);"/></td>
                <td width="75%" align="center"><input id="orderPrintAddressOrdertxt_<?php echo ($key+1); ?>"
                                                      name="orderPrintAddressOrdertxt_<?php echo ($key+1); ?>" type="text"  style="width:98%;"
                                                      value="" autocomplete="off" readonly="readonly" />
                <input type="hidden" id="orderPrintOrdersn_<?php echo ($key+1); ?>" name="orderPrintOrdersn_<?php echo ($key+1); ?>" value="" /></td>
                <td width="10%" align="center"><a href="#" onclick="OrderPrintHandleViewModule.clearAddressOrdertxt(<?php echo ($key+1); ?>);">清空内容</a></td>
            </tr><?php } endif; ?>
</table>

<script type="text/javascript">
    var OrderPrintHandleViewModule = {

        //根据输入的订单号，显示订单内容
        getOrderTxtByid: function (rowNum, evt, obj) {
            evt = evt ? evt : ((window.event) ? window.event : null);
            var key = evt.keyCode ? evt.keyCode : evt.which;
            var orderPrintHandleid = $(obj).val();
            if (key == 13) {
                $.ajax({
                    url: "__URL__/getOrderTxtByid/printNumber/" + orderPrintHandleid,
                    async: true,
                    beforeSend: function () {

                    },
                    complete: function () {
                    },
                    success: function (mydata) {
                        if (mydata.error == 'error') {
                            $.messager.show({
                                title: '提示',
                                msg: '输入号码有误!',
                                height: 70,
                                timeout: 5000,
                                showType: 'slide',
                                style: {
                                    left: 0, right: '', top: '',
                                    bottom: -document.body.scrollTop - document.documentElement.scrollTop
                                }
                            });
                            $('#orderPrintAddressOrdertxt_' + rowNum).val('');
                            $('#orderPrintOrdersn_'+rowNum).val('');  //订单号
                            $('#orderPrintAddressOrdertxt_' + rowNum).css("color","black");
                            return false;
                        }
                        if (mydata.success == 'success') {
                            $('#orderPrintAddressOrdertxt_' + rowNum).css("color","black");
                            $('#orderPrintAddressOrdertxt_' + rowNum).val(mydata.addressOrdertxt);
                            $('#orderPrintOrdersn_'+rowNum).val(mydata.ordersn);
                            rowNum = rowNum + 1;
                            $('#orderPrintHandleid_' + rowNum).focus();
                        }
                        if (mydata.success == 'repeat'){
                            $('#orderPrintAddressOrdertxt_' + rowNum).val(mydata.addressOrdertxt);
                            $('#orderPrintOrdersn_'+rowNum).val(mydata.ordersn);
                            $('#orderPrintAddressOrdertxt_' + rowNum).css("color","red");
                            rowNum = rowNum + 1;
                            $('#orderPrintHandleid_' + rowNum).focus();
                        }
                    }
                })
            }
        },

        sendnamePickList: function (url) {
            $('#globel-dialog-div').dialog({
                title: '选择送餐员',
                iconCls: 'icons-application-application_add',
                width: 400,
                height: 540,
                cache: false,
                href: url,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '关闭',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $('#globel-dialog-div').dialog('close');
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

        //删除内容
        clearAddressOrdertxt : function(rowNum){
            $('#orderPrintHandleid_' + rowNum).val('');
            $('#orderPrintAddressOrdertxt_' + rowNum).val('');
            $('#orderPrintOrdersn_'+rowNum).val('');
            $('#orderPrintHandleid_' + rowNum).focus();
        }




    }


</script>
    </td>
    
    <?php elseif($uitype == 61): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <div align="left">
    <label>增加预订日期：</label><input type="text" class="easyui-datebox" id="bookdateadd" style="float: left;"></br>
    <div id="bookdatediv" style="float: left;width:auto;border: 0px solid red;" >
        <?php if(is_array($bookdate)): foreach($bookdate as $key=>$vo): ?><div style='border: 1px solid #A9A9A9;float: left;margin:4px;'>
                <input  type="text" id="bookorderdate<?php echo ($key); ?>" name="bookorderdate[]" value="<?php echo ($vo["bookdate"]); ?>" size="10" readonly  style="font-size: 14px;"/>
                <input  type='button' value='删除' onclick="clearbook(<?php echo ($key); ?>,this);" >
            </div><?php endforeach; endif; ?> 
    </div>
</div>
<script type="text/javascript">
    var count = 1;
    $(function(){
        //$('#bookdatediv').width(document.body.clientWidth);
        $('#bookdateadd').datebox({
            onSelect: function(date){
                var date_now = new Date();  //当前日期
                var date_str_now = date_now.Format('yyyy-MM-dd'); // date_now.getFullYear()+"-"+(date_now.getMonth()+1)+"-"+date_now.getDate();
                //选择的日期
                //selectDate =    date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                selectDate = date.Format('yyyy-MM-dd');
                //计算日期差
                var dateOfDiff = daysBetween(selectDate,date_str_now);
                if(dateOfDiff < 0){
                    //不能选择小于今天的日期,小于今天,就不算是预订了
                    alert('预订日期'+selectDate+',选择有误,请重新选择!');
                    return false;
                }

                var st = "<div style='border: 1px solid #A9A9A9;float: left;margin:4px;'><input type='text' id='bookorderdate"+count+"' value='"+selectDate+"' name='bookorderdate[]' size='10' readonly  style='font-size:14px;' /><input  type='button' value='删除' onclick='clearbook("+count+",this);'></div>";
                $('#bookdatediv').append(st);
                count = count + 1;
            }
        });    
    })

    //+---------------------------------------------------
    //| 求两个时间的天数差 日期格式为 YYYY-MM-dd
    //+---------------------------------------------------
    function daysBetween(DateOne,DateTwo)
    {
        var OneMonth = DateOne.substring(5,DateOne.lastIndexOf ('-'));
        var OneDay = DateOne.substring(DateOne.length,DateOne.lastIndexOf ('-')+1);
        var OneYear = DateOne.substring(0,DateOne.indexOf ('-'));

        var TwoMonth = DateTwo.substring(5,DateTwo.lastIndexOf ('-'));
        var TwoDay = DateTwo.substring(DateTwo.length,DateTwo.lastIndexOf ('-')+1);
        var TwoYear = DateTwo.substring(0,DateTwo.indexOf ('-'));

        var cha=((Date.parse(OneMonth+'/'+OneDay+'/'+OneYear)- Date.parse(TwoMonth+'/'+TwoDay+'/'+TwoYear))/86400000);
        return cha;
    }

    function add(){
        //$('#bookdatediv').append("<input type='text' class='easyui-datebox'>");
        $('#dd').clone().prependTo('#bookdatediv');
    }

    function clearbook(i,obj){
        //$(obj).val('');
        bookorderdate = "#bookorderdate"+i;
        $(bookorderdate).remove();
        $(obj).hide();

    }

    // 对Date的扩展，将 Date 转化为指定格式的String
    // 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
    // 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
    // 例子：
    // (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
    // (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
    Date.prototype.Format = function(fmt)
    { //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    }

</script>
    </td>

    
    <?php elseif($uitype == 62): ?>
    <td width="100%" class="dvtCellLabel" align="left" colspan="2">
        <label style="float:left;font-size: 14px;line-height: 30px;margin-left: 100px;">按F10快捷键</label><input
            id="todaymenu" type="button" value="今日菜单" style=""/>
    </td>

    
    <?php elseif($uitype == 63): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="<?php echo ($name); ?>" value="<?php echo ($value); ?>" <?php echo ($readonly); ?> size="<?php echo ($length); ?>" class="detailedViewTextBox"
               AUTOCOMPLETE="off">
        <img id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1" title="产品选择"
             src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;"
             align="absmiddle"
             onclick="OrderPrintHandleViewModule.sendnamePickList('__URL__/popupSendnameview');"/><a
            href="javascript:OrderPrintHandleViewModule.sendnamePickList('__URL__/popupSendnameview');">选择</a>

    </td>

    
    <?php elseif($uitype == 64): ?>
    <td width="15%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font>
        <?php echo ($label); ?>
    </td>
    <td width="35%" align=left class="dvtCellInfo">
        <?php $select = $company[select]; ?>
        
        <select name="<?php echo ($name); ?>" id="<?php echo ($name); ?>" class="detailedViewTextBox" style="width:120px;">
            <assing name="ccc" value="$currentName"/>
            <?php if($name): ?><option value="<?php echo ($value); ?>"><?php echo ($value); ?></option>
                <?php else: ?>
                <option value="  ">&nbsp;</option><?php endif; ?>
            <?php if(is_array($$name)): foreach($$name as $key=>$pick): ?><option value="<?php echo ($pick["name"]); ?>"><?php echo ($pick["name"]); ?></option><?php endforeach; endif; ?>
        </select>
    </td>

    
    <?php elseif($uitype == 65): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="worklunchcode" id="worklunchcode"  <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'"
               onkeydown="YingshouWorklunchCreateviewModule.getWorklunchByCode(event,this);"
               AUTOCOMPLETE="off">
        <img id="YingshouRoomServicesearchIcon1" title="选择" src="./__PUBLIC__/Images/products.gif"
             style="cursor: pointer;"
             align="absmiddle"
             onclick="YingshouWorklunchCreateviewModule.worklunchPickList('__URL__/popupWorklunchview/module/Accounts/row/<?php echo ($key+1); ?>');"/><a
            href="javascript:YingshouWorklunchCreateviewModule.worklunchPickList('__URL__/popupWorklunchview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
    </td>

    
    <?php elseif($uitype == 66): ?>
    <td width=15% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>*</font><?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <input type="text" name="paymentcode" id="paymentcode"  <?php echo ($readonly); ?> size="<?php echo ($length); ?>"
               class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'"
               onBlur="this.className='detailedViewTextBox'"
               onkeydown="YingshouIncomeMgrListviewModule.getAccountsByCode(event,this,'<?php echo ($module); ?>');"
               AUTOCOMPLETE="off">
        <img id="YingshouRoomServicesearchIcon1" title="选择" src="./__PUBLIC__/Images/products.gif"
             style="cursor: pointer;"
             align="absmiddle"
             onclick="YingshouIncomeMgrListviewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>');"/><a
            href="javascript:YingshouIncomeMgrListviewModule.accountsPickList('__URL__/popupAccountsview/module/Accounts/row/<?php echo ($key+1); ?>')">选择</a>
    </td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                <tr style="line-height: 5px;">
                                    <td>&nbsp;</td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                            <table border="0" cellspacing="0" cellpadding="0" width="98%"
                                   style="margin-top:5px; border: 1px solid #e0dddd; ">
                                <tr>
                                    <td colspan="4" class="tabBlockViewHeader">
                                        客户支付
                                    </td>
                                </tr>

                                <tr style="background: #FFFFFF;">
                                    <td colspan="4">
                                        <div id="accountbilldetailview" />
                                    </td>
                                </tr>
                            </table>

                            <tr>
                                <td colspan="4" align="center">
                                    <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-other-tick'"
                                       onclick="<?php echo ($moduleName); ?>CreateviewModule.insert();"
                                       style="width:100px;margin-right:40px;">确认</a>
                                    <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-arrow-cross'"
                                       onclick="IndexIndexModule.updateOperateTab('__URL__/listview');"
                                       style="width:100px;">放弃</a>
                                </td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>


<script>
    var YingshouIncomeMgrCreateviewModule = {

        //初始化
        init: function () {
            $('.moduleoperator').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //保存记录
        insert: function () {
            $('#YingshouIncomeMgrCreateviewForm').form('submit', {
                url: '__URL__/insert',
                onSubmit: function () {
                    //进行表单验证
                    if($('#code').val() == ''){
                        alert('产品编码不能为空!');
                        return false;
                    }
                    if($('#name').val() == ''){
                        alert('产品名称不能为空!');
                        return false;
                    }
                    var isValid = $(this).form('validate');
                    if (!isValid) {
                        alert('数据不能为空！或者输入错误，请检查！');
                        return false;
                    }
                },
                success: function (res) {
                    var data = eval('(' + res + ')');
                    if (!data.status) {
                        $.app.method.tip('提示信息', data.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', data.info, 'info');
                        IndexIndexModule.updateOperateTab(data.url);
                    }
                }
            });
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+9快捷键,保存公告
            Mousetrap.bind(['ctrl+9', 'ctrl+f9', 'f9'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告' && ($('#NoticeAction').val() == 'Createview')) {
                    NoticeCreateviewModule.insert();
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告' && ($('#NoticeAction').val() == 'Createview')) {
                    IndexIndexModule.updateOperateTab('__URL__/listview');
                };
            });
        }
    }

    $(function () {

        YingshouIncomeMgrCreateviewModule.init();
        $('#YingshouIncomeMgrCreateviewForm input[name=incomemgrcode]').focus();
    })
</script>