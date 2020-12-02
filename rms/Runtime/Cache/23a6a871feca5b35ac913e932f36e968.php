<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    #wrap{
        width:96%;
        margin:5px auto;
        padding:4px 10px;
        border:1px solid #ccc;
        font-family: '黑体';
        clear:both;
        overflow:scroll;
    }

    #wrap  strong{
        font-size:16px;
        color:#0b99d8;
    }

    #wrap input{
        float: left;
    }

    #wrap dl{
        clear:both;
        margin:10px 0;
        border:1px solid #dcdcdc;
        height:auto;
        overflow:hidden;
    }

    #wrap  dl dt{
        display:block;
        height:25px;
        line-height:25px;
        background:#e6e6fa;
        text-indent:10px;
    }

    #wrap  dl dt strong{
        font-size:14px;
        color:#61;
        float:left;
    }

    #wrap dl dt input{
        font-size: 16px;
        margin-top: 8px;
        margin-left: 5px;
    }

    #wrap  dl dd{
        padding:5px;
        float:left;
    }

    #wrap dd span,a{
        font-size: 14px;
    }

    #wrap dd span{
        float: left;
    }
    #wrap dd input{
        margin-top: -4px;
        margin-left: 2px;
        font-size: 6px;   
    }
</style>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;添加模块功能操作</li>
        <li style="width: 50px;">&nbsp;</li>
        <li style="margin-left: 10px;"><a href="javascript:;" onclick="RoleEditviewAccessModule.update();"><img
                src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);" onclick="RoleEditviewAccessModule.update();">保存订单<span>^9</span></a></li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/listview')"><img src=".__PUBLIC__/Images/returnBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview')">返回列表</a></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>

    </ul>
</div>

<div id="wrap">
    <form id="RoleEditviewAccessForm" name="RoleEditviewAccessForm" method="post" >
        <?php if(is_array($node)): foreach($node as $key=>$action): ?><dl>
                <dt>
                    <strong><?php echo ($action["title"]); ?></strong>
                    <input type="checkbox" name="access[]" value="<?php echo ($action["id"]); ?>_1" level="1"  
                    <?php if($action["access"]): ?>checked='checked'<?php endif; ?>
                    />
                </dt>
                <?php if(is_array($action["child"])): foreach($action["child"] as $key=>$method): ?><dd>
                        <span><?php echo ($method["title"]); ?></span>
                        <input type="checkbox" name="access[]" value="<?php echo ($method["id"]); ?>_2"  level="2"
                        <?php if($method["access"]): ?>checked='checked'<?php endif; ?>
                        />
                    </dd><?php endforeach; endif; ?>
            </dl><?php endforeach; endif; ?>
        <input type="hidden" name="rid" value="<?php echo ($rid); ?>" >
        <table border="0" cellspacing=0 cellpadding=0 width="98%" class="small">
            <tr>
                <td></td>
                <td align="right">
                    <input  name="createsave" title="" accessKey="" class="crmbutton small save"   type="button"   value="<?php echo (L("Button_Save")); ?>" style="width:70px;float:right;margin-right:2px;margin-top:2px;margin-bottom:2px;"></td>
                <td>
                    <input title="<?php echo (L("Button_Cancel")); ?>" accessKey="" class="crmbutton small cancel" onclick="updateTab('__APP__/<?php echo ($moduleName); ?>/<?php echo ($returnAction); ?>')" type="button" name="button" value="  <?php echo (L("Button_Cancel")); ?>  " style="width:70px;float:left;margin-left:2px;margin-top:2px;margin-bottom:2px;"></td>
                <td> 
                </td>
            </tr>
        </table>
    </form>
</div>

<script>
    var RoleEditviewAccessModule = {

        //初始化
        init :function(){
            $('#wrap').height(IndexIndexModule.operationHeight);
            this.quickKeyboardAction();
        },

        //保存记录
        update: function () {
            $('#RoleEditviewAccessForm').form('submit', {
                url: '__URL__/editAccess',
                onSubmit: function () {
                    var isValid = $(this).form('validate');
                    if (!isValid) {
                        return false;
                    }
                },
                success: function (res) {
                    var data = eval('(' + res + ')');
                    if(!data.status){
                        $.app.method.tip('提示信息', data.info, 'error');
                    }else{
                        $.app.method.tip('提示信息', data.info, 'info');
                        IndexIndexModule.updateOperateTab(data.url);
                    }
                }
            });
        },

        //新建的快捷操作
        quickKeyboardAction: function () {

            $('input[level=1]').click(function(){
                var inputs = $(this).parents('dl').find('input');
                if($(this).is(":checked")){
                    inputs.attr('checked','checked');
                }else{
                    inputs.removeAttr('checked');
                }

            });

            // ctrl+9快捷键,新建公告
            Mousetrap.bind(['ctrl+9','ctrl+f9','f9'], function(e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#MessagesAction').val() == 'editview')) {
                    MessagesEditviewModule.update();
                };
            });

            // ctrl+4快捷键,放弃
            Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
                // 返回选项卡
                var tab = $('#operation').tabs('getSelected');
                var tabOptions = tab.panel('options');
                if (tabOptions.title == '群发消息' && ($('#MessagesAction').val() == 'editview')) {
                    IndexIndexModule.updateOperateTab('__URL__/listview');
                };
            });
        }
    }

    $(function () {

        RoleEditviewAccessModule.init();


    })
</script>