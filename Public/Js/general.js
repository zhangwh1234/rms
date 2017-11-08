//定义命名空间类
var Namespace = new Object();
//定义全局发票数据
var telInvoiceArray = {};

Namespace.register = function (path) {
    var arr = path.split(".");
    var ns = "";
    for (var i = 0; i < arr.length; i++) {
        if (i > 0) ns += ".";
        ns += arr[i];
        eval("if(typeof(" + ns + ") == 'undefined') " + ns + " = new Object();");
    }
}

//查找html实体的位置--x
function findPosX(obj) {
    var curleft = 0;
    if (document.getElementById || document.all) {
        while (obj.offsetParent) {
            curleft += obj.offsetLeft
            obj = obj.offsetParent;
        }
    } else if (document.layers) {
        curleft += obj.x;
    }
    return curleft;
}

// 查找html实体的位置--y
function findPosY(obj) {
    var curtop = 0;
    if (document.getElementById || document.all) {
        while (obj.offsetParent) {
            curtop += obj.offsetTop
            obj = obj.offsetParent;
        }
    } else if (document.layers) {
        curtop += obj.y;
    }
    return curtop;
}


// 鼠标在导航后显示主菜单
function fnDropDown(obj, Lay) {
    return;
    var tagName = document.getElementById(Lay);
    var leftSide = findPosX(obj);
    var topSide = findPosY(obj);
    var maxW = tagName.style.width;
    var widthM = maxW.substring(0, maxW.length - 2);
    var getVal = eval(leftSide) + eval(widthM);
    if (getVal > document.body.clientWidth) {
        leftSide = eval(leftSide) - eval(widthM);
        tagName.style.left = leftSide + 24 + 'px';
    }
    else
        tagName.style.left = leftSide - 1 + 'px';
    tagName.style.top = topSide + 24 + 'px';
    // tagName.style.display = 'block';

    // 显得当前的模块
    $(category).hide();
    tagName.style.display = 'block';
    category = '#' + Lay;
    // alert(category);


}

// 隐藏主菜单
function fnHideDrop(obj) {
    // document.getElementById(obj).style.display = 'none';
    // $(category).show();
}

// 离开导航后，到主菜单，继续显示主菜单
function fnShowDrop(obj) {
    document.getElementById(obj).style.display = 'block';
    // 显得当前的模块
    // $(category).hide();
}

