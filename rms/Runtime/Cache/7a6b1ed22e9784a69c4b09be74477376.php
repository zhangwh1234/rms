<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
    $(function () {
        $('#reportStartDate').datebox({
            required: true,
            formatter: function (date) {
                return date.Format('yyyy-MM-dd');
            }

        });
        $('#reportEndDate').datebox({
            required: true,
            formatter: function (date) {
                return date.Format('yyyy-MM-dd');
            }

        });

        $("#reportViewFormSendnameMingxi").form({
            url: "__URL__/showSendnameMingxiReport",
            type: "get",
            onSubmit: function () {
                //进行表单验证
                //如果返回false阻止提交
                if ($('#reportStartDate').datebox('getValue') == '') {
                    // alert('查询开始日期不能空');
                    //alert($('#generalreport-startdate').datebox('getValue'));
                    // return false;
                }
                if ($('#reportEndDate').datebox('getValue') == '') {
                    //alert('查询结束日期不能空');
                    // return false;
                }

            },
            success: function (data) {
                console.log(data);
                $('#listSendnameMingxi').html(data);
            }
        });

        $('#reportViewSendnameMingxiBtn').bind('click', function () {
            //提交表单
            $("#reportViewFormSendnameMingxi").submit();

        })

        // 对Date的扩展，将 Date 转化为指定格式的String
        // 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
        // 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
        // 例子：
        // (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
        // (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
        Date.prototype.Format = function (fmt) { //author: meizz
            var o = {
                "M+": this.getMonth() + 1, //月份
                "d+": this.getDate(), //日
                "h+": this.getHours(), //小时
                "m+": this.getMinutes(), //分
                "s+": this.getSeconds(), //秒
                "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                "S": this.getMilliseconds()
                //毫秒
            };
            if (/(y+)/.test(fmt))
                fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "")
                    .substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt))
                    fmt = fmt.replace(RegExp.$1,
                        (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k])
                            .substr(("" + o[k]).length)));
            return fmt;
        }
    })

    //导出excel统计表
    function outputTongjiExcel() {
        var reportSerialize = $("#reportViewFormSendnameMingxi")
            .serialize();
        document.location.href = '__URL__/outputSendnameMingxiExcel' +
            '&' + reportSerialize;
    }
    
</script>

<div class="searchDiv" style="clear: both;">
    <form id="reportViewFormSendnameMingxi" name="reportViewFormSendnameMingxi" method="post" style="border: 1px solid white; width: 100%;">
        <input id="SendnameMingxiAction" type="hidden" value="searchview" />
        <ul class="searchOption">
            <li>分公司</li>
            <li>
                <select name="reportCompany" id="reportCompany">
                    <?php if(is_array($companySelect)): foreach($companySelect as $key=>$vo): ?><option><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                </select>
            </li>
            <li>开始日期</li>
            <li><input id="reportStartDate" name="reportStartDate" type="text" style="width: 150px" value="<?php echo ($reportStartValue); ?>" AUTOCOMPLETE="off" /></li>
            <li>结束日期</li>
            <li><input id="reportEndDate" name="reportEndDate" type="text" style="width: 150px" value="<?php echo ($reportEndValue); ?>" AUTOCOMPLETE="off" /></li>
            <li>&nbsp;</li>
            <li>午别</li>
            <li><select name="reportAp">
                    <option value="全天">全天</option>
                    <option value="上午">上午</option>
                    <option value="下午">下午</option>
                </select></li>

            <li><input id="reportViewSendnameMingxiBtn" name="reportViewBtn" type="button" class="crmbutton small create" value=" 确  认  " />&nbsp;</li>
            <li>&nbsp;</li>

            <li><input id="reportExcelSendnameMingxiBtn" name="reportExcelBtn" type="button" class="crmbutton small create" value="导出EXCEL" onclick="outputTongjiExcel();" />&nbsp;</li>

        </ul>
    </form>
</div>

<div class="list" id="listSendnameMingxi" style="border: 0px solid red; clear: both;"></div>