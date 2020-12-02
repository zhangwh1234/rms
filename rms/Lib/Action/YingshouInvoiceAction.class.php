<?php
/**
 *
 */

class YingshouInvoiceAction extends YingshouAction
{
    // listview 的第二种，没有午别
    public function listview()
    {
        $this->listviewTwo();
    }

    // listview 的第二种，没有午别
    public function listviewTwo()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            //数据权限检查
            $revparType = $this->getRevparType();

            // 启动当前模块的模型
            $focus = D($moduleName);

            //结账日期
            $getDate = $_REQUEST['getDate'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb('');
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName, " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = array('like', substr($getDate, 0, 7) . '%');
            $where['domain'] = $this->getDomain();

            if ($revparType == 'finance') {

            }
            if ($revparType == 'company') {
                $where['company'] = $company;
            }

            $total = $Model->where($where)->count(); // 查询满足要求的总记录数

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
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            //加入其它字段
            foreach ($focus->otherListFields as $otherFields) {
                array_unshift($selectFields, $otherFields);
            }

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray = array();

            //数据权限检查
            foreach ($listResult as $value) {
                $value['revparType'] = $revparType;
                $orderHandleArray[] = $value;
            }

            //$orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                //$orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $Model->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION['searchText' . $moduleName]);
                unset($_SESSION[$moduleName . 'page']);
            }

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            if (empty($_REQUEST['getDate'])) {
                $getDate = date('Y-m-d');
            } else {
                $getDate = $_REQUEST['getDate'];
            }
            $param = array(
                'getDate' => $getDate,
            );
            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $param),
                    'pageNumber' => $pageNumber,
                    'pageSize' => 10,
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

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'ListviewModule.sendname',
                    );
                } else {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                    );
                }

            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $revparType = $this->getRevparType();
            $this->assign('revparType', $revparType);
            $this->assign('returnAction', 'listview');
            if ($revparType == 'company') {
                $this->assign('createon', 'ok');
            } else {
                $this->assign('createon', 'off');
            }

            //当前日期
            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $this->getAp());

            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    // 编辑数据的页面editview
    public function editview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        $getDate = $_REQUEST['getDate'];

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where = array();
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where($where)->find();

        // 返回区块
        $blocks = $focus->editBlocks($result);

        $this->assign('info', $result);
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点
        $this->assign('record', $record); // 订单记录号
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('blocks', $blocks);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara();

        $this->assign('getDate', $getDate);
        // 返回从表的内容
        $this->get_slave_table($record, $getDate);

        $this->display($moduleName . '/editview');

    }

    // 查看数据的页面
    public function detailview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where($where)->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table($record, $getDate);
        $this->display($moduleName . '/detailview');
    }

    /*
     * 需要返回的字段
     */
    public function returnMainFnPara()
    {
        $domain = $this->getDomain();
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午'),
        );
        $this->assign('ap', $ap);
        $this->assign('currentDate', date('Y-m-d'));
        $this->assign('currentAp', $this->getAp());

        $type = array(
            array('name' => '餐费'),
            array('name' => '盒饭'),
            array('name' => '快餐'),
        );
        $this->assign('body', $type);

        $taxtype = array(
            array('name' => '6%'),
            array('name' => '3%'),
            array('name' => '13%'),
        );
        $this->assign('tax', $taxtype);

    }

    //根据客户代码，查询客户名称
    public function getAccountsByCode()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();

        // 配送店（分公司）的信息
        $company = $this->userInfo['department'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        if (empty($getDate)) {
            $getDate = $currentDate;
            $getAp = $currentAp;
        }

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($currentDate);
        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                break;
        }

        //查询客户，显示客户
        $code = $_REQUEST['code'];
        $paymentmgrModel = D('PaymentMgr');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $paymentResult = $paymentmgrModel->field('name,invoiceheader')->where($where)->find();
        if (empty($paymentResult)) {
            $this->ajaxReturn(null, 'JSON');
        }
        //显示客户的账户
        $where = array();
        $where['name'] = $paymentResult['name'];
        $where['type'] = '营收';
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $accountsbillsmingxiResult = $accountsbillsmingxiModel->where($where)->limit(100)->select();
        $this->assign('incomemgraccounts', $accountsbillsmingxiResult);
        $accountsTotalMoney = $accountsbillsmingxiModel->where($where)->sum('money');
        $this->assign('accountsTotalMoney', $accountsTotalMoney);
        $paymentResult['accounts'] = $this->fetch($moduleName . '/accountbill');
        $this->ajaxReturn($paymentResult, 'JSON');
    }

    //弹出客户选择窗口
    public function popupAccountsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            // 取得模块的名称
            $popupModuleName = 'paymentmgr';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupPaymentMgrFields;

            // 模块的ID
            $moduleId = 'paymentmgrid';
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            $where['domain'] = $this->getDomain();
            //获取类型
            $revparType = $this->getRevparType();
            if ($revparType == 'company') {
                $where[] = " company = '$company' or company='总部' ";
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $popupModule->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order(" code asc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult, 'sql' => $popupModule->getLastSql());

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'PaymentMgr';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupPaymentMgrFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupAccountsview'),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true,
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
                'formatter' => 'YingshouIncomeMgrPopupAccountsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouIncomeMgr/popupAccountsview');
        }
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $data = array(
            array(
                'company',
                $company,
            ),
            array(
                'domain',
                $this->getDomain(),
            ),
            array(
                'create_time',
                date('Y-m-d H:i:s'),
            ),
        );
        return $data;

    }

    /* 一般顺序表记录的保存 */
    public function insert()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        $getDate = $_REQUEST['date'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaInsert();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $result = $Model->create();

        if (!$result) {
            exit($Model->getError());
        }
        $result = $Model->add();

        if (!$result) {
            $info['status'] = 0;
            $info['info'] = '保存数据不成功！';
            $info['sql'] = $Model->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->save_slave_table($record, $getDate);

        // 如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'getDate' => $getDate,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        $data = array(
            array(
                'domain',
                $_SERVER['HTTP_HOST'],
            ),
            array(
                'update_time',
                date('Y-m-d H:i:s'),
            ),
        );
        return $data;

    }

    // 更新记录
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);
        // 返回的页面
        $returnAction = $_REQUEST['returnAction'];

        // 取得记录号
        $record = $_REQUEST['record'];
        $moduleId = $focus->getPk();

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $Model->create();

        $where = array();
        $where[$moduleId] = $record;
        $result = $Model->where($where)->save();

        // 新写的保存从表方案
        $slaveResult = $this->update_slave_table($record, $getDate);

        $return['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'rowIndex' => $_REQUEST['rowIndex'], 'getDate' => $getDate,
        ));

        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $detailviewUrl;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /* 删除记录 */
    public function delete()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['record'];
        $getDate = $_REQUEST['getDate'];

        $moduleId = $focus->getPk();

        $where[$moduleId] = $record;

        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        // 删除记录
        $result = $Model->where($where)->delete();

        //删除从表
        $this->delete_slave_table($record, $getDate);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '删除成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '删除失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }
    }

    /**
     * 审核单据
     */
    public function audit()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $getDate = $_REQUEST['getDate'];
        $moduleId = $focus->getPk();

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($getDate);
        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                break;
        }

        // 删除记录
        $where = array();
        $where['date'] = $getDate;
        $data = array();
        $data['shenhe'] = 1;
        $result = $Model->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '审核成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '审核失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }

    /**
     * 恢复审核
     */
    public function recover()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);
        $domain = $this->getDomain();

        // 取得保存的主键
        $getDate = $_REQUEST['getDate'];

        $moduleId = $focus->getPk();

        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName, " ", $connectionDb);

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($getDate);
        $domain = $this->getDomain();

        //恢复审核
        $where = array();
        $where['date'] = $getDate;
        $data = array();
        $data['shenhe'] = 0;
        $result = $Model->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '审核成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '审核失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }

    /**
     *  导出excel分录底稿
     */
    public function outputExcel()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);
        $domain = $this->getDomain();

        //结账日期
        $startDate = $_REQUEST['startDate'];
        $endDate = $_REQUEST['endDate'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        if (empty($getDate)) {
            $getDate = $currentDate;
            $getAp = $currentAp;
        }

        $companymgrModel = D('companymgr');
        $paymentmgrModel = D('paymentmgr');

        //连接字符串
        $connectionDb = $this->connectReveueDb($startDate);
        //连接的数据表
        $tableName = 'yingshouinvoice';
        // 连接数据库
        $invoiceModel = M($tableName, " ", $connectionDb);

        // 生成list字段列表
        $listFields = array('date', 'number', 'header', 'body', 'notaxmoney', 'tax', 'taxmoney', 'money', 'money as shoumoney', 'paytype', 'company');

        // 模块的ID
        $moduleId = strtolower($focus->getPk());

        // 建立查询条件
        $where = array();
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        $where['shenhe'] = 0;
        $where['domain'] = $this->getDomain();

        //判断是否有没有审核的数据
        $invoiceResult = $invoiceModel->where($where)->find();
        if (!empty($incomemgrResult)) {
            $this->display($moduleName . '/error'); // 执行方法自身的列表
            return;
        }

        // 建立查询条件
        $where = array();
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        //$where['shenhe'] = 1;
        $where['domain'] = $this->getDomain();
        //判断是否有没有审核的数据
        $invoiceResult = $invoiceModel->field($listFields)->where($where)->select();
       

        //导出excel
        $selectFields = array(
            '日期', '发票号码', '公司名称', '开票内容', '未税金额', '税率', '税额', '价税合计', '收款金额',
            '收款方式', '分公司', '备注',
        );
        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();
        //设置文本格式
        $objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        // 设置高度，大小
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'L1')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'L1000')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('A2:' . 'L1000')->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'L1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $i = 1;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i - 1, 1, $value);
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($invoiceResult as $tongjiKey => $tongjiValue) {
            $i = $i+1;
            $l = 0;
            //$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i - 1);
            foreach ($tongjiValue as $colKey => $colValue) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $colValue);
                $l = $l + 1;
            }
            // 设置边框
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'L' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('发票登记');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = $startDate . '到' . $endDate . '发票登记情况';

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();

    }

}
