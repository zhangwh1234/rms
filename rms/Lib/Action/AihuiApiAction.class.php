<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/6/13
 * Time: 上午10:35
 * 爱汇医院订餐的接口
 * 测试：/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rmsdaemon/index.php Home/AihuiApi/test
 */

class AihuiApiAction extends Action
{

    public function index()
    {
        // 定义日志文件
        $LogFile = LOG_PATH . 'AihuiApi_' . date('Y_m_d') . ".log";

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);


        if (empty($_POST)) {
            var_dump('hello,I miss you!');
            exit();
        }

        // 保存到日志中
        Log::write('获得爱汇外卖推送', Log::INFO, Log::FILE, $LogFile);
        $aiJson = json_encode($_REQUEST);
        Log::write($aiJson, Log::INFO, Log::FILE, $LogFile);

        $data['message'] = 'ok';
        $this->ajaxReturn($data, 'JSON');
    }


    public function test(){
        $aihuiJson = '{"body":"{\\\"order\\\":{\\\"address\\\":\\\"\u4e0a\u6d77\u5e02\u7b2c\u4e00\u4eba\u6c11\u533b\u9662\uff0c\u9aa8\u79d1\uff0c159\\\",\\\"custtime\\\":\\\"13:15:11\\\",\\\"ordersn\\\":\\\"180627124516418\\\",\\\"city\\\":491,\\\"latitude\\\":31.25822,\\\"songcanfei\\\":1,\\\"totalmoney\\\":1.01,\\\"rectime\\\":\\\"12:45:11\\\",\\\"clientname\\\":\\\"Jack\\\",\\\"telphone\\\":\\\"18118376813\\\",\\\"beizhu\\\":\\\"\\\",\\\"custdate\\\":\\\"2018-06-27\\\",\\\"recdate\\\":\\\"2018-06-27\\\",\\\"longitude\\\":121.496178},\\\"products\\\":[{\\\"number\\\":\\\"1\\\",\\\"canhefei\\\":0,\\\"money\\\":0.01,\\\"price\\\":0.01,\\\"name\\\":\\\"\u5241\u6912\u9c7c\u5934\\\",\\\"orderproductsid\\\":666}]}"}';
        $aihuiJson = str_replace('\\"', "\"",$aihuiJson);

        $aihuiOrder = json_decode($aihuiJson,true);

        var_dump($aihuiOrder['body']);
        $order = json_decode($aihuiOrder['body'],true);
        var_dump($order['products']);
        $connect = 'mysql://root:@localhost/rms#utf8';
        $otherconnect = 'mysql://root:@localhost/rms_otherplatform#utf8';
         $this->saveAihuiOrder($order,$connect,$otherconnect );
    }


    /**
     * 保存爱汇订单
     *
     * @param $orderContent 订单数组
     * @param
     *            $connect_str
     * @throws \Exception
     */
    public function saveAihuiOrder($orderContent, $connect_str,$otherconnect)
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
        $aihuiwaimaiorderid_model = M('aihuiwaimaiorderid', ' ', $otherconnect);

        $orderValue = $orderContent['order'];
        /**
         * ***************************
         */
        // 先处理订单主表 order
        $orderform_array = array();
        $orderform_array ['ordersn'] = $orderValue ['ordersn'];  //订单号

        // 返回城市识别
        $domain = $this->getCity($orderValue['city']);

        //联系人
        if (empty ($orderValue ['clientname'])) {
            $orderform_array ['clientname'] = '';
        } else {
            $orderform_array ['clientname'] = $orderValue ['clientname'];
        }

        // 用户地址
        $orderform_array ['address'] = $orderValue ['address'];
        $orderform_array ['address'] = $this->ReMoveChar($orderform_array ['address']);
        if ($orderValue ['serviceFee']) {
            // 应上海的要求，加上服务费的内容
            $orderform_array ['address'] = '(费' . $orderValue ['serviceFee'] . ')' . $orderform_array ['address'];
        }
        // 商品的下载数据Extra,附加的优惠
        // 首先处理优惠附加
        $elmOrderGoods = $orderValue ['orderActivities'];
        $extralGoodsPrice = 0; // 计算活动金额
        foreach ($elmOrderGoods as $orderGoods) {
            if ($orderGoods ['name'] == '配送费') {
                continue;
            }
            // 立减优惠
            if (($orderGoods ['category_id'] == 11) || ($orderGoods ['name'] == '10元吃大餐*') || ($orderGoods ['amount'] < 0)) {
                if (strpos($orderGoods ['name'], '红包') !== false) {
                    $extralGoodsPrice += $orderGoods ['quantity'] * $orderGoods ['amount'];
                    $activityMoney = $orderGoods ['quantity'] * $orderGoods ['price'];
                    $order ['address'] = '(红包' . $activityMoney . ')' . $orderform_array ['address'];
                } else {
                    $extralGoodsPrice += $orderGoods ['amount'];
                    $activityMoney = $orderGoods ['amount'];
                    $orderform_array ['address'] = "(饿" . $activityMoney . "元)" . $orderform_array ['address'];
                }
            }
        }
        // 判断是否有货到付款或者在线支付
        if ($orderValue ['onlinePaid'] == true) {
            // 地址提示
            $orderform_array ['address'] = '' . $orderform_array ['address'];
        } else {
            $orderform_array ['address'] = '' . $orderform_array ['address'];
        }

        // 用户电话
        $orderform_array ['telphone'] = $this->ReMoveChar($orderValue ['telphone']);
        // 送餐日期
        $orderform_array ['custdate'] = $orderValue['custdate'];
        // 要餐时间
        if ($orderValue ['custtime']) {
            $orderform_array ['custtime'] = $orderValue['custtime'];
        } else {
            $orderform_array ['custtime'] = date("H:i:s", time() + 60 * 30);
        }
        // 接线员
        $orderform_array ['telname'] = '爱汇';
        // 录入日期
        $orderform_array ['recdate'] = $orderValue['recdate'];
        // 录入时间
        $orderform_array ['rectime'] = $orderValue['rectime'];
        // 备注
        $orderform_array ['beizhu'] = $orderValue ['beizhu'];
        $orderform_array ['beizhu'] = $this->ReMoveChar($orderform_array ['beizhu']);
        // 订单总金额
        $orderform_array ['totalmoney'] = $orderValue ['totalmoney'];
        // 送餐费不要输入
        $orderform_array ['shippingmoney'] = 0;

        // 发票
        if ($orderValue ['invoiced']) {
            $orderform_array ['invoiceheader'] = $orderValue ['invoice'];
        } else {
            $orderform_array ['invoiceheader'] = '';
        }
        if (!empty ($orderValue ['taxpayerId'])) {
            $orderform_array ['gmf_nsrsbh'] = $orderValue ['taxpayerId'];
        } else {
            $orderform_array ['gmf_nsrsbh'] = '';
        }

        if (!empty ($orderform_array ['invoiceheader'])) {
            $orderform_array ['beizhu'] = $orderform_array ['beizhu'] . ' 票:' . $orderform_array ['invoiceheader'];
        }
        $orderform_array ['invoicebody'] = '工作餐';

        // 分公司
        $orderform_array ['company'] = $this->getCityShopname($orderValue ['shopId'], $connect_str, $domain); // $this->getCompanyName ( $elmParam ['shopid'], $orderValue ['restaurant_id'] );
        $orderform_array ['ordertxt'] = '';
        $orderform_array ['state'] = '订餐';

        if (!empty ($orderValue ['deliveryGeo'])) {
            // 定义坐标
            $baidu_la = explode(',', $orderValue ['deliveryGeo']);
            $baidu_la = $this->Convert_GCJ02_To_BD09($baidu_la [0], $baidu_la [1]);
            $orderform_array ['longitude'] = $baidu_la ['ba_lat'];
            $orderform_array ['latitude'] = $baidu_la ['bd_lon'];
        }

        // 根据送餐时间来判断
        if (intval(substr($orderform_array ['custtime'], 0, 2)) >= 15)
            $cAp = '下午';
        else
            $cAp = '上午';
        $orderform_array ['ap'] = $cAp;

        // 根据suppliers_id来判断,输入domain
        $orderform_array ['domain'] = $domain;
        // 订单来源
        $orderform_array ['origin'] = '爱汇';

        // 检查是否重复，如果重复，删除重复的
        $where = array();
        $where ['ordersn'] = $orderValue ['ordersn'];
        $orderformResult = $orderform_model->where($where)->select();
        // var_dump($orderformResult);
        if (count($orderformResult) > 0) {
            // 记入日志
            Log::write('订单重复:' . '', Log::INFO, '', $this->logFile);
            $orderformid = $orderformResult [0] ['orderformid'];
        } else {
            // 保存到rms_orderform表中
            $orderform_model->create();
            $orderformid = $orderform_model->add($orderform_array);
            // 记入日志
            Log::write('订单保存Sql:' . $orderform_model->getLastSql(), Log::INFO, '', $this->logFile);
        }

        var_dump($orderform_model->getLastSql());

        /**
         * *********************
         * 处理产品
         */
        $where = array();
        $where ['ordersn'] = $orderValue ['ordersn'];
        $ordergoods_model->where($where)->delete();

        //定义餐盒费的数据
        $box_number  = 0;
        $box_price = 0;
        $goodsnumber = 0;  //缓存商品数量
        $ordertxt = "";
        $goodsmoney = 0;
        $goods = $orderContent ['products'];
        foreach ($goods as $goodsValue) {
            $arr = $goodsValue;
            $goods_tmp = array();
            $goods_tmp ['orderformid'] = $orderformid;
            $goods_tmp ['ordersn'] = $orderValue ['ordersn'];
            $goods_tmp ['code'] = $this->getProductsCode($arr ['name'], $domain, $connect_str); // 返回产品代码
            $goods_tmp ['name'] = $arr ['name'];
            $goods_tmp ['shortname'] = $this->getProductsShortName($arr ['name'], $domain, $connect_str); // 返回产品代码
            $goods_tmp ['number'] = $arr ['number'];

            $goods_tmp ['price'] = $arr ['price'];
            $goods_tmp ['money'] = $arr ['number']  * $arr ['price'];
            $goods_tmp ['domain'] = $domain;
            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($goods_tmp);

            // 记入日志
            Log::write('产品保存Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $this->logFile);
            $ordertxt .= $arr ['number'] . '×' . $goods_tmp ['shortname'] . ' '; // 生成产品简述
            $goodsmoney = $goodsmoney + $arr ['number'] * $arr ['price'];
            var_dump($ordergoods_model->getLastSql());
            if($arr ['canhefei'] > 0){
                $box_number = $box_number +  $arr ['canhefei'];
                //$box_price = $orderGoods['box_price'];
            }
        }

        //还要加上打包费 box_num
        if($box_number > 0){
            // 定义商品数组
            $orderproducts_arr = array();
            //订单号
            $orderproducts_arr['ordersn'] = $orderValue ['ordersn'];
            //订单id
            $orderproducts_arr['orderformid'] = $orderformid;
            //产品code
            $orderproducts_arr['code'] = '';
            //产品名称
            $orderproducts_arr['name'] = '爱餐盒费';
            //产品简称
            $orderproducts_arr['shortname'] = '爱餐盒费';
            //产品单价
            $orderproducts_arr['price'] = '1' ;
            //产品数量
            $orderproducts_arr['number'] = $box_number;
            //产品金额
            $orderproducts_arr['money'] = $box_number * 1 ;
            //配送地区标识
            $orderproducts_arr['domain'] = $domain;
            // 商品简述
            $ordertxt .= $orderproducts_arr['number'] . '×' . $orderproducts_arr ['shortname'] . ' ';
            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($orderproducts_arr);
            // 记入日志
            Log::write('保存商品的Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $this->logFile);
            $goodsmoney = $goodsmoney + $orderproducts_arr['number'] * $orderproducts_arr['price'];
        }

        $goods_tmp = array();
        // 加入送餐费
        if ($orderValue ['songcanfei'] > 0) {
            $goods_tmp ['orderformid'] = $orderformid;
            $goods_tmp ['ordersn'] = $orderValue ['ordersn'];
            $goods_tmp ['number'] = 1;
            $goods_tmp ['code'] = '5';
            $goods_tmp ['name'] = intval($orderValue ['songcanfei']) . 'S';
            $goods_tmp ['shortname'] = intval($orderValue ['songcanfei']) . 'S';
            $goods_tmp ['price'] = $orderValue ['songcanfei'];
            $goods_tmp ['money'] = $orderValue ['songcanfei'];
            $goods_tmp ['domain'] = $domain;
            $ordergoods_model->create();
            $ordergoodsid = $ordergoods_model->add($goods_tmp);
            // var_dump($ordergoods_model->getLastSql());
            // 记入日志
            Log::write('送餐费保存Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $this->logFile);
            $ordertxt .= ' 1×' . $goods_tmp ['name']; // 生成产品简述
            $goodsmoney = $goodsmoney + $goods_tmp ['number'] * $goods_tmp ['price'];
        }



        // 处理活动表
        $where = array();
        $where ['ordersn'] = $orderValue ['id'];
        $orderactivity_model->where($where)->delete();

        $activitymoney = 0;
        $activity = $orderValue ['orderActivities'];
        foreach ($activity as $arr) {
            if ($arr ['name'] == '配送费') {
                continue;
            }
            if ($arr ['name'] == '餐盒') {
                $goods_tmp = array();
                // 加入餐盒费
                $goods_tmp ['orderformid'] = $orderformid;
                $goods_tmp ['ordersn'] = $orderValue ['id'];
                $goods_tmp ['number'] = $arr ['price'];
                $goods_tmp ['code'] = $arr ['category_id'];
                $goods_tmp ['name'] = $arr ['name'] . '费';
                $goods_tmp ['shortname'] = $arr ['name'] . '费';
                $goods_tmp ['price'] = 1; // $arr ['price'];
                $goods_tmp ['money'] = intval($arr ['quantity'] * $arr ['price']);
                $goods_tmp ['domain'] = $domain;
                $ordergoods_model->create();
                $ordergoodsid = $ordergoods_model->add($goods_tmp);
                // 记入日志
                Log::write('餐盒费保存Sql:' . $ordergoods_model->getLastSql(), Log::INFO, '', $this->logFile);
                $ordertxt .= $goods_tmp ['number'] . '×' . $goods_tmp ['name']; // 生成产品简述
                $goodsmoney = $goodsmoney + $goods ['number'] * $goods ['price'];

                continue;
            }

            $activity_tmp = array();
            $activity_tmp ['orderformid'] = $orderformid;
            $activity_tmp ['ordersn'] = $orderValue ['id'];
            $activity_tmp ['activityid'] = $arr ['id'];
            $activity_tmp ['name'] = $arr ['name'];
            $activity_tmp ['money'] = abs($arr ['amount']);
            $orderactivity_model->create();
            $activityid = $orderactivity_model->add($activity_tmp);
            // 记入日志
            Log::write('活动保存Sql:' . $orderactivity_model->getLastSql(), Log::INFO, '', $this->logFile);
            $activitymoney = $activitymoney + $activity_tmp ['amount'];
        }

        // 加入服务费到产品类中，现在是5%，（2016.7.23开始）
        if (!empty ($orderValue ['serviceFee'])) {
            $activity_tmp = array();
            $activity_tmp ['orderformid'] = $orderformid;
            $activity_tmp ['ordersn'] = $orderValue ['id'];
            $activity_tmp ['activityid'] = 0;
            $activity_tmp ['name'] = '服务费';
            $activity_tmp ['money'] = abs($orderValue ['serviceFee']);
            $orderactivity_model->create();
            $activityid = $orderactivity_model->add($activity_tmp);
            // 记入日志
            Log::write('服务费保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $this->logFile);
            // $activitymoney = $activitymoney + $activity_tmp['money'];
        }

        // 处理支付
        $where = array();
        $where ['ordersn'] = $orderValue ['id'];
        $orderpayment_model->where($where)->delete();

        $paymentmoney = 0;
        if ($orderValue ['onlinePaid'] === true) {
            // 保存到支付表中
            $payment = array();
            $payment ['orderformid'] = $orderformid;
            $payment ['ordersn'] = $orderValue ['ordersn'];
            $payment ['paymentid'] = 0;
            $payment ['name'] = '饿支付';
            $payment ['money'] = $orderValue ['totalPrice'];
            $payment ['date'] = date('Y-m-d H:i:s');
            $orderpayment_model->create();
            $orderpayment_model->add($payment);
            // 记入日志
            Log::write('支付保存Sql:' . $orderpayment_model->getLastSql(), Log::INFO, '', $this->logFile);
            $paymentmoney = $orderValue ['originalPrice'];
        }

        // 为了结账的需要
        $elmOrderGoods = $orderValue ['orderActivities'];
        foreach ($elmOrderGoods as $orderGoods) {
            if ($orderGoods ['name'] == '配送费') {
                continue;
            }
            // 判断是否有货到付款或者在线支付,那么保存的方式是不同的
            if ($orderValue ['onlinePaid'] == true) {
            } else {
                $clientdebt = array();
                $clientdebt ['ordersn'] = $orderValue ['ordersn'];
                $clientdebt ['debt_id'] = 0;
                $clientdebt ['name'] = '饿了吗优惠';
                $clientdebt ['money'] = abs($goods ['price']);
                $clientdebt ['date'] = date('Y-m-d H:i:s');
                $clientdebt ['domain'] = $domain;
                $orderclientdebt_model->create();
                $orderclientdebt_model->add($clientdebt);
                // 结账用保存日志
                Log::write('结账保存Sql:' . $orderclientdebt_model->getLastSql(), Log::INFO, '', $this->logFile);
            }
        }

        if ($orderValue ['onlinePaid'] === true) {
            $clientdebt = array();
            $clientdebt ['ordersn'] = $orderValue ['ordersn'];
            $clientdebt ['debt_id'] = 0;
            $clientdebt ['name'] = '饿了吗';
            $clientdebt ['money'] = $orderValue ['originalPrice'];
            $clientdebt ['date'] = date('Y-m-d H:i:s');
            $clientdebt ['domain'] = $domain;
            $orderclientdebt_model->create();
            $orderclientdebt_model->add($clientdebt);
            // 结账用保存日志
            Log::write('结账保存Sql:' . $orderclientdebt_model->getLastSql(), Log::INFO, '', $this->logFile);
        }

        $data = array();
        // 计算应收金额  订单总额-支付金额 -活动金额 - 红包
        $data ['shouldmoney'] = $orderform_array ['totalmoney'] - $orderValue ['totalPrice'] + $orderValue ['activityTotal'] + $orderValue['hongbao'];
        // 已付金额
        $data ['paidmoney'] = $paymentmoney;
        $data ['ordertxt'] = $ordertxt;
        $data ['goodsmoney'] = $goodsmoney;
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderform_model->where($where)->save($data);
        // 最终金额保存
        Log::write('订单最终保存Sql:' . $orderform_model->getLastSql(), Log::INFO, '', $this->logFile);

        // 写入到状态表中
        $data = array();
        $data ['create'] = 1;
        $data ['createtime'] = date('Y-m-d H:i:s');
        $data ['createcontent'] = '饿了么输入';
        $data ['orderformid'] = $orderformid;
        $data ['ordersn'] = $orderValue ['ordersn'];
        $data ['domain'] = $domain;
        $orderstate_model->create();
        $orderstate_model->add($data);
        // 记入日志
        Log::write('状态保存Sql:' . $orderstate_model->getLastSql(), Log::INFO, '', $this->logFile);

        // 记入操作到action中
        $action ['orderformid'] = $orderformid; // 订单号
        $action ['ordersn'] = $orderValue ['ordersn'];
        $action ['action'] = '饿了么' . ' 新建 ' . $orderValue ['address'] . ' ' . $ordertxt . '分:' . $orderValue ['company'] . ' ' . $orderValue ['beizhu'];
        $action ['logtime'] = date('H:i:s');
        $action ['domain'] = $domain;
        $orderaction_model->create();
        $result = $orderaction_model->add($action);
        // 记入日志
        Log::write('日志保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $this->logFile);

        // 如果下载的定的中有分公司，说明已经是自动分配
        if (!empty ($orderform_array ['company'])) {
            // 同时写入日志中
            // 记入操作到action中
            $action = array();
            $action ['orderformid'] = $orderformid; // 订单号
            $action ['ordersn'] = $orderValue ['ordersn']; // 订单号
            $action ['action'] = "订单分配给" . $orderform_array ['company'] . "配送点";
            $action ['logtime'] = date('H:i:s');
            $action ['domain'] = $domain;
            $orderaction_model->create();
            $result = $orderaction_model->add($action);
            // 记入日志
            Log::write('日志保存Sql:' . $orderaction_model->getLastSql(), Log::INFO, '', $this->logFile);

            // 写入到状态表中
            $data = array();
            $data ['distribution'] = 1;
            $data ['distributiontime'] = date('Y-m-d H:i:s');
            $data ['distributioncontent'] = $orderform_array ['company'];
            $where = array();
            $where ['orderformid'] = $orderformid;
            $where ['ordersn'] = $orderValue ['ordersn'];
            $orderstate_model->where($where)->save($data);
            // 记入日志
            Log::write('订单状态保存Sql:' . $orderstate_model->getLastSql(), Log::INFO, '', $this->logFile);

            // 写入到营收状态表,驱动营收结账
            $data = array();
            $data ['orderformid'] = $orderformid;
            $data ['ordersn'] = $orderValue ['id'];
            $data ['status'] = 0;
            $data ['domain'] = $domain;
            $orderyingshouexchange_model->create();
            $orderyingshouexchange_model->add($data);
            // 记入日志
            Log::write('营收保存Sql:' . $orderyingshouexchange_model->getLastSql(), Log::INFO, '', $this->logFile);
        }

        // 保存发票，发票处理
        if ($orderValue ['invoiced'] === true) {
            $data = array();
            $data ['header'] = $orderValue ['invoice'];
            $data ['body'] = '工作餐';
            if (!empty ($orderValue ['taxpayerId'])) {
                $data ['gmf_nsrsbh'] = $orderValue ['taxpayerId'];
            } else {
                $data ['gmf_nsrsbh'] = '';
            }

            $data ['type'] = 2;
            $data ['ordersn'] = $orderValue ['ordersn'];
            $data ['orderformtxt'] = $orderform_array ['address'] . ' ' . $ordertxt;
            $data ['ordermoney'] = $orderform_array ['totalmoney'] - $activitymoney;
            $data ['ordertime'] = date('H:i:s');
            $data ['state'] = '未开';
            if (empty ($orderform_array ['company'])) {
                $data ['company'] = '';
            } else {
                $data ['company'] = $orderform_array ['company'];
            }
            $data ['domain'] = $domain;
            $invoice_model->create();
            $invoice = $invoice_model->add($data);
            // 记入日志
            Log::write('发票保存Sql:' . $invoice_model->getLastSql(), Log::INFO, '', $this->logFile);
        }

        //写入到aihuiwaimaiorderid表中，让程序去做确认操作
        $data = array();
        $data['ordersn'] = $orderValue ['ordersn'];
        $data['type'] = 3;
        $data['state'] = 0;
        $data['create_time'] = date('Y-m-d H:i:s');
        $aihuiwaimaiorderid_model->create();
        $aihuiwaimaiorderid_model->add($data);
        var_dump($aihuiwaimaiorderid_model->getLastSql());
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
        $where ['name'] = $name;
        $where ['domain'] = $domain;
        $products = $products_model->where($where)->find();
        if ($products) {
            return $products ['code'];
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
        $where ['name'] = $name;
        $where ['domain'] = $domain;
        $products = $products_model->where($where)->find();
        if ($products) {
            return $products ['shortname'];
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

        for ($i = 0; $i < 32; $i++) {
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
        $where ['domain'] = $domain;
        $where ['elmopt'] = $shop_id;
        $companymgr = $companymgr_model->field('name,elmopt,elmauto')->where($where)->find();
        if ($companymgr) {
            if ($companymgr ['elmauto'] == '自动') {
                return $companymgr ['name'];
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
    public function getCity($city){
        if($city == 491) return 'sh.lihuaerp.com';
        //默认返回
        return 'sh.lihuaerp.com';
    }


}