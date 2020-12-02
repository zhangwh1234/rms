<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/10
 * Time: 下午5:18
 * 电子发票统计
 */

class ElectronInvoiceReportModel extends CRMEntityModel
{
    protected $tableName = 'invoicereport';

    //定义列表显示字段
    public $listFields = array(
        'company' => array('title' => '公司名称', 'width' => 50, 'align' => 'left', 'halign' => 'center'),
        'day1_number' => array('title' => '发票数量', 'width' => 50, 'align' => 'left', 'halign' => 'center'),
        'day1_money' => array('title' => '发票金额', 'width' => 50, 'align' => 'left', 'halign' => 'center'),
        'sum(day1_number)' => array('title' => '总计', 'width' => 50, 'align' => 'left', 'halign' => 'center'),
        'sum(day1_money)' => array('title' => '总计', 'width' => 50, 'align' => 'left', 'halign' => 'center'),
    );
}
