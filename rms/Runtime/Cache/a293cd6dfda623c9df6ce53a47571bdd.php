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

        $("#reportViewFormAccountMingxi").form({
            url: "__URL__/showAccountMingxiReport",
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
                $('#listAccountMingxi').html(data);
            }
        });

        $('#reportViewAccountMingxiBtn').bind('click', function () {
            //提交表单
            $("#reportViewFormAccountMingxi").submit();

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
    function outputAccountMingxiExcel() {
        var reportSerialize = $("#reportViewFormAccountMingxi")
            .serialize();
        document.location.href = '__URL__/outputAccountMingxiExcel' +
            '/' + reportSerialize;
    }

    /* 弹出窗口，选择产品 */
    //moduleName:产品名称 rowNum:行号 moduleName,rowNum
    function reportPaymentPickList(url) {
        company = $('#reportCompany').val();
        url = url + "company/" + company;
        $('#globel-dialog-div').dialog({
            title: '选择客户',
            iconCls: 'icons-application-application_add',
            width: 600,
            height: 610,
            cache: false,
            href: url,
            modal: true,
            collapsible: false,
            minimizable: false,
            resizable: false,
            maximizable: false

        });

    };

    //根据客户代码返回客户名称
    function reportViewFormAccountMingxi_keydownGetAccountCode(evt, obj) {
        evt = evt ? evt : ((window.event) ? window.event : null);
        var key = evt.keyCode ? evt.keyCode : evt.which;
        var code = $(obj).val();

        if ((key == 13) && (code.length > 0)) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        $("#accountmingxireportPayment").val(mydata.name); //跳转到下一行

                    } else {
                        alert("输入的客户代码有错误，请重新输入！");
                        return;
                    }
                }
            });
        }
    }


    function reportViewFormAccountMingxi_blurGetAccountCode(obj) {
       
        var code = $(obj).val();

        if ( (code.length > 0)) {
            var that = this;
            $.ajax({
                url: "__URL__/getAccountsByCode&code=" + code,
                async: true,
                beforeSend: function () {},
                success: function (mydata) {
                    var accountsName = '';
                    if (mydata) {
                        $("#accountmingxireportPayment").val(mydata.name); //跳转到下一行

                    } else {
                        alert("输入的客户代码有错误，请重新输入！");
                        return;
                    }
                }
            });
        }
    }
</script>

<div class="searchDiv" style="clear: both;">
    <form id="reportViewFormAccountMingxi" name="reportViewFormAccountMingxi" method="post" style="border: 1px solid white; width: 100%;">
        <input id="AccountMingxiAction" type="hidden" value="searchview" />
        <ul class="searchOption">
            <li>分公司</li>
            <li>
                <select name="reportCompany" id="reportCompany">
                    <?php if(is_array($companySelect)): foreach($companySelect as $key=>$vo): ?><option><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                </select>
            </li>
            <li>
                <label>代码</label>
                <input id="reportViewFormAccountMingxiCode" name="reportAccountCode" type="text" size="10" onkeydown="reportViewFormAccountMingxi_keydownGetAccountCode(event,this);"
                    onblur="reportViewFormAccountMingxi_blurGetAccountCode(this);" tabindex="1" value="" style="font-size:16px;" />
                <img id="<?php echo ($moduleName); echo (ucfirst(ACTION_NAME)); ?>searchIcon1" title="产品选择" src="./__PUBLIC__/Images/products.gif" style="cursor: pointer;" align="absmiddle"
                    onclick="reportPaymentPickList('__URL__/popupPaymentMgrview/'); " /><a href="javascript:reportPaymentPickList('__URL__/popupPaymentMgrview/');">选择</a>
                <input name="reportPayment" id="accountmingxireportPayment" size="20" readonly>
            </li>
            <li>开始日期</li>
            <li><input id="reportStartDate" name="reportStartDate" type="text" style="width: 100px" value="<?php echo ($reportStartValue); ?>" AUTOCOMPLETE="off" /></li>
            <li>结束日期</li>
            <li><input id="reportEndDate" name="reportEndDate" type="text" style="width: 100px" value="<?php echo ($reportEndValue); ?>" AUTOCOMPLETE="off" /></li>
            <li>&nbsp;</li>
            <li>午别</li>
            <li><select name="reportAp">
                    <option value="全天">全天</option>
                    <option value="上午">上午</option>
                    <option value="下午">下午</option>
                </select></li>

            <li><input id="reportViewAccountMingxiBtn" name="reportViewBtn" type="button" class="crmbutton small create" value=" 确  认  " />&nbsp;</li>
            <li>&nbsp;</li>

            <li><input id="reportExcelAccountMingxiBtn" name="reportExcelBtn" type="button" class="crmbutton small create" value="导出EXCEL" onclick="outputAccountMingxiExcel();" />&nbsp;</li>
            <li>&nbsp;</li>

        </ul>
    </form>
</div>

<div class="list" id="listAccountMingxi" style="border: 0px solid red; clear: both;"></div>