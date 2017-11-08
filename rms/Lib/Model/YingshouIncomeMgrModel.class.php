<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2019/1/25
 * Time: 3:35 PM
 * 营收收入管理的数据模型
 */

class YingshouIncomeMgrModel extends CRMEntityModel{
    var  $trueTableName = 'incomemgr_';

    var $focusFields = 'code';
    var $listFields = array(
        'code'=>array('width'=>20),
        'name'=>array('width'=>20),
        'operation'=>array('width'=>20),
        'money' => array('width'=>20),
        'date' => array('width' =>20));

    //焦点字段
    var $fieldsFocus = 'name';

    //定义查询的字段
    var $searchFields = array('rms_telcustomer.telphone','rms_teladdress.address');


    //定义新建，浏览，编辑数据的字段
    var $createFields = array(
        'LBL_IncomeMgr_INFORMATION' => array(
            array(
                'name'=>'code','uitype'=> 66,'readonly'=> 1,'length'=>24
            ),array(
                'name'=>'name','uitype' => 1,'readonly' => 0,'length' => 30
            ),
            array(
                'name'=>'operation','uitype'=> 9,'readonly'=> 1,'length'=>24
            ),array(
                'name'=>'money','uitype' => 1,'readonly' => 1,'length' => 30
            ),
            array(
                'name'=>'date','uitype'=> 5,'readonly'=>1,'length'=>35
            ),
            array(
                'name'=>'ap','uitype' => 9,'readonly' => 1,'length' => 30
            ),
        ),
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

        return 'incomemgrid';

    }


}