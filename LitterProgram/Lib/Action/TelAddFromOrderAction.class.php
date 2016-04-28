<?php
    /**
    * 来电添加系统
    * 将来电填入地址系统中
    * 2014-06-10
    */

    class TelAddFromOrderAction extends Action{
        public function index(){
            //订单表
            $orderformModel = D('Orderform');
            //来电客户表
            $telcustomerModel = D('Telcustomer');
            //来电地址表
            $teladdressModel = D('Teladdress');

            //查订单
            $where = array();
            $orderformResult = $orderformModel->where($where)->select();
            foreach($orderformResult as $value){
                $where = array();
                $where['telphone'] = $value['telphone'];
                $telcustomerResult = $telcustomerModel->where($where)->find();
                if($telcustomerResult){
                    //更新来电客户表
                    $where = array();
                    $where['telphone'] = $value['telphone'];
                    $data = array();
                    $data['rectime'] = date('Y-m-d');
                    $data['address'] = $value['address'];
                    $telcustomerModel->where($where)->save($data);
                    var_dump($telcustomerModel->getLastSql());
                    $telcustomerResult = $telcustomerModel->where($where)->find();
                    var_dump($telcustomerResult);
                    //更新来电地址表
                    $where = array();
                    $data = array();
                    $data['telcustomerid'] = $telcustomerResult['telcustomerid'];
                    $data['address']  = $value['address'];
                    $data['company'] = $value['company'];
                    $teladdressModel->create();
                    $teladdressModel->add($data);
                    var_dump($teladdressModel->getLastSql());
                }else{
                    //存入来电客户表
                    $data = array();
                    $data['name'] = $value['clientname'];
                    $data['telphone'] = $value['telphone'];
                    $data['rectime'] = date('Y-m-d');
                    $telcustomerModel->create();
                    $telcustomerModel->add($data);
                    var_dump($telcustomerModel->getLastSql());
                    //存入来电地址表
                    $data = array();
                    $data['telcustomerid'] = $telcustomerModel->getLastInsID();
                    $data['telphone'] = $value['value'];
                    $data['address'] = $value['address'];
                    $data['company'] = $value['company'];
                    $teladdressModel->create();
                    $teladdressModel->add($data);  
                    var_dump($teladdressModel->getLastSql());                  
                } 
            }
        }

    }
?>
