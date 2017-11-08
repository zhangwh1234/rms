<?php
/**
 * 堂口售卖的数据模型
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/3/30
 * Time: 上午10:40
 */

class DiningSaleModel extends CRMEntityModel{
    protected $trueTableName = 'rms_diningsale';

    //定义列表
    var $listFields = array('saletime','productstxt','money');

    //定义选择产品编码的字段
    var $popupProductsFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
        'shortname' => array('width' => 20, 'align' => 'right'),
        'price' => array('width' => 20, 'align' => 'left'),
        'brief' => array('width' => 20, 'align' => 'left'));


    // 回调方法 ，初始化
    protected function _initialize()
    {
    }

    //返回ID
    public function getPk()
    {
        return 'diningsaleid';
    }
}