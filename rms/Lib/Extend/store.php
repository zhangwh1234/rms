<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\general\LihuaStore;
use app\common\model\LihuaOrder;
use app\common\model\LihuaOrderGoods;
use app\common\model\LihuaOrderLogistics;
use app\common\model\LihuaOrderPayment;

/**
 * 订单处理
 */
class Lhorder extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 抓取城市订单列表
     *
     * @ApiMethod   (POST)
     * @ApiReturn   ({"code":1,"msg":"OK","time":"1573374975","data":{"orderList":[{"id":53,"sn":"190911421954660","user_id":2,"city_id":3,"city_name":"常州","store_id":23,"store_name":"怀南","consignee":"f23f23f","mobile":"13888888888","address":"白氏留青竹刻博物馆001","lng":"119.937757","lat":"31.781121","packing_fee":"1.00","deliver_fee":"3.00","enough_name":"","enough_fee":"0.00","bonus_name":"","bonus_fee":"0.00","deliver_time":"2019-09-19 17:00:00","tableware":1,"invo_type":2,"invo_title":"wrwer11111","invo_taxno":"3r3r3r2222","remark":"","goods_amount":"26.00","original_amount":"30.00","order_amount":"30.00","discount":"0.00","add_time":"2019-09-19 14:33:40","platform":"app","pay_type":2,"pay_status":1,"pay_time":"2019-09-19 14:33:40","status":2},{"id":171,"sn":"191046362879001","user_id":0,"city_id":3,"city_name":"常州","store_id":17,"store_name":"鸣新","consignee":"啦啦啦","mobile":"17088888888","address":"东吴面馆7777","lng":"119.936226","lat":"31.677160","packing_fee":"0.00","deliver_fee":"0.01","enough_name":"","enough_fee":"0.00","bonus_name":"","bonus_fee":"0.00","deliver_time":"2019-10-28 11:00:00","tableware":0,"invo_type":0,"invo_title":"","invo_taxno":"","remark":"","goods_amount":"0.01","original_amount":"0.02","order_amount":"0.02","discount":"0.00","add_time":"2019-10-28 09:20:18","platform":"app","pay_type":1,"pay_status":1,"pay_time":"2019-10-28 09:20:26","status":2}],"goodsList":{"53":[{"id":46,"name":"雪菜汤料","short_name":"雪菜汤","price":1,"pic":"http://image.lihua.com/images/newweb/2016/09/thumb_57cfc709e088f.jpg","num":1,"num_price":1},{"id":10015,"name":"特别的爱砂锅","short_name":"特爱砂锅","price":25,"pic":"http://image.lihua.com/images/newweb/2019/08/thumb_5d5b494cbe158.jpg","num":1,"num_price":25}],"171":[{"id":1180,"name":"冬菇滑鸡饭","short_name":"冬菇滑鸡饭","price":0.01,"pic":"https://lihua.czapi.cn/uploads/goods/2019/10/20725d9efea561b3e.jpg","num":1,"num_price":0.01}]},"payList":{"53":[{"pay_name":"支付宝支付","pay_money":"0.02"}],"171":[{"pay_name":"现金券","pay_money":"5.00"},{"pay_name":"支付宝支付","pay_money":"10.00"}]}}})
     */
    public function lists()
    {
        #$city_id = (int)$this->request->request('city_id');
        #if (!$city_id) $this->error('参数错误');
        $store = LihuaStore::all(function ($query) {
            $query->field('id,name', false)
                ->limit(100);
        });

        foreach ($store as $s_key => $s_value) {
            $store_id = $s_value['id'];
            $list = LihuaOrder::all(function ($query) use ($store_id) {
                $query->where(['store_id' => $store_id, 'status' => 2])
                    ->field('goods,confirm_time,store_time,rider_time,finish_time,cancel_time,create_time,update_time', true)
                    ->limit(30);
            });
            //支付方式:1=支付宝支付,2=微信支付,3=丽华钱包,4=货到付款,5=贵宾卡
            $pay_type_text = ['', '支付宝', '微支付', '丽华钱包', '货到付款', '贵宾卡'];
            $data = [];
            if ($list) {
                $order_ids = array_column($list, 'id');
                $goodsList = LihuaOrderGoods::ordersGoodsList($order_ids);

                $res = LihuaOrderPayment::whereIn('order_id', $order_ids)
                    ->field('order_id,pay_name,pay_money')->select();
                $payList = [];
                foreach ($res as $k => $v) {
                    $payList[$v['order_id']][] = [
                        'pay_name' => $v['pay_name'],
                        'pay_money' => $v['pay_money'],
                    ];
                }
                foreach ($list as $k => $v) {
                    $payText = '';
                    if ($v['pay_type'] != 4) {
                        $payText = '(' . $pay_type_text[$v['pay_type']] . ')';
                    }
                    if ($v['enough_fee'] > 0) {
                        $payText = $payText . '(满减-' . $v['enough_fee'] . '元)';
                    }
                    if ($v['bonus_fee'] > 0) {
                        $payText = $payText . '(红包-' . $v['bonus_fee'] . '元)';
                    }
                    $list[$k]['address'] = $payText . $v['address'];
                }
                $data = [
                    'orderList' => $list,
                    'goodsList' => $goodsList,
                    'payList' => $payList,
                ];
            }
        }
        $this->success('OK', $data);
    }

    /**
     * 更新订单状态
     *
     * @ApiMethod   (POST)
     * @ApiParams   (name="sn", type="string", required=true, description="订单号")
     * @ApiParams   (name="status", type="int", required=true, description="订单状态")
     * @ApiParams   (name="des", type="string", required=true, description="状态描述")
     * @ApiReturn   ({"code":1,"msg":"OK","time":"1570765630","data":{}})
     */
    public function updatestatus()
    {
        //状态：0=取消订单，3=确认订单，4=分配门店，5=骑手配送，6=订单完成
        $sn = $this->request->request('sn');
        $status = $this->request->request('status');
        $des = $this->request->request('des', '');
        if (!$sn || !in_array($status, [0, 3, 4, 5, 6])) {
            $this->error('参数错误');
        }

        $row = LihuaOrder::getBySn($sn);
        if (!$row) {
            $this->error($sn . ' 订单不存在');
        }

        switch ($status) {
            case 3:
                if ($row->status > 2) {
                    $this->error($sn . '订单状态错误');
                }

                $row->status = 3;
                $row->confirm_time = date('Y-m-d H:i:s');
                break;

            case 4:
                if ($row->status > 3) {
                    $this->error($sn . '订单状态错误');
                }

                $row->status = 4;
                $row->store_time = date('Y-m-d H:i:s');
                break;

            case 5:
                if ($row->status > 4) {
                    $this->error($sn . '订单状态错误');
                }

                $row->status = 5;
                $row->rider_time = date('Y-m-d H:i:s');
                break;

            case 6:
                if ($row->status > 5) {
                    $this->error($sn . '订单状态错误');
                }

                $row->status = 6;
                $row->finish_time = date('Y-m-d H:i:s');
                break;

            case 0:
                $row->status = 0;
                $row->cancel_time = date('Y-m-d H:i:s');
                break;
        }
        $row->save();
        if ($des) {
            LihuaOrderLogistics::create([
                'order_id' => $row['id'],
                'order_sn' => $sn,
                'des' => $des,
            ]);
        }

        $this->success('OK');
    }

}
