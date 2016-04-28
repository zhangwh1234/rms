<?php
/***
 * 订单状态消息服务，将订单状态写入到数据库中，然后有各种不同的系统发送状态消息
 * 2014-11-27
 */

class OrderStateServerAction extends Action
{
	public function index(){
		
	}
	
	/**
	 * 取得没有处理的订单状态记录
	 * 参数空
	 * 返回：订单状态记录
	 */
	private function getOrderState(){
		
		
		return $orderstate;
	}
	
	/**
	 * 状态工作的工厂 
	 */
	private function factoryOrderState(){
		$orderstate = $this->getOrderState();
		foreach($orderstate as $value){ 
			//启动微信写入
			$this->inputWeixin($value);
		}
	}
	
	/**
	 * 写入微信表中
	 * 参数：订单状态记录的数组
	 */
	private function inputWeixin($orderstate){
		
	}
	
	/**
	 * 写入APP的推送表中
	 */
	private function inputApp($orderstate){
		
	}
	
	/**
	 * 写入短信表中
	 */
	private function inputSms($orderstate){
		
	}
	
	/**
	 * 写入网站表中
	 */
	private function inputWeb($orderstate){
		
	}
	
	/**
	 * 写入饿了吗
	 */
	
	
	
}