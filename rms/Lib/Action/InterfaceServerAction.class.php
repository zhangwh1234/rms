<?php

/**
 * Created by zhangwh1234.
 * User: lihua
 * Date: 16/3/14
 * Time: 上午10:21
 * 微信短信服务程序
 * 测试案例:http://localhost/rms/index.php/InterfaceServer/getCzMsg/token/lihua1
 * 2016-09-08修改,添加sendnameapp实例
 */
class InterfaceServerAction extends Action
{

    /**
     * 营收系统获取信息
     */
    public function YingshouGetOrderForm()
    {
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
              
        // 实例化订单表
        $orderformModel = M('orderform','rms_',$connect_str);
        // 实例化订货表
        $orderproductsModel = M('orderproducts','rms_',$connect_str);
        //活动表
        $orderactivity_model = M('orderactivity', 'rms_', $connect_str);
        //支付表
        $orderpayment_model = M('orderpayment', 'rms_', $connect_str);
        // 实例化订单发送表
        $orderyingshouexchangeModel = M('orderyingshouexchange','rms_',$connect_str);
        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;
        $orderform = array();
        // 开始查询
        $where = array();
        $where ['status'] = 0;
        $where ['domain'] =  $this->getDomain($_REQUEST['domain']);
        $where['ordersn']  = array('neq','');
        $orderyingshouexchangeResult = $orderyingshouexchangeModel->where($where)->limit(100)->select();
        foreach ($orderyingshouexchangeResult as $value) {
            $ordersn = $value ['ordersn'];
            $where = array();
            $where ['ordersn'] = $ordersn;
            $orderformResult = $orderformModel->where($where)->find();
            $orderproductsResult = $orderproductsModel->where($where)->select();
            $orderactivity = $orderactivity_model->where($where)->select();
            $orderpayment = $orderpayment_model->where($where)->select();
            $orderformResult ['orderproducts'] = $orderproductsResult;
            $orderformResult ['orderactivity'] = $orderactivity;
            $orderformResult ['orderpayment'] = $orderpayment;
            $orderform [$value['id']] = $orderformResult;
        }

        if (!empty ($orderyingshouexchangeResult)) {
            $orderformArray ['success'] = 'success';
            $orderformArray ['result'] = $orderform;
            echo json_encode($orderformArray);
        } else {
            $orderformArray ['error'] = 1;
            $orderformArray ['msg'] = 'no date';
            echo json_encode($orderformArray);
        }
    }


    // 营收订单数据收到验证
    public function YingshouSetOrderForm()
    {
        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;
        // 订单号
        $orderformid = $_REQUEST ['orderformid'];
        
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        // 实例化订单发送表
        $orderyingshouexchangeModel = M('orderyingshouexchange','rms_',$connect_str);  //D('Orderyingshouexchange'); 
        $where = array();
        $where ['id'] = $orderformid;
        $data = array();
        $data ['status'] = 1;
        $result = $orderyingshouexchangeModel->where($where)->save($data);
        if ($result) {
        }
        echo json_encode($orderformArray);
    }

    //营收获取打印单号信息
    public function YingshouGetOrderPrint(){

        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;

        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        // 实例化订单表
        $orderformModel = M('orderform','rms_',$connect_str);
        // 实例化打印表
        $orderprinterModel = M('orderprinter','rms_',$connect_str);


        $where = array();
        $where ['status'] = 0;
        $where ['domain'] =  $this->getDomain($_REQUEST['domain']);
        $orderprinter = $orderprinterModel->where($where)->limit(100)->select();
        foreach($orderprinter as $printerValue){
            $ordersn = $printerValue['ordersn'];
            $where = array();
            $where['ordersn'] = $ordersn;
            $orderform  = $orderformModel->where($where)->find();
            if(!empty($orderform)){
                $data = array();
                $data['getorderid'] = $printerValue['printnumber']; //打印号
                $data['orderid'] = $orderform['ordersn']; //订单号
                $data['address'] = $orderform['address'];
                $data['money'] = $orderform['totalmoney'];
                $data['company'] = $orderform ['company'];
                $data ['date'] = date('Y-m-d');
                $data['ap'] = $orderform['ap'];
                $orderformArray[$printerValue['orderprintid']] = $data;
            }
        }

        if (!empty ($orderprinter)) {
            $orderformArray ['success'] = 'success';
            $orderformArray ['result'] = $orderformArray;
            echo json_encode($orderformArray);
        } else {
            $orderformArray ['error'] = 1;
            $orderformArray ['msg'] = 'no date';
            echo json_encode($orderformArray);
        }


    }

