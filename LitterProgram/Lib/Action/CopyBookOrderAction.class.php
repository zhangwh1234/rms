<?php

/**
 * 自动拷贝预订订单到订单表中
 * 2014-06-18
 * /Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php CopyBookOrder/index
 */
class CopyBookOrderAction extends Action
{
    // 定义数据库的连接字符
    var $dnsConnectionDB = '';

    public function index()
    {
        $dataConnectArr = array(
            'bj.lihuaerp.com' => '',
            'cz.lihuaerp.com' => '',
            'nj.lihuaerp.com' => '',
            'wx.lihuaerp.com' => '',
            'sz.lihuaerp.com' => '',
            'sh.lihuaerp.com' => '',
            'gz.lihuaerp.com' => '',
            'localhost' => 'mysql://root:@localhost:3306/rms',
        );

        foreach($dataConnectArr as $domain =>$value){
            // 获得数据库的路径
            $this->dnsConnectionDB = $value;
            $this->bookorder($domain);
        }
    }

    public function bookorder($domain){
        //预订表
        $bookorderModel = M('bookorder', 'rms_', $this->dnsConnectionDB);
        //预订产品
        $bookproductModel = M('bookproducts', 'rms_', $this->dnsConnectionDB);
        //预订活动表
        $bookactivityModel = M('bookactivity', 'rms_', $this->dnsConnectionDB);
        //预订支付表
        $bookpaymentModel = M('bookpayment', 'rms_', $this->dnsConnectionDB);
        //预订日志表
        $bookactionModel = M('bookaction', 'rms_', $this->dnsConnectionDB);

        //订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);
        //订货表
        $orderproductsModel = M('orderproducts', 'rms_', $this->dnsConnectionDB);
        //活动表
        $orderactivityModel = M('orderactivity', 'rms_', $this->dnsConnectionDB);
        //支付表
        $orderpaymentModel = M('orderpayment', 'rms_', $this->dnsConnectionDB);
        //日志表
        $orderactionModel = M('orderaction', 'rms_', $this->dnsConnectionDB);


        /**
         * 从预订表中读出预订订单,写入订单表中
         */
        $where = array();
        $where['bookdate'] = date('Y-m-d');
        $bookorderResult = $bookorderModel->where($where)->select();
        foreach ($bookorderResult as $orderValue) {

            $bookorderid = $orderValue['bookorderid'];

            $ordersn = rand(1000, 9999) . date('Ymd') . $orderValue['bookorderid'];
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['clientname'] = $orderValue['clientname'];
            $data['address'] = $orderValue['address'];
            $data['telphone'] = $orderValue['telphone'];
            $data['ordertxt'] = $orderValue['ordertxt'];
            $data['beizhu'] = $orderValue['beizhu'];
            $data['totalmoney'] = $orderValue['totalmoney'];
            $data['paidmoney'] = $orderValue['paidmoney'];
            $data['shouldmoney'] = $orderValue['shouldmoney'];
            $data['custtime'] = $orderValue['custtime'];
            $data['custdate'] = date('Y-m-d');
            $data['ap'] = $orderValue['ap'];
            $data['telname'] = $orderValue['telname'];
            $data['rectime'] = $orderValue['rectime'];
            $data['recdate'] = $orderValue['recdate'];
            $data['state'] = '预订';
            $data['invoiceheader'] = $orderValue['invoiceheader'];
            $data['invoicebody'] = $orderValue['invoicebody'];
            $data['shippingname'] = $orderValue['shippingname'];
            $data['shippingmoney'] = $orderValue['shippingmoney'];
            $data['domain'] = $domain;
            $data['lastdatetime'] = date('Y-m-d H:i:s');
            $orderformModel->create();
            $record = $orderformModel->add($data);
            var_dump($orderformModel->getLastSql());


            $where = array();
            $where['bookorderid'] = $bookorderid;
            //取预订产品的内容
            $bookproductResult = $bookproductModel->where($where)->select();
            foreach ($bookproductResult as $productsValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data ['ordersn'] = $ordersn;
                $data['code'] = $productsValue['code'];
                $data['name'] = $productsValue['name'];
                $data['shortname'] = $productsValue['shortname'];
                $data['price'] = $productsValue['price'];
                $data['number'] = $productsValue['number'];
                $data['money'] = $productsValue['money'];
                $orderproductsModel->create();
                $orderproductsModel->add($data);
                var_dump($orderproductsModel->getLastSql());
            }

            //保存活动表
            $bookactivityResult = $bookactivityModel->where($where)->select();
            foreach ($bookactivityResult as $activityValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data['ordersn'] = $ordersn;
                $data['name'] = $activityValue['name'];
                $data['money'] = $activityValue['money'];
                $data['date'] = date('Y-m-d H:i:s');
                $orderactivityModel->create();
                $orderactivityModel->add($data);
            }

            //保存支付表
            $bookpaymentResult = $bookpaymentModel->where($where)->select();
            foreach ($bookpaymentResult as $paymentValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data['ordersn'] = $ordersn;
                $data['name'] = $paymentValue['name'];
                $data['money'] = $paymentValue['money'];
                $data['date'] = date('Y-m-d H:i:s');
                $orderpaymentModel->create();
                $orderpaymentModel->add($data);
            }


            //保存到订单表日志中
            //记入操作到action中
            $action = array();
            $action['ordersn'] = $ordersn;  //订单号
            $action['action'] = '将预订单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '输入订单表中';
            $action['logtime'] = date('Y-m-d H:i:s');
            $orderactionModel->create();
            $result = $orderactionModel->add($action);
            var_dump($orderactionModel->getLastSql());


            //记入到预订的日至中
            //记入操作到action中
            $action = array();
            $action['bookorderid'] = $bookorderid;  //预订单号
            $action['action'] = '将预订单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '输入订单表中，订单号：' . $record;
            $action['logtime'] = date('H:i:s');
            $bookactionModel->create();
            $result = $bookactionModel->add($action);
            var_dump($bookactionModel->getLastSql());


            //修改预订的订单状态
            $where = array();
            $where['bookorderid'] = $bookorderid;
            $data = array();
            $data['state'] = '已处理';
            $bookorderModel->where($where)->save($data);

        }


        /**
         * 清除无效的订单
         * 计算最后修改日期是一周前
         */
        $where = 'TO_DAYS(NOW()) - TO_DAYS(bookdate) >= 3';
        $bookorderResult = $bookorderModel->where($where)->select();
        foreach ($bookorderResult as $bookorderValue) {
            $where = array();
            $where['bookorderid'] = $bookorderValue['bookorderid'];
            //没有预订日期,可以清除
            //清除预订订单
            $bookorderModel->where($where)->delete();
            var_dump($bookorderModel->getLastSql());
            //清除预订产品
            $bookproductModel->where($where)->delete();
            //清除预订活动表
            $bookactivityModel->where($where)->delete();
            //清除预订支付表
            $bookpaymentModel->where($where)->delete();
            //清除预订日志表
            $bookactionModel->where($where)->delete();


        }


    }

}

?>
