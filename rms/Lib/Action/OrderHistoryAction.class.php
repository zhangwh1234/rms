<?php

/**
 * 历史订单查询模块
 */
class OrderHistoryAction extends ModuleAction
{

    public function index()
    {
        // 如果是从listview进入的，必须删除session['where']
        unset ($_SESSION ['searchOption' . 'OrderHistory']);
        unset ($_SESSION ['searchText' . 'OrderHistory']);
        unset ($_SESSION ['startDate' . 'OrderHistory']);
        unset ($_SESSION ['endDate' . 'OrderHistory']);
        unset ($_SESSION ['searchAp' . 'OrderHistory']);
        $this->searchview();
    }

    // listview
    public function searchview()
    {
        if (IS_POST) {

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
            $searchOption = $_REQUEST ['searchOption']; // 查询项目
            $searchText = $_REQUEST ['searchText']; // 查询内容
            if (isset ($searchOption) && isset ($searchText)) {
                if ($searchOption == '全部') {
                    foreach ($focus->searchFields as $value) {
                        $where [$value] = array(
                            'like',
                            '%' . $searchText . '%'
                        );
                        $where ['_logic'] = 'or';
                    }
                } else {
                    $where [$searchOption] = array(
                        'like',
                        '%' . $searchText . '%'
                    );
                }
                $this->assign('searchOptionValue', $searchOption);
                $this->assign('searchTextValue', $searchText);
                $_SESSION ['searchOption' . $moduleName] = $searchOption;
                $_SESSION ['searchText' . $moduleName] = $searchText;
            } else {
                if (isset ($_SESSION ['searchOption' . $moduleName], $_SESSION ['searchText' . $moduleName])) {
                    if ($_SESSION ['searchOption' . $moduleName] == '全部') {
                        foreach ($focus->searchFields as $value) {
                            $where [$value] = array(
                                'like',
                                '%' . $_SESSION ['searchText' . $moduleName] . '%'
                            );
                            $where ['_logic'] = 'or';
                        }
                    } else {
                        $where [$_SESSION ['searchOption']] = array(
                            'like',
                            '%' . $_SESSION ['searchText' . $moduleName] . '%'
                        );
                    }
                    $this->assign('searchOptionValue', $_SESSION ['searchOption' . $moduleName]);
                    $this->assign('searchTextValue', $_SESSION ['searchText' . $moduleName]);
                }
            }

            $map ['_complex'] = $where;

            // 查询开始日期
            $startDate = $_REQUEST ['startDate'];
            if (!empty ($startDate)) {
                $map ['custdate'] = array(
                    array(
                        'EGT',
                        $startDate
                    )
                ); // 日期区间查询
                $dbNameTableName = substr($startDate, 0, 4) . '.rms_orderform';
                $this->assign('startDate', $startDate);
                $_SESSION ['startDate' . $moduleName] = $startDate;
            } elseif (isset ($_SESSION ['startDate' . $moduleName])) {
                $startDate = $_SESSION ['startDate' . $moduleName];
                $map ['custdate'] = array(
                    array(
                        'EGT',
                        $startDate
                    )
                );
                $dbNameTableName = '' . substr($startDate, 0, 4) . '.rms_orderform';
                $this->assign('startDate', $startDate);
            }

            // 查询结束日期
            $endDate = $_REQUEST ['endDate'];
            if (!empty ($endDate)) {
                $this->assign('endDate', $endDate);
                array_push($map ['custdate'], array(
                    'ELT',
                    $endDate
                )); // 日期区间查询
                $_SESSION ['endDate' . $moduleName] = $endDate;
            } elseif (isset ($_SESSION ['endDate' . $moduleName])) {
                $endDate = $_SESSION ['endDate' . $moduleName];
                array_push($map ['custdate'], array(
                    'ELT',
                    $endDate
                )); // 日期区间查询
                $this->assign('endDate', $endDate);
            }

            // 查询的午别
            $searchAp = $_REQUEST ['searchAp'];
            if (isset ($searchAp)) {
                if ($searchAp == '全天') {
                    $this->assign('searchAp', $searchAp);
                } else {
                    $map ['ap'] = $searchAp;
                    $_SESSION ['searchAp' . $moduleName] = $searchAp;
                    $this->assign('searchAp', $searchAp);
                }
            } else {
                if (isset ($_SESSION ['searchAp' . $moduleName])) {
                    $map ['ap'] = $_SESSION ['searchAp' . $moduleName];
                    $this->assign('searchAp', $_SESSION ['searchAp' . $moduleName]);
                } else { // 如果没有指定上午或者下午，那取当前时间的上午和下午
                    $this->assign('searchAp', $this->getAp());
                }
            }
            $map ['domain'] =  $_SERVER['HTTP_HOST'];

            $name = 'orderform' . substr(date('Y-m-d'), 0, 4);

            // 取得表的连接信息
            import('COM.Db.SystemDb');
            $systemDb = new SystemDb ();
            $connectConfig = $systemDb->getHistoryDbConnection($name);


            $db_type = 'mysql';
            $db_user = trim($connectConfig ['db_user']);
            $db_pwd = trim($connectConfig ['db_pwd']);
            $db_host = trim($connectConfig ['db_host']);
            $db_port = trim($connectConfig ['db_port']);
            $db_name = trim($connectConfig ['db_name']);

            $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

            // 连接历史数据库
            $orderformModel = M("orderform", "rms_", $connectionDns);

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $dbNameTableName = 'rms_orderform';
            $total = $orderformModel->table("$dbNameTableName")->where($map)->count(); // 查询满足要求的总记录数

            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowSearchview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowSearchview'];
            }
            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset ($listMaxRows)) {
                $Page->listRows = $listMaxRows;
            } else {
                $listMaxRows = 15;
            }

