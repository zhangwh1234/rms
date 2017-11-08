<?php if (!defined('THINK_PATH')) exit();?>
<script type="text/javascript">
    //定义当前的导航名
    var category = '#<?php echo ($category); ?>_subnav';
</script>
<style type="text/css">

</style>

<div id="nav">
    <div id="mainnav">
        <ul>
            <?php if(is_array($navmenu)): $i = 0; $__LIST__ = $navmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tab): $mod = ($i % 2 );++$i; if($key == $category): ?><li> <a class="select" href="javascript:void(0);" onclick="clickTabMenu('<?php echo (L("$key")); ?>','<?php echo ($key); ?>_subnav',this);" ><?php echo (L("$key")); ?></a></li>
                    <?php else: ?>
                    <li> <a href="javascript:void(0);"  onclick="clickTabMenu('<?php echo (L("$key")); ?>','<?php echo ($key); ?>_subnav',this);" ><?php echo (L("$key")); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
    <div id="subnav">
        <span>子菜单:</span>
        <?php if(is_array($navmenu)): $i = 0; $__LIST__ = $navmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tab): $mod = ($i % 2 );++$i; if($key == $category): ?><ul id="<?php echo ($key); ?>_subnav">
                    <?php if(is_array($tab)): $i = 0; $__LIST__ = $tab;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a  href="javascript:void(0);" onclick="clickSubMenu('__APP__/<?php echo ($sub); ?>/index/delsession/del','<?php echo (L("$sub")); ?>','<?php echo ($key); ?>_subnav',this);"><?php echo (L("$sub")); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <?php else: ?>
                <ul id="<?php echo ($key); ?>_subnav" style="display: none;">
                    <?php if(is_array($tab)): $i = 0; $__LIST__ = $tab;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><li><a href="javascript:void(0);" onclick="clickSubMenu('__APP__/<?php echo ($sub); ?>/index/delsession/del','<?php echo (L("$sub")); ?>','<?php echo ($key); ?>_subnav',this);"><?php echo (L("$sub")); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>



<script type="text/javascript">


    $(function(){
        $('#menu').panel({
            border:false
        });


    })

    //点击导航菜单
    function clickTabMenu(title,bottomMenu,obj){   //       
        var t =  $('#operation').tabs('exists',title);
        if(t){
            $('#operation').tabs('select',title);
            return;
        }

        //选中，改变颜色
        $("#mainnav a").removeClass("select");
        $(obj).addClass('select');  



        //显得当前的模块
        $(category).hide();
        //显示底部菜单
        $('#'+bottomMenu).show();
        //设置当前的底部菜单
        category = '#'+ bottomMenu;
    }

    //点击子菜单
    function clickSubMenu(url,title,bottomMenu,obj){
        var t =  $('#operation').tabs('exists',title);
        if(t){
            $('#operation').tabs('select',title);
            return;
        }

        //选中，改变颜色
        $("#subnav a").removeClass("select");
        $(obj).addClass('select');  

        //添加一个选项卡面板
        $('#operation').tabs('add',{ 
            closable:false, 
            title:title,  
            href:url,  
            closable:false
        });

    }
</script>