<?php if (!defined('THINK_PATH')) exit();?><!-- 综合查询  -->
<form style="overflow:hidden;">
    <table cellspacing="6" width="100%" style="margin-left: 10px;margin-top:10px;">
        <tr>
            <td>
                <span style="font-size: 16px;">综合查询</span>
                <input type="text"  id="invoiceMgrListviewOtherSearchInput"
                       name="searchTextOther"
                       style="font-size: 16px;width:80%;" value=""/>
            </td>
        <tr>
    </table>
</form>
<script>
    $(function(){
        $('#invoiceMgrListviewOtherSearchInput').focus();
        $('#invoiceMgrListviewOtherSearchInput').keydown(function(event){
            var that = this;
            if(event.keyCode == 13){
                var dialog = '#globel-dialog-div';
                var moduleName =  $(dialog).find("form input[name='searchTextOther']").val();
                $(dialog).find('form').eq(0).form('submit', {
                    onSubmit: function () {

                        var searchTextOther = $('#invoiceMgrListviewOtherSearchInput').val();
                        var url = '__URL__/searchviewOther/searchTextOther/'+ encodeURIComponent(searchTextOther);
                        url = encodeURI(url);
                        IndexIndexModule.openOperateTab(url, '发票查询');
                        $(dialog).dialog('close');
                        InvoiceMgrListviewModule.setRefresh(); //开启刷新
                        return false;
                    }
                });
                return false;
            }
        })
    })
</script>