<?php
    /**
    * 自定义的数据库路径，算法：
    * 根据访问的url来确定数据库访问参数
    * 2014-05-08 开始编制
    */
    $rmsDataPath = array(
        //localhost的数据库路径
        'localhost' => array(
                'DB_TYPE' => 'mysql',
                'DB_HOST'=>'localhost',
                'DB_NAME'=>'rms',
                'DB_USER'=>'root',
                'DB_PWD'=>'',
                'DB_PORT'=>'3306',
                'DB_PREFIX'=>'rms_',   
                'CITY' => '常州',
        ),
        //localhost历史库的数据库路径
        'localhostHistory' => array(
                'DB_TYPE' => 'mysql',
                'DB_HOST'=>'localhost',
                'DB_NAME'=>'czrms_',
                'DB_USER'=>'root',
                'DB_PWD'=>'',
                'DB_PORT'=>'3306',
                'DB_PREFIX'=>'rms_',    
        ),
        //南京的统计程序执行命令
        'nj.lihuaerp.comTongjiCmd' => '/usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140316-AYr/workprogram/litter.php OrderTongji/index',

        //localhost的数据库路径
        '192.168.0.102' => array(
            'DB_TYPE' => 'mysql',
            'DB_HOST'=>'localhost',
            'DB_NAME'=>'rms',
            'DB_USER'=>'root',
            'DB_PWD'=>'',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>'rms_',
            'CITY' => '上海市',
        ),
        //localhost历史库的数据库路径
        '192.168.0.202History' => array(
            'DB_TYPE' => 'mysql',
            'DB_HOST'=>'localhost',
            'DB_NAME'=>'rms_2014',
            'DB_USER'=>'root',
            'DB_PWD'=>'',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>'rms_',
        ),

    )
    
?>
