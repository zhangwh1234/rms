<?php
/**
 *  发票登记模型
 */

class YingshouInvoiceModel extends CRMEntityModel
{
    public $trueTableName = 'yingshouinvoice';

    public $focusFields = 'code';
    public $listFields = array(
        'header' => array('width' => 30),
        'name' => array('width' => 20),
        'number' => array('width' => 10),
        'money' => array('width' => 20),
        'date' => array('width' => 20),

    );

    //焦点字段
    public $fieldsFocus = 'name';

    //定义查询的字段
    public $searchFields = array('rms_telcustomer.telphone', 'rms_teladdress.address');

    //定义新建，浏览，编辑数据的字段
    public $createFields = array(
        'LBL_InVOICE_INFORMATION' => array(
            array(
                'name' => 'code', 'uitype' => 67, 'readonly' => 1, 'length' => 24,
            ), array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 0, 'length' => 30,
            ),
            array(
                'name' => 'number', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'header', 'uitype' => 1, 'readonly' => 1, 'length' => 50,
            ),
            array(
                'name' => 'money', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'tax', 'uitype' => 9, 'readonly' => 1, 'length' => 35,
            ),
            array(
                'name' => 'body', 'uitype' => 9, 'readonly' => 1, 'length' => 35,
            ),

            array(
                'name' => 'date', 'uitype' => 5, 'readonly' => 1, 'length' => 35,
            ),

        ),
    );

    public $editFields = array();

    public $detailFields = array(
        'LBL_Invoice_INFORMATION' => array(
            array(
                'name' => 'code', 'uitype' => 1, 'readonly' => 1, 'length' => 24,
            ), array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 0, 'length' => 30,
            ),
            array(
                'name' => 'number', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'header', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'notaxmoney', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'tax', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'body', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'taxmoney', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ),
            array(
                'name' => 'money', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
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

    // 回调方法 ，初始化
    protected function _initialize()
    {
        $this->editFields = $this->createFields; //编辑字段
    }

    //返回ID
    public function getPk()
    {

        return 'invoiceid';

    }
}
