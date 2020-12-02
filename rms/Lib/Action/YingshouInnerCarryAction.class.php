<?php
/**
 * Created by zhangwh
 * User: lihua
 * Date: 2019/12/09
 * Time: 15:43 PM
 * 营收内部转账管理
 */

class YingshouInnerCarryAction extends YingshouAction
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
            $startDate = $_REQUEST['startDate'];
            $endDate = $_REQUEST['endDate'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb($startDate);
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName . substr($startDate, 5, 2), " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = array(
                array('EGT', $startDate),
                array('ELT', $endDate),
                'and',
            ); //array('like', substr($getDate, 0, 7) . '%');
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

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("date desc")->select(); //lastdatetime desc,

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
            //当前日期

            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $this->getAp());
            $revparType = $this->getRevparType();
            if ($revparType == 'company') {
                $this->assign('createon', 'ok');
            } else {
                $this->assign('createon', 'off');
            }

            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    //生成分录底稿界面
    public function generalview()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        //当前日期
        $this->assign('cdate', date('Y-m-d'));
        $this->display($moduleName . '/generalview');
    }

    /*
     * 需要返回的字段
     */
    public function returnMainFnPara()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        $domain = $this->getDomain();
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午'),
        );
        $this->assign('ap', $ap);
        $this->assign('currentDate', date('Y-m-d'));
        $this->assign('currentAp', $this->getAp());

        //返回分公司
        $companymgrModel = D('companymgr');
        $where = array();
        //$where['name'] = array('neq', $company);
        $where['domain'] = $this->getDomain();
        $companyResult = $companymgrModel->where($where)->select();
        $this->assign('innercompany', $companyResult);

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
        $where['company'] = array(
            array('EQ',$company),
            array('EQ','总部'),
            'OR'
        );
        $where['domain'] = $this->getDomain();
        $paymentResult = $paymentmgrModel->field('name')->where($where)->find();
        if (empty($paymentResult)) {
            $this->ajaxReturn(null, 'JSON');
        }
        //显示客户的账户
        $where = array();
        $where['name'] = $paymentResult['name'];
        //$where['type'] = '营收';
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $accountsbillsmingxiResult = $accountsbillsmingxiModel->where($where)->limit(100)->select();
        $this->assign('incomemgraccounts', $accountsbillsmingxiResult);
        $accountsTotalMoney = $accountsbillsmingxiModel->where($where)->sum('money');
        $this->assign('accountsTotalMoney', $accountsTotalMoney);
        $paymentResult['accounts'] = $this->fetch($moduleName . '/accountbill');
        $this->ajaxReturn($paymentResult, 'JSON');
    }
     
    /** *转账**/   
    //根据客户代码，查询客户名称
    public function getInnerAccountsByCode()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();

        // 配送店（分公司）的信息
        $company = $_REQUEST['company'];

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
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $paymentResult = $paymentmgrModel->field('name')->where($where)->find();
        if (empty($paymentResult)) {
            $this->ajaxReturn(null, 'JSON');
        }
        //显示客户的账户
        $where = array();
        $where['name'] = $paymentResult['name'];
        //$where['type'] = '营收';
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
        $company = $_REQUEST['company'];
        if (empty($company)) {
            $company = '';
        }
        $this->assign('company', $company);
        $this->display('YingshouInnerCarry/selectpaymentmgrview');

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

    /**转账客户 */
    //弹出客户选择窗口
    public function popupInnerAccountsview()
    {
        $company = $_REQUEST['company'];
        if (empty($company)) {
            $company = '';
        }
        $this->assign('company', $company);
        $this->display('YingshouInnerCarry/selectinnerpaymentmgrview');
       
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        $data = array(
            array('code',
                $_REQUEST['paymentcode'],
            ),
            array(
                'company',
                $company,
            ),
            array(
                'domain',
                $this->getDomain(),
            ),
            array(
                'inneraccount',
                $_REQUEST['inneraccount'],
            ),
            array(
                'create_time',
                date('Y-m-d H:i:s'),
            ),
            array(
                'type',
                '转出',
            ),
        );
        return $data;
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
            array(
                'code',
                $_REQUEST['paymentcode'],
            ),

        );
        return $data;
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
        $startDate = $_REQUEST['startDate'];
        $endDate = $_REQUEST['endDate'];
        $moduleId = $focus->getPk();

        //连接字符串
        $connectionDb = $this->connectReveueDb($startDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($startDate, 5, 2), " ", $connectionDb);

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($startDate);
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

        //返回审核的数据
        $where = array();
        $where['date'] = array(
            array('EGT', $startDate),
            array('ELT', $endDate),
            'and',
        );
        $where['shenhe'] = 0;
        $where['domain'] = $domain;
        $innerResult = $Model->where($where)->select();

        if (!$innerResult) {
            $info['status'] = 1;
            $info['info'] = '没有审核数据';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        foreach ($innerResult as $key => $value) {
            if($value['shenhe'] == 1){
                continue;
            }
            //删除原来的数据
            $where = array();
            $where['ordersn'] = 'inner'.$value['innercarryid'];
            $where['company'] = $value['company'];
            $where['date'] = $value['date'];
            $where['domain'] = $domain;
            $accountsbillsmingxiModel->where($where)->delete();
            //保存数据到客户明细表中
            $data = array();
            $data['ordersn'] = 'inner'.$value['innercarryid'];
            $data['code'] = $value['innercode'];
            $data['name'] = $value['innername'];
            $data['type'] = '转入';
            $data['money'] = $value['money'];
            $data['note'] = $value['company'] . '转出到' . $value['innercompany'];
            $data['date'] = $value['date'];
            $data['company'] = $value['innercompany'];
            $data['domain'] = $value['domain'];
            $data['create_time'] = date('Y-m-d H:i:s');
            $accountsbillsmingxiModel->create();
            $accountsbillsmingxiModel->add($data);
            //同时反方向保存数据，证明原公司传出
            $data = array();
            $data['ordersn'] = 'inner'.$value['innercarryid'];
            $data['code'] = $value['code'];
            $data['name'] = $value['name'];
            $data['type'] = '转出';
            $data['money'] = -$value['money'];
            $data['note'] = $value['company'] . '转出到' . $value['innercompany'];
            $data['date'] = $value['date'];
            $data['company'] = $value['company'];
            $data['domain'] = $value['domain'];
            $data['create_time'] = date('Y-m-d H:i:s');
            $accountsbillsmingxiModel->create();
            $accountsbillsmingxiModel->add($data);

            //设置审核成功
            $where = array();
            $where['date'] = $value['date'];
            $where['innercarryid'] = $value['innercarryid'];
            $data = array();
            $data['shenhe'] = 1;            
            $result = $Model->where($where)->save($data);

            if (!$result) {
                $info['status'] = 0;
                $info['info'] = '审核失败';
                $this->ajaxReturn(json_encode($info), 'EVAL');
            }

        }

        $info['status'] = 1;
        $info['info'] = '审核成功';
        $this->ajaxReturn(json_encode($info), 'EVAL');
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
        $startDate = $_REQUEST['startDate'];
        $endDate = $_REQUEST['endDate'];
        $moduleId = $focus->getPk();

        //连接字符串
        $connectionDb = $this->connectReveueDb($startDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($startDate, 5, 2), " ", $connectionDb);

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($startDate);
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

        //返回审核的数据
        $where = array();
        $where['date'] = array(
            array('EGT', $startDate),
            array('ELT', $endDate),
            'and',
        );
        $where['shenhe'] = 1;
        $where['domain'] = $domain;
        $innerResult = $Model->where($where)->select();

        if (!$innerResult) {
            $info['status'] = 1;
            $info['info'] = '没有放弃审核数据';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        foreach ($innerResult as $ky => $value) {
            $where = array();
            $where['ordersn'] = 'inner'.$value['innercarryid'];
            $where['company'] = $value['innercompany'];
            $where['type'] = '转入';
            $where['date'] = $value['date'];
            $where['domain'] = $domain;
            $accountsbillsmingxiModel->where($where)->delete();

            $where = array();
            $where['ordersn'] = 'inner'.$value['innercarryid'];
            $where['company'] = $value['company'];
            $where['type'] = '转出';
            $where['date'] = $value['date'];
            $where['domain'] = $domain;
            $accountsbillsmingxiModel->where($where)->delete();

        }

        //恢复审核
        $where = array();
        $where['date'] = array(
            array('EGT', $startDate),
            array('ELT', $endDate),
            'and',
        );
        $where['domain'] = $domain;
        $data = array();
        $data['shenhe'] = 0;
        $result = $Model->where($where)->save($data);

        $info['status'] = 1;
        $info['info'] = '放弃审核成功';
        $this->ajaxReturn(json_encode($info), 'EVAL');

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
        $tableName = 'incomemgr_';
        // 连接数据库
        $incomemgrModel = M($tableName . substr($startDate, 5, 2), " ", $connectionDb);

        // 生成list字段列表
        $listFields = $focus->content_fields;

        // 模块的ID
        $moduleId = strtolower($focus->getPk());

        // 查询模块的数据
        foreach ($listFields as $key => $value) {
            $selectFields[] = $key;
        }

        // 建立查询条件
        $where = array();
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        $where['operation'] = array(array('NEQ', '调整单'));
        $where['shenhe'] = 0;
        $where['domain'] = $this->getDomain();

        //判断是否有没有审核的数据
        $incomemgrResult = $incomemgrModel->where($where)->find();
        if (!empty($incomemgrResult)) {
            $this->display($moduleName . '/error'); // 执行方法自身的列表
            return;
        }

        //获取核算项
        $where = array();
        $where['domain'] = $this->getDomain();
        $paymentmgrResult = $paymentmgrModel->where($where)->select();
        foreach ($paymentmgrResult as $value) {
            $paymentmgr[$value['name']] = $value['accounting'];
            $paymentmgr_subject[$value['name']] = $value['subject'];
        }

        //获取分公司鼎捷系统部门编码
        $company_department = array();
        $where = array();
        $where['domain'] = $domain;
        $companymgrResult = $companymgrModel->field('name,yingshoudepartment')->where($where)->select();
        foreach ($companymgrResult as $value) {
            $company_department[$value['name']] = $value['yingshoudepartment'];
        }

        //定义支付的科目
        $paytypeArr = array(
            '现金' => array('现金', '113311', ''),
            '支票' => array('银行存款', '100201', ''),
            '支付宝扫码' => array('微信扫码/支付宝扫码', '1131', '11046'),
            '微信扫码' => array('微信扫码/支付宝扫码', '1131', '11046'),
            '银联卡' => array('银联卡', '100904', ''),
        );

        $financeReulst = array();
        //返回查询的数据
        // 建立查询条件
        $where = array();
        $where['operation'] = array(array('eq', '还欠'), array('eq', '预收'), 'or');
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        $where['domain'] = $this->getDomain();
        $listResult = $incomemgrModel->where($where)->select();

        // 'summary', 'subject', 'subjectname', 'department', 'departname', 'debitcredit', 'money', 'checks',
        // '项次', '摘要', '科目编码', '科目名称', '部门编码', '部门名称', '1借2贷', '原币金额', '核算项',
        foreach ($listResult as $key => $value) {

            $financeReulst[] = array(
                'summary' => $value['name'] . '-' . $value['date'],
                'subject' => $paytypeArr[$value['paytype']][1],
                'subjectname' => $paytypeArr[$value['paytype']][0],
                'department' => $company_department[$value['company']],
                'departname' => $value['company'],
                'debitcredit' => '1',
                'money' => $value['money'],
                'checks' => $paytypeArr[$value['paytype']][2],
            );

            $financeReulst[] = array(
                'summary' => $value['name'] . '-' . $value['date'],
                'subject' => $paymentmgr_subject[$value['name']],
                'subjectname' => '应收账款',
                'department' => $company_department[$value['company']],
                'departname' => $value['company'],
                'debitcredit' => '2',
                'money' => $value['money'],
                'checks' => $paymentmgr[$value['name']],
            );
        }

        //导出excel
        $selectFields = array(
            '项次', '摘要', '科目编码', '科目名称', '部门编码', '部门名称', '1借2贷', '原币金额', '核算项',
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'J1')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'J1000')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('A2:' . 'J1000')->getFont()->setSize(12);

        $i = 1;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i - 1, 1, $value);
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($financeReulst as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i - 1);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                if ($colKey == 'department') {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $colValue, PHPExcel_Cell_DataType::TYPE_STRING);
                } elseif ($colKey == 'checks') {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $i, $colValue, PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $colValue);
                }

            }
            // 设置边框
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'J' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('分录底稿');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = $startDate . '到' . $endDate . '收入管理(还欠预付)';

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

    /**
     * 获取分公司支付方式
     */
    public function getCompanyPayment()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        $getCompany = $company;
        if($_REQUEST['company']){
            $getCompany = $_REQUEST['company'];
        }
        $domain = $this->getDomain();

        //如果有缓存，就直接返回缓存
        //$payment_cache = F('yingshouroomserver' . $company . $domain);
        if (!empty($payment_cache)) {
            $this->ajaxReturn($payment_cache, 'JSON');
        }

        $revparType = $this->getRevparType();

        $paymentmgrModel = D('paymentmgr');
        $where = array();
        if ($revparType == 'finance') {

            if (!empty($getCompany)) {
                if ($getCompany == '总部') {
                    $where['company'] = '总部';
                } else {
                    $where['company'] = $getCompany;
                }
            }

        } else {
            if (!empty($getCompany)) {
                $where['company'] = array(
                    array('EQ', $getCompany),
                    array('EQ', '总部'),
                    'or',
                );
            }

        }
        $where['domain'] = $this->getDomain();
        $paymentmgr = $paymentmgrModel->where($where)->select();
        //var_dump($paymentmgrModel->getLastSql());

        import('@.Extend.ChinesePinyin');
        $Pinyin = new ChinesePinyin();

        //定义返回全部payment，以便点击name的搜索code
        $payment_all = array();

        $companyArr = array();
        foreach ($paymentmgr as $value) {
            //搜索赋值
            $payment_all[$value['name']] = $value['code'];

            //$py = $this->getFirstCharter(trim($value['name']));
            $py = $Pinyin->TransformUcwords(trim($value['name']));
            $py = $py[0];
            if ($py == 'A') {
                $A[] = trim($value['name']);
            }

            if ($py == 'B') {
                $B[] = trim($value['name']);
            }

            if ($py == 'C') {
                $C[] = trim($value['name']);
            }

            if ($py == 'D') {
                $D[] = trim($value['name']);
            }

            if ($py == 'E') {
                $E[] = trim($value['name']);
            }

            if ($py == 'F') {
                $F[] = trim($value['name']);

            }

            if ($py == 'G') {
                $G[] = trim($value['name']);
            }

            if ($py == 'H') {
                $H[] = trim($value['name']);

            }

            if ($py == 'I') {
                $I[] = trim($value['name']);

            }

            if ($py == 'J') {
                $J[] = trim($value['name']);

            }

            if ($py == 'K') {
                $K[] = trim($value['name']);

            }

            if ($py == 'L') {
                $L[] = trim($value['name']);

            }

            if ($py == 'M') {
                $M[] = trim($value['name']);

            }

            if ($py == 'N') {
                $N[] = trim($value['name']);

            }

            if ($py == 'O') {
                $O[] = trim($value['name']);

            }

            if ($py == 'P') {
                $P[] = trim($value['name']);

            }

            if ($py == 'Q') {
                $Q[] = trim($value['name']);

            }

            if ($py == 'R') {
                $R[] = trim($value['name']);

            }

            if ($py == 'S') {
                $S[] = trim($value['name']);

            }

            if ($py == 'T') {
                $T[] = trim($value['name']);

            }

            if ($py == 'U') {
                $U[] = trim($value['name']);

            }

            if ($py == 'V') {
                $V[] = trim($value['name']);

            }

            if ($py == 'W') {
                $W[] = trim($value['name']);

            }

            if ($py == 'X') {
                $X[] = trim($value['name']);

            }

            if ($py == 'Y') {
                $Y[] = trim($value['name']);

            }

            if ($py == 'Z') {
                $Z[] = trim($value['name']);
            }

        }
        if (!empty($A)) {
            $A_arr = array(
                'key' => 'A',
                'data' => $A,
            );
            $companyArr[] = $A_arr;
        }

        if (!empty($B)) {
            $B_arr = array(
                'key' => 'B',
                'data' => $B,
            );
            $companyArr[] = $B_arr;
        }

        if (!empty($C)) {
            $C_arr = array(
                'key' => 'C',
                'data' => $C,
            );
            $companyArr[] = $C_arr;
        }

        if (!empty($D)) {
            $D_arr = array(
                'key' => 'D',
                'data' => $D,
            );
            $companyArr[] = $D_arr;
        }

        if (!empty($E)) {
            $E_arr = array(
                'key' => 'E',
                'data' => $E,
            );
            $companyArr[] = $E_arr;
        }

        if (!empty($F)) {
            $F_arr = array(
                'key' => 'F',
                'data' => $F,
            );
            $companyArr[] = $F_arr;
        }

        if (!empty($G)) {
            $G_arr = array(
                'key' => 'G',
                'data' => $G,
            );
            $companyArr[] = $G_arr;
        }

        if (!empty($H)) {
            $H_arr = array(
                'key' => 'H',
                'data' => $H,
            );
            $companyArr[] = $H_arr;
        }

        if (!empty($I)) {
            $I_arr = array(
                'key' => 'I',
                'data' => $I,
            );
            $companyArr[] = $I_arr;
        }

        if (!empty($J)) {
            $J_arr = array(
                'key' => 'J',
                'data' => $J,
            );
            $companyArr[] = $J_arr;
        }

        if (!empty($K)) {
            $K_arr = array(
                'key' => 'K',
                'data' => $K,
            );
            $companyArr[] = $K_arr;
        }

        if (!empty($L)) {
            $L_arr = array(
                'key' => 'L',
                'data' => $L,
            );
            $companyArr[] = $L_arr;
        }

        if (!empty($M)) {
            $M_arr = array(
                'key' => 'M',
                'data' => $M,
            );
            $companyArr[] = $M_arr;
        }

        if (!empty($N)) {
            $N_arr = array(
                'key' => 'N',
                'data' => $N,
            );
            $companyArr[] = $N_arr;
        }

        if (!empty($O)) {
            $O_arr = array(
                'key' => 'O',
                'data' => $O,
            );
            $companyArr[] = $O_arr;
        }

        if (!empty($P)) {
            $P_arr = array(
                'key' => 'P',
                'data' => $P,
            );
            $companyArr[] = $P_arr;
        }

        if (!empty($Q)) {
            $Q_arr = array(
                'key' => 'Q',
                'data' => $Q,
            );
            $companyArr[] = $Q_arr;
        }

        if (!empty($R)) {
            $R_arr = array(
                'key' => 'R',
                'data' => $R,
            );
            $companyArr[] = $R_arr;
        }

        if (!empty($S)) {
            $S_arr = array(
                'key' => 'S',
                'data' => $S,
            );
            $companyArr[] = $S_arr;
        }

        if (!empty($T)) {
            $T_arr = array(
                'key' => 'T',
                'data' => $T,
            );
            $companyArr[] = $T_arr;
        }

        if (!empty($U)) {
            $U_arr = array(
                'key' => 'U',
                'data' => $U,
            );
            $companyArr[] = $U_arr;
        }

        if (!empty($V)) {
            $V_arr = array(
                'key' => 'V',
                'data' => $V,
            );
            $companyArr[] = $V_arr;
        }

        if (!empty($W)) {
            $W_arr = array(
                'key' => 'W',
                'data' => $W,
            );
            $companyArr[] = $W_arr;
        }

        if (!empty($X)) {
            $X_arr = array(
                'key' => 'X',
                'data' => $X,
            );
            $companyArr[] = $X_arr;
        }

        if (!empty($Y)) {
            $Y_arr = array(
                'key' => 'Y',
                'data' => $Y,
            );
            $companyArr[] = $Y_arr;
        }

        if (!empty($Z)) {
            $Z_arr = array(
                'key' => 'Z',
                'data' => $Z,
            );
            $companyArr[] = $Z_arr;
        }

        $returnArr['city'] = $companyArr;

        /**
         * 获取总部
         */
        $companyArr = array();
        $where = array();
        if ($revparType == 'finance') {
            $where['company'] = '总部';

        } else {
            $where['company'] = '总部';
        }
        $where['domain'] = $this->getDomain();

        $paymentmgr = $paymentmgrModel->where($where)->select();

        foreach ($paymentmgr as $value) {
            //搜索赋值
            $payment_all[$value['name']] = $value['code'];

            $companyArr[] = trim($value['name']);
        }
        $returnArr['area'] = $companyArr;
        $returnArr['findcode'] = $payment_all;

        F('yingshouroomserver' . $company . $domain, $returnArr);

        $this->ajaxReturn($returnArr, 'JSON');
    }

}
