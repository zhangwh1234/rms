<?php

  /**
  * 递归重组节点信息为多维数组
  * @param [type] $node [要处理的节点数组]
  * @param integer $pid [父级ID]
  * @return [type]      [description]
  */
  function node_merge($node,$access = null, $pid = 0) {
      $arr = array();
      
      foreach ($node as $v){
          if(is_array($access)){
          $v['access'] = in_array($v['id'],$access) ? 1 : 0;    
          }
          
          if ($v['pid']  ==  $pid) {
              $v['child'] = node_merge($node,$access,$v['id']);
              $arr[]  = $v;
          }
      }
      
      return $arr;
  }
  
   /***
    * 返回
    */
    function getSelect($name){
        return $name.'Select';
    }
    
    /**
    * 打印数组的函数
    */
    function p($array) {
        dump($array,1,'<pre>',0);
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
