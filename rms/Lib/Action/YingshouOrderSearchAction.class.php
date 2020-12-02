<?php
/**
 * 营收订单查询系统
 * 根据小毛的要求，提供给查询当前订单和历史订单的要求
 * 2019-04-12 创建
 */

class YingshouOrderSearchAction extends YingshouAction
{

    // listview
    public function listview()
    {
        if (IS_POST) {

            // 分公司的名称
            $company = $this->userInfo['department'];

            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;

            // 建立查询条件
            $where = array();
            $searchOption = $_REQUEST['searchOption']; // 查询项目
            $searchText = $_REQUEST['searchText']; // 查询内容
            if (isset($searchOption) && isset($searchText)) {
                if ($searchOption == '全部') {
                    foreach ($focus->searchFields as $value) {
                        $where[$value] = array(
                            'like',
                            '%' . $searchText . '%',
                        );
                        $where['_logic'] = 'or';
                    }
                } else {
                    $where[$searchOption] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                }
                $this->assign('searchOptionValue', $searchOption);
                $this->assign('searchTextValue', $searchText);
                $_SESSION['searchOption' . $moduleName] = $searchOption;
                $_SESSION['searchText' . $moduleName] = $searchText;
            } else {
                if (isset($_SESSION['searchOption' . $moduleName], $_SESSION['searchText' . $moduleName])) {
                    if ($_SESSION['searchOption' . $moduleName] == '全部') {
                        foreach ($focus->searchFields as $value) {
                            $where[$value] = array(
                                'like',
                                '%' . $_SESSION['searchText' . $moduleName] . '%',
                            );
                            $where['_logic'] = 'or';
                        }
                    } else {
                        $where[$_SESSION['searchOption']] = array(
                            'like',
                            '%' . $_SESSION['searchText' . $moduleName] . '%',
                        );
                    }
                    $this->assign('searchOptionValue', $_SESSION['searchOption' . $moduleName]);
                    $this->assign('searchTextValue', $_SESSION['searchText' . $moduleName]);
                }
            }

            $map['_complex'] = $where;

            // 查询开始日期
            $startDate = $_REQUEST['startDate'];
            if (!empty($startDate)) {
                $map['custdate'] = array(
                    array(
                        'EGT',
                        $startDate,
                    ),
                ); // 日期区间查询
                $dbNameTableName = substr($startDate, 0, 4) . '.rms_orderform';
                $this->assign('startDate', $startDate);
                $_SESSION['startDate' . $moduleName] = $startDate;
            } elseif (isset($_SESSION['startDate' . $moduleName])) {
                $startDate = $_SESSION['startDate' . $moduleName];
                $map['custdate'] = array(
                    array(
                        'EGT',
                        $startDate,
                    ),
                );
                $dbNameTableName = '' . substr($startDate, 0, 4) . '.rms_orderform';
                $this->assign('startDate', $startDate);
            }

            // 查询结束日期
            $endDate = $_REQUEST['endDate'];
            if (!empty($endDate)) {
                $this->assign('endDate', $endDate);
                array_push($map['custdate'], array(
                    'ELT',
                    $endDate,
                )); // 日期区间查询
                $_SESSION['endDate' . $moduleName] = $endDate;
            } elseif (isset($_SESSION['endDate' . $moduleName])) {
                $endDate = $_SESSION['endDate' . $moduleName];
                array_push($map['custdate'], array(
                    'ELT',
                    $endDate,
                )); // 日期区间查询
                $this->assign('endDate', $endDate);
            }

            // 查询的午别
            $searchAp = $_REQUEST['searchAp'];
            if (isset($searchAp)) {
                if ($searchAp == '全天') {
                    $this->assign('searchAp', $searchAp);
                } else {
                    $map['ap'] = $searchAp;
                    $_SESSION['searchAp' . $moduleName] = $searchAp;
                    $this->assign('searchAp', $searchAp);
                }
            } else {
                if (isset($_SESSION['searchAp' . $moduleName])) {
                    $map['ap'] = $_SESSION['searchAp' . $moduleName];
                    $this->assign('searchAp', $_SESSION['searchAp' . $moduleName]);
                } else { // 如果没有指定上午或者下午，那取当前时间的上午和下午
                    $this->assign('searchAp', $this->getAp());
                }
            }

            $revparType = $this->getRevparType();
            if($revparType == 'company'){
                $where['company'] = $company;
            }
            $map['domain'] = $this->getDomain();

            $userInfo = $this->userInfo;

            if ($revparType == 'company') {
                $map['company'] = $userInfo['department'];

            }

            $name = 'orderform' . substr($startDate, 0, 4);

            $roomDate = $startDate;
            $roomAp = $searchAp;
            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            //连接订单dns
            $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //如果不是，就要选择备份库
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $diningsaleModel = M("diningsale_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $diningsalepaymentModel = M("diningsalepayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            } else {
                //连接当前库和表
                $orderformModel = D('orderform');
                $ordergoodsModel = D('orderproducts');
                $orderfinanceModel = D('orderfinance');
                $diningsaleModel = D('diningsale');
                $diningsalepaymentModel = D('diningsalepayment');
                $orderpaymentModel = D('orderpayment');
                $orderactivityModel = D('orderactivity');
            }

            $total = $orderformModel->where($map)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            //计算订单总额
            $totalmoney = $orderformModel->where($map)->Sum('totalmoney');

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            //$selectFields = $listFields;
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);
            array_unshift($selectFields, 'ordersn');
            $listResult = $orderformModel->field($selectFields)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select();
            $sql = $orderformModel->getLastSql();
            $this->assign('moduleId', $moduleId);

            $searchOption = $focus->searchFields;
            $this->assign('searchOption', $searchOption);
            $this->assign('returnAction', 'searchview'); // 定义返回的方法

            $orderHistoryArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHistoryArray['rows'] = $listResult;
            } else {
                $orderHistoryArray['rows'] = array();
            }
            $footer = array(
                array(
                    'ordertxt' => '订单总额:',
                    'telphone' => $totalmoney,

                ),
            );
            $data = array('total' => $total, 'rows' => $listResult, 'footer' => $footer, 'sql' => $sql);
            $this->ajaxReturn($data);
        } else {

            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName);

            // 启动列表菜单
            $this->assign('moduleName', $moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            $searchArray = array();
            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $searchArray),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                    'showFooter' => true,
                    'toolbar' => '#yingshouordersearch-listview-datagrid-toolbar',
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }
            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );

            $searchOption = $focus->searchFields;
            $this->assign('searchOption', $searchOption);

            $this->assign('datagrid', $datagrid);
            //显示当前日期
            $this->assign('startDate',date('Y-m-d'));
            $this->assign('endDate',date('Y-m-d'));

            $this->display('YingshouOrderSearch/listview');
        }
    }

    // 查看数据
    public function detailview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 模块的ID
        $moduleNameId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['ordersn'];
        // 重新设定订单历史查询的数据库
        $startDate = $_REQUEST['startDate'];
        $startAp = $_REQUEST['startAp'];
        $roomDate = $startDate;
        $roomAp = $startAp;
        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        //连接订单dns
        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleModel = M("diningsale_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsalepaymentModel = M("diningsalepayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $orderfinanceModel = D('orderfinance');
            $diningsaleModel = D('diningsale');
            $diningsalepaymentModel = D('diningsalepayment');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
        }

        // 返回模块的记录
        $result = $orderformModel->where("ordersn=$record")->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);

        // 返回从表的内容
        $this->get_slave_table($record, $startDate, $startAp);
        $this->display('YingshouOrderSearch/detailview');
    }

    // 返回从表的内容:产品
    public function get_slave_table($record, $startDate, $startAp)
    {

        // 设定日期
        $roomDate = $startDate;
        $roomAp = $startAp;
        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        //连接订单dns
        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleModel = M("diningsale_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderstateModel = M("orderastate_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactionModel = M("orderaction_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderfinanceModel = D('orderfinance');
            $diningsaleModel = D('diningsale');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
            $orderstateModel = D('orderactivity');
            $orderactionModel = D('orderaction');
        }

        $orderproducts = $orderproductsModel->field('orderformid,code,name,price,number,money')->where("ordersn=$record")->select();
        $this->assign('orderproducts', $orderproducts);

        //取得活动信息
        $orderactivity = $orderactivityModel->where("ordersn=$record")->select();
        $this->assign('orderactivity', $orderactivity);

        //取得订单支付信息
        $orderpayment = $orderpaymentModel->table($dbNameTableName)->where("ordersn=$record")->select();
        $this->assign('orderpayment', $orderpayment);

        // 取得订单的状态
        $orderStateResult = $orderstateModel->table($dbNameTableName)->where("ordersn=$record")->find(); //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderaction = $orderactionModel->table($dbNameTableName)->where("ordersn=$record")->select();
        $this->assign('orderaction', $orderaction);
    }

}
