<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;查看操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/newBtn.gif" alt="" title="" border="0"></a></li>
        <li><a id="create<?php echo ($moduelName); ?>" href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">新建<span>^1</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');" ><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">返回列表<span>^4</span></a></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <div style="clear:both;"></div>
    </ul>
</div>


<div class="moduleoperator" style="border: 1px solid white;">
    <input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Detailview" />
    <input type="hidden" id="<?php echo ($moduleName); ?>record" value="<?php echo ($record); ?>">
    <input type="hidden" id="<?php echo ($moduleName); ?>returnAction" value="<?php echo ($returnAction); ?>">
    <table border="0" cellspacing="0" cellpadding="0" width="99%" align="0" bgcolor="">
        <tr>
            <td>
                <table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
                    <tr>
                        <td class="dvtTabCache" style="width:20px" nowrap>&nbsp;</td>
                        <td class="dvtSelectedCell" align="center" nowrap> 查看 </td>
                        <td class="dvtTabCache" style="width:80%">&nbsp;</td>
                    <tr>
                </table>
            </td>
        </tr>
        <tr>
            <td valign=top align="center" >
                <div id="basicTab" style="border: 1px solid #e0dddd; background: white;">
                    <table border=0 cellspacing=0 cellpadding=0 width="98%" class="small" style="padding-top: 10px;">

                        <?php if(is_array($blocks)): $i = 0; $__LIST__ = $blocks;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><tr>
                                <td colspan=4 class="tabBlockViewHeader">
                                    <?php echo (L("$key")); ?>
                                </td>
                            </tr>

                            <!-- Here we should include the uitype handlings-->
                            <?php if(is_array($data)): $label = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subdata): $mod = ($label % 2 );++$label;?><tr style="height:30px;border: 1px solid black;background: #F0F0F0;">
                                    <?php if(is_array($subdata)): $mainlabel = 0; $__LIST__ = $subdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$maindata): $mod = ($mainlabel % 2 );++$mainlabel;?><style type="text/css">
    .dvtCellLabel, .cellLabel {
        background:#F7F7F7 url(./__PUBLIC__/Images/testsidebar.jpg) repeat-y scroll right center;
        border-bottom:1px solid #DEDEDE;
        border-left:1px solid #DEDEDE;
        border-right:1px solid #DEDEDE;
        color:#545454;
        padding-left:10px;
        padding-right:10px;
        white-space:nowrap;
        font-size: 15px;
    }
    .dvtCellInfo, .cellInfo {
        background:#FFFFFF ;
        padding-left:10px;
        padding-right:10px;
        border-bottom:1px solid #dedede;    
        border-right:1px solid #dedede;
        border-left:1px solid #dedede;
        color: black;
        font-size: 16px ;
    }
    
    .dvtCellInfo span{
        border: 0px solid ;
        font-size: 14px ;
    }
</style>
<?php $label = L("$maindata[name]"); ?>        
<?php $uitype = $maindata[uitype]; ?>                
<?php $name = $maindata[name]; ?>                      
<?php $value = $maindata[value]; ?>                    
<?php $length = $maindata[length]; ?>                  
<?php $readonly = $maindata[readonly]; ?>              



<?php if(($uitype == 0)): ?><td width=20% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>             </td>
<td width=30% align=left class="dvtCellInfo"><?php echo ($value); ?>
</td>


<?php elseif($uitype == 1): ?>                 
    <td width=20% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=30% align=left class="dvtCellInfo">
        <span><?php echo ($value); ?></span>
    </td>

    
    <?php elseif($uitype == 2): ?>
    <td width=20% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=30% align=left class="dvtCellInfo"><span><?php echo ($value); ?></span></td> 

    
    <?php elseif($uitype == 3): ?>
    <td width=20% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=30% align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td> 

            
    <?php elseif($uitype == 4): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?> 
    </td>
    <td width="30%" align=left class="dvtCellInfo">                
       <?php echo ($value); ?>
    </td>

    
    <?php elseif($uitype == 5): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td>

    
    <?php elseif($uitype == 6): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?>ww</font><?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td>  

    
    <?php elseif($uitype == 7): ?>   
    <td width=20% class="dvtCellLabel" align=right><font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
        <?php echo ($value); ?>
    </td>               

   

     
    <?php elseif($uitype == 9): ?>
    <td width="20%" class="dvtCellLabel" align=right>
       
        <?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
            <?php echo ($value); ?>
    </td>   

    
    <?php elseif($uitype == 10): ?>                 
    <td width=20% class="dvtCellLabel" align=right>
       </font><?php echo ($label); ?> 
    </td>
    <td width=30% align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td>

           
    <?php elseif($uitype == 11): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <?php echo ($label); ?> 
    </td>
    <td width="20%" align="left" class="dvtCellInfo">
        <textarea class="detailedViewTextBox" style="border:0px;" readonly="readonly" cols="60" rows="2"><?php echo ($value); ?></textarea>
    </td>

    
    <?php elseif($uitype == 17): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <?php echo ($label); ?> 
    </td>
    <td width="30%" align=left class="dvtCellInfo">
       <?php echo ($value); ?>
    </td>


    
    <?php elseif($uitype == 26): ?>
    <td width="20%" class="dvtCellLabel" align=right>
        <?php echo ($label); ?>
    </td>
    <td width="30%" align=left class="dvtCellInfo">
        
    </td>

  

