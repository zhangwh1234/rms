<?php
  /**
  * sqlserver的来电库
  */
  class TelAddressModel extends Model{
       protected $trueTableName = 'tel'; 
        
        //在模型里单独设置数据库连接信息
        protected $connection = array(
        'db_type'  => 'mssql',
        'db_user'  => 'sa',
        'db_pwd'   => '',
        'db_host'  => '127.0.0.1',
        'db_port'  => '1433',
        'db_name'  => 'lihuaclient'
        );
  }
?>
