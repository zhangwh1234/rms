<?php
/**
 * Created by zhangwh.
 * Date: 14/11/2
 * Time: 下午3:08
 * 功能说明：
 * /Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php MessageGetQueryToDb/MessageJob
 */
class MessageGetQueryToDbAction extends Action {
	
	/**
	 * *
	 * 从队列中获取消息
	 */
	public function getMessageFromQuery($access) {
		load ( '@.function' );
		require APP_PATH . 'Conf/messageconfig.php';
		// 队列名称
		$queryName = $messageConfig [$access] ['MESSAGENAME'] . 'QueryMessage';		
		// 建立执行的消息命令
		$messageQueryStr = 'http://' . $messageConfig [$access] ['ncmqServer'] . ':' . $messageConfig [$access] ['ncmqPort'] . "/?name=$queryName&opt=get&ver=2";
		$getput = curl_post ( $messageQueryStr ); // 读取队列
		if(substr($getput,0,25) == 'UCMQ_HTTP_ERR_QUE_NO_EXIST'){  //消息队列不存在，返回空
			//var_dump(array());
			return array();
		}
		
		if(substr($getput,0,24) == 'UCMQ_HTTP_ERR_QUE_EMPTY'){  //消息队列为空，然后空
			return array();
		}
		
		//分析队列
		/*$getput = 'UCMQ_HTTP_OK
{"smsmgrid":"203","telphone":"18912306390","content":"\u6d4b\u8bd5\u8ba2\u5355","firstdate":"2015-01-03 16:01:33","sendname":"","issend":"0","smstype":"","company":"\u65b0\u8857\u53e3","orderformid":"0","senddate":"","domain":"localhost"}';
		*/
		if(substr($getput,0,12) == 'UCMQ_HTTP_OK'){
			$queryStr = substr($getput,13);
			$queryStr = json_decode($queryStr,true);	
			return $queryStr;		
		}
		
	}
	
	/**
	 * 将消息队列的工作
	 */
	public function MessageJob()
	{
		require APP_PATH.'Conf/domainconfig.php';
		foreach ($domainArray as $key => $value){
			//数据表MODEL
			$returnMessage = $this->getMessageFromQuery($value);
			if(count($returnMessage) != 0){
				$data = array();
				$data['MSGID'] = $returnMessage['smsmgrid'];
				$data['DESTnumber'] = $returnMessage['telphone'];
				$data['MSGCONTENT'] = $returnMessage['content'];
				$data['COMMITTIME'] = date('Y-m-d H:i:s');
				$data['sendname'] = $returnMessage['sendname'];
				$data['company'] = $returnMessage['company'];
				$data['area'] = $key;
				$data['dispID'] = $returnMessage['orderformid'];
				$interface_mt_msgModel = D('interfacemtmsg','',$value);
				$interface_mt_msgModel->create();
				$interface_mt_msgModel->add($data);
 			}
		}
	}
}