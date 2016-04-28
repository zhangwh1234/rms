<?php


    class UserModel extends CRMEntityModel{
        protected $tableName = 'user'; 
        //
        var $fieldsFocus = 'name';

        //定义列表
        var $listFields = array(
            'name'=>array('width'=>20),
            'truename'=>array('width'=>20),
            'rolename'=>array('width'=>20),
            'lastlog'=>array('width'=>20));

        //定义查询字段
        var $searchFields = array('name');

        //新建的字段
        var $createFields = array(
        'LBL_USER_INFORMATION' => array(
            array(
                'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
            ),
            array(
                'name'=>'password','uitype' => 12,'readonly' => 1,'length' => 24
            ),
            array(
                'name'=>'passwordtwo','uitype' => 12,'readonly' => 1,'length' => 24
            ),
            array(
                'name'=>'truename','uitype' => 1,'readonly' => 1,'length' => 24
            ),
            ),
        'LBL_ROLE_INFORMATION' => array(
            array(
                'name'=>'role','uitype' => 54,'readonly'=>1,'length' => 24  
            )
        ) 
        );


        var $editFields =  array();

        var $detailFields =array(
        'LBL_USER_INFORMATION' => array(
        array(
            'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        array(
            'name'=>'truename','uitype' =>1,'readonly' => 1,'length' => 24
        ),
        array(
            'name'=>'rolename','uitype' =>1,'readonly' => 1,'length' => 24
        ),
        array(
            'name'=>'lastlog','uitype' => 1,'readonly' => 1,'length' => 24
        ),
        ) 
        );

        // 回调方法 ，初始化
        protected function _initialize() {
            $this->editFields = $this->createFields; //编辑字段
        }

        //返回ID
        public function getPk(){
            return 'userid';
        }

    }
?>
