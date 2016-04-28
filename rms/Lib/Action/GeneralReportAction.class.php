<?php
// 报表的启动程序
class GeneralReportAction extends ModuleAction {
	public function index() {
		// 取得模块的名称
		$moduleName = $this->getActionName ();
		$this->assign ( 'moduleName', $moduleName ); // 模块名称

		// 启动当前模块的模型
		$focus = D ( $moduleName );

		// 取得对应的导航名称
		$navName = $focus->getNavName($moduleName);
		$this->assign('navName', $navName); // 导航名称

		$this->display ( 'listview' );
	}

	// 订单量统计
	public function getReport() {
		$this->reporttype = $_REQUEST ['reporttype']; // 报表类型
		$this->display ( 'GeneralReport/generalreport' );
	}

	// 显示报表
	public function showReport() {
		$startDate = $_REQUEST ['reportStartDate'];
		$endDate = $_REQUEST ['reportEndDate'];
		// 建立查询条件
		$where = array ();

		// 查询开始日期
		if (! empty ( $startDate ) && ! empty ( $endDate )) {
			$where ['date'] = array (
				array (
					'EGT',
					$startDate
				),
				array (
					'ELT',
					$endDate
				)
			); // 日期区间查询
			// $dbNameTableName = 'rms_'.substr($startDate,0,4).'.rms_orderform';
		} else {
			// 查询结束日期
			if (! empty ( $startDate )) {
				$where ['date'] = array (
					'EGT',
					$startDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
			}
			// 查询结束日期
			if (! empty ( $endDate )) {
				$where ['date'] = array (
					'ELT',
					$endDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
			}
		}

		// 查询的午别
		$searchAp = $_REQUEST ['searchAp'];
		if (isset ( $searchAp )) {

			if ($searchAp == '全天') {
				$this->assign ( 'searchApValue', $searchAp );
			} else {
				$where ['ap'] = $searchAp;
				$_SESSION ['searchAp' . $moduleName] = $searchAp;
				$this->assign ( 'searchApValue', $searchAp );
			}
		} else {
			if (isset ( $_SESSION ['searchAp' . $moduleName] )) {
				$where ['ap'] = $_SESSION ['searchAp' . $moduleName];
				$this->assign ( 'searchApValue', $_SESSION ['searchAp' . $moduleName] );
			} else { // 如果没有指定上午或者下午，那取当前时间的上午和下午
				$this->assign ( 'searchApValue', $this->getAp () );
			}
		}
		$where ['domain'] = $_SERVER['HTTP_HOST'];
		$reporttype = $_REQUEST ['reporttype']; // 报表类型
		if ($reporttype == 'Tongjiorderhurry') {
			$this->tongjiOrderHurry ( $reporttype, $where );
			return;
		}
		if ($reporttype == 'Tongjibigcustomer') {
			$this->tongjiBigCustomer ( $reporttype, $where );
			return;
		}

		if ($reporttype == 'Tongjiorderbackcancel') {
			$this->tongjiOrderBackCancel ( $reporttype, $where );
			return;
		}

		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->where ( $where )->order ( 'number desc' )->select ();

		// 查询项目名称
		$ordertongjiNameResult = $ordertongjiModel->Distinct ( true )->field ( 'name' )->where ( $where )->select ();
		// 查询统计的项目内容
		$where [] = 'length(content) > 0';
		$ordertongjiContentResult = $ordertongjiModel->Distinct ( true )->field ( 'content' )->where ( $where )->select ();
		// 定义统计的数组
		$tongji = array ();
		// 建立统计的二维数组表
		foreach ( $ordertongjiNameResult as $nameValue ) {
			foreach ( $ordertongjiContentResult as $contentValue ) {
				$tongji [$nameValue ['name']] [$contentValue ['content']] = 0;
			}
		}

		foreach ( $ordertongjiResult as $key => $value ) {
			foreach ( $ordertongjiNameResult as $nameValue ) {
				foreach ( $ordertongjiContentResult as $contentValue ) {
					if ($value ['name'] == $nameValue ['name']) {
						if ($value ['content'] == $contentValue ['content']) {
							$tongji [$value ['name']] [$contentValue ['content']] += $value ['number'];
						}
					}
				}
			}
		}
		$this->tongji = $tongji;
		// 建立表格头
		foreach ( $ordertongjiContentResult as $contentValue ) {
			$listHeader [] = $contentValue ['content'];
		}
		$this->listHeader = $listHeader; // 表格头

		$this->assign ( 'startdate', $generalreport_startdate );
		$this->display ( 'GeneralReport/reportresult' );
	}

	/**
	 * 导出通用的统计表
	 */
	public function outputTongjiExcel() {
		$startDate = $_REQUEST ['reportStartDate'];
		$endDate = $_REQUEST ['reportEndDate'];
		// 建立查询条件
		$where = array ();

		// 查询开始日期
		if (! empty ( $startDate ) && ! empty ( $endDate )) {
			$where ['date'] = array (
				array (
					'EGT',
					$startDate
				),
				array (
					'ELT',
					$endDate
				)
			); // 日期区间查询
			// $dbNameTableName = 'rms_'.substr($startDate,0,4).'.rms_orderform';
		} else {
			// 查询结束日期
			if (! empty ( $startDate )) {
				$where ['date'] = array (
					'EGT',
					$startDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
				$startDate = date ( 'Y-m-d' );
			}
			// 查询结束日期
			if (! empty ( $endDate )) {
				$where ['date'] = array (
					'ELT',
					$endDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
				$endDate = date ( 'Y-m-d' );
			}
		}

		// echo '<pre>';
		// var_dump($endDate);
		// 查询的午别
		$searchAp = $_REQUEST ['searchAp'];
		if (isset ( $searchAp )) {

			if ($searchAp == '全天') {
				$this->assign ( 'searchApValue', $searchAp );
			} else {
				$where ['ap'] = $searchAp;
				$_SESSION ['searchAp' . $moduleName] = $searchAp;
				$this->assign ( 'searchApValue', $searchAp );
			}
		} else {
			if (isset ( $_SESSION ['searchAp' . $moduleName] )) {
				$where ['ap'] = $_SESSION ['searchAp' . $moduleName];
				$this->assign ( 'searchApValue', $_SESSION ['searchAp' . $moduleName] );
			} else { // 如果没有指定上午或者下午，那取当前时间的上午和下午
				$this->assign ( 'searchApValue', $this->getAp () );
			}
		}
		$where['domain'] = $_SERVER['HTTP_HOST'];
		$reporttype = $_REQUEST ['reporttype']; // 报表类型

		if ($reporttype == 'Tongjiorderhurry') {
			$this->outputTongjiOrderHurryExcel ( $reporttype, $where );
			return;
		}
		if ($reporttype == 'Tongjibigcustomer') {
			$this->outputTongjiBigCustomerExcel ( $reporttype, $where );
			return;
		}

		if ($reporttype == 'Tongjiorderbackcancel') {
			$this->outputTongjiOrderBackCancelExcel ( $reporttype, $where );
			return;
		}

		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// $ordertongjiModel->db(1,$connectionConfig);
		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->where ( $where )->select ();
		// var_dump($ordertongjiModel->getLastSql());
		// 查询项目名称
		$ordertongjiNameResult = $ordertongjiModel->Distinct ( true )->field ( 'name' )->where($where)->select ();
		// 查询统计的项目内容
		$where [] = "length(content) > 0";
		$ordertongjiContentResult = $ordertongjiModel->Distinct ( true )->field ( 'content' )->where($where)->select ();
		// 定义统计的数组
		$tongji = array ();
		// 建立统计的二维数组表
		foreach ( $ordertongjiNameResult as $nameValue ) {
			foreach ( $ordertongjiContentResult as $contentValue ) {
				$tongji [$nameValue ['name']] [$contentValue ['content']] = 0;
			}
		}

		foreach ( $ordertongjiResult as $key => $value ) {
			foreach ( $ordertongjiNameResult as $nameValue ) {
				foreach ( $ordertongjiContentResult as $contentValue ) {
					if ($value ['name'] == $nameValue ['name']) {
						if ($value ['content'] == $contentValue ['content']) {
							$tongji [$value ['name']] [$contentValue ['content']] += $value ['number'];
						}
					}
				}
			}
		}

		// echo '<pre>';
		// var_dump($tongji);
		// 建立表格头
		foreach ( $ordertongjiContentResult as $contentValue ) {
			$listHeader [] = $contentValue ['content'];
		}

		// var_dump($listHeader);
		// exit;

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = new PHPExcel ();

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $i, 1, $value );
			$i ++;
		}

		$i = 1;
		$l = 0;
		foreach ( $tongji as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );

		$filename = L ( $reporttype ) . $startDate . '-' . $endDate;

		// Redirect output to a client’s web browser (Excel5)
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}

	/**
	 * 导出催送的excel表
	 */
	private function outputTongjiOrderHurryExcel($reporttype, $where) {
		$reporttype = 'Tongjihurry';
		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'rectime',
			'custtime',
			'hurrytime',
			'hurrynumber',
			'hurrytimetime',
			'sendname',
			'company'
		);
		// $ordertongjiModel->db(1,$connectionConfig);
		$where = array ();
		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '接线员';
		$listHeader [] = '录入时间';
		$listHeader [] = '送餐时间';
		$listHeader [] = '催送时间';
		$listHeader [] = '催送次数';
		$listHeader [] = '催时间';
		$listHeader [] = '送餐员';
		$listHeader [] = '分公司';

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = new PHPExcel ();

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );

		$filename = L ( $reporttype ) . $startDate . '-' . $endDate;

		// Redirect output to a client’s web browser (Excel5)
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}

