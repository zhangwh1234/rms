<?php
    /**
    * 角色表的模型
    */

    class RoleModel extends CRMEntityModel{
        //定义列表焦点
        var $fieldsFocus = 'name';
        //定义列表字段
        var $listFields = array(
            'name'   =>array('width'=>20),
            'remark' =>array('width'=>20),
            'status' =>array('width'=>20)
        );
        //新建的字段
        var $createFields = array(
        'LBL_ROLE_INFORMATION' => array(
        array(
            'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        array(
            'name'=>'remark','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        array(
            'name'=>'status','uitype' => 1,'readonly' => 1,'length' => 24
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
