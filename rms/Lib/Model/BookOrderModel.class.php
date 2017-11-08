<?php
    /**
    * 订单预订
    */

    class BookOrderModel extends CRMEntityModel{
        protected $tableName = 'bookorder';
          //焦点字段
        var $fieldsFocus = 'address';
        //列表字段
        var $listFields = array(
            'address'=>array('width' => 45,'align'=>'left','halign'=>'center'),
            'ordertxt'=>array('width' => 20,'align'=>'left' ),
            'telphone'=>array('width' => 10,'align'=>'left'),
            'totalmoney'=>array('width' => 10,'align'=>'center'),
            'bookdate' => array('width' => 13,'align'=>'center'),
            'custtime'=>array('width' => 8,'align'=>'center'),
            'state' => array('width' => 7,'align'=>'center'),
            'telname' => array('width' => 10,'align'=>'left'),
            'recdate' => array('width' => 13,'align'=>'center'),
            );

        //定义查询字段
        var $searchFields = array('address','telphone','datetxt','state');
        //定义列表快捷字段
        var $listLinkField = 'address';
        //定义选择产品编码的字段
        var $popupProductsFields = array('code','name','shortname','price','brief');
        //定义焦点产品选择的字段
        var $popupProductsLinkField = 'name';

        //新建的字段表
        var $createFields = array(
               'LBL_ORDERFORM_INFORMATION' => array(
                array(
                    'name'=>'clientname','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphone','uitype'=>21,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'address','uitype'=> 22,'readonly'=>1,'length'=>80
                ),
                array(
                    'name'=>'custtime','uitype'=>23,'readonly'=>1,'length'=>24
                ),
                 array(
                    'name'=>'beizhu','uitype'=> 1,'readonly'=>1,'length'=>80
                ),            
                array(
                    'name'=>'totalmoney','uitype'=> 4,'readonly'=>0,'length'=>24
                ),
                
             ),
             'LBL_BOOKDATE_INFORMATION' => array(
                    array(
                       'name'=>'bookdate','uitype'=> 61,'readonly'=>1,'length'=>24   
                    )
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                        'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                    )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(
                        'name'=>'invoiceheader','uitype' => 57,'readonly' => 1,'length' => 280
                    ),
                    array(
                        'name'=>'invoicecontent','uitype' => 9,'readonly' => 1,'length' => 100
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(
                    array(
                         'name'=>'shippingname','uitype' => 30,'readonly' => 1,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
             )
             
            
        );

    
            
    //改单的字段
    var $editFields =   array(
               'LBL_ORDERFORM_INFORMATION' => array(
                array(
                    'name'=>'clientname','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphone','uitype'=>21,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'address','uitype'=> 22,'readonly'=>1,'length'=>80
                ),
                array(
                    'name'=>'custtime','uitype'=>1,'readonly'=>1,'length'=>24
                ),
                
                array(
                    'name'=>'beizhu','uitype'=> 1,'readonly'=>1,'length'=>80
                ),
                array(
                    'name'=>'company','uitype'=> 9,'readonly'=>1,'length'=>100
                ),
                array(
                    'name'=>'telname','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'rectime','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'ap','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'state','uitype'=> 2,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_BOOKDATE_INFORMATION' => array(
                    array(
                       'name'=>'bookdate','uitype'=> 61,'readonly'=>1,'length'=>24   
                    )
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(
                        'name'=>'invoiceheader','uitype' => 57,'readonly' => 1,'length' => 250
                    ),
                    array(
                        'name'=>'invoicecontent','uitype' => 9,'readonly' => 1,'length' => 100
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(
                    array(
                         'name'=>'shippingname','uitype' => 1,'readonly' => 0,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERPROCESS_INFORMATION' => array(
                     array(
                         'name'=>'shippingname','uitype' => 58,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonly'=>1,'length'=>24
                )
             ),
        );

        
    var $detailFields = array(
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
                    'name'=>'totalmoney','uitype'=> 4,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'telname','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'rectime','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'ap','uitype'=> 1,'readonly'=>1,'length'=>24
                ),
                array(
                    'name'=>'state','uitype'=> 2,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_BOOKDATE_INFORMATION' => array(
                    array(
                       'name'=>'bookdate','uitype'=> 61,'readonly'=>1,'length'=>24   
                    )
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_INVOICE_INFORMATION' => array(
                    array(
                        'name'=>'invoiceheader','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'invoicecontent','uitype' => 2,'readonly' => 1,'length' => 24
                    )                   
             ), 
             'LBL_SHIPPING_INFORMATION' => array(
                    array(
                         'name'=>'shippingname','uitype' => 1,'readonly' => 0,'length' => 24
                    ),
                     array(
                         'name'=>'shippingmoney','uitype' => 2,'readonly' => 1,'length' => 24
                    ),
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonly'=>1,'length'=>24
                )
             ),
        );
        
        var $returnFields = array(
            'LBL_RETURNORDERFORM_INFORMATION' => array(
                     array(
                    'name'=>'cancelcontent','uitype'=> 1,'readonly'=>1,'length'=>100
                )
             ),
        );


        // 回调方法 ，初始化
        protected function _initialize() {
        }

        //返回ID
        public function getPk(){
            return 'bookorderid';
        }
    
    }

?>
