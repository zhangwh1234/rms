<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
  .topCls{
      font-size:14px ;
  }
</style>

<div id="top">
    <div id="logo"><span>system logo 系统图标</span></div>
    <div id="subject" align="center"><span><?php echo ($city); ?> 送 餐 管 理 系 统</span><span style="margin-left: 20px;color: red;font-size: 14px;line-height: 30px;word-spacing: 6px;display: none;"><?php echo ($_SESSION['userInfo']['rolename']); ?></span></div>
    <div id="topnav">
        <ul>   
            <?php if(Think.config.RBAC_SUPERADMIN == 'admin'): ?><li class="topCls">超级用户管理员</li><?php endif; ?>
            <?php if(!empty($_SESSION['userInfo']['department'])): ?><li class="topCls">部门:</li>
                <li class="topCls">[<?php echo ($_SESSION['userInfo']['department']); ?>]&nbsp;&nbsp;</li><?php endif; ?>
            <?php if(!empty($_SESSION['userInfo']['rolename'])): ?><li class="topCls">职务:</li>
                <li class="topCls" style="color:blue;">[<?php echo ($_SESSION['userInfo']['rolename']); ?>]&nbsp;&nbsp;</li><?php endif; ?>
            <li>用户名(<?php echo ($_SESSION['userInfo']['truename']); ?>)&nbsp;<a  onclick="IndexIndexModule.changeCode();" style="color:blue;">密码</a>&nbsp; <a href="__APP__/Login/logout">退出</a></li>
        </ul>
    </div>
</div>