    //营收确认打印信息
    public function YingshouSetOrderPrint(){
        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;

        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        // 实例化打印表
        $orderprinterModel = M('orderprinter','rms_',$connect_str);

        $orderprintid = $_REQUEST['orderprintid'];
        $where = array();
        $where['orderprintid'] = $orderprintid;
        $data = array();
        $data ['status'] = 1;

        $result = $orderprinterModel->where($where)->save($data);
        if ($result) {
        }
        echo json_encode($orderformArray);

    }

    /**
     * 小助手获取订单
     */
    public function assisGetOrderForm(){
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domaintmp = str_replace('.','',$domain);
        $connect_str = C($domaintmp);
        
        // 实例化订单表
        $orderformModel = M('orderform','rms_',$connect_str);
        // 实例化订货表
        $orderproductsModel = M('orderproducts','rms_',$connect_str);
        //活动表
        $orderactivity_model = M('orderactivity', 'rms_', $connect_str);
        //支付表
        $orderpayment_model = M('orderpayment', 'rms_', $connect_str);
        // 实例化订单发送表
        $orderyingshouexchangeModel = M('orderyingshouexchange','rms_',$connect_str);
        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;
        $orderform = array();
        // 开始查询
        $where = array();
        $where ['assisstatus'] = 0;
        $where ['domain'] = $_REQUEST['domain'];
        $orderyingshouexchangeResult = $orderyingshouexchangeModel->where($where)->limit(1)->select();
        foreach ($orderyingshouexchangeResult as $value) {
            $ordersn = $value ['ordersn'];
            $where = array();
            $where ['ordersn'] = $ordersn;
            $orderformResult = $orderformModel->where($where)->find();
            $orderproductsResult = $orderproductsModel->where($where)->select();
            $orderactivity = $orderactivity_model->where($where)->select();
            $orderpayment = $orderpayment_model->where($where)->select();
            $orderformResult ['orderproducts'] = $orderproductsResult;
            $orderformResult ['orderactivity'] = $orderactivity;
            $orderformResult ['orderpayment'] = $orderpayment;
            $orderform [$value['id']] = $orderformResult;
        }

        if (!empty ($orderyingshouexchangeResult)) {
            $orderformArray ['success'] = 'success';
            $orderformArray ['result'] = $orderform;
            echo json_encode($orderformArray);
        } else {
            $orderformArray ['error'] = 1;
            $orderformArray ['msg'] = 'no data';
            echo json_encode($orderformArray);
        }
    }

    /**
     * 小助手设置消息
     */
    public function assisSetOrderForm(){
        // 定义返回数据
        $orderformArray = array();
        $orderformArray ['error'] = 0;
        // 订单号
        $orderformid = $_REQUEST ['orderformid'];
        
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        // 实例化订单发送表
        $orderyingshouexchangeModel = M('orderyingshouexchange','rms_',$connect_str); //D('Orderyingshouexchange');
        $where = array();
        $where ['id'] = $orderformid;
        $data = array();
        $data ['assisstatus'] = 1;
        $result = $orderyingshouexchangeModel->where($where)->save($data);
        if ($result) {
        }
        echo json_encode($orderformArray);
    }

    /**
     *  小助手获取送餐消息
     */
    public function assisGetMsg(){
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        $smsmgr_model = M('smsmgr', 'rms_', $connect_str);
        $where = array();
        $where['assissend'] = 0;
        $where['domain'] = $this->getDomain($_REQUEST['domain']);
        $smsmgr = $smsmgr_model->where($where)->limit(3)->select();
        $msg_json = json_encode($smsmgr);
        echo $msg_json;
    }

