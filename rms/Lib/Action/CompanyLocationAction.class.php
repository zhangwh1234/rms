<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/4/22
 * Time: 下午12:59
 */


class CompanyLocationAction extends ModuleAction{

    /**
     * 返回分公司的坐标,和送餐范围
     */
    public function getCompany(){
        $companymgrModel = D('companymgr');

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo ['department'];

        $where = array();
        $where['domain'] = $_SERVER['HTTP_HOST'];
        $fields = array('companymgrid as id,name,longitude,latitude,region');
        $companymgr = $companymgrModel->field($fields)->where($where)->select();

        $this->ajaxReturn($companymgr,'json');
    }

    /**
     * 返回送餐员的坐标位置等信息
     */
    public function getSendname(){

        $sendnamemgrModel = D('sendnamemgr');

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo ['department'];

        $where = array();
        //$where['company'] = $company;
        //$where['domain'] = $_SERVER['HTTP_HOST'];
        $where[] = ' length(longitude) > 0 ';
        $fields = array('sendnamemgrid as id,telphone,name,longitude,latitude');
        $sendnamemgr = $sendnamemgrModel->field($fields)->where($where)->select();

        $this->ajaxReturn($sendnamemgr,'json');
    }

    /**
     * 返回城市坐标
     */
    public function getCityLocal(){

    }
}