$(document).ready(function () {

    // 键盘回车自动下移
    $("input").keypress(function (e) {
        var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
        if (keyCode == 13) {
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

    $.extend($.fn.datagrid.methods, {
        keyCtr: function (jq) {
            return jq.each(function () {
                var grid = $(this);
                grid.datagrid('getPanel').panel('panel').attr('tabindex', 1).bind('keydown', function (e) {
                    switch (e.keyCode) {
                        case 38: // up
                            var selected = grid.datagrid('getSelected');
                            if (selected) {
                                var index = grid.datagrid('getRowIndex', selected);
                                grid.datagrid('selectRow', index - 1);
                            } else {
                                var rows = grid.datagrid('getRows');
                                grid.datagrid('selectRow', rows.length - 1);
                            }
                            break;
                        case 40: // down
                            var selected = grid.datagrid('getSelected');
                            if (selected) {
                                var index = grid.datagrid('getRowIndex', selected);
                                grid.datagrid('selectRow', index + 1);
                            } else {
                                grid.datagrid('selectRow', 0);
                            }
                            break;
                    }
                });
            });
        }
    });


});

// 来电显示，F2复制电话和地址到订单表单中
Mousetrap.bind(['ctrl+2', 'ctrl+f2', 'f2'], function (e) {
    // 返回选项卡
    var tab = $('#operation').tabs('getSelected');
    var tabOptions = tab.panel('options');
    if (tabOptions.title == '订餐单') {  // 订单创建状态，
        if ($('#OrderFormAction').val() == 'Createview') { // 保存
            // 可以复制电话内容
            var telphoneNumber = $('#telphoneNumber').val();
            $.each(teladdressObj, function (key, value) {
                if (value.teladdressid == teladdressObjId) {
                    $('#OrderFormCreateviewForm input[name=address]').val(value.address);
                    if (value.longitude) {
                        $('#longitude-create').val(value.longitude);
                        $('#latitude-create').val(value.latitude);
                        $('#createcoordShow').show();
                    }
                    return false;
                }
            });

            if (typeof(telphoneNumber) != "undefined") {
                $('#OrderFormCreateviewForm input[name=telphone]').val(telphoneNumber);
                if (telphoneNumber.length == 0) {
                    return;
                }
                // 查询发票，如果有发票，加入发票下拉列表框
                $.ajax({
                    url: APP + '/OrderForm/getTelphoneInvoiceHeader/telphoneNumber/' + telphoneNumber,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.telinvoice) {
                            // 删掉以前的选项
                            $('#invoiceheaderselect').empty();
                            $(data.telinvoice).each(function (i, val) {
                                $('#invoiceheaderselect').append("<option value=''>" + val.header + "</option>");
                            });
                            if (data.telinvoice[0].header) {
                                $('#invoice_header').val($.trim(data.telinvoice[0].header));
                            }

                            if (data.telinvoice[0].body) {
                                $('#invoice_body').val($.trim(data.telinvoice[0].body));
                            }
                            $('#invoice_gmf_nsrsbh').val($.trim(data.telinvoice[0].gmf_nsrsbh));
                            $('#invoice_gmf_dzdh').val($.trim(data.telinvoice[0].gmf_dzdh));
                            $('#invoice_gmf_yhzh').val($.trim(data.telinvoice[0].gmf_yhzh));

                            //放入发票内容
                            telInvoiceArray = data.telinvoice;
                            //放入发票内容,弃掉,不用
                            //$('#OrderFormCreateviewForm input[name=invoiceheader]').val(data.telinvoice[0]);
                        }
                    }
                });
            }
        }
    }
});


// 今日菜单功能中看昨天的菜单
function yesterdayMenuClick() {
    var selectdate = $('#todaymenudate').datebox('getValue');
    var d = new Date(selectdate);   // 格式化为Date对像;
    if (d == "Invalid Date") {
        alert("非日期");
        return;
    }
    // 当前日期的毫秒数 + 天数 * 一天的毫秒数
    var n = d.getTime() - 1 * 24 * 60 * 60 * 1000;
    var result = new Date(n);
    // 格式化日期
    var month = result.getMonth() + 1;
    if (month < 10) {
        month = '0' + month;
    }
    var date = result.getDate();
    if (date < 10) {
        date = '0' + date;
    }

    var yesterday = result.getFullYear() + "-" + month + "-" + date;
    $('#todaymenudate').datebox('setValue', yesterday);
    var url = APP + '/TodayMenu/detailview/date/' + yesterday;

    IndexIndexModule.updateOperateTab(url); // 显示菜单
}

// 今日菜单功能中看明天的菜单
function tomorrowMenuClick() {
    var selectdate = $('#todaymenudate').datebox('getValue');
    var d = new Date(selectdate);   // 格式化为Date对像;
    if (d == "Invalid Date") {
        alert("非日期");
        return;
    }
    // 当前日期的毫秒数 + 天数 * 一天的毫秒数
    var n = d.getTime() + 1 * 24 * 60 * 60 * 1000;
    var result = new Date(n);
    // 格式化日期
    var month = result.getMonth() + 1;
    if (month < 10) {
        month = '0' + month;
    }
    var date = result.getDate();
    if (date < 10) {
        date = '0' + date;
    }

    var tomorrow = result.getFullYear() + "-" + month + "-" + date;
    $('#todaymenudate').datebox('setValue', tomorrow);
    var url = APP + '/TodayMenu/detailview/date/' + tomorrow;

    IndexIndexModule.updateOperateTab(url); // 显示菜单
}


// 今日菜单中，选择日期
function onTodaymenuSelect(date) {
    var todaymenuDate = date.Format("yyyy-MM-dd");
    var url = APP + '/TodayMenu/detailview/date/' + todaymenuDate;
    IndexIndexModule.updateOperateTab(url);
}


// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
// 例子：
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
// (new Date()).Format("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18
Date.prototype.Format = function (fmt) { // author: meizz
    var o = {
        "M+": this.getMonth() + 1,                 // 月份
        "d+": this.getDate(),                    // 日
        "h+": this.getHours(),                   // 小时
        "m+": this.getMinutes(),                 // 分
        "s+": this.getSeconds(),                 // 秒
        "q+": Math.floor((this.getMonth() + 3) / 3), // 季度
        "S": this.getMilliseconds()             // 毫秒
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


// 调度主界面打印程序
function orderPrintData(orderformid, rowIndex, accounttype) {
    // 取得打印的内容
    $.ajax({
        type: "POST",
        url: APP + "/OrderHandle/getPrintOrder/orderformid/" + orderformid,
        dataType: "json",
        success: function (data) {
            printOrderForm(data, data['accounttype']);
        }

    })
}

// 实际打印
function printOrderForm(data, accounttype) {
    var printPage = $.cookie('rmsPrintPage');  //localStorage['printPage'];  // 取得打印纸张类型
    if (printPage == '') {
        alert('请设置打印纸张类型');
    } else if (printPage == '60hot') {
        print_60(data);
    } else if (printPage == '80hot') {
        print_80(data, accounttype);
    } else if (printPage == '30lian') {
        print_ht(data);
    } else {
        alert('没有这样的打印纸张类型');
        return false;
    }

}


// 80宽热敏的打印代码
function print_80(data, accounttype) {
    var print_index = $.cookie('rmsPrinterIndex'); // 读取 cookie,打印机类型
    if (print_index < 0) {
        alert('请设置打印机');
        return;
    }

    // 定义行号
    var linespacing = 14;
    var row = 0;  // 循环变量
    // 重新设置打印机的设备
    LODOP.PRINT_INIT("printorder");
    LODOP.SET_PRINTER_INDEX(print_index);


    // ********** 送餐联 *****************
    // 送餐单标题
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    row = row;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '送餐单');

    // 订单日期号
    var orderformid = '日期:' + data['orderform'].custdate +
        ' 打印:' + data['orderform'].printnumber +
        ' 订单:' + data['orderform'].orderformid;
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 20;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

    // 打印条码 注释:北京要求去掉条形码
    //LODOP.SET_PRINT_STYLEA, (0, "FontSize", 10);
    //linespacing = 17;
    //row = row + 2;
    //LODOP.ADD_PRINT_BARCODE(linespacing * row, 0, 260, 40, '128Auto', data['orderform'].orderformid);

    // 地址
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    address = '地址:' + data['orderform'].address;
    address1 = address.subCHStr(0, 30);
    address2 = address.subCHStr(30, 30);
    address3 = address.subCHStr(60, 30);
    address4 = address.subCHStr(90, 30);
    address5 = address.subCHStr(120, 30);

    if (address1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
    }
    if (address2.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
    }
    if (address3.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
    }
    if (address4.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
    }
    if (address5.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address5);
    }

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 电话
    var telphone = '电话:' + data['orderform'].telphone;
    // 客户
    var clientname = '客户:' + data['orderform'].clientname;
    // 要餐时间
    var custtime = '要餐:' + data['orderform'].custtime;
    // 订餐时间
    var teltime = '录入:' + data['orderform'].rectime;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '    ' + clientname);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, custtime + '      ' + teltime);
    // 备注
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    var beizhu = '备注:' + data['orderform'].beizhu;
    beizhu1 = beizhu.subCHStr(0, 30);
    beizhu2 = beizhu.subCHStr(30, 30);
    beizhu3 = beizhu.subCHStr(60, 30);
    beizhu4 = beizhu.subCHStr(90, 30);
    beizhu5 = beizhu.subCHStr(120, 30);
    beizhu6 = beizhu.subCHStr(150, 30);
    if (beizhu1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
    }
    if (beizhu2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
    }
    if (beizhu3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
    }
    if (beizhu4.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
    }
    if (beizhu5.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu5);
    }
    if (beizhu6.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu6);
    }

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 商品打印
    var productsTitle = '名称          数量     单价    金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
    if (data['orderproducts']) {
        $.each(data['orderproducts'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
            // 产品数量
            LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
            // 单价
            LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
        })
    }
    // 送餐费金额
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    row = row + 1;
    var shippingmoney = '送餐费:' + data['orderform'].shippingmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);

    // 总金额
    LODOP.SET_PRINT_STYLE("FontSize", 8);
    linespacing = 19;
    var totalmoney = '总金额:' + data['orderform'].totalmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, totalmoney);
    //活动打印
    if (data['orderactivity']) {
        var activityTitle = '营销活动:    名称          金额';
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, activityTitle);
        $.each(data['orderactivity'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
        })
    }
    //支付打印
    if (data['orderpayment']) {
        var paymentTitle = '订单支付:     名称           金额';
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, paymentTitle);
        $.each(data['orderpayment'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
        })
    }
    // 客户还需付款金额
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    linespacing = 19;
    row = row + 1;
    var shouldmoney = '客户还需付款金额:' + data['orderform'].shouldmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shouldmoney);

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    // 发票抬头和发票内容
    if (data['orderform'].invoiceheader.length > 0) {
        var invoice = '发票:' + data['orderform'].invoiceheader + ' 内容:'; // +
                                                                        // data['orderform'].invoicebody;
        invoice1 = invoice.subCHStr(0, 30);
        invoice2 = invoice.subCHStr(30, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
        if (invoice2.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
        }
    }
    //发票的购买方纳税人识别号
    if (data['orderform'].gmf_nsrsbh.length > 0) {
        invoice = '识别号:' + data['orderform'].gmf_nsrsbh;
        invoice1 = invoice.subCHStr(0, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
    }

    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

    LODOP.PRINT();

    for (i = 0; i < 10000; i++) {

    }


    // 重新设置打印机的设备
    LODOP.PRINT_INIT("printorder");
    LODOP.SET_PRINTER_INDEX(print_index);
    // ********** 领餐联 *****************
    // 送餐单标题
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    row = 0;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '领餐单');

    //打印订单号
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

    // 地址
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    address = '地址:' + data['orderform'].address;
    address1 = address.subCHStr(0, 30);
    address2 = address.subCHStr(30, 30);
    address3 = address.subCHStr(60, 30);
    address4 = address.subCHStr(90, 30);
    address5 = address.subCHStr(120, 30);

    if (address1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
    }
    if (address2.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
    }
    if (address3.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
    }
    if (address4.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
    }
    if (address5.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address5);
    }

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 电话
    telphone = '电话:' + data['orderform'].telphone;
    // 客户
    clientname = '客户:' + data['orderform'].clientname;
    // 要餐时间
    custtime = '要餐时间:' + data['orderform'].custtime;
    // 订餐时间
    teltime = '下单时间:' + data['orderform'].rectime;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + custtime);
    //row = row + 1;
    //LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, clientname + '  ' + teltime);
    // 备注
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    beizhu = '备注:' + data['orderform'].beizhu;
    beizhu1 = beizhu.subCHStr(0, 30);
    beizhu2 = beizhu.subCHStr(30, 30);
    beizhu3 = beizhu.subCHStr(60, 30);
    beizhu4 = beizhu.subCHStr(90, 30);
    beizhu5 = beizhu.subCHStr(120, 30);
    beizhu6 = beizhu.subCHStr(150, 30);
    if (beizhu1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
    }
    if (beizhu2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
    }
    if (beizhu3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
    }
    if (beizhu4.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
    }
    if (beizhu5.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu5);
    }
    if (beizhu6.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu6);
    }

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 商品打印
    var productsTitle = '名称        数量    单价   金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
    if (data['orderproducts']) {
        $.each(data['orderproducts'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
            // 产品数量
            LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
            // 单价
            LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
        })
    }
    // 总金额
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    row = row + 1;
    totalmoney = '总金额:' + data['orderform'].totalmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);

    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

    LODOP.PRINT();

    for (i = 0; i < 10000; i++) {

    }

    // 重新设置打印机的设备
    LODOP.PRINT_INIT("printorder");
    LODOP.SET_PRINTER_INDEX(print_index);
    // ********** 调度联 *****************
    // 送餐单标题
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    row = 0;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '调度联');


    //打印订单号
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 20;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

    // 打印条码 :去掉条形码
    //LODOP.SET_PRINT_STYLEA, (0, "FontSize", 10);
    //linespacing = 17;
    //row = row + 1;
    //LODOP.ADD_PRINT_BARCODE(linespacing * row, 0, 260, 40, '128Auto', data['orderform'].orderformid);

    // 地址
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    //row = row + 1;
    address = '地址:' + data['orderform'].address;
    address1 = address.subCHStr(0, 32);
    address2 = address.subCHStr(32, 32);
    address3 = address.subCHStr(64, 32);
    address4 = address.subCHStr(96, 32);
    address5 = address.subCHStr(120, 30);

    if (address1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
    }
    if (address2.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
    }
    if (address3.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
    }
    if (address4.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
    }
    if (address5.length > 0) {
        row = row + 1;
        linespacing = 19;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address5);
    }


    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 电话
    telphone = '电话:' + data['orderform'].telphone;
    // 客户
    clientname = '客户:' + data['orderform'].clientname;
    // 要餐时间
    custtime = '要餐时间:' + data['orderform'].custtime;
    // 订餐时间
    teltime = '下单时间:' + data['orderform'].rectime;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + custtime);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, clientname + '  ' + teltime);
    // 备注
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 1);
    linespacing = 19;
    beizhu = '备注:' + data['orderform'].beizhu;
    beizhu1 = beizhu.subCHStr(0, 30);
    beizhu2 = beizhu.subCHStr(30, 30);
    beizhu3 = beizhu.subCHStr(60, 30);
    beizhu4 = beizhu.subCHStr(90, 30);
    beizhu5 = beizhu.subCHStr(120, 30);
    beizhu6 = beizhu.subCHStr(150, 30);
    if (beizhu1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
    }
    if (beizhu2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
    }
    if (beizhu3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
    }
    if (beizhu4.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
    }
    if (beizhu5.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu5);
    }
    if (beizhu6.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu6);
    }

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '宋体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    linespacing = 19;
    // 商品打印
    var productsTitle = '名称        数量    单价   金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
    if (data['orderproducts']) {
        $.each(data['orderproducts'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
            // 产品数量
            LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
            // 单价
            LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
        })
    }
    // 送餐费金额
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    row = row + 1;
    var shippingmoney = '送餐费:' + data['orderform'].shippingmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);
    // 总金额
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    totalmoney = '总金额:' + data['orderform'].totalmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, totalmoney);

    //活动打印
    /**
     if (data['orderactivity']) {
    var activityTitle = '营销活动:    名称          金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, activityTitle);
        $.each(data['orderactivity'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
        })
    }
     **/
    //支付打印
    if (data['orderpayment']) {
        var paymentTitle = '订单支付:     名称           金额';
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, paymentTitle);
        $.each(data['orderpayment'], function (key, value) {
            linespacing = 19;
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
        })
    }
    // 客户还需付款金额
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 19;
    row = row + 1;
    var shouldmoney = '客户还需付款金额:' + data['orderform'].shouldmoney;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shouldmoney);


    // 发票抬头和发票内容
    if (data['orderform'].invoiceheader.length > 0) {
        invoice = '发票抬头:' + data['orderform'].invoiceheader + ' 内容:'; // +
        // data['orderform'].invoicebody;
        invoice1 = invoice.subCHStr(0, 30);
        invoice2 = invoice.subCHStr(30, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
        if (invoice2.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
        }
    }
    //发票的购买方纳税人识别号
    if (data['orderform'].gmf_nsrsbh.length > 0) {
        invoice = '识别号:' + data['orderform'].gmf_nsrsbh;
        invoice1 = invoice.subCHStr(0, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
    }
    //购买方地址电话
    if (data['orderform'].gmf_dzdh.length > 0) {
        invoice = '发票地址电话:' + data['orderform'].gmf_dzdh;
        invoice1 = invoice.subCHStr(0, 30);
        invoice2 = invoice.subCHStr(30, 30);
        invoice3 = invoice.subCHStr(60, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
        if (invoice2.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
        }
        if (invoice3.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice3);
        }
    }
    //购买方银行账户
    if (data['orderform'].gmf_yhzh.length > 0) {
        invoice = '发票银行账号:' + data['orderform'].gmf_yhzh;
        invoice1 = invoice.subCHStr(0, 30);
        invoice2 = invoice.subCHStr(30, 30);
        if (invoice1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, invoice1);
        }
        if (invoice2.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, invoice2);
        }
    }


    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

    LODOP.PRINT();

    for (i = 0; i < 10000; i++) {

    }


    // ********** 客户联 *****************
    //因为常州客服提出的需求，所以加上 *********************************************
    if (accounttype === 2) {
        // 定义行号
        var linespacing = 14;
        var row = 0;  // 循环变量
        // 重新设置打印机的设备
        LODOP.PRINT_INIT("printorder");
        LODOP.SET_PRINTER_INDEX(print_index);
        // 送餐单标题
        LODOP.SET_PRINT_STYLE("FontSize", 12);
        row = row;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '客户联');

        // 订单日期号
        var orderformid = '日期:' + data['orderform'].custdate +
            ' 打印:' + data['orderform'].printnumber +
            ' 订单:' + data['orderform'].orderformid;
        LODOP.SET_PRINT_STYLE("FontSize", 10);
        linespacing = 20;
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

        // 地址
        LODOP.SET_PRINT_STYLE("FontSize", 12);
        LODOP.SET_PRINT_STYLE('FontName', '宋体');
        LODOP.SET_PRINT_STYLE('Bold', 1);
        linespacing = 19;
        address = '地址:' + data['orderform'].address;
        address1 = address.subCHStr(0, 30);
        address2 = address.subCHStr(30, 30);
        address3 = address.subCHStr(60, 30);
        address4 = address.subCHStr(90, 30);
        address5 = address.subCHStr(120, 30);

        if (address1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
        }
        if (address2.length > 0) {
            row = row + 1;
            linespacing = 19;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
        }
        if (address3.length > 0) {
            row = row + 1;
            linespacing = 19;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
        }
        if (address4.length > 0) {
            row = row + 1;
            linespacing = 19;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address4);
        }
        if (address5.length > 0) {
            row = row + 1;
            linespacing = 19;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address5);
        }

        LODOP.SET_PRINT_STYLE("FontSize", 10);
        LODOP.SET_PRINT_STYLE('FontName', '宋体');
        LODOP.SET_PRINT_STYLE('Bold', 0);
        linespacing = 19;
        // 电话
        var telphone = '电话:' + data['orderform'].telphone;
        // 客户
        var clientname = '客户:' + data['orderform'].clientname;
        // 要餐时间
        var custtime = '要餐:' + data['orderform'].custtime;
        // 订餐时间
        var teltime = '录入:' + data['orderform'].rectime;
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '    ' + clientname);
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, custtime + '      ' + teltime);
        // 备注
        LODOP.SET_PRINT_STYLE("FontSize", 10);
        LODOP.SET_PRINT_STYLE('FontName', '宋体');
        LODOP.SET_PRINT_STYLE('Bold', 1);
        linespacing = 19;
        var beizhu = '备注:' + data['orderform'].beizhu;
        beizhu1 = beizhu.subCHStr(0, 30);
        beizhu2 = beizhu.subCHStr(30, 30);
        beizhu3 = beizhu.subCHStr(60, 30);
        beizhu4 = beizhu.subCHStr(90, 30);
        beizhu5 = beizhu.subCHStr(120, 30);
        beizhu6 = beizhu.subCHStr(150, 30);
        if (beizhu1.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
        }
        if (beizhu2.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu2);
        }
        if (beizhu3.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu3);
        }
        if (beizhu4.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu4);
        }
        if (beizhu5.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu5);
        }
        if (beizhu6.length > 0) {
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, beizhu6);
        }

        LODOP.SET_PRINT_STYLE("FontSize", 10);
        LODOP.SET_PRINT_STYLE('FontName', '宋体');
        LODOP.SET_PRINT_STYLE('Bold', 0);
        linespacing = 19;
        // 商品打印
        var productsTitle = '名称          数量     单价    金额';
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
        if (data['orderproducts']) {
            $.each(data['orderproducts'], function (key, value) {
                linespacing = 19;
                row = row + 1;
                // 产品名称
                LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
                // 产品数量
                LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, value.number);
                // 单价
                LODOP.ADD_PRINT_TEXT(linespacing * row, 150, 644, 62, value.price);
                // 金额
                LODOP.ADD_PRINT_TEXT(linespacing * row, 210, 644, 62, value.money);
            })
        }
        // 送餐费金额
        LODOP.SET_PRINT_STYLE("FontSize", 10);
        linespacing = 19;
        row = row + 1;
        var shippingmoney = '送餐费:' + data['orderform'].shippingmoney;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shippingmoney);

        // 总金额
        LODOP.SET_PRINT_STYLE("FontSize", 8);
        linespacing = 19;
        var totalmoney = '总金额:' + data['orderform'].totalmoney;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 110, 644, 62, totalmoney);
        //活动打印
        if (data['orderactivity']) {
            var activityTitle = '营销活动:    名称          金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, activityTitle);
            $.each(data['orderactivity'], function (key, value) {
                linespacing = 19;
                row = row + 1;
                // 产品名称
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                // 金额
                LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
            })
        }
        //支付打印
        if (data['orderpayment']) {
            var paymentTitle = '订单支付:     名称           金额';
            row = row + 1;
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, paymentTitle);
            $.each(data['orderpayment'], function (key, value) {
                linespacing = 19;
                row = row + 1;
                // 产品名称
                LODOP.ADD_PRINT_TEXT(linespacing * row, 20, 644, 62, value.name);
                // 金额
                LODOP.ADD_PRINT_TEXT(linespacing * row, 185, 644, 62, value.money);
            })
        }
        // 客户还需付款金额
        LODOP.SET_PRINT_STYLE("FontSize", 12);
        linespacing = 19;
        row = row + 1;
        var shouldmoney = '客户还需付款金额:' + data['orderform'].shouldmoney;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, shouldmoney);

        LODOP.SET_PRINT_STYLE("FontSize", 10);

        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '.');

        LODOP.PRINT();

        for (var i = 0; i < 10000; i++) {

        }
    }


}

