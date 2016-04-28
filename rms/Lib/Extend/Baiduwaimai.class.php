<?php

/**
 * Created by zhangwh1234
 * User: lihua
 * Date: 15/6/25
 * Time: 下午5:08
 * 百度外卖的接口处理函数
 */
class Baiduwaimai
{

    private $source = ""; //"65524";
    private $secret = "";  //'983269a9ba70f146';
    private $url = "http://api.waimai.baidu.com";

    public function __construct(){
        //获得百度接入参数
        $info = $this->baiduwaimaiInfo();
        $this->source = $info['source'];
        $this->secret = $info['secret'];
    }

    //获得百度接入参数
    public function baiduwaimaiInfo(){
        $where = array();
        $where['domain'] = $_SERVER['HTTP_HOST'];
        $baiduwaimaiModel = D('baiduwaimai');
        $baiduwaimaiResult = $baiduwaimaiModel->where($where)->find();
        if($baiduwaimaiResult){
            return $baiduwaimaiResult;
        }else{
            return array();
        }
    }

    //创建商户的接口
    public function shopinfoCreate($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];
        $body['name'] = $info['name'];
        $body['shop_logo'] = $info['shop_logo'];
        $body['province'] = $info['province'];
        $body['city'] = $info['city'];
        $body['county'] = $info['county'];
        $body['address'] = $info['address'];
        $body['brand'] = $info['brand'];
        $body['category1'] = $info['category1'];
        $body['category2'] = $info['category2'];
        $body['category3'] = $info['category3'];
        $body['phone'] = $info['phone'];
        $body['service_phone'] = $info['service_phone'];
        $body['longitude'] = $info['longitude'];
        $body['latitude'] = $info['latitude'];
        $body['coord_type'] = 'bdll';
        $delivery_region_region_arr = explode(',', $info['delivery_region_region']);
        for ($i = 0; $i < count($delivery_region_region_arr); $i++) {
            $delivery_region_region_tmp['longitude'] = $delivery_region_region_arr[$i];
            $i=$i +1;
            $delivery_region_region_tmp['latitude'] = $delivery_region_region_arr[$i];
            $delivery_region_region_t1[] = $delivery_region_region_tmp;
        }
        $delivery_region_region[] = $delivery_region_region_t1;
        $body['delivery_region'] = array(
            array(
                'name' => $info['delivery_region_name'],
                'region' => $delivery_region_region,
                'delivery_time' => $info['delivery_region_time'],
                'delivery_fee' => $info['delivery_region_fee']
            )
        );
        $business_time_arr = explode(',', $info['business_time']);
        $body['business_time'] = array(
            array(
                'start' => $business_time_arr[0],
                'end' => $business_time_arr[1]
            ),
            array(
                'start' => $business_time_arr[2],
                'end' => $business_time_arr[3]
            )
        );
        $body['book_ahead_time'] = $info['book_ahead_time'];
        $body['invoice_support'] = $info['invoice_support'];
        $body['min_order_price'] = $info['min_order_price'];
        $body['package_box_price'] = $info['package_box_price'];
        $body['threshold'] = array(
            array(
                'num' => 100000,
                'time' => "0|8|*"
            ),
            array(
                'num' => 100000,
                'time' => "0|16|*"
            )

        );


        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.create";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.create";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '新建商户';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $body['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;

    }


    //下线商户
    public function shopinfoOffline($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.offline";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.offline";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '商户下线操作';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;
    }


