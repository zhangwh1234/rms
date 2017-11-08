<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/11/1
 * Time: 下午12:38
 */

class CompanyParamModel extends CRMEntityModel{

    protected $trueTableName = 'rms_companyparam';

    //返回ID
    public function getPk()
    {
        return 'companyparamid';
    }
}