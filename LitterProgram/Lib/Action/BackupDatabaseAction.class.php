<?php
    /**
    * 备份数据到备份的目录中
    * 
    * $bakdns = 'mysql://root:zhangwh0731@localhost:3306/rms'; 
    *        //连接备份数据库
    *        $Model = M(null,null,$bakdns);
    *        $bakdns = 'mysql://db625r43111537tc:zhangwh0731@rdsjj7vrrnaby2m.mysql.rds.aliyuncs.com:3306/rms';
    * 
    * /Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php BackupDatabase/index
    * /usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140316-AYr/www.lihuaerp.com/litter.php BackupDatabase/index
    */




    class BackupDatabaseAction extends Action{
    	/**
    	 * 架构函数
    	 *
    	 * @access 定义日志文件
    	 */
    	public function __construct() {
    		Log::$format = '[y-m-d H:i:s]';
    		$this->LogFile = LOG_PATH . 'BackupData_' . date ( 'Y-m-d' ) . '.log';
    	}
    	
        public function index(){
            //备份的数据
            $bakdns = 'mysql://rdsvezmirevayff:zhangwh0731@rdsvezmirevayff.mysql.rds.aliyuncs.com:3306/czrms_';
            $rmsdns = 'mysql://rdsvezmirevayff:zhangwh0731@rdsvezmirevayff.mysql.rds.aliyuncs.com:3306/czrms';
        	//$bakdns = 'mysql://root:@localhost:3306/czrms_';
        	//$rmsdns = 'mysql://root:@localhost:3306/rms';
        	$this->bak_rms($bakdns,$rmsdns);                   
            
        }

        public function bak_rms($bakdns,$rmsdns){
        	Log::write ( '开始备份数据', INFO, LOG::FILE, $this->LogFile );
            //备份的日期
            $backDate = date('Y-m-d'); 
            $backMonth = substr($backDate,5,2);
            $backYear = substr($backDate,0,4);

            $bakdns = $bakdns.$backYear;   
            //连接备份数据库
            $Model = M(null,null,$bakdns);
            
            //建立orderform的备份表
            $orderformModel = M('orderform','rms_',$rmsdns);
         
            //备份订单表orderform的内容  
            $where = array();  
            //$where['custdate'] = $backDate;
            //$where['ap'] = $this->getAp();
            $orderformResult = $orderformModel->where($where)->select();
            foreach($orderformResult as $orderformValue){
            	//备份orderform的脚本
                $sql = $this->insertsql('rms_orderform',$orderformValue);
                $Model->query($sql);  
                //备份orderproducts
                $orderproductsModel = M('orderproducts','rms_',$rmsdns);
                $where = array();
                $where['orderformid'] = $orderformValue['orderformid'];
                $orderproductsResult = $orderproductsModel->where($where)->select();
                foreach($orderproductsResult as $orderproductRow){
                    $sql = $this->insertsql('rms_orderproducts',$orderproductRow);
                    $Model->query($sql);  //备份orderproducts的脚本
                    $orderproductsModel->where($where)->delete(); //删除原有的记录
                }
				
                //备份orderaction
                $orderactionModel = M('orderaction','rms_',$rmsdns);               
                $where = array();
                $where['orderformid'] = $orderformValue['orderformid'];
                $orderactionResult = $orderactionModel->where($where)->select();
                foreach($orderactionResult as $orderactionRow){
                    $sql = $this->insertsql('rms_orderaction',$orderactionRow);
                    $Model->query($sql);  //备份orderaction的脚本
                    $orderactionModel->where($where)->delete();  //删除原有的记录
                }
                
                //备份orderastate
                $orderstateModel = M('orderstate','rms_',$rmsdns);
                $where = array();
                $where['orderformid'] = $orderformValue['orderformid'];
                $orderstateResult = $orderstateModel->where($where)->select();
                foreach($orderstateResult as $orderastateValue){
                	$sql = $this->insertsql('rms_orderaction',$orderstateValue);
                	$Model->query($sql);  //备份orderaction的脚本
                	$orderstateModel->where($where)->delete();  //删除原有的记录
                }
                
                //备份催送表
                $orderhurryModel = M('orderhurry','rms_',$rmsdns);
                $where = array();
                $where['orderformid'] = $orderformValue['orderformid'];
                $orderhurryResult = $orderhurryModel->where($where)->select();
                foreach($orderhurryResult as $orderhurryValue){
                	$sql = $this->insertsql('rms_orderhurry',$orderstateValue);
                	$Model->query($sql);  //备份orderaction的脚本
                	$orderhurryModel->where($where)->delete();  //删除原有的记录
                }
                
                //删除orderform原有的记录
                $orderformModel->where($where)->delete();				
            }
            
     
            //建立smsmgr的备份表
            $smsmgrModel = M('Smsmgr','rms_',$rmsdns);
            //备份ordermonit的表
            $smsmgrResult = $smsmgrModel->select();
            foreach($smsmgrResult as $smsmgrValue){
                $sql = $this->insertsql('rms_smsmgr',$smsmgrValue);
                $Model->query($sql);  //备份smsmgr的脚本
            }
            $smsmgrModel->query('delete from rms_smsmgr');

            //建立备份messages
            $messagesModel = M('messages','rms_',$rmsdns);        
            //备份ordermonit的表
            $messagesResult = $messagesModel->select();
            foreach($messagesResult as $messagesValue){
                $sql = $this->insertsql('rms_messages',$messagesValue);
                $Model->query($sql);  //备份messages的脚本
            }
            $messagesModel->query('delete from rms_messages');
            

            //备份装箱表
            $zhuangxiangModel = M('zhuangxiangform','rms_',$rmsdns);
            $zhuangxiangResult = $zhuangxiangModel->select();
            foreach($zhuangxiangResult as $value){
                $sql = $this->insertsql('rms_zhuangxiangform',$value);
                $Model->query($sql);                
            }
            $zhuangxiangModel->query('delete from rms_zhuangxiangform');  //删除装箱表
            
            //备份装货表
            $zhuangxiangproductsModel = M('zhuangxiangproducts','rms_',$rmsdns);
            $zhuangxiangproductsResult = $zhuangxiangproductsModel->select();
            foreach($zhuangxiangproductsResult as $value){
                $sql = $this->insertsql('rms_zhuangxiangproducts',$value);
                $Model->query($sql);                
            }
            $zhuangxiangproductsModel->query('delete from rms_zhuangxiangproducts'); //删除装货表
            
            //备份装箱日志表
            $zhuangxiangactionModel = M('zhuangxiangaction','rms_',$rmsdns);
            $zhuangxiangactionResult = $zhuangxiangactionModel->select();
            foreach($zhuangxiangactionResult as $value){
                $sql = $this->insertsql('rms_zhuangxiangaction',$value);
                $Model->query($sql);                
            }
            $zhuangxiangactionModel->query('delete from rms_zhuangxiangaction');  //删除装箱日志表
           
            if($this->getAp() == '下午'){
            	
                $orderformModel->query('delete from rms_session');
                $orderformModel->query('delete from rms_productsmonit');
                $orderformModel->query('delete from rms_ordermonit');
                $orderformModel->query('delete from rms_sendnameproducts');
				//$orderformModel->query('truncate table rms_orderform');
            }
            //删除营收发送表
            $orderformModel->query('delete from rms_orderyingshouexchange');

            Log::write ( '备份结束', INFO, LOG::FILE, $this->LogFile );

        }

        //生成SQL备份语句
        function insertsql($table, $row)
        {
            $sql = "INSERT INTO `{$table}` VALUES ("; 
            $values = array(); 
            foreach ($row as $value) 
            {
                $values[] = "'" . mysql_real_escape_string($value) . "'"; 
            }
            $sql .= implode(', ', $values) . ");\n"; 
            return $sql;
        }

        //获取当前时间的午别
        function getAp(){
        	date_default_timezone_set('Asia/Shanghai');
            $nowTime = time();
            $splitTime = strtotime('15:30:00');  //分割的时间
            if(($nowTime - $splitTime) >= 0){
                $ap = '下午'; 
            }else{
                $ap = '上午';
            }
            return $ap;
        }
    }

?>
