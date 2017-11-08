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

            // 建立查询条件
            $where = array();
            $where['date'] = $getDate;
            $where['ap'] = $getAp;
            //$where['domain'] = $_SERVER['HTTP_HOST'];

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

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', array()),
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

            $repvarType = $this->getRevparType();
            //只有财务才可以删除
            if ($repvarType == 'finance') {
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
            $this->assign('getDate', date('Y-m-d'));
            //当前午别
            $this->assign('getAp', $this->getAp());
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
        $company = '怀南';

        //获取类型
        $repvarType = $this->getRevparType();

        //结账日期
        $revparDate = $_REQUEST['revpar_date'];
        //结账午别
        $revparAp = $_REQUEST['revpar_ap'];

        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        $rmsConnectDb = $this->connectRmsDb($revparDate);
        if ($currentDate !== $revparDate) {
            $orderformModel = M("orderform_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //开启订单数据表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderpaymentModel = D('orderpayment');
            $orderpaymentModel = D('orderpayment');
        }
        //午别不同,也必须连接下午
        if ($currentAp != $revparAp) {
            $orderformModel = M("orderform_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderpayment_" . substr($revparDate, 5, 2), "rms_", $rmsConnectDb);
        }
        //支付表
        $paymentmgrModel = D('paymentmgr');

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($revparDate);

        // 连接结账数据表
        $revparmgrresultModel = M("revparmgrresult", " ", $reveueConnectDb);
        $revparmgrModel = M('revparmgr_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);
        $roomserviceModel = M('roomservice_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);
        $roomserviceaccountsModel = M('roomserviceaccounts_' . substr($revparDate, 5, 2), " ", $reveueConnectDb);

        /**
         * 核对外送订单是否全部有送餐员的名字,如果还有订单没有配送,就输出错误信息
         */
        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;
        $where['sendname'] = array('EQ', '');
        if ($repvarType == 'company') {
            $where['company'] = $company;
        }
        $orderformResult = $orderformModel->where($where)->select();
        if (count($orderformResult) > 0) {
            //$revparmgrresultModel->where(1)->delete();
            //还有订单没有配送,输出错误信息
            foreach ($orderformResult as $orderform) {
                $insertStr = '订单:' . $orderform['address'] . ' ' . $orderform['ordertxt'] . '还没有派单,无法结账';
                $data = array();
                $data['result'] = $insertStr;
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
        if ($repvarType == 'company') {
            $where['company'] = $company;
        }

        $where['domain'] = $this->getDomain();
        $orderformResult = $orderformModel->where($where)->limit(1)->select();
        if (count($orderformResult) > 0) {
            //有订单已经结账，就不能再结账了
            $revparmgrresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $revparDate . $revparAp . '的订单已经结账，不能再结账了！';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
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
        //$where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $roomserviceResult = $roomserviceModel->where($where)->select();
        //判断是否有送餐员的结账数据，如果没有，就退出
        if (empty($roomserviceResult)) {
            $revparmgrresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $revparDate . $revparAp . '没有结账单！';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $revparmgrresultModel->create();
            $revparmgrresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //有结账单，就继续结账
        $revparmgrbool = false; //设定一个错误开关，如果有错误，最后提示
        $revparmgrresultModel->where(1)->delete();
        foreach ($roomserviceResult as $roomservice) {
            //对结账金额检查，是否正确
            if ($roomservice['totalmoney'] != $roomservice['jiezhangmoney']) {
                $data = array();
                $data['result'] = $roomservice['name'] . '的送餐员的结账金额不对！' . '结账金额：' . $roomservice['jiezhangmoney'] . ' 订单金额：' . $roomservice['totalmoney'];
                $data['datetime'] = date('Y-m-d H:i:s');
                //$data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $revparmgrresultModel->create();
                $revparmgrresultModel->add($data);
                $revparmgrbool = true;
            }
        }
        //因为有错误，所以退出，显示错误
        if ($revparmgrbool) {
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
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
        $where['date'] = $revparDate;
        $where['ap'] = $revparAp;
        //$where['company'] = $company;
        $where['domain'] = $this->getDomain();
        //返回所有的订单,支付，产品，活动等数据
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderValue) {
            //计算外送销售额
            $total_money += $orderValue['totalmoney'];
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
            //计算支付
            $orderpaymentResult = $orderpaymentModel->where($where)->select();
            foreach ($orderpaymentResult as $paymentValue) {
                if (empty($payment_money_arr[$paymentValue['name']])) {
                    $payment_money_arr[$paymentValue['name']] = 0;
                }
                $payment_money_arr[$paymentValue['name']] += $paymentValue['money'];
            };
            //计算促销费用等
            $orderactivityResult = $orderactivityModel->where($where)->select();
            foreach ($orderactivityResult as $activityValue) {
                //管理费(美团，饿了么收)
                if ($activityValue['name'] == '服务费') {
                    //美团服务费
                    if ($orderValue['orign'] == '美团') {
                        $meituan_fuwu_money += $activityValue['money'];
                    }
                    //饿了么服务费
                    if ($orderValue['name'] == '饿了么') {
                        $eleme_fuwu_noney += $activityValue['money'];
                    }
                    //全部的服务费
                    $otherplat_fuwu_money += $activityValue['money'];
                } else {
                    //美团促销
                    if ($orderValue['orign'] == '美团') {
                        $promotion_meituan_money += $activityValue['money'];
                    }
                    //饿了么促销
                    if ($orderValue['orign'] == '美团') {
                        $promotion_eleme_money += $activityValue['money'];
                    }
                    //网站促销
                    //全部的促销
                    $promotion_money += $activityValue['money'];
                }
            }
        }

        //计算门市金额
        $dinding_money = 0;

        //计算工作餐金额
        $worklunch_money = 0;

        //先删除以前的记录
        $where = array();
        $where['date'] = $revparDate;
        $where['ap'] = $revparAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $revparmgrModel->where($where)->delete();

        $row = 1;
        //外卖销售额
        $waimai_money = $total_money - $promotion_money - $songcanfei_money;
        //保存数据
        $this->saveRevpar($row, '外卖销售额', $waimai_money, 'M', $revparDate, $revparAp, $company);

        //送餐费
        $row = $row + 1;
        $this->saveRevpar($row, '送餐费', $songcanfei_money, 'M', $revparDate, $revparAp, $company);

        //门市金额
        $row = $row + 1;
        $this->saveRevpar($row, '门市金额', $dinding_money, 'M', $revparDate, $revparAp, $company);

        //工作餐金额
        $row = $row + 1;
        $this->saveRevpar($row, '工作餐金额', $worklunch_money, 'M', $revparDate, $revparAp, $company);

        //支付循环输入
        $where = array();
        //$where['domain'] = $this->getDomain();
        $paymentmgrResult = $paymentmgrModel->where($where)->select();
        foreach ($paymentmgrResult as $paymentValue) {
            if ($payment_money_arr[$paymentValue['name']] > 0) {
                $row = $row + 1;
                $this->saveRevpar($row, $paymentValue['name'], $payment_money_arr[$paymentValue['name']], 'M', $revparDate, $revparAp, $company);
            }
        }

        //促销金额
        $row = $row + 1;
        $this->saveRevpar($row, '外卖促销额', $promotionmoney, 'O', $revparDate, $revparAp, $company);

        //营业额
        $row = $row + 1;
        $this->saveRevpar($row, '营业额', $total_money, 'O', $revparDate, $revparAp, $company);

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
         * 设置送餐员结账表为结账状态
         */
        $where = array();
        $data = array();
        $data['isjiezhang'] = 1;
        $roomserviceModel->where($where)->save($data);

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
        $compay = D('companymgr')->field('companymgrid as id,name')->select();
        $c = array();
        foreach ($compay as $value) {
            $c[] = array(
                'id' => $value['id'],
                'text' => $value['name'],
            );
        }
        $this->ajaxReturn($c);
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
            $repvarType = 'company';
        }

        //查询财务结账节点
        $nodeidResult = $nodeModel->where("name='financeRevpar'")->find();
        $nodeidFinanceRevpar = $nodeidResult['id'];
        if (in_array($nodeidCompanyRevpar, $accessArr)) {
            $repvarType = 'finance';
        }

        return $repvarType;
    }
}
