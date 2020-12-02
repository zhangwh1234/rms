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

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            //结账日期
            $getDate = $_REQUEST['getDate'];
            $roomDate = $getDate;
            //结账午别
            $getAp = $_REQUEST['getAp'];

            $domain = $this->getDomain();

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate) || empty($getAp)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $reveueConnectDb = $this->connectReveueDb($roomDate);
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            // 连接结账的数据库
            if ($roomDate == $currentDate) {
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
                        $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
                        break;
                }
            } else {
                $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
            }

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = $getDate;
            $where['ap'] = $getAp;
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();

            $total = $roomserviceModel->where($where)->count(); // 查询满足要求的总记录数

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

            $listResult = $roomserviceModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order(" (totalmoney -jiezhangmoney) desc,name")->select(); //lastdatetime desc,
            //判断结账金额和交账金额是否相等，做提示用
            foreach ($listResult as $key => $value) {
                if ($value['totalmoney'] != $value['jiezhangmoney']) {
                    $listResult[$key]['isShow'] = 1; //显示颜色
                } else {
                    $listResult[$key]['isShow'] = 0; //不显示颜色
                }
            }

            $orderHandleArray = array();
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $roomserviceModel->getLastSql());
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
        $domain = $this->getDomain();

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
        if ($roomDate == $currentDate) {
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
                    $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
                    break;
            }
        } else {
            $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        }

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

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $username = $this->userInfo['truename'];

        /** 将堂口输入拷贝到结账堂口中 */
        $doorbillModel = M("doorbill_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $doorbillpayModel = M("doorbillpay_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $where = array();
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $where['noupdate'] = 1;
        //清除以前到记录
        $doorbillModel->where($where)->delete();
        $salepayment = array();
        $saletotalmoney = 0;
        //查询堂口数据
        $where = array();
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $diningsaleResult = $diningsaleModel->where($where)->select();
        foreach ($diningsaleResult as $diningValue) {
            $saletotalmoney += $diningValue['money'];
            $where = array();
            $where['diningsaleid'] = $diningValue['diningsaleid'];
            $diningsalepaymentResult = $diningsalepaymentModel->where($where)->select();
            foreach ($diningsalepaymentResult as $diningpaymentValue) {
                if (empty($salepayment[$diningpaymentValue['name']])) {
                    $salepayment[$diningpaymentValue['name']] = 0;
                }
                $salepayment[$diningpaymentValue['name']] += $diningpaymentValue['money'];
            }
        }
        //保存到结账的堂口数据中
        $data = array();
        $data['code'] = '00';
        $data['name'] = $company;
        $data['money'] = $saletotalmoney;
        $data['operator'] = $username;
        $data['date'] = date('Y-m-d');
        $data['ap'] = $this->getAp();
        $data['company'] = $company;
        $data['domain'] = $this->getDomain();
        $data['noupdate'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s');
        $doorbillModel->create();
        $doorbillid = $doorbillModel->add($data);
        //保存到堂口支付表中
        foreach ($salepayment as $key => $value) {
            $data = array();
            $data['doorbillid'] = $doorbillid;
            $data['code'] = '';
            $data['name'] = $key;
            $data['money'] = $value;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $doorbillpayModel->create();
            $doorbillpayModel->add($data);
        }

        /************************************ */

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
            $this->ajaxReturn($res);
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
            //初始化计算送餐员的数据
            $sendnameTotalMoney[$sendname] = 0;
            $sendnameJiezhangMoney[$sendname] = 0;
            //计算条件
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
                $sendnameTotalMoney[$sendname] += $goodsMoney;

                //从原来的支付表和活动表中获取数据
                //改成从财务表中获取数据
                $jiezhangmoney = 0;
                $orderfinanceResult = $orderfinanceModel->where($where)->select();
                foreach ($orderfinanceResult as $orderfinance) {
                    $sendnameJiezhangMoney[$sendname] += $orderfinance['money'];
                    $jiezhangmoney += $orderfinance['money'];
                }

                //从orderpayment获取支付金额
                $orderpaymentMoney = 0;
                $orderpaymentName = '';
                $orderpaymentResult = $orderpaymentModel->where($where)->select();
                foreach ($orderpaymentResult as $orderpayment) {
                    $orderpaymentMoney += $orderpayment['money'];
                    $orderpaymentName = $orderpayment['name'];
                }

                //从活动中获取营销金额
                $orderactivityMoney = 0;
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $where['name'] = array('NOT IN', array('服务费'));
                $orderactivityResult = $orderactivityModel->where($where)->select();
                foreach ($orderactivityResult as $orderactivity) {
                    $orderactivityMoney += $orderactivity['money'];
                }

                //将金额保存在订单的结账金额中
                $data = array();
                $data['jiezhangmoney'] = $jiezhangmoney;
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $orderformModel->where($where)->save($data);

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
            // $where['totalmoney'] = array('gt', 0);
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

            $listResult = $orderformModel->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("jiezhangmoney,orderformid asc")->select(); //lastdatetime desc,

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
    public function checkOrderGetFinance()
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
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderactivityModel = D('orderactivity');
            $orderfinanceModel = D('orderfinance');
        }

        $data = array();
        $where = array();
        $where['ordersn'] = $ordersn;

        $i = 0;
        $orderfinanceResult = $orderfinanceModel->where($where)->select();
        foreach ($orderfinanceResult as $finance) {
            $data[] = array(
                'id' => $i + 1,
                'code' => $finance['paymentid'],
                'name' => $finance['name'],
                'money' => $finance['money'],
            );
        }

        $this->ajaxReturn($data);
    }

    /* 弹出客户支付选择窗口 */
    public function popupPaymentMgrview()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $this->assign('company', $company);
        // 取得父窗口的表格行数
        $row = $_REQUEST['row'];
        $this->assign('row', $row); //返回点击的订购商品行
        $this->display('YingshouRoomService/selectpaymentmgrview');

        return;

        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称
            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

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
            $where['company'] = $company;
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

            $total = count($listResult);
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
                'formatter' => 'YingshouRoomServicePopupPaymentMgrviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouRoomService/popuppaymentmgrview');
        }
    }

    /* 批量处理的弹出客户支付选择窗口 */
    public function batchPopupPaymentMgrview()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $this->assign('company', $company);
        // 取得父窗口的表格行数
        $row = $_REQUEST['row'];
        $this->assign('row', $row); //返回点击的订购商品行
        $this->display('YingshouRoomService/batchSelectpaymentmgrview');

        return;
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
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderfinanceModel = D('orderfinance');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
        }

        $where = array();
        $where['ordersn'] = $record;
        // 取得产品信息
        $orderproducts = $orderproductsModel->field('orderformid,code,name,shortname,price,number,money')->where($where)->select();
        $this->assign('orderproducts', $orderproducts);

        //财务结算金额
        $orderfinance = $orderfinanceModel->where($where)->select();
        $this->assign('orderfinance', $orderfinance);

        //总金额 -- 财务结算
        $orderfinancemoney = $orderfinanceModel->where($where)->sum('money');
        $this->assign('orderfinancemoney', $orderfinancemoney);

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

    // 编辑结账数据的页面
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
        //判断结账金额>0,并且结账金额不等于订单金额，那么多显示一条支付表格
        if (($result['jiezhangmoney'] > 0) && ($result['jiezhangmoney'] != $result['totalmoney'])) {
            $this->assign('paymenttwoshow', 1);
        }
        $this->display('YingshouRoomService' . '/paymenteditview');
    }

    //查看结账数据的页面
    public function payment_detailview()
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
        //判断结账金额>0,并且结账金额不等于订单金额，那么多显示一条支付表格
        if (($result['jiezhangmoney'] > 0) && ($result['jiezhangmoney'] != $result['totalmoney'])) {
            $this->assign('paymenttwoshow', 1);
        }
        $this->display('YingshouRoomService' . '/payment_detailview');
    }

    //支付详情
    // 查看结账数据的页面
    public function paymentDetailview()
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

            $roomDate = $_REQUEST['room_date'];
            $roomAp = $_REQUEST['room_ap'];

            $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //如果不是，就要选择备份库
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
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
            $listFields = $focus->paymentDetailListFields;

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
            $where['totalmoney'] = array('gt', 0);
            $where['domain'] = $this->getDomain();
            $domain = $this->getDomain();
            //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
            //查询所有支付类型
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                $sql = " select ordersn from rms_orderform_" . substr($roomDate, 5, 2) .
                    " as a
                          where a.sendname='$sendname' and a.company='$company'
                         and a.custdate = '$roomDate' and a.ap = '$roomAp' and a.domain = '$domain' order by a.orderformid ";
                $orderformResult = $orderformModel->query($sql);
            } else {
                $sql = " select ordersn from rms_orderform" .
                    " as a
                          where a.sendname='$sendname' and a.company='$company'
                         and a.custdate = '$roomDate' and a.ap = '$roomAp' and a.domain = '$domain' order by a.orderformid ";

                $orderformResult = $orderformModel->query($sql);
            }

            $orderfinanceList = array();
            $orderfinanceTotalMoney = 0; //总的合计金额
            foreach ($orderformResult as $value) {
                $ordersn = $value['ordersn'];
                $orderfinanceMoney = 0; //合计金额
                //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
                //如果不是，就要选择备份库
                if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                    $sql = " select a.code,a.name,a.money,b.address,b.ordertxt,b.sendname,b.company from rms_orderform_" . substr($roomDate, 5, 2) .
                    " as b
                         left join rms_orderfinance_" . substr($roomDate, 5, 2) . " as a on a.ordersn = b.ordersn where b.sendname='$sendname' and b.company='$company'
                         and b.custdate = '$roomDate' and b.ap = '$roomAp' and b.domain = '$domain' and b.ordersn = '$ordersn'  ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                } else {
                    $sql = " select a.code,a.name,a.money,b.address,b.ordertxt,b.sendname,b.company from rms_orderfinance " .
                        " as a
                         left join rms_orderform  as b on a.ordersn = b.ordersn where b.sendname='$sendname' and b.company='$company'
                         and b.custdate = '$roomDate' and b.ap = '$roomAp' and b.domain = '$domain' and b.ordersn = '$ordersn'  ";
                    $orderfinanceResult = $orderfinanceModel->query($sql);
                }
                foreach ($orderfinanceResult as $finance) {
                    $orderfinanceMoney += $finance['money'];
                    $listResult[] = $finance;
                }
                if ($orderfinanceMoney > 0) {
                    $hejiArray = array(
                        'code' => '',
                        'name' => '',
                        'money' => $orderfinanceMoney,
                        'address' => '金额合计',
                        'ap' => '',
                        'company' => '',
                    );
                    $listResult[] = $hejiArray;
                };
                $orderfinanceTotalMoney += $orderfinanceMoney;
            }

            if ($orderfinanceTotalMoney > 0) {
                $hejiArray = array(
                    'code' => '',
                    'name' => '',
                    'money' => '总合计金额',
                    'address' => $orderfinanceTotalMoney,
                    'ap' => '',
                    'company' => '',
                );
                $listResult[] = $hejiArray;
            };

            //查询总的订单金额
            $orderformTotalMoney = 0;
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                $sql = " select sum(b.totalmoney) as totalmoney from rms_orderform_" . substr($roomDate, 5, 2) .
                    " as b
                         where b.sendname='$sendname' and b.company='$company'
                         and b.custdate = '$roomDate' and b.ap = '$roomAp' and b.domain = '$domain'   ";
                $orderformTotalMoney = $orderformModel->query($sql);
            } else {
                $sql = " select sum(b.totalmoney) a totalmoney from rms_orderfinance " .
                    " as b
                         where b.sendname='$sendname' and b.company='$company'
                         and b.custdate = '$roomDate' and b.ap = '$roomAp' and b.domain = '$domain' ";
                $orderformTotalMoney = $orderformModel->query($sql);
            }
            if ($orderformTotalMoney[0]['totalmoney'] > 0) {
                $hejiArray = array(
                    'code' => '',
                    'name' => '',
                    'money' => '订单总金额',
                    'address' => $orderformTotalMoney[0]['totalmoney'],
                    'ap' => '',
                    'company' => '',
                );
                $listResult[] = $hejiArray;
            };

            $total = $orderformModel->where($where)->count(); // 查询满足要求的总记录数

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $sql);
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
            $listFields = $focus->paymentDetailListFields;
            // 模块的ID
            $moduleId = 'orderformid';

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            // 取得返回的是列表还是查询列表
            $returnAction = $_REQUEST['returnAction'];
            $this->assign('returnAction', $returnAction);

            $roomDate = $_REQUEST['room_date'];
            $roomAp = $_REQUEST['room_ap'];
            $sendname = $_REQUEST['sendname'];

            $param = array(
                'room_date' => $roomDate,
                'room_ap' => $roomAp,
                'name' => $sendname,
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/paymentDetailview', $param),
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
                'formatter' => 'PaymentDetailviewModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            $this->assign('info', $result);
            $this->assign('record', $record);
            $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
            $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
            $this->assign('pagetype', $_REQUEST['pagetype']);
            $this->assign('sendname', $sendname);
            $this->assign('custdate', $roomDate);
            $this->assign('custap', $roomAp);

            // 返回从表的内容
            $this->get_slave_table($record, $roomDate, $roomAp);

            $this->display('YingshouRoomService' . '/paymentdetailview');
        }
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
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderfinanceModel = D("orderfinance");
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
        //$where[] = " jiezhangmoney = 0 ";
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();

        // 返回模块的行记录
        $result = $orderformModel->where($where)->select();

        // 查询支付，同时支付返回，支持有支付也能结账
        $orderResult = array();
        foreach ($result as $value) {
            $where = array();
            $where['ordersn'] = $value['ordersn'];
            $orderfinanceResult = $orderfinanceModel->where($where)->select();
            if ($orderfinanceResult) {
                $value['finance'] = $orderfinanceResult;
            }
            $orderResult[] = $value;
        }

        $this->assign('info', $orderResult);
        $this->assign('record', $record);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);
        $this->assign('name', $sendname);
        $this->assign('custdate', $roomDate);
        $this->assign('custap', $roomAp);

        // 返回从表的内容
        //$this->get_slave_table($record, $roomDate, $roomAp);
        $this->display('YingshouRoomService' . '/batchpaymenteditview');
    }

    //保存数据
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $domain = $this->getDomain();
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
            $orderproductsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderfinanceModel = D('orderfinance');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
        }

        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);

        $ordersn = $_REQUEST['record'];
        $jiezhangmoney = $_REQUEST['paymentjiezhangmoney'];

        $where = array();
        $where['ordersn'] = $ordersn;
        //获取订单号orderformid
        $orderformResult = $orderformModel->field('orderformid,sendname')->where($where)->find();

        $where = array();
        $where['ordersn'] = $ordersn;
        //先删除订单支付表中的数据
        $orderfinanceModel->where($where)->delete();
        //保存在订单支付表中
        $jiezhangmoney = 0;
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i < $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                // 支付编号
                $data['financeid'] = 'finance_' . date('YmdHis') . $domain . $i;
            }
            if (empty($note)) {
                $note = '营收结账输入';
            }

            if (empty($code)) {
                $code = '00';
            }
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = $note;
            $data['date'] = date('Y-m-d H:i:s');
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($name)) {
                $orderfinanceModel->create();
                $orderfinanceModel->add($data);
            }
            $jiezhangmoney += $money;
        };

        $where = array();
        $where['ordersn'] = $ordersn;

        //保存结账金额到订单中
        $data = array();
        $data['jiezhangmoney'] = $jiezhangmoney;
        $orderformModel->where($where)->save($data);

        //这个金额是要累计的,所以累计计算送餐员的交账金额
        $where = array();
        $where['sendname'] = $orderformResult['sendname'];
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $turnover = $orderformModel->where($where)->sum('jiezhangmoney');

        // 连接结账的数据库
        if ($roomDate == $currentDate) {
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
                    $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
                    break;
            }
        } else {
            $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        }
        $where = array();
        $where['name'] = $orderformResult['sendname'];
        $where['date'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();

        $data = array();
        $data['jiezhangmoney'] = $turnover;
        $roomserviceModel->where($where)->save($data);

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

    //批量结账的单独保存数据
    public function singleUpdate()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $domain = $this->getDomain();
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
            $orderproductsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderpaymentModel = M("orderpayment_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderactivityModel = M("orderactivity_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderproductsModel = D('orderproducts');
            $orderfinanceModel = D('orderfinance');
            $orderpaymentModel = D('orderpayment');
            $orderactivityModel = D('orderactivity');
        }

        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);

        $ordersn = $_REQUEST['record'];
        $jiezhangmoney = $_REQUEST['paymentjiezhangmoney'];

        $where = array();
        $where['ordersn'] = $ordersn;
        //获取订单号orderformid
        $orderformResult = $orderformModel->field('orderformid,sendname')->where($where)->find();

        $where = array();
        $where['note'] = '批量';
        $where['ordersn'] = $ordersn;
        //先删除订单支付表中的数据
        $orderfinanceModel->where($where)->delete();
        $sql = $orderfinanceModel->getLastSql();
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i < $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
                // 支付编号
                $data['financeid'] = 'finance_' . date('YmdHis') . $domain . $i;
            }
            
            if (empty($code)) {
                $code = '00';
            }
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = '批量';
            $data['date'] = date('Y-m-d H:i:s');
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($name)) {
                $orderfinanceModel->create();
                $orderfinanceModel->add($data);
            }
            break;
        };

        $where = array();
        $where['ordersn'] = $ordersn;

        //保存结账金额到订单中
        $data = array();
        $data['jiezhangmoney'] = $jiezhangmoney;
        $orderformModel->where($where)->save($data);

        //这个金额是要累计的,所以累计计算送餐员的交账金额
        $where = array();
        $where['sendname'] = $orderformResult['sendname'];
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $turnover = $orderformModel->where($where)->sum('jiezhangmoney');

        // 连接结账的数据库
        if ($roomDate == $currentDate) {
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
                    $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
                    break;
            }
        } else {
            $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        }
        $where = array();
        $where['name'] = $orderformResult['sendname'];
        $where['date'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();

        $data = array();
        $data['jiezhangmoney'] = $turnover;
        $roomserviceModel->where($where)->save($data);

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
        $info['sql'] = $sql;
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
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $orderfinanceModel = D('orderfinance');
        }

        //午别不同,也必须连接下午
        if ($currentAp != $roomAp) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderfinanceModel = M("orderfinance_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        }

        // 模块的ID
        $moduleId = 'orderformid';

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

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

        $financeArray = array();
        $alreadyFinanceMoney = 0;
        //查询支付
        foreach ($listResult as $value) {
            $where = array();
            $where['ordersn'] = $value['ordersn'];
            $financeResult = $orderfinanceModel->field('name,money')->where($where)->select();
            foreach ($financeResult as $financeValue) {
                if (empty($financeArray[$financeValue['name']])) {
                    $financeArray[$financeValue['name']] = $financeValue['money'];
                } else {
                    $financeArray[$financeValue['name']] += $financeValue['money'];
                }
                $alreadyfinanceMoney += $financeValue['money'];
            }
        }

        $totalfinanceArray = array();
        //已经支付金额汇总
        $totalfinanceArray['已经支付金额'] = $alreadyfinanceMoney;

        //订单总额
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['domain'] = $this->getDomain();
        $totalmoney = $orderformModel->where($where)->sum('totalmoney');
        $totalfinanceArray['订单总金额'] = $totalmoney;

        $finance = array();
        $finance[] = $financeArray;
        $finance[] = $totalfinanceArray;

        $this->assign('finance', $finance);
        $this->display($moduleName . '/payableview');
    }

    /**
     * 返回选择送餐员
     */
    public function getSendnameView()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        $sendnameModel = D('sendnamemgr');
        $where = array();
        $where['company'] = $company;
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
        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();
        $roomDate = $_REQUEST['custdate'];
        $roomAp = $_REQUEST['custap'];

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $roomDate) || ($currentAp != $roomAp)) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        $sendname = $_REQUEST['sendname'];
        $ordersn = $_REQUEST['ordersn'];

        $where = array();
        $where['ordersn'] = $ordersn;
        $data = array();
        $data['sendname'] = $sendname;
        $orderformModel->where($where)->save($data);
    }

    /**
     * 获取分公司支付方式
     */
    public function getCompanyPayment()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        $getCompany = $company;
        $domain = $this->getDomain();

        //如果有缓存，就直接返回缓存
        $payment_cache = F('yingshouroomserver' . $company . $domain);
        if (!empty($payment_cache)) {
            $this->ajaxReturn($payment_cache, 'JSON');
        }

        $revparType = $this->getRevparType();

        $paymentmgrModel = D('paymentmgr');
        $where = array();
        if ($revparType == 'Finance') {

            if (!empty($getCompany)) {
                if ($getCompany == '总部') {
                    $where['company'] = '总部';
                } else {
                    $where['company'] = $getCompany;
                }
            }

        } else {
            if (!empty($company)) {
                $where['company'] = array(
                    array('EQ', '$company'),
                    array('EQ', '总部'),
                    'or',
                );
            }
        }
        $where['domain'] = $this->getDomain();
        $where['is_shenhe'] = 1; //只有经过审核的，才可以使用
        $paymentmgr = $paymentmgrModel->where($where)->select();

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
