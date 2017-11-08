<?php
/**
 * Created by zhangwh1234
 * User: lihua.com
 * Date: 16/8/9
 * Time: 下午1:06
 * 通过地图显示订单位置和显示送餐员的位置
 */

class MapActon extends ModuleAction{

    /**
     * 通过订单号显示订单位置和送餐员位置
     */
    public function getOrderLocation($ordersn){

        $this->display('location');
    }



}