<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2017/12/30
 * Time: 下午1:16
 * 餐卡模型
 */

class MealticketMgrModel extends CRMEntityModel{

    var $trueTableName = 'rms_mealticket';
    var $fieldsFocus = 'code';

    //定义列表
    var $listFields = array(
        'code'=>array('width'=>20),
        'name'=>array('width'=>20),
        'price'=>array('width'=>20),
        );
    //定义查询的字段
    var $searchFields = array('code','name');
    //定义新建，浏览，编辑数据的字段
    var $createFields= array(
        'LBL_MEALTICKETMGR_INFORMATION' => array(
            array(
                'name'=>'code','uitype'=>1,'readonly'=>1,'length'=>24
            ),
            array(
                'name'=>'name','uitype' => 1,'readonly' => 1,'length' => 30
            ),
            array(
                'name'=>'price','uitype'=>4,'readonly'=>1,'length'=>24
            ),

        ),
    );

    var $detailFields =  array(
    );
    var $editFields =array(
    );

    // 回调方法 ，初始化
    protected function _initialize() {
        $this->detailFields = $this->createFields; //查看浏览
        $this->editFields = $this->createFields; //编辑字段
    }

    //返回ID
    public function getPk(){
        return 'mealticketid';
    }

}