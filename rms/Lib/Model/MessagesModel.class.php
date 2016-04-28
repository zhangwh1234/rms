<?php
    /**
    * 群发消息模型
    */
    class MessagesModel extends CRMEntityModel{
        //定义列表焦点
        var $fieldsFocus = 'content';
        
        //定义查询的字段
        var $searchFields = array('content');
        
        //定义列表字段
        var $listFields = array(
                'content'=>array('width'=>20),
                'sender'=>array('width'=>20),
                'status'=>array('width'=>20),
                'time' => array('width'=>20));
        //新建的字段
        var $createFields = array(
            'LBL_Messages_INFORMATION' => array(
                array(
                    'name'=>'content','uitype' => 11,'readonly' => 1,'length' => 24
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
            return 'messagesid';
        }

    }
?>
