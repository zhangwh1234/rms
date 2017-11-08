<?php if (!defined('THINK_PATH')) exit();?><style>
    .eedatagrid-row-selected {
        background: yellow;
        color: red;
        cursor: default;
    }
</style>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="IndexIndexModule.updateOperateTab('__URL__/createview/returnAction/<?php echo ($returnAction); ?>');"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="IndexIndexModule.updateOperateTab('__URL__/createview/returnAction/<?php echo ($returnAction); ?>');">新建订单<span>^1</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="OrderFormListviewModule.addressSearchInput();"><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="OrderFormListviewModule.addressSearchInput();">地址查询<span>^6</span></a></li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="OrderFormListviewModule.telphoneSearchInput();"><img
                src=".__PUBLIC__/Images/phone.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"
               onclick="OrderFormListviewModule.telphoneSearchInput();">电话号码查询<span>^7</span></a>
        </li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);"
                                          onclick="OrderFormListviewModule.comeinTelphoneSearchInput();"><img
                src=".__PUBLIC__/Images/phone.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="OrderFormListviewModule.comeinTelphoneSearchInput();">来电记录查询<span>^8</span></a>
        </li>

        <li style="margin-left: 10px;"><a href="javascript:;" onMouseOver=""><img
                src=".__PUBLIC__/Images/addressBtn.jpg" alt="" title="" border="0"></a></li>
        <li><a href="#" class="moduleName"
               onclick="OrderFormListviewModule.otherSearchInput();">综合查询<span>^9</span></a>
        </li>
        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="OrderFormListviewDiv" style="height:400px;width:100%;clear:both;">
    <table id="orderform_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />
