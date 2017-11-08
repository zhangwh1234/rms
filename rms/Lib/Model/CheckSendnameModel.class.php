<?php
class CheckSendnameModel extends CRMEntityModel{

    protected $tableName = 'checksendname';
    //
    var $list_link_field= 'name';
    //定义列表
    var $listFields = array('name','online','ontime','noread','alreadyread','nocomplete','alreadycomplete','totalorder');

    var $orderviewFields = array(
        'sendname'=>array('width' => 25, 'align' => 'left'),
        'ordertxt'=>array('width' => 25, 'align' => 'left'),
        'noreceivetime' => array( 'width' => 25 , 'align' => 'left'),
        'noreadtime' => array( 'width' => 25 , 'align' => 'left'),
        'nocompletetime' => array( 'width' => 25 , 'align' => 'left'),
        'completetime' => array( 'width' => 25 , 'align' => 'left'),
    );

    //返回主键的名称
    public function getPk(){
        return 'checksendnameid';
    }

}
?>
