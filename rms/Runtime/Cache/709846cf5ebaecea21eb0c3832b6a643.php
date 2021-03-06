<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" onclick="updateTab('__URL__/index');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;<span style="background-color: #FF9797;font-size: 16px;"><?php echo ($operName); ?></span></li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="float: right;margin-right: 60px;"><a href="#" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div class="easyui-layout" style="width:100%;height:500px;">
    <div data-options="region:'west',split:true,title:'菜单'" style="width:150px;padding:10px;">
        <ul id="generalreporttree">
            <li>
                <span>报表</span>
                <ul>
                    <li>订单量</li>
                    <li>产品销售量</li>
                    <li>产品客户数</li>
                    <li>产品销售额</li>
                    <li>产品客户数分布</li>
                    <li>接线量</li>
                    <li>催餐统计</li>
                    <li>大客户统计</li>
                    <li>退餐废单统计</li>
                    <li>送达率统计</li>
                    <li>北京财务订单金额统计</li>
                    <?php if($domainnode == 'bj.lihuaerp.com'): ?><li>北京财务订单金额统计</li><?php endif; ?>
                     <li>总部订单金额统计</li>
                    <?php if($domainnode == 'finance'): ?><li>总部订单金额统计</li><?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>
    <div data-options="region:'center',title:'内容'">
        <div id="generalreportcenter"></div>
    </div>
</div>

<script type="text/javascript">
    $("#generalreporttree").tree({
        onClick: function (node) {
            //$(this).tree('beginEdit',node.target);

            if (node.text == '订单量') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiorder",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '产品销售量') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiproductsnumber",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }


            if (node.text == '产品客户数') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiproductscn",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '产品销售额') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiproductstotalmoney",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '产品客户数分布') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiproductscf",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '接线量') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjitelname",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '催餐统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiorderhurry",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }
            if (node.text == '大客户统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjibigcustomer",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '退餐废单统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiorderbackcancel",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '送达率统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/Tongjiordersendtime",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }
            if (node.text == '北京财务订单金额统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/TongjiFinance",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '总部订单金额统计') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/GeneralReport/getReport/reporttype/TongjiZongbu",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }


        }
    });
</script>