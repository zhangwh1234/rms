<?php if (!defined('THINK_PATH')) exit();?><!-- 地址查询  -->
<form style="overflow: hidden;">
 <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
            <span style="font-size: 16px;">地址查询</span>
            <input type="text"  id="orderFormListviewAddressSearchInput"
                       name="searchTextAddress" autocomplete="off"
                       style="font-size: 16px;width:80%;" value=""/>
                <input type="text" style="width:0px;visibility:hidden;" >
            </td>
        <tr>
 </table>
</form>
<script>
    $(function(){
        $('#orderFormListviewAddressSearchInput').focus();
        $('#orderFormListviewAddressSearchInput').keydown(function(event){
            var that = this;
            if(event.keyCode == 13){
                var dialog = '#globel-dialog-div';
                var moduleName =  $(dialog).find("form input[name='searchTextAddress']").val();
                $(dialog).find('form').eq(0).form('submit', {
                    onSubmit: function () {
                        var isValid = $(this).form('validate');
                        if (!isValid) return false;
                        var searchTextAddress = $(that).val();
                        var url = '__URL__/searchviewAddress/searchTextAddress/'+encodeURIComponent(searchTextAddress);
                        IndexIndexModule.openOperateTab(url, '订餐地址查询');
                        $(dialog).dialog('close');
                        return false;
                    }
                });
                return false;
            }
        })
    })
</script>