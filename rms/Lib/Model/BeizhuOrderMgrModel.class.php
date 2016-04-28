<?php
  
  class BeizhuOrderMgrModel extends CRMEntityModel{
      
      //定义表
        protected $tableName = 'beizhuordermgr';
        //定义列表焦点
        var $fieldsFocus  = 'name';
        //定义列表字段
        var $listFields = array(
            'name'=>array('width'=>20)
        );
               
        //新建的字段
        var $createFields = array(
                'LBL_BEIZHUORDERMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                    
                    ) 
        );
        
        //浏览的字段
        var $detailFields = array(
                'LBL_BEIZHUORDERMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                   
                    ) 
        );
        
        //编辑的字段
        var $editFields = array(
                'LBL_COMPANYMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                    
                    ) 
        );
  }
?>
