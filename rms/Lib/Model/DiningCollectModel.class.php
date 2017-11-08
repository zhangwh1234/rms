<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/30
 * Time: 下午5:22
 */

class DiningCollectModel extends CRMEntityModel{
    protected $trueTableName = 'rms_diningcollect';

    var $listFields = array(
        'company'=>array('width'=>20),
        'money'=>array('width'=>20),
        'date'=>array('width'=>20),
        'ap'=>array('width'=>20)
        );

    //返回ID
    public function getPk(){
        return 'diningcollectid';
    }


}