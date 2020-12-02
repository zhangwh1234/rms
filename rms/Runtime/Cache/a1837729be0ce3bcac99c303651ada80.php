<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD html 4.01 Transitional//EN">
<html>
    <head>
        <link href=".__PUBLIC__/Images/lhkc/favicon.ico" rel="SHORTCUT ICON">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>送餐系统</title>
        <script type="text/javascript" src=".__PUBLIC__/Js/jquery.min.js"></script>
        <script type="text/javascript" src=".__PUBLIC__/Js/easyui/jquery.easyui.min.js"></script>
        <style type="text/css">
            .loginsmall{
                font-family: Arial, Helvetica, sans-serif;
                font-size: 14px;
                color: #000000;    
            }

            .small {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 16px;
                color: #000000;
            }

            #loginTitle{
                font-size: 43px;
                font-family: 黑体;
                letter-spacing:10px;
                font-weight: 300;
                color:red;
            }

            .footer{
                height: 50px;
                width: 100%;
                background-color:white;
                position:fixed;
                bottom:0;
                font-size:14px;
                
            }
        </style>

        <script type="text/javascript">

            $(document).ready(function() { 
                $('#loginForm input[name=name]').focus();
                //键盘回车自动下移
                $("input").keypress(function (e) {
                    var keyCode = e.which;
                    if (keyCode == 13){
                        var i;
                        for (i = 0; i < this.form.elements.length; i++)
                        if (this == this.form.elements[i])
                            break;
                        i = (i + 1) % this.form.elements.length;
                        this.form.elements[i].focus();
                        return false;
                    }
                    else
                        return true;
                });

                //验证码回车
                $('#verifyInput').keydown(function(e){
                    if(e.which == 13){
                        $('#loginForm').submit(); 
                    }
                });

            });
            //登陆提交
            function submitLogin(){
                $('#loginForm').submit(); 
            }


            function fleshVerify(){
                var timenow = new Date().getTime();
                $('#verifyImg').attr('src', '__URL__/verify/'+timenow);
            }


        </script>
    </head>

    <body>
        </BR>
        </BR>
        <div align="center" style="margin-top: 80px;">    
            <table border="0" cellpadding="0" cellspacing="0" width="700" style="margin-bottom: 20px;">
                <tr>
                </tr>
                <tr>
                    <td align="center"><span id="loginTitle"><?php echo ($city); ?>送餐管理信息系统</span></td>
                </tr>
                <tr>
                    <td class="small" align="center">

                    </td>

                </tr>
            </table>
            <!-- key to check session_out in Ajax key=s18i14i22a19 -->
            <!-- Login Starts -->
            <table border="0" cellspacing="0" cellpadding="0" width=700 style="border: 1px solid blue;">
                <tr>
                    <td class="small z1" align="center">
                        <img src=".__PUBLIC__/Images/login/lihualeft.jpg">
                    </td>
                    <td class="small z2" align="center">

                        <!-- Sign in form -->
                        <br>
                        <form  action="__URL__/login" method="POST" name="loginForm" id="loginForm">
                            <input type="hidden" name="module" value="Users">
                            <input type="hidden" name="action" value="Authenticate">
                            <input type="hidden" name="return_module" value="Users">
                            <input type="hidden" name="return_action" value="Login">
                            <table border="0" cellpadding="0" cellspacing="0" width="90%" bgcolor="#f5f5f5" >
                                <tr bgcolor="#f5f5f5">
                                    <td class="signinHdr" style="display:none;"><img src=".__PUBLIC__/Images/login/signin.gif" alt="" title="">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="small">
                                        <!-- form elements -->
                                        <br>
                                        <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tr bgcolor="#f5f5f5">
                                                <td class="small" align="right" width="30%">用户名:</td>
                                                <td class="small" align="left" width="70%"><input class="loginsmall" type="text" size="20"  name="name" value="<?php  echo $login_user_name ?>" tabindex="1" AUTOCOMPLETE="off"></td>
                                            </tr>
                                            <tr bgcolor="#f5f5f5">
                                                <td class="small" align="right" width="30%">密    码:</td>
                                                <td class="small" align="left" width="70%"><input class="loginsmall" type="password" size='20' name="password" value="" tabindex="2"></td>
                                            </tr>
                                            <tr bgcolor="#f5f5f5">
                                                <td class="small" align="right" width="30%">验证码:</td>
                                                <td class="small" align="left" width="70%"><input id="verifyInput" class="loginsmall" style='ime-mode:disabled;' autocomplete="off" type="text" size='20' name="verify"   value="" tabindex="3"   /></td>
                                            </tr>
                                            <tr bgcolor="#f5f5f5">
                                                <td class="small" align="right" width="30%"></td>
                                                <td width="70%">&nbsp;
                                                    <img id="verifyImg" src='__APP__/Login/verify/' onclick="fleshVerify();" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle" />
                                                </td>
                                            </tr>
                                            <?php
 if(isset($login_error) && $login_error != ""){ ?>
                                            <tr>
                                                <td class="small" align="right" width="30%"></td>
                                                <td width="70%" class="small" colspan="2"><font color="Red"> <?php echo $login_error ?> </font></td>
                                            </tr>
                                            <?php
 } ?>
                                            <tr bgcolor="#f5f5f5">
                                                <td class="small">&nbsp;</td>
                                                <td class="small"><img src=".__PUBLIC__/Images/login/lihuabtnSignInNEW.gif"  onclick="submitLogin();"></td>
                                            </tr>
                                        </table>
                                        <br><br>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer"><a style="align:bottom;" href="http://www.beian.miit.gov.cn" target="_blank">京ICP备05007639-1号</a></div>

    </body>
    </html>