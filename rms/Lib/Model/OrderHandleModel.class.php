<?php
    class OrderHandleModel extends CRMEntityModel{

        protected $trueTableName = 'rms_orderform'; 
        //返回主键的名称
        public function getPk(){
            return 'orderformid';
        }

        //定义列表
        var $listFields = array('address','ordertxt','totalmoney','telphone','custtime','state','sendname','beizhu','telname','rectime','invoiceHeader');   


        //返回列表字段
        function getListFields(){
            return $this->listFields;
        }

        //定义查询字段
        var $searchFields = array('address','telphone','sendname');

        // 回调方法 ，初始化
        protected function _initialize() {
        }

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
    }

?>
