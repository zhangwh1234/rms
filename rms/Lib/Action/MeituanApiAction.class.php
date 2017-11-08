<?php

/**
 * Created by zhangwh
 * User: lihua
 * Date: 2017--7-22
 * Time: 上午11:54
 * 美团的API的callbackurl
 */
class MeituanApiAction extends Action {
    public function index() {

        // 定义日志文件
        $LogFile = LOG_PATH . 'MeituanApi_' . date ( 'Y_m_d' ) . ".log";

        // 记入日志
        Log::write ( '有人在访问：', Log::INFO, Log::FILE, $LogFile );
        Log::write ( '客户浏览器：' . $_SERVER ['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile );
        Log::write ( '来源IP：' . $_SERVER ['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile );

        if (empty ( $_POST )) {
            //var_dump ( 'hello,I miss you!' );
            //exit ();
        }


        $otherplatformModel = M ( 'meituanorderid', ' ','mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/rms_otherplatform#utf8' );
        // 保存到日志中
        Log::write ( '获得美团外卖推送', Log::INFO, Log::FILE, $LogFile );
        Log::write ( 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], Log::INFO, Log::FILE, $LogFile );

        $meituanJson = json_encode ( $_REQUEST );
        Log::write ( $meituanJson, Log::INFO, Log::FILE, $LogFile );

        //将税号插入到订单中
        if ($_REQUEST ['order_id']) {
            Log::write ( $_REQUEST ['order_id'], Log::INFO, Log::FILE, $LogFile );
            Log::write ( $_REQUEST['city_id'], Log::INFO, Log::FILE, $LogFile );
            if($_REQUEST['city_id'] == "110100"){
                Log::write ( '北京', Log::INFO, Log::FILE, $LogFile );
            }else{
                Log::write ( 'other', Log::INFO, Log::FILE, $LogFile );
            }

            $ordersn = $_REQUEST ['order_id'];
            Log::write ( $_REQUEST ['taxpayer_id'], Log::INFO, Log::FILE, $LogFile );
            $where = array ();
            $where ['ordersn'] = $ordersn;
            $data = array ();
            //$data ['gmf_nsrsbh'] = $_REQUEST['taxpayer_id'];
            $data ['ordersn'] = $ordersn;
            $data ['insertdate'] = date('Y-m-d H:i:s');
            $data ['domain'] = $this->MeituanGetDomain($_REQUEST['city_id']);
            $otherplatformModel->create ();
            $otherplatformModel->add ( $data );
            $sql = $otherplatformModel->getlastSql ();
            return;
            if ( ! empty ( $_REQUEST ['taxpayer_id'] )) {
                Log::write ( $_REQUEST ['taxpayer_id'], Log::INFO, Log::FILE, $LogFile );
                $where = array ();
                $where ['ordersn'] = $ordersn;
                $data = array ();
                $data ['gmf_nsrsbh'] = $_REQUEST['taxpayer_id'];
                $data ['ordersn'] = $ordersn;
                $data ['insertdate'] = date('Y-m-d H:i:s');
                $data ['domain'] = $this->MeituanGetDomain($_REQUEST['city_id']);
                $otherplatformModel->create ();
                $otherplatformModel->add ( $data );
                $sql = $otherplatformModel->getlastSql ();
                Log::write ( $sql, Log::INFO, Log::FILE, $LogFile );
                Log::write ( $_REQUEST['city_id'], Log::INFO, Log::FILE, $LogFile );
                return;
                if ($_REQUEST ['city_id'] == '110100') {
                    $bjrmsModel = M ( 'orderform', 'rms_', C ( 'bjlihuaerpcom' ) );

                } else {
                    $czrmsModel = M ( 'orderform', 'rms_','mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/czrms#utf8' );
                    $czrmsModel->create ();
                    $czrmsModel->add ( $data );
                    $sql = $czrmsModel->getlastSql ();
                    Log::write ( $sql, Log::INFO, Log::FILE, $LogFile );
                    Log::write ( $_REQUEST['city_id'], Log::INFO, Log::FILE, $LogFile );
                }
            }
        }

        $this->Message();
    }

    public function Message() {
        $data = array();
        $data ['message'] = 'ok';
        $this->ajaxReturn ( $data );
    }


    /**
     * 催送订单
     */
    public function hurry(){
        // 定义日志文件
        $LogFile = LOG_PATH . 'MeituanApi_' . date ( 'Y_m_d' ) . ".log";

        // 记入日志
        Log::write ( '催送有人在访问：', Log::INFO, Log::FILE, $LogFile );
        Log::write ( '催送客户浏览器：' . $_SERVER ['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile );
        Log::write ( '催送来源IP：' . $_SERVER ['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile );

        if (empty ( $_POST )) {
            //var_dump ( 'hello,I miss you!' );
            //exit ();
        }

        // 保存到日志中
        Log::write ( '获得美团催送推送', Log::INFO, Log::FILE, $LogFile );
        Log::write ( 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], Log::INFO, Log::FILE, $LogFile );

        $meituanJson = json_encode ( $_REQUEST );
        Log::write ( $meituanJson, Log::INFO, Log::FILE, $LogFile );

        $connect_str = C('RMS_CONNECT');
        $bjconnect_str = C('bjlihuaerpcom');

        if ($_REQUEST ['order_id']) {
            $orderid = $_REQUEST ['order_id'];
            // 获取订单的地址等信息
            $where = array();
            $where['ordersn'] = $orderid;
            $orderformModel = M('orderform', 'rms_', $connect_str);
            $orderformResult = $orderformModel->where($where)->find();
            $connect_db = $connect_str;
            if(empty($orderformResult)){
                // 获取是否是北京的订单
                $where = array();
                $where['ordersn'] = $orderid;
                $orderformModel = M('orderform', 'rms_', $bjconnect_str);
                $orderformResult = $orderformModel->where($where)->find();
                $connect_db = $bjconnect_str;
            }
            //开始处理催送
            if ($orderformResult) {
                // 设置订单
                $where = array();
                $where['ordersn'] = $orderid;
                $data = array();
                $data['state'] = '催送';
                $data ['hurrynumber'] = array(
                    'exp',
                    'hurrynumber+1'
                );
                $data ['hurrytime'] = date('H:i:s');
                $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                $orderformModel->where($where)->save($data);

                // 记入操作到action中
                $orderactionModel = M('orderaction', 'rms_', $connect_db);
                $action = array();
                $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
                $action ['ordersn'] = $orderid;
                $action ['action'] = '美团催送订单 ';
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
        $this->Message();
    }

    /**
     * 美团取消订单的接口
     * @param $city_id
     * @return string
     */
    public function cancel(){
        // 定义日志文件
        $LogFile = LOG_PATH . 'MeituanApi_' . date ( 'Y_m_d' ) . ".log";

        // 记入日志
        Log::write ( '催送有人在访问：', Log::INFO, Log::FILE, $LogFile );
        Log::write ( '催送客户浏览器：' . $_SERVER ['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile );
        Log::write ( '催送来源IP：' . $_SERVER ['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile );

        if (empty ( $_POST )) {
            //var_dump ( 'hello,I miss you!' );
            //exit ();
        }

        // 保存到日志中
        Log::write ( '获得美团催送推送', Log::INFO, Log::FILE, $LogFile );
        Log::write ( 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], Log::INFO, Log::FILE, $LogFile );


        $meituanJson = json_encode ( $_REQUEST );
        Log::write ( $meituanJson, Log::INFO, Log::FILE, $LogFile );

        $connect_str = C('RMS_CONNECT');
        $bjconnect_str = C('bjlihuaerpcom');

        if ($_REQUEST ['order_id']) {
            $orderid = $_REQUEST ['order_id'];
            // 获取订单的地址等信息
            $where = array();
            $where['ordersn'] = $orderid;
            $orderformModel = M('orderform', 'rms_', $connect_str);
            $orderformResult = $orderformModel->where($where)->find();
            $connect_db = $connect_str;
            if (empty($orderformResult)) {
                // 获取是否是北京的订单
                $where = array();
                $where['ordersn'] = $orderid;
                $orderformModel = M('orderform', 'rms_', $bjconnect_str);
                $orderformResult = $orderformModel->where($where)->find();
                $connect_db = $bjconnect_str;
            }
            //开始处理催送
            if ($orderformResult) {
                // 设置订单
                $where = array();
                $where['ordersn'] = $orderid;
                $data = array();
                $data['state'] = '订餐';
                $data['company'] = '';
                $data ['beizhu'] = '美团用户申请退单，请及时处理！';
                $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
                $orderformModel->where($where)->save($data);

                // 记入操作到action中
                $orderactionModel = M('orderaction', 'rms_', $connect_db);
                $action = array();
                $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
                $action ['ordersn'] = $orderid;
                $action ['action'] = '美团申请退单';
                $action ['logtime'] = date('H:i:s');
                $action ['domain'] = $orderformResult['domain'];
                $orderactionModel->create();
                $result = $orderactionModel->add($action);

                // 插入通知
                $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') and `domain`  = '" . $orderformResult['domain'] . "'";
                $userModel = M('user', 'rms_', $connect_db);
                $userResult = $userModel->where($where)->select();

                foreach ($userResult as $userValue) {
                    $data = array();
                    $data['sender'] = $userValue['name'];
                    $data['status'] = 0;
                    $data['content'] = '美团订单:' . $orderformResult['address'] . ' 申请退单！';
                    $data['time'] = date('H:i:s');
                    $data['domain'] = $orderformResult['domain'];

                    // 保存消息表
                    $messagesModel = M('messages', 'rms_', $connect_db);
                    $result = $messagesModel->create();
                    $result = $messagesModel->add($data);
                }
            }

        }
        $this->Message();
    }


    private function MeituanGetDomain($city_id){
        if($city_id == '110100'){
            return 'bj.lihuaerp.com';
        }
        if($city_id == '310100'){
            return 'sh.lihuaerp.com';
        }
        if($city_id == '320100'){
            return 'nj.lihuaerp.com';
        }
        if($city_id == '320400'){
            return 'cz.lihuaerp.com';
        }
        if($city_id == '440100'){
            return 'gz.lihuaerp.com';
        }
        if($city_id == '320200'){
            return 'wx.lihuaerp.com';
        }
        if($city_id == '320500'){
            return 'sz.lihuaerp.com';
        }
        return $city_id;
    }
}