// 60宽热敏的打印代码
function print_60(data) {
    if (localStorage['printIndex'] >= 0) {
        var print_index = localStorage.printIndex;
    } else {
        alert('请设置打印机');
    }
    // 定义行号
    var linespacing = 14;
    var row = 0;  // 循环变量
    // 重新设置打印机的设备
    LODOP.SET_PRINTER_INDEX(print_index);
    LODOP.SET_PRINT_PAGESIZE(3, "58mm", "10mm", 'ORDER');
    LODOP.PRINT_INIT("printorder");
    // 送餐单标题
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 20);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 40, 644, 62, '送餐单');

    // 订单号
    var orderformid = data['orderform'].recdate + '-' + data['orderform'].printnumber;
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);
    // 地址
    address = '地址:' + data['orderform'].address;
    address1 = address.subCHStr(0, 30);
    address2 = address.subCHStr(30, 30);
    address3 = address.subCHStr(60, 30);


    if (address1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
    }
    if (address2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
    }
    if (address3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
    }


    LODOP.SET_PRINT_STYLEA(0, "FontSize", 10);
    // 电话
    telphone = '电话:' + data['orderform'].telphone;
    // 客户
    clientname = '客户:' + data['orderform'].clientname;
    // 要餐时间
    custtime = '要餐时间:' + data['orderform'].custtime;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + clientname);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, custtime);
    // 备注
    beizhu = '备注:' + data['orderform'].beizhu;
    beizhu1 = beizhu.subCHStr(0, 30);
    beizhu2 = beizhu.subCHStr(30, 30);
    beizhu3 = beizhu.subCHStr(60, 30);
    if (beizhu1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
    }
    if (beizhu2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu2);
    }
    if (beizhu3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu3);
    }
    // 商品打印
    productsTitle = '名称      数量   单价    金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
    if (data['orderproducts']) {
        $.each(data['orderproducts'], function (key, value) {
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
            // 产品数量
            LODOP.ADD_PRINT_TEXT(linespacing * row, 70, 644, 62, value.number);
            // 单价
            LODOP.ADD_PRINT_TEXT(linespacing * row, 100, 644, 62, value.price);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 145, 644, 62, value.money);
        })
    }
    // 总金额
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 10);
    totalmoney = '总金额:' + data['orderform'].totalmoney;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);

    // 空白
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '');

    // 空白
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '');
    // 空白
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, '');

    // 送餐单标题
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 20);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 40, 644, 62, '领餐单');

    // 订单号
    var orderformid = data['orderform'].recdate + '-' + data['orderform'].printnumber;
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);
    // 地址
    address = '地址:' + data['orderform'].address;
    address1 = address.subCHStr(0, 30);
    address2 = address.subCHStr(30, 30);
    address3 = address.subCHStr(60, 30);

    // alert(cutstr("新加文本1桑德菲杰来撒机房的阿萨德房间爱塑料袋件发生的房间里圣诞节来看阿萨德乐尽哀生",26))
    if (address1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address1);
    }
    if (address2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address2);
    }
    if (address3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, address3);
    }


    LODOP.SET_PRINT_STYLEA(0, "FontSize", 10);
    // 电话
    telphone = '电话:' + data['orderform'].telphone;
    // 客户
    clientname = '客户:' + data['orderform'].clientname;
    // 要餐时间
    custtime = '要餐时间:' + data['orderform'].custtime;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, telphone + '  ' + clientname);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, custtime);
    // 备注
    beizhu = '备注:' + data['orderform'].beizhu;
    beizhu1 = beizhu.subCHStr(0, 30);
    beizhu2 = beizhu.subCHStr(30, 30);
    beizhu3 = beizhu.subCHStr(60, 30);
    if (beizhu1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu1);
    }
    if (beizhu2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu2);
    }
    if (beizhu3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, beizhu3);
    }
    // 商品打印
    productsTitle = '名称      数量   单价    金额';
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, productsTitle);
    if (data['orderproducts']) {
        $.each(data['orderproducts'], function (key, value) {
            row = row + 1;
            // 产品名称
            LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, value.shortname);
            // 产品数量
            LODOP.ADD_PRINT_TEXT(linespacing * row, 70, 644, 62, value.number);
            // 单价
            LODOP.ADD_PRINT_TEXT(linespacing * row, 100, 644, 62, value.price);
            // 金额
            LODOP.ADD_PRINT_TEXT(linespacing * row, 145, 644, 62, value.money);
        })
    }
    // 总金额
    totalmoney = '总金额:' + data['orderform'].totalmoney;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, totalmoney);


    // LODOP.PREVIEW();
    LODOP.PRINT();

    // 设定订单状态为已打印
    $.ajax({
        type: "GET",
        url: APP + "/OrderHandle/setOrderPrinted/orderformid/" + data['orderform'].orderformid,
        dataType: "json",
        success: function (data) {

        }
    })


}

