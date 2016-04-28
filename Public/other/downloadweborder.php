<?php

    //当前版本:Taoapi TOP PHP SDK 2.2
    header("Content-type:text/html; charset=utf-8");
    //echo '<meta http-equiv="Refresh" content="40" >';  //定期执行

    //设置时区
    date_default_timezone_set('PRC');


   

    $ch = curl_init();
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/site1/dcxt/1/getorder.asp?currentDate=2013/7/20");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);
    //var_dump($output);
    $resp = json_decode($output,true);
    var_dump($resp);
    exit;


    set_time_limit(0);

    print_r('*********************************************************************************************</br>');
    print_r('***                                                                                            </br>');
    print_r('***                              cz网订单下载程序                              </br>');
    print_r('***                                制作：丽华快餐                                </br>');
    print_r('***                               时间：2011-09-15                               </br>');
    print_r('***                                                                              </br>');
    print_r('*********************************************************************************************</br>');


    echo '<pre>';

    $logger->debug(date('Y-m-d H:i:s').' 开始网上下载淘宝网的数据');

    $OrderNumber = $resp['OrderCount'];
    //取得下载订单的数量
    if($OrderNumber == 0){
        echo date('Y-m-d H:i:s').' 没有订单....................</br>';
        print_r(date('Y-m-d H:i:s').' 没有订单....................</br>');
        return;
    }


    //var_dump($TaobaokeData);
    var_dump($OrderNumber);

    print_r(date('Y-m-d H:i:s').' 下载一条订单....................</br>');

    for($i=0;$i<(int)$OrderNumber;$i++){
    $orderid = $resp['OrderData'][$i]['OrderID'];
    //先清除，防止重复
    $sql = "delete from ordergoods where corderid='".$orderid."'";
    var_dump($sql);
    //$db->Execute($sql);
    $sql = "delete from orderform where corderid='".$orderid."'";
    $db->Execute($sql);
    }

    $OrderGoods = $resp['OrderList'];
    foreach($OrderGoods as $goods){
        $orderid =$goods['OrderID'] ;

        $money = $ordergoods['count'] * $ordergoods['real_price'];
        $sql = "insert into ordergoods (corderlistid,corderid,cname,mprice,mnumber,mmoney) values ('".uniqid('tb_',true)."','".$orderid."','".$goods['OrderID']."',".$goods['ProdName'].",".$goods['ProdPrice'].",".$goods['ProdCount'].",".$goods['Total'].")";  //iconv("UTF-8", "GBK", $result);
        $goods_ordertxt[$orderid] .= $goods['ProdCount'] . '×' .$goods['ProdName']. " ";

        print_r('插入数据');
        var_dump($sql);
        //$db->Execute($sql);
    }

    $OrderForm = $resp['OrderData'];
    foreach($OrderForm as $order){
        $orderid = $order['OrderID'];
        $logger->debug(date('Y-m-d H:i:s').' 网上下载一条订单'.$orderid);
        $phone = iconv("UTF-8", "GBK",$order['Tel']);
        $address = $order['Address'];

        $note = $order['Memo'];
        $custdate = substr($order['SendTime'],0,10); 
        $custtime = substr($order['SendTime'],11,8); 
        $aptime = substr($custtime,0,2);
        $name = iconv("UTF-8", "GBK",$order['ConnUser']);
        $name = substr($name,0,6);
        if ($aptime > 16){
            $ap = '下午';
        }else{
            $ap = '上午';
        }
        $totalmoney = $order['Total'];
        $ordertxt = $goods_ordertxt[$orderid];
        $rectime =  date('H:i:s');
        $memo = iconv("UTF-8", "GBK",  $note);

        //插入订单
        //var_dump($ordertxt);
        $address = iconv("UTF-8", "GBK", '(淘宝)'.$address);
        $sql = "insert into orderform (corderid,cclientname,clinkman,caddress,cphone,crectime,cmemo,ctelname,ccustdate,ccusttime,mmoney,cordertxt,cap) values ('".$orderid."','".
        $name."','".iconv("UTF-8", "GBK",'淘宝')."','".$address."','".$phone."','".$rectime."','".$memo."','".iconv("UTF-8", "GBK",'淘宝')."','".$custdate."','".$custtime."',".$totalmoney.",'".$ordertxt."','".iconv("UTF-8", "GBK",$ap)."')";
        var_dump($sql);
        $db->Execute($sql);
        print_r('<font color=green>'.date('Y-m-d H:i:s').'  订单地址:'.$address.'  '.'客户姓名:'.$name.'客户电话:'.$phone.'订购:'.$ordertxt.'</font></br>'); 

        //确认订单
        //获取订单

    }






?>