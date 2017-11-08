<?php
/**
 * 系统数据库连接类，根据用户使用的域名，返回用户需要访问的表的连接字符串
 * 2015-01-01编写
 * 
 */
class SystemDb {
	
	/**
	 * 数据库句柄
	 */
	protected $hander  =  array();
	
	/**
	 * 根据域名，取得用户表的连接字符串
	 */
	public function getDbConnection($tableName) {
		// 用户访问域名
		$accessUrl = $_SERVER ['HTTP_HOST'];
		// 连接系统库
		// 系统库主机名称
		$host = C ( 'DB_HOST' );
		// 系统库端口
		$port = C ( 'DB_PORT' );
		// 系统库用户名
		$user = C ( 'DB_USER' );
		// 系统库的密码
		$pwd = C ( 'DB_PWD' );
		// 系统库名称
		$name = 'systemrms';
		
		$cacheConfig = F('systemDb/'.$accessUrl.$tableName);
		if(!empty($cacheConfig))  return $cacheConfig;
		
		//从数据库链接	
		$hander['systemdb'] = mysql_connect(
				$host.(isset($port)?':'.$port:':'.$port),
				isset($user)?$user:$user,
				isset($pwd)?$pwd:$pwd,new_link
		);

		$dbSel = mysql_select_db(
				isset($name)?$name:$name
				,$hander['systemdb']);		
		if(!$hander || !$dbSel)
			return false;
		$this->hander = $hander['systemdb'];
		
		$res = mysql_query("SELECT * FROM system_db where domain='".$accessUrl."' and tablename='".$tablename."'",$this->hander);
		$row = mysql_fetch_assoc($res);
		if($row) {			
			$db_type = 'mysql';
			$db_user = $row ['dbuser'];
			$db_pwd = $row ['dbpassword'];
			$db_host = $row ['dbhost'];
			$db_port = $row ['dbport'];
			$db_name = $row ['dbname'];
			$trueTableName = $row ['truetablename'];
			$config = array(
					'db_type' => $db_type,
					'db_user' => $db_user,
					'db_pwd'  => $db_pwd,
					'db_host' => $db_host,
					'db_port' => $db_port,
					'db_name' => $db_name,
					'trueTableName' => $trueTableName, 
			);			
		}else{
			$default = 'default';
			$res = mysql_query("SELECT * FROM system_db where domain='".$accessUrl."' and tablename='default' ",$this->hander);
			$row = mysql_fetch_assoc($res);
			$db_type = 'mysql';
			$db_user = $row ['dbuser'];
			$db_pwd = $row ['dbpassword'];
			$db_host = $row ['dbhost'];
			$db_port = $row ['dbport'];
			$db_name = $row ['dbname'];
			$trueTableName = $row ['truetablename'];
			$config = array(
					'db_type' => $db_type,
					'db_user' => $db_user,
					'db_pwd'  => $db_pwd,
					'db_host' => $db_host,
					'db_port' => $db_port,
					'db_name' => $db_name,
					'trueTableName' => $trueTableName,
			);
		}
		mysql_close($this->hander);
		
		//缓存
		F('systemDb/'.$accessUrl.$tableName,$config);
		return $config;
	}
	
