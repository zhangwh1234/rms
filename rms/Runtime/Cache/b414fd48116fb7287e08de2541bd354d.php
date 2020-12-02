<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    #NodeListviewOperationDiv{
        width:96%;
        margin:5px auto;
        padding:4px 10px;
        border:1px solid #ccc;
        font-family: '黑体';
        clear: both;
        overflow: scroll;
    }

    #NodeListviewOperationDiv  strong{
        font-size:16px;
        color:#0b99d8;
    }

    #NodeListviewOperationDiv dl{
        margin:10px 0;
        border:1px solid #dcdcdc;
        height:auto;
        overflow:hidden;
    }

    #NodeListviewOperationDiv  dl dt{
        display:block;
        height:36px;
        line-height:36px;
        background:#e6e6fa;
        text-indent:10px;
    }

    #NodeListviewOperationDiv  dl dt strong{
        font-size:14px;
        color:#61;
    }

    #NodeListviewOperationDiv  dl dd{
        padding:5px;
        float:left;
    }
    
    #NodeListviewOperationDiv dd span,a{
        font-size: 14px;
    }
</style>
<div class="moduleMenu">
    <ul>
        <li><?php echo (L("$navName")); ?></li>
        <li><a href="#"  onclick="updateTab('__URL__/listview');">&nbsp;&gt;<?php echo (L("$moduleName")); ?></a></li>
        <li>&nbsp;&gt;列表操作</li>
        <li style="width: 50px;">&nbsp;</li>

        <li style="margin-left: 10px;"><a href="javascript:;" onclick="IndexIndexModule.updateOperateTab('__URL__/createviewModule');"><img src=".__PUBLIC__/Images/newBtn.png" alt="" title="" border="0"></a></li>
        <li><a href="#"  onclick="IndexIndexModule.updateOperateTab(&apos;<?php echo U('Node/createviewModule');?>&apos;);">新建模块</a></li>


        <li style="float: right;margin-right: 60px;"><a href="#"   onclick="IndexIndexModule.closeOperateTab();" >关闭</a></li>
        <li style="float:right;"><a href="javascript:;" onclick="IndexIndexModule.closeOperateTab();"><img src=".__PUBLIC__/Images/closeBtn.png" alt="" title="" border="0"></a></li>

    </ul>
</div>
<div id="NodeListviewOperationDiv">
    <?php if(is_array($node)): foreach($node as $key=>$action): ?><dl>
            <dt>
                <span>模块名称:</span>
                <strong><?php echo ($action["title"]); ?></strong>&nbsp;|<?php echo ($action["name"]); ?>|
                [<a href="#" onclick="IndexIndexModule.updateOperateTab(&apos;<?php echo U('Node/createviewMethod',array('pid'=>$action['id'],'level'=>2));?>&apos;)">添加功能</a>]
                [<a href="#" onclick="IndexIndexModule.updateOperateTab(&apos;<?php echo U('Node/editviewModule',array('record'=>$action['id'],'level'=>2));?>&apos;)">修改</a>]
                [<a href="#" onclick="NodeListviewModule.deleteModule('<?php echo ($action["id"]); ?>');">删除</a>]
            </dt>
            <?php if(is_array($action["child"])): foreach($action["child"] as $key=>$method): ?><dd>
                    <span>功能名称:</span>
                    <strong><?php echo ($method["title"]); ?></strong>
                    [<a href="#" onclick="IndexIndexModule.updateOperateTab(&apos;<?php echo U('Node/editviewMethod',array('record'=>$method['id'],'level'=>2));?>&apos;)">修改</a>]
                    [<a href="#" onclick="NodeListviewModule.deleteMethod('<?php echo ($method["id"]); ?>');">删除</a>]
                </dd><?php endforeach; endif; ?>
        </dl><?php endforeach; endif; ?>
</div>

<script>
    var NodeListviewModule = {

        init:function(){
            //设置div的高度
            $('#NodeListviewOperationDiv').height(IndexIndexModule.operationHeight);
        },

        //删除模块
        deleteModule: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;

                $.messager.progress({text: '处理中，请稍候...'});
                $.get("<?php echo U('Node/deleteModule');?>", {record: id}, function (res) {
                            $.messager.progress('close');

                            if (!res.status) {
                                $.app.method.tip('提示信息', res.info, 'error');
                            } else {
                                $.app.method.tip('提示信息', res.info, 'info');
                                that.refresh();
                            }
                        }, 'json'
                )
                ;
            });
        },

        //删除功能
        deleteMethod: function (id) {
            var that = this;
            $.messager.confirm('提示信息', '确定要删除吗？', function (result) {
                if (!result) return false;

                $.messager.progress({text: '处理中，请稍候...'});
                $.get("<?php echo U('Node/deleteMethod');?>", {record: id}, function (res) {
                            $.messager.progress('close');

                            if (!res.status) {
                                $.app.method.tip('提示信息', res.info, 'error');
                            } else {
                                $.app.method.tip('提示信息', res.info, 'info');
                                that.refresh();
                            }
                        }, 'json'
                )
                ;
            });
        }
    }

    $(function(){
        NodeListviewModule.init();
    })
</script>