<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 17/3/15
 * Time: 下午4:57
 */

class InvoiceElectronModel extends CRMEntityModel{

    //定义列表
    var $listFields = array(
        'header',
        'body',
        'eticketno',
        'ordersn',
        'ordertxt',
        'money',
    );


    //返回主键的名称
    public function getPk(){
        return 'invoicewebid';
    }

}