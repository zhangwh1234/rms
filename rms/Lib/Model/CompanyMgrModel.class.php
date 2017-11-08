<?php
  
  class CompanyMgrModel extends CRMEntityModel{

      /**
       * ALTER TABLE `rms_companymgr` ADD `cashier` VARCHAR(20) NOT NULL COMMENT '收款人' AFTER `latitude`, ADD `checker` VARCHAR(20) NOT NULL COMMENT '复核人' AFTER `cashier`;
       */
      
      //定义表
        protected $tableName = 'companymgr';
        //定义列表焦点
        var $fieldsFocus  = 'name';
        //定义列表字段
        var $listFields = array(
            'name'=>array('width'=>20),
            'distributionCode'=>array('width'=>20),
            'telphoneauto' => array('width'=>20),
            'baiduauto' =>  array('width' => 10),
            'elmauto' => array('elmauto' => 10),
            'meituanauto' =>  array('meituanauto' => 10)
        );
               
        //新建的字段
        var $createFields = array(
                'LBL_COMPANYMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                    array(
                        'name'=>'distributionCode','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                ) ,
                'LBL_COMPANYMGR_TELPHONE' => array(
                    array(
                        'name'=>'longitude','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'latitude','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'region','uitype' => 11,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'telphoneauto','uitype' => 64,'readonly' => 1,'length' => 24
                    ),

                ),
                'LBL_COMPANYMGR_BAIDU' => array(
                    array(
                        'name'=>'baiduopt','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'baiduauto','uitype' => 64,'readonly' => 1,'length' => 24
                        ),
                ) ,
                'LBL_COMPANYMGR_ELM' => array(
                    array(
                        'name'=>'elmopt','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'elmauto','uitype' =>64,'readonly' => 1,'length' => 24
                    ) ,
                ),
                'LBL_COMPANYMGR_MEITUAN' => array(
                    array(
                         'name'=>'meituanopt','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                    array(
                         'name'=>'meituanauto','uitype' => 64,'readonly' => 1,'length' => 24
                        ),
                ),
                'LBL_COMPANYMGR_INVOICE' => array(
                    array(
                        'name'=>'cashier','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                    array(
                        'name'=>'checker','uitype' => 1,'readonly' => 1,'length' => 24
                 ),
            ),

        );
        
        //浏览的字段
        var $detailFields = array(
                'LBL_COMPANYMGR_INFORMATION' => array(
                    array(
                        'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                    array(
                        'name'=>'distributionCode','uitype' => 1,'readonly' => 1,'length' => 24
                        ),
                ) ,
            'LBL_COMPANYMGR_TELPHONE' => array(
                array(
                    'name'=>'longitude','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'latitude','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'region','uitype' => 11,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphoneauto','uitype' => 64,'readonly' => 1,'length' => 24
                ),

            ),
            'LBL_COMPANYMGR_BAIDU' => array(
                array(
                    'name'=>'baiduopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'baiduauto','uitype' => 9,'readonly' => 1,'length' => 24
                ),
            ) ,
            'LBL_COMPANYMGR_ELM' => array(
                array(
                    'name'=>'elmopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'elmauto','uitype' =>9,'readonly' => 1,'length' => 24
                ) ,
            ),
            'LBL_COMPANYMGR_MEITUAN' => array(
                array(
                    'name'=>'meituanopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'meituanauto','uitype' => 9,'readonly' => 1,'length' => 24
                ),
            ),
            'LBL_COMPANYMGR_INVOICE' => array(
                array(
                    'name'=>'cashier','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'checker','uitype' => 1,'readonly' => 1,'length' => 24
                ),
            ),
        );
        
        //编辑的字段
        var $editFields = array(
            'LBL_COMPANYMGR_INFORMATION' => array(
                array(
                    'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                array(
                    'name'=>'distributionCode','uitype' => 1,'readonly' => 1,'length' => 24
                    ),
                ) ,
            'LBL_COMPANYMGR_TELPHONE' => array(
                array(
                    'name'=>'longitude','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'latitude','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'region','uitype' => 11,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'telphoneauto','uitype' => 64,'readonly' => 1,'length' => 24
                ),

            ),
            'LBL_COMPANYMGR_BAIDU' => array(
                array(
                    'name'=>'baiduopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'baiduauto','uitype' => 64,'readonly' => 1,'length' => 100
                ),
            ) ,
            'LBL_COMPANYMGR_ELM' => array(
                array(
                    'name'=>'elmopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'elmauto','uitype' =>64,'readonly' => 1,'length' => 100
                ) ,
            ),
            'LBL_COMPANYMGR_MEITUAN' => array(
                array(
                    'name'=>'meituanopt','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'meituanauto','uitype' => 64,'readonly' => 1,'length' => 100
                ),
            ),
            'LBL_COMPANYMGR_INVOICE' => array(
                array(
                    'name'=>'cashier','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'checker','uitype' => 1,'readonly' => 1,'length' => 24
                ),
            ),
        );
  }
?>
