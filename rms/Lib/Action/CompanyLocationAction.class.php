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
        $where['domain'] = $this->getDomain();
        $fields = array('companymgrid as id,name,longitude,latitude,region');
        $companymgr = $companymgrModel->field($fields)->where($where)->select();
        $return = array();
        $return['city'] =  $this->getCityLocal();
        $return['company'] = $companymgr;
        $return['domain'] = $this->getDomain();
        $this->ajaxReturn($return,'json');
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
        //$where['domain'] = $this->getDomain();
        $where[] = ' length(longitude) > 0 ';
        $fields = array('sendnamemgrid as id,telphone,name,longitude,latitude');
        $sendnamemgr = $sendnamemgrModel->field($fields)->where($where)->select();

        $this->ajaxReturn($sendnamemgr,'json');
    }

    /**
     * 返回城市视野
     */
    public function getCityBoundary(){
        $cityModel = D('city');

        $where = array();
        $where['domain'] = $this->getDomain();
        $fields = array('name,longitude,latitude,region');
        $city = $cityModel->field($fields)->where($where)->find();

        $this->ajaxReturn($city,'json');
    }


    /**
     * 返回城市坐标
     */
    public function getCityLocal(){
        $domain = $this->getDomain();
        if($domain == 'bj.lihuaerp.com'){
            return '北京市';
        }
        if($domain == 'sh.lihuaerp.com'){
            return '上海市';
        }
        if($domain == 'nj.lihuaerp.com'){
            return '南京';
        }
        if($domain == 'cz.lihuaerp.com'){
            return '常州';
        }
        if($domain == 'wx.lihuaerp.com'){
            return '无锡';
        }
        if($domain == 'sz.lihuaerp.com'){
            return '苏州';
        }
        if($domain == 'gz.lihuaerp.com'){
            return '广州市';
        }
        if($domain == 'localhost'){
            return '北京市';
        }
    }
}