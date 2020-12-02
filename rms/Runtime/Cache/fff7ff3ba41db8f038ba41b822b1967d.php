<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
    $(function () {

        $("#reportViewFormAccount").form({
            url: "__URL__/showAccountReport",
            type: "get",
            onSubmit: function () {

            },
            success: function (data) {
            
                $('#listaccount').html(data);
            }
        });

        $('#reportViewAccountBtn').bind('click', function () {
            //提交表单
            $("#reportViewFormAccount").submit();

        })

    })

    //导出excel统计表
    function outputAccountExcel() {
        var reportSerialize = $("#reportViewFormAccount")
            .serialize();
        document.location.href = '__URL__/outputAccountExcel' +
            '&' + reportSerialize;
    }
</script>

<div class="searchDiv" style="clear: both;">
    <form id="reportViewFormAccount" name="reportViewFormAccount" method="post" style="border: 1px solid white; width: 100%;">
        <input id="AccountAction" type="hidden" value="searchview" />
        <ul class="searchOption">
            <li>分公司</li>
            <li>
                <select name="reportCompany" id="reportCompany">
                    <?php if(is_array($companySelect)): foreach($companySelect as $key=>$vo): ?><option><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                </select>
            </li>
            <li></li>

            <li><input id="reportViewAccountBtn" name="reportViewBtn" type="button" class="crmbutton small create" value=" 确  认  " />&nbsp;</li>
            <li>&nbsp;</li>

            <li><input id="reportExcelAccountBtn" name="reportExcelBtn" type="button" class="crmbutton small create" value="导出EXCEL" onclick="outputAccountExcel();" />&nbsp;</li>
            <li>&nbsp;</li>

        </ul>
    </form>
</div>

<div class="list" id="listaccount" style="border: 0px solid red; clear: both;"></div>