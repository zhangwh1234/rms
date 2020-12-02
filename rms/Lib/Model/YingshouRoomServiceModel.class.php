<?php
/**
 * Created by zhangwh.
 * User: lihua
 * Date: 16/7/5
 * Time: 下午12:51
 */

class YingshouRoomServiceModel extends CRMEntityModel
{
    public $trueTableName = 'roomservice_';

    

    //返回主键的名称
    public function getPk()
    {
        return 'roomserviceid';
    }

    //定义列表
    public $listFields = array(
        'name' => array('width' => 10, 'align' => 'left', 'halign' => 'center'),
        'totalmoney' => array('width' => 15, 'align' => 'left'),
        'jiezhangmoney' => array('width' => 15, 'align' => 'left'),
        'date' => array('width' => 10, 'align' => 'left'),
        'ap' => array('width' => 10, 'align' => 'center'),
    );

    public $orderformListFields = array(
        'address' => array('width' => 80 ,'align' => 'left', 'halign' => 'center'),
        'ordertxt' => array('width' => 25, 'align' => 'left'),
        'custtime' => array('width' => 10, 'align' => 'center'),
        'ap' => array('width' => 10, 'align' => 'left'),
        'sendname' => array('width' => 10, 'align' => 'center'),
        'company' => array('width' => 10, 'align' => 'center'),
        'state' => array('width' => 10, 'align' => 'center'),
        'goodsmoney' => array('width' => 10, 'align' => 'center'),
        'totalmoney' => array('width' => 10, 'align' => 'center'),
        'jiezhangmoney' => array('width' => 10, 'align' => 'center'),
    );

    public $paymentDetailListFields = array(
        'code' => array('width' => 10),
        'name' => array('width' => 20),
        'money' => array('width' => 20),
        'address' => array('width' => 80, 'align' => 'left', 'halign' => 'center'),
        'ordertxt' => array('width' => 25, 'align' => 'left'),
        'sendname' => array('width' => 10, 'align' => 'center'),
        'company' => array('width' => 10, 'align' => 'center'),
    );

    //定义选择产品编码的字段
    public $popupPaymentMgrFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
    );
     

}
