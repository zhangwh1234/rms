<?php if (!defined('THINK_PATH')) exit();?><!-- 送餐员查询  -->
<form style="overflow:hidden;" method="get">
    <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
                <span style="font-size: 16px;">送餐员查询</span>
                <input type="text" class="easyui-validatebox" id="orderHandleListviewSearchInputSendcode"
                       name="searchTextSendcode"
                       style="font-size: 16px;width:20%;" value="" autocomplete="off" />
                <input type="text" name="searchTextSendname"  id="orderHandleListviewSearchInputSendname"
                       style="font-size: 16px;width:40%;"
                        value="" autocomplete="off" readonly>
            </td>
        <tr>
    </table>
</form>
<script>
    var orderHandleListviewSearchSendname = {
        init:function(){
            $('#orderHandleListviewSearchInputSendcode').focus();
            this.inputSendcodeEvent();
        },

        //输入送餐员代码回车键事件
        inputSendcodeEvent:function(){
            $('#orderHandleListviewSearchInputSendcode').keydown(function(event){
               if(event.keyCode == 13){
                   var sendcode = $('#orderHandleListviewSearchInputSendcode').val();
                   var sendname = $('#orderHandleListviewSearchInputSendname').val();
                   if(sendname){
                       sendname = encodeURIComponent(sendname);
                       var dialog = '#globel-dialog-div';
                       var url = '__URL__/searchviewSendname/searchTextSendname/'+sendname;
                       url = encodeURI(url);
                       IndexIndexModule.openOperateTab(url, '配送送餐员查询');
                       $(dialog).dialog('close');
                       OrderHandleListviewModule.setRefresh();
                   }else{
                       url = "__URL__/getSendnameByCode/code/"+sendcode;
                       $.get(url,function(data){
                           if(data.error == 'error'){
                               $.messager.show({
                                   title:'提示',
                                   msg:data.msg,
                                   showType:'show',
                                   style:{
                                       left:0,
                                       right:'',
                                       top:'',
                                       bottom:-document.body.scrollTop-document.documentElement.scrollTop
                                   }
                               });

                           }else if(data.success){
                               $('#orderHandleListviewSearchInputSendname').val(data.data.sendname);
                               OrderHandleListviewModule.orderHandleData = '';  //清理缓存输入量
                           }
                       });
                   }
               }
            });
        }
    }
    $(function(){
        orderHandleListviewSearchSendname.init();
    })
</script>