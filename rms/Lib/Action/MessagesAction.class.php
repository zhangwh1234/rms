<?php
/**
    * 群发消息系统
    */
class MessagesAction extends ModuleAction {

	// 返回消息
	public function getMessages() {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称
		                                             
		// 启动当前模块的模型
		$focus = D ( $moduleName );
		
		// 查询当前是否有最新的消息
		$filed = array (
				'messagesid',
				'content',
				'sender',
				'time' 
		);
		$where = array ();
		$where ['status'] = 0;
		$where ['sender'] = $this->userInfo ['name'];
		$where ['domain'] = $this->getDomain();
		$newMsg = $focus->field ( $filed )->where ( $where )->limit ( 0, 1 )->select ();
		if (count ( $newMsg )) {
			$returnMsg = array (
					$newMsg [0] ['content'] 
			);
			$focus->create ();
			$data ['status'] = 1;
			$where ['messagesid'] = $newMsg [0] ['messagesid'];
			$focus->where ( $where )->save ( $data );
			$this->ajaxReturn ( $returnMsg );
		} else {
			$returnMsg = array ();
		}
	}
	
	/* 一般顺序表记录的保存 */
	public function insert() {
		// 返回当前的模块名
		$moduleName = $this->getActionName ();
		
		$focus = D ( $moduleName );
		$this->assign ( 'moduleName', $moduleName );
		
		// 启动用户表
		$userModel = D ( 'User' );
		$userResult = $userModel->select ();
		
		foreach ( $userResult as $value ) {
			$data = array ();
			$data ['sender'] = $value ['name'];
			$data ['status'] = 0;
			$data ['content'] = $_REQUEST ['content'];
			$data ['time'] = date('H:i:s');
			$data ['domain'] = $this->getDomain();
			// 保存主表
			$result = $focus->create ();
			if (! $result) {
				exit ( $focus->getError () );
			}
			$result = $focus->add ( $data );
		}
		if (! $result) {
			$this->error ( '保存数据不成功！' );
		}
		
		// 取得保存的主键
		$record = $result;
		// 如果保存订单都成功，就跳转到查看页面
		$return ['record'] = $record;

		// 生成查看的url
		$detailviewUrl = U ( "$moduleName/detailview", array (
			'record' => $record
		) );
		$return = $detailviewUrl;
		$info['status'] = 1;
		$info['info'] ='保存成功' ;
		$info['url'] = $return;
		$this->ajaxReturn(json_encode($info),'EVAL');
	}
	
	/**
	 * 返回listview的where
	 */
	public function returnWhere(&$where) {

		$userInfo = $_SESSION['userInfo'];
		$username = $userInfo ['name'];
		$where ['sender '] = $username;
		$where ['domain'] = $this->getDomain();
		return $where;
	}



	

}
?>
