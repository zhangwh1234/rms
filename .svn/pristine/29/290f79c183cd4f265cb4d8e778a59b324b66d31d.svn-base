<?php
/**
 * 系统数据库连接类，根据用户使用的域名，返回用户需要访问的表的连接字符串
 * 2015-01-01编写
 * 
 */
class SystemDb {
	
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
		$password = C ( 'DB_PWD' );
		// 系统库名称
		$dbname = C ( 'DB_NAME' );
		
		$config = [ 
				'db_type' => 'mysql',
				'db_user' => $user,
				'db_pwd' => $password,
				'db_host' => $host,
				'db_port' => $port,
				'db_name' => $dbname 
		];
		
		$systemDbModel = new Model ( 'systemrms', '', $config );
		
		$where = array ();
		$where ['domain'] = $accessUrl; // 域名
		$where ['tablename'] = $tableName; // 表名
		$systemDbResult = $systemDbModel->table ( 'system_db' )->where ( $where )->find ();
		
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
			$systemDbResult = $systemDbModel->table ( 'system_db' )->where ( $where )->find ();
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
	
		$config = [
				'db_type' => 'mysql',
				'db_user' => $user,
				'db_pwd' => $password,
				'db_host' => $host,
				'db_port' => $port,
				'db_name' => $dbname
		];
	
		$systemDbModel = new Model ( 'systemrms', '', $config );
	
		$where = array ();
		$where ['domain'] = $accessUrl; // 域名
		$where ['tablename'] = $tableName; // 表名
		$systemDbResult = $systemDbModel->table ( 'history_db' )->where ( $where )->find ();
	    
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
}