	/**
	 * 根据域名，取得历史表的连接字符串
	 */
	public function getHistoryDbConnection($tableName) {

		// 用户访问域名
		$accessUrl = $_SERVER ['HTTP_HOST'];
		// 连接系统库
		// 系统库主机名称
		$host = C ( 'DB_HOST' );
		// 系统库端口
		$port = C ( 'DB_PORT' );
		// 系统库用户名
		$user = C ( 'DB_USER' );
		// 系统库的密码
		$password = C ( 'DB_PWD' );
		// 系统库名称
		$dbname = C ( 'DB_NAME' );
	
		$config = array(
				'db_type' => 'mysql',
				'db_user' => $user,
				'db_pwd' => $password,
				'db_host' => $host,
				'db_port' => $port,
				'db_name' => $dbname
		);
	
		$systemDbModel = new Model ( 'systemrms', '', $config );

		$where = array ();
		$where ['domain'] = $accessUrl; // 域名
		$where ['tablename'] = $tableName; // 表名
		$systemDbResult = $systemDbModel->table ( 'history_db' )->where ( $where )->find ();
	    var_dump($where);
		// 组成连接字符串
		if ($systemDbResult) {
			$db_type = 'mysql';
			$db_user = $systemDbResult ['dbuser'];
			$db_pwd = $systemDbResult ['dbpassword'];
			$db_host = $systemDbResult ['dbhost'];
			$db_port = $systemDbResult ['dbport'];
			$db_name = $systemDbResult ['dbname'];
			$trueTableName = $systemDbResult ['truetablename'];
			$config = array(
					'db_type' => $db_type,
					'db_user' => $db_user,
					'db_pwd'  => $db_pwd,
					'db_host' => $db_host,
					'db_port' => $db_port,
					'db_name' => $db_name,
					'trueTableName' => $trueTableName,
			);
		} else {
			// 提取default的连接信息
			$where = array ();
			$where ['domain'] = $accessUrl; // 域名
			$where ['tablename'] = 'default'; // 表名
			$systemDbResult = $systemDbModel->table ( 'history_db' )->where ( $where )->find ();
			$db_type = 'mysql';
			$db_user = $systemDbResult ['dbuser'];
			$db_pwd = $systemDbResult ['dbpassword'];
			$db_host = $systemDbResult ['dbhost'];
			$db_port = $systemDbResult ['dbport'];
			$db_name = $systemDbResult ['dbname'];
			$trueTableName = $systemDbResult ['truetablename'];
			$config = array(
					'db_type' => $db_type,
					'db_user' => $db_user,
					'db_pwd'  => $db_pwd,
					'db_host' => $db_host,
					'db_port' => $db_port,
					'db_name' => $db_name,
					'trueTableName' => $trueTableName,
			);
		}
	
		// 生成连接字符串
		//$connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";
		return $config;
	}

    /**
     * 自定义
     * 基础方法，返回domain,因为lihuaerp备案系统有问题，赶紧改的
     * 2018-4-6做的
     */
    private function getDomain(){
        $domain = $_SERVER['HTTP_HOST'];
        //测试域名
        if($domain == 'localhost'){
            return 'localhost';
        }
        //北京的域名
        if($domain == 'bj.lihuaerp.com'){
            return 'bj.lihuaerp.com';
        }
        //上海的域名
        if($domain == 'sh.lihuaerp.com'){
            return 'sh.lihuaerp.com';
        }
        //苏州的域名
        if($domain == 'sz.lihuaerp.com'){
            return 'sz.lihuaerp.com';
        }
        //无锡的域名
        if($domain == 'wx.lihuaerp.com'){
            return 'wx.lihuaerp.com';
        }
        //常州的域名
        if($domain == 'cz.lihuaerp.com'){
            return 'cz.lihuaerp.com';
        }
        //南京的域名
        if($domain == 'nj.lihuaerp.com'){
            return 'nj.lihuaerp.com';
        }
        //广州的域名
        if($domain == 'gz.lihuaerp.com'){
            return 'gz.lihuaerp.com';
        }

        /*******************************/
        //北京的域名
        if($domain == 'bj.lihua.com'){
            return 'bj.lihuaerp.com';
        }
        //上海的域名
        if($domain == 'sh.lihua.com'){
            return 'sh.lihuaerp.com';
        }
        //苏州的域名
        if($domain == 'sz.lihua.com'){
            return 'sz.lihuaerp.com';
        }
        //无锡的域名
        if($domain == 'wx.lihua.com'){
            return 'wx.lihuaerp.com';
        }
        //常州的域名
        if($domain == 'cz.lihua.com'){
            return 'cz.lihuaerp.com';
        }
        //南京的域名
        if($domain == 'nj.lihua.com'){
            return 'nj.lihuaerp.com';
        }
        //广州的域名
        if($domain == 'gz.lihua.com'){
            return 'gz.lihuaerp.com';
        }
    }
}