<?php
    /**
    * 订单打印处理的模型
    * 2013-12-30编制
    */

    class OrderPrintHandleModel extends CRMEntityModel{

        //定义表名
        protected $tableName = 'orderprinthandle';        
        //主要连接字段
       var $fieldsFocus = 'code';
        //定义列表字段
        var $listFields = array(
            'code' => array('width' => 10, 'align' => 'left', 'halign' => 'center'),
            'name' =>  array('width' => 10, 'align' => 'left', 'halign' => 'center'),
            'printnumbertxt'=> array('width' => 80, 'align' => 'left', 'halign' => 'center'),
            'time' =>  array('width' => 10, 'align' => 'left', 'halign' => 'center'),
        );
        //定义查询字段
        var $searchFields = array('code','name');

        //定义选择产品编码的字段
        var $popupSendnameFields = array(
            'code' => array('width' => 20, 'align' => 'left'),
            'name' => array('width' => 20, 'align' => 'left'),
            );

        //新建的字段表
        var $createFields = array( 
            'LBL_ORDERPRINT_INFORMATION' => array(
                array(
                    'name'=>'code','uitype' => 63,'readonly' => 1,'length' => 24
                    ),
                array(
                    'name'=>'name','uitype' => 1,'readonly' => 0,'length' => 24
                    )
                ),
             'LBL_ORDERPRINTCONTENT_INFORMATION' => array(
                array(
                    'name'=>'content','uitype' => 60,'readonly' => 1,'length' => 4
                )
             )
        );
        
        var $editFields = array();
        var $detailFields = array(
            'LBL_ORDERPRINT_INFORMATION' => array(
                array(
                    'name'=>'code','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'name','uitype' => 1,'readonly' => 0,'length' => 24
                )
            ),
            'LBL_ORDERPRINTCONTENT_INFORMATION' => array(
                array(
                    'name'=>'content','uitype' => 60,'readonly' => 1,'length' => 4
                )
            )
        );
        
        // 回调方法 ，初始化
        protected function _initialize() {
           $this->editFields = $this->createFields; //编辑字段
           $this->detailFields = $this->detailFields; //浏览字段
        }
        
        //返回ID
    public function getPk(){
            return 'orderprinthandleid';
    }

    }
?>
