<?php
/* *
 * 类名：ChuanglanSmsApi
 * 功能：创蓝接口请求类
 * 详细：构造创蓝短信接口请求，获取远程HTTP数据
 * 版本：1.3
 * 日期：2014-07-16
 * 说明：
 * 以下代码只是为了方便客户测试而提供的样例代码，客户可以根据自己网站的需要，按照技术文档自行编写,并非一定要使用该代码。
 * 该代码仅供学习和研究创蓝接口使用，只是提供一个参考。
 * 直接引用这个代码，做了微小修改。
 * 账号：vip-lhct
 * 密码：Tch123456
 */
class ChuanglanSmsApi {
	//创蓝发送短信接口URL, 如无必要，该参数可不用修改
	private $api_send_url = 'http://222.73.117.158/msg/HttpBatchSendSM';

	//创蓝短信余额查询接口URL, 如无必要，该参数可不用修改
	private $api_balance_query_url = 'http://222.73.117.158/msg/QueryBalance';

	//创蓝账号 替换成你自己的账号
	//private $api_account	= 'shangwuE_ymw';
	private $api_account	= 'vip-lhct';

	//创蓝密码 替换成你自己的密码
	//private $api_password	= 'Clwh123456';
	private $api_password	= 'Tch123456';

	/**
	 * 发送短信
	 *
	 * @param string $mobile 手机号码
	 * @param string $msg 短信内容
	 * @param string $needstatus 是否需要状态报告
	 * @param string $product 产品id，可选
	 * @param string $extno   扩展码，可选
	 */
	public function sendSMS( $mobile, $msg, $needstatus = 'false', $product = '', $extno = '') {
		//创蓝接口参数
		$postArr = array (
				          'account' => $this->api_account,
				          'pswd' => $this->api_password,
				          'msg' => $msg,
				          'mobile' => $mobile,
				          'needstatus' => $needstatus,
				          'product' => $product,
				          'extno' => $extno
                     );
		
		$result = $this->curlPost( $this->api_send_url , $postArr);
		return $this->sendResponse($result[1]);
	}
	
	/**
	 * 查询额度
	 *
	 *  查询地址
	 */
	public function queryBalance() {
		//查询参数
		$postArr = array ( 
		          'account' => $this->api_account,
		          'pswd' => $this->api_password,
		);
		$result = $this->curlPost($this->api_balance_query_url, $postArr);
		return $result;
	}

	/**
	 * 处理返回值
	 * 
	 */
	public function execResult($result){
		$result=preg_split("/[,\r\n]/",$result);
		return $result;
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数 
	 * @return mixed
	 */
	private function curlPost($url,$postFields){
		echo $url ;

		$postFields = http_build_query($postFields);
		echo $postFields;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	//魔术获取
	public function __get($name){
		return $this->$name;
	}
	
	//魔术设置
	public function __set($name,$value){
		$this->$name=$value;
	}

	//发送的返回信息
	private function sendResponse($respCode){
		switch($respCode){
			case '0':
				return '发送成功';
				break;
			case '101':
				return '无此用户';
				break;
			case '102':
				return '密码错误';
				break;
			case '103':
				return '提交过快（提交速度超过流速限制）';
				break;
			case '104':
				return '系统忙（因平台侧原因，暂时无法处理提交的短信）';
				break;
			case '105':
				return '敏感短信（短信内容包含敏感词）';
				break;
			case '106':
				return '消息长度错（>536或<=0）';
				break;
			case '107':
				return '包含错误的手机号码';
				break;
			case '108':
				return '手机号码个数错（群发>50000或<=0;单发>200或<=0）';
				break;
			case '109':
				return '无发送额度（该用户可用短信数已使用完）';
				break;
			case '110':
				return '不在发送时间内';
				break;
			case '111':
				return '超出该账户当月发送额度限制';
				break;
			case '112':
				return '无此产品，用户没有订购该产品';
				break;
			case '113':
				return 'extno格式错（非数字或者长度不对）';
				break;
			case '114':
				return '自动审核驳回';
				break;
			case '115':
				return '签名不合法，未带签名（用户必须带签名的前提下）';
				break;
			case '116':
				return 'P地址认证错,请求调用的IP地址不是系统登记的IP地址';
				break;
			case '117':
				return '用户没有相应的发送权限';
				break;
			case '118':
				return '用户已过期';
				break;

		}

	}
}
?>