// 横式针打订单
function print_ht(data) {
    var print_index = $.cookie('rmsPrinterIndex'); // 读取 cookie,打印机类型
    if (print_index < 0) {
        alert('请设置打印机');
        return;
    }
    // 重新设置打印机的设备
    LODOP.SET_PRINTER_INDEX(print_index);
    LODOP.PRINT_INITA(0, 0, 1522, 382, "打印控件功能_Lodop功能");
    LODOP.SET_PRINT_PAGESIZE(0, 0, 0, "lihua");  //(1, 2400, 700, "lihua");
    // 订单号
    orderformid = data['orderform'].recdate + '-' + data['orderform'].orderformid;
    LODOP.ADD_PRINT_TEXT(6, 29, 187, 24, orderformid);
    // 地址
    address = data['orderform'].address;
    address1 = address.subCHStr(0, 22);
    // 第一格
    LODOP.ADD_PRINT_TEXT(35, 27, 301, 40, address);
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 12);
    // 第二格
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 11);
    LODOP.ADD_PRINT_TEXT(34, 390, 182, 36, address1);
    // 第三格
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 11);
    LODOP.ADD_PRINT_TEXT(35, 612, 206, 37, address);


    // 数量规格
    ordertxt = data['orderform'].ordertxt;
    LODOP.ADD_PRINT_TEXT(79, 393, 180, 48, ordertxt);
    LODOP.SET_PRINT_STYLEA(0, "FontSize", 10);
    LODOP.ADD_PRINT_TEXT(77, 611, 213, 30, ordertxt);
    // 要餐时间
    custtime = data['orderform'].custtime;
    LODOP.ADD_PRINT_TEXT(113, 56, 121, 19, custtime);
    // 来电时间
    teltime = data['orderform'].rectime;
    LODOP.ADD_PRINT_TEXT(113, 210, 113, 19, teltime);
    // 电话号码
    telphone = data['orderform'].telphone;
    LODOP.ADD_PRINT_TEXT(137, 54, 125, 22, telphone);
    // 接线员
    telname = data['orderform'].telname;
    LODOP.ADD_PRINT_TEXT(136, 210, 115, 22, telname);
    // 总金额
    totalmoney = data['orderform'].totalmoney;
    // 备注
    beizhu = data['orderform'].beizhu;
    // 加入金额
    beizhu = '共' + totalmoney + ' ' + beizhu;
    LODOP.ADD_PRINT_TEXT(136, 393, 182, 74, beizhu);
    LODOP.ADD_PRINT_TEXT(119, 613, 219, 79, beizhu);
    LODOP.ADD_PRINT_TEXT(165, 34, 296, 37, beizhu);

    LODOP.ADD_PRINT_TEXT(81, 55, 271, 28, ordertxt);

    // 打印
    LODOP.PRINT();

    // 设定订单状态为已打印
    $.ajax({
        type: "GET",
        url: APP + "/OrderHandle/setOrderPrinted/orderformid/" + data['orderform'].orderformid,
        dataType: "json",
        success: function (data) {

        }
    })
}


