<?php
// 定义订单分配模块
class OrderDistributionAction extends ModuleAction {

	// listview
	public function listview() {

		if(IS_POST){

			//获取上传的distributon数据，并分配订单数据
			$companyCode = $_REQUEST['companyCode'];
			$companyCode = str_replace('\\','',$companyCode);
			$companyCode = json_decode($companyCode,true);
			foreach ($companyCode as $value) {
				$this->listviewOrderDistributionByCode($value['orderformid'],$value['code']);
			}

			// 取得模块的名称
			$moduleName = $this->getActionName ();
			$this->assign ( 'moduleName', $moduleName ); // 模块名称

			// 启动当前模块
			$focus = D ( $moduleName );

			// 生成list字段列表
			$listFields = $focus->listFields;

			// 模块的ID
			$moduleId = $focus->getPk ();

			// 加入模块id到listHeader中
			$listHeader = $listFields;
			$this->assign ( "listHeader", $listHeader ); // 列表头

			$where = array ();
			$where [] = ' length(trim(company)) = 0 ';
			$where [] = " not (state like '已%')";
			$where ['ap'] = $this->getAp ();
			$where ['domain'] = $this->getDomain();
			$total = $focus->where ( $where )->count (); // 查询满足要求的总记录数

			//使用cookie读取rows
			$listMaxRows = $_COOKIE['listMaxRows'];
			if(!empty($listMaxRows)){

			}else{
				$listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
			}

			//应该订单分配还要显示两个统计数据，所以listrow还要减
			$listMaxRows = $listMaxRows - 2;

			// 导入分页类
			import ( 'ORG.Util.Page' ); // 导入分页类
			$pageNumber = $_REQUEST ['page'];
			// 取得页数
			$_GET ['p'] = $pageNumber;
			$Page = new Page ( $total, $listMaxRows );

			//保存页数
			$_SESSION [$moduleName . 'listview' . 'page'] = $pageNumber;

			// 分公司的名称
			$userInfo = $_SESSION ['userInfo'];
			$company = $this->userInfo ['department'];

			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			// 查询模块的数据
			$select_fields =$listFields;
			array_unshift ( $select_fields, 'orderformid' );
			array_unshift ( $select_fields, 'telname','rectime','hurrynumber','hurrytime' );
			$where = array ();
			$where [] = ' length(trim(company)) = 0 ';
			$where [] = " not (state like '已%')";
			$where ['ap'] = $this->getAp ();
			$where ['domain'] = $this->getDomain();
			$listResult = $focus->where ( $where )->field ( $select_fields )->limit ( $Page->firstRow . ',' . $Page->listRows )->order ( "$moduleId asc" )->select ();

			if (empty ( $listResult )) {
				$listResult = array ();
			}
			$orderDistributionArray ['total'] = $total;
			$orderDistributionArray ['rows'] = $listResult;
			$this->ajaxReturn ( $orderDistributionArray, 'JSON' );

		}else{
			// 取得模块的名称
			$moduleName = $this->getActionName ();
			$this->assign ( 'moduleName', $moduleName ); // 模块名称

			// 启动当前模块
			$focus = D ( $moduleName );

			// 取得对应的导航名称
			$navName = $focus->getNavName ( $moduleName );
			$this->assign ( 'navName', $navName ); // 导航民

			// 生成list字段列表
			$listFields = $focus->listFields;

			// 模块的ID
			$moduleId = $focus->getPk ();

			// 加入模块id到listHeader中
			$listHeader = $listFields;
			$this->assign ( "listHeader", $listHeader ); // 列表头
			$this->assign ( 'returnAction', 'listview' ); // 定义返回的方法

			//如果存在页数,获取
			if(isset($_REQUEST['pagetype'])){
				$pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
			}else{
				$pageNumber = 1;
			}
			$this->assign( 'pagenumber',$pageNumber);
			//是否存在选中的行号
			if(isset($_REQUEST['rowIndex'])){
				$this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
			}else{
				$this->assign ( 'rowIndex',0);
			}

			$this->display ( 'OrderDistribution/listview' );
		}

	}


