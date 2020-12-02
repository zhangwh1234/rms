<?php

/**
 * 历史订单查询模块
 */
class OrderHistoryAction extends ModuleAction
{

    public function index()
    {
        // 如果是从listview进入的，必须删除session['where']
        unset($_SESSION['searchOption' . 'OrderHistory']);
        unset($_SESSION['searchText' . 'OrderHistory']);
        unset($_SESSION['startDate' . 'OrderHistory']);
        unset($_SESSION['endDate' . 'OrderHistory']);
        unset($_SESSION['searchAp' . 'OrderHistory']);
        $this->searchview();
    }

    // listview
    public function searchview()
    {
        if (IS_POST) {

            $startDate = $_REQUEST['startDate'];
            $endDate = $_REQUEST['endDate'];
            //判断是否在同一个月，如果不在同一个月，就转到另外流程
            $startMonth = substr($startDate, 0, 7);
            $endMonth = substr($endDate, 0, 7);
            if ($startMonth != $endMonth) {
                $this->acrossMonthview();
                return;
            }

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
            $map['domain'] = $this->getDomain();

            $userInfo = $this->userInfo;
            if ($userInfo['rolename'] == '调度员') {
                $map['company'] = $userInfo['department'];
            }

            $name = 'orderform' . substr($startDate, 0, 4);

            // 取得表的连接信息
            import('COM.Db.SystemDb');
            $systemDb = new SystemDb();
            $connectConfig = $systemDb->getHistoryDbConnection($name, $this->getDomain());

            $db_type = 'mysql';
            $db_user = trim($connectConfig['db_user']);
            $db_pwd = trim($connectConfig['db_pwd']);
            $db_host = trim($connectConfig['db_host']);
            $db_port = trim($connectConfig['db_port']);
            $db_name = trim($connectConfig['db_name']);

            $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

            // 连接历史数据库
            $orderformModel = M("orderform", "rms_", $connectionDns);

            //先处理历史数据表，如果是跨月查询，就执行跨月逻辑，把查询记录保存到orderform表中，如果当月查询，就按照原来的逻辑查询
            // 查询开始日期
            $startMonth = substr($_REQUEST['startDate'], 5, 2);
            $startMonth = (integer) $startMonth;
            // 查询结束日期
            $endMonth = substr($_REQUEST['endDate'], 5, 2);
            $endMonth = (integer) $endMonth;

            if ($startMonth == $endMonth) {
                //跨月查询
                $dbNameTableName = 'rms_orderform_' . substr($startDate, 5, 2);
            } else {
                //相同月份
                $dbNameTableName = 'rms_orderform';
            }

            $total = $orderformModel->table("$dbNameTableName")->where($map)->count(); // 查询满足要求的总记录数

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
            $totalmoney = $orderformModel->table("$dbNameTableName")->where($map)->Sum('totalmoney');

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
            array_unshift($selectFields, $moduleId, 'invoice_open');
            array_unshift($selectFields, 'ordersn');
            $listResult = $orderformModel->table("$dbNameTableName")->field($selectFields)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select();

            //未开票金额
            $map_noopen = array();

            $map_noopen = $map;

            $map_noopen['invoice_open'] = array('NEQ', '已开');
            $invoice_no_open = $orderformModel->table("$dbNameTableName")->where($map_noopen)->sum('totalmoney');
            if (!$invoice_no_open) {
                $invoice_no_open = '0.00';
            }

            //已开票金额
            $map_open = $map;
            $map_open['invoice_open'] = array('EQ', '已开');
            $invoice_open = $orderformModel->table("$dbNameTableName")->where($map_open)->sum('totalmoney');
            if (!$invoice_open) {
                $invoice_open = '0.00';
            }

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
                    'address' => '未开发票:' . $invoice_no_open . '  |' . '   已开发票:' . $invoice_open,
                ),
            );
            //
            $data = array('total' => $total, 'rows' => $listResult, 'footer' => $footer, 'sql' => $startMonth . $endMonth);
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
            $invoice_header = array('width' => 10, 'align' => 'center');
            //array_push($listFields,$invoice_header);
            $listFields['invoice_open'] = array('width' => 5, 'align' => 'center');

            // 模块的ID
            $moduleId = $focus->getPk();

