<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/1
 * Time: 上午10:25
 * 客户支付的类型管理，比如现金，赠卡等，有不同的属性
 */

class PaymentMgrModel extends CRMEntityModel
{
    public $trueTableName = 'rms_paymentmgr';
    public $fieldsFocus = 'code';

    public $listFields = array(
        'code' => array('width' => 20),
        'name' => array('width' => 20),
        'accounting' => array('width' => 20),
        'subject' => array('width' => 20),
        'type' => array('width' => 20),
        'company' => array('width' => 20),
    );

   

    //定义查询的字段
    public $searchFields = array('rms_telcustomer.telphone', 'rms_teladdress.address');

    //定义新建，浏览，编辑数据的字段
    public $createFields = array(
        'LBL_PAYMENTMGR_INFORMATION' => array(
            array(
                'name' => 'code', 'uitype' => 21, 'readonley' => 1, 'length' => 24,
            ), array(
                'name' => 'name', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ), array(
                'name' => 'accounting', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ), array(
                'name' => 'subject', 'uitype' => 1, 'readonly' => 1, 'length' => 30,
            ), array(
                'name' => 'type', 'uitype' => 64, 'readonly' => 1, 'length' => 30,
            ), array(
                'name' => 'company', 'uitype' => 64, 'readonly' => 1, 'length' => 30,
            ),

        ),
    );

    public $otherListFiels = array(
        'is_use'
    );

    public $editFields = array();

    public $detailFields = array();

    // 回调方法 ，初始化
    protected function _initialize()
    {
        $this->editFields = $this->createFields; //编辑字段
        $this->detailFields = $this->createFields; //浏览字段
    }

    //返回ID
    public function getPk()
    {

        return 'paymentmgrid';

    }

}