	/**
	 * listview输入的编码处理订单
	 * code 输入的编码
	 * 第一次的订单分配
	 */
	public function listviewOrderDistributionByCode($orderformid,$code) {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称

		// 启动当前模块的模型
		$focus = D ( $moduleName );

		// 分公司的名称
		$userInfo = $this->userInfo;
		$company = $userInfo ['department'];

		// 获得处理过了的编码
		$code = $code;

		// 定义返回的数组
		$returnInfo = array ();

		/**
		 * 先编辑配送店的编码 **
		 */
		// 根据编码取得配送点名字
		$companyMgrModel = D ( 'CompanyMgr' );
		$where ['distributionCode'] = $code; // 配送点的编号
		$where ['domain'] = $this->getDomain();
		$companymgrResult = $companyMgrModel->field ( 'name,distributioncode' )->where ( $where )->find ();
		if ($companymgrResult) {
			$companyName = $companymgrResult ['name'];
		} else {
			return;
		}

		// 根据配送信息，处理订单
		$orderformid = $orderformid;
		//获得订单号
		$where = array();
		$where['orderformid'] = $orderformid;
		$orderformResult = $focus->field('ordersn,origin')->where($where)->find();
		$ordersn = $orderformResult['ordersn'];
		$origin  = $orderformResult['origin'];

		$data = array();
		$data ['company'] = $companyName;
		$where = array();
		$where ['orderformid'] = $orderformid;
		$focus->where ( $where )->save ( $data );

		// 同时写入日志中
		// 记入操作到action中
		$orderactionModel = D ( 'Orderaction' );
		$orderactionData ['orderformid'] = $orderformid;
		$orderactionData ['ordersn'] = $ordersn; // 订单号
		$orderactionData ['action'] = "订单分配给" . $companyName . "配送点";
		$orderactionData ['logtime'] = date ( 'H:i:s' );
		$orderactionData ['domain'] = $this->getDomain();
		$orderactionModel->create ();
		$result = $orderactionModel->add ( $orderactionData );

		// 写入到状态表中
		$orderstateModel = D ( 'Orderstate' );
		$data = array ();
		$data ['distribution'] = 1;
		$data ['distributiontime'] = date ( 'Y-m-d H:i:s' );
		$data ['distributioncontent'] = $companyName;
		$where = array ();
		$where ['orderformid'] = $orderformid;
		$orderstateModel->where ( $where )->save ( $data );

		// 写入到营收状态表
		$data = array();
		$data ['orderformid'] = $orderformid;
		$data ['ordersn'] = $ordersn;
		$data ['status'] = 0;
		$data ['assisstatus'] = 0;
		$data ['domain'] =  $this->getDomain();
		$orderyingshouexchangeModel = D('Orderyingshouexchange');
		$orderyingshouexchangeModel->create();
		$orderyingshouexchangeModel->add($data);

		//写入到网站状态接口表
		$data = array();
		$data['ordersn'] = $ordersn;
		$data['type'] = 2 ;  //表示分配
		$data['content'] = "订单分配给" . $companyName . "配送点";
		$data['date'] = date('Y-m-d H:i:s');
		$data['origin'] = $origin;
		$data['domain'] = $this->getDomain();
		$webstatusModel = D('Webstatus');
		$webstatusModel->create();
		$webstatusModel->add($data);

		//发票处理
		$invoiceModel = D('Invoice');
		$where = array();
		$where['ordersn'] = $ordersn;
		$data = array();
		$data['company']= $companyName;
		$invoiceModel->where($where)->save($data);


		return;
	}

	// 显示产品明细等
	public function showproducts() {
		// 取得记录号
		$record = $_REQUEST ['orderformid'];
		
		// 设置查询条件
		$where ['orderformid'] = $record;
		
		$orderproductsModel = D ( 'Orderproducts' );
		
		// 取得订单产品信息
		$orderproducts = $orderproductsModel->where ( $where )->select ();
		// 返回数据
		$this->ajaxReturn ( $orderproducts, 'JSON' );
	}

