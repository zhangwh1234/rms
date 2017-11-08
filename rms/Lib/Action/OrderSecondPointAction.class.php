<?php

/**
 * 分送点管理模块，是分公司（配送店）俗称的输血点
 */
class OrderSecondPointAction extends ModuleAction
{

    //列表
    public function listview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());


            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo ['department'];
            $where = array();
            $where ['state'] = array(
                'notlike',
                '已%'
            );
            //$where ['company'] = $company;
            $where ['_string'] = "length(trim(company)) > 0";
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
            }

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            //订单配送还要显示两个统计数据
            $listMaxRows = $listMaxRows - 2;

            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);


            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowlistview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
            }


            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData ['rows'] = $listResult;
                $listData ['total'] = $total;
            } else {
                $listData ['rows'] = array();
                $listData ['total'] = 0;
            }
            $this->ajaxReturn($listData, 'JSON');

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 启动列表菜单
            //$this->display ( 'OrderHandle/listviewmenu' );
            //获取打印的纸张类型
            $printPageName = cookie('rmsPrintPageName');
            $this->assign('rmsPrintPageName', $printPageName);

            //获取打印机
            $printerName = cookie('rmsPrinterName');
            $this->assign('rmsPrinterName', $printerName);

            // 取得订单的备注字段
            $beizhuorderModel = D('beizhuordermgr'); // 备注表
            $beizhuorderresult = $beizhuorderModel->select();
            $this->assign('beizhuOrderhandle', $beizhuorderresult);
            $this->display('OrderSecondPoint/listview');
        }

    }

    /**
     * 地址查询输入
     */
    public function searchAddressInput()
    {
        $this->display('OrderSecondPoint/searchaddressinput');
    }

    /**
     * 单独的地址查询
     */
    function searchviewAddress()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();

            // 启动当前模块
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            // 建立查询条件
            $where = array();
            $searchText = urldecode($_REQUEST ['searchAddress']); // 查询内容
            if (isset ($searchText)) {
                $where ['address'] = array(
                    'like',
                    '%' . $searchText . '%'
                );
                $_SESSION ['orderHandleSearchForAddressAddressText'] = $searchText;
            } else {
                if (isset ($_SESSION ['orderHandleSearchForAddressAddressText'])) {
                    $where ['address'] = array(
                        'like',
                        '%' . $_SESSION ['orderHandleSearchForAddressAddressText'] . '%'
                    );
                }
            }

            $where ['domain'] = $_SERVER ['HTTP_HOST'];
            // 获取分公司
            $company = $this->userInfo ['department'];
            $where ['company'] = $company;

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数


            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            //订单配送还要显示两个统计数据
            $listMaxRows = $listMaxRows - 2;

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData ['rows'] = $listResult;
                $listData ['total'] = $total;
            } else {
                $listData ['rows'] = array();
                $listData ['total'] = 0;
            }
            $this->ajaxReturn($listData, 'JSON');
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            $this->assign('operName', '地址查询');

            $searchAddress = urldecode($_REQUEST ['searchTextAddress']); // 查询内容
            $url = U('OrderDistribution/searchviewAddress', array('searchTextAddress' => $searchAddress));
            $this->assign('url', $url);
            $this->assign('searchAddress', $searchAddress);
            $this->assign('searchIntroduce', '查询内容：' . $searchAddress);
            $this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法

            $pageNumber = $_SESSION[$moduleName . 'searchviewaddress' . 'page'];
            //如果存在页数,获取
            if (isset($pageNumber)) {
                $pageNumber = $_SESSION[$moduleName . 'searchviewaddress' . 'page'];
            } else {
                $pageNumber = 1;
            }
            $this->assign('pagenumber', $pageNumber);
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }
            $this->display('OrderHandle/searchviewaddress');
        }

    }

    /**
     * 送餐员查询输入
     */
    public function searchSendnameInput()
    {
        $this->display('OrderHandle/searchsendnameinput');
    }

    /**
     * 送餐员条件查询
     */
    public function searchviewSendname()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();

            // 启动当前模块
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            // 建立查询条件
            $where = array();
            $searchSendname = $_REQUEST ['searchSendname']; // 查询的送餐员
            var_dump($_REQUEST['searchSendname']);
            if (isset ($searchSendname)) {
                $where ['sendname'] = $searchSendname;
                $this->assign('searchSendname', $searchSendname);
                $_SESSION ['orderHandleSearchForSendnameSendnameText'] = $searchSendname;
            } else {
                if (isset ($_SESSION ['searchText' . $moduleName . 'Sendname'])) {
                    $where ['sendname'] = $_SESSION ['searchText' . $moduleName . 'Sendname'];
                    $this->assign('searchSendname', $_SESSION ['searchText' . $moduleName . 'Sendname']);
                }
            }


            $where ['domain'] = $_SERVER ['HTTP_HOST'];
            // 获取分公司
            $company = $this->userInfo ['department'];
            $where ['company'] = $company;

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            //订单配送还要显示两个统计数据
            $listMaxRows = $listMaxRows - 4;

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);
            //保存页数
            $_SESSION [$moduleName . 'searchviewsendname' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();
            var_dump($focus->getLastSql());
            $listData = array();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData ['rows'] = $listResult;
                $listData ['total'] = $total;
            } else {
                $listData ['rows'] = array();
                $listData ['total'] = 0;
            }
            $this->ajaxReturn($listData, 'JSON');
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称
            $this->assign('functionName', 'searchviewForSendname'); // 输出程序名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '送餐员查询操作');

            // 模块的ID
            $moduleId = $focus->getPk();
            // 加入模块id到listHeader中

            $this->assign('returnAction', 'searchviewForSendname'); // 定义返回的方法

            $searchSendname = urldecode($_REQUEST ['searchTextSendname']); // 查询的送餐员
            $url = U('OrderDistribution/searchviewSendname', array('searchSendname' => $searchSendname));
            $this->assign('url', $url);
            $this->assign('searchSendname', $searchSendname);

            $this->assign('moduleId', $moduleId);
            $this->assign('returnAction', 'searchviewSendname'); // 定义返回的方法
            //$this->getSendnameproductsByName(); // 显示送餐员餐数情况

            $pageNumber = $_SESSION[$moduleName . 'searchviewsendname' . 'page'];
            //如果存在页数,获取
            if (isset($pageNumber)) {
                $pageNumber = $_SESSION[$moduleName . 'searchviewsendname' . 'page'];
            } else {
                $pageNumber = 1;
            }
            $this->assign('pagenumber', $pageNumber);
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderHandle/searchviewsendname'); // 查询的结果显示
        }
    }

    /**
     * 综合查询页面
     */
    public function searchOtherInput()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);
        $this->display($moduleName . '/searchotherinput');
    }

    /**
     * 其他条件查询
     */
    public function searchviewOther()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '综合查询操作');

            // 生成list字段列表
            $listFields = $focus->serchListFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';


            // 生成list字段列表
            $listFields = $focus->searchListFields;
            // 模块的ID
            $moduleId = $focus->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;

            // 建立查询条件
            $where = array();
            $searchText = urldecode($_REQUEST ['searchOther']); // 查询内容
            foreach ($focus->searchFields as $value) {
                $where [$value] = array(
                    'like',
                    '%' . $searchText . '%'
                );
            }
            $where ['_logic'] = 'OR';

            $map['_complex'] = $where;
            $map['domain'] = $this->getDomain();
            // 获取分公司
            $company = $this->userInfo ['department'];
            $map ['company'] = $company;

            $total = $focus->where($map)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            //订单配送还要显示两个统计数据
            $listMaxRows = $listMaxRows - 2;

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'searchviewother' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData ['rows'] = $listResult;
                $listData ['total'] = $total;
            } else {
                $listData ['rows'] = array();
                $listData ['total'] = 0;
            }
            $this->ajaxReturn($listData, 'JSON');
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            $this->assign('operName', '综合查询');

            $searchOther = urldecode($_REQUEST ['searchTextOther']); // 查询内容
            $url = U('OrderDistribution/searchviewOther', array('searchOther' => $searchOther));
            $this->assign('url', $url);
            $this->assign('searchOther', $searchOther);
            $this->assign('searchIntroduce', '查询内容：' . $searchOther);
            $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法

            $pageNumber = $_SESSION[$moduleName . 'searchviewother' . 'page'];
            //如果存在页数,获取
            if (isset($pageNumber)) {
                $pageNumber = $_SESSION[$moduleName . 'searchviewother' . 'page'];
            } else {
                $pageNumber = 1;
            }
            $this->assign('pagenumber', $pageNumber);
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderHandle/searchviewother'); // 查询的结果显示
        }

    }


    // 根据送餐员，显示送餐员的送餐情况
    public function getSendnameproductsByName()
    {
        $sendname = urldecode($_REQUEST ['searchTextSendname']); // 查询的送餐员
        $userInfo = $this->userInfo;
        $company = $userInfo ['department'];

        $where = array();
        $where ['sendname'] = $sendname;
        $where ['company'] = $company;
        $where ['domain'] = $this->getDomain();

        //应该从装箱表中获得统计的产品名
        $zhuangxiangModel = D('Zhuangxiangform');
        $zhuangxiangproductsModel = D('Zhuangxiangproducts');

        $zhuangxiangproductsResult = $zhuangxiangproductsModel->distinct('shortname')->where($where)->select();
        $zhuangxiangproductsArray = array();
        foreach ($zhuangxiangproductsResult as $value) {
            $zhuangxiangproductsArray[] = $value['shortname'];
        }

        // 产品装箱配送表
        $sendnameproductsModel = D('Sendnameproducts');

        // 定义统计表
        $tongji = array();

        // 查询装箱
        $listHeader = array();
        foreach ($zhuangxiangproductsResult as $value) {
            $tongji ['装箱'] [$value ['shortname']] = 0;
            $tongji ['已送'] [$value ['shortname']] = 0;
            $tongji ['剩余'] [$value ['shortname']] = 0;
            $listHeader [] = $value ['shortname'];
        }
        $this->sendnameProductsListHeader = $listHeader;

        // 查询
        $where = array();
        $where ['sendname'] = $sendname;
        $where ['company'] = $company;
        $where['shortname'] = array('in', $zhuangxiangproductsArray);
        $sendnameProductsResult = $sendnameproductsModel->where($where)->select();
        foreach ($sendnameProductsResult as $key => $value) {
            if ($value ['type'] == '装箱') {
                $tongji ['装箱'] [$value ['shortname']] += $value ['number'];
            }
            if ($value ['type'] == '已送') {
                $tongji ['已送'] [$value ['shortname']] += $value ['number'];
            }
        }

        // 计算剩余
        foreach ($listHeader as $value) {
            $tongji ['剩余'] [$value] = $tongji ['装箱'] [$value] - $tongji ['已送'] [$value];
        }
        $zhuangxiang = '';
        $yisong = '';
        $shengyu = '';
        //组成返回字符串
        foreach ($tongji as $key => $value) {
            if ($key == '装箱') {
                foreach ($value as $childKey => $childValue) {
                    $zhuangxiang .= $childValue . '*' . $childKey . ' ';
                }
            }
            if ($key == '已送') {
                foreach ($value as $childKey => $childValue) {
                    $yisong .= $childValue . '*' . $childKey . ' ';
                }
            }
            if ($key == '剩余') {
                foreach ($value as $childKey => $childValue) {
                    $shengyu .= $childValue . '*' . $childKey . ' ';
                }
            }
        }
        $this->assign('zhuangxiang', $zhuangxiang);
        $this->assign('yisong', $yisong);
        $this->assign('shengyu', $shengyu);
    }


    /**
     * 根据输入的编码处理订单
     * code 输入的编码
     */
    public function orderHandleByCode()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $this->userInfo ['department'];

        // 获得处理过了的编码
        $code = $_REQUEST ['code'];
        // 订单号
        $orderformid = $_REQUEST ['orderformid'];

        // 获得订单号
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderformResult = $focus->field('ordersn')->where($where)->find();
        $ordersn = $orderformResult ['ordersn'];

        // 定义返回的数组
        $returnInfo = array();

        /**
         * 先编辑送餐员的编码 **
         */
        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where ['code'] = $code; // 送餐员的编号
        $where ['company'] = $company;
        $where ['domain'] = $this->getDomain();
        $sendnameResult = $sendnameMgrModel->field('name,telphone,weixin')->where($where)->find();

        if ($sendnameResult) {
            $sendname = $sendnameResult ['name'];
            $telphone = $sendnameResult ['telphone'];
            $sendtype = $sendnameResult ['sendtype'];
            if (!empty($sendnameResult['weixin'])) {
                $weixin = $sendnameResult['weixin'];
            } else {
                $weixin = '';
            }

        } else {
            $returnInfo ['error'] = 'error';
            $returnInfo ['msg'] = '没有查到信息';
            $this->ajaxReturn($returnInfo);
        }
        // 查询订单的本身状态
        $data = array();
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderformResult = $focus->field('state')->where($where)->find();
        if (!empty ($orderformResult)) {
            if ($orderformResult ['state'] == '退餐') { // 立即返回
                $data ['state'] = '已退餐';
            } elseif ($orderformResult ['state'] == '已退餐') {
                $data ['state'] = '已退餐';
            } elseif ($orderformResult ['state'] == '废单') {
                $data ['state'] = '废单';
            } elseif ($orderformResult ['state'] == '已作废') {
                $data ['state'] = '已作废';
            } else {
                $data ['state'] = '已处理';
            }
        } else {
            $data ['state'] = '已处理';
        }

        $data ['sendname'] = $sendname;
        $state = $data ['state'];
        $focus->where($where)->save($data);
        // 输入短信表中，如果要发短信的话
        if (!empty ($telphone) and (strlen($telphone) == 11)) {
            $orderform = $focus->where("orderformid='$orderformid'")->find();
            $payment = D('orderpayment')->where("orderformid='$orderformid'")->select();
            $activity = D('orderactivity')->where("orderformid='$orderformid'")->select();
            $activity_str = '';
            foreach ($activity as $value) {
                $activity_str .= $value['name'] . '-' . $value['money'];
            }
            $payment_str = '';
            foreach ($payment as $value) {
                $payment_str .= $value['name'] . '-' . $value['money'];
            }
            $smsString = '地址:' . $orderform ['address'] . ' 客户:' . $orderform['clientname'] . chr(10) .
                '订餐:' . $orderform ['ordertxt'] . chr(10) .
                '要餐时间:' . $orderform ['custtime'] . '电话:' . $orderform ['telphone'] . chr(10) .
                '备注:' . $orderform ['beizhu'] . chr(10) .
                '营销活动:' . $activity_str . chr(10) .
                '支付:' . $payment_str . chr(10) .
                '订单金额:' . $orderform['totalmoney'] . ' 送餐费:' . $orderform['shippingmoney'] . chr(10) .
                '还需收客人金额:' . $orderform['shouldmoney'];
            // 计算已经发生的信息量
            $smsmgr_model = D('Smsmgr');
            $smscount = $smsmgr_model->where("telphone='$telphone'")->count();
            if ($smscount) {
                $smscount += 1;
            } else {
                $smscount = 1;
            }
            $smsString = $smscount . '|' . $smsString;
            $smsData = array();
            $smsData ['telphone'] = $telphone;
            $smsData ['content'] = $smsString;
            $smsData ['firstdate'] = date('Y-m-d H:i:s');
            $smsData ['company'] = $company;
            $smsData ['sendtype'] = '信息';
            $smsData ['sendname'] = $sendname;
            $smsData ['orderformid'] = $orderformid;
            $smsData ['weixin'] = $weixin;
            $smsData ['domain'] = $_SERVER ['HTTP_HOST'];
            $smsmgr_model->add($smsData);
        }

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $data = array();
        $data ['orderformid'] = $orderformid; // 订单号
        $data ['ordersn'] = $ordersn;
        $data ['action'] = "订单配送给" . $sendname . "送餐员";
        $data ['logtime'] = date('H:i:s');
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $orderactionModel->create();
        $result = $orderactionModel->add($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data ['handle'] = 1;
        $data ['handletime'] = date('Y-m-d H:i:s');
        $data ['handlecontent'] = $sendname . ' ' . $telphone;
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderstateModel->where($where)->save($data);

        // 取得分公司名称
        $company = $this->userInfo ['department'];
        // 保存到送餐员餐售情况
        $sendnameproductsModel = D('Sendnameproducts');
        $where = array();
        $where ['extid'] = $orderformid;
        $sendnameproductsModel->where($where)->delete();
        // 查询订货
        $orderproductsModel = D('Orderproducts');
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderproductsResult = $orderproductsModel->where($where)->select();

        // 获得订单号
        $where = array();
        $where ['orderformid'] = $orderformid;
        $orderformResult = $focus->where($where)->find();

        //通知客户的消息, 如果是微信或者推送
        if (!empty($orderformResult['app_tk'])) {
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['app_tk'] = $orderformResult['app_tk'];
            $data['contenttype'] = 'sendname';
            $data['origin'] = 'APP';
            $data['domain'] = $this->getDomain();
            $notifyclientModel = D('NotifyClient');
            $notifyclientModel->create();
            $notifyclientModel->add($data);
        } else {
            //那么如果是手机号码
            if (preg_match("/^1[34578]\d{9}$/", $orderformResult['telphone'])) {
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['telphone'] = $telphone;
                $data['contenttype'] = 'sendname';
                $data['origin'] = '电话';
                $data['domain'] = $this->getDomain();
                $notifyclientModel = D('NotifyClient');
                $notifyclientModel->create();
                $notifyclientModel->add($data);
            }
        }

        // 是为了计算装箱送餐员的饭
        foreach ($orderproductsResult as $productsValue) {
            $code = $productsValue ['code'];
            $name = $productsValue ['name'];
            $shortname = $productsValue ['shortname'];
            $price = $productsValue ['price'];
            $number = $productsValue ['number'];
            $money = $productsValue ['money'];
            $data = array();
            $data ['productsname'] = $name;
            $data ['shortname'] = $shortname;
            $data ['type'] = '已送';
            $data ['number'] = $number;
            $data ['extid'] = $orderformid;
            $data ['sendname'] = $sendname; // 送餐员
            $data ['company'] = $company;
            $data ['date'] = date('Y-m-d');
            $data ['ap'] = $this->getAp();
            $sendnameproductsModel->create();
            $sendnameproductsModel->add($data);
        }

        // 定义返回
        $returnInfo ['success'] = 'success';
        $orderformData ['sendname'] = $sendname;
        $orderformData ['state'] = $state;
        $returnInfo ['data'] = $orderformData;
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 显示产品明细等
    public function showproducts()
    {
        // 取得记录号
        $record = $_REQUEST ['orderformid'];

        // 取得模块的名称
        $currentModule = $this->getActionName();
        // var_dump($currentModule);
        // 启动当前模块
        $focus = D($currentModule);

        // 取得订单信息
        $orderform = $focus->where("orderformid=$record")->find();
        // dump($orderform);
        $orderproducts_model = D('Orderproducts');
        // 取得订单产品信息
        $orderproducts = $orderproducts_model->where("orderformid=$record")->select();

        $this->ajaxReturn($orderproducts, 'JSON');
    }


    /**
     *  启动发送单独消息的输入界面
     */
    public function sendAloneMessagesInput()
    {
        $this->display('OrderHandle/sendalonemessagesinput');
    }

    /**
     * 发送单独消息
     */
    public function setAloneMessages()
    {
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $this->userInfo ['department'];

        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where ['name'] = urldecode($_REQUEST['sendname']); // 送餐员的编号
        $where ['company'] = $company;
        $sendnameResult = $sendnameMgrModel->field('name,telphone,sendtype')->where($where)->find();
        if ($sendnameResult) {
            $sendname = $sendnameResult ['name'];
            $telphone = $sendnameResult ['telphone'];
            $sendtype = $sendnameResult ['sendtype'];
        }

        // 输入短信表中，如果要发短信的话
        if (!empty ($telphone) and (strlen($telphone) > 6)) {
            // 计算已经发生的信息量
            $smsmgrModel = D('Smsmgr');
            $smsString = urldecode($_REQUEST['msgcontent']);
            $smsData = array();
            $smsData ['telphone'] = $telphone;
            $smsData ['content'] = $smsString;
            $smsData ['firstdate'] = date('Y-m-d H:i:s');
            $smsData ['company'] = $company;
            $smsData ['sendtype'] = '短信';
            $smsData ['sendname'] = urldecode($_REQUEST['sendname']);
            $smsData ['orderformid'] = '';
            $smsData ['domain'] = $_SERVER ['HTTP_HOST'];
            $smsmgrResult = $smsmgrModel->add($smsData);
            if ($smsmgrResult) {
                // 定义返回
                $returnInfo ['success'] = 'success';
                $returnInfo ['msg'] = '单发消息保存成功';
                $this->ajaxReturn($returnInfo, 'JSON');
            } else {
                $returnInfo ['error'] = 'error';
                $returnInfo ['msg'] = '单发消息保存失败';
                $this->ajaxReturn($returnInfo);

            }
        }
    }

    /* 取得打印需要的数据 */
    function getPrintOrder()
    {
        // 取得订单号
        $record = $_REQUEST ['orderformid'];

        $orderformModel = D('Orderform');
        // 查询订单
        $orderform = $orderformModel->where("orderformid=$record")->find();

        $orderprinterModel = D('Orderprinter');
        // 分公司的名称
        $company = $this->userInfo ['department'];
        $where = array();
        $where ['orderformid'] = $record;
        $where ['company'] = $company;
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        //获取订单打印数量
        $printnumberResult = $orderprinterModel->where($where)->find();
        if (empty($printnumberResult)) {  //如果不存在打印号，就生成打印号
            $where = array();
            $where ['company'] = $company;
            $where ['domain'] = $_SERVER ['HTTP_HOST'];
            $print_number = $orderprinterModel->where($where)->count();
            if ($print_number == 0) {
                $print_number = 1;
            } else {
                $print_number = $print_number + 1;
            }

            $print_number = $print_number + 300;
            $data = array();
            $data ['printnumber'] = $print_number;
            $data ['orderformid'] = $record;
            $data ['ordersn'] = $orderform['ordersn'];
            $data ['company'] = $company;
            $data ['domain'] = $this->getDomain();
            $data ['date'] = date('Y-m-d H:i:s');
            $orderprinterModel->create();
            $orderprinterModel->add($data);
        } else {
            $print_number = $printnumberResult['printnumber'];
        }

        $orderform['printnumber'] = $print_number;
        // 查询订货
        $orderproductsModel = D('Orderproducts');
        $orderproducts = $orderproductsModel->where("orderformid=$record")->select();
        //活动表
        $orderactivityModel = D('Orderactivity');
        $orderactivity = $orderactivityModel->where("orderformid=$record")->select();
        //支付表
        $orderpaymentModel = D('Orderpayment');
        $orderpayment = $orderpaymentModel->where("orderformid=$record")->select();

        $order ['orderform'] = $orderform;
        $order ['orderproducts'] = $orderproducts;
        $order ['orderactivity'] = $orderactivity;
        $order ['orderpayment'] = $orderpayment;

        $this->ajaxReturn($order, 'JSON');
    }

    /* 设定订单已打印状态 */
    function setOrderPrinted()
    {
        // 取得订单号
        $record = $_REQUEST ['orderformid'];
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where ['orderformid'] = $record;
        $orderformResult = $orderformModel->field('state')->where($where)->find();
        if (!empty ($orderformResult)) {
            if ($orderformResult ['state'] == '退餐') { // 立即返回
                $data ['state'] = '已退餐';
            } elseif ($orderformResult ['state'] == '已退餐') {
                $data ['state'] = '已退餐';
            } elseif ($orderformResult ['state'] == '废单') {
                $data ['state'] = '废单';
            } elseif ($orderformResult ['state'] == '已作废') {
                $data ['state'] = '已作废';
            } else {
                $data ['state'] = '已打印';
            }
        } else {
            $data ['state'] = '已打印';
        }
        $result = $orderformModel->where($where)->save($data);

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $action ['ordersn'] = $orderformResult['ordersn'];
        $company = $this->userInfo ['department'];
        $action ['action'] = $company . "打印订单";
        $action ['logtime'] = date('H:i:s');
        $action ['domain'] = $this->getDomain();
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

    }

    /**
     * 订单备注处理
     */
    public function beizhuInput()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = 'BeizhuOrderMgr';

            // 启动当前模块
            $focus = D($moduleName);

            $listResult = $focus->field('name')->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData ['rows'] = $listResult;
            } else {
                $listDate ['rows'] = array();
                $listData ['total'] = 0;
            }
            $this->ajaxReturn($listData, 'JSON');

        } else {
            $this->assign('className', $_REQUEST['className']);
            $this->assign('orderformid', $_REQUEST['orderformid']);
            $this->assign('rowIndex', $_REQUEST['rowIndex']);
            $this->display('OrderHandle/beizhuInput');
        }

    }

    /* 设定订单订单备注 */
    function setBeizhuOrder()
    {
        // 取得订单号
        $record = $_REQUEST ['orderformid'];
        // 取得备注内容
        $beizhu = ' ' . urldecode($_REQUEST ['beizhu']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where ['orderformid'] = $record;
        $data ['beizhu'] = array(
            'exp',
            "concat('$beizhu',beizhu)"
        );
        $result = $orderformModel->where($where)->save($data);

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $company = $this->userInfo ['department'];
        $action ['action'] = $company . "订单号:" . $record . "订单备注：" . $beizhu;
        $action ['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        $where = array();
        $where ['orderformid'] = $record;
        $result = $orderformModel->field('beizhu')->where($where)->find();

        // 返回成功
        $returnInfo ['success'] = 'success';
        $returnInfo ['beizhu'] = $result['beizhu'];
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    /**
     * 将订单转其他分公司的输入页面
     */
    function distributeOtherCompanyInput()
    {
        $companymgrModel = D('companymgr');
        $where = array();
        $where['domain'] = $this->getDomain();
        $companyResult = $companymgrModel->where($where)->select();

        $this->assign('company', $companyResult);
        $this->display('OrderHandle/distributeothercompanyinput');
    }

    /**
     *  将订单转到其他分公司
     */
    function setOtherCompany()
    {
        // 取得订单号
        $record = $_REQUEST ['orderformid'];
        // 取得备注内容
        $company = ' ' . urldecode($_REQUEST ['company']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where ['orderformid'] = $record;
        $data ['company'] = $company;
        $result = $orderformModel->where($where)->save($data);


        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $orignCompany = $this->userInfo ['department'];

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $orderactionData ['ordersn'] = $record; // 订单号
        $company = $data ['company'];
        $orderactionData ['action'] = "分公司:" + $orignCompany + "将订单转分给" . $company . "配送点";
        $orderactionData ['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($orderactionData);

        // 返回成功
        $returnInfo ['success'] = 'success';
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    /**
     * 转给分送点的页面
     */
    public function distributeOrderSecondPointInput()
    {
        if (IS_POST) {
            // 分公司的名称
            $company = $this->userInfo ['department'];

            $secondpoindmgrModel = D('secondpointmgr');
            $where = array();
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $secondpointmgrResult = $secondpoindmgrModel->where($where)->select();
            $this->ajaxReturn($secondpointmgrResult, 'JSON');
        } else {
            $this->assign('className', $_REQUEST['className']);
            $this->assign('orderformid', $_REQUEST['orderformid']);
            $this->assign('rowIndex', $_REQUEST['rowIndex']);
            $this->display('OrderHandle/distributeordersecondpointinput');
        }
    }

    /**
     * 将订单转给分送点
     */
    public function setSecondPoint()
    {
        // 取得订单号
        $record = $_REQUEST ['orderformid'];
        // 取得备注内容
        $secondPointName = ' ' . urldecode($_REQUEST ['secondPointName']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where ['orderformid'] = $record;
        $data ['secondpointname'] = $secondPointName;
        $result = $orderformModel->where($where)->save($data);

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $company = $this->userInfo ['department'];
        $action ['action'] = $company . "订单号:" . $record . "转给分送点：" . $secondPointName;
        $action ['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        // 返回成功
        $returnInfo ['success'] = 'success';
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 设置打印机类型
    public function setprintupdateview()
    {
        // 返回当前的模块名
        $currentModule = $this->getActionName();
        // var_dump($currentModule);
        $focus = D($currentModule);

        // 引入模块菜单
        $Modulemenu = A('ModuleMenu');
        $Modulemenu->index($this->getActionName(), 'createview');

        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['company'];

        $companymgr_model = D('Companymgr');
        $printtype = $companymgr_model->field('printtype')->where("name='$name'")->find();
        // dump($printtype);

        $this->assign('printtype', $printtype ['printtype']); // 指定字段获得焦点

        // dump($this->blocks);
        $this->display('./Tpl/OrderHandle/setprintupdateview.html');
    }

    // 保存打印设置
    public function saveSetPrint()
    {

        // 打印类型
        $printtype = $_REQUEST ['printtypesetup'];

        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['company'];

        $companymgr_model = D('Companymgr');
        $data ['printtype'] = $printtype;
        $result = $companymgr_model->where("name='$name'")->save($data);
        // echo $companymgr_model->getLastSql();

        // 保存成功
        // $this->redirect("OrderHandle/setprintdetailview", array(), 0, '页面跳转中...');
        $returnArr = array();
        $this->ajaxReturn($returnArr, 'JSON');
    }

    // 显示打印设置
    public function setPrintPage()
    {

        //获取打印的纸张类型
        $printPageName = cookie('rmsPrintPageName');
        $this->assign('rmsPrintPageName', $printPageName);

        $printPage = cookie('rmsPrintPage');
        $this->assign('rmsPrintPage', $printPage);

        $this->display('OrderHandle/setprintpage');
    }

    /* 返回分公司订单的情况 */
    function getOrderMonit()
    {
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $userInfo ['department'];

        $where = array();
        $where ['name'] = $company;
        $ordermonit_model = D('Ordermonit');
        $ordermonit = $ordermonit_model->where($where)->select();
        if (empty ($ordermonit)) {
            $ordermonit = array();
        }
        $this->ajaxReturn($ordermonit, 'JSON');
    }

    // 根据代码获取送餐员名字
    public function getSendnameByCode()
    {
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $userInfo ['department'];

        // 获得处理过了的编码
        $code = $_REQUEST ['code'];

        // 定义返回的数组
        $returnInfo = array();

        /**
         * 先编辑送餐员的编码 **
         */
        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $sendnameWhere ['code'] = $code; // 送餐员的编号
        $sendnameWhere['company'] = $company;
        $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($sendnameWhere)->find();
        if ($sendnameResult) {
            $sendname = $sendnameResult ['name'];
            $telphone = $sendnameResult ['telphone'];
        } else {
            $returnInfo ['error'] = 'error';
            $returnInfo ['msg'] = '没有查到信息';
            $this->ajaxReturn($returnInfo);
        }
        // 根据送餐员信息，处理订单
        $orderformData ['sendname'] = $sendname;

        // 定义返回
        $returnInfo ['success'] = 'success';
        $returnInfo ['data'] = $orderformData;
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 返回所有送餐员的名称和代码
    public function getSendnameMgr()
    {
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $this->userInfo ['department'];

        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where ['company'] = $company;
        $where ['domain'] = $this->getDomain();
        $sendnameMgrResult = $sendnameMgrModel->field("code,name,telphone,weixin")->where($where)->select();
        $this->ajaxReturn($sendnameMgrResult, 'JSON');
    }

    // 处理订单的退餐，改为已退餐
    // 处理改单或者催单
    public function cancelchangehurryOrderHandle()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 订单号
        $record = $_REQUEST ['orderformid'];

        // 查询当前的订单状态
        $where = array();
        $where ['orderformid'] = $record;
        $orderformResult = $focus->field('state,sendname,ordersn')->where($where)->find();
        $ordersn = $orderformResult ['ordersn']; // 订单号

        if (!empty ($orderformResult)) {
            if ($orderformResult ['state'] == '退餐') {
                $where = array();
                $where ['orderformid'] = $record;
                $data = array();
                $data ['state'] = '已退餐';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data ['orderformid'] = $record; // 订单号
                $company = $this->userInfo ['department'];
                $data ['action'] = $company . "将订单处理成已退餐";
                $data ['logtime'] = date('H:i:s');
                $data ['domain'] = $_SERVER ['HTTP_HOST'];
                $data ['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                // 写入到状态表中
                $orderstateModel = D('Orderstate');
                $data = array();
                $data ['cancel'] = 1;
                $data ['canceltime'] = date('Y-m-d H:i:s');
                $data ['cancelcontent'] = $company . '处理成已退餐';
                $data ['domain'] = $_SERVER ['HTTP_HOST'];
                $data ['ordersn'] = $ordersn;
                $where = array();
                $where ['orderformid'] = $record;
                $orderstateModel->where($where)->save($data);

                //消息给送餐员
                $this->sendSmsToSendname($record, $orderformResult ['sendname'], $orderformResult ['state']);

                // 返回成功
                $returnInfo ['success'] = 'success';
                $returnInfo ['state'] = '已退餐';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult ['state'] == '改单') {
                if ($orderformResult ['sendname'] == '') {
                    // 返回成功
                    $returnInfo ['error'] = 'error';
                    $returnInfo ['msg'] = '订单还没有配给送餐员,改单无法处理！';
                    $this->ajaxReturn($returnInfo, 'JSON');
                }
                $where = array();
                $where ['orderformid'] = $record;
                $data = array();
                $data ['state'] = '已更改';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data ['orderformid'] = $record; // 订单号
                $data ['ordersn'] = $ordersn;
                $company = $this->userInfo ['department'];
                $data ['action'] = $company . "将订单处理成已更改";
                $data ['logtime'] = date('H:i:s');
                $data ['domain'] = $_SERVER ['HTTP_HOST'];
                $data ['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                //消息给送餐员
                $this->sendSmsToSendname($record, $orderformResult ['sendname'], $orderformResult ['state']);

                // 返回成功
                $returnInfo ['success'] = 'success';
                $returnInfo ['state'] = '已更改';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult ['state'] == '催送') {
                if ($orderformResult ['sendname'] == '') {
                    // 返回成功
                    $returnInfo ['error'] = 'error';
                    $returnInfo ['msg'] = '订单还没有配送送餐员,无法处理催送！';
                    $this->ajaxReturn($returnInfo, 'JSON');
                }
                $where = array();
                $where ['orderformid'] = $record;
                $data = array();
                $data ['state'] = '已催送';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data ['orderformid'] = $record; // 订单号
                $data ['ordersn'] = $ordersn;
                $company = $this->userInfo ['department'];
                $data ['action'] = $company . "将订单处理成已催送";
                $data ['logtime'] = date('H:i:s');
                $data ['domain'] = $_SERVER ['HTTP_HOST'];
                $data ['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                //消息给送餐员
                $this->sendSmsToSendname($record, $orderformResult ['sendname'], $orderformResult ['state']);

                // 返回成功
                $returnInfo ['success'] = 'success';
                $returnInfo ['state'] = '已催送';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            $returnInfo ['error'] = 'error';
            $returnInfo ['msg'] = '订单无法处理';
            $this->ajaxReturn($returnInfo, 'JSON');
        }

        // 返回错误
        $returnInfo ['error'] = 'error';
        $returnInfo ['msg'] = '订单不存在';
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 订单返回的操作
    public function backOrderHandle()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);
        // 订单号
        $record = $_REQUEST ['orderformid'];
        // 获得订单号
        $where = array();
        $where ['orderformid'] = $record;
        $orderformResult = $focus->field('ordersn')->where($where)->find();
        $ordersn = $orderformResult ['ordersn'];

        // 查询返回订单
        $where = array();
        $where ['orderformid'] = $record;
        $data = array();
        $data ['company'] = '';
        $data ['sendname'] = '';
        $data ['state'] = '返回';
        $result = $focus->where($where)->save($data);
        if ($result == false) {
            // 更新失败
            $returnInfo ['success'] = 'error';
            $returnInfo ['msg'] = '返回失败';
            $this->ajaxReturn($returnInfo, 'JSON');
        }

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $data = array();
        $data ['orderformid'] = $record; // 订单号
        $company = $this->userInfo ['department'];
        $data ['action'] = $company . "将订单返回";
        $data ['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($data);

        // 返回成功
        $returnInfo ['success'] = 'success';
        $this->ajaxReturn($returnInfo, 'JSON');
    }


    /**
     * 前台的预处理程序
     * 根据送餐员代码，显示装箱送餐员的送餐情况
     * 返回 装箱情况的字符串
     */
    public function getProperSendnameproductsByCode()
    {
        // 定义返回
        $sendnameProducts = array();
        $sendnameProducts ['content'] = '';
        // 送餐员代码
        $code = $_REQUEST ['code'];
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $this->userInfo ['department'];

        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where ['company'] = $company;
        $where ['code'] = $code;
        $sendnameMgrResult = $sendnameMgrModel->field("code,name,telphone")->where($where)->find();
        if (empty ($sendnameMgrResult)) {
            $this->ajaxReturn($sendnameProducts, 'JSON');
        } else {
            $sendname = $sendnameMgrResult ['name'];
        }

        // 产品表
        $sendnameproductsModel = D('Sendnameproducts');
        $where = array();
        $where ['sendname'] = $sendname;
        $where ['company'] = $company;
        // 定义统计表
        $tongji = array();
        $productsResult = $sendnameproductsModel->Distinct(True)->field('productsname,shortname')->where($where)->select();

        // 查询装箱
        $listHeader = array();
        foreach ($productsResult as $value) {
            $tongji ['装箱'] [$value ['shortname']] = 0;
            $tongji ['已送'] [$value ['shortname']] = 0;
            $tongji ['剩余'] [$value ['shortname']] = 0;
            $listHeader [] = $value ['shortname'];
        }
        // $this->sendnameProductsListHeader = $listHeader;

        // 查询
        $returnData = array();
        $where = array();
        $where ['sendname'] = $sendname;
        $where ['company'] = $company;
        $sendnameProductsResult = $sendnameproductsModel->where($where)->select();
        foreach ($sendnameProductsResult as $key => $value) {
            if ($value ['type'] == '装箱') {
                $tongji ['装箱'] [$value ['shortname']] += $value ['number'];
                $returnData ['装箱'] .= $value ['number'] . '*' . $value ['shortname'] . ' ';
            }
            if ($value ['type'] == '已送') {
                $tongji ['已送'] [$value ['shortname']] += $value ['number'];
                $returnData ['已送'] .= $value ['number'] . '*' . $value ['shortname'] . ' ';
            }
        }

        // 计算剩余
        foreach ($listHeader as $value) {
            $tongji ['剩余'] [$value] = $tongji ['装箱'] [$value] - $tongji ['已送'] [$value];
            $returnData ['剩余'] .= $tongji ['剩余'] [$value] . '*' . $value . ' ';
        }

        $sendnameProducts ['content'] = $sendname . '  装箱:' . $returnData ['装箱'] . ' 已送:' . $returnData ['已送'] . ' 剩余:' . $returnData ['剩余'];
        $this->ajaxReturn($sendnameProducts, 'JSON');
    }

    /**
     *发送消息到送餐员
     *para $record  订单号
     *para $sendname
     *para $state
     */
    function sendSmsToSendname($record, $sendname, $state)
    {
        // 分公司的名称
        $userInfo = $_SESSION ['userInfo'];
        $company = $this->userInfo ['department'];

        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where ['name'] = $sendname; // 送餐员的编号
        $where ['company'] = $company;
        $sendnameResult = $sendnameMgrModel->field('name,telphone,sendtype')->where($where)->find();
        if ($sendnameResult) {
            $sendname = $sendnameResult ['name'];
            $telphone = $sendnameResult ['telphone'];
            $sendtype = $sendnameResult ['sendtype'];
        }

        // 输入短信表中，如果要发短信的话
        if (!empty ($telphone) and (strlen($telphone) > 6)) {
            $orderformModel = D('Orderform');
            $where = array();
            $where ['orderformid'] = $record;
            $orderform = $orderformModel->where($where)->find();

            $smsString = $orderform ['address'] . '|' . $orderform ['ordertxt'] . '|' . $state . '|' . $orderform ['custtime'] . '|' . $orderform ['telphone'];
            // 计算已经发生的信息量
            $smsmgrModel = D('Smsmgr');
            $smscount = $smsmgrModel->where("telphone='$telphone'")->count();
            if ($smscount) {
                $smscount += 1;
            } else {
                $smscount = 1;
            }
            $smsString = $smscount . '|' . $smsString;
            $smsData = array();
            $smsData ['telphone'] = $telphone;
            $smsData ['content'] = $smsString;
            $smsData ['firstdate'] = date('Y-m-d H:i:s');
            $smsData ['company'] = $company;
            $smsData ['sendtype'] = '短信';
            $smsData ['sendname'] = $sendname;
            $smsData ['orderformid'] = $record;
            $smsData ['domain'] = $_SERVER ['HTTP_HOST'];
            $smsmgrModel->add($smsData);
        }
    }

    // 返回从表的内容:产品
    public function get_slave_table($record)
    {
        // 取得产品信息
        $orderproducts_model = D('Orderproducts');
        $orderproducts = $orderproducts_model->field('orderformid,code,name,shortname,price,number,money')->where("orderformid=$record")->select();
        $this->assign('orderproducts', $orderproducts);

        // 单独取得订单金额
        $orderform_model = D('Orderform');
        $orderform = $orderform_model->field('totalmoney')->where("orderformid=$record")->select();
        $totalmoney = $orderform [0] ['totalmoney'];
        $this->assign('totalmoney', $totalmoney);

        //取得活动信息
        $orderactivity_model = D('Orderactivity');
        $orderactivity = $orderactivity_model->where("orderformid=$record")->select();
        $this->assign('orderactivity', $orderactivity);

        //取得订单支付信息
        $orderpayment_model = D('Orderpayment');
        $orderpayment = $orderpayment_model->where("orderformid=$record")->select();
        $this->assign('orderpayment', $orderpayment);

        // 取得订单的状态
        $orderStateModel = D('Orderstate');
        $orderStateResult = $orderStateModel->where("orderformid=$record")->find();  //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderaction_model = D('Orderaction');
        $orderaction = $orderaction_model->where("orderformid=$record")->select(); //
        $this->assign('orderaction', $orderaction);
    }


}


?>
