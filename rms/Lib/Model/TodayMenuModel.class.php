<?php
  /**
  * 今日菜单的模型
  */
  class TodayMenuModel extends CRMEntityModel{
      //定义实际的表名
      protected $tableName = 'todaymenu';
       
      var $fieldsFocus = 'content';
      //定义列表
      var $listFields = array(
          'date'=>array('width'=>20),
          'content'=>array('width'=>20));

      //定义新建，浏览，编辑数据的字段
      var $createFields= array(
            'LBL_TODAYMENU_INFORMATION' => array(
                array(
                    'name'=>'date','uitype'=>55,'readonly'=>0,'length'=>24
                )               
             ),
             'LBL_CONTENT_INFORMATION' => array(
                array(
                    'name'=>'content','uitype' => 56,'readonly' => 1,'length' => 30
                )
             )
                        
        );
        
     var $detailFields =  array( 
        'LBL_TODAYMENU_INFORMATION' => array(
                array(
                    'name'=>'date','uitype'=>55,'readonly'=>1,'length'=>24
                )               
             ),
             'LBL_CONTENT_INFORMATION' => array(
                array(
                    'name'=>'content','uitype' => 56,'readonly' => 1,'length' => 30
                )
             )          
        );
     var $editFields =array(
        );     
     
            // 回调方法 ，初始化
        protected function _initialize() {
            $this->editFields = $this->createFields; //编辑字段
        }
        
        //自动验证
        protected $_validate = array(
            array('date','require','日期必须！'), //默认情况下用正则进行验证
        );

        //返回ID
        public function getPk(){
            return 'todaymenuid';
        }

  }
?>
