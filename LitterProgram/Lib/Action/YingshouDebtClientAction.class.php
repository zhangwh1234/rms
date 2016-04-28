<?php
/**
 * Created by  zhangwh1234
 * User: lihua
 * Date: 15/6/16
 * 营收服务器的客户端，从网站服务器中提取活动数据
 * /usr/local/php/bin/php /home/lihuasoft/yingshouclient/litter.php YingshouActivityClient/getActivity
 */

class YingshouDebtClientAction  extends  Action{

    /**
     * 架构函数
     *
     * @access 定义日志文件
     */
    public function __construct() {
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'YSClinet_' . date ( 'Y-m-d' ) . '.log';
    }


    /**
     * 获取活动数据
     */
    public function getDebt(){
        // 载入curl函数等
        load ( "@.function" );

        $clientdebtModel = D('clientdebt');

        // 抓取的url
        $url = "http://baiduwaimai.lihua.com/YingshouDebtServer/putDebt/suppliersid/1";

        // 启动curl去抓取
        $resp = curl ( $url );

        $clientdebtArr = json_decode($resp,true);
        foreach($clientdebtArr as $clientdebtInfo){
            $data = array();
            $data['order_id'] = $clientdebtInfo['id'];
            $data['order_sn'] = $clientdebtInfo['order_sn'];
            $data['debt_id'] = $clientdebtInfo['debt_id'];
            $data['date'] = $clientdebtInfo['date'];
            $data['debt_name'] = $clientdebtInfo['debt_name'];
            $data['discount'] = $clientdebtInfo['discount'];

            $where = array();
            $where['order_id'] = $clientdebtInfo['id'];
            $clientdebtResult = $clientdebtModel->where($where)->select();
            if($clientdebtResult){
                //$clientdebtModel->add($data);
            }else{
                $clientdebtModel->create();
                $clientdebtModel->add($data);
            }
            var_dump($clientdebtModel->getLastSql());
            // 确认订单
            $confirmUrl = 'http://baiduwaimai.lihua.com/YingshouDebtServer/confirmClientdebt/id/' . $clientdebtInfo['id'] . '.html';
            var_dump($confirmUrl);
            //Log::write ( '确认订单：' . $confirmUrl, LOG::INFO, LOG::FILE, $this->LogFile );
            $resp = curl ( $confirmUrl );
        }
    }

    /**
     *  这个函数，是从网站服务器中或者百度或者饿了吗等的活动数据，以便分公司计算各自分摊的金额
     */
    public function getActivity(){
        // 载入curl函数等
        load ( "@.function" );

        $activityModel = D('activity');
        // 抓取的url
        $url = "http://baiduwaimai.lihua.com/YingshouActivityServer/putActivity/suppliersid/1";

        // 启动curl去抓取
        $resp = curl ( $url );

        $activityArr = json_decode($resp,true);
        foreach($activityArr as $activityInfo){
            $data = array();
            $data['order_id'] = $activityInfo['id'];
            $data['order_sn'] = $activityInfo['order_sn'];
            $data['activities_id'] = $activityInfo['activities_id'];
            $data['date'] = $activityInfo['date'];
            $data['activities_name'] = $activityInfo['activities_name'];
            $data['discount'] = $activityInfo['discount'];

            $where = array();
            $where['order_id'] =  $activityInfo['id'];
            $activityResult =  $activityModel->where($where)->select();
            if($activityResult){
                //$clientdebtModel->add($data);
            }else{
                $activityModel->create();
                $activityModel->add($data);
            }

            var_dump($activityModel->getLastSql());
            // 确认订单
            $confirmUrl = 'http://baiduwaimai.lihua.com/YingshouActivityServer/confirmActivities/id/' . $activityInfo['id'] . '.html';
            var_dump($confirmUrl);
            //Log::write ( '确认订单：' . $confirmUrl, LOG::INFO, LOG::FILE, $this->LogFile );
            $resp = curl ( $confirmUrl );
        }
    }


}