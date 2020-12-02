<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/8/13
 * Time: 下午5:47
 */

class YingshouReportModel extends CRMEntityModel{


     public $paymentDetailListFields = array(
        'code' => array('width' => 10),
        'name' => array('width' => 20),
        'money' => array('width' => 20),
        'address' => array('width' => 80, 'align' => 'left', 'halign' => 'center'),
        'ordertxt' => array('width' => 25, 'align' => 'left'),
        'sendname' => array('width' => 10, 'align' => 'center'),
        'company' => array('width' => 10, 'align' => 'center'),
    );
}