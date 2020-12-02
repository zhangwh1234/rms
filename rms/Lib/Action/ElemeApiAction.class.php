<?php

/**
 * Created by zhangwh
 * User: lihua
 * Date: 15/6/11
 * Time: 上午11:54
 * 饿了吗新API的callbackurl
 */
class ElemeApiAction extends Action
{

    public function index()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'ElemeApi_' . date('Y_m_d') . ".log";

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
        $LogFile = LOG_PATH . 'New_ElemeApi_' . date('Y_m_d') . ".log";

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);

        // 保存到日志中
        Log::write('获得饿了吗外卖推送', Log::INFO, Log::FILE, $LogFile);
        // Log::write ( $_POST, Log::INFO, Log::FILE, $LogFile );
        // $elmJson = json_encode ( $_POST );
        $elmJson = file_get_contents('php://input');
        // var_dump($elmJson);

        if (empty($elmJson)) {
            Log::write('HELLO,I MISS YOU!', Log::INFO, Log::FILE, $LogFile);
            $data['message'] = 'ok';
            $this->ajaxReturn($data, 'JSON');
            exit();
        }

        Log::write($elmJson, Log::INFO, Log::FILE, $LogFile);

        // 保存到日志中
        Log::write('获得饿了吗外卖推送', Log::INFO, Log::FILE, $LogFile);
        // Log::write ( $_POST, Log::INFO, Log::FILE, $LogFile );
        // $elmJson = json_encode ( $_POST );
        $elmReturn = json_decode($elmJson, true);
        $type = $elmReturn['type'];
        Log::write($type, Log::INFO, Log::FILE, $LogFile);
        $message = $elmReturn['message'];
        $message = json_decode($message, true);
        Log::write($message['id'], Log::INFO, Log::FILE, $LogFile);

        // 开始根据type，进行处理
        $shopid = $message['shopId'];
        $orderid = $message['orderId'];

        if ($type == '45') { // 用户申请催单
            Log::write('开始处理', Log::INFO, Log::FILE, $LogFile);
            $connect_str = C('RMS_CONNECT');
            $bjconnect_str = C('bjlihuaerpcom');
            $otherconnect_str = C('OTHER_CONNECTSTR');

            // 查询商户，获得domain
            $where = array();
            $where['restaurant_id'] = $shopid;
            $elemeshopinfoModel = M('elemeshopinfo', ' ', $otherconnect_str);
            $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();

            if ($elemeshopinfoResult) {
                $domain = $elemeshopinfoResult['domain'];
                if ($domain == 'bj.lihuaerp.com') { // 北京的处理
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
                    $data ['hurrytime'] = date('H:i:s');
                    $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                    $orderformModel->where($where)->save($data);

                    // 记入操作到action中
                    $orderactionModel = M('orderaction', 'rms_', $connect_db);
                    $action = array();
                    $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
                    $action ['ordersn'] = $orderid;
                    $action ['action'] = '饿了吗催送订单 ';
                    $action ['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($action);

                    // 记入到催餐表中orderhurry中
                    $orderhurryModel = M('orderhurry', 'rms_', $connect_db);
                    $data = array();
                    $data ['orderformid'] = $orderformResult['orderformid']; // 订单号
                    $data ['ordersn'] = $orderid;
                    $data ['hurrytime'] = date('H:i:s');
                    $orderhurryModel->create();
                    $result = $orderhurryModel->add($data);

                    // 插入通知
                    /***
                     * $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') and `domain`  = '" . $domain . "'";
                     * $userModel = M('user', 'rms_', $connect_db);
                     * $userResult = $userModel->where($where)->select();
                     * foreach ($userResult as $userValue) {
                     * $data = array();
                     * $data['sender'] = $userValue['name'];
                     * $data['status'] = 0;
                     * $data['content'] = '饿了么订单:' . $orderformResult['address'] . ' 催送！';
                     * $data['time'] = date('H:i:s');
                     * $data['domain'] = $domain;
                     *
                     * // 保存消息表
                     * $messagesModel = M('messages', 'rms_', $connect_db);
                     * $result = $messagesModel->create();
                     * $result = $messagesModel->add($data);
                     * }
                     **/
                }
            }

        }

        if ($type == '20') { // 用户申请退单
            Log::write('开始处理', Log::INFO, Log::FILE, $LogFile);
            $connect_str = C('RMS_CONNECT');
            $bjconnect_str = C('bjlihuaerpcom');
            $otherconnect_str = C('OTHER_CONNECTSTR');

            // 查询商户，获得domain
            $where = array();
            $where['restaurant_id'] = $shopid;
            $elemeshopinfoModel = M('elemeshopinfo', ' ', $otherconnect_str);
            $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();

            if ($elemeshopinfoResult) {
                $domain = $elemeshopinfoResult['domain'];
                if ($domain == 'bj.lihuaerp.com') { // 北京的处理
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
                    $data ['beizhu'] = '饿了吗用户申请退单，请及时处理！';
                    $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                    $orderformModel->where($where)->save($data);

                    // 记入操作到action中
                    $orderactionModel = M('orderaction', 'rms_', $connect_db);
                    $action = array();
                    $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
                    $action ['ordersn'] = $orderid;
                    $action ['action'] = '饿了吗申请退单';
                    $action ['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($action);

                    // 插入通知
                    $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') and `domain`  = '" . $domain . "'";
                    $userModel = M('user', 'rms_', $connect_db);
                    $userResult = $userModel->where($where)->select();

                    foreach ($userResult as $userValue) {
                        $data = array();
                        $data['sender'] = $userValue['name'];
                        $data['status'] = 0;
                        $data['content'] = '饿了么订单:' . $orderformResult['address'] . ' 申请退单！';
                        $data['time'] = date('H:i:s');
                        $data['domain'] = $domain;

                        // 保存消息表
                        $messagesModel = M('messages', 'rms_', $connect_db);
                        $result = $messagesModel->create();
                        $result = $messagesModel->add($data);
                    }
                }
            }
        }

        if ($type == '17') { // 用户申请退单
            Log::write('开始处理', Log::INFO, Log::FILE, $LogFile);
            $connect_str = C('RMS_CONNECT');
            $bjconnect_str = C('bjlihuaerpcom');
            $otherconnect_str = C('OTHER_CONNECTSTR');

            // 查询商户，获得domain
            $where = array();
            $where['restaurant_id'] = $shopid;
            $elemeshopinfoModel = M('elemeshopinfo', ' ', $otherconnect_str);
            $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();

            if ($elemeshopinfoResult) {
                $domain = $elemeshopinfoResult['domain'];
                if ($domain == 'bj.lihuaerp.com') { // 北京的处理
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
                    $data ['beizhu'] = '饿了吗用户免责退款，请及时处理！';
                    $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                    $orderformModel->where($where)->save($data);

                    // 记入操作到action中
                    $orderactionModel = M('orderaction', 'rms_', $connect_db);
                    $action = array();
                    $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
                    $action ['ordersn'] = $orderid;
                    $action ['action'] = '饿了吗申请退单';
                    $action ['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($action);

                    // 插入通知
                    $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') and `domain`  = '" . $domain . "'";
                    $userModel = M('user', 'rms_', $connect_db);
                    $userResult = $userModel->where($where)->select();

                    foreach ($userResult as $userValue) {
                        $data = array();
                        $data['sender'] = $userValue['name'];
                        $data['status'] = 0;
                        $data['content'] = '饿了么订单:' . $orderformResult['address'] . ' 申请退单！';
                        $data['time'] = date('H:i:s');
                        $data['domain'] = $domain;

                        // 保存消息表
                        $messagesModel = M('messages', 'rms_', $connect_db);
                        $result = $messagesModel->create();
                        $result = $messagesModel->add($data);
                    }
                }
            }
        }


        $data = array();
        $data['message'] = 'ok';
        $this->ajaxReturn($data, 'JSON');
        str_replace();

    }

    /**
     * 范例：
     * {"requestId":"200012388447675246","type":45,"appId":11652495,"message":"{\"orderId\":\"3020491076082129115\",\"shopId\":260538,\"remindId\":1026822794,\"userId\":22561490,\"updateTime\":1520913213}","shopId":260538,"timestamp":1520913213763,"signature":"27F4AF4C35BA1EAC2EF2646AE7B2D38B","userId":50047084944126952}
     * {"requestId":"200012424638350240","type":15,"appId":11652495,"message":"{\"orderId\":\"3020536234124757224\",\"state\":\"invalid\",\"shopId\":247346,\"updateTime\":1520998604,\"role\":2}","shopId":247346,"timestamp":1520998604709,"signature":"EDEF0FC895A13EC7E51B5974D498815C","userId":50047085150334727}
     */
}