            $Page = new Page ($total, $listMaxRows);
            $show = $Page->show();

            // 查询模块的数据
            //$selectFields = $listFields;
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);
            array_unshift($selectFields, 'ordersn');
            $listResult = $orderformModel->table("$dbNameTableName")->field($selectFields)->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select();


            $this->assign('moduleId', $moduleId);
            $this->assign('page', $show); // 赋值分页输出

            $searchOption = $focus->searchFields;
            $this->assign('searchOption', $searchOption);
            $this->assign('returnAction', 'searchview'); // 定义返回的方法

            $orderHistoryArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHistoryArray ['rows'] = $listResult;
            } else {
                $orderHistoryArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
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
                    'url' => U($moduleName . '/searchview', $searchArray),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                    'toolbar' => '#orderhistory-searchview-datagrid-toolbar',

                )
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid ['fields'] [$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width']
                );
            }
            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewModule.operate'
            );

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
        $this->returnMainFnPara();

        // 取得记录ID
        $record = $_REQUEST ['ordersn'];
        // 重新设定订单历史查询的数据库
        $startDate = $_REQUEST ['startDate'];

        $name = 'orderform' . substr(date('Y-m-d'), 0, 4);
        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb ();
        $connectConfig = $systemDb->getHistoryDbConnection($name);
        $db_type = 'mysql';
        $db_user = trim($connectConfig ['db_user']);
        $db_pwd = trim($connectConfig ['db_pwd']);
        $db_host = trim($connectConfig ['db_host']);
        $db_port = trim($connectConfig ['db_port']);
        $db_name = trim($connectConfig ['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 连接历史数据库
        $orderformModel = M("orderformhistory", "rms_", $connectionDns);

        // 重新设定订单历史查询的数据库
        $dbNameTableName = 'rms_orderform';

        // 返回模块的记录
        $result = $orderformModel->table("$dbNameTableName")->where("ordersn=$record")->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('blocks', $blocks);
        $this->assign('record', $record);

        // 返回从表的内容
        $this->get_slave_table($record);
        $this->display('OrderHistory/detailview');
    }

    // 返回从表的内容:产品
    public function get_slave_table($record)
    {

        $name = 'orderform' . substr(date('Y-m-d'), 0, 4);
        // 取得表的连接信息
        import('COM.Db.SystemDb');
        $systemDb = new SystemDb ();
        $connectConfig = $systemDb->getHistoryDbConnection($name);
        $db_type = 'mysql';
        $db_user = trim($connectConfig ['db_user']);
        $db_pwd = trim($connectConfig ['db_pwd']);
        $db_host = trim($connectConfig ['db_host']);
        $db_port = trim($connectConfig ['db_port']);
        $db_name = trim($connectConfig ['db_name']);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";


        // 设定日期
        $date = $_REQUEST ['startDate'];
        $dbNameTableName = 'rms_orderproducts';

        // 连接历史数据库
        $orderproductsModel = M("orderproducts", "rms_", $connectionDns);

        $orderproducts = $orderproductsModel->table($dbNameTableName)->field('orderformid,code,name,price,number,money')->where("ordersn=$record")->select();
        // echo $orderproductsModel->getLastSql();
        // dump($orderproducts);
        $this->assign('orderproducts', $orderproducts);
        // return $teladdress;

        // 取得订单日志
        $orderactionModel = M("orderaction", "rms_", $connectionDns);
        $dbNameTableName = 'rms_orderaction';
        $orderaction = $orderactionModel->table($dbNameTableName)->where("ordersn=$record")->select();
        $this->assign('orderaction', $orderaction);
    }
}

?>