<script>
    var OrderFormListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#orderform_index_datagrid',
        screenWidth:0,  //屏幕宽度

        init: function () {
            //设置div的高度
            $('#OrderFormListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            this.screenWidth = window.screen.availWidth;
            $("#orderform_index_datagrid").datagrid({
                nowrap: false
            });
        },

        //重新设置page
        setPagination:function(){
            //定义订单分页表
            var pager = $('#orderform_index_datagrid').datagrid().datagrid('getPager');
            pager.pagination({
                showRefresh: false,
                pageSize : IndexIndexModule.gridRowsNumber,
                layout: ['sep','first','prev','manual','links','next','last'],
                buttons: [{
                    id: 'orderformOtherMsg',
                    text: '<?php echo ($orderformOtherMsg); ?>'

                }]
            });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            if(window.screen.availWidth < 1280){
                var btn = [];
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.detailview(' + rowData.orderformid +','+ rowIndex+ ')">查看</a>');
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.editview(' + rowData.orderformid +','+ rowIndex+  ')">改单</a>');
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.hurry(' + rowData.orderformid +','+ rowIndex+ ')">催送</a>');
                return btn.join(' ');
            }else{
                var btn = [];
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.detailview(' + rowData.orderformid +','+ rowIndex+ ')">查看</a>');
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.editview(' + rowData.orderformid +','+ rowIndex+  ')">改单</a>');
                btn.push('<a href="javascript:void(0);" onclick="OrderFormListviewModule.hurry(' + rowData.orderformid +','+ rowIndex+ ')">催送</a>');
                return btn.join(' | ');
            }
        },

        //送餐员的格式化操作
        sendname:function(val,rowData,rowIndex){
            if((rowData.longitude) && (rowData.latitude) && (!rowData.sendlongitude)){
                var btn = [];
                btn.push( val +
                '<a href="javascript:void(0);" onclick="OrderFormListviewModule.mapshowview(' + rowData.orderformid +','+ rowIndex+ ')" ><img src=".__PUBLIC__/Images/lhkc/location.png" style="height: 20px;" /></a>');
                return btn.join('');
            }else if((rowData.sendlongitude) && (rowData.sendlatitude)){
                var btn = [];
                btn.push( val +
                '<a href="javascript:void(0);" onclick="OrderFormListviewModule.sendmapshowview(' + rowData.orderformid +','+ rowIndex+ ')" ><img src=".__PUBLIC__/Images/lhkc/sendlocation.png" style="height: 20px;" /></a>');
                return btn.join('');
            }else{
                var btn = [];
                btn.push(val);
                return btn.join(' | ');
            }

        },

        //初始返回,定位行操作,但是在翻页是,就不操作
        setRowSelect :function(){
            $('#orderform_index_datagrid').datagrid('selectRow', <?php echo ($rowIndex); ?>);
        },

        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },

        //查看记录
        detailview: function (id,rowIndex) {
            var url = '__URL__/detailview/returnAction/<?php echo ($returnAction); ?>/record/'+id
                    + '/rowIndex/' + rowIndex+'/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        //修改订单
        editview: function (id ,rowIndex) {
            var url = '__URL__/editview/returnAction/<?php echo ($returnAction); ?>/record/'+id
                    + '/rowIndex/' + rowIndex+'/pagetype/listview';
            IndexIndexModule.updateOperateTab(url);
        },

        //催送订单
        hurry:function(id ,rowIndex){
            var that = this;
            $.messager.confirm('提示信息', '确定要催送订单吗？', function(result){
                if(!result) return false;

                $.messager.progress({text:'处理中，请稍候...'});
                $.post("<?php echo U('OrderForm/hurry');?>", {record: id}, function(res){
                    $.messager.progress('close');
                    $('#orderform_index_datagrid').datagrid('reload');
                    $.app.method.tip('提示信息', res.info, 'info');
                    setTimeout(function(){
                        $('#orderform_index_datagrid').datagrid('selectRow', rowIndex);  //显示行定位
                    },200)
                }, 'json');
            });
        },

        //订单地址输入
        addressSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐地址查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderForm/searchAddressInput');?>",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [
                    {
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
                                    if((value.name == 'searchTextAddress') && (value.value == '')){
                                        value.value = '全部';
                                    }
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订餐地址查询');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        //电话查询
        telphoneSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐电话查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderForm/searchTelphoneInput');?>",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [
                    {
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).find('form').eq(0).form('submit', {
                            onSubmit: function () {
                                var isValid = $(this).form('validate');
                                if (!isValid) return false;

                                var formArray = $(this).serializeArray();
                                var url = '__URL__/searchviewTelphone/';
                                $.each(formArray, function (key, value) {
                                    if((value.name == 'searchTextTelphone') && (value.value == '')){
                                        value.value = '全部';
                                    }
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订餐电话查询');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        //来电记录查询
        comeinTelphoneSearchInput: function(){
            var that = this;
            $(that.dialog).dialog({
                title: '来电记录查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderForm/searchComeintelphoneInput');?>",
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
                                var url = '__URL__/searchviewComeinTelphone/';
                                $.each(formArray, function (key, value) {
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '来电记录查询');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        /**
         * 其他查询：综合查询，查询多个字段,或者叫普通查询
         */
        otherSearchInput: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '订餐综合查询',
                iconCls: 'icons-application-application_add',
                width: 500,
                height: 140,
                cache: false,
                href: "<?php echo U('OrderForm/searchOtherInput');?>",
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
                                var url = '__URL__/searchviewOther/';
                                $.each(formArray, function (key, value) {
                                    if((value.name == 'searchTextOther') && (value.value == '')){
                                        value.value = '全部';
                                    }
                                    url = url + value.name + '/' + value.value;
                                })
                                IndexIndexModule.openOperateTab(url, '订餐综合查询');
                                $(that.dialog).dialog('close');
                                return false;
                            }
                        });
                    }
                }, {
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },


        //订单坐标地址显示
        mapshowview: function (id) {
            var url = '__URL__/mapshowview/record/'+id;
            var that = this;
            $(that.dialog).dialog({
                title: '显示',
                iconCls: 'icons-application-application_add',
                width: 800,
                height: 540,
                cache: false,
                href: url,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        //送餐员坐标地址显示
        sendmapshowview: function (id) {
            var url = '__URL__/sendmapshowview/record/'+id;
            var that = this;
            $(that.dialog).dialog({
                title: '显示',
                iconCls: 'icons-application-application_add',
                width: 800,
                height: 540,
                cache: false,
                href: url,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '取消',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },


        //新建的快捷操作
        quickKeyboardAction:function(){
            var that = this;
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1','ctrl+f1','f1'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+6快捷键, 地址查询
            Mousetrap.bind(['ctrl+6','ctrl+f6','f6'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    that.addressSearchInput();
                };
            });

            // ctrl+7快捷键,电话查询
            Mousetrap.bind(['ctrl+7', 'ctrl+f7', 'f7'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    that.telphoneSearchInput();
                };
            });

            // ctrl+8快捷键,来电记录查询
            Mousetrap.bind(['ctrl+8', 'ctrl+f8', 'f8'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    that.comeinTelphoneSearchInput();
                };
            });

            // ctrl+9快捷键 ,综合查询
            Mousetrap.bind(['ctrl+9', 'ctrl+f9', 'f9'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    that.otherSearchInput();
                };
            });

            // ESC键
            Mousetrap.bind('esc', function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单') {
                    $(IndexIndexModule.dialog).dialog('close');
                }
            });
        }


    }

    $(function () {
        OrderFormListviewModule.init();
        setTimeout(function(){
            OrderFormListviewModule.setPagination();
        },100);

        setTimeout(function(){
            OrderFormListviewModule.setRowSelect();  //显示行定位
        },600)
    })

</script>