/**
 *  使用POS打印出电子票
 */
function printEticketInvoice(data) {
    var print_index = $.cookie('rmsPrinterIndex'); // 读取 cookie,打印机类型
    if (print_index < 0) {
        alert('请设置打印机');
        return false;
    }
    // 定义行号
    var linespacing = 14;
    var row = 1;  // 循环变量
    // 重新设置打印机的设备

    LODOP.PRINT_INIT("printorder");
    LODOP.SET_PRINTER_INDEX(print_index);


    // ********** 发票联 *****************
    // 发票标题
    LODOP.SET_PRINT_STYLE("FontSize", 12);
    row = row;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 60, 644, 62, '电子发票提取码');

    // 日期;时间,分公司名称
    var orderformid = '日期:' + data['date'];
    ' 打印:' + data['printman'] +
    ' 分公司:' + data['company'];

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    linespacing = 16;
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

    orderformid = '打印:' + data['printman'] +
        ' 分公司:' + data['company'];
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, orderformid);

    //提示:
    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '仿宋');
    var fapiaointro = "* 根据相关税法规定,电子发票的开票请务必在消费当日登录网上申请电子发票,此提取码  30天有效,扫码请保持票面平整.";

    var fapiaotmp_1 = fapiaointro.subCHStr(0, 40);
    var fapiaotmp_2 = fapiaointro.subCHStr(40, 80);
    var fapiaotmp_3 = fapiaointro.subCHStr(80, 120);
    var fapiaotmp_4 = fapiaointro.subCHStr(120, 160);

    if (fapiaotmp_1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
    }
    if (fapiaotmp_2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
    }
    if (fapiaotmp_3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
    }
    if (fapiaotmp_4.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_4);
    }


    var fapiaointro = "(1)请登录网站:http://invoice.lihua.com ;根据步骤开具增值税普通发票.";
    fapiaotmp_1 = fapiaointro.subCHStr(0, 40);
    fapiaotmp_2 = fapiaointro.subCHStr(40, 80);
    fapiaotmp_3 = fapiaointro.subCHStr(120, 160);
    if (fapiaotmp_1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
    }
    if (fapiaotmp_2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
    }
    if (fapiaotmp_3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
    }

    //LODOP.SET_PRINT_STYLE("FontSize", 10);
    //LODOP.SET_PRINT_STYLE('FontName', '黑体');
    //LODOP.SET_PRINT_STYLE('Bold', 0);
    //fapiaointro = "--------------------------";
    //row = row + 1;
    //LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '黑体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    fapiaointro = "提取码:";
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

    LODOP.SET_PRINT_STYLE("FontSize", 17);
    LODOP.SET_PRINT_STYLE('FontName', '黑体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    fapiaointro = data['eticketno'];
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '黑体');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    fapiaointro = "--------------------------";
    row = row + 1;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaointro);

    LODOP.SET_PRINT_STYLE("FontSize", 10);
    LODOP.SET_PRINT_STYLE('FontName', '仿宋');
    LODOP.SET_PRINT_STYLE('Bold', 0);
    fapiaointro = "(2)也可以扫描下方二维码,根据步骤开具您的电子普通增值税发票.";
    fapiaotmp_1 = fapiaointro.subCHStr(0, 30);
    var fapiaotmp_2 = fapiaointro.subCHStr(30, 30);
    var fapiaotmp_3 = fapiaointro.subCHStr(60, 30);
    if (fapiaotmp_1.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_1);
    }
    if (fapiaotmp_2.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_2);
    }
    if (fapiaotmp_3.length > 0) {
        row = row + 1;
        LODOP.ADD_PRINT_TEXT(linespacing * row, 0, 644, 62, fapiaotmp_3);
    }

    //打印二维码
    row = row + 1;
    LODOP.ADD_PRINT_BARCODE(linespacing * row, 40, 644, 146, 'QRCode', 'http://invoice.lihua.com?n=' + data['eticketno']);


    row = row + 6;
    LODOP.ADD_PRINT_TEXT(linespacing * row, 10, 644, 62, '.');


    LODOP.PRINT();

    for (i = 0; i < 10000; i++) {

    }

    // 设定订单状态为已打印
    $.ajax({
        type: "GET",
        url: APP + "/InvoiceMgr/setInvoiceOpened/invoiceid/" + data['invoiceid'],
        dataType: "json",
        success: function (data) {
        }
    });

    return true;

}


