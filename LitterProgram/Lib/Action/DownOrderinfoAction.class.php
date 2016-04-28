<?php
  /**
  * 下载订单到老的送餐系统中
  * 
  * http://115.29.190.255:8887/index.php?s=/DownOrderinfo/getOrder/name/bj/pwd/lihua.html
  */
  class DownOrderinfoAction extends Action{
      
      /**
        * 取得网站的订单的函数
        * 
        */     
        public function getOrder(){
            //载入curl函数等
            load("@.function");
            $currentDate = date('Y-m-d');
            
            //用户名
            $name = $_REQUEST['name'];
            if($name == 'bj'){
                
            }else{
                 $this->error('error');
            }
            //密码
            $pwd = $_REQUEST['pwd'];
            if($pwd == ''){
                
            }else{
                $this->error('error');
            }
            
            //字段表
            $fields = array(
            'order_id','order_sn','address','tel','shipping_time','best_time',
            'referer','postscript','order_amount','inv_payee','inv_content','pay_id'
            );
            $orderinfoModel = D('order_info');
            $ordergoodsModel = D('order_goods');
            
            $where = array();
            $where['rms_state'] = 0;
            $where["FROM_UNIXTIME( add_time,'%Y-%m-%d')"] = $currentDate;
            $orderinfoResult = $orderinfoModel->field($fields)->where("rms_state = 0  and FROM_UNIXTIME(add_time,'%Y-%m-%d')='$currentDate' and suppliers_id = 1")->limit(10)->select();

            $orderinfoArray = array();
            foreach($orderinfoResult as $value){
                $orderinfoid = $value['order_id'];
                $fields = array('goods_id','goods_name','goods_number','goods_price');
                $ordergoodsResult = $ordergoodsModel->field($fields)->where("order_id=$orderinfoid")->select();
                foreach($ordergoodsResult as $goodsKey=>$goodsValue){
                     $goods_id = $goodsValue['goods_id'];
                     $shortName = $this->getShortName($goods_id);
                     if($shortName){
                         $ordergoodsResult[$goodsKey]['shortname'] = $shortName['goods_sname'];
                     }else{
                         $ordergoodsResult[$goodsKey]['shortname'] = $goodsValue['goods_name'];
                     }
                }
                $value['goods'] = $ordergoodsResult;
                $orderinfoArray[] = $value;
                //p($ordergoodsResult);
            }

            //p($orderinfoArray);
            $this->ajaxReturn($orderinfoArray);          
        }

      
  }
?>