<?php
    class OrderHistoryModel extends CRMEntityModel{
       protected $tableName = 'orderform';    
          //指定焦点字段
        var $fieldsFocus = 'address';
        //定义列表
        var $listFields = array(
            'address'=>array('width' => 50,'align'=>'left','halign'=>'center'),
            'ordertxt'=>array('width' => 20,'align'=>'left' ),
            'telphone'=>array('width' => 10,'align'=>'left'),
            'totalmoney'=>array('width' => 10,'align'=>'center'),
            'custdate' =>array('width' => 15,'align'=>'center'),
            'custtime'=>array('width' => 10,'align'=>'center'),
            'sendname' => array('width' => 10,'align'=>'center'),
            'company' => array('width' => 10,'align'=>'center'),
            'state' => array('width' => 10,'align'=>'center'),
            'telname' => array('width' => 10,'align'=>'left'),
            'rectime' => array('width' => 10,'align'=>'center'));
        //定义查询字段
        var $searchFields = array('address','telphone','sendname','company','ordersn');
        //定义选择产品编码的字段
        var $popupProductsFields = array('code','name','price','brief');
        //定义焦点产品选择的字段
        var $popupProductsLinkField = 'name';

        //新建的字段表
        var $createFields = array(
               'LBL_ORDERFORM_INFORMATION' => array(
                array(
                    'name'=>'clientname','uitype' => 2,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphone','uitype'=>21,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'address','uitype'=> 22,'readonly'=>1,'length'=>100
                ),
                array(
                    'name'=>'custtime','uitype'=>1,'readonly'=>1,'length'=>24
                ),
                 array(
                    'name'=>'beizhu','uitype'=> 11,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'company','uitype'=> 9,'readonly'=>1,'length'=>24
                ),            
                array(
                    'name'=>'totalmoney','uitype'=> 4,'readonly'=>0,'length'=>24
                ),
                
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(  //发票抬头
                        'name'=>'invoiceheader','uitype' => 2,'readonly' => 1,'length' => 34
                    ),
                    array(  //发票内容
                        'name'=>'invoicecontent','uitype' => 9,'readonly' => 1,'length' => 14
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
                    array(
                         'name'=>'shippingname','uitype' => 30,'readonly' => 1,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             )
             
            
        );

    
            
    //改单的字段
    var $editFields =   array(
               'LBL_ORDERFORM_INFORMATION' => array(
                array(
                    'name'=>'clientname','uitype' => 2,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphone','uitype'=>21,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'address','uitype'=> 22,'readonley'=>1,'length'=>100
                ),
                array(
                    'name'=>'custtime','uitype'=>1,'readonley'=>1,'length'=>24
                ),
                
                array(
                    'name'=>'beizhu','uitype'=> 11,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'company','uitype'=> 9,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'totalmoney','uitype'=> 4,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'telname','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'rectime','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'ap','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'state','uitype'=> 2,'readonley'=>1,'length'=>24
                )
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonley'=>1,'length'=>24
                )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(  //发票抬头
                        'name'=>'invoiceheader','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
                    array(  //发票内容
                        'name'=>'invoicecontent','uitype' => 2,'readonly' => 1,'length' => 24
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
                    array(
                         'name'=>'shippingname','uitype' => 2,'readonly' => 0,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERPROCESS_INFORMATION' => array(  //订单流程
                     array(
                         'name'=>'shippingname','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonley'=>1,'length'=>24
                )
             ),
        );

        
    var $detailFields = array(
               'LBL_ORDERFORM_INFORMATION' => array(
                array(
                    'name'=>'clientname','uitype' => 2,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphone','uitype'=>21,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'address','uitype'=> 22,'readonley'=>1,'length'=>100
                ),
                array(
                    'name'=>'custtime','uitype'=>1,'readonley'=>1,'length'=>24
                ),
                
                array(
                    'name'=>'beizhu','uitype'=> 11,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'company','uitype'=> 9,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'totalmoney','uitype'=> 4,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'telname','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'rectime','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'ap','uitype'=> 1,'readonley'=>1,'length'=>24
                ),
                array(
                    'name'=>'state','uitype'=> 2,'readonley'=>1,'length'=>24
                )
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonley'=>1,'length'=>24
                )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(  //发票抬头
                        'name'=>'invoiceheader','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
                    array(  //发票内容
                        'name'=>'invoicecontent','uitype' => 2,'readonly' => 1,'length' => 24
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(   //送餐方式
                    array(
                         'name'=>'shippingname','uitype' => 2,'readonly' => 0,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERPROCESS_INFORMATION' => array(  //订单流程
                     array(
                         'name'=>'shippingname','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonley'=>1,'length'=>24
                )
             ),
        );

        
    // 回调方法 ，初始化
    protected function _initialize() {
    }
    
    //返回ID
    public function getPk(){
            return 'ordersn';
    }
    }
?>