            $searchArray = array();
            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/searchview', $searchArray),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                    'showFooter' => true,
                    'toolbar' => '#orderhistory-searchview-datagrid-toolbar',
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
                'formatter' => $moduleName . 'SearchviewModule.operate',
            );

            $searchOption = $focus->searchFields;
            $this->assign('searchOption', $searchOption);

            $this->assign('datagrid', $datagrid);

            $this->display('OrderHistory/searchview');
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

        // 返回新建区块和字段
        // $blocks = $focus->detailBlocks();

        // 回调主程序需要的参数,比如下拉框的数据
        //$this->returnMainFnPara();

        // 取得记录ID
        $record = $_REQUEST['ordersn'];
        // 重新设定订单历史查询的数据库
        $startDate = $_REQUEST['startDate'];
        $name = 'orderform' . substr($startDate, 0, 4);
        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb();
        $connectConfig = $systemDb->getHistoryDbConnection($name, $this->getDomain());
        $db_type = 'mysql';
        $db_user = trim($connectConfig['db_user']);
        $db_pwd = trim($connectConfig['db_pwd']);
        $db_host = trim($connectConfig['db_host']);
        $db_port = trim($connectConfig['db_port']);
        $db_name = trim($connectConfig['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 连接历史数据库
        $orderformModel = M("orderformhistory", "rms_", $connectionDns);

        // 重新设定订单历史查询的数据库
        $dbNameTableName = 'rms_orderform_' . substr($startDate, 5, 2);

        // 返回模块的记录
        $result = $orderformModel->table("$dbNameTableName")->where("ordersn='$record'")->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);

        // 返回从表的内容
        $this->get_slave_table($record, $startDate);
        $this->display('OrderHistory/detailview');
    }

    // 返回从表的内容:产品
    public function get_slave_table($record, $startDate)
    {

        $name = 'orderform' . substr($startDate, 0, 4);
        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb();
        $connectConfig = $systemDb->getHistoryDbConnection($name, $this->getDomain());
        $db_type = 'mysql';
        $db_user = trim($connectConfig['db_user']);
        $db_pwd = trim($connectConfig['db_pwd']);
        $db_host = trim($connectConfig['db_host']);
        $db_port = trim($connectConfig['db_port']);
        $db_name = trim($connectConfig['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 设定日期
        $date = $_REQUEST['startDate'];
        $startdate = $_REQUEST['startDate'];
        $dbNameTableName = 'rms_orderproducts_' . substr($startDate, 5, 2);
        // 连接历史数据库
        $orderproductsModel = M("orderproducts_" . substr($startDate, 5, 2), "rms_", $connectionDns);

        $orderproducts = $orderproductsModel->table($dbNameTableName)->field('orderformid,code,name,price,number,money')->where("ordersn='$record'")->select();

        $this->assign('orderproducts', $orderproducts);

        //取得活动信息
        $orderactivity_model = M("orderactivity_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderactivity_' . substr($startDate, 5, 2);
        $orderactivity = $orderactivity_model->table($dbNameTableName)->where("ordersn='$record'")->select();
        $this->assign('orderactivity', $orderactivity);

        //取得订单支付信息
        $orderpayment_model = M("orderpayment_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderpayment_' . substr($startDate, 5, 2);
        $orderpayment = $orderpayment_model->table($dbNameTableName)->where("ordersn='$record'")->select();
        $this->assign('orderpayment', $orderpayment);

        // 取得订单的状态
        $orderStateModel = M("orderstate_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderstate_' . substr($startDate, 5, 2);
        $orderStateResult = $orderStateModel->table($dbNameTableName)->where("ordersn='$record'")->find(); //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderactionModel = M("orderaction_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderaction_' . substr($startDate, 5, 2);
        $orderaction = $orderactionModel->table($dbNameTableName)->where("ordersn='$record'")->select();
        $this->assign('orderaction', $orderaction);
    }

    /* 取得打印需要的数据 */
    public function getPrintOrder()
    {
        // 取得订单号
        $record = $_REQUEST['ordersn'];
        // 重新设定订单历史查询的数据库
        $startDate = $_REQUEST['startDate'];
        $name = 'orderform' . substr($startDate, 0, 4);

        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb();
        $connectConfig = $systemDb->getHistoryDbConnection($name, $this->getDomain());
        $db_type = 'mysql';
        $db_user = trim($connectConfig['db_user']);
        $db_pwd = trim($connectConfig['db_pwd']);
        $db_host = trim($connectConfig['db_host']);
        $db_port = trim($connectConfig['db_port']);
        $db_name = trim($connectConfig['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 连接历史数据库
        $orderformModel = M("orderform", "rms_", $connectionDns);

        // 重新设定订单历史查询的数据库
        $dbNameTableName = 'rms_orderform_' . substr($startDate, 5, 2);

        // 返回模块的记录
        $orderform = $orderformModel->table("$dbNameTableName")->where("ordersn=$record")->find();

        $orderform['printnumber'] = rand(100, 999);

        // 连接历史数据库
        $orderproductsModel = M("orderproducts_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderproducts_' . substr($startDate, 5, 2);
        $orderproducts = $orderproductsModel->table($dbNameTableName)->where("ordersn=$record")->select();

        //取得活动信息
        $orderactivityModel = M("orderactivity_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderactivity_' . substr($startDate, 5, 2);
        $orderactivity = $orderactivityModel->table($dbNameTableName)->where("ordersn=$record")->select();

        //取得订单支付信息
        $orderpaymentModel = M("orderpayment_" . substr($startDate, 5, 2), "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderpayment_' . substr($startDate, 5, 2);
        $orderpayment = $orderpaymentModel->table($dbNameTableName)->where("ordersn=$record")->select();

        $order['orderform'] = $orderform;
        $order['orderproducts'] = $orderproducts;
        $order['orderactivity'] = $orderactivity;
        $order['orderpayment'] = $orderpayment;

        $this->ajaxReturn($order, 'JSON');
    }

    //跨月查询
    public function acrossMonthview()
    {
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

        // 查询开始日期
        $startDate = $_REQUEST['startDate'];
        $endDate = $_REQUEST['endDate'];
        $startYear = substr($startDate, 0, 4);
        $endYear = substr($endYear, 0, 4);
        $startMonth = substr($startDate, 5, 2);
        $endMonth = substr($endMonth, 5, 2);
        if ($startYear != $endYear) {
            return;
        }
        for ($i = $startMonth; $i <= $endMonth; $i++) {
            if (length($i) == 1) {
                $tableName += " rms_orderform_0" . $i . " ";
                $whereName += " rms_orderform_0" . $i . ".custdate >=".$startDate . " and " . " rms_orderform_0" . $i . ".custdate <=".$endDate ;
            } else {
                $tableName += "rms_orderform_" . $i + " ";
                $whereName += " rms_orderform_" . $i . ".custdate >=" . $startDate . " and " . " rms_orderform_" . $i . ".custdate <=" . $endDate;
            }

        }

        

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
        $map['domain'] = $this->getDomain();

        $userInfo = $this->userInfo;
        if ($userInfo['rolename'] == '调度员') {
            $map['company'] = $userInfo['department'];
        }

        $name = 'orderform' . substr($startDate, 0, 4);

        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb();
        $connectConfig = $systemDb->getHistoryDbConnection($name, $this->getDomain());

        $db_type = 'mysql';
        $db_user = trim($connectConfig['db_user']);
        $db_pwd = trim($connectConfig['db_pwd']);
        $db_host = trim($connectConfig['db_host']);
        $db_port = trim($connectConfig['db_port']);
        $db_name = trim($connectConfig['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 连接历史数据库
        $orderformModel = M("orderform", "rms_", $connectionDns);

        //先处理历史数据表，如果是跨月查询，就执行跨月逻辑，把查询记录保存到orderform表中，如果当月查询，就按照原来的逻辑查询
        // 查询开始日期
        $startMonth = substr($_REQUEST['startDate'], 5, 2);
        $startMonth = (integer) $startMonth;
        // 查询结束日期
        $endMonth = substr($_REQUEST['endDate'], 5, 2);
        $endMonth = (integer) $endMonth;

        if ($startMonth == $endMonth) {
            //跨月查询
            $dbNameTableName = 'rms_orderform_' . substr($startDate, 5, 2);
        } else {
            //相同月份
            $dbNameTableName = 'rms_orderform';
        }

        $total = $orderformModel->table("$dbNameTableName")->where($map)->count(); // 查询满足要求的总记录数

        

        //计算订单总额
        $totalmoney = $orderformModel->table("$dbNameTableName")->where($map)->Sum('totalmoney');

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
        array_unshift($selectFields, $moduleId, 'invoice_open');
        array_unshift($selectFields, 'ordersn');
        $query = " select * from " . $tableName . " where ".  $whereName . " limit 0, 100";
        $listResult = $orderformModel->query($query);

        //未开票金额
        $map_noopen = array();

        $map_noopen = $map;

        $map_noopen['invoice_open'] = array('NEQ', '已开');
        $invoice_no_open = $orderformModel->table("$dbNameTableName")->where($map_noopen)->sum('totalmoney');
        if (!$invoice_no_open) {
            $invoice_no_open = '0.00';
        }

        //已开票金额
        $map_open = $map;
        $map_open['invoice_open'] = array('EQ', '已开');
        $invoice_open = $orderformModel->table("$dbNameTableName")->where($map_open)->sum('totalmoney');
        if (!$invoice_open) {
            $invoice_open = '0.00';
        }

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
                'address' => '未开发票:' . $invoice_no_open . '  |' . '   已开发票:' . $invoice_open,
            ),
        );
        //
        $data = array('total' => $total, 'rows' => $listResult, 'footer' => $footer, 'sql' => $startMonth . $endMonth);
        $this->ajaxReturn($data);

    }

}
