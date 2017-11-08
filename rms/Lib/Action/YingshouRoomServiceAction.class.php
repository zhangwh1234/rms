<?php
/**
 * 外送结账模块
 * Created by zhangwh
 * User: lihua
 * Date: 16/7/5
 * Time: 下午12:46
 */

class YingshouRoomServiceAction extends YingshouAction
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
            $connectionDb = $this->connectReveueDb('');
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
            //判断结账金额和交账金额是否相等，做提示用
            foreach ($listResult as $key => $value) {
                if ($value['totalmoney'] != $value['jiezhangmoney']) {
                    $listResult[$key]['isShow'] = 1; //显示颜色
                } else {
                    $listResult[$key]['isShow'] = 0; //不显示颜色
                }
            }

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

            //结账日期
            $getDate = $_REQUEST['getDate'];
            //结账午别
            $getAp = $_REQUEST['getAp'];

            if (empty($getDate)) {
                $getDate = date('Y-m-d');
                $getAp = $this->getAp();
            }

            $param = array(
                'getDate' => $getDate,
                'getAp' => $getAp,
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $param),
                    'pageNumber' => $pageNumber,
                    'pageSize' => 10,
                    //'rowStyler' => 'YingshouRoomServiceListviewModule.rowStyler'
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
            $this->assign('getAp', $getAp);
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    //生成报数单界面
    public function generalview()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        //当前日期
        $this->assign('getDate', date('Y-m-d'));
        //当前午别
        $this->assign('getAp', $this->getAp());
        $this->display($moduleName . '/generalview');
    }

    /**
     * 从订单表中生成送餐外送结账单
     */
    public function roomCalculate()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //结账日期
        $roomDate = $_REQUEST['room_date'];
        //结账午别
        $roomAp = $_REQUEST['room_ap'];
        //判断日期和无别是否为空，如果为空，就要跳出，显示结果
        if (empty($roomDate) || empty($roomAp)) {
            $res = array();
            $res['state'] = 2;
            $this->ajaxReturn($res);
        }

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();
        //连接订单dns
        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //连接结账库dns
        $reveueConnectDb = $this->connectReveueDb($roomDate);
        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);
        // 连接结账的数据库
        $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);

        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
        }

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';

        //首先查询订单是否已经结账，如果有结账，就返回,只要有有个订单结账，就不能结账
        $where = array();
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['isjiezhang'] = 1;
        $where['company'] = $company; //测试不用
        $where['domain'] = $this->getDomain();
        $orderformResult = $orderformModel->where($where)->limit(1)->select();
        if (count($orderformResult) > 0) {
            //有订单已经结账，就不能再结账了
            $roomserviceresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $company . ' ' . $roomDate . $roomAp . '的订单已经结账，不能再结账了！';
            $data['datetime'] = date('Y-m-d H:i:s');
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $roomserviceresultModel->create();
            $roomserviceresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $res['sql'] = $orderformResult;
            //$this->ajaxReturn($res);
        }

        //判断是否有要结账的订单，如果没有，就跳出错误
        $where = array();
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $orderformResultCount = $orderformModel->where($where)->count();
        if ($orderformResultCount == 0) {
            //没有要结账的订单
            $roomserviceresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $company . ' ' . $roomDate . $roomAp . '的订单数量为零！';
            $data['datetime'] = date('Y-m-d H:i:s');
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $roomserviceresultModel->create();
            $roomserviceresultModel->add($data);
            $res = array();
            $res['state'] = 0;
            $this->ajaxReturn($res);
        }

        //查询出所有要结账的送餐员，保存到数组中
        $where = array();
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $sendnameResult = $orderformModel->distinct(true)->where($where)->field('sendname')->select();

        //初始化送餐员统计的数组变量
        $sendnameTotalMoney = array();
        $sendnameJiezhangMoney = array();
        //遍历送餐员统计
        foreach ($sendnameResult as $value) {
            $sendname = $value['sendname'];
            $where = array();
            $where['custdate'] = $roomDate;
            $where['ap'] = $roomAp;
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $where['sendname'] = $sendname;
            //查询订单数据
            $orderformResult = $orderformModel->where($where)->select();
            foreach ($orderformResult as $orderform) {
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $ordergoodsResult = $ordergoodsModel->where($where)->select();
                $goodsMoney = 0;
                foreach ($ordergoodsResult as $ordergoods) {
                    $goodsMoney += $ordergoods['number'] * $ordergoods['price'];
                }
                //判断有下，赋值
                if (empty($sendnameTotalMoney[$sendname])) {
                    $sendnameTotalMoney[$sendname] = 0;
                }

                $sendnameTotalMoney[$sendname] += $goodsMoney;

                //从orderpayment获取支付金额
                $orderpaymentMoney = 0;
                $orderpaymentName = '';
                $orderpaymentResult = $orderpaymentModel->where($where)->select();
                foreach ($orderpaymentResult as $orderpayment) {
                    $orderpaymentMoney += $orderpayment['money'];
                    $orderpaymentName = $orderpayment['name'];
                }
                $sendnameJiezhangMoney[$sendname] += $orderpaymentMoney;

                //从活动中获取营销金额
                $orderactivityMoney = 0;
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $where['name'] = array('NOT IN', array('服务费'));
                $orderactivityResult = $orderactivityModel->where($where)->select();
                foreach ($orderactivityResult as $orderactivity) {
                    $orderactivityMoney += $orderactivity['money'];
                }
                $sendnameJiezhangMoney[$sendname] += $orderactivityMoney;

                //将金额保存在订单的结账金额中
                if (($orderpaymentMoney + $orderactivityMoney) > 0) {
                    $data = array();
                    $data['jiezhangmoney'] = $orderpaymentMoney + $orderactivityMoney;
                    if ($orderpaymentName == '支付宝') {
                        $data['needjiezhang'] = 1;
                    }
                    if ($orderpaymentName == '美支付') {
                        $data['needjiezhang'] = 1;
                    }
                    if ($orderpaymentName == '饿支付') {
                        $data['needjiezhang'] = 1;
                    }
                    $orderformModel->where($where)->save($data);
                } else {
                    $data = array();
                    $data['jiezhangmoney'] = 0;
                    $orderformModel->where($where)->save($data);

                }
            }
        }

        //删除交集外的送餐员
        $where = array();
        $where['date'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $oldsendnameResult = $roomserviceModel->field('name')->where($where)->select();
        foreach ($oldsendnameResult as $name) {
            if (!in_array($name['name'], $sendnameResult)) {
                $where = array();
                $where['name'] = $name['name'];
                $where['date'] = $roomDate;
                $where['ap'] = $roomAp;
                $where['company'] = $company;
                $where['domain'] = $this->getDomain();
                $roomserviceModel->where($where)->delete();
            }
        }

        //保存送餐员的数据到结账表中roomservice 和 roomserviceaccount客户表
        foreach ($sendnameResult as $value) {
            $sendname = $value['sendname'];
            //保存到结账表中
            $data = array();
            $data['name'] = $sendname;
            if ($sendnameTotalMoney[$sendname] == 0) {
                continue;
            }
            //如果金额为零，不用结账
            $data['totalmoney'] = $sendnameTotalMoney[$sendname];
            $data['jiezhangmoney'] = $sendnameJiezhangMoney[$sendname];
            $data['date'] = $roomDate;
            $data['ap'] = $roomAp;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $where = array();
            $where['name'] = $sendname;
            $where['date'] = $roomDate;
            $where['ap'] = $roomAp;
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $roomserviceResult = $roomserviceModel->where($where)->find();
            if (!empty($roomserviceResult)) {
                $roomserviceid = $roomserviceResult['roomserviceid'];
                $data['update_time'] = date('H:i:s');
                $where['roomserviceid'] = $roomserviceid;
                $roomserviceModel->where($where)->save($data);
            } else {
                $data['create_time'] = date('H:i:s');
                $roomserviceModel->create();
                $roomserviceid = $roomserviceModel->add($data);
            }
        }

        $res = array();
        $res['state'] = 1;
        $this->ajaxReturn($res);
    }

    /**
     * 查看送餐员结账的订单
     */
    public function checkOrder()
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
            $company = '怀南';

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getDbAp();

            $roomDate = $_REQUEST['room_date'];
            $roomAp = $_REQUEST['room_ap'];

            $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //如果不是，就要选择备份库
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            } else {
                //连接当前库和表
                $orderformModel = D('orderform');
                $ordergoodsModel = D('orderproducts');
                $orderpaymentModel = D('orderpayment');
            }

            // 生成list字段列表
            $listFields = $focus->orderformListFields;
            // 模块的ID
            $moduleId = 'orderformid';

            //送餐员姓名
            $sendname = $_REQUEST['name'];
            if (empty($sendname)) {
                $sendname = '';
            }

            // 建立查询条件
            $where = array();
            $where['sendname'] = $sendname;
            $where['company'] = $company;
            $where['custdate'] = $roomDate;
            $where['ap'] = $roomAp;
            $where['domain'] = $this->getDomain();

            $total = $orderformModel->where($where)->count(); // 查询满足要求的总记录数

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
            array_unshift($selectFields, 'custdate');

            $listResult = $orderformModel->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("orderformid asc")->select(); //lastdatetime desc,

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $orderformModel->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->orderformListFields;
            // 模块的ID
            $moduleId = 'orderformid';

            $roomDate = $_REQUEST['room_date'];
            $roomAp = $_REQUEST['room_ap'];
            $sendname = $_REQUEST['name'];
            $this->assign('sendname', $sendname);

            $param = array(
                'room_date' => $roomDate,
                'room_ap' => $roomAp,
                'name' => $sendname,
            );

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/checkOrder', $param),
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
                'formatter' => $moduleName . 'CheckOrderModule.operate',
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
            $this->assign('pagetype', 1);
            //当前日期
            $this->assign('custdate', $roomDate);
            //当前午别
            $this->assign('custap', $roomAp);

            $this->display('YingshouRoomService/checkorder');
        }
    }

    /**
     * 送餐员的订单,返回其他信息
     */
    public function checkOrderGetPayment()
    {

        $data = array();
        //获取传入的数据
        $ordersn = $_REQUEST['ordersn'];
        $roomDate = $_REQUEST['room_date'];
        $roomAp = $_REQUEST['room_ap'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderactivityModel = D('orderactivity');
            $orderpaymentModel = D('orderpayment');
        }

        $data = array();
        $where = array();
        $where['ordersn'] = $ordersn;

        $i = 0;
        $paymentResult = $orderpaymentModel->where($where)->select();
        foreach ($paymentResult as $payment) {
            $data[] = array(
                'id' => $i + 1,
                'code' => $payment['paymentid'],
                'name' => $payment['name'],
                'money' => $payment['money'],
            );
        }

        $orderactivityResult = $orderactivityModel->where($where)->select();
        foreach ($orderactivityResult as $orderactivity) {
            $data[] = array(
                'id' => $i + 1,
                'code' => $orderactivity['activityid'],
                'name' => $orderactivity['name'],
                'money' => $orderactivity['money'],
            );
        }

        $this->ajaxReturn($data);
    }

    /* 弹出客户支付选择窗口 */
    public function popupPaymentMgrview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

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
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
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
            //array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult,'sql'=>$popupModule->getLastSql());

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'paymentmgr';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupPaymentMgrFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popuppaymentmgrview'),
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
                'formatter' =>  'YingshouRoomServicePopupPaymentMgrviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouRoomService/popuppaymentmgrview');
        }
    }

    // 返回结账从表的内容:产品，活动促销
    public function get_slave_table($record, $roomDate, $roomAp)
    {
        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();


        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderproductsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderactivityModel = D('orderactivity');
            $orderpaymentModel = D('orderpayment');
        }

        $where = array();
        $where['ordersn'] = $record;
        // 取得产品信息
        $orderproducts = $orderproductsModel->field('orderformid,code,name,shortname,price,number,money')->where($where)->select();
        $this->assign('orderproducts', $orderproducts);

        //促销表活动表
        $activity = $orderactivityModel->where($where)->select();
        $this->assign('orderactivity', $activity);

        //获取活动金额
        $activitymoney = $orderactivityModel->where($where)->sum('money');
        $this->assign('activitymoney', $activitymoney);

        //取得支付表
        $orderpaymentResult = $orderpaymentModel->where($where)->select();
        $this->assign('roomserviceaccounts', $orderpaymentResult);

        //去掉支付金额
        $orderpaymentmoney = $orderpaymentModel->where($where)->sum('money');
        $this->assign('orderpaymentmoney', $orderpaymentmoney);

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
        $roomserviceresultModel = M("roomserviceresult", " ", $connectionDb);

        $where = array();
        $where['domain'] = $this->getDomain();
        // 返回模块的记录
        $roomserviceresult = $roomserviceresultModel->where($where)->select();

        $result = '';
        foreach ($roomserviceresult as $value) {
            $result .= "<p>" . $value['result'] . "</p>";
        }

        $this->assign('result', $result);
        $this->display($moduleName . '/resultview');
    }

    //根据客户代码，查询客户支付名称
    public function getAccountsByCode()
    {
        $code = $_REQUEST['code'];
        $accountsModel = D('PaymentMgr');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $accounts = $accountsModel->field('name')->where($where)->find();
        $this->ajaxReturn($accounts, 'JSON');
    }

    // 查看结账数据的页面
    public function paymentEditview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();


        $roomDate = $_REQUEST['room_date'];
        $roomAp = $_REQUEST['room_ap'];

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where['ordersn'] = $record;

        // 返回模块的行记录
        $result = $orderformModel->where($where)->find();

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);
        $this->assign('name', $result['sendname']);
        $this->assign('custdate', $roomDate);
        $this->assign('custap', $roomAp);

        // 返回从表的内容
        $this->get_slave_table($record, $roomDate, $roomAp);
        $this->display('YingshouRoomService' . '/paymenteditview');
    }

    // 批量结账
    public function batchPaymentEditview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();

        $roomDate = $_REQUEST['room_date'];
        $roomAp = $_REQUEST['room_ap'];

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        //送餐员姓名
        $sendname = $_REQUEST['sendname'];

        // 建立查询条件
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where[] = 'totalmoney <> jiezhangmoney';
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();

        // 返回模块的行记录
        $result = $orderformModel->where($where)->select();

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);
        $this->assign('name', $sendname);
        $this->assign('custdate', $roomDate);
        $this->assign('custap', $roomAp);

        // 返回从表的内容
        $this->get_slave_table($record, $roomDate, $roomAp);
        $this->display('YingshouRoomService' . '/batchpaymenteditview');
    }
    //保存数据
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        //结账日期
        $roomDate = $_REQUEST['custdate'];
        //结账午别
        $roomAp = $_REQUEST['custap'];
        //判断日期和无别是否为空，如果为空，就要跳出，显示结果
        if (empty($roomDate) || empty($roomAp)) {
            $res = array();
            $res['state'] = 2;
            $this->ajaxReturn($res);
        }

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();

        //连接订单dns
        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //连接结账库dns
        $reveueConnectDb = $this->connectReveueDb($roomDate);
        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库

        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $orderpaymentModel = D('orderpayment');
        }

        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);

        $ordersn = $_REQUEST['record'];
        $jiezhangmoney = $_REQUEST['paymentjiezhangmoney'];

        $where = array();
        $where['ordersn'] = $ordersn;

        //保存结账金额到订单中
        $data = array();
        $data['jiezhangmoney'] = $jiezhangmoney;
        $orderformModel->where($where)->save($data);

        //获取订单号orderformid
        $orderformResult = $orderformModel->field('orderformid')->where($where)->find();

        //先删除订单支付表中的数据
        $orderpaymentModel->where($where)->delete();
        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            if (empty($note)) {
                $note = '营收结账输入';
            }
            $data = array();
            $data['paymentid'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = $note;
            $data['date'] = date('Y-m-d H:i:s');
            $data['orderformid'] = $orderformResult['orderformid'];
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($code) && !empty($name)) {
                $orderpaymentModel->create();
                $orderpaymentModel->add($data);
            }
        };

        //这个金额是要累计的,所以累计计算送餐员的交账金额
        $where = array();
        $where['sendname'] = $_REQUEST['name'];
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();
        $turnover = $orderformModel->where($where)->sum('jiezhangmoney');

        // 连接结账的数据库
        $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $where = array();
        $where['name'] = $_REQUEST['name'];
        $where['date'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();

        $data = array();
        $data['jiezhangmoney'] = $turnover;
        $roomserviceModel->where($where)->save($data);

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '测试';

        // 生成查看的url
        $detailviewUrl = U("$moduleName/checkorder", array(
            'name' => $_REQUEST['name'], 'room_date' => $roomDate,
            'room_ap' => $roomAp,
            'rowIndex' => $_REQUEST['rowIndex'], 'pagetype' => $pagetype,
        ));
        $return = $detailviewUrl;
        $info = array();
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn($info);
    }

    //送餐员应交账款
    public function payable()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        $roomDate = $_REQUEST['room_date'];
        $roomAp = $_REQUEST['room_ap'];

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);

        //计算应该用哪个库和表
        if ($currentDate !== $roomDate) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderpaymentModel = D('orderpayment');
        }

        //午别不同,也必须连接下午
        if ($currentAp != $roomAp) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        }

        // 模块的ID
        $moduleId = 'orderformid';

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';

        //送餐员姓名
        $sendname = $_REQUEST['sendname'];

        // 建立查询条件
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();

        //返回订单号
        $listResult = $orderformModel->field('ordersn')->where($where)->select();

        $paymentArray = array();
        $alreadyPaymentMoney = 0;
        //查询支付
        foreach ($listResult as $value) {
            $where = array();
            $where['ordersn'] = $value['ordersn'];
            $paymentResult = $orderpaymentModel->field('name,money')->where($where)->select();
            foreach ($paymentResult as $paymentValue) {
                if (empty($paymentArray[$paymentValue['name']])) {
                    $paymentArray[$paymentValue['name']] = $paymentValue['money'];
                } else {
                    $paymentArray[$paymentValue['name']] += $paymentValue['money'];
                }
                $alreadyPaymentMoney += $paymentValue['money'];
            }
        }

        $totalpaymentArray = array();
        //已经支付金额汇总
        $totalpaymentArray['已经支付金额'] = $alreadyPaymentMoney;

        //订单总额
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();
        $totalmoney = $orderformModel->where($where)->sum('totalmoney');
        $totalpaymentArray['订单总金额'] = $totalmoney;

        $payment = array();
        $payment[] = $paymentArray;
        $payment[] = $totalpaymentArray;

        $this->assign('payment', $payment);
        $this->display($moduleName . '/payableview');
    }

    /**
     * 返回选择送餐员
     */
    public function getSendnameView()
    {
        $sendnameModel = D('sendnamemgr');
        $where = array();
        $where['domain'] = $this->getDomain();
        $sendnamemgrResult = $sendnameModel->field('name')->where($where)->select();
        $this->assign('sendnamemgr', $sendnamemgrResult);
        $this->display('selectsendname');
    }

    /**
     * 设置选择对送餐员
     */
    public function setSendname()
    {
        $sendname = $_REQUEST['sendname'];
        $ordersn = $_REQUEST['ordersn'];

        $orderformModel = D('orderform');
        $where = array();
        $where['ordersn'] = $ordersn;
        $data = array();
        $data['sendname'] = $sendname;
        $orderformModel->where($where)->save($data);
    }

}