<?php elseif($uitype == 21): ?>                 
<td width=15% class="dvtCellLabel" align=right>
    </font><?php echo ($label); ?> 
</td>
<td width=35% align=left class="dvtCellInfo">
   <?php echo ($value); ?>
</td>

    
<?php elseif($uitype == 22): ?>                 
<td width=15% class="dvtCellLabel" align=right>
    <?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td>   

    
<?php elseif($uitype == 23): ?>                 
    <td width=15% class="dvtCellLabel" align=right>
        <?php echo ($label); ?>
    </td>
    <td width=35% align=left class="dvtCellInfo">
        <?php echo ($value); ?>
    </td>
    
    
    <?php elseif($uitype == 50): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <table id="teladdresstable" width="90%" border=0 style="border: 1px solid #e0dddd; margin-top: 2px;" class="small">
            <tr class="detailedViewHeader">
                <td width="10%" align="center">序号</td>
                <td width="70%" align="center">地址</td>
                <td width="10%" align="center">所属分公司</td>
                
            </tr>
            <?php if(is_array($teladdress)): foreach($teladdress as $key=>$vo): ?><tr style="height:25px;border: 1px solid black;background: #F0F0F0;" class="CaseRow" >
                <td width="10%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
                <td width="70%" align="left" class="dvtCellLabel"><?php echo ($vo["address"]); ?></td>
                <td width="10%" align="left" class="dvtCellLabel"><?php echo ($vo["company"]); ?></td> 
            </tr><?php endforeach; endif; ?>  
        </table> 
    </td>  
    
    
    <?php elseif($uitype == 51): ?>
    <td width="100%" class="dvtCellLabel" align=center colspan="4">
        <table id="productsTable" width="90%" border=0 style="border: 1px solid #e0dddd; margin-top: 2px;" class="small">
            <tr class="detailedViewHeader" style="border: 1px solid red;">
                <td width="5%" align="center">序号</td>
                <td width="10%" align="center">产品代码</td>
                <td width="30%" align="center">产品名称</td>
                <td width="15%" align="center">数量</td>
                <td width="15%" align="center">单价</td>
                <td width="15%" align="center">金额</td>
            </tr>
            <?php if(is_array($orderproducts)): foreach($orderproducts as $key=>$vo): ?><tr style="height:20px;border: 1px solid black;background: #F0F0F0;" class="CaseRow" >
                <td width="5%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
                <td width="10%" align="center" class="dvtCellLabel"> 
                    <input id="productsCode_1" name="productsCode_1" type="text" size="10" readonly="readonly"  value="<?php echo ($vo["code"]); ?>" style="text-align:center;vertical-align:middle;"   />
                </td>
                <td width="30%" align="center" class="dvtCellLabel"> 
                    <input id="productsName_1" name="productsName_1" type="text" size="30" readonly="readonly" value="<?php echo ($vo["name"]); ?>" />
                </td>
                <td width="15%" align="center" class="dvtCellLabel"> 
                    <input id="productsNumber_1" name="productsNumber_1" type="text" size="5"  readonly="readonly" value="<?php echo ($vo["number"]); ?>" style="text-align:center;vertical-align:middle;" />
                </td>
                <td width="15%" align="center" class="dvtCellLabel"> 
                    <input id="productsPrice_1" name="productsPrice_1" type="text" size="10" readonly="readonly"  value="<?php echo ($vo["price"]); ?>" style="text-align:center;vertical-align:middle;" />
                </td>
                <td width="15%" align="center" class="dvtCellLabel"> 
                    <input id="productsMoney_1" name="productsMoney_1" type="text" size="10" readonly="readonly"   value="<?php echo ($vo["money"]); ?>" style="text-align:center;vertical-align:middle;" />
                </td>
            </tr><?php endforeach; endif; ?>
            <tr style="height:20px;border: 1px solid black;background: #F0F0F0;" class="CaseRow" >
                <td width="" align="right" colspan="5">
                    小计:
                </td>  
                <td class="dvtCellLabel"> 
                    <input id="productsTotalMoney" name="productsTotalMoney" type="text" size="10" readonly="readonly" style="border: 0px;" value="<?php echo ($totalmoney); ?>" />
                </td> 
             </tr>   
        </table> 
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
    

    <?php elseif($uitype == 55): ?>
    <td width="15%" class="dvtCellLabel" align=right>
    <input value="上一个日期" type="button" style="" onclick="yesterdayMenuClick();">
    <input value="下一个日期" type="button" style="float: right;" onclick="tomorrowMenuClick();"> 
    </td>
    <td width="35%" align="left" class="dvtCellInfo1">
    <input name="<?php echo ($name); ?>" tabindex="<?php echo ($vt_tab); ?>" id="todaymenudate" class="easyui-datebox" type="text" style="border:1px solid black;font-size:16px;" size="11" maxlength="10" value="<?php echo ($todaymenudate); ?>" data-options="onSelect:onTodaymenuSelect">
    </td>
    

