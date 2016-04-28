<?php
/**
    * 订餐管理系统rms和营收结账系统yingshou的数据交换系统
    * YingshouServer是运行在订餐管理系统的守候服务程序
    * 2014-07-08
    */
class YingshouServerAction extends Action {
	
	// 首程序
	public function index() {
		$this->putOrderForm ();
	}
	
	// 发送订单数据
	public function putOrderForm() {
		Log::write ( '结账数据传输开始', LOG::INFO, LOG::FILE );
		// 实例化订单表
		$orderformModel = D ( 'Orderform' );
		// 实例化订货表
		$orderproductsModel = D ( 'Orderproducts' );
		// 实例化订单发送表
		$orderyingshouexchangeModel = D ( 'Orderyingshouexchange' );
		// 定义返回数据
		$orderformArray = array ();
		$orderformArray ['error'] = 0;
		$orderform = array ();
		// 开始查询
		$where = array ();
		$where ['status'] = 0;
		$orderyingshouexchangeResult = $orderyingshouexchangeModel->where ( $where )->limit ( 0, 100 )->select();
		foreach ( $orderyingshouexchangeResult as  $value ) {
			$orderformid = $value ['orderformid'];
			Log::write ( '获得一条需要传输的数据' . $orderformid, LOG::INFO, LOG::FILE, $this->LogFile );
			$where = array ();
			$where ['orderformid'] = $orderformid;
			$orderformResult = $orderformModel->where ( $where )->find ();
			$orderproductsResult = $orderproductsModel->where ( $where )->select ();
			$orderformResult ['orderproducts'] = $orderproductsResult;
			$orderform [$value['id']] = $orderformResult;
		}
		
		if (! empty ( $orderyingshouexchangeResult )) {
			$orderformArray ['success'] = 'success';
			$orderformArray ['result'] = $orderform;
			echo json_encode ( $orderformArray );
		} else {
			$orderformArray ['error'] = 1;
			$orderformArray ['msg'] = 'no date';
			echo json_encode ( $orderformArray );
		}
	}
	
	// 订单数据收到验证
	public function confirmOrderForm() {
		// 定义返回数据
		$orderformArray = array ();
		$orderformArray ['error'] = 0;
		// 订单号
		$orderformid = $_REQUEST ['orderformid'];
		// 实例化订单发送表
		$orderyingshouexchangeModel = D ( 'Orderyingshouexchange' );
		$where = array ();
		$where ['id'] =  $orderformid;
		$data = array ();
		$data ['status'] = 1;
		$result = $orderyingshouexchangeModel->where ( $where )->save ( $data );
		//Log::write ( '传输验证:' . $orderformid, LOG::INFO, LOG::FILE, $this->LogFile );
		if ($result) {
		}
		echo json_encode ( $orderformArray );
	}
}
?>
