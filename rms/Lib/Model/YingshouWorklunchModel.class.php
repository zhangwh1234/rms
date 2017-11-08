<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/5/19
 * Time: 下午12:39
 * 工作餐交账模型
 */

class YingshouWorklunchModel extends CRMEntityModel{
    var  $trueTableName = 'worklunchbill_';

    var $focusFields = 'name';
    var $listFields = array(
        'code'=>array('width'=>20),
        'name'=>array('width'=>20),
        'money'=>array('width'=>20),
        'date' => array('width'=>20),
        'ap' => array('width' =>20));

    //焦点字段
    var $fieldsFocus = 'name';

    //定义查询的字段
    var $searchFields = array('rms_telcustomer.telphone','rms_teladdress.address');


    //定义新建，浏览，编辑数据的字段
    var $createFields = array(
        'LBL_WORKLUNCHBILL_INFORMATION' => array(
            array(
                'name'=>'code','uitype'=> 65,'readonly'=> 1,'length'=>24
            ),
            array(
                'name'=>'name','uitype' => 1,'readonly' =>0,'length' => 30
            ),
            array(
                'name'=>'money','uitype'=> 1,'readonly'=>0,'length'=>24
            ),
            array(
                'name'=>'date','uitype'=> 5,'readonly'=>1,'length'=>35
            ),
            array(
                'name'=>'ap','uitype' => 9,'readonly' => 1,'length' => 30
            ),
        ),
    );

    //定义选择产品编码的字段
    var $popupWorklunchFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
    );

    //定义选择产品编码的字段
    var $popupAccountsFields = array(
        'code' => array('width' => 20, 'align' => 'left'),
        'name' => array('width' => 20, 'align' => 'left'),
    );

    var $editFields =  array();

    var $detailFields = array();

    // 回调方法 ，初始化
    protected function _initialize() {
        $this->editFields = $this->createFields; //编辑字段
        $this->detailFields = $this->createFields; //浏览字段
    }

    //返回ID
    public function getPk(){
        return 'worklunchid';
    }

}