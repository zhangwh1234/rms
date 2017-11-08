<?php

/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/6/13
 * Time: 上午10:35
 * 滴滴外卖订餐的接口
 */
class DidiApiAction extends Action
{

    public function test()
    {
        $t = '{"order_id":3458764551552569332,"remark":"","city_name":"\\u65e0\\u9521\\u5e02","create_time":1531066453,"pay_time":1531066461,"day_seq":2,"expected_cook_eta":0,"delivery_eta":1531068359,"app_shop_id":"0","shop_id":3458764751122464805,"delivery_type":1,"order_price":10,"pay_type":1,"real_pay_price":510,"receiver_address":"\\u60e0\\u5c71\\u533a\\u6d1b\\u793e\\u5927\\u6865\\u5357\\u580d\\u6d1b\\u793e\\u6865\\u7f18\\u65c5\\u793e","latitude":31.64875,"longitude":120.19028,"receiver_phone":"13912302008","delivery_price":500,"status":100,"real_price":510,"cover_num":1,"cover_price":0,"complete_time":0,"shop_confirm_time":0,"cancel_time":0,"order_items":[{"app_item_id":"","app_sku_id":"","attr_sold_value":"","item_name":"\\u6d4b\\u8bd5","sku_price":10,"amount":1,"unit":"\\u4efd","item_property":[]}],"shop_poi_name":"\\u6c5f\\u82cf\\u7701\\u65e0\\u9521\\u5e02\\u60e0\\u5c71\\u533a\\u6d1b\\u793e\\u6865\\u7f18\\u65c5\\u793e","shop_name":"Soda\\u6d4b\\u8bd5API\\u8054\\u8c03\\u95e8\\u5e9709","shop_phone":"[\\"13466397087\\"]","activity_info":[],"bill":{"order_price":10,"total_favour_price":0,"real_pay_price":510,"delivery_price":500,"platform_fee":2,"shop_receive":8,"act_order_charge_by_didi":[{"comment":"\\u6ee1\\u51cf\\u8865\\u8d34","fee_type_desc":"\\u6ee1\\u51cf\\u8865\\u8d34","fee_type_id":227,"money_cent":0}],"act_order_charge_by_poi":[{"comment":"\\u5546\\u5bb6\\u8865\\u8d34","fee_type_desc":"\\u5546\\u5bb6\\u8865\\u8d34","fee_type_id":99904,"money_cent":0}]}}';
        $c = stripslashes($t);
        var_dump($c);

        $c = json_decode($t, true);
        var_dump($c);
    }

