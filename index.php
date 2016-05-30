<?php
    define('APP_NAME', 'rms');
    define('APP_PATH', './rms/');
    define('THINK_PATH','./ThinkPHP/');
    define('APP_DEBUG', true);   

    // 加载框架入口文件
    //require( "./ThinkPHP/ThinkPHP.php");
    require APP_PATH.'Conf/datapath.php';
    $HTTP_POST = $_SERVER['HTTP_HOST'];
    if(!isset($rmsDataPath[$HTTP_POST])){
        print_r('route error!');
        exit;  
    } 

    require THINK_PATH.'ThinkPHP.php';
?>