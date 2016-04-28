<?php
/**
 * Created by zhangwh1234.
 * User: lihua.com
 * Date: 15/7/13
 * Time: 上午11:28
 * 百度外卖接口，完成在cmd模式下的执行任务
 */

class Baiduwaimai {
    private $source = ""; //"65524";
    private $secret = "";  //'983269a9ba70f146';
    private $url = "http://api.waimai.baidu.com";

    //完成订单的接口
    public function OrderComplete($info){
        $body = array();
        $body['order_id'] =   $info['cOrderID'];  //$info['order_sn'];

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
        $resp = curl($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);
        var_dump($returnArray);
        $data = array();
        $data['cmd'] = '完成订单';
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;
    }

    //用户取消订单
    public function OrderCancel($info){
        $body = array();
        $body['order_id'] =   $info['cOrderID'];  //$info['order_sn'];
        $body['type'] = 5;
        $body['reason'] = '退单';

        $signArray = array();
        //数据体
        $signArray['body'] = $body;
        //创建商户命令
        $signArray['cmd'] = "order.cancel";
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
        $cmdArray['cmd'] = "order.cancel";
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
        $resp = curl($this->url, json_encode($cmdArray));

        //将结果转化成数组
        $returnArray = json_decode($resp, true);
        var_dump($returnArray);
        $data = array();
        $data['cmd'] = '订单取消';
        $data['errno'] = $returnArray['body']['errno'];
        $data['error'] = $returnArray['body']['error'];
        return $data;
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


/**
 * cur post的处理函数
 */
function curl($url, $postFields = null,$headers = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_HTTPHEADER , $headers );
    curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $reponse = curl_exec($ch);
    curl_close($ch);
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