    /**
     * 小助手设置送餐消息
     */
    public function assisSetMsg(){
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        $smsmgr_model = M('smsmgr', 'rms_', $connect_str);
        $smsmgrid = $_REQUEST['smsmgrid'];
        if (empty($smsmgrid)) {
            return;
        }
        $where = array();
        $where['smsmgrid'] = $smsmgrid;
        $data = array();
        $data['assissend'] = 1;
        $smsmgr_model->where($where)->save($data);
        echo 'ok';
    }

    /***
     * 获取msg数据，发送json
     * http://localhost/rms/index.php/InterfaceServer/getMsg/token/lihua1/domain/4
     * 服务器测试命令：http://nj.lihuaerp.com/index.php/InterfaceServer/getMsg/token/lihua1/domain/4
     */
    public function getMsg()
    {
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        $smsmgr_model = M('smsmgr', 'rms_', $connect_str);
        $where = array();
        $where['issend'] = 0;
        $where['domain'] =  $this->getDomain($_REQUEST['domain']);
        $smsmgr = $smsmgr_model->where($where)->limit(10)->select();
        $msg_json = json_encode($smsmgr);
        echo $msg_json;
    }

    //设置消息为已经发送状态
    //测试命令:http://localhost/rms/index.php/WeChatMsgServer/setMsg/smsmgrid/281/token/lihua1
    function setMsg()
    {
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);
        
        $smsmgr_model = M('smsmgr', 'rms_', $connect_str);
        $smsmgrid = $_REQUEST['smsmgrid'];
        if (empty($smsmgrid)) {
            return;
        }
        $where = array();
        $where['smsmgrid'] = $smsmgrid;
        $data = array();
        $data['issend'] = 1;
        $smsmgr_model->where($where)->save($data);
        echo 'ok';
    }

    /***
     * 获取sendname数据，发送json
     * http://localhost/rms/index.php/InterfaceServer/getSendnameApp/token/lihua1/domain/4
     * 服务器测试命令：http://nj.lihuaerp.com/index.php/InterfaceServer/getSendnameApps/token/lihua1/domain/4
     */
    public function getSendnameApp()
    {
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);

        $sendnameapp_model = M('sendnameapp', 'rms_', $connect_str);
        $where = array();
        $where['state'] = 0;
        $where['domain'] =  $this->getDomain($_REQUEST['domain']);
        $sendnameapp = $sendnameapp_model->where($where)->limit(10)->select();
        $sendnameapp_json = json_encode($sendnameapp);
        echo $sendnameapp_json;
    }

    //设置sendnameapp为已经发送状态
    //测试命令:http://localhost/rms/index.php/WeChatMsgServer/setSendnameApp/sendnameappid/281/token/lihua1
    function setSendnameApp()
    {
        if ($_REQUEST['token'] !== 'lihua1') {
            return;
        }
        $domain = $this->getDomain($_REQUEST['domain']);
        $domain = str_replace('.','',$domain);
        $connect_str = C($domain);

        $sendnameapp_model = M('sendnameapp', 'rms_', $connect_str);
        $sendnameappid = $_REQUEST['sendnameappid'];
        if (empty($sendnameappid)) {
            return;
        }
        $where = array();
        $where['sendnameappid'] = $sendnameappid;
        $data = array();
        $data['state'] = 1;
        $sendnameapp_model->where($where)->save($data);
        var_dump($sendnameapp_model->getLastSql());
        echo 'ok';
    }

    /*
     * 返回标识
     */
    function getDomain($domain)
    {
        switch ($domain) {
        	case 1:
        		$domain = 'bj.lihuaerp.com';
        		break;
        	case 3:
        		$domain = 'sh.lihuaerp.com';
        		break;
            case 4:
                $domain = 'cz.lihuaerp.com';
                break;
            case 8:
                $domain = 'wx.lihuaerp.com';
                break;
            case 5:
                $domain = 'sz.lihuaerp.com';
                break;
            case 6:
                $domain = 'nj.lihuaerp.com';
                break;
            case 9:
                $domain = 'gz.lihuaerp.com';
                break;
        }
        return $domain;
    }
}