<?php

class OrderFormModel extends CRMEntityModel
{
    protected $tableName = 'orderform';
    //指定焦点字段
    var $fieldsFocus = 'address';
    //定义列表
    var $listFields = array(
        'address' => array('width' => 50, 'align' => 'left', 'halign' => 'center'),
        'ordertxt' => array('width' => 20, 'align' => 'left'),
        'telphone' => array('width' => 10, 'align' => 'left'),
        'totalmoney' => array('width' => 10, 'align' => 'center'),
        'custtime' => array('width' => 10, 'align' => 'center'),
        'sendname' => array('width' => 12, 'align' => 'center'),
        'company' => array('width' => 10, 'align' => 'center'),
        'state' => array('width' => 10, 'align' => 'center'),
        'telname' => array('width' => 10, 'align' => 'left'),
        'rectime' => array('width' => 10, 'align' => 'center'));
    //定义列表
    var $searchListFields = array(
        'address' => array('width' => 80, 'align' => 'left', 'halign' => 'center'),
        'ordertxt' => array('width' => 25, 'align' => 'left'),
        'telphone' => array('width' => 15, 'align' => 'left'),
        'totalmoney' => array('width' => 10, 'align' => 'center'),
        'custtime' => array('width' => 10, 'align' => 'center'),
        'sendname' => array('width' => 10, 'align' => 'center'),
        'company' => array('width' => 10, 'align' => 'center'),
        'state' => array('width' => 10, 'align' => 'center'),
        'telname' => array('width' => 10, 'align' => 'left'),
        'ap' => array('width' => 10, 'align' => 'left'),
    );

    //定义来电列表
    var $telphonecomeListFields = array(
        'teldate' => array('width' => 10, 'align' => 'center'),
        'telphone' => array('width' => 10, 'align' => 'center'),
        'teltask' => array('width' => 10, 'align' => 'center'),
        'telname' => array('width' => 6, 'align' => 'center')
    );

    //定义查询字段
    var $searchFields = array('address', 'telphone', 'sendname', 'company', 'state','ordersn');
    //定义列表快捷字段
    var $listLinkField = 'address';
    //定义选择产品编码的字段
    var $popupProductsFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
        'shortname' => array('width' => 20, 'align' => 'right'),
        'price' => array('width' => 20, 'align' => 'left'),
        'brief' => array('width' => 20, 'align' => 'left'));

    //定义焦点产品选择的字段
    var $popupProductsLinkField = 'name';

    //新建的字段表
    var $createFields = array(
        'LBL_ORDERFORM_INFORMATION' => array(
            array(
                'name' => 'clientname', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'telphone', 'uitype' => 21, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'address', 'uitype' => 22, 'readonly' => 1, 'length' => 80
            ),
            array(
                'name' => 'custtime', 'uitype' => 23, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'beizhu', 'uitype' => 1, 'readonly' => 1, 'length' => 80
            ),
            array(
                'name' => 'company', 'uitype' => 9, 'readonly' => 1, 'length' => 100
            ),
            array(
                'name' => 'totalmoney', 'uitype' => 4, 'readonly' => 0, 'length' => 24
            ),
            array(
                'name' => 'todaymenu', 'uitype' => 62, 'readonly' => 0, 'length' => 24
            ),

        ),
        'LBL_PRODUCTS_INFORMATION' => array(
            array(
                'name' => 'goods', 'uitype' => 51, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_INVOICE_INFORMATION' => array(
            array(  //发票抬头
                'name' => 'invoiceheader', 'uitype' => 57, 'readonly' => 1, 'length' => 280
            ),
            array(  //发票内容
                'name' => 'invoicebody', 'uitype' => 9, 'readonly' => 1, 'length' => 100
            )
        ),
        'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
            array(
                'name' => 'shippingname', 'uitype' => 30, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'shippingmoney', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
        )


    );


    //改单的字段
    var $editFields = array(
        'LBL_ORDERFORM_INFORMATION' => array(
            array(
                'name' => 'clientname', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'telphone', 'uitype' => 21, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'address', 'uitype' => 22, 'readonly' => 1, 'length' => 80
            ),
            array(
                'name' => 'custtime', 'uitype' => 23, 'readonly' => 1, 'length' => 24
            ),

            array(
                'name' => 'beizhu', 'uitype' => 1, 'readonly' => 1, 'length' => 80
            ),
            array(
                'name' => 'company', 'uitype' => 9, 'readonly' => 1, 'length' => 100
            ),
            array(
                'name' => 'totalmoney', 'uitype' => 4, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'telname', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'rectime', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'ap', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'state', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_PRODUCTS_INFORMATION' => array(
            array(
                'name' => 'goods', 'uitype' => 51, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_INVOICE_INFORMATION' => array(
            array(  //发票抬头
                'name' => 'invoiceheader', 'uitype' => 2, 'readonly' => 1, 'length' => 250
            ),
            array(  //发票内容
                'name' => 'invoicebody', 'uitype' => 9, 'readonly' => 1, 'length' => 100
            )
        ),
        'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
            array(
                'name' => 'shippingname', 'uitype' => 1, 'readonly' => 0, 'length' => 24
            ),
            array(
                'name' => 'shippingmoney', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
        ),
        'LBL_ORDERPROCESS_INFORMATION' => array(  //订单流程
            array(
                'name' => 'shippingname', 'uitype' => 58, 'readonly' => 1, 'length' => 24
            ),
        ),
        'LBL_ORDERACTION_INFORMATION' => array(
            array(
                'name' => 'orderaction', 'uitype' => 52, 'readonly' => 1, 'length' => 24
            )
        ),
    );


    var $detailFields = array(
        'LBL_ORDERFORM_INFORMATION' => array(
            array(
                'name' => 'clientname', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'telphone', 'uitype' => 21, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'address', 'uitype' => 22, 'readonly' => 1, 'length' => 100
            ),
            array(
                'name' => 'custtime', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),

            array(
                'name' => 'beizhu', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'company', 'uitype' => 9, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'totalmoney', 'uitype' => 4, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'shouldmoney', 'uitype' => 4, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'paidmoney', 'uitype' => 4, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'goodsmoney', 'uitype' => 4, 'readonly' => 1, 'length' => 24
            ),

            array(
                'name' => 'telname', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'rectime', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'ap', 'uitype' => 1, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'state', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_PRODUCTS_INFORMATION' => array(
            array(
                'name' => 'goods', 'uitype' => 51, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_INVOICE_INFORMATION' => array(
            array(  //发票抬头
                'name' => 'invoiceheader', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
            array(  //发票内容
                'name' => 'invoicecontent', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            )
        ),
        'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
            array(
                'name' => 'shippingname', 'uitype' => 1, 'readonly' => 0, 'length' => 24
            ),
            array(
                'name' => 'shippingmoney', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
        ),
        'LBL_ORDERPROCESS_INFORMATION' => array(  //订单流程
            array(
                'name' => 'orderstate', 'uitype' => 58, 'readonly' => 1, 'length' => 24
            ),
        ),
    );

    var $returnFields = array(
        'LBL_RETURNORDERFORM_INFORMATION' => array(
            array(
                'name' => 'cancelcontent', 'uitype' => 1, 'readonly' => 1, 'length' => 100
            )
        ),
    );


    // 回调方法 ，初始化
    protected function _initialize()
    {
    }

    //返回ID
    public function getPk()
    {
        return 'orderformid';
    }


}

?>
