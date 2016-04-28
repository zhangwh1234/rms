<?php

    class SmsMgrModel extends CRMEntityModel{

        protected $tableName = 'smsmgr'; 
        //
        var $fieldsFocus = 'telphone';
        //定义列表
        var $listFields = array(
            'sendname' => array('width'=>20),
            'telphone'=>array('width'=>20),
            'content'=>array('width'=>20),
            'issend'=>array('width'=>20),
            'senddate'=>array('width'=>20)
        );

        //新建的字段
        var $createFields = array(
                'LBL_SMSMGR_INFORMATION' => array(
                    array(
                        'name'=>'sendcode','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'sendname','uitype' => 1,'readonly' => 0,'length' => 24
                    ),
                    array(
                        'name'=>'telphone','uitype' => 1,'readonly' => 0,'length' => 24
                        ),
                    array(
                        'name'=>'content','uitype' => 11,'readonly' => 1,'length' => 200
                        ),
                    array(
                        'name'=>'weixin','uitype' => 1,'readonly' => 0,'length' => 24
                    ),
                    ) 
        );
        
        var $detailFields =array();
        
         // 回调方法 ，初始化
        protected function _initialize() {
            $this->detailFields = $this->createFields; //编辑字段
        }

        //返回ID
        public function getPk(){
            return 'smsmgrid';
        }
        
        
    }
?>
