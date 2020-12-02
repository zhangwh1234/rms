<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName" onclick="updateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;营收订单查询操作</li>
        <li style="width: 50px;">&nbsp;</li>


        <li style="float: right;margin-right: 40px;"><a href="javascript:void(0);" class="moduleName"
                                                        onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>




<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
    <table id="yingshouordersearch_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>

<div id="yingshouordersearch-listview-datagrid-toolbar" style="padding:5px;height:auto">
    <form>
        查询选项
        <select name="searchOption" id="searchOption" class="txtBox" style="width:100px">
            <?php if($searchOptionValue): ?><option value="<?php echo ($searchOptionValue); ?>"><?php echo (L("$searchOptionValue")); ?></option>
                <?php else: ?>
                <option>全部</option><?php endif; ?>
            <?php if(is_array($searchOption)): foreach($searchOption as $key=>$value): ?><option value="<?php echo ($value); ?>"><?php echo (L("$value")); ?></option><?php endforeach; endif; ?>
        </select>
        查询内容
        <input id="searchText" name="searchText" type="text" style="width:240px" value="<?php echo ($searchTextValue); ?>">
        开始日期:
        <input id="startDate" name="startDate" type="text" class="easyui-datebox" required="required" value="<?php echo ($startDate); ?>" style="width:100px">
        结束日期:
        <input id="endDate" name="endDate" type="text" class="easyui-datebox" required="required" value="<?php echo ($endDate); ?>" style="width:100px">
        午别：
        <select name="searchAp" id="searchAp" class="txtBox" style="width:150px">
            <?php if($searchAp): ?><option value="<?php echo ($searchAp); ?>"><?php echo ($searchAp); ?></option><?php endif; ?>           
            <option value="上午">上午</option>
            <option value="下午">下午</option>
            <option value="全天">全天</option>
        </select>
        <a href="javascript:;" onclick="YingshouOrderSearchListviewModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">查询</a>
    </form>
</div>


<script>
    var YingshouOrderSearchListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouordersearch_index_datagrid',
        //初始化
        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
        },

        //重新设置page
        setPagination: function () {
            //定义订单分页表
            var pager = $('#yingshouordersearch_index_datagrid').datagrid().datagrid('getPager')
            pager.pagination({
                showRefresh: false,
                pageSize: IndexIndexModule.gridRowsNumber,
                layout: ['sep', 'first', 'links', 'last']
            });
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouOrderSearchListviewModule.detailview(' + rowData.ordersn + ')">查看</a>');
            return btn.join(' | ');
        },

        //查看记录
        detailview: function (id) {
            var that = this;
            startDate = $('#startDate').datebox('getValue');
            startAp = $('#searchAp').val();
            $(that.dialog).dialog({
                title: '订单详情',
                iconCls: 'icons-application-application_add',
                width: 1000,
                height: 540,
                cache: false,
                href: "__URL__/detailview/ordersn/" + id+"/startDate/"+startDate+
                    "/startAp/"+startAp,
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '关闭',
                    iconCls: 'icons-arrow-cross',
                    handler: function () {
                        $(that.dialog).dialog('close');
                    }
                }]
            });
        },

        //搜索
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            $(this.datagrid).datagrid({pageNumber: 1});
        },
    }

    $(function () {
        YingshouOrderSearchListviewModule.init();
        setTimeout(function () {
            YingshouOrderSearchListviewModule.setPagination();
            //$('#searchAp').val('下午');
        }, 100);

    })
</script>