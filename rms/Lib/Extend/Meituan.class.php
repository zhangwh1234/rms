<?php

/**
 * Created by zhangwh1234.
 * User: lihua.com
 * Date: 15/6/28
 * Time: 上午11:18
 * 用于美团外卖的接口
 */
class Meituan
{
    public $appid = '3';
    public $app_poi_code = '';
    public $consumer_secret = 'ddb777ba29851759f1b58cd96ba5dd52';

    //初始化类
    public function  __construct($info = null)
    {
        $this->app_poi_code = $info['app_poi_code'];
    }

    //开启商户营业
    public function shopinfoOpen()
    {
        $param = array(
            //时间戳
            'timestamp' => time(),
            //程序id
            'app_id' => $this->appid,
            //商店API
            'app_poi_code' => $this->app_poi_code
        );

        //开始营业url
        $url = 'http://waimaiopen.meituan.com/api/v1/poi/open';
        //加密
        $sign = $this->genSig($url,$param,$this->consumer_secret);

        //获得url
        $url = $url . '?' . $this->concatParams($param) . '&sig=' . $sign;
        var_dump($url);
        $param = array();
        $param['app_poi_code'] = $this->app_poi_code;

        $resp = $this->curl_post($url, $param);
        var_dump($resp);
    }

    //设置商户为休息状态
    public function shopinfoClose(){
        $param = array(
            //时间戳
            'timestamp' => time(),
            //程序id
            'app_id' => $this->appid,
            //商店API
            'app_poi_code' => $this->app_poi_code
        );

        //开始营业url
        $url = 'http://waimaiopen.meituan.com/api/v1/poi/close';
        //加密
        $sign = $this->genSig($url,$param,$this->consumer_secret);

        var_dump($sign);
        //获得url
        $url =  $url . '?' . $this->concatParams($param). '&sig=' . $sign;
        var_dump($url);

        $param = array();
        $param['app_poi_code'] = $this->app_poi_code;

        $resp = $this->curl_post($url, $param);
        var_dump($resp);
    }

    //设置商户预计送达时长
    public function shopinfoSendtime(){
        $param = array(
            //时间戳
            'timestamp' => time(),
            //程序id
            'app_id' => $this->appid,
            //商店API
            'app_poi_codes' => $this->app_poi_code
        );

        //开始营业url
        $url = 'http://waimaiopen.meituan.com/api/v1/poi/sendtime/save';
        //加密
        $sign = $this->genSig($url,$param,$this->consumer_secret);

        var_dump($sign);
        //获得url
        $url =  $url . '?' . $this->concatParams($param). '&sig=' . $sign;
        var_dump($url);

        $param = array();
        $param['app_poi_codes'] = $this->app_poi_code;
        $param['send_time'] = 50;
        $resp = $this->curl_post($url, $param);
        var_dump($resp);
    }


    /**
     * 商品分类更新
     */
    public function categoryUpdate($info){
        var_dump('ee');
        $param = array(
            //时间戳
            'timestamp' => time(),
            //程序id
            'app_id' => $this->appid,
            //商店API
            'app_poi_code' => 'lihua_poi_01'
        );

        //开始营业url
        $url = 'http://waimaiopen.meituan.com/api/v1/footCat/update';
        //加密
        $sign = $this->genSig($url,$param,$this->consumer_secret);

        var_dump($sign);
        //获得url
        $url =  $url . '?' . $this->concatParams($param). '&sig=' . $sign;
        var_dump($url);

        $param = array();
        $param['app_poi_code'] = 'lihua_poi_01';
        $param['category_name'] = $info['category_name'];
        $param['sequence'] = $info['sequence'];
        $resp = $this->curl_post($url, $param);
        var_dump($resp);
    }

    //获取签名
    function genSig($pathUrl, $params, $consumerSecret)
    {
        $params = $this->concatParams($params);
        $str = $pathUrl . '?' . $params . $consumerSecret;
        var_dump($str);
        return md5($str);
    }

    //连接参数
    function concatParams($params)
    {
        ksort($params);
        $pairs = array();
        foreach ($params as $key => $val)
            array_push($pairs, $key . '=' . $val);
        return join('&', $pairs);
    }

    /**
     * cur的处理函数
     */
    function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1))//判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else//文件上传用multipart/form-data，否则用www-form-urlencoded
                {
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


}