<?php

/**
 * 订单管理系统和营收系统交换数据的接口
 * 营收系统客户端
 * 2014-07-08
 * 测试命令：/Applications/XAMPP/xamppfiles/bin/php  /Applications/XAMPP/htdocs/rms/litter.php YingshouClient/getCzOrder
 * 部署命令：/usr/local/php/bin/php /home/lihuasoft/assistant/litter.php YingshouClient/getCzOrder
 */
class YingshouClientAction extends Action
{

    /**
     * 架构函数
     */
    public function __construct()
    {
    }

    public function getCzOrder()
    {
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'czYSClinet_' . date('Y-m-d') . '.log';
        $this->getOrderForm(4);
        exit;
        while(0){
            usleep(20000000);
        }

    }

    public function getNjOrder()
    {
        $this->getOrderForm(6);
    }

    // 取得订单
    public function getOrderForm($shopid)
    {
        // 载入curl函数等
        load("@.function");

        // 抓取的url
        $url = 'http://nj.lihuaerp.com/index.php/InterfaceServer/YingshouGetOrderForm/token/lihua1/domain/' . $shopid;
        var_dump($url);
        // 启动curl去抓取
        $resp = curl($url);
        //Log::write('获取数据！' . $resp, LOG::INFO, LOG::FILE, $this->LogFile);
        var_dump($resp);

        // 数组化
        $orderformArray = json_decode($resp, true);
        var_dump($orderformArray);
        if ($orderformArray ['error'] == 1) {
            Log::write('没有数据！', LOG::INFO, LOG::FILE, $this->LogFile);
            exit ();
        }

        // 取得订单数组
        $orderform = $orderformArray ['result'];

        foreach ($orderform as $key => $value) {
            //Log::write ( '获得订单' . json_encode ( $value ), LOG::INFO, LOG::FILE, $this->LogFile );
            $this->saveOrderForm($value, $shopid);
            $this->saveOrderGoods($value, $shopid);
            $this->saveOrderActivity($value, $shopid);
            $this->saveOrderPayment($value, $shopid);


            // 确认订单
            $confirmUrl = 'http://nj.lihuaerp.com/index.php/InterfaceServer/YingshouSetOrderForm/orderformid/' . $key . '.html';
            //Log::write ( '确认订单：' . $confirmUrl, LOG::INFO, LOG::FILE, $this->LogFile );
            $resp = curl($confirmUrl);
            usleep(10000);
        }
    }

    // 保存订单表
    function saveOrderForm($orderform_array, $shopid)
    {
        $data = array();
        $data['cOrderID'] = $orderform_array ['ordersn'];
        $data ['cAddress'] = $orderform_array ['address'];
        $data ['cPhone'] = $orderform_array ['telphone'];
        $data ['cRecTime'] = $orderform_array ['rectime'];
        $data ['cMemo'] = $orderform_array ['beizhu'];
        $data ['cTelName'] = $orderform_array ['telname'];
        $data ['cCustDate'] = $orderform_array ['custdate'];
        $data ['cCustTime'] = $orderform_array ['custtime'];
        $data ['mMoney'] = $orderform_array ['totalmoney'];;
        $data ['cOrderTxt'] = $orderform_array ['ordertxt'];
        $data ['cCustName'] = $orderform_array ['sendname'];
        $data ['cCompany'] = $orderform_array ['company'];
        $data ['cArea'] = $this->getArea($shopid);
        $data ['cAp'] = $orderform_array ['ap'];
        $data ['BillHeader'] = $orderform_array ['invoiceheader'];
        $data ['BillBody'] = $orderform_array ['invoicebody'];
        $data ['sand_state'] = 0;

        // 查询订单是否已经存在
        $where = array();
        $where ['cOrderID'] = $orderform_array ['ordersn'];

        $dbname = $this->getDbName($shopid);
        $connect_str = $this->getConnectStr($dbname);
        $kforderformModel = M('kforderform', Null, $connect_str);
        $result = $kforderformModel->where($where)->find();
        var_dump($result);
        if (!empty ($result)) {

            $where = array();
            $where ['cOrderID'] = $orderform_array ['orderformid'];

            $kforderformModel->where($where)->save($data);
            //Log::write ( '订单：'.$kforderformModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );

        } else {
            // $data['sand_state'] = 0;
            $kforderformModel->create();
            $kforderformModel->add($data);
            var_dump($kforderformModel->getLastSql());
            //Log::write ( '订单：'.$kforderformModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );
        }
    }

