<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/12
 * Time: 上午11:12
 * 财务结账的模型
 */


class YingshouJournalModel extends CRMEntityModel{
    var  $trueTableName = 'journal_';

    var $focusFields = 'code';
    var $listFields = array(
        'turnover'=>array('width'=>20),
        'diningroom'=>array('width'=>20),
        'workinglunch' => array('width'=>20),
        'cash' => array('width' =>20));

    //焦点字段
    var $fieldsFocus = 'name';

    //定义查询的字段
    var $searchFields = array('rms_telcustomer.telphone','rms_teladdress.address');


    //定义新建，浏览，编辑数据的字段
    var $createFields = array(
        'LBL_ACCOUNT_INFORMATION' => array(
            array(
                'name'=>'telphone','uitype'=>21,'readonley'=>1,'length'=>24
            ),array(
                'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 30
            )
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
        return 'journalid';
    }

}