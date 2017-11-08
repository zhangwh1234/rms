<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/23
 * Time: 下午5:19
 * 退单模块
 */


class DiningReturnModel extends CRMEntityModel{
    protected $trueTableName = 'rms_diningreturn';

    //定义列表焦点
    var $fieldsFocus = 'sequence';

    //定义查询的字段
    var $searchFields = array('sequence');

    //定义列表字段
    var $listFields = array(
        'company' => array('width'=>20),
        'sequence'=>array('width'=>20),
        'money'=>array('width'=>20),
        'time'=>array('width'=>20),
       );
    //新建的字段
    var $createFields = array(
        'LBL_DiningReturn_INFORMATION' => array(
            array(
                'name' => 'company', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'sequence', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'money', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
            array(
                'name' => 'time', 'uitype' => 2, 'readonly' => 1, 'length' => 24
            ),
        )
    );

    //改单的字段
    var $editFields =  array();

    var $detailFields = array();

    // 回调方法 ，初始化
    protected function _initialize() {
        $this->editFields = $this->createFields; //编辑字段
        $this->detailFields = $this->createFields; //浏览字段
    }

    //返回ID
    public function getPk(){
        return 'diningreturnid';
    }

}