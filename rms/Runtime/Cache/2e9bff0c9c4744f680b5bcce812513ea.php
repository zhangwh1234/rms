<?php if (!defined('THINK_PATH')) exit();?><form>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>
        <li style="margin-left: 50px;"><input type="text"  id="diningCollectListviewDateInput"
                                              class="easyui-datebox"
                                              name="cdate"
                                              style="font-size: 16px;width:150px;" value="cdate"/></li>

        <li style="margin-left: 20px;"><select name="cap" id="diningCollectListviewApInput"
                                               class="txtBox" style="width:100px;font-size: 14px;margin-top: 5px;">
            <?php if($searchAp): ?><option value="<?php echo ($cap); ?>"><?php echo ($cp); ?></option><?php endif; ?>
            <option value="上午">上午</option>
            <option value="下午">下午</option>
        </select></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="DiningCollectListviewModule.search(this);" class="easyui-linkbutton"
                                          iconCls="icons-table-table">查询</a></li>

        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img
                src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>
</form>

<div id="DiningCollectListviewDiv" style="height:400px;width:100%;clear:both;" class="ModuleListviewDiv">
    <table id="diningcollect_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden"  value="Listview" />

<script type="text/javascript">

    var DiningCollectListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#diningcollect_index_datagrid',


        init: function () {
            //设置div的高度
            $('.ModuleListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },


        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];

            return btn.join(' | ');
        },


        //新建的快捷操作
        quickKeyboardAction:function(){
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1','ctrl+f1','f1'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '产品' && ($('#ProductsAction').val() == 'Listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+3快捷键,查询公告
            Mousetrap.bind(['ctrl+3','ctrl+f3','f3'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '产品' && ($('#ProductsAction').val() == 'Listview')) {
                    IndexIndexModule.search('Products','<?php echo (L("Products")); ?>');
                };
            });


            // ESC键
            Mousetrap.bind('esc', function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '产品') {
                    $(IndexIndexModule.dialog).dialog('close');
                }
            });
        },

        //根据日期查询显示销售汇总
        search: function(that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {

                queryParams[this['name']] = this['value'];
            });
            var cdate = $('#diningCollectListviewDateInput').datebox('getValue');
            var cap = $('#diningCollectListviewApInput').val();

            queryParams['cdate'] = cdate;
            queryParams['cap'] = cap;
            $(this.datagrid).datagrid({pageNumber: 1});
        }
    };

    $(function(){
        DiningCollectListviewModule.init();

    })
</script>