<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/9
 * Time: 下午4:44
 */

class YingshouDoorBillAction extends YingshouAction
{

    /*
     * 需要返回的字段
     */
    public function returnMainFnPara($focus)
    {
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午'),
        );
        $this->assign('ap', $ap);

        //查询分公司名称，作为门市名称
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';
        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $operator = $userInfo['truename'];

        $createFields = $focus->createFields;
        foreach ($createFields['LBL_DOORBILL_INFORMATION'] as $index => $fields) {
            foreach ($fields as $key => $value) {
                if ($key == 'name') {
                    if ($value == 'code') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = '01';
                    }
                    if ($value == 'name') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $company;
                    }
                    if ($value == 'operator') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $operator;
                    }
                    if ($value == 'date') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = date('Y-m-d');
                    }
                    if ($value == 'ap') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $this->getAp();
                    }
                }
            }
        }
    }

    // 保存工作餐支付数据等其他数据
    public function save_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['date'] = $getDate;
            $data['note'] = $note;
            $data['doorbillid'] = $record;
            $data['domain'] = $this->getDomain();
            if (!empty($code) && !empty($name)) {
                $doorbillpayModel->create();
                $doorbillpayModel->add($data);
            }
        };

    }

    //更新门市支付
    public function update_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);
        $where = array();
        $where['doorbillid'] = $record;
        $where['date'] = $getDate;
        $where['domain'] = $this->getDomain();
        //先删除订单支付表中的数据
        $doorbillpayModel->where($where)->delete();

        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['date'] = $getDate;
            $data['note'] = $note;
            $data['doorbillid'] = $record;
            $data['domain'] = $this->getDomain();
            if (!empty($code) && !empty($name)) {
                $doorbillpayModel->create();
                $doorbillpayModel->add($data);
            }
        };

    }

    //显示工作餐支付信息
    public function get_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['doorbillid'] = $record;
        $where['date'] = $getDate;
        $where['domain'] = $this->getDomain();
        $doorbillpayResult = $doorbillpayModel->where($where)->select();
        $this->assign('doorbillaccounts', $doorbillpayResult);
    }

    //定义删除从表
    public function delete_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['doorbillid'] = $record;
        $where['date'] = $getDate;
        $where['domain'] = $this->getDomain();
        $doorbillpayResult = $doorbillpayModel->where($where)->delete();

    }

}