    // 保存订单产品表
    function saveOrderGoods($orderform_array, $shopid)
    {
        $where = array();
        $where ['cOrderID'] = $orderform_array ['ordersn'];

        $dbname = $this->getDbName($shopid);
        $connect_str = $this->getConnectStr($dbname);
        $kfordergoodsModel = M('kfordergoods', Null, $connect_str);
        $kfordergoodsModel->where($where)->delete();
        $ordergoodsform_product_array = $orderform_array ['orderproducts'];

        foreach ($ordergoodsform_product_array as $products_value) {
            $data = array();
            $data ['cOrderListID'] = $products_value ['orderproductsid'];
            $data ['cOrderID'] = $orderform_array ['ordersn'];
            $data ['cName'] = $products_value ['name'];
            $data ['mNumber'] = $products_value ['number'];
            $data ['mPrice'] = $products_value ['price'];
            $data ['mMoney'] = $products_value ['money'];

            $kfordergoodsModel->create();
            $kfordergoodsModel->add($data);
            //var_dump($kfordergoodsModel->getLastSql());
            //Log::write ( '订单产品保存：'.$kfgetgoodsModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );
        }

        //为了和结账系统兼容，单独输入送餐费
        if($orderform_array ['shippingmoney'] > 0 ){
            $data = array();
            $data ['cOrderListID'] = 1;
            $data ['cOrderID'] = $orderform_array ['ordersn'];
            $data ['cName'] = $orderform_array ['price'] . 'S' ;
            $data ['mNumber'] = 1;
            $data ['mPrice'] = $orderform_array ['price'];
            $data ['mMoney'] = $orderform_array ['price'];

            $kfordergoodsModel->create();
            $kfordergoodsModel->add($data);
        }
    }

    //保存活动表
    function saveOrderActivity($orderform_array, $shopid)
    {
        $where = array();
        $where ['order_sn'] = $orderform_array ['ordersn'];

        $dbname = $this->getDbName($shopid);
        $connect_str = $this->getConnectStr($dbname);
        $kforderactivityModel = M('kforderactivity', Null, $connect_str);
        $kforderactivityModel->where($where)->delete();
        $orderform_activity_array = $orderform_array ['orderactivity'];

        foreach ($orderform_activity_array as $activity_value) {
            $data = array();
            $data ['order_id'] = $activity_value ['orderformid'];
            $data ['order_sn'] = $orderform_array ['ordersn'];
            $data ['name'] = $activity_value ['name'];
            $data ['date'] = date('Y-m-d H:i:s');
            $data ['discount'] = $activity_value ['money'];

            $kforderactivityModel->create();
            $kforderactivityModel->add($data);
            //var_dump($kforderactivityModel->getLastSql());
            //Log::write ( '订单产品保存：'.$kfgetgoodsModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );
        }
    }

    //保存支付表
    function saveOrderPayment($orderform_array, $shopid)
    {
        $where = array();
        $where ['order_sn'] = $orderform_array ['ordersn'];

        $dbname = $this->getDbName($shopid);
        $connect_str = $this->getConnectStr($dbname);
        $kforderpaymentModel = M('kforderpayment', Null, $connect_str);
        $kforderpaymentModel->where($where)->delete();
        $orderform_payment_array = $orderform_array ['orderpayment'];

        foreach ($orderform_payment_array as $payment_value) {
            $data = array();
            $data ['order_id'] = $payment_value ['orderformid'];
            $data ['order_sn'] = $orderform_array ['ordersn'];
            $data ['name'] = $payment_value ['name'];
            $data ['date'] = date('Y-m-d H:i:s');
            $data ['discount'] = $payment_value ['money'];

            $kforderpaymentModel->create();
            $kforderpaymentModel->add($data);
            //var_dump($kforderpaymentModel->getLastSql());
            //Log::write ( '订单产品保存：'.$kfgetgoodsModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );
        }
    }

