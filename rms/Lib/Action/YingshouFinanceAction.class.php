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
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

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
            $connectionDb = $this->connectReveueDb($getDate);
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = array('ELT', $getDate);
            $where['domain'] = $this->getDomain();

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

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
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

            $this->assign('returnAction', 'listview');
            //当前日期

            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $this->getAp());
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }

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
        $startDate = $_REQUEST['start_date'];
        $endDate = $_REQUEST['end_date'];
        $financeDate = $startDate;

        //检查日期是否相同月
        $startMonth = substr($startDate, 5, 2);
        $endMonth = substr($endDate, 5, 2);
        if ($startMonth !== $endMonth) {
            $data = array();
            $data['result'] = '分录底稿月份需要相同月';
            $data['datetime'] = date('Y-m-d H:i:s');
            $data['domain'] = $this->getDomain();
            $financeresultModel->create();
            $financeresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        $domain = $this->getDomain();

        $reveueConnectDb = $this->connectReveueDb($financeDate);

        // 连接数据库
        $revparmgrModel = M("revparmgr_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financeModel = M("finance_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financecontentModel = M("financecontent_" . substr($financeDate, 5, 2), " ", $reveueConnectDb);
        $financeresultModel = M("financeresult", " ", $reveueConnectDb);
        $paymentmgrModel = D('paymentmgr');
        $companymgrModel = D('companymgr');

        /**
         * 从revparmgr结账表中汇总，产生分录底稿
         * 首先要检查是否存在总公司的结账数据
         * array(array('EGT', $startDate), array('ELT', $endDate));
         *
         */
        //计算相差天生
        $days = round((strtotime($endDate) - strtotime($startDate)) / 3600 / 24);

        for ($i = 0; $i <= $days; $i++) {
            $checkDate = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($startDate)));

            $where = array();
            $where['date'] = $checkDate;
            $where['company'] = '总部';
            $where['domain'] = $this->getDomain();
            $revparmgrResult = $revparmgrModel->where($where)->select();

            $financeresultModel->where(1)->delete();
            //如果没有结账数据,抛出错误
            if (empty($revparmgrResult)) {
                $data = array();
                $data['result'] = $checkDate . ' 没有结账数据';
                $data['datetime'] = date('Y-m-d H:i:s');
                //$data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $financeresultModel->create();
                $financeresultModel->add($data);
                $res = array();
                $res['state'] = 0;
                $this->ajaxReturn($res);
            }
        }

        //检查科目代码是否有空？有空，就跳出提示
        $checkPayment = false;
        //根据名称查账科目代码
        $where = array();
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        $where['company'] = array('NEQ', '总部');
        $where['domain'] = $this->getDomain();
        $where['type'] = array('eq', 'M2');
        $revparmgrResult = $revparmgrModel->distinct(true)->field('name')->where($where)->select();
        foreach ($revparmgrResult as $value) {
            if ($value['name'] == '外送收入') {continue;}
            if ($value['name'] == '送餐费') {continue;}
            if ($value['name'] == '门市金额') {continue;}
            if ($value['name'] == '工作餐金额') {continue;}
            //开始查询支付表，查看是否有科目
            $where = array();
            $where['name'] = $value['name'];
            $where['domain'] = $this->getDomain();
            $paymentmgrResult = $paymentmgrModel->where($where)->find();
            if (empty($paymentmgrResult)) {
                $data = array();
                $data['result'] = $value['name'] . ': 支付名称没有设置，没法生成分录';
                $data['datetime'] = date('Y-m-d H:i:s');
                //$data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $financeresultModel->create();
                $financeresultModel->add($data);
                $checkPayment = true;
            }
            if (empty($paymentmgrResult['subject'])) {
                $data = array();
                $data['result'] = $paymentmgrResult['code'] . ' | ' . $value['name'] . '科目没有设置，没法生成分录';
                $data['datetime'] = date('Y-m-d H:i:s');
                //$data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $financeresultModel->create();
                $financeresultModel->add($data);
                $checkPayment = true;
            }
        }

        //如果有问题，集中显示
        if ($checkPayment) {
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        $where = array();
        $where['name'] = $value['name'];
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $paymentmgrResult = $paymentmgrModel->where($where)->select();

        //循环次数
        $companyNumber = 4;
        //获取所有分公司
        $where = array();
        $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
        $where['company'] = array('NEQ', '总部');
        $where['domain'] = $this->getDomain();
        $companyResult = $revparmgrModel->distinct(true)->field('company')->where($where)->select();
        $l = 0;
        $companyConection = '';
        $financeTotalMoney = 0;
        $i = 1;
        foreach ($companyResult as $company_value) {
            $company = $company_value['company'];
            //获取分公司鼎捷系统部门编码
            $where = array();
            $where['name'] = $company;
            $where['domain'] = $domain;
            $companymgrResult = $companymgrModel->field('yingshoudepartment')->where($where)->find();
            $company_department = $companymgrResult['yingshoudepartment'];
            //主表
            //营业收入
            $where = array();
            $where['date'] = $financeDate;
            $where['type'] = 'M1';
            $where['company'] = $company;
            $where['domain'] = $domain;
            $revparmgrResult = $revparmgrModel->where($where)->sum('money');
            $financeTotalMoney += $revparmgrResult;

            //保存主表操作
            if (empty($financeid)) {
                $data = array();
                $data['summary'] = $startDate . '到' . $endDate . '营收分录';
                $data['company'] = $company;
                $data['money'] = $financeTotalMoney;
                $data['date'] = $financeDate;
                $data['domain'] = $this->getDomain();
                $data['create_time'] = date('Y-m-d H:i:s');
                $financeModel->create();
                $financeid = $financeModel->add($data);
                $companyConection = $company;
                $l = $l + 1;
            } else {
                if ($l == $companyNumber) {
                    $data = array();
                    $data['summary'] = $startDate . '到' . $endDate . '营收分录';
                    $data['company'] = $company;
                    $data['money'] = $financeTotalMoney;
                    $data['date'] = $financeDate;
                    $data['domain'] = $this->getDomain();
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $financeModel->create();
                    $financeid = $financeModel->add($data);
                    $companyConection = $company;
                    $financeTotalMoney = 0;
                    $l = 1;
                    $i = 1;
                } else {
                    $companyConection = $companyConection . ' ' . $company;
                    $where = array();
                    $where['financeid'] = $financeid;
                    $data = array();
                    $data['company'] = $companyConection;
                    $data['money'] = $financeTotalMoney;
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $data['domain'] = $this->getDomain();
                    $financeModel->where($where)->save($data);
                    $l = $l + 1;
                }
            }

            //$sql = $financeModel->getLastSql();

            //将分录底稿的内容存入表中
            $where = array();
            $where['date'] = array(array('EGT', $startDate), array('ELT', $endDate));
            $where['company'] = $company;
            $where['domain'] = $domain;
            $where['type'] = array(
                array('eq', 'M1'),
                array('eq', 'M2'),
                'or',
            );
            $fields = array(
                'name',
                'sum(money) as money',
            );
            $revparmgrResult = $revparmgrModel->field($fields)->where($where)->group('name')->order('row')->select();
            //var_dump($revparmgrModel->getLastSql());

            foreach ($revparmgrResult as $value) {
                if ($value['name'] == '外送收入') {
                    $data = array();
                    $data['financeid'] = $financeid;
                    $data['journalorder'] = $i; //项次
                    $data['summary'] = $company . $startDate . '到' . $endDate . '营收'; //摘要
                    $data['subject'] = '510101'; //科目
                    $data['subjectname'] = '外送收入'; //科目名称
                    $data['department'] = $company_department; //部门
                    $data['debitcredit'] = '2'; //借贷
                    $data['money'] = $value['money']; //原币金额
                    $data['check'] = ''; //核算项
                    $data['checkname'] = ''; //核算项名称
                    $financecontentModel->create();
                    $financecontentModel->add($data);
                    $sql = $financecontentModel->getLastSql();
                    $i = $i + 1;
                    continue;
                }
                if ($value['name'] == '送餐费') {
                    $data = array();
                    $data['financeid'] = $financeid;
                    $data['journalorder'] = $i; //项次
                    if ($domain == 'wx.lihuaerp.com') {
                        $data['summary'] = $company . $startDate . '到' . $endDate . '营收'; //摘要

                    } else {
                        $data['summary'] = '团定餐'; //摘要
                    }
                    $data['subject'] = '510103'; //科目
                    $data['subjectname'] = '外送附加费'; //科目名称
                    $data['department'] = $company_department; //部门
                    $data['debitcredit'] = '2'; //借贷
                    $data['money'] = $value['money']; //原币金额
                    $data['check'] = ''; //核算项
                    $data['checkname'] = ''; //核算项名称
                    $financecontentModel->create();
                    $financecontentModel->add($data);
                    $sql = $financecontentModel->getLastSql();
                    $i = $i + 1;
                    continue;
                }
                if ($value['name'] == '门市金额') {
                    $data = array();
                    $data['financeid'] = $financeid;
                    $data['journalorder'] = $i; //项次
                    $data['summary'] = $company . $startDate . '到' . $endDate . '营收'; //摘要
                    $data['subject'] = '510104'; //科目
                    $data['subjectname'] = '门市'; //科目名称
                    $data['department'] = $company_department; //部门
                    $data['debitcredit'] = '2'; //借贷
                    $data['money'] = $value['money']; //原币金额
                    $data['check'] = ''; //核算项
                    $data['checkname'] = ''; //核算项名称
                    $financecontentModel->create();
                    $financecontentModel->add($data);
                    $sql = $financecontentModel->getLastSql();
                    $i = $i + 1;
                    continue;
                }
                if ($value['name'] == '工作餐金额') {
                    $data = array();
                    $data['financeid'] = $financeid;
                    $data['journalorder'] = $i; //项次
                    $data['summary'] = $company . $startDate . '到' . $endDate . '营收'; //摘要
                    $data['subject'] = '510102'; //科目
                    $data['subjectname'] = '工作餐'; //科目名称
                    $data['department'] = $company_department; //部门
                    $data['debitcredit'] = '2'; //借贷
                    $data['money'] = $value['money']; //原币金额
                    $data['check'] = ''; //核算项
                    $data['checkname'] = ''; //核算项名称
                    $financecontentModel->create();
                    $financecontentModel->add($data);
                    $sql = $financecontentModel->getLastSql();
                    $i = $i + 1;
                    continue;
                }

                $data = array();
                $data['financeid'] = $financeid;
                $data['journalorder'] = $i; //项次
                $data['summary'] = $company . $startDate . '到' . $endDate . '营收'; //摘要
                //根据名称查账科目代码
                $where = array();
                $where['name'] = $value['name'];
                //$where['company'] = $company;
                $where['domain'] = $domain;
                $paymentmgrResult = $paymentmgrModel->where($where)->find();
                if ($paymentmgrResult) {
                    $data['subject'] = $paymentmgrResult['subject']; //科目
                    $data['subjectname'] = $value['name']; //科目名称
                    $data['department'] = $company_department; //部门
                    $data['debitcredit'] = '1'; //借贷
                    $data['money'] = $value['money']; //原币金额
                    $data['checks'] = $paymentmgrResult['accounting']; //核算项
                    $data['checkname'] = ''; //核算项名称
                    $financecontentModel->create();
                    $financecontentModel->add($data);
                    $sql = $financecontentModel->getLastSql();
                    $i = $i + 1;
                }
            }
        }

        $res = array();
        $res['state'] = 1;
        $this->ajaxReturn($res);
    }

    /**
     * 查看单个分公司的分录底稿
     */
    public function detailview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            //结账日期
            $getDate = $_REQUEST['getDate'];
            $financeid = $_REQUEST['record'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb($getDate);

            // 连接数据库
            $financeModel = M("finance_" . substr($getDate, 5, 2), " ", $connectionDb);
            $financecontentModel = M("financecontent_" . substr($getDate, 5, 2), " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->content_fields;

            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            $company = $_REQUEST['company'];
            $domain = $this->getDomain();

            // 建立查询条件
            $where = array();
            $where['financeid'] = $financeid;
            $where['domain'] = $this->getDomain();

            $total = $financecontentModel->where($where)->count(); // 查询满足要求的总记录数

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

            $listResult = $financecontentModel->where($where)->field($selectFields)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray);
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
            $listFields = $focus->content_fields;

            // 模块的ID
            //$moduleId = $focus->getPk();

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $param = array(
                'getDate' => $_REQUEST['getDate'],
                'record' => $_REQUEST['record'],
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/detailview', $param),
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
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期
            $this->assign('getDate', $_REQUEST['getDate']);
            $this->display($moduleName . '/detailview'); // 执行方法自身的列表
        }

    }

    /**
     *   删除分录
     */
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

        $where = array();
        $where['financeid'] = $record;
        $where['domain'] = $this->getDomain();

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);

        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $financeModel = M('finance_' . substr($getDate, 5, 2), " ", $connectionDb);
        $financecontentModel = M('financecontent_' . substr($getDate, 5, 2), " ", $connectionDb);

        // 删除记录
        $result = $financeModel->where($where)->delete();
        $result = $financecontentModel->where($where)->delete();

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
     * 查看全部分录底稿
     */
    public function seeDetailview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            //结账日期
            $getDate = $_REQUEST['getDate'];
            $company = $_REQUEST['company'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb($getDate);
            //连接的数据表
            $tableName = 'financecontent_';
            // 连接数据库
            $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

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
            $where['date'] = $getDate;
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();

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

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
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
            $listFields = $focus->content_fields;

            // 模块的ID
            //$moduleId = $focus->getPk();

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $param = array(
                'getDate' => $_REQUEST['getDate'],
                'company' => $_REQUEST['company'],
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/detailview', $param),
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
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期
            $this->assign('getDate', date('Y-m-d'));
            $this->display($moduleName . '/detailview'); // 执行方法自身的列表
        }

    }

    /**
     * 导出分录底稿
     */
    public function outputExcel()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //结账日期
        $getDate = $_REQUEST['getDate'];
        //id
        $financeid = $_REQUEST['record'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        if (empty($getDate)) {
            $getDate = $currentDate;
            $getAp = $currentAp;
        }

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $financeModel = M('finance_' . substr($getDate, 5, 2), " ", $connectionDb);
        $tableName = 'financecontent_';
        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 模块的ID
        $moduleId = strtolower($focus->getPk());

        $selectFields = array(
            'summary', 'subject', 'subjectname', 'department', 'departname', 'debitcredit', 'money', 'checks',
        );
        
        

        // 建立查询条件
        $where = array();
        $where['financeid'] = $financeid;
        $where['domain'] = $this->getDomain();
        $financeResult = $financeModel->where($where)->find();

        $departmentResult = $Model->field('department')->distinct(true)->where($where)->select();

        $listResult = array();
        foreach ($departmentResult as $value) {
            $where = array();
            $where['financeid'] = $financeid;
            $where['domain'] = $this->getDomain();
            $where['department'] = $value['department'];
            
            $financecontentResult = $Model->where($where)->field($selectFields)->order("debitcredit,subject")->select(); //lastdatetime desc,
           
            $listResult = array_merge($listResult ,$financecontentResult);        
        }

        //$listResult = $Model->where($where)->field($selectFields)->order("financecontentid")->select(); //lastdatetime desc,

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
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
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

        foreach ($listResult as $tongjiKey => $tongjiValue) {
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

        $filename = $financeResult['company'] . $financeResult['summary'];

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
     * 生成报数单错误,产生的结果,显示一下
     */
    public function resultview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 重新设定订单历史查询的数据库
        $getdate = $_REQUEST['getdate'];

        $connectionDb = $this->connectReveueDb($getdate);

        // 连接数据库
        $revparmgrresultModel = M("financeresult", " ", $connectionDb);

        $where = array();
        $where['domain'] = $this->getDomain();
        // 返回模块的记录
        $revparmgrresult = $revparmgrresultModel->where($where)->select();

        $result = '';
        foreach ($revparmgrresult as $key => $value) {
            $index = $key + 1;
            $result .= "<p>(" . $index . ')' . $value['result'] . "</p>";
        }

        $this->assign('result', $result);
        $this->display($moduleName . '/resultview');
    }

}
