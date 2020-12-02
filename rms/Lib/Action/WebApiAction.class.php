<?php

/**
 * Created by zhangwh
 * User: lihua
 * Date: 2020-09-11
 * Time: 下午
 * 订单系统接口操作
 */
class WebApiAction extends Action
{

    public function index()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'WebApi_' . date('Y_m_d') . ".log";

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);

        // $data['message'] = 'ok';
        // $this->ajaxReturn($data,'JSON');

        if (empty($_POST)) {
            var_dump('hello,I miss you!');
            exit();
        }

        // 保存到日志中
        Log::write('获得饿了吗外卖推送', Log::INFO, Log::FILE, $LogFile);
        $elmJson = json_encode($_REQUEST);
        Log::write($elmJson, Log::INFO, Log::FILE, $LogFile);

        $data['message'] = 'ok';
        $this->ajaxReturn($data, 'JSON');
    }

    public function Message()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'WebApi_' . date('Y_m_d') . ".log";

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);

        $type = $_REQUEST['type'];
        $orderid = $_REQUEST['ordersn'];
        $city = $_REQUEST['city'];

        // 保存到日志中
        Log::write('获得丽华外卖推送', Log::INFO, Log::FILE, $LogFile);

        if (empty($type)) {
            Log::write('HELLO,I MISS YOU!', Log::INFO, Log::FILE, $LogFile);
            $data['message'] = 'error';
            $this->ajaxReturn($data, 'JSON');
            exit();
        }

        if ($type == '45') { // 用户申请催单
            Log::write('开始处理', Log::INFO, Log::FILE, $LogFile);
            $connect_str = C('RMS_CONNECT');
            $bjconnect_str = C('bjlihuaerpcom');
            $otherconnect_str = C('OTHER_CONNECTSTR');

            if ($city == 'bj') { // 北京的处理
                $connect_db = $bjconnect_str;
            } else { // 其他地区的处理
                $connect_db = $connect_str;
            }
            // 获取订单的地址等信息
            $where = array();
            $where['ordersn'] = $orderid;
            $orderformModel = M('orderform', 'rms_', $connect_db);
            $orderformResult = $orderformModel->where($where)->find();

            if ($orderformResult) {
                // 设置订单
                $where = array();
                $where['ordersn'] = $orderid;
                $data = array();
                $data['state'] = '催送';
                $data['hurrytime'] = date('H:i:s');
                $data['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                $orderformModel->where($where)->save($data);

                // 记入操作到action中
                $orderactionModel = M('orderaction', 'rms_', $connect_db);
                $action = array();
                $action['orderformid'] = $orderformResult['orderformid']; // 订单号
                $action['ordersn'] = $orderid;
                $action['action'] = '饿了吗催送订单 ';
                $action['logtime'] = date('H:i:s');
                $orderactionModel->create();
                $result = $orderactionModel->add($action);

                // 记入到催餐表中orderhurry中
                $orderhurryModel = M('orderhurry', 'rms_', $connect_db);
                $data = array();
                $data['orderformid'] = $orderformResult['orderformid']; // 订单号
                $data['ordersn'] = $orderid;
                $data['hurrytime'] = date('H:i:s');
                $orderhurryModel->create();
                $result = $orderhurryModel->add($data);

                // 插入通知
                /**
                $where = "  ((trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') ";
                $userModel = M('user', 'rms_', $connect_db);
                $userResult = $userModel->where($where)->select();
                foreach ($userResult as $userValue) {
                $data = array();
                $data['sender'] = $userValue['name'];
                $data['status'] = 0;
                $data['content'] = '客户订单:' . $orderformResult['address'] . ' 催送！';
                $data['time'] = date('H:i:s');
                $data['domain'] = $orderformResult['domain'];

                // 保存消息表
                $messagesModel = M('messages', 'rms_', $connect_db);
                $result = $messagesModel->create();
                $result = $messagesModel->add($data);
                }
                 **/

                $data = array();
                $data['message'] = '处理完成';
                $data['code'] = 'ok';
                $this->ajaxReturn($data, 'JSON');

            } else {
                $data = array();
                $data['message'] = '信息不全';
                $data['code'] = 'error';
                $this->ajaxReturn($data, 'JSON');

            }

        }

        if ($type == '20') { // 用户申请退单

            Log::write('开始处理', Log::INFO, Log::FILE, $LogFile);
            $connect_str = C('RMS_CONNECT');
            $bjconnect_str = C('bjlihuaerpcom');
            $otherconnect_str = C('OTHER_CONNECTSTR');

            if ($city == 'bj.lihuaerp.com') { // 北京的处理
                $connect_db = $bjconnect_str;
            } else { // 其他地区的处理
                $connect_db = $connect_str;
            }
            // 获取订单的地址等信息
            $where = array();
            $where['ordersn'] = $orderid;
            $orderformModel = M('orderform', 'rms_', $connect_db);
            $orderformResult = $orderformModel->where($where)->find();

            if ($orderformResult) {
                // 设置订单
                $where = array();
                $where['ordersn'] = $orderid;
                $data = array();
                $data['state'] = '订餐';
                $data['company'] = '';
                $data['beizhu'] = '用户申请退单，请及时处理！';
                $data['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                $orderformModel->where($where)->save($data);

                // 记入操作到action中
                $orderactionModel = M('orderaction', 'rms_', $connect_db);
                $action = array();
                $action['orderformid'] = $orderformResult['orderformid']; // 订单号
                $action['ordersn'] = $orderid;
                $action['action'] = '用户申请退单';
                $action['logtime'] = date('H:i:s');
                $orderactionModel->create();
                $result = $orderactionModel->add($action);

                // 插入通知
                $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') ";
                $userModel = M('user', 'rms_', $connect_db);
                $userResult = $userModel->where($where)->select();

                foreach ($userResult as $userValue) {
                    $data = array();
                    $data['sender'] = $userValue['name'];
                    $data['status'] = 0;
                    $data['content'] = '用户订单:' . $orderformResult['address'] . ' 申请退单！';
                    $data['time'] = date('H:i:s');
                    $data['domain'] = $orderformResult['domain'];

                    // 保存消息表
                    $messagesModel = M('messages', 'rms_', $connect_db);
                    $result = $messagesModel->create();
                    $result = $messagesModel->add($data);
                }

                $data = array();
                $data['message'] = '处理完成';
                $data['code'] = 'ok';
                $this->ajaxReturn($data, 'JSON');

            } else {
                $data = array();
                $data['message'] = '信息不全';
                $data['code'] = 'error';
                $this->ajaxReturn($data, 'JSON');

            }
        }

    }

}
