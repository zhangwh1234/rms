<?php
/**
 * Created by zhangwh1234
 * User: lihua
 * Date: 15/6/16  Time: 上午10:11
 * 营收活动的提取服务端，守候在网站系统中
 */

class YingshouDebtServerAction extends Action
{

    // 首程序
    public function index()
    {
        $this->putDebt();
    }

    /**
     * 获取欠单数据，传输
     */
    public function putDebt(){
        //Log::write ( '结账数据传输开始', LOG::INFO, LOG::FILE );
        $clientdebtModel = D('OrderClientdebt');
        $where = array();
        $where['yingshou'] = 0;
        $clientdebtResult = $clientdebtModel->where($where)->limit(10)->select();
        echo json_encode ( $clientdebtResult );
    }


    // 活动数据验证
    public function confirmClientdebt() {
        $id = $_REQUEST['id'];
        $clientdebtModel = D('OrderClientdebt');
        $where = array();
        $where['id'] = $id;
        $data = array();
        $data['yingshou'] = 1;
        $clientdebtModel->where($where)->save($data);
    }

    /**
     * 获取活动数据，传输
     */
    public function putActivity(){
        //Log::write ( '结账数据传输开始', LOG::INFO, LOG::FILE );
        $activitiesModel = D('OrderActivities');
        $where = array();
        $where['yingshou'] = 0;
        $where['suppliers_id']= $_REQUEST['suppliersid'];
        $activitiesResult = $activitiesModel->where($where)->limit(100)->select();
        echo json_encode ( $activitiesResult );
    }


    // 活动数据验证
    public function confirmActivities() {
        $id = $_REQUEST['id'];
        $activitiesModel = D('OrderActivities');
        $where = array();
        $where['id'] = $id;
        $data = array();
        $data['yingshou'] = 1;
        $activitiesModel->where($where)->save($data);
    }


    /**
     * 获取支付数据，传输
     */
    public function putPayment(){
        //Log::write ( '结账数据传输开始', LOG::INFO, LOG::FILE );
        $paymentModel = D('OrderPayment');
        $where = array();
        $where['yingshou'] = 0;
        $paymentResult = $paymentModel->where($where)->limit(10)->select();
        echo json_encode ( $paymentResult );
    }


    // 活动数据验证
    public function confirmPayment() {
        $id = $_REQUEST['id'];
        $paymentModel = D('OrderPayment');
        $where = array();
        $where['id'] = $id;
        $data = array();
        $data['yingshou'] = 1;
        $paymentModel->where($where)->save($data);
    }
}
