<?php
    /**
    * 装箱单模型
    * 2014-5-25
    */
class ZhuangxiangModel extends CRMEntityModel{
   protected $tableName = 'zhuangxiangform'; 
          //指定焦点字段
        var $fieldsFocus = 'code';
        //定义列表
        var $listFields = array(
            'code' => array('width' => 10, 'align' => 'left', 'halign' => 'center'),
            'sendname' => array('width' => 30, 'align' => 'left', 'halign' => 'center'),
            'zhuangxiangtxt' => array('width' => 60, 'align' => 'left', 'halign' => 'center'),
            'totalmoney' =>array('width' => 20, 'align' => 'left', 'halign' => 'center'),
            'rectime' =>array('width' => 20, 'align' => 'left', 'halign' => 'center'),
            'state' =>array('width' => 20, 'align' => 'left', 'halign' => 'center'),
        );
        //定义查询字段
        var $searchFields = array('code','name');

    //定义选择产品编码的字段
    var $popupProductsFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
        'shortname' => array('width' => 20, 'align' => 'right'),
        'price' => array('width' => 20, 'align' => 'left'),
        'brief' => array('width' => 20, 'align' => 'left'));

    //定义焦点产品选择的字段
        var $popupProductsLinkField = 'name';

        //新建的字段表
        var $createFields = array(
               'LBL_ZHUANGXIANG_INFORMATION' => array(
                array(
                    'name'=>'code','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'sendname','uitype'=>1,'readonly'=>0,'length'=>24
                ),                
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                        'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                    )
             ),
        );

    
            
    //改单的字段
    var $editFields =   array(
               'LBL_ZHUANGXIANG_INFORMATION' => array(
                array(
                    'name'=>'code','uitype' => 1,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'sendname','uitype'=>1,'readonly'=>0,'length'=>24
                ),
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonly'=>1,'length'=>24
                )
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonly'=>1,'length'=>24
                )
             ),
        );

        
    var $detailFields = array(
               'LBL_ZHUANGXIANG_INFORMATION' => array(
                array(
                    'name'=>'code','uitype' => 2,'readonly' => 1,'length' => 24
                ),
                array(
                    'name'=>'sendname','uitype'=>1,'readonley'=>1,'length'=>24
                ),
             ),
             'LBL_PRODUCTS_INFORMATION' => array(
                    array(
                    'name'=>'goods','uitype'=> 51,'readonley'=>1,'length'=>24
                )
             ),
             'LBL_ORDERACTION_INFORMATION' => array(
                     array(
                    'name'=>'orderaction','uitype'=> 52,'readonley'=>1,'length'=>24
                )
             ),
        );




    // 回调方法 ，初始化
    protected function _initialize() {
    }
    
    //返回ID
    public function getPk(){
            return 'zhuangxiangid';
    }
    }
?>
