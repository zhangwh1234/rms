<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/10
 * Time: 下午5:27
 * 分录底稿模块
 */

class YingshouFinanceModel extends CRMEntityModel
{
    public $trueTableName = 'finance_';

    public $focusFields = 'code';
    public $listFields = array(
         'summary' => array('width' => 20),
        'company' => array('width' => 20),
        'money' => array('width' => 20),
        'date' => array('width' => 20),
    );

    //焦点字段
    public $fieldsFocus = 'name';

    //定义查询的字段
    public $searchFields = array('rms_telcustomer.telphone', 'rms_teladdress.address');

    //定义新建，浏览，编辑数据的字段
    public $createFields = array(
        'LBL_ACCOUNT_INFORMATION' => array(
            array(
                'name' => 'company', 'uitype' => 21, 'readonley' => 1, 'length' => 24,
            ), array(
                'name' => 'money', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
        ),

    );

    public $editFields = array();

    public $detailFields = array();

    public $content_fields = array(
        'journalorder' => array('width' => 20),
        'summary' => array('width' => 20),
        'subject' => array('width' => 20),
        'subjectname' => array('width' => 20),
        'checks' => array('width' => 20),
        'money' => array('width' => 20),
        'debitcredit' => array('width' => 20),
    );

    // 回调方法 ，初始化
    protected function _initialize()
    {
        $this->editFields = $this->createFields; //编辑字段
        $this->detailFields = $this->createFields; //浏览字段
    }

    //返回ID
    public function getPk()
    {
        return 'financeid';
    }

}
