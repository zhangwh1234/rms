<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 30px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:void(0);" onclick="YingshouRoomServiceListviewModule.createview();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="YingshouRoomServiceListviewModule.createview();">生成报数单</a></li>

        <li style="margin-left: 20px;"><input type="text" id="roomservicebillCreateviewDateInput" class="easyui-datebox" name="roomservicebillCreateviewDateInput" style="font-size: 16px;width:150px;"
                value="<?php echo ($getDate); ?>" /></li>

        <li style="margin-left: 20px;"><select name="roomservicebillCreateviewApInput" id="roomservicebillCreateviewApInput" class="txtBox" style="width:100px;font-size: 14px;margin-top: 5px;">
                <?php if($getAp): ?><option value="<?php echo ($getAp); ?>"><?php echo ($getAp); ?></option><?php endif; ?>
                <option value="上午">上午</option>
                <option value="下午">下午</option>
            </select></li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="YingshouRoomServiceListviewModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">查询</a></li>


        <li style="float: right;margin-right: 10px;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();">关闭</a></li>
        <li style="float:right;"><a href="javascript:void(0);" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>
    </ul>
</div>

<div id="OrderFormListviewDiv" style="height:400px;width:100%;clear:both;">
    <table id="yingshouroomservice_index_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => true, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 10, 1 => 20, 2 => 30, 3 => 50, 4 => 80, 5 => 100, ), 'pageSize' => 10, ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>
</div>
<input id="<?php echo ($moduleName); ?>Action" type="hidden" value="Listview" />
<script>
    var YingshouRoomServiceListviewModule = {
        dialog: '#globel-dialog-div',
        datagrid: '#yingshouroomservice_index_datagrid',

        init: function () {
            //设置div的高度
            $('#OrderFormListviewDiv').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
            $('#yingshouroomservice_index_datagrid').datagrid({
                rowStyler: YingshouRoomServiceListviewModule.rowStyler
            })
        },

        //操作格式化
        operate: function (val, rowData, rowIndex) {
            var btn = [];

            btn.push('<a href="javascript:void(0);" onclick="YingshouRoomServiceListviewModule.checkorder(' +
                "'" + rowData.name + "'" + ',' + rowIndex + ',\'' + rowData.date + '\',\'' + rowData.ap +
                '\')">交账</a>');
            return btn.join(' | ');
        },

        rowStyler: function (index, row) { //就改变背景颜色，以便区别
            state = row.isShow;
            if (state == 1) {
                return 'background-color:#6293BB; color:black'; // return inline style
            }
        },

        //初始返回,定位行操作,但是在翻页是,就不操作
        setRowSelect: function () {
            //$('#roomservicebill_index_datagrid').datagrid('selectRow', <?php echo ($rowIndex); ?>);
        },


        //查看送餐员的所送的订单
        checkorder: function (name, rowIndex, date, ap) {
            var url = '__URL__/checkOrder/name/' + name +
                '/room_date/' + date + '/room_ap/' + ap;

            IndexIndexModule.updateOperateTab(url);
        },

        //生成报数单的对话框
        createview: function () {
            var that = this;
            $(that.dialog).dialog({
                title: '报数单生成器',
                iconCls: 'icons-application-application_add',
                width: 400,
                height: 140,
                cache: false,
                href: "<?php echo U('YingshouRoomService/generalview');?>",
                modal: true,
                collapsible: false,
                minimizable: false,
                resizable: false,
                maximizable: false,
                buttons: [{
                    text: '确定',
                    iconCls: 'icons-other-tick',
                    handler: function () {
                        $(that.dialog).dialog('close');
                        $.messager.progress({
                            text: '处理中，请稍候...'
                        });
                        //获取日期
                        var room_date = $('#roomservicebillGeneralviewDateInput').datebox(
                            'getValue'); //
                        var room_ap = $('#roomservicebillGeneralviewApInput').val();
                        var data = {
                            'room_date': room_date,
                            'room_ap': room_ap
                        };
                        $.ajax({
                            type: "POST",
                            url: "__URL__/roomCalculate",
                            data: data,
                            dataType: "json",
                            success: function (res) {
                                if (res.state == 0) { //0就是错误
                                    $.messager.progress('close');
                                    var url = '__URL__/resultview';
                                    IndexIndexModule.updateOperateTab(url);
                                }
                                if (res.state == 2) {
                                    alert('没有或者错误的日期和午别，无法生成！');
                                    $.messager.progress('close');
                                    return false;
                                };
                                if (res.state == 1) { //state = 1就是success
                                    $.messager.progress('close');
                                    $('#roomservicebillCreateviewDateInput').datebox('setValue', room_date);
                                    $('#roomservicebillCreateviewApInput').val(room_ap);
                                    //$(that.datagrid).datagrid('reload');
                                    var queryParams = $(that.datagrid).datagrid('options').queryParams;
                                    queryParams['getDate'] = room_date;
                                    queryParams['getAp'] = room_ap;
                                    $(that.datagrid).datagrid({
                                        pageNumber: 1,
                                        rowStyler: this.rowStyler
                                    });
                                }
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

        //根据日期查询显示销售汇总
        search: function (that) {
            var queryParams = $(this.datagrid).datagrid('options').queryParams;
            $.each($(that).parent('form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });
            var getDate = $('#roomservicebillCreateviewDateInput').datebox('getValue');
            var getAp = $('#roomservicebillCreateviewApInput').val();
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
            var that = this;
            // ctrl+1快捷键,新建公告
            Mousetrap.bind(['ctrl+1', 'ctrl+f1', 'f1'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    IndexIndexModule.updateOperateTab('__URL__/createview');
                };
            });

            // ctrl+6快捷键, 地址查询
            Mousetrap.bind(['ctrl+6', 'ctrl+f6', 'f6'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '订餐单' && ($('#OrderFormAction').val() == 'Listview')) {
                    that.addressSearchInput();
                };
            });

         
            // ESC键
            Mousetrap.bind('esc', function (e) {
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
        YingshouRoomServiceListviewModule.init();

        setTimeout(function () {
            YingshouRoomServiceListviewModule.setRowSelect(); //显示行定位
        }, 600)
    })
</script>