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
        $rmsConnectDb = $this->connectRmsDb($roomDate);
        //连接结账库dns
        $reveueConnectDb = $this->connectReveueDb($roomDate);
        $roomserviceresultModel = M("roomserviceresult", " ", $reveueConnectDb);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if ($currentDate !== $roomDate) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderyingshouModel = M("orderyingshou_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //开启订单数据表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $orderyingshouModel = D('orderyingshou');
        }
        //午别不同,也必须连接下午
        if ($currentAp != $roomAp) {
            $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            $orderyingshouModel = M("orderyingshou_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
        }

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo ['department'];
        $company = '测试';

        //首先查询订单是否已经结账，如果有结账，就返回,只要有有个订单结账，就不能结账
        $where = array();
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;
        $where['isjiezhang'] = 1;
        //$where['company'] = $company;  *测试不用
        $where['domain'] = $this->getDomain();
        $orderformResult = $orderformModel->where($where)->limit(1)->select();
        //var_dump($orderformModel->getLastSql());
        if (count($orderformResult) > 0) {
            //有订单已经结账，就不能再结账了
            $roomserviceresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $company . ' ' . $roomDate . $roomAp . '的订单已经结账，不能再结账了！';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
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
        //$where['company'] = $company;  *测试不用
        $where['domain'] = $this->getDomain();
        $orderformResultCount = $orderformModel->where($where)->count();
        if ($orderformResultCount == 0) {
            //没有要结账的订单
            $roomserviceresultModel->where(1)->delete();
            $data = array();
            $data['result'] = $company . ' ' . $roomDate . $roomAp . '的订单数量为零！';
            $data['datetime'] = date('Y-m-d H:i:s');
            //$data['company'] = $company;
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
        //$where['company'] = $company;  *测试不用
        $where['domain'] = $this->getDomain();
        $sendnameResult = $orderformModel->distinct(true)->where($where)->field('sendname')->select();


        // 连接结账的数据库
        $roomserviceModel = M("roomservice_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $roomserviceaccountsModel = M("roomserviceaccounts_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $roomservicepromotionModel = M("roomservicepromotion_" . substr($roomDate, 5, 2), " ", $reveueConnectDb);
        $accountsModel =  D('accounts');
        $orderactivityModel = D('orderactivity');

        //初始化送餐员统计的数组变量
        $sendnameTotalMoney = array();
        $sendnameAccounts = array();  //客户金额
        $accountsName = array();      //客户名称
        $sendnamePromotion = array();  //促销数组
        $promotionName = array();   //促销名单
        //遍历送餐员统计
        foreach ($sendnameResult as $value) {
            $sendname = $value['sendname'];
            $where = array();
            $where['custdate'] = $roomDate;
            $where['ap'] = $roomAp;
            //$where['company'] = $company;  *测试不用
            $where['domain'] = $this->getDomain();
            $where['sendname'] = $sendname;
            //查询订单数据
            $orderformResult = $orderformModel->where($where)->select();
            foreach ($orderformResult as $orderform) {
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $ordergoodsResule = $ordergoodsModel->where($where)->select();
                $goodsMoney = 0;
                foreach ($ordergoodsResule as $ordergoods) {
                    $goodsMoney += $ordergoods['number'] * $ordergoods['price'];
                }
                //判断有下，赋值
                if (empty($sendnameTotalMoney[$sendname])) $sendnameTotalMoney[$sendname] = 0;
                $sendnameTotalMoney[$sendname] += $goodsMoney;
                //查询订单结账表rms_orderyingshou
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $orderyingshouResult = $orderyingshouModel->where($where)->select();
                foreach ($orderyingshouResult as $orderyingshou) {
                    //赋初值
                    if (empty($sendnameAccounts[$sendname][$orderyingshou['name']])) $sendnameAccounts[$sendname][$orderyingshou['name']] = 0;
                    $sendnameAccounts[$sendname][$orderyingshou['name']] += $orderyingshou['money'];
                    //保存客户名称到数组
                    if (!in_array($orderyingshou['name'], $accountsName)) {
                        $accountsName[] = $orderyingshou['name'];
                    }
                }
                //查询活动表，计算促销金额
                $where = array();
                $where['ordersn'] = $orderform['ordersn'];
                $orderactivityResult = $orderactivityModel->where($where)->select();
                foreach($orderactivityResult as $orderactivity){
                    //赋初值
                    if (empty($sendnamePromotion[$sendname][$orderactivity['name']])){
                        $sendnamePromotion[$sendname][$orderactivity['name']]['money'] = 0;
                    }
                    $sendnamePromotion[$sendname][$orderactivity['name']]['code'] =  $orderactivity['activityid'];
                    $sendnamePromotion[$sendname][$orderactivity['name']]['name'] =  $orderactivity['name'];
                    $sendnamePromotion[$sendname][$orderactivity['name']]['originmoney'] =  $orderactivity['money'];
                    $sendnamePromotion[$sendname][$orderactivity['name']]['money'] =+  $orderactivity['promotion'];
                    $sendnamePromotion[$sendname][$orderactivity['name']]['original'] =  $orderactivity['note'];
                    //保存促销名称到数组
                    if (!in_array($orderactivity['name'], $promotionName)) {
                        $promotionName[] = $orderactivity['name'];
                    }
                }
            }
        }

        //保存送餐员的数据到结账表中roomservice 和 roomserviceaccount客户表
        foreach ($sendnameResult as $value) {
            $sendname = $value['sendname'];
            //保存到结账表中
            $data = array();
            $data['name'] = $sendname;
            $data['totalmoney'] = $sendnameTotalMoney[$sendname];
            $data['date'] = $roomDate;
            $data['ap'] = $roomAp;
            //$data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $where = array();
            $where['name'] = $sendname;
            //$where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $roomserviceResult = $roomserviceModel->where($where)->find();
            if (!empty($roomserviceResult)) {
                $roomserviceid = $roomserviceResult['roomserviceid'];
                $data['update_time'] = date('H:i:s');
                $where['roomserviceid']= $roomserviceid;
                $roomserviceModel->where($where)->save($data);
            } else {
                $data['create_time'] = date('H:i:s');
                $roomserviceModel->create();
                $roomserviceid = $roomserviceModel->add($data);
            }
            //保存客户表
            $sendnameAccountsTotalMoney = 0;  //签单总额
            foreach ($accountsName as $account) {
                //获取客户代码
                $where = array();
                $where['name'] = $account;
                $accountsResult = $accountsModel->where($where)->find();
                if($accountsResult){
                    $accountsCode = $accountsResult['code'];
                }else{
                    $accountsCode = '';
                }
                $where = array();
                $where['roomserviceid'] = $roomserviceid;
                $where['name'] = $account;
                //$where['company'] = $company;
                //$where['domain'] = $this->getDomain();
                $data = array();
                $data['money'] = $sendnameAccounts[$sendname][$account];
                $data['code'] = $accountsCode;
                if (!(empty($sendnameAccounts[$sendname][$account]) || $sendnameAccounts[$sendname][$account] == 0)) {
                    $roomserviceaccountsResult = $roomserviceaccountsModel->where($where)->find();
                    if (!empty($roomserviceaccountsResult)) {
                        $roomserviceaccountsModel->where($where)->save($data);
                    } else {
                        $data['roomserviceid'] = $roomserviceid;
                        $data['name'] = $account;
                        $data['domain'] = $this->getDomain();
                        $roomserviceaccountsModel->create();
                        $roomserviceaccountsModel->add($data);
                    }
                }
                $sendnameAccountsTotalMoney = $sendnameAccountsTotalMoney + $sendnameAccounts[$sendname][$account];
            }
            //保存客户的签单
            $where = array();
            $where['roomserviceid'] = $roomserviceid;
            $data = array();
            $data['note'] = $sendnameAccountsTotalMoney;
            $roomserviceModel->where($where)->save($data);

            //保存促销的数据
            $sendnamePromotionTotalMoney = 0;  //签单总额
            foreach ($promotionName as $promotion) {
                $where = array();
                $where['roomserviceid'] = $roomserviceid;
                $where['name'] = $promotion;
                //$where['company'] = $company;
                //$where['domain'] = $this->getDomain();
                $data = array();
                $data['money'] = $sendnamePromotion[$sendname][$promotion]['money'];
                $data['code'] = $sendnamePromotion[$sendname][$promotion]['code'];
                $data['originmoney'] = $sendnamePromotion[$sendname][$promotion]['money'];
                $data['original'] = $sendnamePromotion[$sendname][$promotion]['original'];
                if (!(empty($sendnamePromotion[$sendname][$promotion]['money']) || $sendnamePromotion[$sendname][$promotion]['money'] == 0)) {
                    $roomservicepromotionResult = $roomservicepromotionModel->where($where)->find();
                    if (!empty($roomservicepromotionResult)) {
                        $roomservicepromotionModel->where($where)->save($data);
                    } else {
                        $data['roomserviceid'] = $roomserviceid;
                        $data['name'] = $promotion;
                        $data['domain'] = $this->getDomain();
                        $roomservicepromotionModel->create();
                        $roomservicepromotionModel->add($data);
                    }
                }
                $sendnamePromotionTotalMoney = $sendnamePromotionTotalMoney + $sendnamePromotion[$sendname][$promotion]['money'];
            }
            //保存客户的签单
            $where = array();
            $where['roomserviceid'] = $roomserviceid;
            $data = array();
            $data['promotion'] = $sendnamePromotionTotalMoney;
            $roomserviceModel->where($where)->save($data);
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

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();

            $roomDate = $_REQUEST['room_date'];
            $roomAp = $_REQUEST['room_ap'];

            $rmsConnectDb = $this->connectRmsDb($roomDate);
            //计算应该用哪个库和表
            if ($currentDate !== $roomDate) {
                $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            } else {
                //连接当前库和表
                $orderformModel = D('orderform');
                $ordergoodsModel = D('orderproducts');
            }

            //午别不同,也必须连接下午
            if ($currentAp != $roomAp) {
                $orderformModel = M("orderform_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
                $ordergoodsModel = M("orderproducts_" . substr($roomDate, 5, 2), "rms_", $rmsConnectDb);
            }

            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');

            // 生成list字段列表
            $listFields = $focus->orderformListFields;
            // 模块的ID
            $moduleId = 'orderformid';

            //送餐员姓名
            $sendname = $_REQUEST['name'];

            // 建立查询条件
            $where = array();
            $where ['sendname'] = $sendname;
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            $total = $orderformModel->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }

            array_unshift($selectFields, $moduleId);

            $listResult = $orderformModel->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray ['total'] = $total;
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

            $param = array(
                'room_date' => $roomDate,
                'room_ap' => $roomAp,
                'name' => $sendname
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
                    'pageSize' => 10
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
                'formatter' => $moduleName . 'CheckOrderModule.operate'
            );


            //计算接线员的接单量
            // 接线员的姓名
            $userInfo = $_SESSION ['userInfo'];
            $name = $userInfo ['truename'];
            $where = array();
            $where['telname'] = $name;
            $telOrderNumber = $focus->where($where)->count();
            $telOrderNumber = '[' . $name . ']' . $telOrderNumber . '张订单';

            $this->assign('orderformOtherMsg', $telOrderNumber);
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
            $this->assign('cdate', date('Y-m-d'));
            //当前午别
            $this->assign('cap', $this->getAp());
            $this->display('YingshouRoomService/checkorder');
        }
    }

    /**
     * 送餐员的订单,返回其他信息
     */
    public function checkOrderGetOther()
    {

        $data = array(
            array(
                'id' => 1,
                'name' => '支付宝',
                'money' => -100
            ),
            array(
                'id' => 1,
                'name' => '英联',
                'money' => 10
            ),
            array(
                'id' => 1,
                'name' => '支付宝',
                'money' => -100
            ),
        );

        $this->ajaxReturn($data);
    }


    /* 弹出客户选择窗口 */
    public function popupAccountsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'YingshouAccounts';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
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
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset ($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $orderHandleArray ['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'YingshouAccounts';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupAccountsview'),
                    'pageNumber' => 1,
                    'pageSize' => 10
                )
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true
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
                'formatter' => $moduleName . 'PopupAccountsviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row', $row);  //返回点击的订购商品行

            $this->display('YingshouRoomService/popupAccountsview');
        }
    }

    /* 弹出赠卡选择窗口 */
    public function popupFreebieview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Freebie';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];

            // 生成list字段列表
            $listFields = $focus->popupFreebieFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
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
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset ($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $orderHandleArray ['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'FreebieMgr';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupFreebieFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupFreebieview'),
                    'pageNumber' => 1,
                    'pageSize' => 10
                )
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true
            );

            foreach ($listFields as $key => $value) {
                $header = L('freebie'.$key);
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
                'formatter' => $moduleName . 'PopupFreebieviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row', $row);  //返回点击的订购商品行

            $this->display('YingshouRoomService/popupFreebieview');
        }
    }

    /* 弹出餐券选择窗口 */
    public function popupMealticketview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'MealticketMgr';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];

            // 生成list字段列表
            $listFields = $focus->popupMealticketFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
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
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset ($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $orderHandleArray ['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'YingshouMealticket';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupMealticketFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupMealticketview'),
                    'pageNumber' => 1,
                    'pageSize' => 10
                )
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true
            );

            foreach ($listFields as $key => $value) {
                $header = L('mealticket'.$key);
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
                'formatter' => $moduleName . 'PopupMealticketviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row', $row);  //返回点击的订购商品行

            $this->display('YingshouRoomService/popupMealticketview');
        }
    }

    // 查看结账数据
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

        // 模块的ID
        $moduleNameId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST ['record'];

        // 重新设定订单历史查询的数据库
        $getDate = $_REQUEST ['getDate'];

        $connectionDb = $this->connectReveueDb($getdate);

        // 连接数据库
        $roomserviceModel = M("roomservice_" . substr($getDate, 5, 2), " ", $connectionDb);

        // 返回模块的记录
        $result = $roomserviceModel->where("roomserviceid=$record")->find();

        $this->assign('info', $result);
        $this->assign('record', $record);

        // 返回从表的内容
        $this->get_slave_table($record, $getDate);
        $this->display($moduleName . '/detailview');
    }

    // 返回结账从表的内容:签单;赠卡,餐券
    public function get_slave_table($record, $getdate)
    {
        $where = array();
        $where['ordersn'] = $record;
        // 取得产品信息
        $orderproducts_model = D('Orderproducts');
        $orderproducts = $orderproducts_model->field('orderformid,code,name,shortname,price,number,money')->where($where)->select();
        $this->assign('orderproducts', $orderproducts);

        //促销表活动表
        $orderactivity_model = D('OrderActivity');
        $activity = $orderactivity_model->where($where)->select();
        $this->assign('orderactivity',$activity);

        //获取活动金额
        $activitymoney = $orderactivity_model->where($where)->sum('money');
        $this->assign('activitymoney',$activitymoney);

        return;
        //链接数据库
        $connectionDb = $this->connectReveueDb($getdate);

        //定义查询条件
        $where = array();
        $where['roomserviceid'] = $record;

        //取得客户表
        $roomserviceaccountsModel = M("roomserviceaccounts_" . substr($getdate, 5, 2), " ", $connectionDb);
        $roomserviceaccountsResult = $roomserviceaccountsModel->where($where)->select();
        $this->assign('roomserviceaccounts', $roomserviceaccountsResult);

        //返回促销表
        $roomservicepromotionModel = M("roomservicepromotion_" . substr($getdate, 5, 2), " ", $connectionDb);
        $roomservicepromotionResult = $roomservicepromotionModel->where($where)->select();
        $this->assign('roomservicepromotion', $roomservicepromotionResult);

        //取得赠卡
        $roomservicefreebieModel = M('roomservicefreebie_' . substr($getdate, 5, 2), " ", $connectionDb);
        $roomservicefreebieResult = $roomservicefreebieModel->where($where)->select();
        $this->assign('roomservicefreebie', $roomservicefreebieResult);

        //取得赠券
        $roomservicemealticketModel = M('roomservicemealticket_' . substr($getdate, 5, 2), " ", $connectionDb);
        $roomservicemealticketResult = $roomservicemealticketModel->where($where)->select();
        $this->assign('roomservicemealticket', $roomservicemealticketResult);
    }

    /**
     * 保存客户表，赠卡表，餐券表等从表
     */
    public function update_slave_table($record,$getDate)
    {

        //处理订单
        $jiezhangmoney = $_REQUEST['paymentjiezhangmoney'];

        $orderformModel = D('orderform');
        $where = array();
        $where['ordersn'] = $record;
        $data = array();
        $data['jiezhangmoney'] = $jiezhangmoney;
        $orderformModel->where($where)->save($data);

        return;
        //链接数据库
        $connectionDb = $this->connectReveueDb($getDate);

        //定义查询条件
        $where = array();
        $where['roomserviceid'] = $record;

        //取得客户表
        $roomserviceaccountsModel = M("roomserviceaccounts_" . substr($getDate, 5, 2), " ", $connectionDb);
        $roomserviceaccountsResult = $roomserviceaccountsModel->where($where)->delete();
        $accountLength =  $_REQUEST ['accountsLength'];

        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i ];
            $name = $_REQUEST['accountsName_' . $i ];
            $money = $_REQUEST['accountsMoney_' . $i ];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['roomserviceid'] = $record;
            if(!empty($code) && !empty($name)){
                $roomserviceaccountsModel->create();
                $roomserviceaccountsModel->add($data);

            }
        };



    }

    /**
     * 删除客户表，促销，赠卡，餐券
     */
    public function delete_slave_table($record,$getDate){
        //链接数据库
        $connectionDb = $this->connectReveueDb($getDate);

        //定义查询条件
        $where = array();
        $where['roomserviceid'] = $record;

        //取得客户表
        $roomserviceaccountsModel = M("roomserviceaccounts_" . substr($getDate, 5, 2), " ", $connectionDb);
        $roomserviceaccountsResult = $roomserviceaccountsModel->where($where)->delete();


        //返回促销表
        $roomservicepromotionModel = M("roomservicepromotion_" . substr($getDate, 5, 2), " ", $connectionDb);
        $roomservicepromotionResult = $roomservicepromotionModel->where($where)->delete();


        //取得赠卡
        $roomservicefreebieModel = M('roomservicefreebie_' . substr($getDate, 5, 2), " ", $connectionDb);
        $roomservicefreebieResult = $roomservicefreebieModel->where($where)->delete();


        //取得赠券
        $roomservicemealticketModel = M('roomservicemealticket_' . substr($getDate, 5, 2), " ", $connectionDb);
        $roomservicemealticketResult = $roomservicemealticketModel->where($where)->delete();

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
        $getdate = $_REQUEST ['getdate'];

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

    //根据客户代码，查询客户名称
    public function getAccountsByCode()
    {
        $code = $_REQUEST['code'];
        $accountsModel = D('Accounts');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $accounts = $accountsModel->field('name')->where($where)->find();
        $this->ajaxReturn($accounts, 'JSON');
    }

    //根据赠卡代码，查询赠卡名称
    public function getFreebieByCode()
    {
        $code = $_REQUEST['code'];
        $freebieModel = D('freebie');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $freebie = $freebieModel->field('name,price')->where($where)->find();
        $this->ajaxReturn($freebie, 'JSON');
    }

    //根据餐券代码，查询餐券名称
    public function getMealticketByCode()
    {
        $code = $_REQUEST['code'];
        $mealticketModel = D('mealticket');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $mealticket = $mealticketModel->field('name,price')->where($where)->find();
        $this->ajaxReturn($mealticket, 'JSON');
    }

    // 查看数据的页面
    public function paymentEditview()
    {
        // 取得模块的名称
        $moduleName = 'OrderForm';
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $where ['ordersn'] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);
        $this->assign('name',$result['sendname']);
        $this->assign('room_date',$result['custdate']);
        $this->assign('room_ap',$result['ap']);

        // 返回从表的内容
        $this->get_slave_table($record);
        $this->display('YingshouRoomService'. '/paymenteditview');
    }

    //保存数据
    public function update(){

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        /*
        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);
        // 返回的页面
        $returnAction = $_REQUEST ['returnAction'];

        // 取得记录号
        $record = $_REQUEST ['record'];
        $moduleId = $focus->getPk();

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate();
        $focus->setProperty("_auto", $auto);
        // 保存主表
        $focus->create();

        $where = array();
        $where[$moduleId] = $record;
        $result = $focus->where($where)->save();

        // 新写的保存从表方案
        $slaveResult = $this->update_slave_table($record);

        $return ['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
         */
        // 生成查看的url
        $detailviewUrl = U("$moduleName/checkorder", array(

            'rowIndex' => $_REQUEST['rowIndex'], 'pagetype' => $pagetype
        ));
//'record' => $record, 'returnAction' => $returnAction,
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

}

