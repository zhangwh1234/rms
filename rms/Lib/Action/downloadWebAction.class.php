<?php
    /**
    * 下载订单
    */
    class downloadWebAction extends Action{

        /**
        * 取得订单的函数
        * 
        */     
        public function getOrder(){
            //载入curl函数等
            load("@.function");
            $currentDate = date('Y-m-d');
            //字段表
            $fields = array(
            'order_id','order_sn','address','tel','shipping_time','best_time',
            'referer','postscript','order_amount','inv_payee','inv_content','pay_id'
            );
            $orderinfoModel = D('order_info');
            $where = array();
            $where['rms_state'] = 0;
            $where["FROM_UNIXTIME( add_time,'%Y-%m-%d')"] = $currentDate;
            $orderinfoResult = $orderinfoModel->field($fields)->where('rms_state = 0')->limit(10)->select();
            //var_dump($orderinfoModel->getLastSql());
            $orderinfoArray = array();
            foreach($orderinfoResult as $value){
                $orderinfoid = $value['order_id'];
                $ordergoodsModel = D('order_goods');
                $fields = array('goods_id','goods_name','goods_number','goods_price');
                $ordergoodsResult = $ordergoodsModel->field($fields)->where("order_id=$orderinfoid")->select();
                //foreach($ordergoodsResult as $goodsValue){
                //    p($goodsValue);
                //}
                $value['goods'] = $ordergoodsResult;
                $orderinfoArray[] = $value;
                //p($ordergoodsResult);
            }

            //p($orderinfoArray);
            $this->ajaxReturn($orderinfoArray);          
        }

        /**
        * 确认订单
        */
        public function confirmOrder(){
            //载入curl函数等
            load("@.function");
            //订单号
            $record = $_REQUEST['record'];
            //启动订单模块
            $orderinfoModel = D('OrderInfo');
            $where = array();
            $where['order_id'] = $record;
            $data = array();
            $data['rms_state'] = 1;
            $orderinfoModel->where($where)->save($data);
            //var_dump($orderinfoModel->getLastSql());
        }


    }
?>
