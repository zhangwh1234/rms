<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#" onclick="updateTab('__URL__/index');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;<span style="background-color: #FF9797;font-size: 16px;"><?php echo ($operName); ?></span></li>
        <li style="width: 5px;">&nbsp;</li>
        <li><span id="reportName" style="font-size: 16px;"></span></li>

        <li style="float: right;margin-right: 60px;"><a href="#" onclick="IndexIndexModule.closeOperateTab();;">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();;"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>


<div class="easyui-layout" style="width:100%;height:500px;" id="YingshouReportDiv">
    <div data-options="region:'west',split:true,title:'菜单'" style="width:150px;padding:10px;">
        <ul id="generalreporttree">
            <li>
                <span>报表</span>
                <ul>
                    <li>营收汇总表</li>
                    <li>客户汇总表</li>
                    <li>客户明细表</li>
                    <li>外送产品销售汇总表</li>
                    <li>送餐员送餐业务汇总表</li>
                    <li>送餐员送餐业务明细表</li>
                    <li>赠卡明细表</li>
                    <li>餐券明细表</li>
                    <li>活动明细表</li>
                    <li>客户出入汇总表</li>
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

            if (node.text == '营收汇总表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getHuizongReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
                $('#reportName').html('营收汇总表');
            }


            if (node.text == '客户汇总表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getAccountReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '客户明细表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getAccountMingxiReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '外送产品销售汇总表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getProductsReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '送餐员送餐业务汇总表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getSendnameReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '送餐员送餐业务明细表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getSendnameMingxiReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '赠卡明细表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getFreebieReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }
            if (node.text == '餐券明细表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getMealticketReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }


            if (node.text == '活动明细表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getActivityReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }

            if (node.text == '客户出入汇总表') {
                $.ajax({
                    type: "POST",
                    url: "__APP__/YingshouReport/getAccountInoutReport",
                    dataType: "html",
                    success: function (data) {
                        $("#generalreportcenter").html(data);
                    }
                });
            }


        }
    });

    $(function () {
        
        setTimeout(function () {
            $('#YingshouReportDiv').layout('resize', {
                width: '100%',
                height: IndexIndexModule.operationHeight-5
            })
        }, 200)

    })
</script>