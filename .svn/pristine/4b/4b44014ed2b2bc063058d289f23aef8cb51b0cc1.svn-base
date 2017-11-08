<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
    $(function(){
        //新建表单提交事件
        $('#createView<?php echo ($moduleName); ?>').submit(function(event) {
            if($('#createView<?php echo ($moduleName); ?>  input[name=code]').val() == ''){
                alert('代码不能为空!');
                submitIs = false;
            }
            if($('#createView<?php echo ($moduleName); ?>  input[name=name]').val() == ''){
                alert('姓名不能为空!');
                submitIs = false;
            }
        })

        //新建定制键盘移动方案
        $('#createView<?php echo ($moduleName); ?>  #code').bind('keydown',function(event){    
                
            if((event.which == 13) || (event.which == 40)){ 
                $('#createView<?php echo ($moduleName); ?>  #name').focus();
            }           
        })
        //姓名
        $('#createView<?php echo ($moduleName); ?>  #name').bind('keydown',function(event){ 
            if((event.which == 13) || (event.which == 40)){ 
                $('#createView<?php echo ($moduleName); ?>  #telphone').focus();
            } 
            if(event.which == 38){  //上移
                $('#createView<?php echo ($moduleName); ?>  #code').focus();
            }          
        })
        //号码
        $('#createView<?php echo ($moduleName); ?>  #telphone').bind('keydown',function(event){ 
            if(event.which == 38){  //上移
                $('#createView<?php echo ($moduleName); ?>  input[name=name]').focus();
            }          
        })
        //编辑定制键盘移动方案
        $('#editView<?php echo ($moduleName); ?>  #code').bind('keydown',function(event){ 
            if((event.which == 13) || (event.which == 40)){ 
                $('#editView<?php echo ($moduleName); ?>  input[name=name]').focus();
            }           
        })
        //姓名
        $('#editView<?php echo ($moduleName); ?>  input[name=name]').bind('keydown',function(event){ 
            if((event.which == 13) || (event.which == 40)){ 
                $('#editView<?php echo ($moduleName); ?>  #telphone').focus();
            } 
            if(event.which == 38){  //上移
                $('#editView<?php echo ($moduleName); ?>  #code').focus();
            }          
        })
        //号码
        $('#editView<?php echo ($moduleName); ?>  #telphone').bind('keydown',function(event){ 
            if(event.which == 38){  //上移
                $('#editView<?php echo ($moduleName); ?>  input[name=name]').focus();
            }          
        })
    });
</script>