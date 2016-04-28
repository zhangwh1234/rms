<?php

/**
 * Created by zhangwh
 * Date: 14/11/2
 * Time: 上午11:14
 * 功能说明：这个程序主要的工作，是将送餐等消息，压入队列中
 * 帮助说明:http://ucweb.github.io/ucmq_guide/
 * help:http://115.29.43.18:1818/?name=rmsprogram&opt=put&data=OrderTongjinj.lihuaerp.com&ver=2
 * http://115.29.43.18:1818/?name=rmsprogram&opt=get&ver=2
 * http://115.29.43.18:1818/?name=smsczQueryMessage&opt=status&ver=2
 * 测试命令：/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php MessagePushQuery/MessageJob
 */
class MessagePushQueryAction extends Action
{
    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        //Log::$format = '[y-m-d H:i:s]';
        //$this->LogFile = LOG_PATH . 'Messages_' . date('Y-m-d') . '.log';
    }

    //index 一般空
    public function index()
    {

    }

    /**
     * 从数据库获得消息数据
     * @return $array 返回一个数组
     */
    function getMessageFromDb($messageModel,$access)
    {
        $messageArray = array();
        $where = array();
        $where['issend'] = 0;
        $where['domain'] = $access;
        $result = $messageModel->where($where)->limit(1)->select();
        if ($result) {
            $messageArray = $result; //返回消息
        }
        return $messageArray;
    }
    
    /***
     *  写入数据库，表示信息压入成功
     *  @para    消息ID
     */
    function setMessageToDb($messageID,$messageModel){    	
    	//吸入数据库，设置已经写入的标志
    	$data = array();
    	$data['issend'] = 1;
    	$where = array();
    	$where['smsmgrid'] = $messageID;
    	$result = $messageModel->where($where)->save($data);
    }
    

    /**
     * 压入消息数组到消息队列中
     * @para $data :消息数组
     * @return 空
     */
    function pushMessageToQuery($messageModel,$data,$access)
    {    	
        load('@.function');
        require APP_PATH . 'Conf/messageconfig.php';
        //队列名称
        $queryName = $messageConfig[$access]['MESSAGENAME'] . 'QueryMessage';

        foreach ($data as $key => $value) {
        	if($this->is_dianxin($value['telphone'])){
        		//电信号码，发送电信队列	
        	}else{  //不是电信号码，发送到消息服务器
        		$queryName = 'sms' .$queryName;	
        	}
            //将数据变成json
            $queryMessageData = json_encode($value);
            $queryMessageData = urlencode($queryMessageData);
            //建立执行的消息命令
            $messageQueryStr = 'http://' . $messageConfig[$access]['ncmqServer'] . ':' . $messageConfig[$access]['ncmqPort'] . "/?name=$queryName&opt=put&data=$queryMessageData&ver=2";
            var_dump($messageQueryStr);
            $output = curl_post($messageQueryStr);  //写入队列
            
            //将消息状态做压入成功标志
            //$this->setMessageToDb($value['smsmgrid'],$messageModel);  			
        }
        return '';
    }

    /**
     * 将消息压入队列的工作
     */
    public function MessageJob()
    {
    	require APP_PATH.'Conf/domainconfig.php';
    	foreach ($domainArray as $key => $value){
    		//数据表MODEL
    		$messageModel = D('Smsmgr','',$value);
    		   		
    		//获得消息数据
    		$messageData = $this->getMessageFromDb($messageModel,$value);

    		//压入操作
    		$messageid = $this->pushMessageToQuery($messageModel,$messageData,$value);    	   		
 
    	}
    }
    
    /**
     * 返回是否是电信号码
     */
   function is_dianxin($phone){
   	 $dianxinArray = array('133','153','180','181','189','177');
   	 $tel = substr($phone,0,3); 
   	 if(in_array($tel, $dianxinArray)){
   	 	return true;  // 是电信号码
   	 }else{
   	 	return false;
   	 }
   } 
   

}