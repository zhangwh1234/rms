<?php
    /**
    * 自动拷贝预订订单到订单表中
    * 2014-06-18
    */

    class CopyBookOrderAction extends Action{
        public function index(){
            //预订表
            $bookorderModel = D('Bookorder');
            //预订产品
            $bookproductModel = D('Bookproducts');  
            //取预订表日期
            $bookdateModel = D('Bookdate');
            //订单表
            $orderformModel = D('Orderform');
            //订货表
            $orderproductsModel = D('Orderproducts');
            $where = array();
            $where['bookdate'] = date('Y-m-d');
            $bookdateResult = $bookdateModel->where($where)->select();
            foreach($bookdateResult as $dateValue){
                $where = array();
                $where['bookorderid'] = $dateValue['bookorderid'];  
                //取预订表内容
                $bookorderResult = $bookorderModel->where($where)->find();
                //保存到预订表中
                if(!empty($bookorderResult)){
                    $data = array(); 
                    $data['clientname'] = $bookorderResult['clientname']; 
                    $data['address'] = $bookorderResult['address'];
                    $data['telphone'] = $bookorderResult['telphone'];
                    $data['ordertxt'] = $bookorderResult['ordertxt'];
                    $data['beizhu'] = $bookorderResult['beizhu'];
                    $data['totalmoney'] = $bookorderResult['totalmoney'];
                    $data['custtime'] = $bookorderResult['custtime'];
                    $data['custdate'] = date('Y-m-d');
                    $data['ap'] = $bookorderResult['ap'];
                    $data['telname'] = $bookorderResult['telname'];
                    $data['rectime'] = $bookorderResult['rectime'];
                    $data['recdate'] = $bookorderResult['recdate'];
                    $data['state'] = '预订';
                    $data['invoiceheader'] = $bookorderResult['invoiceheader'];
                    $data['invoicebody'] = $bookorderResult['invoicebody'];
                    $data['shippingname'] = $bookorderResult['shippingname'];
                    $data['shippingmoney'] = $bookorderResult['shippingmoney'];  
                    $orderformModel->create();
                    $record = $orderformModel->add($data); 
                    var_dump($orderformModel->getLastSql());
                }
                //订单编号
                $ordersn = $record . date('Ymd');
                //保存订单编号
                $data = array();
                $data ['ordersn'] = $ordersn;
                $where = array();
                $where['orderformid'] = $record;
                $orderformModel->where($where)->save($data);
                 
                
                //去预订产品的内容
                $bookproductResult = $bookproductModel->where($where)->select();
                foreach($bookproductResult as $productsValue){
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
                
                //保存到订单表日志中
                //记入操作到action中
                $orderactionModel = D('Orderaction');
                $action = array();
                $action['orderformid'] = $record;  //订单号
                $action['ordersn'] = $ordersn;
                $action['action'] = '将预订单'.$bookorderResult['address'].' '.$bookorderResult['ordertxt'].'输入订单表中';
                $action['logtime'] = date('Y-m-d H:i:s');
                $orderactionModel->create();
                $result = $orderactionModel->add($action);
                var_dump($orderactionModel->getLastSql());

                //记入到预订的日至中
                //记入操作到action中
                $bookactionModel = D('Bookaction');
                $action = array();
                $action['bookorderid'] = $dateValue['bookorderid'];  //预订单号
                $action['action'] ='将预订单'.$bookorderResult['address'].' '.$bookorderResult['ordertxt'].'输入订单表中，订单号：'.$record;
                $action['logtime'] = date('H:i:s');
                $bookactionModel->create();
                $result = $bookactionModel->add($action);
                var_dump($bookactionModel->getLastSql());

                //清除预订的日期
                $where = array();
                $where['bookdateid'] = $dateValue['bookdateid'];
                $bookdateModel->where($where)->delete();
                var_dump($bookdateModel->getLastSql());
            }

        }

    }
?>