    //测试命令：Applications/XAMPP/xamppfiles/bin/php  /Applications/XAMPP/htdocs/rms/litter.php YingshouClient/getCzPrint
    public function getCzPrint()
    {
        $this->getOrderPrintNumber(4);
    }

    public function getNjPrint()
    {
        $this->getOrderPrintNumber(6);
    }


    function getOrderPrintNumber($shopid)
    {
        load("@.function");

        // 抓取的url
        $url = 'http://nj.lihuaerp.com/index.php/InterfaceServer/YingshouGetOrderPrint/token/lihua1/domain/' . $shopid;

        // 启动curl去抓取
        $resp = curl($url);
        var_dump($resp);

        // 数组化
        $orderprintArray = json_decode($resp, true);

        if ($orderprintArray ['error'] == 1) {
            Log::write('没有打印单！', LOG::INFO, LOG::FILE, $this->LogFile);
            exit ();
        }
        var_dump($orderprintArray);
        // 取得订单数组
        $orderprint = $orderprintArray ;
        var_dump($orderprint);
        foreach ($orderprint as $printKey => $printValue) {

            $this->saveGetGoodsForm($printValue, $shopid);
            $confirmUrl = 'http://nj.lihuaerp.com/index.php/InterfaceServer/YingshouSetOrderPrint/orderprintid/' . $printKey . '.html';
            var_dump($confirmUrl);
            //Log::write ( '确认订单：' . $confirmUrl, LOG::INFO, LOG::FILE, $this->LogFile );
            $resp = curl($confirmUrl);
            usleep(10000);
        }


    }

    // 保存领单产品
    function saveGetGoodsFormProduct($getgoodsform_array)
    {
        $where = array();
        $where ['orderid'] = $getgoodsform_array ['orderformid'];


        $kfgetgoodsModel = D('Kfgetgoods');
        $kfgetgoodsModel->where($where)->delete();
        $getgoodsform_product_array = $getgoodsform_array ['orderproducts'];

        foreach ($getgoodsform_product_array as $products_value) {
            $data = array();
            $data ['id'] = $products_value ['orderproductsid'];
            $data ['orderid'] = $getgoodsform_array ['orderformid'];
            $data ['name'] = $products_value ['name'];
            $data ['number'] = $products_value ['number'];
            $data ['price'] = $products_value ['price'];
            $data ['money'] = $products_value ['money'];

            $kfgetgoodsModel->create();
            $kfgetgoodsModel->add($data);
            //Log::write ( '领餐产品保存：'.$kfgetgoodsModel->getLastSql() , INFO, LOG::FILE, $this->LogFile );
        }
    }

    // 保存领单内容
    function saveGetGoodsForm($getgoodsform_array, $shopid)
    {
        // 不能有重复的数据
        $where = array();
        $where ['orderid'] = $getgoodsform_array ['orderid'];

        $dbname = $this->getDbName($shopid);
        $connect_str = $this->getConnectStr($dbname);
        $kfgetgoodsformModel = M('kfgetgoodsform', Null, $connect_str);

        $kfgetgoodsformModel->where($where)->delete();
        //$kfgetgoodsformModel->getLastSql();

        $data = $getgoodsform_array;
        $data ['id'] = $this->get_order_sn();
        $data ['area'] = $this->getArea($shopid);

        $kfgetgoodsformModel->create();
        $kfgetgoodsformModel->add($data);
        var_dump($kfgetgoodsformModel->getLastSql());
        //Log::write ( '领餐单：'.$kfgetgoodsformModel->getLastSql () , LOG::INFO, LOG::FILE, $this->LogFile );

    }


    /**
     * 获得地区
     */
    function getArea($shopid)
    {
        switch ($shopid) {
            case 4:
                return '常州地区';
                break;
            case 6:
                return '南京地区';
                break;
            case 9:
                return '广州地区';
                break;

        }
    }

    //根据地区id,获取数据库名
    function  getDbName($shopid)
    {
        switch ($shopid) {
            case 4:
                return 'bjlihua';
                break;
            case 6:
                return 'bjlihua';
                break;
            case 9:
                return 'bjlihua';
                break;

        }
    }

    function getConnectStr($dbname)
    {
        return "mysql://root:@localhost/$dbname#utf8";
    }

    /**
     * 得到新订单号
     *
     * @return string
     */
    function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand(( double )microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}

?>
