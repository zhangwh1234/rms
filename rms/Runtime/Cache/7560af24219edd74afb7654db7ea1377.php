<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="YingshouRoomServiceListviewModule.createview();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');">新建工作餐</a></li>

        <li style="margin-left: 20px;"><input type="text" id="worklunchListviewDateInput" class="easyui-datebox" name="roomservicebillCreateviewDateInput" style="font-size: 16px;width:150px;" value="getDate" /></li>

        <li style="margin-left: 20px;"><select name="worklunchListviewApInput" id="worklunchListviewApInput" class="txtBox" style="width:100px;font-size: 14px;margin-top: 5px;">
                <?php if($getAp): ?><option value="<?php echo ($getAp); ?>"><?php echo ($getAp); ?></option><?php endif; ?>
                <option value="上午">上午</option>
                <option value="下午">下午</option>
            </select></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouWorklunchListviewModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">查询</a></li>


        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>
<div style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
    <table id="yingshouworklunch_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />
<script type="text/javascript">
    var YingshouWorklunchListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouworklunch_index_datagrid',

        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];
            btn.push('<a href="javascript:void(0);" onclick="YingshouWorklunchListviewModule.detailview(' + rowData.worklunchid + ',\'' + rowData.date + '\')">查看</a>');
            btn.push('<a href="javascript:void(0);" onclick="YingshouWorklunchListviewModule.editview(' + rowData.worklunchid + ',\'' + rowData.date + '\')">编辑</a>');
            btn.push('<a href="javascript:void(0);" onclick="YingshouWorklunchListviewModule.deleteRecord(' + rowData.worklunchid + ',\'' + rowData.date + '\')">删除</a>');
            return btn.join(' | ');
        },


        //刷新
        refresh: function () {
            $(this.datagrid).datagrid('reload');
        },



        //查看记录
        detailview: function (id, getDate) {
            var url = "<?php echo U('YingshouWorklunch/detailview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate=' + getDate;
            IndexIndexModule.updateOperateTab(url);
        },

        //编辑
        editview: function (id, getDate) {
            var url = "<?php echo U('YingshouWorklunch/editview');?>";
            url += url.indexOf('?') != -1 ? '&record=' + id : '?id=' + id;
            url += '&getDate=' + getDate;
            IndexIndexModule.updateOperateTab(url);
        },

        //删除
        deleteRecord: function (id, getDate) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;
                $.messager.progress({
                    text: '处理中，请稍候...'
                });
                $.post("<?php echo U('YingshouWorklunch/delete');?>", {
                    record: id,
                    getDate: getDate
                }, function (res) {
                    $.messager.progress('close');
                    if (!res.status) {
                        $.app.method.tip('提示信息', res.info, 'error');
                    } else {
                        $.app.method.tip('提示信息', res.info, 'info');
                        that.refresh();
                    };
                }, 'json');
            });
        },

        //根据日期查询显示销售汇总
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            var getDate = $('#worklunchListviewDateInput').datebox('getValue');
            var getAp = $('#worklunchListviewApInput').val();
            queryParams['getDate'] = getDate;
            queryParams['getAp'] = getAp;
            $(this.datagrid).datagrid({
                pageNumber: 1,
                queryParams: queryParams,
                url: "__URL__/listview"
            });
        },

        //新建的快捷操作
        quickKeyboardAction: function () {
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1', 'ctrl+f1', 'f1'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+3快捷键,查询公告
            Mousetrap.bind(['ctrl+3', 'ctrl+f3', 'f3'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.search('Notice', '<?php echo (L("Notice")); ?>');
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    IndexIndexModule.updateOperateTab('__URL__/listview/delsession/del');
                };
            });

            // ESC键
            Mousetrap.bind('esc', function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '公告') {
                    $(NoticeListviewModule.dialog).dialog('close');
                }
            });
        },
    };

    $(function () {
        YingshouWorklunchListviewModule.init();
    })
</script>