	/**
	 * 地址查询界面
	 */
	public function searchAddressInput(){
		// 返回当前的模块名
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName );
		$this->display($moduleName.'/searchaddressinput');
	}

	/* 查询 */
	public function searchviewAddress() {
		if (IS_POST) {
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '地址查询操作');

			// 生成list字段列表
			$listFields = $focus->serchListFields;
			// 模块的ID
			$moduleId = 'orderformid';

			// 建立查询条件
			$where = array();
			$searchText = urldecode($_REQUEST ['searchTextAddress']); // 查询内容
			if (!empty ( $searchText )) {
				if($searchText == '全部'){
					$where ['address'] = array(
						'like',
						'%%'
					);
					$where ['_logic'] = 'and';
					unset($_SESSION ['searchTextAddress' . $moduleName]);
				}else {
					$where ['address'] = array(
						'like',
						'%' . $searchText . '%'
					);
					$where ['_logic'] = 'and';
					$_SESSION ['searchTextAddress' . $moduleName] = $searchText;
				}
			}else{
				$searchText = $_SESSION ['searchTextAddress' . $moduleName]; // 查询内容
				if (!empty($searchText)) {
					$where ['address'] = array(
						'like',
						'%' . $searchText . '%'
					);
					$where ['_logic'] = 'and';
				}
			}
			$where ['domain'] = $this->getDomain();


			$total = $focus->where($where)->count(); // 查询满足要求的总记录数

			//使用cookie读取rows
			$listMaxRows = $_COOKIE['listMaxRows'];
			if(!empty($listMaxRows)){

			}else{
				$listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
			}

			//订单配送还要显示两个统计数据
			$listMaxRows = $listMaxRows - 1;

			// 导入分页类
			import('ORG.Util.Page'); // 导入分页类
			// 取得显示页数
			$pageNumber = $_REQUEST ['page'];
			// 取得页数
			$_GET ['p'] = $pageNumber;
			$Page = new Page ($total, $listMaxRows);

			//保存页数
			$_SESSION [$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

			// 查询模块的数据
			foreach($listFields as $key => $value) {
				$selectFields[] = $key;
			}
			array_unshift ( $selectFields, $moduleId );

			$listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();
			$orderHandleArray ['total'] = $total;
			if (count($listResult) > 0) {
				$orderHandleArray  = $listResult;
			} else {
				$orderHandleArray  = array();
			}
			$data = array('total' => $total, 'rows' => $orderHandleArray);
			$this->ajaxReturn($data);

		} else {

			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '地址查询操作');

			// 生成list字段列表
			$listFields = $focus->searchListFields;
			// 模块的ID
			$moduleId = $focus->getPk();
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;

			// 建立查询条件
			$where = array();
			$searchText = $_REQUEST ['searchTextAddress']; // 查询内容

			$url = U('OrderDistribution/searchviewAddress', array('searchTextAddress' => $searchText));
			$this->assign('url',$url);
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;
			$this->assign("listHeader", $listHeader); // 列表头
			$this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法

			//如果存在页数,获取
			if(isset($_REQUEST['pagetype'])){
				$pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
			}else{
				$pageNumber = 1;
			}
			$this->assign( 'pagenumber',$pageNumber);
			//是否存在选中的行号
			if(isset($_REQUEST['rowIndex'])){
				$this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
			}else{
				$this->assign ( 'rowIndex',0);
			}

			$this->display('OrderDistribution/searchviewaddress'); // 查询的结果显示
		}
	}

	/**
	 * 配送店查询的输入
	 */
	public function searchCompanyInput(){
		$this->display('OrderDistribution/searchcompanyinput');
	}


	/* 查询配送店 */
	public function searchviewCompany() {

		if(IS_POST){
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '地址查询操作');

			// 生成list字段列表
			$listFields = $focus->serchListFields;
			// 模块的ID
			$moduleId =  'orderformid';

			// 建立查询条件
			$where = array();
			$searchText =  urldecode($_REQUEST ['searchTextCompany']); // 查询内容

			if (!empty ( $searchText )) {
				$where ['company'] = $searchText;
				$where ['_logic'] = 'and';
				$_SESSION ['searchTextCompany' . $moduleName] = $searchText;
			}else{
				$searchText = $_SESSION ['searchTextCompany' . $moduleName]; // 查询内容
				if(!empty($searchText)) {
					$where ['company'] = $searchText;
					$where ['_logic'] = 'and';
				}
			}

			$where ['domain'] = $this->getDomain();

			$total = $focus->where($where)->count(); // 查询满足要求的总记录数

			//使用cookie读取rows
			$listMaxRows = $_COOKIE['listMaxRows'];
			if(!empty($listMaxRows)){

			}else{
				$listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
			}

			//订单配送还要显示两个统计数据
			$listMaxRows = $listMaxRows - 1;

			// 导入分页类
			import('ORG.Util.Page'); // 导入分页类
			$pageNumber = $_REQUEST ['page'];  	// 取得显示页数
			// 取得页数
			$_GET ['p'] = $pageNumber;
			$Page = new Page ($total, $listMaxRows);

			//保存页数
			$_SESSION [$moduleName . 'searchviewcompany' . 'page'] = $pageNumber;

			// 查询模块的数据
			foreach($listFields as $key => $value) {
				$selectFields[] = $key;
			}
			array_unshift ( $selectFields, $moduleId );
			$listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

			$orderHandleArray ['total'] = $total;
			if (count($listResult) > 0) {
				$orderHandleArray  = $listResult;
			} else {
				$orderHandleArray  = array();
			}
			$data = array('page'=>$Page->firstRow,'total' => $total, 'rows' => $orderHandleArray,'sql' => $focus->getLastSql());
			$this->ajaxReturn($data, 'JSON');

		}else{
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '分配店查询操作');

			// 生成list字段列表
			$listFields = $focus->searchListFields;
			// 模块的ID
			$moduleId = $focus->getPk();
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;

			// 建立查询条件
			$where = array();
			$searchCompany = $_REQUEST ['searchTextCompany']; // 查询内容

			$url = U('OrderDistribution/searchviewCompany', array('searchTextCompany' => $searchCompany));
			$this->assign('url',$url);
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;
			$this->assign("listHeader", $listHeader); // 列表头
			$this->assign('returnAction', 'searchviewCompany'); // 定义返回的方法

			//如果存在页数,获取
			if(isset($_REQUEST['pagetype'])){
				$pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
			}else{
				$pageNumber = 1;
			}
			$this->assign( 'pagenumber',$pageNumber);
			//是否存在选中的行号
			if(isset($_REQUEST['rowIndex'])){
				$this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
			}else{
				$this->assign ( 'rowIndex',0);
			}

			$this->display('OrderDistribution/searchviewcompany'); // 查询的结果显示
		}
		

	}

	/**
	 * 综合查询页面
	 */
	public function searchOtherInput(){
		// 返回当前的模块名
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName );
		$this->display($moduleName.'/searchotherinput');
	}

	/**
	 * 其他条件查询
	 */
	public function searchviewOther() {

		if(IS_POST){
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '地址查询操作');

			// 生成list字段列表
			$listFields = $focus->serchListFields;
			// 模块的ID
			$moduleId = strtolower($moduleName) . 'id';


			// 生成list字段列表
			$listFields = $focus->searchListFields;
			// 模块的ID
			$moduleId = 'orderformid';  // $focus->getPk();
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;

			// 建立查询条件
			$where = array ();
			$searchText = urldecode($_REQUEST ['searchTextOther']); // 查询内容
			if (!empty ( $searchText )) {
				if($searchText == '全部'){
					$where ['address'] = array(
						'like',
						'%%'
					);
					unset($_SESSION ['searchTextOther' . $moduleName]);
				}else {
					foreach ($focus->searchFields as $value) {
						$where [$value] = array(
							'like',
							'%' . $searchText . '%'
						);
					}
					$where ['_logic'] = 'OR';
					$map['_complex'] = $where;
					$_SESSION ['searchTextOther' . $moduleName] = $searchText;
				}
			}else{
				if(isset($_SESSION ['searchTextOther' . $moduleName])) {
					$searchText = $_SESSION ['searchTextOther' . $moduleName]; // 查询内容
					foreach ($focus->searchFields as $value) {
						$where [$value] = array(
							'like',
							'%' . $searchText . '%'
						);
					}
					$where ['_logic'] = 'OR';
					$map['_complex'] = $where;
				}
			}
			$map['domain'] = $this->getDomain();

			$total = $focus->where($map)->count(); // 查询满足要求的总记录数

			//使用cookie读取rows
			$listMaxRows = $_COOKIE['listMaxRows'];
			if(!empty($listMaxRows)){

			}else{
				$listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
			}

			//订单配送还要显示两个统计数据
			$listMaxRows = $listMaxRows - 1;

			// 导入分页类
			import('ORG.Util.Page'); // 导入分页类
			// 取得显示页数
			$pageNumber = $_REQUEST ['page'];
			// 取得页数
			$_GET ['p'] = $pageNumber;
			$Page = new Page ($total, $listMaxRows);

			//保存页数
			$_SESSION [ 'OrderDistributionsearchviewother' . 'page'] =  $pageNumber;

			// 查询模块的数据
			foreach($listFields as $key => $value) {
				$selectFields[] = $key;
			}
			array_unshift ( $selectFields, $moduleId );
			$listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

			$orderHandleArray ['total'] = $total;
			if (count($listResult) > 0) {
				$orderHandleArray  = $listResult;
			} else {
				$orderHandleArray  = array();
			}
			$data = array('total' => $total, 'rows' => $orderHandleArray,'pagenumber'=>$pageNumber);
			$this->ajaxReturn($data);
		}else{
			// 取得模块的名称
			$moduleName = $this->getActionName ();
			$this->assign ( 'moduleName', $moduleName ); // 模块名称

			// 启动当前模块
			$focus = D ( $moduleName );

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign ( 'operName', '其他查询操作' );

			// 生成list字段列表
			$listFields = $focus->searchListFields;
			// 模块的ID
			$moduleId = $focus->getPk();
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;

			// 建立查询条件
			$searchText = $_REQUEST ['searchTextOther']; // 查询内容


			$this->assign ( 'searchTextValue', $searchText );

			$url =  U('OrderDistribution/searchviewOther', array('searchTextOther' => $searchText));
			$this->assign('url',$url);
			$this->assign ( 'moduleId', $moduleId );

			$searchOption = $focus->searchFields;
			$this->assign ( 'searchOption', $searchOption );
			$this->assign ( 'returnAction', 'searchviewOther' ); // 定义返回的方法

			//如果存在页数,获取
			if(isset($_REQUEST['pagetype'])){
				$pageNumber =  $_SESSION['OrderDistributionsearchviewother' . 'page'];
			}else{
				$pageNumber = 1;
			}
			$this->assign( 'pagenumber',$pageNumber);
			//是否存在选中的行号
			if(isset($_REQUEST['rowIndex'])){
				$this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
			}else{
				$this->assign ( 'rowIndex',0);
			}

			$this->display ( 'OrderDistribution/searchviewother' ); // 查询的结果显示
		}

	}


	/**
	 * 显示下午的订单
	 */
	public function orderNumberAp(){
		if(IS_POST){
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '下午订单');

			// 生成list字段列表
			$listFields = $focus->serchListFields;
			// 模块的ID
			$moduleId =  'orderformid';

			// 建立查询条件
			$where = array();
			$searchText =  '下午';

			if (!empty ( $searchText )) {
				$where ['ap'] = '下午';
				$where ['_logic'] = 'and';
				$_SESSION ['searchTextAp' . $moduleName] = '下午';
			}else{
				$searchText = $_SESSION ['searchTextAp' . $moduleName]; // 查询内容
				if(!empty($searchText)) {
					$where ['ap'] = $searchText;
					$where ['_logic'] = 'and';
				}
			}

			$where ['domain'] = $this->getDomain();

			$total = $focus->where($where)->count(); // 查询满足要求的总记录数
			//var_dump($focus->getLastSql());

			//使用cookie读取rows
			$listMaxRows = $_COOKIE['listMaxRows'];
			if(!empty($listMaxRows)){

			}else{
				$listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
			}

			//订单配送还要显示两个统计数据
			$listMaxRows = $listMaxRows - 1;

			// 导入分页类
			import('ORG.Util.Page'); // 导入分页类
			$pageNumber = $_REQUEST ['page'];  	// 取得显示页数
			// 取得页数
			$_GET ['p'] = $pageNumber;
			$Page = new Page ($total, $listMaxRows);

			//保存页数
			$_SESSION [$moduleName . 'searchviewAp' . 'page'] = $pageNumber;

			// 查询模块的数据
			foreach($listFields as $key => $value) {
				$selectFields[] = $key;
			}
			array_unshift ( $selectFields, $moduleId );
			$listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();
			//var_dump($focus->getLastSql());

			$orderHandleArray ['total'] = $total;
			if (count($listResult) > 0) {
				$orderHandleArray  = $listResult;
			} else {
				$orderHandleArray  = array();
			}
			$data = array('page'=>$Page->firstRow,'total' => $total, 'rows' => $orderHandleArray,'sql' => $focus->getLastSql());
			$this->ajaxReturn($data, 'JSON');

		}else{
			// 取得模块的名称
			$moduleName = $this->getActionName();
			$this->assign('moduleName', $moduleName); // 模块名称

			// 启动当前模块
			$focus = D($moduleName);

			// 取得对应的导航名称
			$navName = $focus->getNavName($moduleName);
			$this->assign('navName', $navName); // 导航民
			$this->assign('operName', '下午订单');

			// 生成list字段列表
			$listFields = $focus->searchListFields;
			// 模块的ID
			$moduleId = $focus->getPk();
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;

			// 建立查询条件
			$where = array();
			$searchCompany = $_REQUEST ['searchTextCompany']; // 查询内容

			$url = U('OrderDistribution/orderNumberAp', array('searchTextAp' => '下午'));
			$this->assign('url',$url);
			// 加入模块id到listHeader中
			// array_unshift($listFields,$moduleNameId);
			$listHeader = $listFields;
			$this->assign("listHeader", $listHeader); // 列表头
			$this->assign('returnAction', 'searchviewCompany'); // 定义返回的方法

			//如果存在页数,获取
			if(isset($_REQUEST['pagetype'])){
				$pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
			}else{
				$pageNumber = 1;
			}
			$this->assign( 'pagenumber',$pageNumber);
			//是否存在选中的行号
			if(isset($_REQUEST['rowIndex'])){
				$this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
			}else{
				$this->assign ( 'rowIndex',0);
			}

			$this->display('OrderDistribution/ordernumberap'); // 查询的结果显示
		}

	}


	/* 取得打印需要的数据 */
	function getPrintOrder() {
		// 取得订单号
		$record = $_REQUEST ['orderformid'];
		// 查询订单
		$orderform_model = D ( 'Orderform' );
		$orderform = $orderform_model->where ( "orderformid=$record" )->find ();
		// 查询订货
		$orderproducts_model = D ( 'Orderproducts' );
		$orderproducts = $orderproducts_model->where ( "orderformid=$record" )->select ();
		// echo $orderproducts_model->getLastSql();
		$order ['orderform'] = $orderform;
		$order ['orderproducts'] = $orderproducts;
		
		$this->ajaxReturn ( $order, 'JSON' );
	}
	
	/* 设定订单已打印状态 */
	function setOrderPrinted() {
		// 取得订单号
		$record = $_REQUEST ['orderformid'];
		// 查询订单
		$orderform_model = D ( 'Orderform' );
		$data ['state'] = '已打印';
		$result = $orderform_model->where ( "orderformid=$record" )->save ( $data );
		echo $orderform_model->getLastSql ();
	}
	
	// 设置打印机类型
	public function setprintupdateview() {
		// 返回当前的模块名
		$currentModule = $this->getActionName ();
		// var_dump($currentModule);
		$focus = D ( $currentModule );
		
		// 引入模块菜单
		$Modulemenu = A ( 'ModuleMenu' );
		$Modulemenu->index ( $this->getActionName (), 'createview' );
		
		// 分公司的名称
		$userInfo = $_SESSION ['userInfo'];
		$name = $userInfo ['company'];
		
		$companymgr_model = D ( 'Companymgr' );
		$printtype = $companymgr_model->field ( 'printtype' )->where ( "name='$name'" )->find ();
		// dump($printtype);
		
		$this->assign ( 'printtype', $printtype ['printtype'] ); // 指定字段获得焦点
		                                                         
		// dump($this->blocks);
		$this->display ( 'OrderHandle/setprintupdateview' );
	}
	
	// 保存打印设置
	public function saveSetPrint() {
		
		// 打印类型
		$printtype = $_REQUEST ['printtypesetup'];
		// var_dump($printtype);
		
		// 分公司的名称
		$userInfo = $_SESSION ['userInfo'];
		$name = $userInfo ['company'];
		
		$companymgr_model = D ( 'Companymgr' );
		$data ['printtype'] = $printtype;
		$result = $companymgr_model->where ( "name='$name'" )->save ( $data );
		// echo $companymgr_model->getLastSql();
		
		// 保存成功
		// $this->redirect("OrderHandle/setprintdetailview", array(), 0, '页面跳转中...');
		$returnArr = array ();
		$this->ajaxReturn ( $returnArr, 'JSON' );
	}
	
	// 显示打印设置的保存结果
	public function setprintdetailview() {
		// 返回当前的模块名
		$currentModule = $this->getActionName ();
		// var_dump($currentModule);
		$focus = D ( $currentModule );
		
		// 引入模块菜单
		$Modulemenu = A ( 'ModuleMenu' );
		$Modulemenu->index ( $this->getActionName (), 'createview' );
		
		// 分公司的名称
		$userInfo = $_SESSION ['userInfo'];
		$name = $userInfo ['company'];
		
		$companymgr_model = D ( 'Companymgr' );
		$printtype = $companymgr_model->field ( 'printtype' )->where ( "name='$name'" )->find ();
		// dump($printtype);
		$this->assign ( 'printtype', $printtype ['printtype'] );
		$this->display ();
	}
	
	/* 返回分公司订单的情况 */
	function getOrderMonit() {
		// 分公司的名称
		$userInfo = $_SESSION ['userInfo'];
		$company = $userInfo ['company'];
		
		$ordermonitModel = D ( 'Ordermonit' );
		$where = array ();
		$where ['name'] = '全部';
		$where ['domain'] = $this->getDomain();
		$ordermonit = $ordermonitModel->where ( $where )->select ();
		if (empty ( $ordermonit )) {
			$ordermonit = array ();
		}
		$this->ajaxReturn ( $ordermonit, 'JSON' );
	}
	
	/* 分会分公司的分配名称和代码 */
	function getCompanyMgr() {
		$companymgr_model = D ( 'Companymgr' );
		$where = array();
		$where['domain'] = $this->getDomain();
		$companyMgr = $companymgr_model->where($where)->field ( "name,distributionCode" )->select ();
		$test = array();
		foreach($companyMgr as $value){
			$test[$value['distributionCode']] = $value['name'];
		}
		$this->ajaxReturn ( $test, 'JSON' );
	}
	
		

	// 作废订单的页面
	public function cancelview() {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称
		                                             
		// 启动当前模块
		$focus = D ( $moduleName );
		
		// 取得对应的导航名称
		$navName = $focus->getNavName ( $moduleName );
		$this->assign ( 'navName', $navName ); // 导航条
		                                       
		// 取得返回的是列表还是查询列表
		$returnAction = $_REQUEST ['returnAction'];
		$this->assign ( 'returnAction', $returnAction );

		// 模块的ID
		$moduleId = $focus->getPk ();
		

		// 取得记录ID
		$record = $_REQUEST ['record'];
		$where [$moduleId] = $record;
		
		// 返回模块的行记录
		$result = $focus->where ( $where )->find ();
		
		// 返回区块
		$blocks = $focus->detailBlocks ( $result );
		
		$this->assign ( 'blocks', $blocks );
		$this->assign ( 'record', $record );
		$this->assign ( 'pagenumber',$_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
		$this->assign ( 'rowIndex', $_REQUEST['rowIndex']);  //选中的行号
		$this->assign ( 'pagetype', $_REQUEST['pagetype']);
		
		// 返回从表的内容
		$this->get_slave_table ( $record );
		
		$this->display ( $moduleName . '/cancelview' );
	}
	
	/**
	 * 根据输入的编码处理订单
	 * code 输入的编码
	 */
	public function orderDistributionByCode() {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称
		                                             
		// 启动当前模块的模型
		$focus = D ( $moduleName );
		
		// 分公司的名称
		$userInfo = $this->userInfo;
		$company = $userInfo ['department'];
		
		// 获得处理过了的编码
		$code = $_REQUEST ['code'];
				
		// 定义返回的数组
		$returnInfo = array ();
		
		/**
		 * 先编辑配送店的编码 **
		 */
		// 根据编码取得配送点名字
		$companyMgrModel = D ( 'CompanyMgr' );
		$where ['distributionCode'] = $code; // 配送点的编号
		$where ['domain'] = $this->getDomain();
		$companymgrResult = $companyMgrModel->field ( 'name,distributioncode' )->where ( $where )->find ();
		if ($companymgrResult) {
			$companyName = $companymgrResult ['name'];
		} else {
			$returnInfo ['error'] = 'error';
			$returnInfo ['msg'] = '没有查到信息';
			$this->ajaxReturn ( $returnInfo );
		}
		
		// 根据配送信息，处理订单
		$orderformid = $_REQUEST ['orderformid'];
		//获得订单号
		$where = array();
		$where['orderformid'] = $orderformid;
		$orderformResult = $focus->field('ordersn,company,origin')->where($where)->find();
		$ordersn = $orderformResult['ordersn'];
		$origin  = $orderformResult['origin'];
		if(empty($origin)) $origin = '';

		if(!empty($orderformResult['company'])){
			//已经分配过了订单
			$data = array();
			$data ['company'] = $companyName;
			$data ['state'] = '订餐';
			$where = array();
			$where ['orderformid'] = $orderformid;
			$focus->where ( $where )->save ( $data );

			// 同时写入日志中
			// 记入操作到action中
			$orderactionModel = D ( 'Orderaction' );
			$orderactionData ['orderformid'] = $orderformid; // 订单号
			$orderactionData ['ordersn'] = $ordersn; // 订单号
			$orderactionData ['action'] = "订单从".$orderformResult['company']."重新分配给" . $companyName . "配送点";
			$orderactionData ['logtime'] = date ( 'H:i:s' );
			$orderactionData ['domain'] = $this->getDomain();
 			$orderactionModel->create ();
			$result = $orderactionModel->add ( $orderactionData );

		}else{
			$data = array();
			$data ['company'] = $companyName;
			$where = array();
			$where ['orderformid'] = $orderformid;
			$focus->where ( $where )->save ( $data );

			// 同时写入日志中
			// 记入操作到action中
			$orderactionModel = D ( 'Orderaction' );
			$orderactionData ['orderformid'] = $orderformid; // 订单号
			$orderactionData ['ordersn'] = $ordersn; // 订单号
			$orderactionData ['action'] = "订单分配给" . $companyName . "配送点";
			$orderactionData ['logtime'] = date ( 'H:i:s' );
			$orderactionData ['domain'] = $this->getDomain();
			$orderactionModel->create ();
			$result = $orderactionModel->add ( $orderactionData );
		}

		// 写入到状态表中
		$orderstateModel = D ( 'Orderstate' );
		$data = array ();
		$data ['distribution'] = 1;
		$data ['distributiontime'] = date ( 'Y-m-d H:i:s' );
		$data ['distributioncontent'] = $companyName;
		$where = array ();
		$where ['orderformid'] = $orderformid;
		$orderstateModel->where ( $where )->save ( $data );

		// 写入到营收状态表
		$data = array();
		$data ['orderformid'] = $orderformid;
		$data ['ordersn'] = $ordersn;
		$data ['status'] = 0;
		$data ['assisstatus'] = 0;
		$data ['domain'] =  $this->getDomain();
		$orderyingshouexchangeModel = D('Orderyingshouexchange');
		$orderyingshouexchangeModel->create();
		$orderyingshouexchangeModel->add($data);

		//写入到网站状态接口表
		$data = array();
		$data['ordersn'] = $ordersn;
		$data['type'] = 2 ;  //表示分配
		$data['content'] = "订单分配给" . $companyName . "配送点";
		$data['date'] = date('Y-m-d H:i:s');
		$data['origin'] = $origin;
		$data['domain'] = $this->getDomain();
		$webstatusModel = D('Webstatus');
		$webstatusModel->create();
		$webstatusModel->add($data);

		//发票处理
		$invoiceModel = D('Invoice');
		$where = array();
		$where['ordersn'] = $ordersn;
		$data = array();
		$data['company']= $companyName;
		$invoiceModel->where($where)->save($data);

		// 定义返回
		$returnInfo ['success'] = 'success';
		$returnInfo ['data'] = $invoiceModel->getLastSql(); //$webstatusModel->getLastSql(); //$companyName;
		$this->ajaxReturn ( $returnInfo, 'JSON' );
	}
	
	// 输入代码，获得分公司名字
	public function getCompanyByCode() {
		
		// 获得处理过了的编码
		$code = substr($_SERVER['QUERY_STRING'],-1);

		// 定义返回的数组
		$returnInfo = array ();
		
		/**
		 * 先编辑配送店的编码 **
		 */
		// 根据编码取得配送点名字
		$companyMgrModel = D ( 'CompanyMgr' );
		$where ['distributionCode'] = $code; // 配送点的编号
		$where ['domain'] = $this->getDomain();
		$companymgrResult = $companyMgrModel->field ( 'name,distributioncode' )->where ( $where )->find ();

		$companyName = array ();
		if ($companymgrResult) {
			$companyName ['company'] = $companymgrResult ['name'];
		} else {
			$returnInfo ['error'] = 'error';
			$returnInfo ['msg'] = '没有查到信息';
			$this->ajaxReturn ( $returnInfo );
		}
		
		// 定义返回
		$returnInfo ['success'] = 'success';
		$returnInfo ['data'] = $companyName;
		$this->ajaxReturn ( $returnInfo, 'JSON' );
	}
	
	// 作废订单
	public function cancel() {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称
		                                             
		// 启动当前模块
		$focus = D ( $moduleName );

		$returnAction = $_REQUEST['returnAction'];
		// 订单号
		$record = $_REQUEST ['record'];

		$where = array();
		$where ['orderformid'] = $record;

		//删除订餐内容
		$orderproductsModel =D('orderproducts');
		$orderproductsModel->where($where)->delete();

		// 将订单状态设为已作废
		$where = array ();
		$where ['orderformid'] = $record;
		$data = array ();
		$data ['state'] = '已作废';
		$data ['totalmoney'] = 0;
		$data ['paidmoney'] = 0;
		$data ['shouldmoney'] = 0;
		$data ['shippingmoney'] = 0;
		$data ['goodsmoney'] = 0;
		$data ['ordertxt'] = '';
		$focus->where ( $where )->save ( $data );
		
		//获得ordersn
		$orderformResult = $focus->where($where)->find();
		$ordersn = $orderformResult['ordersn'];
		
		// 同时写入日志中
		// 记入操作到action中
		$orderactionModel = D ( 'Orderaction' );
		$orderactionData ['ordersn'] = $ordersn;
		$orderactionData ['orderformid'] = $record;
		$company = '联络员';
		$orderactionData ['action'] = "订单联络员作废";
		$orderactionData ['logtime'] = date ( 'Y-m-d H:i:s' );
		$orderactionModel->create ();
		$result = $orderactionModel->add ( $orderactionData );
		
		// 写入到状态表中
		$orderstateModel = D ( 'Orderstate' );
		$data = array ();
		$data ['cancel'] = 1;
		$data ['canceltime'] = date ( 'Y-m-d H:i:s' );
		$data ['cancelcontent'] = '联络员';
		$where = array ();
		$where ['orderformid'] = $record;
		$orderstateModel->where ( $where )->save ( $data );

		// 写入到营收状态表
		$data = array();
		$data ['orderformid'] = $record;
		$data ['ordersn'] = $ordersn;
		$data ['status'] = 0;
		$data ['assisstatus'] = 0;
		$data ['domain'] =  $this->getDomain();
		$orderyingshouexchangeModel = D('Orderyingshouexchange');
		$orderyingshouexchangeModel->create();
		$orderyingshouexchangeModel->add($data);

        //查询电子发票，将电子发票设置为退票状态
        //启动退票
        $where = array();
        $where['ordersn'] = $ordersn;
        //$where['cancel_state'] = 0;
        $where['state'] = 2;
        $where['domain'] = $this->getDomain();
        $data = array();
        $data['cancel_state'] = 1;
        $invoicewebModel = D('invoiceweb');
        $invoicewebModel->where($where)->save($data);

        //写入到日志中
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['log'] = "联络员作废电子票";
        $data['date'] = date('Y-m-d H:i:s');
        $data['domain'] = $this->getDomain();
        $invoiceweblogModel = D('invoiceweblog');
        $invoiceweblogModel->create();
        $invoiceweblogModel->add($data);


		$pagetype = $_REQUEST['pagetype'];
		// 生成查看的url
		$detailviewUrl = U ( "OrderDistribution/" . $returnAction, array (
			'record' => $record,'returnAction'=>$returnAction,
			'rowIndex' => $_REQUEST['rowIndex'],'pagetype' =>$pagetype
		) );
		$return = $detailviewUrl;
		$info['status'] = 'success';
		$info['info'] ='作废完毕' ;
		$info['url'] = $return;
		$this->ajaxReturn(json_encode($info),'EVAL');

	}

	// 返回从表的内容:产品
	public function get_slave_table($record)
	{
		// 取得产品信息
		$orderproducts_model = D('Orderproducts');
		$orderproducts = $orderproducts_model->field('orderformid,code,name,shortname,price,number,money')->where("orderformid=$record")->select();
		$this->assign('orderproducts', $orderproducts);

		// 单独取得订单金额
		$orderform_model = D('Orderform');
		$orderform = $orderform_model->field('totalmoney')->where("orderformid=$record")->select();
		$totalmoney = $orderform [0] ['totalmoney'];
		$this->assign('totalmoney', $totalmoney);

		//取得活动信息
		$orderactivity_model = D('Orderactivity');
		$orderactivity = $orderactivity_model->where("orderformid=$record")->select();
		$this->assign('orderactivity',$orderactivity);

		//取得订单支付信息
		$orderpayment_model = D('Orderpayment');
		$orderpayment = $orderpayment_model->where("orderformid=$record")->select();
		$this->assign('orderpayment',$orderpayment);

		// 取得订单的状态
		$orderStateModel = D('Orderstate');
		$orderStateResult = $orderStateModel->where("orderformid=$record")->find();  //
		$this->assign('orderstate', $orderStateResult);

		// 取得订单日志
		$orderaction_model = D('Orderaction');
		$orderaction = $orderaction_model->where("orderformid=$record")->select(); //
		$this->assign('orderaction', $orderaction);

	}

	/**
	 * 统计下午订单数量和金额
	 */
	public function getOrderNumberAp(){

		//取得订单数量
		$orderform_model = D('Orderform');
		$where = array();
		$where['ap'] = '下午';
		$where['domain'] = $this->getDomain();
		$ordernumber = $orderform_model->where($where)->count();

		//取得订单金额
		$ordermoney = $orderform_model->where($where)->sum('totalmoney');


		$orderNumberAp = array(
			'number' => $ordernumber,
			'money' =>  $ordermoney
		);
		$this->ajaxReturn ( $orderNumberAp, 'JSON' );
	}
}

?>
