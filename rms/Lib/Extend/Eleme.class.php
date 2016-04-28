<?php

/**
 * Created by zhangwh1234
 * User: lihua
 * Date: 15/6/27
 * Time: 下午12:19
 * 饿了么的接口
 * 测试用：丽华快餐
 * consumer_key	0914469256
 * consumer_secret	7c50076b32c61b201ccd0da9373ede206e2b6647
 * http://r.ele.me/lihuakuaican-1
 */
class Eleme
{
    //饿了吗分配的key参数
    public $consumer_key = '9097121068 ';  //'5534154664';   //合
    public $consumer_secret = 'bd023caa4642ae2a6cbea312c7629889124ace90'; //'0393ae038ee320adbe085fbaa280b9f4ace7a540';  //合参数
    //public $consumer_key = '0914469256';
    //public $consumer_secret = '7c50076b32c61b201ccd0da9373ede206e2b6647';
    //public $consumer_key = '7284397484';
    //public $consumer_secret = '4d31ba58fd73c71db697ab5e4946d52d';
    //public $url = 'http://v2.openapi.ele.me';


    /**
     * 获得商户所属餐厅
     * @param 没有
     * @throws Exception
     */
    public function shopGetOwn(){
        // 查询新订单ID的url
        $getUrl = 'http://v2.openapi.ele.me/restaurant/own/';
        // 时间戳
        $timestamp = time();
        // 取得数组
        $params = array(
            'consumer_key' => $this->consumer_key,
            'timestamp' => time()
        );
        // 加密前的url
        $url = $getUrl . '?' . $this->concatParams($params);
        // 记入日志
        Log::write('下载的url:' . $url, 'info');
        // 加密
        $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

        // 获得url
        $url = $url . '&sig=' . $signUrl;
        var_dump($url);

        $resp = $this->curl_get($url);

        var_dump($resp);

    }

    /**
     * 送饿了吗网站查询餐厅详细信息
     * @param $body
     * @throws Exception
     */
    public function shopinfoGetShopInfo($info){
        // 查询新订单ID的url
        $getUrl = 'http://v2.openapi.ele.me/restaurant/'.$info['restaurant_id'].'/';
        // 时间戳
        $timestamp = time();
        // 取得数组
        $params = array(
            'consumer_key' => $this->consumer_key,
            'restaurant_id' => $info['restaurant_id'],
            'timestamp' => time()
        );
        // 加密前的url
        $url = $getUrl . '?' . $this->concatParams($params);
        // 记入日志
        Log::write('下载的url:' . $url, 'info');
        // 加密
        $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

        // 获得url
        $url = $url . '&sig=' . $signUrl;
        var_dump($url);

        $resp = $this->curl_get($url);

        var_dump($resp);
    }

    //饿了吗测试接口
    public function shopUpdate($info)
    {
        // 查询新订单ID的url
        $getUrl = 'http://v2.openapi.ele.me/restaurant/'.$info['restaurant_id'].'/info/';
        // 时间戳
        $timestamp = time();
        // 取得数组
        $params = array(
            'consumer_key' => $this->consumer_key,
            'open_time' => '10:00-23:00',
            'timestamp' => time()
        );
        $param = array(
            'consumer_key' => $this->consumer_key,
            'timestamp' => $timestamp
        );
        // 加密前的url
        $url = $getUrl . '?' . $this->concatParams($param);
        // 记入日志
        Log::write('下载的url:' . $url, 'info');
        // 加密
        $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

        // 获得url
        $url = $url . '&sig=' . $signUrl;
        var_dump($url);
        //post_url
        $putFields = array(
            'open_time' => '10:00-23:00',
        );

        $resp = $this->curl_put($url,$putFields);

        var_dump($resp);

    }

