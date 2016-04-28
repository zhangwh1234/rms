<?php
    //订单分配的数据
    class OrderDistributionModel extends CRMEntityModel{
        protected $trueTableName = 'rms_orderform'; 
        public function getName(){
            return 'orderform';
        }

        //定义列表
        var $listFields = array('address','ordertxt','totalmoney','telphone','custtime','state','beizhu','company');

        //定义列表
        var $searchListFields = array(
            'address'=>array('width' => 50,'align'=>'left','halign'=>'center'),
            'ordertxt'=>array('width' => 20,'align'=>'left' ),
            'telphone'=>array('width' => 10,'align'=>'left'),
            'totalmoney'=>array('width' => 10,'align'=>'center'),
            'custtime'=>array('width' => 10,'align'=>'center'),
            'state' => array('width' => 10,'align'=>'center'),
            'beizhu' => array('width' => 10,'align'=>'center'),
            'company' => array('width' => 10,'align'=>'center')
        );

         //定义查询字段
        var $searchFields = array('address','telphone','sendname','telname','company');
        
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

             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonley'=>1,'length'=>24
                )
             ),
        );

        
        //返回列表字段
        function getListFields(){
            return $this->listFields;
        }

    }
?>
