<?php
/**
 * Created by zhangwh1234.
 * User: lihua.com
 * Date: 15/7/6
 * Time: 下午4:41
 * 完成百度外卖的部分操作
 */

class BaiduWaimaiAction extends Action{

    //百度订单完成接口的操作
    public function orderComplete(){
        import('@.Extend.Baiduwaimai');
        $baiduwaimaiApi = new Baiduwaimai();


        $month = date('m');
        $month = '07';
        //获取需要传递完成的订单
        $kforderformModel = D('kforderform_'.$month);

        $where = array();
        $where['cTelname'] = array('LIKE','%百%');
        $where['iscomplete'] = 0;
        $where['isjiezhang'] = 1;
        $kforderformResult = $kforderformModel->where($where)->limit(100)->select();
        foreach($kforderformResult as $value){
            $resp = $baiduwaimaiApi->OrderComplete($value);
            $id = $value['id'];
            $where = array();
            $where['id'] = $id;
            $data = array();
            $data['iscomplete'] = 1;
            $kforderformModel->where($where)->save($data);

        }
    }

    //取消订单的操作
    public function orderCancel(){
        import('@.Extend.Baiduwaimai');
        $baiduwaimaiApi = new Baiduwaimai();


        $month = date('m');
        $month = '07';
        //获取需要传递完成的订单
        $kforderformModel = D('kforderform_'.$month);

        $where = array();
        $where['cTelname'] = array('LIKE','%百%');
        $where['iscomplete'] = 0;
        $where['isjiezhang'] = 0;
        $kforderformResult = $kforderformModel->where($where)->limit(100)->select();
        foreach($kforderformResult as $value){
            $resp = $baiduwaimaiApi->OrderCancel($value);
            $id = $value['id'];
            $where = array();
            $where['id'] = $id;
            $data = array();
            $data['iscomplete'] = 1;
            $kforderformModel->where($where)->save($data);

        }
    }
}
