<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2019/12/09
 * Time: 3:35 PM
 * 营收内部转账管理的数据模型
 */

class YingshouInnerCarryModel extends CRMEntityModel
{
    public $trueTableName = 'innercarry_';

    public $focusFields = 'code';
    public $listFields = array(
        'company' => array('width' => 10),
        'code' => array('width' => 10),
        'name' => array('width' => 20),
        'money' => array('width' => 10),
        'type' =>  array('width' => 10),
        'innercompany' => array('width' => 10),
        'innercode' => array('width' => 10),
        'inneraccount' => array('width' => 20),
        'date' => array('width' => 20),
    );

    //焦点字段
    public $fieldsFocus = 'name';

    //定义查询的字段
    public $searchFields = array('rms_telcustomer.telphone', 'rms_teladdress.address');

    //定义新建，浏览，编辑数据的字段
    public $createFields = array(
        'LBL_IncomeMgr_INFORMATION' => array(
            array(
                'name' => 'code', 'uitype' => 68, 'readonly' => 1, 'length' => 24,
            ), array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 0, 'length' => 30,
            ),
            array(
                'name' => 'money', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'innercompany', 'uitype' => 9, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'innercode', 'uitype' => 69, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'inneraccount', 'uitype' => 1, 'readonly' => 0, 'length' => 30,
            ),
             array(
                'name' => 'date', 'uitype' => 5, 'readonly' => 0, 'length' => 35,
            ),
        ),
    );

    public $editFields = array();

    public $detailFields = array(
        'LBL_IncomeMgr_INFORMATION' => array(
            array(
                'name' => 'code', 'uitype' => 1, 'readonly' => 1, 'length' => 24,
            ), array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 0, 'length' => 30,
            ),
            array(
                'name' => 'money', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'type', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'innercompany', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'innercode', 'uitype' => 1, 'readonly' => 1, 'length' => 24,
            ),
            array(
                'name' => 'inneraccount', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'date', 'uitype' => 5, 'readonly' => 1, 'length' => 35,
            ),

        ),
    );

    public $content_fields = array(
        'journalorder' => array('width' => 20),
        'summary' => array('width' => 20),
        'subject' => array('width' => 20),
        'subjectname' => array('width' => 20),
        'money' => array('width' => 20),
    );

    public $otherListFields = array(
        'shenhe',
    );

    //定义选择产品编码的字段
    public $popupPaymentMgrFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
    );

    // 回调方法 ，初始化
    protected function _initialize()
    {
        $this->editFields = $this->createFields; //编辑字段
    }

    //返回ID
    public function getPk()
    {

        return 'innercarryid';

    }

}