	/**
	 * 导出大客户的数据
	 */
	private function outputTongjiBigCustomerExcel($reporttype, $where) {
		$reporttype = 'Tongjibigcustomer';
		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'totalmoney',
			'company'
		);
		// $ordertongjiModel->db(1,$connectionConfig);
		//$where = array ();
		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '金额';
		$listHeader [] = '分公司';

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = new PHPExcel ();

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );

		$filename = L ( $reporttype ) . $startDate . '-' . $endDate;

		// Redirect output to a client’s web browser (Excel5)
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}

	/**
	 * 导出退餐废单的数据
	 */
	private function outputTongjiOrderBackCancelExcel($reporttype, $where) {
		$reporttype = 'Tongjiorderbc';
		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'totalmoney',
			'rectime',
			'custtime',
			'state',
			'sendname',
			'company'
		);
		// $ordertongjiModel->db(1,$connectionConfig);
		$where = array ();
		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '接线员';
		$listHeader [] = '金额';
		$listHeader [] = '录入时间';
		$listHeader [] = '送餐时间';
		$listHeader [] = '状态';
		$listHeader [] = '送餐员';
		$listHeader [] = '分公司';

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = new PHPExcel ();

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );

		$filename = L ( $reporttype ) . $startDate . '-' . $endDate;

		// Redirect output to a client’s web browser (Excel5)
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}

	/**
	 * 导出通用的统计表
	 */
	public function outputAllTongjiExcel() {
		$startDate = $_REQUEST ['reportStartDate'];
		$endDate = $_REQUEST ['reportEndDate'];
		// 建立查询条件
		$where = array ();

		// 查询开始日期
		if (! empty ( $startDate ) && ! empty ( $endDate )) {
			$where ['date'] = array (
				array (
					'EGT',
					$startDate
				),
				array (
					'ELT',
					$endDate
				)
			); // 日期区间查询
			// $dbNameTableName = 'rms_'.substr($startDate,0,4).'.rms_orderform';
		} else {
			// 查询结束日期
			if (! empty ( $startDate )) {
				$where ['date'] = array (
					'EGT',
					$startDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
				$startDate = date ( 'Y-m-d' );
			}
			// 查询结束日期
			if (! empty ( $endDate )) {
				$where ['date'] = array (
					'ELT',
					$endDate
				); // 日期区间查询
			} else {
				$where ['date'] = array (
					'EQ',
					date ( 'Y-m-d' )
				); // 今日的日期
				$endDate = date ( 'Y-m-d' );
			}
		}

		// 查询的午别
		$searchAp = $_REQUEST ['searchAp'];
		if (isset ( $searchAp )) {

			if ($searchAp == '全天') {
				$this->assign ( 'searchApValue', $searchAp );
			} else {
				$where ['ap'] = $searchAp;
				$_SESSION ['searchAp' . $moduleName] = $searchAp;
				$this->assign ( 'searchApValue', $searchAp );
			}
		} else {
			if (isset ( $_SESSION ['searchAp' . $moduleName] )) {
				$where ['ap'] = $_SESSION ['searchAp' . $moduleName];
				$this->assign ( 'searchApValue', $_SESSION ['searchAp' . $moduleName] );
			} else { // 如果没有指定上午或者下午，那取当前时间的上午和下午
				$this->assign ( 'searchApValue', $this->getAp () );
			}
		}

		// 读取连接信息,根据用户访问的url来判断
		// require APP_PATH.'Conf/datapath.php';
		// $HTTP_POST = $_SERVER['HTTP_HOST'];
		// $HTTP_POST = $HTTP_POST.'History';
		// $dbConfig = $rmsDataPath[$HTTP_POST];
		// $connectionDns = $dbConfig['DB_TYPE'].'://'.$dbConfig['DB_USER'].':'.$dbConfig['DB_PWD'].'@'.$dbConfig['DB_HOST'].':'.$dbConfig['DB_PORT'].'/'.$dbConfig['DB_NAME'];
		// $connectionConfig = $localhostHistory;
		// $connectionConfig = $rmsDataPath['localhostHistory'];

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = new PHPExcel ();

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );

		// 循环报表类型
		$reportTypeArr = array ();
		$reportTypeArr [] = 'Tongjiorder'; // 订单量统计
		$reportTypeArr [] = 'Tongjiproductsnumber'; // 产品销售统计
		$reportTypeArr [] = 'Tongjiproductscn'; // 产品客户数
		$reportTypeArr [] = 'Tongjiproductstotalmoney'; // 产品销售额
		$reportTypeArr [] = 'Tongjiproductscf'; // 产品销售分部
		$reportTypeArr [] = 'Tongjitelname'; // 接线量统计

		$worksheetNumber = 0;
		foreach ( $reportTypeArr as $reporttype ) {
			// 连接历史数据库
			$ordertongjiModel = D ( $reporttype );

			// $ordertongjiModel->db(1,$connectionConfig);
			// 返回查询的数据
			$ordertongjiResult = $ordertongjiModel->where ( $where )->select ();
			// var_dump($ordertongjiModel->getLastSql());
			// var_dump($ordertongjiResult);
			// 查询项目名称
			$ordertongjiNameResult = $ordertongjiModel->Distinct ( true )->field ( 'name' )->select ();
			// var_dump($ordertongjiModel->getLastSql());
			// var_dump($ordertongjiNameResult);
			if (empty ( $ordertongjiNameResult )) {
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setTitle ( L ( $reporttype ) );
				$objPHPExcel->createSheet ();
				$worksheetNumber += 1;
				continue;
			}
			// 查询统计的项目内容
			$ordertongjiContentResult = $ordertongjiModel->Distinct ( true )->field ( 'content' )->select ();
			// 定义统计的数组
			$tongji = array ();
			// 建立统计的二维数组表
			foreach ( $ordertongjiNameResult as $nameValue ) {
				foreach ( $ordertongjiContentResult as $contentValue ) {
					$tongji [$nameValue ['name']] [$contentValue ['content']] = 0;
				}
			}

			foreach ( $ordertongjiResult as $key => $value ) {
				foreach ( $ordertongjiNameResult as $nameValue ) {
					foreach ( $ordertongjiContentResult as $contentValue ) {
						if ($value ['name'] == $nameValue ['name']) {
							if ($value ['content'] == $contentValue ['content']) {
								$tongji [$value ['name']] [$contentValue ['content']] += $value ['number'];
							}
						}
					}
				}
			}

			$listHeader = array ();
			// 建立表格头
			foreach ( $ordertongjiContentResult as $contentValue ) {

				$listHeader [] = $contentValue ['content'];
			}

			$i = 1;
			foreach ( $listHeader as $key => $value ) {

				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $i, 1, $value );
				$i ++;
			}

			$i = 1;
			$l = 0;
			foreach ( $tongji as $tongjiKey => $tongjiValue ) {
				$i = $i + 1;
				$l = 0;
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
				foreach ( $tongjiValue as $colKey => $colValue ) {
					$l = $l + 1;
					$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $colValue );
				}
			}

			// Rename worksheet

			$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setTitle ( L ( $reporttype ) );

			$objPHPExcel->createSheet ();
			$worksheetNumber += 1;
		}
		//催餐统计
		$this->outputAllTongjiHurry ( $objPHPExcel, $worksheetNumber ,$where );
		$worksheetNumber  = $worksheetNumber;
		$objPHPExcel->createSheet ();
		$worksheetNumber += 1;
		//大客户
		$this->outputAllTongjiBigCustomer ($objPHPExcel, $worksheetNumber ,$where);
		$objPHPExcel->createSheet ();
		$worksheetNumber += 1;
		//退餐废单
		$this->outputAllTongjiOrderBackCancel ($objPHPExcel, $worksheetNumber ,$where);
		$objPHPExcel->createSheet ();
		$worksheetNumber += 1;

		//导出所有的订单
		$this->outputAllTongjiAllOrder ($objPHPExcel, $worksheetNumber,$where);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );

		$filename = '全部统计' . $startDate . '-' . $endDate;

		// Redirect output to a client’s web browser (Excel5)
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );

		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
		$objWriter->save ( 'php://output' );
		exit ();
	}

	/**
	 * 导出全部excel催送统计表
	 */
	private function outputAllTongjiHurry($objPHPExcel, $worksheetNumber,$where) {
		$worksheetNumber = $worksheetNumber;
		$reporttype = 'Tongjihurry';
		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'rectime',
			'custtime',
			'hurrytime',
			'hurrynumber',
			'hurrytimetime',
			'sendname',
			'company'
		);
		// $ordertongjiModel->db(1,$connectionConfig);

		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '接线员';
		$listHeader [] = '录入时间';
		$listHeader [] = '送餐时间';
		$listHeader [] = '催送时间';
		$listHeader [] = '催送次数';
		$listHeader [] = '催时间';
		$listHeader [] = '送餐员';
		$listHeader [] = '分公司';

		// 引入类
		//vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = $objPHPExcel;

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		$objPHPExcel->createSheet ();
		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );
		$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setTitle ( L ( $reporttype ) );


	}

	/**
	 * 导出全部excel大客户统计表
	 */
	private function outputAllTongjiBigCustomer($objPHPExcel, $worksheetNumber,$where) {

		$reporttype = 'Tongjibigcustomer';

		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'totalmoney',
			'company'
		);

		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();


		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '金额';
		$listHeader [] = '分公司';

		// 创建excel对象
		$objPHPExcel = $objPHPExcel;

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		$objPHPExcel->createSheet ();
		$worksheetNumber += 1;
	}

	/**
	 * 导出全部excel退餐废单统计表
	 */
	private function outputAllTongjiOrderBackCancel($objPHPExcel, $worksheetNumber,$where) {

		$reporttype = 'Tongjiorderbc';
		// 连接历史数据库
		$ordertongjiModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'totalmoney',
			'rectime',
			'custtime',
			'state',
			'sendname',
			'company'
		);
		// $ordertongjiModel->db(1,$connectionConfig);

		// 返回查询的数据
		$ordertongjiResult = $ordertongjiModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '接线员';
		$listHeader [] = '金额';
		$listHeader [] = '录入时间';
		$listHeader [] = '送餐时间';
		$listHeader [] = '状态';
		$listHeader [] = '送餐员';
		$listHeader [] = '分公司';

		// 引入类
		vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = $objPHPExcel;

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $ordertongjiResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );

		$objPHPExcel->createSheet ();
		$worksheetNumber += 1;
	}


	/**
	 * 导出全部excel导出所有的订单
	 */
	private function outputAllTongjiAllOrder($objPHPExcel, $worksheetNumber,$where) {
		$worksheetNumber = $worksheetNumber;
		$reporttype = 'Tongjiallorder';
		// 连接历史数据库
		$tongjiallorderModel = D ( $reporttype );

		// 查询项目名称
		$fields = array (
			'address',
			'ordertxt',
			'totalmoney',
			'telname',
			'rectime',
			'telphone',
			'sendname',
			'company',
			'beizhu',
		);
		// $ordertongjiModel->db(1,$connectionConfig);

		// 返回查询的数据
		$tongjiallorderResult = $tongjiallorderModel->field ( $fields )->where ( $where )->select ();

		// 建立表格头
		$listHeader [] = '送餐地址';
		$listHeader [] = '数量规格';
		$listHeader [] = '总金额';
		$listHeader [] = '接线员';
		$listHeader [] = '录入时间';
		$listHeader [] = '订餐电话';
		$listHeader [] = '电话';
		$listHeader [] = '分公司';
		$listHeader [] = '备注';

		// 引入类
		//vendor ( 'PHPExcel.PHPExcel' );

		// 创建excel对象
		$objPHPExcel = $objPHPExcel;

		// 设置文档的属性
		$objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
		$i = 1;
		foreach ( $listHeader as $key => $value ) {
			$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $i, 2, $value );
			$i ++;
		}

		$i = 2;
		$l = 0;
		foreach ( $tongjiallorderResult as $tongjiKey => $tongjiValue ) {
			$i = $i + 1;
			$l = 0;
			// $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $tongjiKey );
			foreach ( $tongjiValue as $colKey => $colValue ) {
				$l = $l + 1;
				$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setCellValueByColumnAndRow ( $l, $i, $colValue );
			}
		}

		$objPHPExcel->createSheet ();
		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( L ( $reporttype ) );
		$objPHPExcel->setActiveSheetIndex ( $worksheetNumber )->setTitle ( L ( $reporttype ) );


	}


	/**
	 * *********************************************************
	 */
	// 统计催餐
	public function tongjiOrderHurry($reporttype, $where) {
		// 启动催送的数组
		$tongjihurryModel = D ( 'Tongjihurry' );
		$where = array ();
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'rectime',
			'custtime',
			'hurrytime',
			'hurrynumber',
			'sendname',
			'company'
		);
		$tongjihurryResult = $tongjihurryModel->field ( $fields )->where($where)->select ();
		// 显示数据
		$this->assign ( 'hurry', $tongjihurryResult );
		$this->display ( 'GeneralReport/reportresulthurry' );
	}

	/**
	 * ********************
	 * 统计大订单，规则，大于500
	 */
	private function tongjiBigCustomer($reporttype, $where)
	{

		// 实例大订单
		$tongjibigcustomerModel = D ( 'Tongjibigcustomer' );

		$fields = array (
			'address',
			'ordertxt',
			'totalmoney',
			'company'
		);

		$tongjibigcustomerResult = $tongjibigcustomerModel->field ( $fields )->where($where)->select ();

		// 显示数据
		$this->assign ( 'bigcustomer', $tongjibigcustomerResult );
		$this->display ( 'GeneralReport/reportresultbigcustomer' );
	}

	/**
	 * *********************************************************
	 */
	// 退餐和废单的统计
	public function tongjiOrderBackCancel($reporttype, $where) {
		// 实例大订单
		$tongjiorderbcModel = D ( 'Tongjiorderbc' );
		$fields = array (
			'address',
			'ordertxt',
			'telname',
			'totalmoney',
			'rectime',
			'custtime',
			'state',
			'sendname',
			'company'
		);
		$tongjiorderbcResult = $tongjiorderbcModel->field ( $fields )->where($where)->select ();
		// 显示数据
		$this->assign ( 'orderbc', $tongjiorderbcResult );
		$this->display ( 'GeneralReport/reportresultorderbc' );
	}
	public function _empty() {
	}

	/**
	 * *********************************************************
	 */
	// 执行计算统计的脚本
	public function actionTongjiAll() {

		fastcgi_finish_request();

		$domain = $_SERVER['HTTP_HOST'];
		import('@.Extend.OrderTongji');
		$tongji = new OrderTongji();
		$tongji->actionTongji($domain);

		return;

	}
}
?>
