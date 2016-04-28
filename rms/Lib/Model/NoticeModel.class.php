<?php

    class NoticeModel extends CRMEntityModel{
   
        //定义列表显示字段
        var $listFields = array(
            'content' => array('title'=>'公告内容','width'=>50,'align'=>'left','halign'=>'center')
        );
        
        //定义查询的字段
        var $searchFields = array('content');
        
        //焦点字段
        var $fieldsFocus = 'content';
        //新建数据的字段
        var $createFields = array(
            'LBL_NOTICE_INFORMATION' => array(
                array(
                    'name'=>'content',
                    'uitype' => 11,
                    'readonly' => 1,
                    'length' => 100,
                	'data-options' => 'required:true',
                )
            )
        );
        //定义浏览的字段
        var $detailFields = array(
            'LBL_NOTICE_INFORMATION' => array(
                array(
                    'name'=>'content',
                    'uitype' => 11,
                    'readonly' => 1,
                    'length' => 100,
                    'value' => ''
                )
            )
        );
        
        //定义编辑的字段
        var $editFields = array(
            'LBL_NOTICE_INFORMATION' => array(
                array(
                    'name'=>'content',
                    'uitype' => 11,
                    'readonly' => 1,
                    'length' => 100,
                    'value' => ''
                )
            )
        );

    }
?>
