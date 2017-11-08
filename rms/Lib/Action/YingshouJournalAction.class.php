<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/12
 * Time: 上午11:11
 * 财务结账模块
 */

class YingshouJournalAction extends YingshouAction{


    /**
     * listview
     * 覆盖listview
     */
    public function listview(){
        $this->listviewTwo();
    }


    /**
     * 财务结账计算
     */
    public function journalCalculate(){
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //日期
        $journalDate = $_REQUEST['journal_date'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();


        $reveueConnectDb = $this->connectReveueDb($journalDate);

        // 连接数据库
        $revparmgrModel = M("revparmgr_".substr($journalDate,5,2), " ", $reveueConnectDb);
        $journalModel = M("journal_".substr($journalDate,5,2), " ", $reveueConnectDb);
        $financeresultModel = M("financeresult", " ", $reveueConnectDb);

        $orderformModel = M("orderform_" . substr($journalDate, 5, 2), "rms_", $rmsConnectDb);
        $ordergoodsModel = M("orderproducts_" . substr($journalDate, 5, 2), "rms_", $rmsConnectDb);


        //如果没有结账数据,抛出错误
        if(empty($revparmgrResult)){
            $data = array();
            $data['result'] = '没有结账数据';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $financeresultModel->create();
            $financeresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn ($res);
        }

        /**
         * 核对外送订单是否全部有送餐员的名字,如果还有订单没有配送,就输出错误信息
         */
        $where = array();
        $where['custdate'] = $journalDate;
        $where['sendname']  = array('EQ','');
        $orderformResult = $orderformModel->where($where)->select();
        if (count($orderformResult) > 0) {
            //$revparmgrresultModel->where(1)->delete();
            //还有订单没有配送,输出错误信息
            foreach ($orderformResult as $orderform) {
                $insertStr = '订单:' . $orderform['address'] . ' ' . $orderform['ordertxt'] . '还没有派单,无法结账';
                $data = array();
                $data['result'] = $insertStr;
                $data['domain'] = $this->getDomain();
                $financeresultModel->create();
                $financeresultModel->add($data);
            }
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        };

        /*
         * 检查外送订单是否有没有结账的订单，主要检查订单系统中结账字段
         */
        $where = array();
        $where['custdate'] = $journalDate;
        $where['isjiezhang']  = 0;
        $orderformResult = $orderformModel->where($where)->select();
        if (count($orderformResult) > 0) {
            //$revparmgrresultModel->where(1)->delete();
            //还有订单没有配送,输出错误信息
            foreach ($orderformResult as $orderform) {
                $insertStr = '订单:' . $orderform['address'] . ' ' . $orderform['ordertxt'] . $orderform['company'] . '还没有结账,无法结账';
                $data = array();
                $data['result'] = $insertStr;
                $data['domain'] = $this->getDomain();
                $financeresultModel->create();
                $financeresultModel->add($data);
            }
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        };

        /**
         * 从revparmgr结账表中汇总,进行总的财务结账
         */
        $where = array();
        $where['date'] = $journalDate;
        $revparmgrResult = $revparmgrModel->where($where)
            ->sum(turnover,roomservice,dingingroom,workinglunch,cash);
        foreach($revparmgrResult as $revparmgr){
            $data = array();
            $data ['turnover'] = $revparmgr['turnover'];

            $data ['diningroom'] = $revparmgr['diningroom'];
            $data ['workinglunch'] = $revparmgr ['workinglunch'];
            $data ['cash'] = $revparmgr ['cash'];
            $data ['date'] = $journalDate;
            $data ['domain'] = $this->getDomain();
            $journalModel->create();
            $journalModel->add($data);
        }

        $res = array();
        $res['state'] = 1;
        $this->ajaxReturn ($res);
    }

}