    //列表所有商户信息
    public function shopinfoList()
    {
        $signArray = array();
        //创建商户命令
        $signArray['cmd'] = "shop.list";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;
        //
        $signArray['body'] = array();


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.list";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //
        $cmdArray['body'] = array();

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));
        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '查询服务器上的商户';
        foreach($returnArray['body']['data'] as $key=>$value){
            $data['data'][] =  $value;
        }

        return $data;

    }

    //更新商户
    public function shopinfoUpdate($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];
        $body['name'] = $info['name'];
        $body['shop_logo'] = $info['shop_logo'];
        $body['province'] = $info['province'];
        $body['city'] = $info['city'];
        $body['county'] = $info['county'];
        $body['address'] = $info['address'];
        $body['brand'] = $info['brand'];
        $body['category1'] = $info['category1'];
        $body['category2'] = $info['category2'];
        $body['category3'] = $info['category3'];
        $body['phone'] = $info['phone'];
        $body['service_phone'] = $info['service_phone'];
        $body['longitude'] = $info['longitude'];
        $body['latitude'] = $info['latitude'];
        $body['coord_type'] = 'bdll';
        $delivery_region_region_arr = explode(',', $info['delivery_region_region']);
        for ($i = 0; $i < count($delivery_region_region_arr); $i++) {
            $delivery_region_region_tmp['longitude'] = $delivery_region_region_arr[$i];
                $i=$i +1;
            $delivery_region_region_tmp['latitude'] = $delivery_region_region_arr[$i];
            $delivery_region_region_t1[] = $delivery_region_region_tmp;
        }
        $delivery_region_region[] = $delivery_region_region_t1;
        $body['delivery_region'] = array(
            array(
                'name' => $info['delivery_region_name'],
                'region' => $delivery_region_region,
                'delivery_time' => $info['delivery_region_time'],
                'delivery_fee' => $info['delivery_region_fee']
            )
        );
        $business_time_arr = explode(',', $info['business_time']);
        $body['business_time'] = array(
            array(
                'start' => $business_time_arr[0],
                'end' => $business_time_arr[1]
            ),
            array(
                'start' => $business_time_arr[2],
                'end' => $business_time_arr[3]
            )
        );
        $body['book_ahead_time'] = $info['book_ahead_time'];
        $body['invoice_support'] = $info['invoice_support'];
        $body['min_order_price'] = $info['min_order_price'];
        $body['package_box_price'] = $info['package_box_price'];
        $body['threshold'] = array(
            array(
                'num' => 100000,
                'time' => "0|8|*"
            ),
            array(
                'num' => 100000,
                'time' => "0|16|*"
            )
        );


        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.update";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.update";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));
        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '更新商户信息';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;

    }

    //商户暂停营业
    public function shopinfoClose($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.close";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.close";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '下线商户操作';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;

    }


    //商户开始营业
    public function shopinfoOpen($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.open";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.open";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '商户开始营业操作';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;

    }

    //设置商户订单阈值
    public function shopinfoTheshold($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];
        $body['threshold'] = array(array(
            'num' => 10,
            'time' => "0|18|*"
        )
        );

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.threshold.set";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.threshold.set";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '商户订单阈值操作';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;

    }

    //设置商户配送时延
    public function shopinfoDelivery($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];
        $body['delivery_delay_time'] = $info['delivery_delay_time'];

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.delivery.delay";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.delivery.delay";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '商户配送时延';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;
    }

    //上传商户公告
    public function shopinfoContent($info)
    {
        $body = array();
        $body['shop_id'] = $info['shopinfoid'];
        $body['content'] = $info['content'];

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "shop.announcement.set";
        //时间戳
        $signArray['timestamp'] = time();
        //版本号
        $signArray['version'] = 2;
        //请求唯一标示
        $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $signArray['source'] = $this->source;
        //秘钥
        $signArray['secret'] = $this->secret;


        //创建命令
        $cmdArray = array();
        //创建商户命令
        $cmdArray['cmd'] = "shop.announcement.set";
        //时间戳
        $cmdArray['timestamp'] = $signArray['timestamp'];
        //版本号
        $cmdArray['version'] = 2;
        //请求唯一标示
        $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
        //合作方账号
        $cmdArray['source'] = $this->source;
        //数据体
        $cmdArray['body'] = $body;

        //生成签名
        $cmdArray['sign'] = $this->getSign($signArray);
        //执行返回的结果
        $resp = curl_post($this->url, json_encode($cmdArray));
        //将结果转化成数组
        $returnArray = json_decode($resp, true);

        $data = array();
        $data['cmd'] = '上传商户公告';
        $data['shop_id'] = $body['shop_id'];
        $data['name'] = $info['name'];
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;
    }

    //新增商品分类
    public function categorymgrCreate($info, $shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['shop_id'] = $value['shopinfoid'];
            $body['name'] = $info['name'];
            $body['rank'] = $info['rank'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.category.create";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.category.create";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }

        $data = array();
        $data['cmd'] = '新增菜品分类';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;

        return $data;
    }

    /**
     * 商品分类更新
     */
    public function categorymgrUpdate($info,$shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['shop_id'] = 20;
            $body['name'] = $info['name'];
            $body['rank'] = $info['rank'];
            $body['old_name'] = $info['old_name'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.category.update";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.category.update";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }
        $data = array();
        $data['cmd'] = '更新菜品分类';
        $data['name'] = $info['name'];
        $data['old_name'] = $info['old_name'];
        $data['data'] = $returnData;
        return $data;
    }

    //新增菜品
    public function menumgrCreate($info, $shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = $value['shopinfoid'];
            $body['name'] = $info['name'];
            $body['upc'] = $info['upc'];
            $body['price'] = $info['price'];
            $body['pic'] = $body['pic'];
            $body['min_order_num'] = $info['min_order_num'];
            $body['package_box_num'] = $info['package_box_num'];
            $body11['available_time'] = array(array(
                'start' => '12:00',
                'end' => '12:00'
            )
            );
            $body['threshold'] = array(array(
                'num' => 10,
                'time' => "0|18|*"
            )
            );
            $categoryArr = explode(',', $info['category']);
            $body['category'] = array(
                array(
                    'name' => $categoryArr[0],
                    'rank' => $categoryArr[1]
                )
            );


            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.create";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.create";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }
        $data = array();
        $data['cmd'] = '新建菜品';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }

    //更新菜品
    public function menumgrUpdate($info, $shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = $value['shopinfoid'];
            $body['name'] = $info['name'];
            $body['upc'] = $info['upc'];
            $body['price'] = $info['price'];
            $body['pic'] = $info['pic'];
            $body['min_order_num'] = $info['min_order_num'];
            $body['package_box_num'] = $info['package_box_num'];
            $body['description'] = $info['description'];
            $body['available_times'] = array(
                '*' =>
                    array(
                        array(
                            'start' => '10:00',
                            'end' => '13:00'
                        ),
                        array(
                            'start' => '14:00',
                            'end' => '19:00'
                        )
                    )
            );
            $body['threshold'] = array(array(
                'num' => $info['threshold'],
                'time' => "0|9|*"
            ),array(
                'num' => $info['threshold'],
                'time' => "0|17|*"
            )
            );

            $categoryArr = explode(',', $info['category']);
            $body['category'] = array(
                array(
                    'name' => $categoryArr[0],
                    'rank' => $categoryArr[1]
                )
            );

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.update";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.update";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }
        $data = array();
        $data['cmd'] = '更新菜品';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;

    }

    //更新菜品
    public function menumgrDelete($info, $shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = $value['shopinfoid'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.update";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.update";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }
        $data = array();
        $data['cmd'] = '删除菜品';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;

    }

    //菜品上线
    public function menumgrOnline($info,$shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = $value['shopinfoid'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.online";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.online";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));

            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }

        $data = array();
        $data['cmd'] = '菜品上线';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }

    //菜品下线
    public function menumgrOffline($info,$shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = $value['shopinfoid'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.offline";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.offline";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));

            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }

        $data = array();
        $data['cmd'] = '菜品下线';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }


    //菜单阈值设置
    public function menumgrThreshold($info,$shopinfo)
    {
        $returnData = array();
        foreach ($shopinfo as $value) {
            $body = array();
            $body['dish_id'] = $info['menuid'];
            $body['shop_id'] = 20;

            $body['threshold'] = array(array(
                'num' => 10,
                'time' => "0|18|*"
            )
            );


            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "dish.threshold.set";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "dish.threshold.set";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));

            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['shopinfoid'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];
        }

        $data = array();
        $data['cmd'] = '菜品订单阈值设置';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }


    //订单完成操作
    public function ordermgrComplete($info){
            $body = array();
            $body['order_id'] =   $info['orderid'];

            $signArray = array();
            //数据体
            $signArray['body'] = $body;
            //创建商户命令
            $signArray['cmd'] = "order.complete";
            //时间戳
            $signArray['timestamp'] = time();
            //版本号
            $signArray['version'] = 2;
            //请求唯一标示
            $signArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $signArray['source'] = $this->source;
            //秘钥
            $signArray['secret'] = $this->secret;


            //创建命令
            $cmdArray = array();
            //创建商户命令
            $cmdArray['cmd'] = "order.complete";
            //时间戳
            $cmdArray['timestamp'] = $signArray['timestamp'];
            //版本号
            $cmdArray['version'] = 2;
            //请求唯一标示
            $cmdArray['ticket'] = "8C7D975C-9E9B-F8A8-0D8A-D1B5E3ECF786";
            //合作方账号
            $cmdArray['source'] = $this->source;
            //数据体
            $cmdArray['body'] = $body;

            //生成签名
            $cmdArray['sign'] = $this->getSign($signArray);
            //执行返回的结果
            $resp = curl_post($this->url, json_encode($cmdArray));

            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $returnArray['body']['errno'];
            $returnData[] = $returnArray['body']['error'];


        $data = array();
        $data['cmd'] = '订单完成操作';
        $data['data'] = $returnData;
        return $data;
    }

    //****************************************************
    //上线接口
    public function orderConfirm(){

    }

    //编制签名
    public function getSign($signArray)
    {
        $this->ksort_recursive($signArray);
        return strtoupper(md5(json_encode($signArray)));
    }

    // 返回排序的url
    public function ksort_recursive(&$arr)
    {
        ksort($arr);
        foreach ($arr as &$v) {
            if (is_array($v)) {
                $this->ksort_recursive($v);
            }
        }
    }

    public function order($array, $pid = 0)
    {
        $arr = array();
        foreach ($array as $v) {
            if ($v['pid'] == $pid) {
                $arr[] = $v;
                $arr = array_merge($arr, order($array, $v['id']));
            }
        }
        return $arr;
    }


    /**
     * cur的处理函数,post方式
     */
    function curl_post($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

}