    public function index()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'DidiApi_' . date('Y_m_d') . ".log";
        $connect = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/czrms#utf8';
        $otherconnect = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/rms_otherplatform#utf8';

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);

        if (empty($_POST)) {
            var_dump('hello,I miss you!');
            exit();
        }

        // 保存到日志中
        Log::write('获得滴滴外卖推送', Log::INFO, Log::FILE, $LogFile);
        $content = json_encode($_POST);
        Log::write($content, Log::INFO, Log::FILE, $LogFile);
        $content = str_replace('\\"', "\"", $content);

        $didiContent = json_decode($content, true);

        Log::write('didContent:', Log::INFO, Log::FILE, $LogFile);
        Log::write($didContent, Log::INFO, Log::FILE, $LogFile);
        Log::write('pst:', Log::INFO, Log::FILE, $LogFile);
        $content = str_replace('\\"', "\"", $_POST['data']);
        Log::write($content, Log::INFO, Log::FILE, $LogFile);

        // 新订单
        if ($_POST['type'] == 'orderNew') {
            $content = str_replace('\\"', "\"", $_POST['data']);
            $content = (string) $content;
            Log::write('orderNew', Log::INFO, Log::FILE, $LogFile);
            Log::write($content, Log::INFO, Log::FILE, $LogFile);
            $content = stripslashes($content);
            $order = json_decode($content, true);
            $this->saveDidiOrder($order, $connect, $otherconnect, $LogFile);
        }

        if ($_POST['type'] == 'orderCancel') {
            $content = str_replace('\\"', "\"", $_POST['data']);
            Log::write('orderCancel', Log::INFO, Log::FILE, $LogFile);
            Log::write($content, Log::INFO, Log::FILE, $LogFile);
            $order = json_decode($content, true);
            $this->cancelDidiOrder($order, $connect, $otherconnect, $LogFile);
        }

        $data = 'success';
        $this->ajaxReturn($data, '');
    }

    /**
     * 保存滴滴订单
     *
     * @param $orderContent 订单数组
     * @param
     *            $connect_str
     * @throws \Exception
     */
    public function saveDidiOrder($orderContent, $connect_str, $otherconnect, $LogFile)
    {

        // 订单表
        $orderform_model = M('orderform', 'rms_', $connect_str);
        // 订货表
        $ordergoods_model = M('orderproducts', 'rms_', $connect_str);
        // 活动表
        $orderactivity_model = M('orderactivity', 'rms_', $connect_str);
        // 支付表
        $orderpayment_model = M('orderpayment', 'rms_', $connect_str);
        // 欠单结账表
        $orderclientdebt_model = M('orderclientdebt', 'rms_', $connect_str);
        // 状态表
        $orderstate_model = M('orderstate', 'rms_', $connect_str);
        // 日志表action中
        $orderaction_model = M('orderaction', 'rms_', $connect_str);
        // 营收状态表
        $orderyingshouexchange_model = M('orderyingshouexchange', 'rms_', $connect_str);
        // 发票
        $invoice_model = M('invoice', 'rms_', $connect_str);
        // 接收状态
        $didiwaimaiorderid_model = M('didiwaimaiorderid', ' ', $otherconnect);

        $orderValue = $orderContent;

        if (empty($orderValue)) {
            Log::write('无数据', Log::INFO, '', $LogFile);
            return;
        }

        /**
         * ***************************
         */
        // 先处理订单主表 order
        $orderform_array = array();
        $orderform_array['ordersn'] = $orderValue['order_id']; // 订单号
        Log::write('ordersn', Log::INFO, '', $LogFile);
        Log::write($orderform_array['ordersn'], Log::INFO, '', $LogFile);

        // 返回城市识别
        $domain = $this->getCity($orderValue['city_name']);

        // 联系人,滴滴没有联系人？
        if (empty($orderValue['clientname'])) {
            $orderform_array['clientname'] = '';
        } else {
            $orderform_array['clientname'] = $orderValue['clientname'];
        }

        // 用户地址
        $orderform_array['address'] = $orderValue['receiver_address'];
        $orderform_array['address'] = $this->ReMoveChar($orderform_array['address']);
        if ($orderValue['bill']['platform_fee']) {
            // 应上海的要求，加上服务费的内容
            $orderform_array['address'] = '(费' . $orderValue['bill']['platform_fee'] /100 . ')' . $orderform_array['address'];
        }

        // 暂时不用
        // 商品的下载数据Extra,附加的优惠
        // 首先处理优惠附加
        $activity = $orderValue['activity_info'];
        foreach ($activity as $arr) {
            $activity_money = $arr['reduce_fee'] / 100;
            $orderform_array['address'] = '(滴-'. $activity_money .')' .$orderform_array['address'] ;
        }

        // 判断是否有货到付款或者在线支付
        if ($orderValue['onlinePaid'] == true) {
            // 地址提示
            $orderform_array['address'] = '(滴滴)' . $orderform_array['address'];
        } else {
            $orderform_array['address'] = '(滴滴)' . $orderform_array['address'];
        }

        // 用户电话
        $orderform_array['telphone'] = $this->ReMoveChar($orderValue['receiver_phone']);
        // 送餐日期
        $orderform_array['custdate'] = date('Y-m-d', $orderValue['delivery_eta']);
        // 要餐时间
        if ($orderValue['delivery_eta']) {
            $orderform_array['custtime'] = date('H:i:s', $orderValue['delivery_eta']);
        } else {
            $orderform_array['custtime'] = date("H:i:s", time() + 60 * 30);
        }

        // 接线员
        $orderform_array['telname'] = '滴滴';
        // 录入日期
        $orderform_array['recdate'] = date('Y-m_d', $orderValue['create_time']);
        // 录入时间
        $orderform_array['rectime'] = date('H:i:s', $orderValue['create_time']);
        // 备注
        $orderform_array['beizhu'] = $orderValue['remark'];
        $orderform_array['beizhu'] = $this->ReMoveChar($orderform_array['remark']);
        // 订单总金额
        $orderform_array['totalmoney'] = ($orderValue['bill']['order_price'] + $orderValue['bill']['delivery_price']) / 100;
        // 送餐费不要输入
        $orderform_array['shippingmoney'] = 0;

        // 滴滴发票信息暂时没有
        if ($orderValue['invoiced']) {
            $orderform_array['invoiceheader'] = $orderValue['invoice'];
        } else {
            $orderform_array['invoiceheader'] = '';
        }
        if (! empty($orderValue['taxpayerId'])) {
            $orderform_array['gmf_nsrsbh'] = $orderValue['taxpayerId'];
        } else {
            $orderform_array['gmf_nsrsbh'] = '';
        }

        if (! empty($orderform_array['invoiceheader'])) {
            $orderform_array['beizhu'] = $orderform_array['beizhu'] . ' 票:' . $orderform_array['invoiceheader'];
        }
        $orderform_array['invoicebody'] = '工作餐';

        // 分公司
        $orderform_array['company'] = $this->getCityShopname($orderValue['app_shop_id'], $connect_str, $domain);
        $orderform_array['ordertxt'] = '';
        $orderform_array['state'] = '订餐';

        // 订单坐标
        $orderform_array['longitude'] = $orderValue['longitude'];
        $orderform_array['latitude'] = $orderValue['latitude'];

        // 根据送餐时间来判断
        if (intval(substr($orderform_array['custtime'], 0, 2)) >= 15)
            $cAp = '下午';
        else
            $cAp = '上午';
        $orderform_array['ap'] = $cAp;

        // 根据suppliers_id来判断,输入domain
        $orderform_array['domain'] = $domain;
        // 订单来源
        $orderform_array['origin'] = '滴滴';

        // 检查是否重复，如果重复，删除重复的
        $where = array();
        $where['ordersn'] = $orderValue['order_id'];
        $orderformResult = $orderform_model->where($where)->select();
        Log::write('订单保存Sql:' . $orderform_model->getLastSql(), Log::INFO, '', $LogFile);

        if (count($orderformResult) > 0) {
            // 记入日志
            Log::write('订单重复:' . '', Log::INFO, '', $LogFile);
            $orderformid = $orderformResult[0]['orderformid'];
        } else {
            // 保存到rms_orderform表中
            $orderform_model->create();
            $orderformid = $orderform_model->add($orderform_array);
            // 记入日志
            Log::write('订单保存Sql:' . $orderform_model->getLastSql(), Log::INFO, '', $LogFile);
        }

        /**
         * *********************
         * 处理产品
         */
        $where = array();
        $where['ordersn'] = $orderValue['order_id'];
        $ordergoods_model->where($where)->delete();

        // 定义餐盒费的数据
        $box_number = 0;
        $box_price = 0;
        $goodsnumber = 0; // 缓存商品数量
        $ordertxt = "";
        $goodsmoney = 0;
        $goods = $orderValue['order_items'];
        foreach ($goods as $goodsValue) {
            $arr = $goodsValue;
            $goods_tmp = array();
            $goods_tmp['orderformid'] = $orderformid;
            $goods_tmp['ordersn'] = $orderValue['order_id'];
            $goods_tmp['code'] = $this->getProductsCode($arr['item_name'], $domain, $connect_str); // 返回产品代码
            $goods_tmp['name'] = $arr['item_name'];
            $goods_tmp['shortname'] = $this->getProductsShortName($arr['item_name'], $domain, $connect_str); // 返回产品代码
            $goods_tmp['number'] = $arr['amount'];

            $goods_tmp['price'] = $arr['sku_price'] / 100;
            $goods_tmp['money'] = $arr['amount'] * $arr['sku_price'] / 100;
            $goods_tmp['domain'] = $domain;

            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($goods_tmp);

            // 记入日志
            Log::write('产品保存Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $LogFile);
            $ordertxt .= $arr['amount'] . '×' . $goods_tmp['shortname'] . ' '; // 生成产品简述
            $goodsmoney = $goodsmoney + $arr['number'] * $arr['price'];

            if ($arr['canhefei'] > 0) {
                $box_number = $box_number + $arr['canhefei'];
                // $box_price = $orderGoods['box_price'];
            }
        }

        // 还要加上打包费 box_num
        if ($orderValue['cover_num'] > 0) {
            // 定义商品数组
            $orderproducts_arr = array();
            // 订单号
            $orderproducts_arr['ordersn'] = $orderValue['order_id'];
            // 订单id
            $orderproducts_arr['orderformid'] = $orderformid;
            // 产品code
            $orderproducts_arr['code'] = '';
            // 产品名称
            $orderproducts_arr['name'] = '滴餐盒费';
            // 产品简称
            $orderproducts_arr['shortname'] = '滴餐盒费';
            // 产品单价
            $orderproducts_arr['price'] = '1';
            // 产品数量
            $orderproducts_arr['number'] = $orderValue['cover_price']/100 ;
            // 产品金额
            $orderproducts_arr['money'] = $orderValue['cover_price']/100;
            // 配送地区标识
            $orderproducts_arr['domain'] = $domain;
            // 商品简述
            $ordertxt .= $orderproducts_arr['number'] . '×' . $orderproducts_arr['shortname'] . ' ';
            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($orderproducts_arr);
            // 记入日志
            Log::write('保存商品的Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $LogFile);
            $goodsmoney = $goodsmoney + $orderproducts_arr['number'] * $orderproducts_arr['price'];
        }

        $goods_tmp = array();
        // 加入送餐费
        if ($orderValue['delivery_price'] > 0) {
            $goods_tmp['orderformid'] = $orderformid;
            $goods_tmp['ordersn'] = $orderValue['order_id'];
            $goods_tmp['number'] = 1;
            $goods_tmp['code'] = '5';
            $goods_tmp['name'] = intval($orderValue['delivery_price'] / 100) . 'S';
            $goods_tmp['shortname'] = intval($orderValue['sdelivery_price'] / 100) . 'S';
            $goods_tmp['price'] = $orderValue['delivery_price'] / 100;
            $goods_tmp['money'] = $orderValue['delivery_price'] / 100;
            $goods_tmp['domain'] = $domain;
            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($goods_tmp);
            // var_dump($ordergoods_model->getLastSql());
            // 记入日志
            Log::write('送餐费保存Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $LogFile);
            $ordertxt .= ' 1×' . $goods_tmp['name']; // 生成产品简述
            $goodsmoney = $goodsmoney + $goods_tmp['number'] * $goods_tmp['price'];
        }

        // 处理活动表
        $where = array();
        $where['ordersn'] = $orderValue['order_id'];
        $orderactivity_model->where($where)->delete();

        $activitymoney = 0;
        $activity = $orderValue['activity_info'];
        foreach ($activity as $arr) {
            $activity_tmp = array();
            $activity_tmp['orderformid'] = $orderformid;
            $activity_tmp['ordersn'] = $orderValue['order_id'];
            $activity_tmp['activityid'] = $arr['act_id'];
            $activity_tmp['name'] = $arr['type'];
            $activity_tmp['money'] = abs($arr['reduce_fee'] / 100);
            $orderactivity_model->create();
            $activityid = $orderactivity_model->add($activity_tmp);
            // 记入日志
            Log::write('活动保存Sql:' . $orderactivity_model->getLastSql(), Log::INFO, '', $LogFile);
            $activitymoney = $activitymoney + $activity_tmp['amount'];
        }

        // 加入服务费到产品类中，现在是5%，（2016.7.23开始）
        if (! empty($orderValue['bill']['platform_fee'])) {
            $activity_tmp = array();
            $activity_tmp['orderformid'] = $orderformid;
            $activity_tmp['ordersn'] = $orderValue['order_id'];
            $activity_tmp['activityid'] = 0;
            $activity_tmp['name'] = '服务费';
            $activity_tmp['money'] = abs($orderValue['bill']['platform_fee'] / 100);
            $orderactivity_model->create();
            $activityid = $orderactivity_model->add($activity_tmp);
            // 记入日志
            Log::write('服务费保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $LogFile);
            // $activitymoney = $activitymoney + $activity_tmp['money'];
        }

        // 处理支付
        $where = array();
        $where['ordersn'] = $orderValue['order_id'];
        $orderpayment_model->where($where)->delete();

        $paymentmoney = 0;
        // 滴滴肯定是线上支付
        // 保存到支付表中
        $payment = array();
        $payment['orderformid'] = $orderformid;
        $payment['ordersn'] = $orderValue['order_id'];
        $payment['paymentid'] = 0;
        $payment['name'] = '滴滴支付';
        $payment['money'] = $orderValue['bill']['shop_receive'] / 100;
        $payment['date'] = date('Y-m-d H:i:s');
        $orderpayment_model->create();
        $orderpayment_model->add($payment);
        // 记入日志
        Log::write('支付保存Sql:' . $orderpayment_model->getLastSql(), Log::INFO, '', $LogFile);
        $paymentmoney = $orderValue['originalPrice'];

        // 为了结账的需要
        $elmOrderGoods = $orderValue['orderActivities'];
        foreach ($elmOrderGoods as $orderGoods) {
            if ($orderGoods['name'] == '配送费') {
                continue;
            }
            // 判断是否有货到付款或者在线支付,那么保存的方式是不同的
            if ($orderValue['onlinePaid'] == true) {} else {
                $clientdebt = array();
                $clientdebt['ordersn'] = $orderValue['ordersn'];
                $clientdebt['debt_id'] = 0;
                $clientdebt['name'] = '滴滴优惠';
                $clientdebt['money'] = abs($goods['reduce_fee']);
                $clientdebt['date'] = date('Y-m-d H:i:s');
                $clientdebt['domain'] = $domain;
                $orderclientdebt_model->create();
                $orderclientdebt_model->add($clientdebt);
                // 结账用保存日志
                Log::write('结账保存Sql:' . $orderclientdebt_model->getLastSql(), Log::INFO, '', $LogFile);
            }
        }

        if ($orderValue['onlinePaid'] === true) {
            $clientdebt = array();
            $clientdebt['ordersn'] = $orderValue['ordersn'];
            $clientdebt['debt_id'] = 0;
            $clientdebt['name'] = '爱汇';
            $clientdebt['money'] = $orderValue['originalPrice'];
            $clientdebt['date'] = date('Y-m-d H:i:s');
            $clientdebt['domain'] = $domain;
            $orderclientdebt_model->create();
            $orderclientdebt_model->add($clientdebt);
            // 结账用保存日志
            Log::write('结账保存Sql:' . $orderclientdebt_model->getLastSql(), Log::INFO, '', $LogFile);
        }

        $data = array();
        // 计算应收金额 都是线上支付，没有应收
        $data['shouldmoney'] = 0;
        // 已付金额
        $data['paidmoney'] = $paymentmoney;
        $data['ordertxt'] = $ordertxt;
        $data['goodsmoney'] = $goodsmoney;
        $where = array();
        $where['orderformid'] = $orderformid;
        $orderform_model->where($where)->save($data);
        // 最终金额保存
        Log::write('订单最终保存Sql:' . $orderform_model->getLastSql(), Log::INFO, '', $LogFile);

        // 写入到状态表中
        $data = array();
        $data['create'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s');
        $data['createcontent'] = '滴滴输入';
        $data['orderformid'] = $orderformid;
        $data['ordersn'] = $orderValue['order_id'];
        $data['domain'] = $domain;
        $orderstate_model->create();
        $orderstate_model->add($data);
        // 记入日志
        Log::write('状态保存Sql:' . $orderstate_model->getLastSql(), Log::INFO, '', $LogFile);

        // 记入操作到action中
        $action['orderformid'] = $orderformid; // 订单号
        $action['ordersn'] = $orderValue['order_id'];
        $action['action'] = '滴滴' . ' 新建 ' . $orderform_array['address'] . ' ' . $ordertxt . '分:' . $orderform_array['company'] . ' ' . $orderform_array['beizhu'];
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $domain;
        $orderaction_model->create();
        $result = $orderaction_model->add($action);
        // 记入日志
        Log::write('日志保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $LogFile);

        // 如果下载的定的中有分公司，说明已经是自动分配
        if (! empty($orderform_array['company'])) {
            // 同时写入日志中
            // 记入操作到action中
            $action = array();
            $action['orderformid'] = $orderformid; // 订单号
            $action['ordersn'] = $orderValue['order_id']; // 订单号
            $action['action'] = "订单分配给" . $orderform_array['company'] . "配送点";
            $action['logtime'] = date('H:i:s');
            $action['domain'] = $domain;
            $orderaction_model->create();
            $result = $orderaction_model->add($action);
            // 记入日志
            Log::write('日志保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $LogFile);

            // 写入到状态表中
            $data = array();
            $data['distribution'] = 1;
            $data['distributiontime'] = date('Y-m-d H:i:s');
            $data['distributioncontent'] = $orderform_array['company'];
            $where = array();
            $where['orderformid'] = $orderformid;
            $where['ordersn'] = $orderValue['order_id'];
            $orderstate_model->where($where)->save($data);
            // 记入日志
            Log::write('订单状态保存Sql:' . $orderstate_model->getLastSql(), Log::INFO, '', $LogFile);

            // 写入到营收状态表,驱动营收结账
            $data = array();
            $data['orderformid'] = $orderformid;
            $data['ordersn'] = $orderValue['order_id'];
            $data['status'] = 0;
            $data['domain'] = $domain;
            $orderyingshouexchange_model->create();
            $orderyingshouexchange_model->add($data);
            // 记入日志
            Log::write('营收保存Sql:' . $orderyingshouexchange_model->getLastSql(), Log::INFO, '', $LogFile);
        }

        // 保存发票，发票处理
        if ($orderValue['invoiced'] === true) {
            $data = array();
            $data['header'] = $orderValue['invoice'];
            $data['body'] = '工作餐';
            if (! empty($orderValue['taxpayerId'])) {
                $data['gmf_nsrsbh'] = $orderValue['taxpayerId'];
            } else {
                $data['gmf_nsrsbh'] = '';
            }

            $data['type'] = 2;
            $data['ordersn'] = $orderValue['ordersn'];
            $data['orderformtxt'] = $orderform_array['address'] . ' ' . $ordertxt;
            $data['ordermoney'] = $orderform_array['totalmoney'] - $activitymoney;
            $data['ordertime'] = date('H:i:s');
            $data['state'] = '未开';
            if (empty($orderform_array['company'])) {
                $data['company'] = '';
            } else {
                $data['company'] = $orderform_array['company'];
            }
            $data['domain'] = $domain;
            $invoice_model->create();
            $invoice = $invoice_model->add($data);
            // 记入日志
            Log::write('发票保存Sql:' . $invoice_model->getLastSql(), Log::INFO, '', $LogFile);
        }

        // 写入到滴滴waimaiorderid表中，让程序去做确认操作
        $data = array();
        $data['ordersn'] = $orderValue['order_id'];
        $data['type'] = 3;
        $data['state'] = 0;
        $data['restaurant_id'] = $orderValue['app_shop_id'];
        $data['create_time'] = date('Y-m-d H:i:s');
        $didiwaimaiorderid_model->create();
        $didiwaimaiorderid_model->add($data);
    }

    public function testConfirm()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'DidiApi_' . date('Y_m_d') . ".log";
        $otherconnect = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/rms_otherplatform#utf8';
        $this->confirmOrder('3458765056496435559','062018070903',$otherconnect,$LogFile);
    }

    /***========================================================
     *  推送订单确认操作
     */
    public function confirmOrder($orderid,$restaurant_id,$otherconnect,$LogFile){

        // 接收状态
        $didishopinfo_model = M('didishopinfo', ' ', $otherconnect);


        //获取app_token等参数
        $where= array();
        $where['restaurant_id'] = $restaurant_id;
        $didishopinfoResult = $didishopinfo_model->where($where)->find();

        if(empty($didishopinfoResult)){
            Log::write('确认参数不全，无法确认' , Log::INFO, '', $LogFile);
            //没有参数，无法确认
            return false;
        }

        $url = 'https://openapi.rlab.net.cn/openapi/order/confirm';

        import('@.Extend.DidiSign');
        $timestamp = time();
        //预计出餐时间
        $meal_time = time();
        $signData = array(
            "app_auth_token" => 'NWU3ODJkOWQxYmNiZWU4MmZjOGFjNTcxMGIxZjY2NjQ=',
            "timestamp" => $timestamp,
            "version" => 1,
            "order_id" => $orderid,
            'meal_time' => $meal_time
        );
        $appSecret = $didishopinfoResult['shop_secret'];


        $sign = DidiSign::generateMd5Signature("$appSecret", $signData);

        $didiData = array(
            "app_auth_token" => 'NWU3ODJkOWQxYmNiZWU4MmZjOGFjNTcxMGIxZjY2NjQ=',
            "timestamp" => $timestamp,
            "version" => 1,
            'signature' => $sign,
            "order_id" => $orderid,
            'meal_time' => $meal_time
        );

        $return = $this->curl_post($url, $didiData);

        Log::write($return , Log::INFO, '', $LogFile);
    }

    /**
     * 滴滴退单处理
     */
    public function  cancelDidiOrder($orderContent, $connect_str, $otherconnect, $LogFile){

        Log::write('用户退单操作' , Log::INFO, '', $LogFile);
        // 订单表
        $orderform_model = M('orderform', 'rms_', $connect_str);
        // 日志表action中
        $orderaction_model = M('orderaction', 'rms_', $connect_str);

        $orderValue = $orderContent;

        // 获取订单的地址等信息
        $where = array();
        $where['ordersn'] = $orderValue['order_id'];
        $orderformResult = $orderform_model->where($where)->find();

        if ($orderformResult) {
            // 设置订单
            $where = array();
            $where['ordersn'] = $orderValue['order_id'];
            $data = array();
            $data['state'] = '订餐';
            $data['company'] = '';
            $data ['beizhu'] = '滴滴用户申请退单，请及时处理！';
            $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
            $orderform_model->where($where)->save($data);

            Log::write('用户退单'.$orderaction_model->getLastSql() , Log::INFO, '', $LogFile);

            // 记入操作到action中
            $action = array();
            $action ['orderformid'] = $orderformResult['orderformid']; // 订单号
            $action ['ordersn'] = $orderValue['order_id'];
            $action ['action'] = '滴滴申请退单';
            $action ['logtime'] = date('H:i:s');
            $action ['domain'] =  $orderformResult['domain'];
            $orderaction_model->create();
            $result = $orderaction_model->add($action);

            // 插入通知
            $where = "  ( (trim(rolename) <> '调度员')  and (`rolename`  <>  '会计主管') and `rolename` <>'接线员') and `domain`  = '" .  $orderformResult['domain'] . "'";
            $userModel = M('user', 'rms_', $connect_str);
            $userResult = $userModel->where($where)->select();

            foreach ($userResult as $userValue) {
                $data = array();
                $data['sender'] = $userValue['name'];
                $data['status'] = 0;
                $data['content'] = '滴滴订单:' . $orderformResult['address'] . ' 申请退单！';
                $data['time'] = date('H:i:s');
                $data['domain'] =  $orderformResult['domain'];

                // 保存消息表
                $messagesModel = M('messages', 'rms_', $connect_str);
                $result = $messagesModel->create();
                $result = $messagesModel->add($data);
            }
        }

    }

    /**
     * *********************************************************************************************************************************
     */

    // 返回产品代码
    public function getProductsCode($name, $domain, $connect_str)
    {
        // 产品表
        $products_model = M('products', 'rms_', $connect_str);
        $where = array();
        $where['name'] = $name;
        $where['domain'] = $domain;
        $products = $products_model->where($where)->find();
        if ($products) {
            return $products['code'];
        } else {
            return '';
        }
    }

    // 获取产品简称
    public function getProductsShortName($name, $domain, $connect_str)
    {
        // 产品表
        $products_model = M('products', 'rms_', $connect_str);
        $where = array();
        $where['name'] = $name;
        $where['domain'] = $domain;
        $products = $products_model->where($where)->find();
        if ($products) {
            return $products['shortname'];
        } else {
            return $name;
        }
    }

    // 删除特殊的字符
    function ReMoveChar($text)
    {
        $text = str_replace("`", "", $text);
        $text = str_replace("'", "", $text);
        $text = str_replace("~", "", $text);
        $text = str_replace('"', "", $text);
        $text = str_replace('　', " ", $text);
        $text = str_replace('，', "", $text);
        $text = str_replace(',', "", $text);
        $text = str_replace('.', '<', $text);
        $text = str_replace('.', '>', $text);

        for ($i = 0; $i < 32; $i ++) {
            $text = str_replace(chr($i), "", $text);
        }
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * 返回所有城市
     */
    public function getCityShopname($shop_id, $connect_str, $domain)
    {
        $companymgr_model = M('companymgr', 'rms_', $connect_str);
        $where = array();
        $where['domain'] = $domain;
        $where['didiopt'] = $shop_id;
        $companymgr = $companymgr_model->field('name,didiopt,didiauto')
            ->where($where)
            ->find();
        if ($companymgr) {
            if ($companymgr['didiauto'] == '自动') {
                return $companymgr['name'];
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * 根据city，返回城市
     */
    public function getCity($city)
    {
        if ($city == '无锡市')
            return 'wx.lihuaerp.com';
        // 默认返回
        return 'nj.lihuaerp.com';
    }

    /**
     * cur post的处理函数
     */
    function curl_post($url, $post_data = null, $headers = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        //curl_setopt($curl, CURLOPT_REFERER, "http://XXX");

        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        $return = json_decode($data, true);
        return $return;
    }
}