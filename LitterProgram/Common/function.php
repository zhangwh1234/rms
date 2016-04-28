<?php

    function concatParams($params) {
        ksort($params);
        $pairs = array();
        foreach($params as $key=>$val)
            array_push($pairs, $key.'='.$val);
        return join('&', $pairs);
    }

    function genSig($pathUrl, $params, $consumerSecret) {
        $params = concatParams($params);
        $str = $pathUrl.'?'.$params.$consumerSecret;
        return md5($str);
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
        //https ÇëÇó
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if (is_array($postFields) && 0 < count($postFields))
        {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v)
            {
                if("@" != substr($v, 0, 1))//ÅÐ¶ÏÊÇ²»ÊÇÎÄ¼þÉÏ´«
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&"; 
                }
                else//ÎÄ¼þÉÏ´«ÓÃmultipart/form-data£¬·ñÔòÓÃwww-form-urlencoded
                {
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart)
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
            else
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
            }
        }
        $reponse = curl_exec($ch);

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


    //¶ÔÏó×ªÊý×é,Ê¹ÓÃget_object_vars·µ»Ø¶ÔÏóÊôÐÔ×é³ÉµÄÊý×é
    function objectToArray($obj){
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($arr)){
            return array_map(__FUNCTION__, $arr);
        }else{
            return $arr;
        }
    }

    /**
    * 打印的数组
    */
    function p($array) {
        dump($array,1,'<pre>',0);
    }

    /**
    * 拼接字段函数
    */
    function get_str($arr){
         $fields_string = '';
        foreach($arr as $key => $value)
        {
            $fields_string .= $key . '=' . $value . '&';
        }
        return rtrim($fields_string , '&');
    }

    //curl提交数据
    function curl_post($url,$post_data = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output =  curl_exec($ch);
        curl_close($ch);
        return $output;
    }

?>
