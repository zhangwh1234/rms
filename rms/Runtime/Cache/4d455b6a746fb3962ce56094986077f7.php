<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    #wrap{
        width:96%;
        overflow:scroll;
        margin:5px auto;
        padding:4px 10px;
        border:1px solid #ccc;
        font-family: '黑体';
        clear:both;
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

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/createview');"><img src=".__PUBLIC__/Images/returnBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="javascript:void(0);"  onclick="IndexIndexModule.updateOperateTab('__URL__/listview');">返回列表</a></li>


        <li style="float: right;margin-right: 60px;"><a href="javascript:void(0);"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>

    </ul>
</div>
<div id="wrap">

    <?php if(is_array($node)): foreach($node as $key=>$action): ?><dl>
            <dt>
                <strong><?php echo ($action["title"]); ?></strong>            
                <?php if($action["access"]): ?><img src=".__PUBLIC__/Images/checkbox.gif" /><?php endif; ?>                
            </dt>
            <?php if(is_array($action["child"])): foreach($action["child"] as $key=>$method): ?><dd>
                    <span><?php echo ($method["title"]); ?></span>
                    <?php if($method["access"]): ?><img src=".__PUBLIC__/Images/checkbox.gif" /><?php endif; ?>                   
                </dd><?php endforeach; endif; ?>
        </dl><?php endforeach; endif; ?>
    <input type="hidden" name="rid" value="<?php echo ($rid); ?>" >
    <table border="0" cellspacing=0 cellpadding=0 width="98%" class="small">
        <tr>
            <td width="25%"></td>
            <td width="25%">
                </td>
            <td width="25%">
                <input title="<?php echo (L("Button_Cancel")); ?>" accessKey="" class="crmbutton small cancel" onclick="updateTab('__APP__/<?php echo ($moduleName); ?>/<?php echo ($returnAction); ?>')" type="button" name="button" value="  返回  " style="width:70px;float:left;margin-left:2px;margin-top:2px;margin-bottom:2px;"></td>
            <td> 
            </td>
        </tr>
    </table>
</div>

<script>
    var RoleDetailviewAccessModule = {

        //初始化
        init: function () {
            $('#wrap').height(IndexIndexModule.operationHeight);
        }
    }

    $(function(){
        RoleDetailviewAccessModule.init();
    })
</script>