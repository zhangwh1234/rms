/**
 * Created by zhangwh1234 on 2016-04-28.
 * 北京发票的操作代码
 */


(function(window, document, undefined) {

    var InvoiceOper = {

        openInvoice : function () {
            alert('I like invoice!');
        }

    }

    // expose to the global object
    window.InvoiceOper = InvoiceOper;

}) (window, document);

