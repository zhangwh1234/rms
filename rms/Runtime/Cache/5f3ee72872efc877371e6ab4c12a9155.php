<?php if (!defined('THINK_PATH')) exit();?><div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);" class="moduleName"
               onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a>
        </li>
        <li>&nbsp;&gt;新建操作</li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="NoticeCreateviewModule.insert();"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="NoticeCreateviewModule.insert();">保存<span>^9</span></a></li>
        <li style="width: 50px;">&nbsp;</li>
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
<div class="moduleoperator" style="overflow:scroll;clear:both;">
    <form id="NoticeCreateviewForm" method="post">
        <table border="0" cellspacing="0" cellpadding="0" width="99%" align="center">
            <tr>
                <td>
                    <table border="0" cellspacing="0" cellpadding="3" width="100%" class="small">
                        <tr>
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
                               style="margin:10px;border: 1px solid #e0dddd;">
                            <tr>
                                <td colspan="4" class="tabBlockViewHeader">
                                    公告基本信息
                                </td>
                            </tr>
                            <tr style="background: #FFFFFF;">
                                <td width="15%" align="right">
                                    <span style="font-size:14px;margin-right:10px;">公告内容:</span>
                                </td>
                                <td width="35%" align="left">
                                    <textarea class="easyui-validatebox" name="content" data-options="required:true"
                                              style="width:60%;height:150px;font-size:16px;"></textarea>
                                </td>
                            </tr>

                            <tr style="line-height: 5px;">
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>

<input id="NoticeAction" type="hidden" value="Createview"/>

<script>
    var NoticeCreateviewModule = {

        //初始化
        init: function () {
            $('.moduleoperator').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //保存记录
        insert: function () {
            $('#NoticeCreateviewForm').form('submit', {
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

        NoticeCreateviewModule.init();
        $('#NoticeCreateviewForm textarea[name=content]').focus();
    })
</script>