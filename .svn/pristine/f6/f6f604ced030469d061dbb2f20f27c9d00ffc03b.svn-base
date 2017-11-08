<?php
  /**
  * 节点的模型
  */
  class NodeModel extends CRMEntityModel{
        //表名
        protected $tableName = 'node';
        //定义列表焦点
        var $fieldsFocus = 'name';
        //定义列表字段
        var $listFields = array('name','remark','status');
        //新建模块的字段
        var $createFields = array(
        'LBL_MODULE_INFORMATION' => array(
        array(
        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        array(
        'name'=>'title','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        array(
        'name'=>'status','uitype' => 53,'readonly' => 1,'length' => 24
        ),
        ) 
        );
        
        //新建模块功能的字段
        var $createMethodFields =  array(
            'LBL_METHOD_INFORMATION' => array(
            array(
                'name'=>'method_name','uitype' => 1,'readonly' => 1,'length' => 24
            ),
            array(
                'name'=>'method_title','uitype' => 1,'readonly' => 1,'length' => 24
            ),
            array(
                'name'=>'method_status','uitype' => 53,'readonly' => 1,'length' => 24
            ),
            ) 
        ); 
        
        

        //改单的字段
        var $editFields =  array();

        var $detailFields = array();

        // 回调方法 ，初始化
        protected function _initialize() {
            $this->editFields = $this->createFields; //编辑字段
            $this->detailFields = $this->createFields; //浏览字段
        }
        
        //返回ID
        public function getPk(){
            return 'id';
        }
  }
?>