    /**
     * @param $info
     * @param $shopinfo
     * @return array
     * @throws Exception
     */
    public function shopUpdateBusinessStatus($info){
        // 查询新订单ID的url
        $getUrl = 'http://v2.openapi.ele.me/restaurant/'.$info['restaurant_id'].'/business_status/';
        // 时间戳
        $timestamp = time();
        // 取得数组
        $params = array(
            'consumer_key' => $this->consumer_key,
            'is_open' => 1,
            'timestamp' => time()
        );
        $param = array(
            'consumer_key' => $this->consumer_key,
            'timestamp' => $timestamp
        );
        // 加密前的url
        $url = $getUrl . '?' . $this->concatParams($param);
        // 记入日志
        Log::write('下载的url:' . $url, 'info');
        // 加密
        $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

        // 获得url
        $url = $url . '&sig=' . $signUrl;
        var_dump($url);
        //post_url
        $putFields = array(
            'is_open' => 1,
        );

        $resp = $this->curl_put($url,$putFields);

        var_dump($resp);
    }

    //新增分类接口
    public function categorymgrCreate($info, $shopinfo)
    {
        foreach ($shopinfo as $value) {
            // 查询新订单ID的url
            $getUrl = 'http://v2.openapi.ele.me/food_category/';
            // 时间戳
            $timestamp = time();
            // 取得数组
            $params = array(
                'consumer_key' => $this->consumer_key,
                'restaurant_id' => $value['restaurant_id'],
                'name' => $info['name'],
                'weight' => $info['weight'],
                'timestamp' => $timestamp
            );
            //var_dump($params);
            $param = array(
                'consumer_key' => $this->consumer_key,
                'timestamp' => $timestamp
            );
            // 加密前的url
            $url = $getUrl . '?' . $this->concatParams($param);
            // 记入日志
            Log::write('下载的url:' . $url, 'info');
            // 加密
            $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

            //post_url
            $postFields = array(
                'restaurant_id' => $value['restaurant_id'],
                'name' => $info['name'],
                'weight' => $info['weight']
            );

            // 获得url
            $url = $url . '&sig=' . $signUrl;
            var_dump($url);

            $resp = $this->curl_post($url, $postFields);
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            var_dump($returnArray);
            $returnData[] = $value['restaurant_id'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['code'];
            $returnData[] = $returnArray['message'];
        }
        $data = array();
        $data['cmd'] = '新增菜品分类';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }

    //更新菜品分类
    public function categorymgrUpdate($info, $shopinfo)
    {
        foreach ($shopinfo as $value) {
            // 查询新订单ID的url
            $getUrl = 'http://v2.openapi.ele.me/food_category/'.$info['categoryid'];
            // 时间戳
            $timestamp = time();
            // 取得数组
            $params = array(
                'consumer_key' => $this->consumer_key,
                'restaurant_id' => '62028381',
                'name' => $info['name'],
                'weight' => $info['weight'],
                'timestamp' => $timestamp
            );
            $param = array(
                'consumer_key' => $this->consumer_key,
                'timestamp' => $timestamp
            );
            // 加密前的url
            $url = $getUrl . '?' . $this->concatParams($param);
            // 记入日志
            Log::write('下载的url:' . $url, 'info');
            // 加密
            $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

            //post_url
            $postFields = array(
                'restaurant_id' => '62028381',
                'name' => $info['name'],
                'weight' => $info['weight']
            );

            // 获得url
            $url = $url . '&sig=' . $signUrl;
            $resp = $this->curl_post($url, $postFields);
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            $returnData[] = $value['restaurant_id'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['code'];
            $returnData[] = $returnArray['message'];
        }
        $data = array();
        $data['cmd'] = '新增菜品分类';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;

    }

    //新增食物
    public function menumgrCreate($info,$categoryinfo){
        foreach ($categoryinfo as $value) {
            // 查询新订单ID的url
            $getUrl = 'http://v2.openapi.ele.me/food/';
            // 时间戳
            $timestamp = time();
            // 取得数组
            $params = array(
                'consumer_key' => $this->consumer_key,
                'food_category_id' => $value['food_category_id'],
                'name' => $info['name'],
                'price' => 20.0, //$info['price'],
                'description' => $info['description'],
                'max_stock' => $info['max_stock'],
                'stock' => $info['stock'],
                'tp_food_id' => $info['menuid'],
              //  'image_hash' => '',
                'is_new' => 1,
                'is_featured' =>1,
                'is_gum' => 0,
                'is_spicy' => 0,
                'packing_fee' => 0.5,
                'timestamp' => $timestamp
            );
            var_dump($params);
            $param = array(
                'consumer_key' => $this->consumer_key,
                'timestamp' => $timestamp
            );
            // 加密前的url
            $url = $getUrl . '?' . $this->concatParams($param);
            // 记入日志
            Log::write('下载的url:' . $url, 'info');
            // 加密
            $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

            //post_url
            $postFields = array(
                'food_category_id' => $value['food_category_id'],
                'name' => $info['name'],
                'price' => 20.0,  //$info['price'],
                'description' =>  $info['description'],
                'max_stock' => $info['max_stock'],
                'stock' => $info['stock'],
                'tp_food_id' => $info['menuid'],
               // 'image_hash' => '436552072f6da0621642453780d891a2c97b3e2f',
                'is_new' => 1,
                'is_featured' =>1,
                'is_gum' => 0,
                'is_spicy' => 0,
                'packing_fee' => 0.5
            );
            var_dump($postFields);

            // 获得url
            $url = $url . '&sig=' . $signUrl;
            $resp = $this->curl_post($url, $postFields);
            var_dump($resp);
            //将结果转化成数组
            $returnArray = json_decode($resp, true);
            var_dump($returnArray);
            $returnData[] = $value['restaurant_id'];
            $returnData[] = $value['name'];
            $returnData[] = $returnArray['code'];
            $returnData[] = $returnArray['message'];
        }
        $data = array();
        $data['cmd'] = '新增菜品';
        $data['name'] = $info['name'];
        $data['data'] = $returnData;
        return $data;
    }

    //获取商户的新订单
    public function orderGetNew(){
        // 查询新订单ID的url
        $getUrl = 'http://v2.openapi.ele.me/order/new/';
        // 时间戳
        $timestamp = time();
        // 取得数组
        $params = array(
            'consumer_key' => $this->consumer_key,
            'restaurant_id' => '34633330',  // '85478693',
            'timestamp' => time()
        );
        // 加密前的url
        $url = $getUrl . '?' . $this->concatParams($params);
        // 记入日志
        Log::write('下载的url:' . $url, 'info');
        // 加密
        $signUrl = $this->genSig($getUrl, $params, $this->consumer_secret);

        // 获得url
        $url = $url . '&sig=' . $signUrl;
        var_dump($url);

        $resp = $this->curl_get($url);

        var_dump($resp);
    }

    //**********************************************************************
    function concatParams($params)
    {
        ksort($params);
        $pairs = array();
        foreach ($params as $key => $val) {
            array_push($pairs, $key . '=' . urlencode($val));
        }
        return join('&', $pairs);
    }

    function genSig($pathUrl, $params, $consumerSecret)
    {
        $params = $this->concatParams($params);
        $str = $pathUrl . '?' . $params . $consumerSecret;
        //$str = utf8_encode($str);

        return sha1(bin2hex($str));
    }

    /**
     * cur的处理函数
     */
    function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headers );

        $reponse = curl_exec($ch);
        curl_close($ch);
        //var_dump($reponse);
        return $reponse;
        if (curl_errno($ch))
        {
            throw new Exception(curl_error($ch),0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode)
            {
                throw new Exception($reponse,$httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    /**
     *
     */
    function curl_get($url){
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return $output;
    }

    /**
     * cur post的处理函数
     */
    function curl_post($url, $postFields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式

        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
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

    /**
     * cur put的处理函数
     */
    function curl_put($url, $putFields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       // curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->concatParams($putFields));

        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
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


    function curlrequest($url,$data,$method='post'){
        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式

        curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: $method"));//设置HTTP头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        $document = curl_exec($ch);//执行预定义的CURL
        if(!curl_errno($ch)){
            $info = curl_getinfo($ch);
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        } else {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);

        return $document;
    }
}