<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/10
 * Time: 下午5:10
 * 营收结账系统
 */

class YingshouRevparMgrAction extends YingshouAction
{

    // listview
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
            //结账午别
            $getAp = $_REQUEST['getAp'];
            //分公司
            $companyStr = $_REQUEST['companyArr'];
            $companyTmp = explode(',', $companyStr);
            $companyArr = array();
            foreach ($companyTmp as $value) {
                if (!empty($value)) {
                    $companyArr[] = $value;
                }
            }

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate) || empty($getAp)) {
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

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            $revparType = $this->getRevparType();
            // 建立查询条件
            $where = array();
            $where['date'] = $getDate;
            if ($getAp == '全天') {
            } else {
                $where['ap'] = $getAp;
            }
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            if ($revparType == 'finance') {
                foreach ($companyArr as $value) {
                    $where['company'][] = array('eq', $value);
                }
                $where['company'][] = 'OR';
            }
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

            //单独显示销售额
            $where = array();
            $where['date'] = $getDate;
            if ($getAp == '全天') {
            } else {
                $where['ap'] = $getAp;
            }
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            if ($revparType == 'finance') {
                foreach ($companyArr as $value) {
                    $where['company'][] = array('eq', $value);
                }
                $where['company'][] = 'OR';
            }
            $where['name'] = '销售总额';
            $where['domain'] = $this->getDomain();
            if ($revparType == 'company') {
                $selectFields = array(
                    'name',
                    'sum(money) as money',
                    'date',
                    'company',
                );
            }
            if ($revparType == 'finance') {
                if (count($companyArr) == 1) {
                    $companyOne = $companyArr[0];
                    $selectFields = array(
                        'name',
                        'sum(money) as money',
                        'date',
                        "'$companyOne' as company",
                    );

                } else {
                    $selectFields = array(
                        'name',
                        'sum(money) as money',
                        'date',
                        "'$companyStr' as company",
                    );
                }
            }

            $revparmgrResult = $Model->where($where)->field($selectFields)->group('name')->select();
            foreach ($revparmgrResult as $value) {
                $listResult_totalmoney[] = array(
                    'name' => $value['name'],
                    'money' => $value['money'],
                    'date' => $value['date'],
                    'ap' => $getAp,
                    'company' => $value['company'],
                );
            }

            //查询促销金额
            $where = array();
            $where['date'] = $getDate;
            if ($getAp == '全天') {
            } else {
                $where['ap'] = $getAp;
            }
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            if ($revparType == 'finance') {
                foreach ($companyArr as $value) {
                    $where['company'][] = array('eq', $value);
                }
                $where['company'][] = 'OR';
            }
            $where['name'] = '促销额';
            $where['domain'] = $this->getDomain();
            $revparmgrResult = $Model->where($where)->field($selectFields)->group('name')->select();
            $listResult_promotionmoney = array();
            foreach ($revparmgrResult as $value) {
                $listResult_promotionmoney[] = array(
                    'name' => $value['name'],
                    'money' => $value['money'],
                    'date' => $value['date'],
                    'ap' => $getAp,
                    'company' => $value['company'],
                );
            }
            $listResult_promotionmoney[] = array(
                'name' => '',
                'money' => '',
                'date' => '营收情况',
            );

            /********************************/

            // 建立查询条件
            // 查询外送收入等
            $where = array();
            $where['date'] = $getDate;
            if ($getAp == '全天') {
            } else {
                $where['ap'] = $getAp;
            }
            $where['type'] = 'M1';
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            if ($revparType == 'finance') {
                foreach ($companyArr as $value) {
                    $where['company'][] = array('eq', $value);
                }
                $where['company'][] = 'OR';
            }
            $where['domain'] = $this->getDomain();

            if ($revparType == 'company') {
                $selectFields = array(
                    'name',
                    'sum(money) as money',
                    'date',
                    'company',
                );
            }
            if ($revparType == 'finance') {
                if (count($companyArr) == 1) {
                    $companyOne = $companyArr[0];
                    $selectFields = array(
                        'name',
                        'sum(money) as money',
                        'date',
                        "'$companyOne' as company",
                    );

                } else {
                    $selectFields = array(
                        'name',
                        'sum(money) as money',
                        'date',
                        "'$companyStr' as company",
                    );
                }
            }

            $revparmgrResult = $Model->where($where)->field($selectFields)->group('name')->select(); //lastdatetime desc,
            foreach ($revparmgrResult as $value) {
                $listResult_debit[] = array(
                    'name' => $value['name'],
                    'money' => $value['money'],
                    'date' => $value['date'],
                    'ap' => $getAp,
                    'company' => $value['company'],
                );
            }
            if (!empty($revparmgrResult)) {
                $debit_money = $Model->where($where)->field($selectFields)->order("$moduleId asc")->sum('money'); //lastdatetime desc,
                $listResult_debit[] = array(
                    'name' => '',
                    'money' => $debit_money,
                    'date' => '贷方合计营收',
                    'ap' => '',
                );
            } else {
                $listResult_debit = array();
            }

            // 建立查询条件
            // 查询支付等
            $where = array();
            $where['date'] = $getDate;
            if ($getAp == '全天') {
            } else {
                $where['ap'] = $getAp;
            }
            $where['type'] = 'M2';
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            if ($revparType == 'finance') {
                foreach ($companyArr as $value) {
                    $where['company'][] = array('eq', $value);
                }
                $where['company'][] = 'OR';
            }
            $where['domain'] = $this->getDomain();

            $revparmgrResult = $Model->where($where)->field($selectFields)->group('name')->select(); //lastdatetime desc,
            foreach ($revparmgrResult as $value) {
                $listResult_credit[] = array(
                    'name' => $value['name'],
                    'money' => $value['money'],
                    'date' => $value['date'],
                    'ap' => $getAp,
                    'company' => $value['company'],
                );
            }
            if (!empty($listResult_credit)) {
                $credit_moeny = $Model->where($where)->field($selectFields)->order("$moduleId asc")->sum('money'); //lastdatetime desc,
                $listResult_credit[] = array(
                    'name' => '',
                    'money' => $credit_moeny,
                    'date' => '借方合计营收',
                    'ap' => '',
                );
            } else {
                $listResult_credit = array();
            }

            $listResult = array_merge($listResult_totalmoney, $listResult_promotionmoney, $listResult_debit, $listResult_credit);
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

            //结账日期
            $getDate = $_REQUEST['getDate'];
            //结账午别
            $getAp = $_REQUEST['getAp'];
            //分公司
            $companyArr = $_REQUEST['companyArr'];

            if (empty($getDate)) {
                $getDate = date('Y-m-d');
                $getAp = $this->getAp();
            }

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $param = array(
                'getDate' => $getDate,
                'getAp' => $getAp,
                'companyArr' => $companyArr,
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

            $revparType = $this->getRevparType();
            //只有财务才可以删除
            if ($revparType == 'finance') {
                $datagrid['fields']['操作'] = array(
                    'field' => 'id',
                    'width' => 20,
                    'align' => 'center',
                    'formatter' => $moduleName . 'ListviewModule.operate',
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
            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $getAp);
            $this->assign('revparType', $revparType); //财务权限
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    /**
     * 对营收内容进行计算和汇总
     * 要根据定义，分清楚分公司结账，和财务结账
     */
    public function revparMgrCalculate()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        //获取类型
        $revparType = $this->getRevparType();

        //结账日期
        $revparDate = $_REQUEST['revpar_date'];
        //结账午别
        $revparAp = $_REQUEST['revpar_ap'];

        $domain = $this->getDomain();

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        $rmsConnectDb = $this->connectHistoryRmsDb($revparDate);
        if ($currentDate !== $revparDate) {
            $orderformModel = M("orderform_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleModel = M("diningsale_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleproductsModel = M("diningsaleproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsalepaymentModel = M("diningsalepayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //开启订单数据表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderpaymentModel = D('orderpayment');
            $orderpaymentModel = D('orderpayment');
            $orderfinanceModel = D('orderfinance');
            $diningsaleModel = D('diningsale');
            $diningsaleproductsModel = D('diningsaleproducts');
            $diningsalepaymentModel = D('diningsalepayment');
        }
        //午别不同,也必须连接下午
        if ($currentAp != $revparAp) {
            $orderformModel = M("orderform_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleModel = M("diningsale_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsaleproductsModel = M("diningsaleproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $diningsalepaymentModel = M("diningsalepayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);

        }
        //支付表
        $paymentmgrModel = D('paymentmgr');

        //查询出所有支付情况
        $paymentmgrArr = array();
        $where = array();
        $where[] = " (company='$company'  or company='总部') and type='工作餐' and domain='$domain' ";
        $paymentmgrResult = $paymentmgrModel->where($where)->select();
        foreach ($paymentmgrResult as $value) {
            $paymentmgrArr[$value['name']] = $value['type'];
        }

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($revparDate);

        // 连接结账数据表
        $revparmgrresultModel = M("revparmgrresult", " ", $reveueConnectDb);
        $revparmgrModel = M('revparmgr_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);
        // 连接结账的数据库
        if ($currentDate == $revparDate) {
            switch ($domain) {
                case 'bj.lihuaerp.com':
                    $roomserviceModel = M("roomservice_bj", " ", $reveueConnectDb);
                    break;
                case 'nj.lihuaerp.com':
                    $roomserviceModel = M("roomservice_nj", " ", $reveueConnectDb);
                    break;
                case 'cz.lihuaerp.com':
                    $roomserviceModel = M("roomservice_cz", " ", $reveueConnectDb);
                    break;
                case 'wx.lihuaerp.com':
                    $roomserviceModel = M("roomservice_wx", " ", $reveueConnectDb);
                    break;
                case 'sz.lihuaerp.com':
                    $roomserviceModel = M("roomservice_sz", " ", $reveueConnectDb);
                    break;
                case 'sh.lihuaerp.com':
                    $roomserviceModel = M("roomservice_sh", " ", $reveueConnectDb);
                    break;
                case 'gz.lihuaerp.com':
                    $roomserviceModel = M("roomservice_gz", " ", $reveueConnectDb);
                    break;
                default:
                    $roomserviceModel = M("roomservice_" . substr($revparDate, 5, 2), " ", $reveueConnectDb);
                    break;
            }
        } else {
            $roomserviceModel = M("roomservice_" . substr($revparDate, 5, 2), " ", $reveueConnectDb);
        }

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
            case 'localhost':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                break;
        }

        $doorbillModel = M('doorbill_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);

        //先把结果删除了
        $where = array();
        $where['domain'] = $this->getDomain();
        $revparmgrresultModel->where($where)->delete();

        /**
         * 核对外送订单是否全部有送餐员的名字,如果还有订单没有配送,就输出错误信息
         */
        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;
        $where['sendname'] = array('EQ', '');
        $where['totalmoney'] = array('gt', 0);
        $where['domain'] = $this->getDomain();
        if ($revparType == 'company') {
            $where['company'] = $company;
        }
        if ($revparType == 'finance') {
            $where['company'] = '总部';

        }
        $orderformResult = $orderformModel->where($where)->select();
        if (count($orderformResult) > 0) {
            //还有订单没有配送,输出错误信息
            foreach ($orderformResult as $orderform) {
                //分财务结账和分公司结账，财务结账，需要告诉出错的原因和分公司的名字，分公司结账，不需要写出
                if ($revparType == 'finance') {
                    $result_company = '分公司：' . $orderform['company'] . ' ';
                } else {
                    $result_company = '';
                }
                $insertStr = $result_company . '订单:' . $orderform['address'] . ' ' . $orderform['ordertxt'] . '还没有派单,无法结账';
                $data = array();
                $data['result'] = $insertStr;
                if ($revparType == 'company') {
                    $data['company'] = $company;
                }
                $data['domain'] = $this->getDomain();
                $revparmgrresultModel->create();
                $revparmgrresultModel->add($data);
            }
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        };

        /**
         *检查订单是否已经结账，如果结账，就无需结账了
         */
        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;
        $where['isjiezhang'] = 1;
        if ($revparType == 'company') {
            $where['company'] = $company;
        }
        $where['domain'] = $this->getDomain();
        $orderformResult = $orderformModel->where($where)->limit(1)->select();
        if (count($orderformResult) > 0) {
            $data = array();
            $data['result'] = $revparDate . $revparAp . $orderformResult[0]['address'] . '的订单已经结账，不能再结账了！';
            $data['datetime'] = date('Y-m-d H:i:s');
            if ($revparType == 'company') {
                $data['company'] = $company;
            }
            $data['domain'] = $this->getDomain();
            $revparmgrresultModel->create();
            $revparmgrresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        /**
         *  从送餐员结账表中检查一下每一个送餐的账是否正确
         */

        $where = array();
        $where['date'] = $revparDate;
        $where['ap'] = $revparAp;
        if ($revparType == 'company') {
            $where['company'] = $company;
        }
        $where['domain'] = $this->getDomain();
        $roomserviceResult = $roomserviceModel->where($where)->select();
        //判断是否有送餐员的结账数据，如果没有，就退出
        if (empty($roomserviceResult)) {
            $data = array();
            $data['result'] = $company . $revparDate . $revparAp . '没有报数单！';
            $data['datetime'] = date('Y-m-d H:i:s');
            if ($revparType == 'company') {
                $data['company'] = $company;
            }
            $data['domain'] = $this->getDomain();
            $revparmgrresultModel->create();
            $revparmgrresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        $revparmgrbool = false; //设定一个错误开关，如果有错误，最后提示
        foreach ($roomserviceResult as $roomservice) {
            //分财务结账和分公司结账，财务结账，需要告诉出错的原因和分公司的名字，分公司结账，不需要写出
            if ($revparType == 'finance') {
                $result_company = '分公司：' . $roomservice['company'] . ' ';
            } else {
                $result_company = '';
            }
            //对结账金额检查，是否正确
            if ($roomservice['totalmoney'] != $roomservice['jiezhangmoney']) {
                $data = array();
                $data['result'] = $result_company . $roomservice['name'] . '的送餐员的结账金额不对！' . '结账金额：' . $roomservice['jiezhangmoney'] . ' 订单金额：' . $roomservice['totalmoney'];
                $data['datetime'] = date('Y-m-d H:i:s');
                if ($revparType == 'company') {
                    $data['company'] = $company;
                }
                $data['domain'] = $this->getDomain();
                $revparmgrresultModel->create();
                $revparmgrresultModel->add($data);
                $revparmgrbool = true;
            }
        }

        /**
         * 财务结账，还要检查一下分公司是否没有结账的，需要提示
         */
        if ($revparType == 'finance') {
            $where = array();
            $where['custdate'] = $_REQUEST['revpar_date'];
            $where['domain'] = $this->getDomain();
            $where[] = ' length(company) >  0  and totalmoney > 0 ';
            $orderform_company = $orderformModel->distinct('true')->field('company')->where($where)->select();
            $roomservice_company = $roomserviceModel->distinct('true')->field('company')->where($where)->select();
            foreach ($orderform_company as $value) {
                //如果这个分公司还没有结账，需要提示
                if (!in_array($value, $roomservice_company)) {
                    $data = array();
                    $data['result'] = '分公司:' . $value['company'] . ' 还没有结账!';
                    $data['datetime'] = date('Y-m-d H:i:s');
                    $data['domain'] = $this->getDomain();
                    $revparmgrresultModel->create();
                    $revparmgrresultModel->add($data);
                    $revparmgrbool = true;
                }
            }
        }

        //因为有错误，所以退出，显示错误
        if ($revparmgrbool) {
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //有结账单，就继续结账
        //设定一个错误开关,还是要继续检查订单金额和结账金额是否相等，订单金额和支付金额是否相等，如果不等，还是要提示
        $revparmgrbool = false;

        /***
         * 分公司结账，需要删除当日，午别的数据
         */
        if ($revparType == 'company') {
            $where = array();
            $where['date'] = $revparDate;
            $where['ap'] = $revparAp;
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $accountsbillsmingxiModel->where($where)->delete();
        }

        /**
         * 开始结账，计算外送营业金额等数据
         */
        //计算列表
        $total_money = 0; //销售总金额
        $promotion_money = 0; //促销金额
        $waimai_money = 0; //外卖金额
        $dinding_money = 0; //门市金额
        $worklunch_money = 0; //工作餐金额
        $songcanfei_money = 0; //送餐费金额
        $payment_money_arr = array(); //支付项目
        $meituan_fuwu_money = 0; //美团服务费
        $eleme_fuwu_money = 0; //饿了么服务费
        $otherplat_fuwu_money = 0; //服务费

        /****** */
        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;
        if ($revparType == 'company') {
            $where['company'] = $company;
        }

        $where[] = '  totalmoney > 0 ';
        $where['domain'] = $this->getDomain();
        //返回所有的订单,支付，产品，活动等数据
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderValue) {
            //订单金额
            $totalMoney = (float) $orderValue['totalmoney'];
            //结账金额
            $jiezhangMoney = (float) $orderValue['jiezhangmoney'];
            //检查订单金额和结账金额是否相等，不等，记入到数据库中
            if ($totalMoney !== $jiezhangMoney) {
                $data = array();
                $data['result'] = '订单号:' . $orderValue['ordersn'] . ' 分公司:' . $orderValue['company'] . '送餐员:' . $orderValue['sendname'] .
                    '订单金额:' . $orderValue['totalmonehy'] . ' 结账金额:' . $orderValue['jiezhangmoney'] .
                    ' 不相等！';
                $data['datetime'] = date('Y-m-d H:i:s');
                $data['domain'] = $this->getDomain();
                $revparmgrresultModel->create();
                $revparmgrresultModel->add($data);
                $revparmgrbool = true;
            }

            //计算外送销售额
            $total_money += (float) $orderValue['totalmoney'];
            $ordersn = $orderValue['ordersn'];
            $where = array();
            $where['ordersn'] = $ordersn;
            //计算送餐费等
            $orderproductsResult = $orderproductsModel->where($where)->select();
            foreach ($orderproductsResult as $productsValue) {
                if (strpos($productsValue['name'], 'S') > 0) { //S是送餐费等标志
                    $songcanfei_money += $productsValue['money'];
                }
            };
            /********************/
            //这里临时计算门市金额，订单中送餐员为堂口的是门市金额
            if (($orderValue['sendname'] == '堂口') or ($orderValue['sendname'] == '门市')) {
                if($orderValue['origin'] == '电话'){
                    $dinding_money += (float) $orderValue['totalmoney'];
                }else{
                    //不能取totalmoney,要去掉促销今天，所以用paidmoney
                    $dinding_money += (float) $orderValue['paidmoney'];
                }              
            }
            //********************* */

            //计算支付
            $where = array();
            $where['ordersn'] = $ordersn;
            $orderfinanceResult = $orderfinanceModel->where($where)->select();
            //订单支付金额
            $orderFinanceMoney = 0;
            foreach ($orderfinanceResult as $financeValue) {
                //管理费(美团，饿了么收)
                if (strstr($financeValue['name'], '活动费')) {
                    //美团活动费
                    if ($orderValue['orign'] == '美团') {
                        $meituan_fuwu_money += $financeValue['money'];
                    }
                    //饿了么活动费
                    if ($orderValue['name'] == '饿了么') {
                        $eleme_fuwu_noney += $financeValue['money'];
                    }
                    //全部的活动费
                    $promotion_money += $financeValue['money'];
                } //活动费用另外计算
                else {
                    if (empty($finance_money_arr[$financeValue['name']])) {
                        $finance_money_arr[$financeValue['name']] = 0;
                    }
                    $finance_money_arr[$financeValue['name']] += (float) $financeValue['money'];
                }
                $otherplat_fuwu_money += $financeValue['money'];
                //计算订单支付金额
                $orderFinanceMoney += (float) $financeValue['money'];

                /********************/
                //计算工作餐金额
                if ($paymentmgrArr[$financeValue['name']] == '工作餐') {
                    $worklunch_money += (float) $financeValue['money'];
                }

                /**
                 * 如果是分公司结账，需要把支付数据，写入表中
                 */
                if ($revparType == 'company') {
                    $data = array();
                    $data['ordersn'] = $ordersn;
                    $data['code'] = $financeValue['code'];
                    $data['name'] = $financeValue['name'];
                    $data['money'] = $financeValue['money'];
                    $data['note'] = isset($financeValue['note']) ? $financeValue['note'] : '';
                    $data['type'] = '营收';
                    $data['date'] = $revparDate;
                    $data['ap'] = $revparAp;
                    $data['company'] = $company;
                    $data['domain'] = $this->getDomain();
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $accountsbillsmingxiModel->create();
                    $accountsbillsmingxiModel->add($data);
                }
            };
            //检查订单金额和支付账金额是否相等，不等，记入到数据库中
            if (abs((float) $totalMoney - (float) $orderFinanceMoney) > 0.0001) {
                //if(true){
                $data = array();
                $data['result'] = '分公司:' . $orderValue['company'] . '送餐员:' . $orderValue['sendname'] .
                '订单金额:' . $totalMoney . ' 支付金额:' . $orderFinanceMoney . ' ' . abs((float) $totalMoney - (float) $orderFinanceMoney) .
                    ' 不想等！' .
                    '地址:' . $orderValue['address']; //. ' 订餐内容:' . $orderValue['ordertxt'] ;
                $data['datetime'] = date('Y-m-d H:i:s');
                $data['domain'] = $this->getDomain();
                $revparmgrresultModel->create();
                $revparmgrresultModel->add($data);
                $revparmgrbool = true;
            }

        }

        //这里实际计算门市金额
        if ($this->getDomain() == 'cz.lihuaerp.com') {
            $where = array();
            $where['date'] = $revparDate;
            $where['ap'] = $revparAp;
            if ($revparType == 'company') {
                $where['company'] = $company;
            }
            $diningsale_money = 0;
            $where['domain'] = $this->getDomain();
            $where['state'] = 0;
            $diningsaleResult = $diningsaleModel->where($where)->select();
            foreach ($diningsaleResult as $diningsaleValue) {
                $diningsale_money += $diningsaleValue['money'];
                $total_money += (float) $diningsaleValue['money'];

                //ID
                $diningsaleId = $diningsaleValue['diningsaleid'];
                //查询支付
                $where = array();
                $where['diningsaleid'] = $diningsaleId;
                $diningsalepaymentResult = $diningsalepaymentModel->where($where)->select();
                foreach ($diningsalepaymentResult as $diningsalepaymentValue) {
                    $finance_money_arr[$diningsalepaymentValue['name']] += (float) $diningsalepaymentValue['money'];
                }
            }
        }

        //因为有错误，所以退出，显示错误
        if ($revparmgrbool) {
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //计算门市金额
        $door_money = 0;
        $where = array();
        $where['date'] = $revparDate;
        $where['ap'] = $revparAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $doorbillMoneyResult = $doorbillModel->where($where)->sum('money');
        if ($doorbillMoneyResult > 0) {
            $door_money = $doorbillMoneyResult;
        }
        /*** hack一下    ***/
        $door_money = $dinding_money;

        //先删除以前的记录
        $where = array();
        $where['date'] = $revparDate;
        $where['ap'] = $revparAp;
        if ($revparType == 'company') {
            $where['company'] = $company;
        }
        if ($revparType == 'finance') {
            $where['company'] = '总部';
        }
        $where['domain'] = $this->getDomain();
        $revparmgrModel->where($where)->delete();

        //如果是财务结账，那么company 为总部
        if ($revparType == 'finance') {
            $company = '总部';
        }
        $row = 1;
        //外卖销售额
        $waimai_money = $total_money - $promotion_money - $dinding_money - $diningsale_money - $songcanfei_money - $worklunch_money;
       
        
        //保存数据
        $this->saveRevpar($row, '外送收入', $waimai_money, 'M1', $revparDate, $revparAp, $company);

        //送餐费
        $row = $row + 1;
        $this->saveRevpar($row, '送餐费', $songcanfei_money, 'M1', $revparDate, $revparAp, $company);

        //门市金额
        $row = $row + 1;
        $this->saveRevpar($row, '门市金额', ($dinding_money + $diningsale_money), 'M1', $revparDate, $revparAp, $company);

        //工作餐金额
        $row = $row + 1;
        $this->saveRevpar($row, '工作餐金额', $worklunch_money, 'M1', $revparDate, $revparAp, $company);

        //支付循环输入
        foreach ($finance_money_arr as $financeName => $financeValue) {
            $row = $row + 1;
            $this->saveRevpar($row, $financeName, $financeValue, 'M2', $revparDate, $revparAp, $company);
        }

        //营业额
        $row = $row + 1;
        $this->saveRevpar($row, '销售总额', $total_money, 'O', $revparDate, $revparAp, $company);

        //促销金额
        $row = $row + 1;
        $this->saveRevpar($row, '促销额', $promotion_money, 'O', $revparDate, $revparAp, $company);

        //网站销售额

        //APP销售额

        //微信销售额

        //小程序销售额

        //美团销售额

        //美团促销额

        //美团服务费

        //饿了么销售额

        //饿了么促销额

        //饿了么服务费

        //计算支付

        /**
         * 如果是财务结账，
         * 设置订单表为结账状态
         * 然后分公司就不能重新结账了，控制的需要
         */
        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;
        if ($revparType == 'finance') {
            $where['domain'] = $this->getDomain();
            $data = array();
            $data['isjiezhang'] = 1;
            $orderformModel->where($where)->save($data);
            //设置客户支付为不能编辑状态
            foreach ($finance_money_arr as $financeName => $financeValue) {
                $where = array();
                $where['name'] = $financeName;
                $data = array();
                $data['is_edit'] = 1;
                $data['is_use'] = 1;
                $data['update_datetime'] = date('Y-m-d H:i:s');
                $paymentmgrModel->where($where)->save($data);
            }
        }

        //结账完成
        $res = array();
        $res['state'] = 1;
        $this->ajaxReturn($res);
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
        $revparmgrresultModel = M("revparmgrresult", " ", $connectionDb);

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

    /***
     * 删除结账的数据，只有财务管理员才有这个功能
     */
    public function delete()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        //获取类型
        $revparType = $this->getRevparType();

        //只有财务账户才能做删除的动作
        if ($revparType != 'finance') {
            $res = array();
            $res['state'] = 0;
            $res['info'] = '没有删除的权限';
            $this->ajaxReturn($res);
        }

        //结账日期
        $revparDate = $_REQUEST['revpar_date'];
        //结账午别
        $revparAp = $_REQUEST['revpar_ap'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        $rmsConnectDb = $this->connectHistoryRmsDb($revparDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
            $orderformModel = M("orderform_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        //支付表
        $paymentmgrModel = D('paymentmgr');

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($revparDate);

        // 连接结账数据表
        $revparmgrresultModel = M("revparmgrresult", " ", $reveueConnectDb);
        $revparmgrModel = M('revparmgr_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);

        //查询一下是否有结账的记录，没有提示不能删除
        //先删除以前的记录
        $where = array();
        $where['date'] = $revparDate;
        if ($revparAp == '全天') {
            //不用设置
        } else {
            $where['ap'] = $revparAp;

        }
        if ($revparType == 'finance') {
            $where['company'] = '总部';
        }
        $where['domain'] = $this->getDomain();
        $revparmgrResult = $revparmgrModel->where($where)->count();
        if ($revparmgrResult <= 0) {
            $res = array();
            $res['state'] = 0;
            $res['info'] = '没有结账的数据!';
            $this->ajaxReturn($res);
        }

        //先删除以前的记录
        $where = array();
        $where['date'] = $revparDate;
        if ($revparAp == '全天') {
            //不用设置
        } else {
            $where['ap'] = $revparAp;

        }
        if ($revparType == 'finance') {
            $where['company'] = '总部';
        }
        $where['domain'] = $this->getDomain();
        $revparmgrModel->where($where)->delete();

        /**
         * 如果是财务结账，
         * 设置订单表为不结账状态
         * 然后分公司就不能重新结账了，控制的需要
         */
        $where = array();
        $where['custdate'] = $revparDate;
        if ($revparAp == '全天') {
            //不用设置
        } else {
            $where['ap'] = $revparAp;
        }
        $where['isjiezhang'] = 1;
        if ($revparType == 'finance') {
            $where['domain'] = $this->getDomain();
            $data = array();
            $data['isjiezhang'] = 0;
            $orderformModel->where($where)->save($data);
        }

        //返回结账成功的信息
        $res = array();
        $res['state'] = 1;
        $res['info'] = '删除完成！';
        $this->ajaxReturn($res);
    }

    /**
     * 保存数据到revpar中
     */
    public function saveRevpar($row, $name, $money, $type, $date, $ap, $company)
    {
        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($date);

        // 连接结账数据表
        $revparmgrModel = M('revparmgr_' . substr($date, 5, 2), " ", $reveueConnectDb);
        $data = array();
        $data['row'] = $row;
        $data['name'] = $name;
        $data['money'] = $money;
        $data['type'] = $type;
        $data['date'] = $date;
        $data['ap'] = $ap;
        $data['company'] = $company;
        $data['domain'] = $this->getDomain();

        //插入数据
        $revparmgrModel->create();
        $revparmgrModel->add($data);
    }

    /**
     * 返回Tree
     */
    public function tree()
    {
        $where = array();
        $where['domain'] = $this->getDomain();
        $compayResult = D('companymgr')->field('companymgrid as id,name')->where($where)->select();
        $compay = array();
        $company[] = array(
            'id' => '总部',
            'text' => '总部',
        );
        foreach ($compayResult as $value) {
            $company[] = array(
                'id' => $value['name'],
                'text' => $value['name'],
            );
        }
        $this->ajaxReturn($company);
    }

    // 查看结账数据的详细页面
    public function revparDetailview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getDbAp();

            $revparDate = $_REQUEST['revparDate'];
            $revparAp = $_REQUEST['revparAp'];
            //支付名称等
            $revparName = $_REQUEST['revparName'];
            $domain = $this->getDomain();
            //获取类型
            $revparType = $this->getRevparType();

            $rmsConnectDb = $this->connectHistoryRmsDb($revparDate);
            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //如果不是，就要选择备份库
            if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
                $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            } else {
                //连接当前库和表
                $orderformModel = D('orderform');
                $ordergoodsModel = D('orderproducts');
                $orderfinanceModel = D('orderfinance');
            }

            // 生成list字段列表
            $listFields = $focus->revparDetailListFields;

            if ($revparName == '销售总额') {
                if ($revparType == 'finance') {
                    $revparCompany = "";
                    $orderby = " order by company";
                } else {
                    $revparCompany = " company='$company'  and  ";
                    $orderby = "  order by money";
                }
                if ($revparAp == '全天') {
                    $revparApWhere = '';
                } else {
                    $revparApWhere = " and ap = '$revparAp' ";
                }

                //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
                if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
                    $sql = " select '','金额' as name,sum(jiezhangmoney) as money,sendname,company from rms_orderform_" . substr($revparDate, 5, 2) .
                        " where  " . $revparCompany . "  custdate = '$revparDate' " . $revparApWhere . " and domain = '$domain' group by sendname   " . $orderby;
                    $orderformResult = $orderformModel->query($sql);
                    $sql = " select sum(jiezhangmoney) as money from rms_orderform_" . substr($revparDate, 5, 2) .
                        " where  " . $revparCompany . "  custdate = '$revparDate' " . $revparApWhere . " and domain = '$domain'  ";
                    $orderformHejiMoney = $orderformModel->query($sql);
                } else {
                    $sql = " select '','金额',sum(jiezhangmoney) as money,sendname,company from rms_orderform" .
                        " where  " . $revparCompany . "
                           custdate = '$revparDate' " . $revparApWhere . " and domain = '$domain' group by sendname
                          " . $orderby;
                    $orderformResult = $orderformModel->query($sql);
                    $sql = " select '','金额',sum(jiezhangmoney) as money,sendname,company from rms_orderform" .
                        " where  " . $revparCompany . "
                          custdate = '$revparDate' " . $revparApWhere . " and domain = '$domain' ";
                    $orderformHejiMoney = $orderformModel->query($sql);
                }
                $orderformResult[] = array(
                    'code' => '',
                    'name' => '销售总额',
                    'money' => $orderformHejiMoney[0]['money'],
                    'company' => '',
                );

                $data = array('total' => 20, 'rows' => $orderformResult, 'date' => $currentDate, 'ap' => $currentAp, 'sql' => $sql);
                $this->ajaxReturn($data);

            }

            if ($revparType == 'finance') {
                $revparCompany = "";
                $orderby = " order by b.company ";
            } else {
                $revparCompany = "  b.company='$company'  and  ";
                $orderby = "";
            }

            if ($revparAp == '全天') {
                $revparApWhere = '';
            } else {
                $revparApWhere = " and b.ap = '$revparAp' ";
            }
            if ($revparName == '送餐费') {
                //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
                if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
                    $sql = " select '' ,'送餐费' as name,sum(a.money) as money,b.sendname,b.company from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderproducts_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                          b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%S%'  group by b.sendname,a.name  " . $orderby;
                    $orderformResult = $orderformModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderproducts_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn
                         where  " . $revparCompany . "
                          b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%S%'   ";
                    $orderformHejiMoney = $orderformModel->query($sql);
                    $orderformResult[] = array(
                        'code' => '',
                        'name' => '送餐费总额',
                        'money' => $orderformHejiMoney[0]['money'],
                        'company' => '',
                    );
                } else {
                    $sql = " select '' ,'送餐费' as name,sum(a.money) as money,b.sendname,b.company from rms_orderform" .
                        " as b
                         left join rms_orderproducts" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%S%'  group by b.sendname,a.name  ";
                    $orderformResult = $orderformModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform" .
                        " as b
                         left join rms_orderproducts" . " as a on a.ordersn = b.ordersn
                         where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%S%'   ";
                    $orderformHejiMoney = $orderformModel->query($sql);
                    $orderformResult[] = array(
                        'code' => '',
                        'name' => '送餐费总额',
                        'money' => $orderformHejiMoney[0]['money'],
                        'company' => '',
                    );
                }
                $data = array('total' => 20, 'rows' => $orderformResult, 'sql' => $sql);
                $this->ajaxReturn($data);
            }

            //常州扫码
            if (($revparName == '扫码') && ($domain == 'cz.lihuaerp.com')) {
                //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
                if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
                    $sql = " select '' ,a.name,sum(a.money) as money,if(isnull(c.shaomaocode),b.sendname ,concat(c.shaomaocode,' | ',b.sendname ))  as sendname ,b.company from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn
                          join czrms.rms_sendnamemgr as c on b.sendname= c.name and b.company=c.company and b.domain=c.domain
                          where  " . $revparCompany . "
                          b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                          a.name='$revparName'  group by b.sendname,a.name  order by a.name ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'    ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '扫码金额合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );

                } else {
                    $sql = " select '' ,a.name,sum(a.money) as money,if(isnull(c.shaomaocode),b.sendname ,concat(c.shaomaocode,' | ',b.sendname ))  as  sendname ,b.company from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn
                         join czrms.rms_sendnamemgr as c on b.sendname= c.name and b.company=c.company and b.domain=c.domain
                          where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'  group by b.sendname,a.name  ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'    ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '金额合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );

                }
                $data = array('total' => 20, 'rows' => $orderfinanceResult, 'sql' => $fsql);
                $this->ajaxReturn($data);
            }

            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //如果不是，就要选择备份库
            if (($currentDate != $revparDate) || ($currentAp != $revparAp)) {
                if ($revparName == '促销额') {
                    $sql = " select '' ,a.name,sum(a.money) as money,b.sendname,b.company from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%活动费%'  group by b.sendname,a.name  order by a.name ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%活动费%'   ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '促销合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );
                    $sql = " select '' ,a.name,sum(a.money) as money,b.sendname,b.company from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%活动费%'  group by  a.name  order by a.name ";
                    $orderfinanceActivity = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '',
                        'money' => '',
                        'company' => '',
                    );

                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '促销费用统计',
                        'money' => '',
                        'company' => '',
                    );

                    foreach ($orderfinanceActivity as $value) {
                        $orderfinanceResult[] = array(
                            'code' => '',
                            'name' => $value['name'],
                            'money' => $value['money'],
                            'company' => '',
                        );

                    }

                } else {
                    $sql = " select '' ,a.name,sum(a.money) as money,b.sendname,b.company from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'  group by b.sendname,a.name  order by a.name ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform_" . substr($revparDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($revparDate, 5, 2) . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'    ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '金额合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );
                }
            } else {
                if ($revparName == '促销额') {
                    $sqld = " select '' ,a.name,sum(a.money) as money,b.sendname,b.company from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate'  " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%活动费%'  group by b.sendname,a.name order by a.name ";
                    $orderfinanceResult = $orderfinanceModel->query($sqld);
                    $sql = " select sum(a.money) as money from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name like '%活动费%'   ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '促销合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );

                } else {
                    $sql = " select '' ,a.name,sum(a.money) as money,b.sendname,b.company from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'  group by b.sendname,a.name  ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                    $sql = " select sum(a.money) as money from rms_orderform" .
                        " as b
                         left join rms_orderfinance" . " as a on a.ordersn = b.ordersn where  " . $revparCompany . "
                         b.custdate = '$revparDate' " . $revparApWhere . " and b.domain = '$domain' and
                         a.name='$revparName'    ";
                    $orderfinanceHejiMoney = $orderfinanceModel->query($sql);
                    $orderfinanceResult[] = array(
                        'code' => '',
                        'name' => '金额合计',
                        'money' => $orderfinanceHejiMoney[0]['money'],
                        'company' => '',
                    );
                }
            }

            $data = array('total' => 20, 'rows' => $orderfinanceResult, 'sql' => $sqld);
            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->revparDetailListFields;
            // 模块的ID
            $moduleId = 'orderformid';

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            // 取得返回的是列表还是查询列表
            $returnAction = $_REQUEST['returnAction'];
            $this->assign('returnAction', $returnAction);

            $revparDate = $_REQUEST['date'];
            $revparAp = $_REQUEST['ap'];
            $revparName = $_REQUEST['name'];

            $param = array(
                'revparDate' => $revparDate,
                'revparAp' => $revparAp,
                'revparName' => $revparName,
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/revparDetailview', $param),
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

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => 'RevparDetailviewModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            $this->assign('info', $result);
            $this->assign('record', $record);
            $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
            $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
            $this->assign('pagetype', $_REQUEST['pagetype']);
            $this->assign('sendname', $sendname);
            $this->assign('getDate', $revparDate);
            $this->assign('getAp', $revparAp);

            // 返回从表的内容
            $this->get_slave_table($record, $revparDate, $revparAp);

            $this->display('YingshouRevparMgr' . '/revpardetailview');
        }
    }

    // 查看分公司结账完成的情况
    public function revparList()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            $revparDate = $_REQUEST['revparDate'];
            $revparAp = $_REQUEST['revparAp'];
            if ($revparAp == '全天') {
                $whereAp = '';
            } else {
                $whereAp = " and ap = '$revparAp'";
            }
            //支付名称等
            $revparName = $_REQUEST['revparName'];
            $domain = $this->getDomain();

            //链接结账库
            $reveueConnectDb = $this->connectReveueDb($revparDate);

            // 连接结账数据表
            $revparmgrModel = M('revparmgr_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);

            $sql = " select distinct(company) as company, date from revparmgr_" . substr($revparDate, 5, 2) .
                "  where date =  '$revparDate'" . $whereAp . " and domain='$domain'";
            $revparmgrResult = $revparmgrModel->query($sql);

            $data = array('total' => 20, 'rows' => $revparmgrResult, 'sql' => $sql);
            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = array(
                'company' => array('width' => 10, 'align' => 'center'),
                'date' => array('width' => 10, 'align' => 'center'),
            );

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            // 取得返回的是列表还是查询列表
            $returnAction = $_REQUEST['returnAction'];
            $this->assign('returnAction', $returnAction);

            $revparDate = $_REQUEST['revpar_date'];
            $revparAp = $_REQUEST['revpar_ap'];

            $param = array(
                'revparDate' => $revparDate,
                'revparAp' => $revparAp,
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/revparList', $param),
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
            $this->assign('getDate', $revparDate);
            $this->assign('getAp', $revparAp);

            $this->display('YingshouRevparMgr' . '/revparlist');
        }
    }

    /**
     * 获取操作员的权限
     */
    public function getRevparType()
    {
        //定义是哪个结账
        $userid = $_SESSION['userid'];
        //查询角色ID
        $roleuserModel = D('role_user');
        $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
        $roleid = $roleuserResult['role_id'];

        //查询角色的功能
        $accessModel = D('access');
        $where = array();
        $where['role_id'] = $roleid;
        $accessResult = $accessModel->field('node_id')->where($where)->select();
        foreach ($accessResult as $value) {
            $accessArr[] = $value['node_id'];
        }
        //节点表
        $nodeModel = D('node');
        //查询分公司结账节点
        $nodeidResult = $nodeModel->where("name='companyRevpar'")->find();
        $nodeidCompanyRevpar = $nodeidResult['id'];
        if (in_array($nodeidCompanyRevpar, $accessArr)) {
            $revparType = 'company';
        }

        //查询财务结账节点
        $nodeidResult = $nodeModel->where("name='financeRevpar'")->find();
        $nodeidFinanceRevpar = $nodeidResult['id'];
        if (in_array($nodeidFinanceRevpar, $accessArr)) {
            $revparType = 'finance';
        }

        return $revparType;
    }
}
