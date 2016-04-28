<?php
  /**
  * 分送点的数据管理模块
  */
  
  class SecondPointMgrModel extends CRMEntityModel{
      //定义表
        protected $tableName = 'secondpointmgr';
        //定义列表焦点
        var $list_link_field= 'name';
        //定义列表字段
        var $listFields = array(
            'name'=>array('width'=>20),
            'code'=>array('width'=>20)
        );
               
        //新建的字段
        var $createFields = array(
                'LBL_SECONDPOINTMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 2,'readonly' => 1,'length' => 24
                        ),
                    array(
                        'name'=>'code','uitype' => 2,'readonly' => 1,'length' => 24
                        ),
                    ) 
        );
        
        ///改单的字段
        var $editFields =  array();

        var $detailFields = array();

        // 回调方法 ，初始化
        protected function _initialize() {
            $this->editFields = $this->createFields; //编辑字段
            $this->detailFields = $this->createFields; //浏览字段
        }
        
        //返回ID
        public function getPk(){
            return 'secondpointmgrid';
        }
  }
?>
