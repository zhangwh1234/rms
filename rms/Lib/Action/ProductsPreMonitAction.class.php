<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/8/21
 * Time: 下午5:01
 * 备餐系统显示
 */

class ProductsPreMonitAction extends ModuleAction
{

    /**
     *
     */
    public function index()
    {
        $this->listview();
    }

    /**
     * 显示备餐信息
     */
    public function monitview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        if (empty($company)) {
            echo '分公司为空，退出！';
            return;
        }

        $this->assign('company', $company);

        $productspremonitModel = D('productspremonit');
        /**
        $where = array();
        $where['name'] = array('notlike', '%S%');
        $where['company'] = $company;
        $where ['domain'] = $this->getDomain();
        $productspremonitResult = $productspremonitModel->where($where)->order('number desc,name desc')->select();
         */
        $domain = $this->getDomain();
        $sql = "SELECT a.* FROM rms_orderproducts  as a JOIN rms_orderform as b on a.ordersn = b.ordersn
                where  b.company = '" . $company . "' and b.domain  = '" . $domain . "' and length(b.sendname) = 0 group by a.name  order by a.name ";
        $productspremonitResult = $productspremonitModel->query($sql);
        $this->assign('productspremonit', $productspremonitResult);
        $this->display('monitview');
    }

    /**
     * 返回productspremonit的数据
     */
    public function getProductsPre()
    {

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        if (empty($company)) {
            echo '分公司为空，退出！';
            return;
        }

        $productspremonitModel = D('productspremonit');
        /**
        $where = array();
        $where['name'] = array('notlike', '%S%');
        $where['company'] = $company;
        $where ['domain'] = $this->getDomain();
         */
        $domain = $this->getDomain();
        $sql = "SELECT a.* FROM rms_orderproducts  as a JOIN rms_orderform as b on a.ordersn = b.ordersn
                where  b.company = '" . $company . "' and b.domain  = '" . $domain . "' and length(b.sendname) = 0  group by a.name  order by a.name ";
        $productspremonitResult = $productspremonitModel->query($sql);

        //$productspremonitResult = $productspremonitModel->where($where)->order('number desc,name desc')->select();
        $this->ajaxReturn($productspremonitResult, 'json');
    }

    /**
     * 返回productspre的页面
     */
    public function prepareview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        if (empty($company)) {
            //echo '分公司为空，退出！';
            //return;
        }

        $this->assign('company', $company);

        $productsprepareModel = D('productsprepare');
        //返回所有的订单
        $where = array();
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $ordersnResult = $productsprepareModel->distinct(true)->field('ordersn')->where($where)->order('create_time desc')->select();

        $productsprepareArray = array();
        foreach ($ordersnResult as $ordersn) {
            $where = array();
            $where['ordersn'] = $ordersn['ordersn'];
            $productsprepareResult = $productsprepareModel->where($where)->order('name desc')->select();
            $productsprepareArray[] = $productsprepareResult;
        }

        $this->assign('productsprepare', $productsprepareArray);
        $this->display('prepareview');
    }

    /**
     * 返回prepare的数据
     */
    public function getPrepare()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        if (empty($company)) {
            echo '分公司为空，退出！';
            return;
        }

        $this->assign('company', $company);

        $userid = $_SESSION['userid'];
        //查询角色ID
        $roleuserModel = D('role_user');
        $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
        $roleid = $roleuserResult['role_id'];

        //查询角色的功能
        $accessModel = D('access');
        $where = array();
        $where['role_id'] = $roleid;
        $accessResult = $accessModel->field('node_id')->where($where)->select();
        foreach ($accessResult as $value) {
            $accessArr[] = $value['node_id'];
        }
        //节点表
        $nodeModel = D('node');
        //打印功能
        $nodePrinter = $nodeModel->where("name='printer'")->find();
        $nodeidPrinter = $nodePrinter['id'];
        if (in_array($nodeidPrinter, $accessArr)) {
            $this->PrinterOn = "开启";
            $_SESSION['PrintOn'] = '开启';
        }

        $productsprepareModel = D('productsprepare');
        //返回所有的订单
        $where = array();
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $ordersnResult = $productsprepareModel->distinct(true)->field('ordersn')->where($where)->order('create_time desc')->select();

        $productsprepareArray = array();
        foreach ($ordersnResult as $ordersn) {
            $where = array();
            $where['ordersn'] = $ordersn['ordersn'];
            $productsprepareResult = $productsprepareModel->where($where)->order('name desc')->select();
            $productsprepareArray[] = $productsprepareResult;
        }

        $this->ajaxReturn($productsprepareArray, 'json');
    }

    /**
     * 返回产品的数量规格
     */
    public function getOrderTxt()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        if (empty($company)) {
            echo '分公司为空，退出！';
            return;
        }

        /***
         * $productsprepareModel =  D('productsprepare');
         * //返回所有的订单
         * $where = array();
         * $where['company']  = $company;
         * $where ['domain'] = $this->getDomain();
         * $ordersnResult = $productsprepareModel->distinct(true)->field('ordersn')->where($where)->order('create_time desc')->select();
         */

        $ordertxt = array();

        $orderformModel = D('orderform');
        $where = array();
        $where['company'] = $company;
        $where['sendname'] = array('EQ', '');
        $where['domain'] = $this->getDomain();
        $orderformResult = $orderformModel->field('orderformid,ordersn,ordertxt,custtime,beizhu')->where($where)->order('custtime asc')->select();

        $listData['rows'] = $orderformResult;
        $listData['total'] = count($orderformResult);

        $this->ajaxReturn($listData, 'JSON');
    }

    /**
     * 显示备餐产品和订单备餐
     */
    public function doubleview()
    {
        $this->display('doubleview');
    }

    /**
     * 获取备餐打印用数据
     */
    public function getPrintData()
    {
        $ordersn = $_REQUEST['ordersn'];
        $orderformModel = D('orderform');
        $orderproductsModel = D('orderproducts');

        //开始查询
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderformResult = $orderformModel->where($where)->find();
        $orderproductsResult = $orderproductsModel->where($where)->select();
        $retuanData = array();
        $returnData['orderform'] = $orderformResult;
        $returnData['orderproducts'] = $orderproductsResult;
        $this->ajaxReturn($returnData);
    }

}
