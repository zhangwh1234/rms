<?php
    define('MODE_NAME', 'cli'); //采用CLI运行模式运行
    //定义项目名称
    define('APP_NAME', 'LitterProgram');
    //定义项目路径
    define('APP_PATH',dirname(__FILE__).'/LitterProgram/');
    //定义ThinkPHP核心文件路径
    define('THINK_PHP',dirname(__FILE__).'/ThinkPHP/');
    define('APP_DEBUG',true);
    //加载ThinkPHP核心文件
    require THINK_PHP.'ThinkPHP.php';
    
?>
