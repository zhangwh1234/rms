<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/10
 * Time: 下午5:23
 * 财务管理的分录底稿
 */

class YingshouFinanceAction extends YingshouAction
{

    /**
     * listview
     * 覆盖listview
     */
    public function listview()
    {
        $this->listviewTwo();
    }

    //生成报数单界面
    public function generalview()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        //当前日期
        $this->assign('cdate', date('Y-m-d'));
        $this->display($moduleName . '/generalview');
    }

    /**
     * 产生分录底稿
     */
    public function financeCalculate()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //日期
        $financeDate = $_REQUEST['finance_date'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        $reveueConnectDb = $this->connectReveueDb($financeDate);

        // 连接数据库
        $revparmgrModel = M("revparmgr_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financeModel = M("finance_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financecontentModel = M("financecontent_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financeresultModel = M("financeresult", " ", $reveueConnectDb);

        /**
         * 从revparmgr结账表中汇总，产生分录底稿
         * 首先要检查是否存在总公司的结账数据
         */
        $where = array();
        $where['date'] = $financeDate;
        $where['company'] = '总公司';
        $where['domain'] = $domain;
        $revparmgrResult = $revparmgrModel->where($where)->select();

        //如果没有结账数据,抛出错误
        if (empty($revparmgrResult)) {
            $data = array();
            $data['result'] = '没有结账数据';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $financeresultModel->create();
            $financeresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //获取所有分公司
        $where = array();
        $where['date'] = $financeDate;
        $where['company'] = array('nqe', '总公司');
        $where['domain'] = $domain;
        $companyResult = $revparmgrModel->field('company')->where($where)->select();

        //先删除历史记录
        $where = array();
        $where['date'] = $financeDate;
        $where['company'] = array('nqe', '总公司');
        $where['domain'] = $domain;
        $financeModel->where($where)->delete();
        $financecontentModel->where($where)->delete();

        foreach ($companyResult as $company) {
            //主表
            //营业收入
            $where = array();
            $where['date'] = $financeDate;
            $where['company'] = array('nqe', '总公司');
            $where['domain'] = $domain;
            $revparmgrResult = $revparmgrModel->where($where)->sum('money');
            $data = array();
            $data['company'] = $revparmgr['company'];
            $data['money'] = $revparmgrResult['money'];
            $data['date'] = $financeDate;
            $data['domain'] = $this->getDomain();
            $financeModel->create();
            $financeid = $financeModel->add($data);
            
            //外送收入
            $where = array();
            $where['date'] = $financeDate;
            $where['company'] = $company;
            $where['domain'] = $domain;
            $where['name'] = '外送收入';
            $revparmgrResult = $revparmgrModel->where($where)->sum('money');
            $i = 0;
            $data = array();
            $data['financeid'] = $financeid;
            $data['jouranalorder'] = $i++;                         //项次
            $data['summary'] = $company . $financeDate . '营收';    //摘要
            $data['subject'] = '551001';                           //科目
            $data['subjectname'] = '';                             //科目名称
            $data['department']  = '';                             //部门
            $data['debitcredit'] = '2';                            //借贷
            $data['money'] = $revparmgrResult['money'];            //原币金额
            $data['check'] ='';                                    //核算项
            $data['checkname'] = '';                               //核算项名称
            $financecontentModel->create();
            $financecontentModel->save($data);

        }

        $res = array();
        $res['state'] = 1;
        $this->ajaxReturn($res);
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
		$where['domain'] = $this->getDomain();
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

		if ($reporttype == 'Tongjiordersendtime') {
			$this->outputTongjiOrderSendtimeExcel ( $reporttype, $where );
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

}