// 计算字符串长度
String.prototype.strLen = function () {
    var len = 0;
    for (var i = 0; i < this.length; i++) {
        if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0) len += 2; else len++;
    }
    return len;
}

// 将字符串拆成字符，并存到数组中
String.prototype.strToChars = function () {
    var chars = new Array();
    for (var i = 0; i < this.length; i++) {
        chars[i] = [this.substr(i, 1), this.isCHS(i)];
    }
    String.prototype.charsArray = chars;
    return chars;
}

// 判断某个字符是否是汉字
String.prototype.isCHS = function (i) {
    if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0)
        return true;
    else
        return false;
}

// 截取字符串（从start字节到end字节）
String.prototype.subCHString = function (start, end) {
    var len = 0;
    var str = "";
    this.strToChars();
    for (var i = 0; i < this.length; i++) {
        if (this.charsArray[i][1])
            len += 2;
        else
            len++;
        if (end < len)
            return str;
        else if (start < len)
            str += this.charsArray[i][0];
    }
    return str;
}

// 截取字符串（从start字节截取length个字节）
String.prototype.subCHStr = function (start, length) {
    return this.subCHString(start, start + length);
}

//定义title 和 模块名称的对应
function title_module(title) {
    switch (title) {
        case '公告':
            return 'Notice';
            break;
        case '群发消息':
            return 'Messages';
            break;
        case '来电客户管理':
            return 'Telcustomer'
            break;
        case '产品':
            return 'Products';
            break;
        case '今日菜单':
            return 'TodayMenu';
            break;
        case '订餐单':
            return 'OrderForm';
            break;
        case '订餐地址查询':
            return 'OrderFormSearchviewAddress';
            break;
        case '订餐电话查询':
            return 'OrderFormSearchviewTelphone';
            break;
        case '订餐综合查询':
            return 'OrderFormSearchviewOther';
            break;
        case '订单配送':
            return 'OrderHandle';
            break;
        case '配送地址查询':
            return 'OrderHandleSearchviewAddress';
            break;
        case '配送送餐员查询':
            return 'OrderHandleSearchviewSendname';
            break;
        case '配送综合查询':
            return 'OrderHandleSearchviewOther';
            break;
        case '订单预订':
            return 'BookOrder';
            break;
        case '订单分配':
            return 'OrderDistribution';
            break;
        case '分配地址查询':
            return 'OrderDistributionSearchviewAddress';
            break;
        case '分配配送店查询':
            return 'OrderDistributionSearchviewCompany';
            break;
        case '分配综合查询':
            return 'OrderDistributionSearchviewOther';
            break;
        case '历史订单':
            return 'OrderHistory';
            break;
        case '打印派单':
            return 'OrderPrintHandle';
            break;
        case '分送点配送':
            return 'OrderSecondPoint';
            break;
        case '装箱单':
            return 'ZhuangxiangMgr';
            break;
        case '送餐员管理':
            return 'SendnameMgr';
            break;
        case '配送店管理':
            return 'CompanyMgr';
            break;
        case '短信管理':
            return 'SmsMgr';
            break;
        case '分送点管理':
            return 'SecondPointMgr';
            break;
        case '发票管理':
            return 'InvoiceMgr';
            break;
        case '备注管理':
            return 'BeizhuOrderMgr';
            break;
    }
}


//初始化快捷键
function initializeKeyboard() {
    Mousetrap.bind(['ctrl+0', 'ctrl+f10', 'f10'], function (e) {
    });
    Mousetrap.bind(['ctrl+1', 'ctrl+f1', 'f1'], function (e) {
    });
    //Mousetrap.bind(['ctrl+2','ctrl+f2','f2'], function(e) {});
    Mousetrap.bind(['ctrl+3', 'ctrl+f3', 'f3'], function (e) {
    });
    Mousetrap.bind(['ctrl+4', 'ctrl+f4', 'f4'], function (e) {
    });
    Mousetrap.bind(['ctrl+5', 'ctrl+f5', 'f5'], function (e) {
    });
    Mousetrap.bind(['ctrl+6', 'ctrl+f6', 'f6'], function (e) {
    });
    Mousetrap.bind(['ctrl+7', 'ctrl+f7', 'f7'], function (e) {
    });
    Mousetrap.bind(['ctrl+8', 'ctrl+f8', 'f8'], function (e) {
    });
    Mousetrap.bind(['ctrl+9', 'ctrl+f9', 'f9'], function (e) {
    });
    Mousetrap.bind('esc', function (e) {
    });
}






