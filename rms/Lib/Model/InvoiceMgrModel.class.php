<?php
    /**
    * 发票管理模型
    * 2014-05-30
    */

    class InvoiceMgrModel extends CRMEntityModel{
        protected $trueTableName = 'rms_invoice';

        var $searchFields = array(
            'header',
            'body',
            'ordersn',
            'orderformtxt',
            'ordertime',
            'opentime',
            'openname',
            'ordermoney',
        );

        //定义列表
        var $listFields = array(
            'invoiceid',
            'header',
            'body',
            'ordersn',
            'orderformtxt',
            'ordertime',
            'opentime',
            'openname',
            'ordermoney',
            'state',
        );


        //返回主键的名称
        public function getPk(){
            return 'invoiceid';
        }

    }
?>
