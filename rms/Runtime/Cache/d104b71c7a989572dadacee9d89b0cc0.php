<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu" id="listviewMenu" >
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;<a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');">列表操作</a></li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="ProductsPreMonitListviewModule.openDoubleOrderWindow();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="ProductsPreMonitListviewModule.openDoublePrepareWindow();">同时显示备餐<span>^1</span></a></li>
        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="ProductsPreMonitListviewModule.openOrderWindow();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="ProductsPreMonitListviewModule.openPrepareWindow();">显示备餐信息<span>^1</span></a></li>
        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="ProductsPreMonitListviewModule.openOrderWindow();"><img src=".__PUBLIC__/Images/searchBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="ProductsPreMonitListviewModule.openOrderWindow();">显示订单餐备信息<span>^3</span></a></li>

        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>

    </ul>
</div>

<script type="text/javascript">
    var ProductsPreMonitListviewModule = {

        /***
         * 打开备餐窗口
         */
        openDoublePrepareWindow: function(){
            var url = '__URL__' +  '/doubleview';
            window.open(url,'preparewindowmonit',"fullscreen=1")
        },

        /***
         * 打开备餐窗口
         */
       openPrepareWindow: function(){
           var url = '__URL__' +  '/monitview';
            window.open(url,'preparewindowmonit',"fullscreen=1")
        },

        /**
         * 打开订单餐品窗口
         */
        openOrderWindow: function(){
            var url = '__URL__' +  '/prepareview';
            window.open(url,'preparewindow',"fullscreen=1")
        }
    }

    setTimeout(function(){
        clearInterval(window.IndexIndexModule.messageObj);
        clearInterval(window.IndexIndexModule.sendnameMessageObj);
    },100);


</script>