<?php elseif($uitype == 56): ?>
<td width="100%" class="dvtCellLabel" align=center colspan="4"> 
        <textarea class="detailedViewTextBox" style="border:1px solid blcak;width: 98%;" readonly="readonly"  rows="20"><?php echo ($value); ?></textarea>
</td>    


<?php elseif($uitype == 58): ?>
<td width="100%" class="dvtCellLabel" align="left" colspan="4"> 
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


<?php elseif($uitype == 60): ?>
<td width="100%" class="dvtCellLabel" align=center colspan="4">
    <style>
    #productsTable{
        background-color: transparent;
    }


    .productsTableHeader{
        background-color: #66FFCC;
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
    <td width="15%" align="center" class="productsTableHeaderLeftTd">打印号</td>
    <td width="70%" align="center" class="productsTableHeaderLeftTd">打印内容</td>
    </thead>

    <?php if(is_array($orderPrintHandle)): foreach($orderPrintHandle as $key=>$vo): ?><tr  style="background-color: #F5F5F5;" >
            <td width="5%" align="center" class="dvtCellLabel"><?php echo ($key+1); ?></td>
            <td width="15%" align="center" class="dvtCellLabel"> 
                <span> <?php echo ($vo["printnumber"]); ?></span>
            </td>
            <td width="10%" align="center" class="dvtCellLabel"> 
                <span><?php echo ($vo["content"]); ?></span>
            </td>
        </tr><?php endforeach; endif; ?>

</table>


    </td>  


<?php elseif($uitype == 61): ?>
<td width="100%" class="dvtCellLabel" align="left" colspan="4"> 
    <div align="left">
    <label>预订日期：</label></br>  
        <div id="bookdatediv" style="float: left;width:980px;border: 0px solid red;" >
          <?php if(is_array($bookdate)): foreach($bookdate as $key=>$vo): ?><input  value="<?php echo ($vo["bookdate"]); ?>" readonly size="12" style=""><?php endforeach; endif; ?>   
        </div>    
    </div>
</td>

<?php elseif($uitype == 64): ?>
<td width=20% class="dvtCellLabel" align=right>
    <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
</td>
<td width=30% align=left class="dvtCellInfo">
    <span><?php echo ($value); ?></span>
</td>
    
    <?php elseif($uitype == 65): ?>
    <td width=20% class="dvtCellLabel" align=right>
        <font color="red"><?php echo ($mandatory_field); ?></font><?php echo ($label); ?>
    </td>
    <td width=30% align=left class="dvtCellInfo">
        <span><?php echo ($value); ?></span>
    </td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            <tr style="line-height: 2px;"><td>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?>

                        <table border="0" cellspacing="0" cellpadding="0" width="98%"
                               style="margin-top:5px; border: 1px solid #e0dddd; ">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    支付输入
                                </td>
                            </tr>

                            <tr style="background: #FFFFFF;">
                                <td colspan="4">
                                    
                                </td>
                            </tr>
                        </table>

                        <tr style="line-height: 5px;">
                            <td colspan="4" align="center">
                                <a href="#" class="easyui-linkbutton" data-options="iconCls:'icons-arrow-cross'"
                                   onclick="IndexIndexModule.updateOperateTab('__URL__/listview');" style="width:100px;">返回列表</a>
                            </td>
                        </tr>
                    </table>

                </div>
            </td>
        </tr>
    </table>
</div>

<script>
    var YingshouAccountsDetailviewModule = {

        //初始化
        init: function () {
            $('.moduleoperator').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1','ctrl+f1','f1'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告' && ($('#NoticeAction').val() == 'Detailview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告' && ($('#NoticeAction').val() == 'Detailview')) {
                    IndexIndexModule.updateOperateTab('__URL__/listview');
                };
            });
        }
    }

    $(function () {
        YingshouAccountsDetailviewModule.init();
    })
</script>