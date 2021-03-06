<?php
class SendnameMgrModel extends CRMEntityModel
{

    //定义表
    protected $tableName = 'sendnamemgr';
    //指定焦点字段
    public $fieldsFocus = 'code';
    //定义列表字段
    public $listFields = array(
        'code' => array('width' => 20),
        'name' => array('width' => 20),
        'telphone' => array('width' => 20),
        'weixin' => array('width' => 30),
        'assistantlogin' => array('width' => 20),
    );
    //定义查询字段
    public $searchFields = array('name', 'telphone');
    //返回主键的名称
    public function getPk()
    {
        return 'sendnamemgrid';
    }

    //新建的字段
    public $createFields = array(
        'LBL_SENDNAMEMGR_INFOMATION' => array(
            array(
                'name' => 'code', 'uitype' => 2, 'readonly' => 1, 'length' => 10,
            ),
            array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'telphone', 'uitype' => 2, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'weixin', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
        ),
        'LBL_SENDNAMEMGR_ASSISTANT' => array(
            array(
                'name' => 'assistantpwd', 'uitype' => 2, 'readonly' => 1, 'length' => 20,
            ),
        ),
        'LBL_SHAOMAOCODE' => array(
            array(
                'name' => 'shaomaocode', 'uitype' => 2, 'readonly' => 1, 'length' => 20,
            ),
        ),

    );

    //改单的字段
    public $editFields = array();

    public $detailFields = array(
        'LBL_SENDNAMEMGR_INFOMATION' => array(
            array(
                'name' => 'code', 'uitype' => 2, 'readonly' => 1, 'length' => 10,
            ),
            array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'telphone', 'uitype' => 2, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'weixin', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
        ),
        'LBL_SENDNAMEMGR_ASSISTANT' => array(
            array(
                'name' => 'assistantpwd', 'uitype' => 2, 'readonly' => 1, 'length' => 20,
            ),
            array(
                'name' => 'assistantlogin', 'uitype' => 2, 'readonly' => 0, 'length' => 20,
            ),
        ),
        'LBL_SHAOMAOCODE' => array(
            array(
                'name' => 'shaomaocode', 'uitype' => 2, 'readonly' => 1, 'length' => 20,
            ),
        ),
    );

    // 回调方法 ，初始化
    protected function _initialize()
    {
        $this->editFields = $this->createFields; //编辑字段

    }

}
