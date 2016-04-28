<?php
   class OrderMonitModel extends CRMEntityModel{

        //
        var $list_link_field= 'address';
        //定义列表
        var $listFields = array('address','ordertxt','telphone','totalmoney','custtime','company');
        //返回主键的名称
        public function getPk(){
            return 'orderformid';
        }
        
    }
?>
