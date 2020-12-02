<?php if (!defined('THINK_PATH')) exit();?><!-- 配送门店查询  -->
<form style="overflow:hidden;" method="get">
    <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
                <span style="font-size: 16px;">配送店查询</span>
                <input type="text"  id="orderDistributionListviewSearchInputCompanyCode"
                       name="searchTextCompanyCode" maxlength="1"
                       style="font-size: 16px;width:20%;" value="" autocomplete="off" />
                <input type="text" name="searchTextCompany"  id="orderDistributionListviewSearchInputCompanyName"
                       style="font-size: 16px;width:40%;"
                       value="" autocomplete="off" readonly>
            </td>
        <tr>
    </table>
</form>
<script>
    var orderDistributionListviewSearchCompany = {
        init:function(){
            $('#orderDistributionListviewSearchInputCompanyCode').focus();
            this.inputCompanyCodeEvent();
        },

        //输入送餐员代码回车键事件
        inputCompanyCodeEvent:function(){
            $('#orderDistributionListviewSearchInputCompanyCode').keydown(function(event){
                if(event.keyCode == 13){
                    var dialog = '#globel-dialog-div';
                    companyCode = $('#orderDistributionListviewSearchInputCompanyCode').val();
                    companyName = $('#orderDistributionListviewSearchInputCompanyName').val();

                    if(companyName){
                        var url = '__URL__/searchviewCompany/searchTextCompany/'+encodeURIComponent(companyName);
                        IndexIndexModule.openOperateTab(url, '分配配送店查询');
                        $(dialog).dialog('close');
                        OrderDistributionListviewModule.setRefresh();  //开启刷新
                        return false;
                    }

                    url = "__URL__/getCompanyByCode/code/" + companyCode;

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
                            $('#orderDistributionListviewSearchInputCompanyName').val(data.data.company);
                        }
                    });

                }
            });
        }
    }
    $(function(){
        orderDistributionListviewSearchCompany.init();
    })
</script>