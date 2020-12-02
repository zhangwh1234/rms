<?php
/**
 * 发票管理模型
 * 2014-05-30
 */

class ExhibitionInvoiceModel extends CRMEntityModel
{
    protected $trueTableName = 'rms_invoice';

    public $searchFields = array(
        'header',
        'body',
        'ordersn',
        'orderformtxt',
        'ordertime',
        'opentime',
        'openname',
        'ordermoney',
    );

    //定义列表
    public $listFields = array(
        'invoiceid',
        'header',
        'body',
        'ordersn',
        'orderformtxt',
        'ordertime',
        'opentime',
        'openname',
        'ordermoney',
        'state',
        'type',
        'gmf_nsrsbh',
    );

    //返回主键的名称
    public function getPk()
    {
        return 'invoiceid';
    }

}
