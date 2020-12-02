<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/9
 * Time: 下午4:44
 */


class YingshouDoorBillModel extends CRMEntityModel{
    var  $trueTableName = 'doorbill_';

    var $focusFields = 'name';
    var $listFields = array(
        'diningsaleid'=>array('width'=>10),
        'productstxt'=>array('width'=>40),
        'money'=>array('width'=>20),
        'name'=>array('width'=>10),
        'date' => array('width'=>10),
        'ap' => array('width' =>10),
        'saletime'=>array('width'=>10),
        'state'=>array('width'=>8),
        'company'=>array('width'=>10));

    var $otherListFields = array(
        'noupdate' => 'noupdate',
    );


    //焦点字段
    var $fieldsFocus = 'name';

    


    //定义新建，浏览，编辑数据的字段
    var $createFields = array(
        'LBL_DOORBILL_INFORMATION' => array(
            array(
                'name'=>'code','uitype'=> 1,'readonly'=> 0,'length'=>24
            ),array(
                'name'=>'name','uitype' => 1,'readonly' => 0,'length' => 30,'value' => ''
            ),
            array(
                'name'=>'money','uitype'=> 1,'readonly'=> 0,'length'=>24
            ),array(
                'name'=>'operator','uitype' => 1,'readonly' => 0,'length' => 30
            ),
            array(
                'name'=>'date','uitype'=> 5,'readonly'=>1,'length'=>35
            ),array(
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
        return 'doorbillid';
    }

}