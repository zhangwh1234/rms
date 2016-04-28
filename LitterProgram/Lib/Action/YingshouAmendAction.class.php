<?php
/**
 * Created by zhangwh1234.
 * User: lihua.com
 * Date: 15/9/17
 * Time: 下午12:42
 * 这个程序的功能，主要用于在出现满减等活动的时候，订单金额是应收金额，需要改成订单总金额
 */

class YingshouAmendAction extends Action{

    /**
     * 修正订单总金额
     */
    public function amendMoney(){

        $kforderformModel = D('kforderform');
        $kfordergoodsModel = D('kfordergoods');

        $where = array();
        $where['isamend'] = 0;
        $kforderformResult = $kforderformModel->where($where)->limit(1000)->select();

        foreach($kforderformResult as $value){
            var_dump($value['cAddress']);
            $totalmoney = $value['mMoney'];
            $corderid = $value['cOrderID'];
            $where = array();
            $where['cOrderID'] = $corderid;
            $where['mPrice'] > 0;
            $map = "cOrderID='".$corderid."' and mPrice > 0";
            $kfordergoodsResult = $kfordergoodsModel->where($map)->select();

            //计算订单总金额
            $goodsmoney = 0;
            foreach($kfordergoodsResult as $goodsValue){
                $goodsmoney += $goodsValue['mNumber'] *  $goodsValue['mPrice'];
            }
            var_dump((float)$totalmoney);
            var_dump($goodsmoney);
            if($goodsmoney > $totalmoney){
                $where = array();
                $where['cOrderID'] = $corderid;
                $data = array();
                $data['mMoney'] = $goodsmoney;
                $data['isamend'] = 1;
                $kforderformModel->where($where)->save($data);
                var_dump($kfordergoodsModel->getLastSql());
            }else{
                $where = array();
                $where['cOrderID'] = $corderid;
                $data = array();
                $data['isamend'] = 1;
                $kforderformModel->where($where)->save($data);
                //var_dump($kfordergoodsModel->getLastSql());
            }
            usleep(10000);
        }
    }
}