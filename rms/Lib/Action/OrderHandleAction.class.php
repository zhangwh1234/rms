<?php

class OrderHandleAction extends ModuleAction
{

    // 列表
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
            $company = $this->userInfo['department'];
            $where = array();
            $where['state'] = array(
                'notlike',
                '已%',
            );
            $where['ap'] = $this->getAp();
            $where['company'] = $company;
            $where['_string'] = "length(trim(company)) > 0";
            $where['domain'] = $this->getDomain();

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
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
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
            if (!empty($printPageName)) {
                $printPageName = substr($printPageName, 0, 8) . '..';
            }
            $this->assign('rmsPrintPageName', $printPageName);

            //获取打印机
            $printerName = cookie('rmsPrinterName');
            if (!empty($printerName)) {
                $printerName = substr($printerName, 0, 8) . '..';
            }
            $this->assign('rmsPrinterName', $printerName);

            // 取得订单的备注字段
            $beizhuorderModel = D('beizhuordermgr'); // 备注表
            $beizhuorderresult = $beizhuorderModel->select();
            $this->assign('beizhuOrderhandle', $beizhuorderresult);

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
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

            $this->display('OrderHandle/listview');
        }
    }

    /**
     * 地址查询输入
     */
    public function searchAddressInput()
    {
        $this->display('OrderHandle/searchaddressinput');
    }

    /**
     * 单独的地址查询
     */
    public function searchviewAddress()
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
            $searchText = urldecode($_REQUEST['searchAddress']); // 查询内容
            if (isset($searchText)) {
                if ($searchText == '全部') {
                    $where['address'] = array(
                        'like',
                        '%%',
                    );
                } else {
                    $where['address'] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                    $_SESSION['orderHandleSearchForAddressAddressText'] = $searchText;
                }
            } else {
                if (isset($_SESSION['orderHandleSearchForAddressAddressText'])) {
                    $where['address'] = array(
                        'like',
                        '%' . $_SESSION['orderHandleSearchForAddressAddressText'] . '%',
                    );
                }
            }
            $where['ap'] = $this->getAp();
            $where['domain'] = $this->getDomain();
            // 获取分公司
            $company = $this->userInfo['department'];
            $where['company'] = $company;

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
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
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

            $searchAddress = urldecode($_REQUEST['searchTextAddress']); // 查询内容
            $url = U('OrderHandle/searchviewAddress', array('searchAddress' => $searchAddress));
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
            $searchSendname = $_REQUEST['searchSendname']; // 查询的送餐员

            if (isset($searchSendname)) {
                $where['sendname'] = $searchSendname;
                $this->assign('searchSendname', $searchSendname);
                $_SESSION['orderHandleSearchForSendnameSendnameText'] = $searchSendname;
            } else {
                if (isset($_SESSION['orderHandleSearchForSendnameSendnameText'])) {
                    $where['sendname'] = $_SESSION['orderHandleSearchForSendnameSendnameText'];
                    $this->assign('searchSendname', $_SESSION['searchText' . $moduleName . 'Sendname']);
                } else {
                    $where['sendname'] = '';
                }
            }

            $where['ap'] = $this->getAp();
            $where['domain'] = $this->getDomain();
            // 获取分公司
            $company = $this->userInfo['department'];
            $where['company'] = $company;

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
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);
            //保存页数
            $_SESSION[$moduleName . 'searchviewsendname' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $listData = array();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
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

            $searchSendname = urldecode($_REQUEST['searchTextSendname']); // 查询的送餐员
            $url = U('OrderHandle/searchviewSendname', array('searchSendname' => $searchSendname));
            $this->assign('url', $url);
            $this->assign('searchSendname', $searchSendname);

            $this->assign('moduleId', $moduleId);
            $this->assign('returnAction', 'searchviewSendname'); // 定义返回的方法
            $this->getSendnameproductsByName(); // 显示送餐员餐数情况

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
     * 送餐员未派单查询
     */
    public function searchviewSendnameNoPaidan()
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
            $searchSendname = $_REQUEST['searchSendname']; // 查询的送餐员
           

            $where['ap'] = $this->getAp();
            $where['length(sendname)'] = array('eq',0);
            $where['domain'] = $this->getDomain();
            // 获取分公司
            $company = $this->userInfo['department'];
            $where['company'] = $company;

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
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);
            //保存页数
            $_SESSION[$moduleName . 'searchviewsendname' . 'page'] = $pageNumber;

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $listData = array();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
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

            $searchSendname = urldecode($_REQUEST['searchTextSendname']); // 查询的送餐员
            $url = U('OrderHandle/searchviewSendnameNoPaidan', array('searchSendname' => $searchSendname));
            $this->assign('url', $url);
            $this->assign('searchSendname', $searchSendname);

            $this->assign('moduleId', $moduleId);
            $this->assign('returnAction', 'searchviewSendname'); // 定义返回的方法
            $this->getSendnameproductsByName(); // 显示送餐员餐数情况

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
            $searchText = urldecode($_REQUEST['searchOther']); // 查询内容
            foreach ($focus->searchFields as $value) {
                $where[$value] = array(
                    'like',
                    '%' . $searchText . '%',
                );
            }
            $where['_logic'] = 'OR';

            $map['_complex'] = $where;
            $map['domain'] = $this->getDomain();
            // 获取分公司
            $company = $this->userInfo['department'];
            $map['company'] = $company;
            $map['ap'] = $this->getAp();
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
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'searchviewother' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);
            $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
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

            $searchOther = urldecode($_REQUEST['searchTextOther']); // 查询内容
            $url = U('OrderHandle/searchviewOther', array('searchOther' => $searchOther));
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
        $sendname = urldecode($_REQUEST['searchTextSendname']); // 查询的送餐员
        $userInfo = $this->userInfo;
        $company = $userInfo['department'];

        // 产品表
        $sendnameproductsModel = D('Sendnameproducts');
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();

        // 定义统计表
        $tongji = array();
        $productsResult = $sendnameproductsModel->Distinct(true)->field('productsname,shortname')->where($where)->select();

        // 定义统计表
        $tongji = array();

        // 查询装箱
        $listHeader = array();
        foreach ($productsResult as $value) {
            $tongji['装箱'][$value['shortname']] = 0;
            $tongji['已送'][$value['shortname']] = 0;
            $tongji['剩余'][$value['shortname']] = 0;
            $listHeader[] = $value['shortname'];
        }

        //$this->sendnameProductsListHeader = $listHeader;

        // 查询
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        if (!empty($zhuangxiangproductsArray)) {
            $where['shortname'] = array('in', $zhuangxiangproductsArray);
        }
        $sendnameProductsResult = $sendnameproductsModel->where($where)->select();
        foreach ($sendnameProductsResult as $key => $value) {
            if ($value['type'] == '装箱') {
                $tongji['装箱'][$value['shortname']] += $value['number'];
            }
            if ($value['type'] == '已送') {
                $tongji['已送'][$value['shortname']] += $value['number'];
            }
        }

        // 计算剩余
        foreach ($listHeader as $value) {
            if ($tongji['装箱'][$value] > 0) { //只有装箱的产品,才计算剩余
                $tongji['剩余'][$value] = $tongji['装箱'][$value] - $tongji['已送'][$value];
            } else {
                $tongji['剩余'][$value] = '-';
            }
        }
        $zhuangxiang = '';
        $yisong = '';
        $shengyu = '';
        //组成返回字符串
        foreach ($tongji as $key => $value) {
            if ($key == '装箱') {
                foreach ($value as $childKey => $childValue) {
                    if ($childValue > 0) { //排除掉不是装箱的产品,数量为零
                        $zhuangxiang .= $childValue . '*' . $childKey . ' ';
                    }
                }
            }
            if ($key == '已送') {
                foreach ($value as $childKey => $childValue) {
                    $yisong .= $childValue . '*' . $childKey . ' ';
                }
            }
            if ($key == '剩余') {
                foreach ($value as $childKey => $childValue) {
                    if ($childValue == '-') {
                        //排除掉非装箱的剩余
                    } else {
                        $shengyu .= $childValue . '*' . $childKey . ' ';
                    }

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
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        // 获得处理过了的编码
        $code = $_REQUEST['code'];
        // 订单号
        $orderformid = $_REQUEST['orderformid'];

        // 获得订单号
        $where = array();
        $where['orderformid'] = $orderformid;
        $orderformResult = $focus->field('ordersn,sendname,address,ordertxt,origin')->where($where)->find();
        $ordersn = $orderformResult['ordersn'];
        $origin = $orderformResult['origin'];
        $addressordertxt = trim($orderformResult['address']) . trim($orderformResult['ordertxt']);
        if (empty($origin)) {
            $origin = '';
        }

        // 定义返回的数组
        $returnInfo = array();

        /**
         * 先编辑送餐员的编码 **
         */
        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['code'] = $code; // 送餐员的编号
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $sendnameResult = $sendnameMgrModel->field('name,telphone,weixin')->where($where)->find();

        if ($sendnameResult) {
            $sendname = $sendnameResult['name'];
            $telphone = $sendnameResult['telphone'];
            $sendtype = $sendnameResult['sendtype'];
            if (!empty($sendnameResult['weixin'])) {
                $weixin = $sendnameResult['weixin'];
            } else {
                $weixin = '';
            }

        } else {
            $returnInfo['error'] = 'error';
            $returnInfo['msg'] = '没有查到信息';
            $this->ajaxReturn($returnInfo);
        }
        // 查询订单的本身状态
        $data = array();
        //$where = array();
        //$where ['orderformid'] = $orderformid;
        //$orderformResult = $focus->field('state')->where($where)->find();
        if (!empty($orderformResult)) {
            if ($orderformResult['state'] == '退餐') { // 立即返回
                $data['state'] = '已退餐';
            } elseif ($orderformResult['state'] == '已退餐') {
                $data['state'] = '已退餐';
            } elseif ($orderformResult['state'] == '废单') {
                $data['state'] = '废单';
            } elseif ($orderformResult['state'] == '已作废') {
                $data['state'] = '已作废';
            } else {
                $data['state'] = '已处理';
            }
        } else {
            $data['state'] = '已处理';
        }

        $data['sendname'] = $sendname;
        $state = $data['state'];
        $where = array();
        $where['ordersn'] = $ordersn;
        $where['company'] = $company;
        $focus->where($where)->save($data);
        // 输入短信表中，如果要发短信的话
        if (!empty($telphone) and (strlen($telphone) >= 9)) {
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
            $smsString = '地址:' . $orderform['address'] . ' 客户:' . $orderform['clientname'] . chr(10) .
            '订餐:' . $orderform['ordertxt'] . chr(10) .
            '要餐时间:' . $orderform['custtime'] . '电话:' . $orderform['telphone'] . chr(10) .
            '备注:' . $orderform['beizhu'] . chr(10) .
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
            $smsData['telphone'] = $telphone;
            $smsData['content'] = $smsString;
            $smsData['firstdate'] = date('Y-m-d H:i:s');
            $smsData['company'] = $company;
            //$smsData ['sendtype'] = '信息';
            $smsData['sendname'] = $sendname;
            $smsData['orderformid'] = $orderformid;
            $smsData['weixin'] = $weixin;
            $smsData['domain'] = $this->getDomain();
            $smsmgr_model->add($smsData);
        }

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $data = array();
        $data['orderformid'] = $orderformid; // 订单号
        $data['ordersn'] = $ordersn;
        $data['action'] = "订单配送给" . $sendname . "送餐员";
        $data['logtime'] = date('H:i:s');
        $data['domain'] = $this->getDomain();
        $orderactionModel->create();
        $result = $orderactionModel->add($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data['handle'] = 1;
        $data['handletime'] = date('Y-m-d H:i:s');
        $data['handlecontent'] = $sendname . ' ' . $telphone;
        $where = array();
        $where['orderformid'] = $orderformid;
        $orderstateModel->where($where)->save($data);

        //通知客户的消息, 如果是微信或者推送
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['telphone'] = $telphone;
        $data['app_tk'] = $sendname;
        $data['contenttype'] = 'deliver';
        $data['origin'] = $orderformResult['origin'];
        $data['domain'] = $this->getDomain();
        $notifyclientModel = D('NotifyClient');
        $notifyclientModel->create();
        $notifyclientModel->add($data);

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

        // 取得分公司名称
        $company = $this->userInfo['department'];
        // 保存到送餐员餐售情况
        $sendnameproductsModel = D('Sendnameproducts');
        $where = array();
        $where['extid'] = $orderformid;
        $where['type'] = '已送';
        $where['domain'] = $this->getDomain();
        $sendnameproductsModel->where($where)->delete();

        // 查询订货
        $orderproductsModel = D('Orderproducts');
        $where = array();
        $where['orderformid'] = $orderformid;
        $orderproductsResult = $orderproductsModel->where($where)->select();

        // 是为了计算装箱送餐员的饭
        foreach ($orderproductsResult as $productsValue) {
            $code = $productsValue['code'];
            $name = $productsValue['name'];
            $shortname = $productsValue['shortname'];
            $price = $productsValue['price'];
            $number = $productsValue['number'];
            $money = $productsValue['money'];
            $data = array();
            $data['productsname'] = $name;
            $data['shortname'] = $shortname;
            $data['type'] = '已送';
            $data['number'] = $number;
            $data['extid'] = $orderformid;
            $data['sendname'] = $sendname; // 送餐员
            $data['company'] = $company;
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['domain'] = $this->getDomain();
            $sendnameproductsModel->create();
            $sendnameproductsModel->add($data);
        }

        /*****************************************
         * 新加的代码,写入对送餐员APP的数据通知的代码
         */
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['sendname'] = $sendname;
        $data['company'] = $company;
        $data['domain'] = $this->getDomain();
        $data['date'] = date('Y-m-d H:i:s');
        $data['ap'] = $this->getAp();
        //如果订单还没有派单
        if (empty($orderformResult['sendname'])) {
            $data['type'] = 'order'; //新订单
            $sendnameappModel = D('Sendnameapp');
            $sendnameappModel->create();
            $sendnameappModel->add($data);
        } else {
            //已派单,但是还是原来的送餐员
            if ($orderformResult['sendname'] == $sendname) {
                if ($orderformResult['state'] == '改单') {
                    $data['type'] = 'change';
                } else if ($orderformResult['state'] == '打印改') {
                    $data['type'] = 'change';
                } else if ($orderformResult['state'] == '催送') {
                    $data['type'] = 'hurry';
                } else if ($orderformResult['state'] == '打印催') {
                    $data['type'] = 'hurry';
                } else if ($orderformResult['state'] == '退餐') {
                    $data['type'] = 'return';
                } else if ($orderformResult['state'] == '作废') {
                    $data['type'] = 'return';
                } else {
                    $data['type'] = 'order';
                }

                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);
            }

            //已派单,送餐员不相同
            if ($orderformResult['sendname'] != $sendname) {
                //先保存新送餐员的信息
                $data['type'] = 'order';
                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);

                //再处理原来的送餐员的信息
                if ($orderformResult['state'] == '改单') {
                    $data['type'] = 'again';
                } else if ($orderformResult['state'] == '打印改') {
                    $data['type'] == 'agian';
                } else if ($orderformResult['state'] == '催送') {
                    $data['type'] == 'agian';
                } else if ($orderformResult['state'] == '打印催') {
                    $data['type'] == 'agian';
                } else if ($orderformResult['state'] == '退餐') {
                    $data['type'] = 'return';
                } else if ($orderformResult['state'] == '作废') {
                    $data['type'] = 'return';
                } else {
                    $data['type'] = 'again';
                }
                $data['sendname'] = $orderformResult['sendname'];
                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);
            }

        }

        // 写入到营收状态表
        $data = array();
        $data['orderformid'] = $orderformid;
        $data['ordersn'] = $ordersn;
        $data['status'] = 0;
        $data['assisstatus'] = 0;
        $data['domain'] = $this->getDomain();
        $orderyingshouexchangeModel = D('Orderyingshouexchange');
        $orderyingshouexchangeModel->create();
        $orderyingshouexchangeModel->add($data);

        //写入到网站状态接口表
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['type'] = 3; //表示派单
        $data['content'] = "订单派给[" . $sendname . "]送餐员";
        $data['date'] = date('Y-m-d H:i:s');
        $data['origin'] = $origin;
        $data['domain'] = $this->getDomain();
        $webstatusModel = D('Webstatus');
        $webstatusModel->create();
        $webstatusModel->add($data);

        //处理电子发票,把电子发票发送给客户
        $invoicemgrModel = D('invoice');
        $where = array();
        $where['ordersn'] = $ordersn;
        $where['type'] = 3;
        $invoicemgrResult = $invoicemgrModel->where($where)->find();
        //把电子票信息存入invoiceweb
        if (!empty($invoicemgrResult)) {
            //返回收款人和复核人的信息,如果为空,就写默认
            $companymgrModel = D('companymgr');
            $where = array();
            $where['name'] = $company;
            $where['domain'] = $this->getDomain();
            $companymgrResult = $companymgrModel->field('cashier,checker')->where($where)->find();
            if (empty($companymgrResult['cashier'])) {
                $invoicemgrResult['cashier'] = '丽华';
            } else {
                $invoicemgrResult['cashier'] = $companymgrResult['cashier'];
            }
            if (empty($companymgrResult['checker'])) {
                $invoicemgrResult['checker'] = '丽华';
            } else {
                $invoicemgrResult['checker'] = $companymgrResult['checker'];
            }
            // 接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $opername = $userInfo['truename'];
            //将发票数据存入到invoiceweb表
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['ordertxt'] = $invoicemgrResult['orderformtxt'];
            $data['header'] = $invoicemgrResult['header'];
            $data['body'] = $invoicemgrResult['body'];
            $data['money'] = $invoicemgrResult['ordermoney'];
            $data['KPR'] = mb_substr($opername, 0, 3, 'utf-8'); //开票人
            $data['SKR'] = $invoicemgrResult['cashier']; //收款人
            $data['FHR'] = $invoicemgrResult['checker']; //复核人
            $data['gmf_nsrsbh'] = strtoupper($invoicemgrResult['gmf_nsrsbh']); //购买方纳税人识别号
            $data['gmf_dzdh'] = $invoicemgrResult['gmf_dzdh'];
            $data['gmf_yhzh'] = $invoicemgrResult['gmf_yhzh'];
            $data['issms'] = 1;
            $data['telphone'] = $orderform['telphone'];
            $data['type'] = 1;
            $data['date'] = date('Y-m-d H:i:s');
            $data['createdate'] = date('Y-m-d H:i:s');
            // 获取分公司
            $company = $this->userInfo['department'];
            $data['company'] = $company;
            $data['state'] = 0;
            $data['domain'] = $this->getDomain();
            $where = array();
            $where['ordersn'] = $ordersn;
            $invoicewebModel = D('invoiceweb');
            $invoicewebResult = $invoicewebModel->where($where)->find();
            if (empty($invoicewebResult)) {
                //hack一下,让电子票能够识别不同的地区
                if ($this->getDomain() == 'bj.lihuaerp.com') {
                    $data['eticketno'] = '1' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                } else {
                    $data['eticketno'] = '2' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                }
                $invoicewebModel->create();
                $invoicewebModel->add($data);
            } else {
                if ($invoicewebResult['state'] != 2) {
                    $data['eticketno'] = $invoicewebResult['eticketno'];
                    $invoicewebModel->where($where)->save($data);
                }
            }
            $eticketno = $data['eticketno'];

            //提取信息存入sms库中
            // * 2017.08.28作废,恢复
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['telphone'] = $orderform['telphone'];
            $data['content'] = "您的电子票提取码:" . $eticketno . "请您到http://invoice.lihua.com/?n=" . $eticketno . " 上开具发票!谢谢!";
            $data['createdate'] = date('Y-m-d H:i:s');
            $data['domain'] = $this->getDomain();
            $invoicesmsModel = D('invoicesms');
            $invoicesmsModel->create();
            $invoicesmsModel->add($data);

            // 同时写入日志中
            // 记入操作到action中
            $orderactionModel = D('Orderaction');
            $data = array();
            $data['orderformid'] = $orderformid; // 订单号
            $data['ordersn'] = $ordersn;
            $data['action'] = "产生电子票提取码:" . $eticketno;
            $data['logtime'] = date('H:i:s');
            $data['domain'] = $this->getDomain();
            $orderactionModel->create();
            $result = $orderactionModel->add($data);
        }

        //将订单配送情况,写入送餐监测表中
        $checkorderformModel = D('checkorderform');
        $where = array();
        $where['ordersn'] = $ordersn;
        $checkorderformResult = $checkorderformModel->where($where)->find();
        if (empty($checkorderformResult)) {
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['ordertxt'] = $addressordertxt;
            $data['sendname'] = $sendname;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['noreceivetime'] = date('H:i:s');
            $checkorderformModel->create();
            $checkorderformModel->add($data);
        } else {
            $data = array();
            $data['sendname'] = $sendname;
            $data['ordertxt'] = $addressordertxt;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['noreceivetime'] = date('H:i:s');
            $checkorderformModel->where($where)->save($data);
        }

        //删除一下备餐表中的信息，防止错误
        $productsprepareModel = D('productsprepare');
        $where = array();
        $where['ordersn'] = $ordersn;
        $productsprepareModel->where($where)->delete();

        // 定义返回
        $returnInfo['success'] = 'success';
        $orderformData['sendname'] = $sendname;
        $orderformData['state'] = $state;
        $returnInfo['data'] = $orderformData;
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 显示产品明细等
    public function showproducts()
    {
        // 取得记录号
        $record = $_REQUEST['orderformid'];

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
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['name'] = urldecode($_REQUEST['sendname']); // 送餐员的编号
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($where)->find();

        if ($sendnameResult) {
            $sendname = $sendnameResult['name'];
            $telphone = $sendnameResult['telphone'];
        }

        // 输入短信表中，如果要发短信的话
        if (!empty($telphone) and (strlen($telphone) > 6)) {
            // 计算已经发生的信息量
            $smsmgrModel = D('Smsmgr');
            $smsString = urldecode($_REQUEST['msgcontent']);
            $smsData = array();
            $smsData['telphone'] = $telphone;
            $smsData['content'] = $smsString;
            $smsData['firstdate'] = date('Y-m-d H:i:s');
            $smsData['company'] = $company;
            $smsData['sendtype'] = '短信';
            $smsData['sendname'] = urldecode($_REQUEST['sendname']);
            $smsData['orderformid'] = '';
            $smsData['domain'] = $this->getDomain();
            $smsmgrResult = $smsmgrModel->add($smsData);
            if ($smsmgrResult) {
                // 定义返回
                $returnInfo['success'] = 'success';
                $returnInfo['msg'] = '单发消息保存成功';
                $this->ajaxReturn($returnInfo, 'JSON');
            } else {
                $returnInfo['error'] = 'error';
                $returnInfo['msg'] = '单发消息保存失败';
                $this->ajaxReturn($returnInfo);

            }
        }
    }

    /* 取得打印需要的数据 */
    public function getPrintOrder()
    {
        $companyparamModel = D('companyparam');
        $where = array();
        $where['domain'] = $this->getDomain();
        $companyparamResult = $companyparamModel->where($where)->find();
        if ($companyparamResult['accountprinted'] == 2) {
            $accounttype = 2; //需要打印
        } else {
            $accounttype = 1; //不需要打印
        }

        // 取得订单号
        $record = $_REQUEST['orderformid'];

        $orderformModel = D('Orderform');
        // 查询订单
        $orderform = $orderformModel->where("orderformid=$record")->find();

        $orderprinterModel = D('Orderprinter');
        // 分公司的名称
        $company = $this->userInfo['department'];
        $where = array();
        $where['orderformid'] = $record;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        //获取订单打印数量
        $printnumberResult = $orderprinterModel->where($where)->find();
        if (empty($printnumberResult)) { //如果不存在打印号，就生成打印号
            $where = array();
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $print_number = $orderprinterModel->where($where)->count();
            if ($print_number == 0) {
                $print_number = 1;
            } else {
                $print_number = $print_number + 1;
            }

            $print_number = $print_number + 300;
            $data = array();
            $data['printnumber'] = $print_number;
            $data['orderformid'] = $record;
            $data['ordersn'] = $orderform['ordersn'];
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['date'] = date('Y-m-d H:i:s');
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

        $order['orderform'] = $orderform;
        $order['orderproducts'] = $orderproducts;
        $order['orderactivity'] = $orderactivity;
        $order['orderpayment'] = $orderpayment;
        $order['accounttype'] = $accounttype;

        $this->ajaxReturn($order, 'JSON');
    }

    /* 设定订单已打印状态 */
    public function setOrderPrinted()
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];
        // 取得订单号
        $record = $_REQUEST['orderformid'];
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where['orderformid'] = $record;
        $orderformResult = $orderformModel->field('state,ordersn')->where($where)->find();
        $ordersn = $orderformResult['ordersn'];
        if (!empty($orderformResult)) {
            if ($orderformResult['state'] == '退餐') { // 立即返回
                $data['state'] = '已退餐';
            } elseif ($orderformResult['state'] == '已退餐') {
                $data['state'] = '已退餐';
            } elseif ($orderformResult['state'] == '废单') {
                $data['state'] = '废单';
            } elseif ($orderformResult['state'] == '已作废') {
                $data['state'] = '已作废';
            } else {
                $data['state'] = '已打印';
            }
        } else {
            $data['state'] = '已打印';
        }
        $where['company'] = $company;
        $result = $orderformModel->where($where)->save($data);

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $action['ordersn'] = $orderformResult['ordersn'];
        $action['action'] = $company . "打印订单";
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $this->getDomain();
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        // 写入到营收状态表
        $data = array();
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $data['status'] = 0;
        $data['assisstatus'] = 1;
        $data['domain'] = $this->getDomain();
        $orderyingshouexchangeModel = D('Orderyingshouexchange');
        $orderyingshouexchangeModel->create();
        $orderyingshouexchangeModel->add($data);

        //吸入到备餐系统中
        $this->writeProductsPrepare();
    }

    //写入到备餐系统中
    public function writeProductsPrepare()
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];
        // 取得订单号
        $record = $_REQUEST['orderformid'];
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where['orderformid'] = $record;
        $orderformResult = $orderformModel->field('state,ordersn')->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        // 写入到备餐标中
        $orderproductsModel = D('orderproducts');
        $productsprepareModel = D('productsprepare');
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderproductsResult = $orderproductsModel->where($where)->select();
        $where = array();
        $where['ordersn'] = $ordersn;
        $productsprepareModel->where($where)->delete();
        foreach ($orderproductsResult as $orderproducts) {
            $data = array();
            $data['ordersn'] = $orderformResult['ordersn'];
            $data['orderformid'] = $record;
            $data['code'] = $orderproducts['code'];
            $data['name'] = $orderproducts['name'];
            $data['shortname'] = $orderproducts['shortname'];
            $data['number'] = $orderproducts['number'];
            $data['price'] = $orderproducts['price'];
            $data['money'] = $orderproducts['money'];
            $data['create_time'] = date('H:i:s');
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $productsprepareModel->create();
            $productsprepareModel->add($data);
        }
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

            $where = array();
            $where['domain'] = $this->getDomain();
            $listResult = $focus->where($where)->field('name')->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
            } else {
                $listDate['rows'] = array();
                $listData['total'] = 0;
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
    public function setBeizhuOrder()
    {
        // 取得订单号
        $record = $_REQUEST['orderformid'];
        // 取得备注内容
        $beizhu = ' ' . urldecode($_REQUEST['beizhu']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where['orderformid'] = $record;
        $data['beizhu'] = array(
            'exp',
            "concat('$beizhu',beizhu)",
        );
        $result = $orderformModel->where($where)->save($data);

        $where = array();
        $where['orderformid'] = $record;
        $orderformResult = $orderformModel->field('ordersn')->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $action['ordersn'] = $ordersn;
        $company = $this->userInfo['department'];
        $action['action'] = $company . "订单号:" . $record . "订单备注：" . $beizhu;
        $action['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        $where = array();
        $where['orderformid'] = $record;
        $result = $orderformModel->field('beizhu')->where($where)->find();

        // 返回成功
        $returnInfo['success'] = 'success';
        $returnInfo['beizhu'] = $result['beizhu'];
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    /**
     * 将订单转其他分公司的输入页面
     */
    public function distributeOtherCompanyInput()
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
    public function setOtherCompany()
    {
        // 取得订单号
        $record = $_REQUEST['orderformid'];
        // 取得备注内容
        $company = ' ' . urldecode($_REQUEST['company']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where['orderformid'] = $record;
        $data['company'] = $company;
        $result = $orderformModel->where($where)->save($data);

        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $orignCompany = $this->userInfo['department'];

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $orderactionData['ordersn'] = $record; // 订单号
        $company = $data['company'];
        $orderactionData['action'] = "分公司:"+$orignCompany+"将订单转分给" . $company . "配送点";
        $orderactionData['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($orderactionData);

        // 返回成功
        $returnInfo['success'] = 'success';
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    /**
     * 转给分送点的页面
     */
    public function distributeOrderSecondPointInput()
    {
        if (IS_POST) {
            // 分公司的名称
            $company = $this->userInfo['department'];

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
        $record = $_REQUEST['orderformid'];
        // 取得备注内容
        $secondPointName = ' ' . urldecode($_REQUEST['secondPointName']);
        // 查询订单
        $orderformModel = D('Orderform');
        // 查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
        $data = array();
        $where = array();
        $where['orderformid'] = $record;
        $data['secondpointname'] = $secondPointName;
        $result = $orderformModel->where($where)->save($data);

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $company = $this->userInfo['department'];
        $action['action'] = $company . "订单号:" . $record . "转给分送点：" . $secondPointName;
        $action['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        // 返回成功
        $returnInfo['success'] = 'success';
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
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['company'];

        $companymgr_model = D('Companymgr');
        $printtype = $companymgr_model->field('printtype')->where("name='$name'")->find();
        // dump($printtype);

        $this->assign('printtype', $printtype['printtype']); // 指定字段获得焦点

        // dump($this->blocks);
        $this->display('./Tpl/OrderHandle/setprintupdateview.html');
    }

    // 保存打印设置
    public function saveSetPrint()
    {

        // 打印类型
        $printtype = $_REQUEST['printtypesetup'];

        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['company'];

        $companymgr_model = D('Companymgr');
        $data['printtype'] = $printtype;
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
    public function getOrderMonit()
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $userInfo['department'];

        $where = array();
        $where['name'] = $company;
        $where['domain'] = $this->getDomain();
        $ordermonit_model = D('Ordermonit');
        $ordermonit = $ordermonit_model->where($where)->select();
        if (empty($ordermonit)) {
            $ordermonit = array();
        }
        $this->ajaxReturn($ordermonit, 'JSON');
    }

    // 根据代码获取送餐员名字
    public function getSendnameByCode()
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $userInfo['department'];

        // 获得处理过了的编码
        $code = $_REQUEST['code'];

        // 定义返回的数组
        $returnInfo = array();

        /**
         * 先编辑送餐员的编码 **
         */
        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $sendnameWhere['code'] = $code; // 送餐员的编号
        $sendnameWhere['company'] = $company;
        $sendnameWhere['domain'] = $this->getDomain();
        $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($sendnameWhere)->find();
        if ($sendnameResult) {
            $sendname = $sendnameResult['name'];
            $telphone = $sendnameResult['telphone'];
        } else {
            $returnInfo['error'] = 'error';
            $returnInfo['msg'] = '没有查到信息';
            $this->ajaxReturn($returnInfo);
        }
        // 根据送餐员信息，处理订单
        $orderformData['sendname'] = $sendname;

        // 定义返回
        $returnInfo['success'] = 'success';
        $returnInfo['data'] = $orderformData;
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    // 返回所有送餐员的名称和代码
    public function getSendnameMgr()
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
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
        $record = $_REQUEST['orderformid'];

        // 查询当前的订单状态
        $where = array();
        $where['orderformid'] = $record;
        $orderformResult = $focus->field('state,sendname,ordersn')->where($where)->find();
        $ordersn = $orderformResult['ordersn']; // 订单号

        if (!empty($orderformResult)) {
            if ($orderformResult['state'] == '退餐') {
                $where = array();
                $where['orderformid'] = $record;
                $data = array();
                $data['state'] = '已退餐';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $record; // 订单号
                $company = $this->userInfo['department'];
                $data['action'] = $company . "将订单处理成已退餐";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                // 写入到状态表中
                $orderstateModel = D('Orderstate');
                $data = array();
                $data['cancel'] = 1;
                $data['canceltime'] = date('Y-m-d H:i:s');
                $data['cancelcontent'] = $company . '处理成已退餐';
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $where = array();
                $where['orderformid'] = $record;
                $orderstateModel->where($where)->save($data);

                /*****************************************
                 * 新加的代码,写入对送餐员APP的数据通知的代码
                 */
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['sendname'] = $orderformResult['sendname'];
                $data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $data['date'] = date('Y-m-d H:i:s');
                $data['ap'] = $this->getAp();
                $data['type'] = 'return'; //新订单
                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);

                //消息给送餐员
                if ($orderformResult['sendname'] == '') {
                    $this->sendSmsToSendname($record, $ordersn, $orderformResult['sendname'], '退餐');
                }
                // 返回成功
                $returnInfo['success'] = 'success';
                $returnInfo['state'] = '已退餐';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult['state'] == '改单') {

                $where = array();
                $where['orderformid'] = $record;
                $data = array();
                $data['state'] = '已更改';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $record; // 订单号
                $data['ordersn'] = $ordersn;
                $company = $this->userInfo['department'];
                $data['action'] = $company . "将订单处理成已更改";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                /*****************************************
                 * 新加的代码,写入对送餐员APP的数据通知的代码
                 */
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['sendname'] = $orderformResult['sendname'];
                $data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $data['date'] = date('Y-m-d H:i:s');
                $data['ap'] = $this->getAp();
                $data['type'] = 'change'; //新订单
                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);

                //消息给送餐员
                if ($orderformResult['sendname'] == '') {
                    $this->sendSmsToSendname($record, $ordersn, $orderformResult['sendname'], '改单');
                }
                // 返回成功
                $returnInfo['success'] = 'success';
                $returnInfo['state'] = '已更改';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult['state'] == '打印改') {
                $where = array();
                $where['orderformid'] = $record;
                $data = array();
                $data['state'] = '已更改';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $record; // 订单号
                $data['ordersn'] = $ordersn;
                $company = $this->userInfo['department'];
                $data['action'] = $company . "将订单处理成已更改";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                if ($orderformResult['sendname'] == '') {
                    $this->sendSmsToSendname($record, $ordersn, $orderformResult['sendname'], '改单');
                }
                // 返回成功
                $returnInfo['success'] = 'success';
                $returnInfo['state'] = '已更改';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult['state'] == '打印催') {
                $where = array();
                $where['orderformid'] = $record;
                $data = array();
                $data['state'] = '已催送';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $record; // 订单号
                $data['ordersn'] = $ordersn;
                $company = $this->userInfo['department'];
                $data['action'] = $company . "将订单处理成已催送";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                if ($orderformResult['sendname'] == '') {
                    $this->sendSmsToSendname($record, $ordersn, $orderformResult['sendname'], '催送');
                }

                // 返回成功
                $returnInfo['success'] = 'success';
                $returnInfo['state'] = '已催送';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            if ($orderformResult['state'] == '催送') {
                if ($orderformResult['sendname'] == '') {
                    // 返回成功
                    $returnInfo['error'] = 'error';
                    $returnInfo['msg'] = '订单还没有配送送餐员,无法处理催送！';
                    $this->ajaxReturn($returnInfo, 'JSON');
                }
                $where = array();
                $where['orderformid'] = $record;
                $data = array();
                $data['state'] = '已催送';
                $focus->where($where)->save($data);

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $record; // 订单号
                $data['ordersn'] = $ordersn;
                $company = $this->userInfo['department'];
                $data['action'] = $company . "将订单处理成已催送";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $data['ordersn'] = $ordersn;
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                /*****************************************
                 * 新加的代码,写入对送餐员APP的数据通知的代码
                 */
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['sendname'] = $orderformResult['sendname'];
                $data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $data['date'] = date('Y-m-d H:i:s');
                $data['ap'] = $this->getAp();
                $data['type'] = 'hurry'; //新订单
                $sendnameappModel = D('Sendnameapp');
                $sendnameappModel->create();
                $sendnameappModel->add($data);

                if ($orderformResult['sendname'] == '') {
                    $this->sendSmsToSendname($record, $ordersn, $orderformResult['sendname'], '催送');
                }

                // 返回成功
                $returnInfo['success'] = 'success';
                $returnInfo['state'] = '已催送';
                $this->ajaxReturn($returnInfo, 'JSON');
            }

            $returnInfo['error'] = 'error';
            $returnInfo['msg'] = '订单无法处理';
            $this->ajaxReturn($returnInfo, 'JSON');
        }

        // 返回错误
        $returnInfo['error'] = 'error';
        $returnInfo['msg'] = '订单不存在';
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
        $record = $_REQUEST['orderformid'];
        // 获得订单号
        $where = array();
        $where['orderformid'] = $record;
        $orderformResult = $focus->field('ordersn')->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        // 查询返回订单
        $where = array();
        $where['orderformid'] = $record;
        $data = array();
        $data['company'] = '';
        $data['sendname'] = '';
        $data['state'] = '返回';
        $result = $focus->where($where)->save($data);
        if ($result == false) {
            // 更新失败
            $returnInfo['success'] = 'error';
            $returnInfo['msg'] = '返回失败';
            $this->ajaxReturn($returnInfo, 'JSON');
        }

        // 同时写入日志中
        // 记入操作到action中
        $orderactionModel = D('Orderaction');
        $data = array();
        $data['orderformid'] = $record; // 订单号
        $company = $this->userInfo['department'];
        $data['action'] = $company . "将订单返回";
        $data['logtime'] = date('H:i:s');
        $orderactionModel->create();
        $result = $orderactionModel->add($data);

        // 写入到营收状态表
        $data = array();
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $data['status'] = 0;
        $data['assisstatus'] = 0;
        $data['domain'] = $this->getDomain();
        $orderyingshouexchangeModel = D('Orderyingshouexchange');
        $orderyingshouexchangeModel->create();
        $orderyingshouexchangeModel->add($data);

        // 返回成功
        $returnInfo['success'] = 'success';
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
        $sendnameProducts['content'] = '';
        // 送餐员代码
        $code = $_REQUEST['code'];
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['company'] = $company;
        $where['code'] = $code;
        $sendnameMgrResult = $sendnameMgrModel->field("code,name,telphone")->where($where)->find();
        if (empty($sendnameMgrResult)) {
            $this->ajaxReturn($sendnameProducts, 'JSON');
        } else {
            $sendname = $sendnameMgrResult['name'];
        }

        // 产品表
        $sendnameproductsModel = D('Sendnameproducts');
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        // 定义统计表
        $tongji = array();
        $productsResult = $sendnameproductsModel->Distinct(true)->field('productsname,shortname')->where($where)->select();

        // 查询装箱
        $listHeader = array();
        foreach ($productsResult as $value) {
            $tongji['装箱'][$value['shortname']] = 0;
            $tongji['已送'][$value['shortname']] = 0;
            $tongji['剩余'][$value['shortname']] = 0;
            $listHeader[] = $value['shortname'];
        }
        // $this->sendnameProductsListHeader = $listHeader;

        // 查询
        $returnData = array();
        $where = array();
        $where['sendname'] = $sendname;
        $where['company'] = $company;
        $sendnameProductsResult = $sendnameproductsModel->where($where)->select();
        foreach ($sendnameProductsResult as $key => $value) {
            if ($value['type'] == '装箱') {
                $tongji['装箱'][$value['shortname']] += $value['number'];
                $returnData['装箱'] .= $value['number'] . '*' . $value['shortname'] . ' ';
            }
            if ($value['type'] == '已送') {
                $tongji['已送'][$value['shortname']] += $value['number'];
                $returnData['已送'] .= $value['number'] . '*' . $value['shortname'] . ' ';
            }
        }

        // 计算剩余
        foreach ($listHeader as $value) {
            $tongji['剩余'][$value] = $tongji['装箱'][$value] - $tongji['已送'][$value];
            $returnData['剩余'] .= $tongji['剩余'][$value] . '*' . $value . ' ';
        }

        $sendnameProducts['content'] = $sendname . '  装箱:' . $returnData['装箱'] . ' 已送:' . $returnData['已送'] . ' 剩余:' . $returnData['剩余'];
        $this->ajaxReturn($sendnameProducts, 'JSON');
    }

    /**
     *发送消息到送餐员
     *para $record  订单号
     *para $sendname
     *para $state
     */
    public function sendSmsToSendname($record, $ordersn, $sendname, $state)
    {
        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        // 根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['name'] = $sendname; // 送餐员的编号
        $where['company'] = $company;
        $sendnameResult = $sendnameMgrModel->field('name,telphone,sendtype')->where($where)->find();
        if ($sendnameResult) {
            $sendname = $sendnameResult['name'];
            $telphone = $sendnameResult['telphone'];
            $sendtype = $sendnameResult['sendtype'];
        }

        // 输入短信表中，如果要发短信的话
        if (!empty($telphone) and (strlen($telphone) > 6)) {
            $orderformModel = D('Orderform');
            $where = array();
            $where['orderformid'] = $record;
            $orderform = $orderformModel->where($where)->find();

            $smsString = $orderform['address'] . '|' . $orderform['ordertxt'] . '|' . $state . '|' . $orderform['custtime'] . '|' . $orderform['telphone'];
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
            $smsData['telphone'] = $telphone;
            $smsData['content'] = $smsString;
            $smsData['firstdate'] = date('Y-m-d H:i:s');
            $smsData['company'] = $company;
            $smsData['sendtype'] = '短信';
            $smsData['sendname'] = $sendname;
            $smsData['orderformid'] = $record;
            $smsData['domain'] = $this->getDomain();
            $smsmgrModel->create();
            $smsmgrModel->add($smsData);
        }

        /*****************************************
         * 新加的代码,写入对送餐员APP的数据通知的代码
         */
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['sendname'] = $sendname;
        $data['company'] = $company;
        $data['domain'] = $this->getDomain();
        $data['date'] = date('Y-m-d H:i:s');
        $data['ap'] = $this->getAp();

        if ($state == '改单') {
            $data['type'] = 'change';
        } else if ($state == '催送') {
            $data['type'] = 'hurry';
        } else if ($state == '退餐') {
            $data['type'] = 'return';
        } else if ($state == '作废') {
            $data['type'] = 'return';
        } else {
            $data['type'] = 'order';
        }

        $sendnameappModel = D('Sendnameapp');
        $sendnameappModel->create();
        $sendnameappModel->add($data);
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
        $totalmoney = $orderform[0]['totalmoney'];
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
        $orderStateResult = $orderStateModel->where("orderformid=$record")->find(); //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderaction_model = D('Orderaction');
        $orderaction = $orderaction_model->where("orderformid=$record")->select(); //
        $this->assign('orderaction', $orderaction);
    }

    /**
     * 返回订单完成情况
     *
     */
    public function getOrderCondition()
    {
        $orderconditionModel = D('ordercondition');

        // 分公司的名称
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];

        $where = array();
        $where['ap'] = $this->getAp();
        $where['date'] = date('Y-m-d');
        $where['name'] = $company;
        $where['domain'] = $this->getDomain();
        $orderconditionResult = $orderconditionModel->where($where)->find();

        $this->ajaxReturn($orderconditionResult);
    }

    /***
     * 获取电子发票的信息
     * 以便打印电子发票信息和处理电子发票
     */
    public function getInvoiceInfo()
    {
        $orderformModel = D('orderform');
        $orderformid = $_REQUEST['orderformid'];

        //获取订单号
        $where = array();
        $where['orderformid'] = $orderformid;
        $orderformResult = $orderformModel->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $opername = $userInfo['truename'];

        // 获取分公司
        $company = $this->userInfo['department'];

        $orderprinterModel = D('Orderprinter');
        // 分公司的名称
        $company = $this->userInfo['department'];
        $where = array();
        $where['orderformid'] = $orderformid;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        //获取订单打印数量
        $printnumberResult = $orderprinterModel->where($where)->find();
        if (empty($printnumberResult)) { //如果不存在打印号，就生成打印号
            $where = array();
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
            $print_number = $orderprinterModel->where($where)->count();
            if ($print_number == 0) {
                $print_number = 1;
            } else {
                $print_number = $print_number + 1;
            }

            $print_number = $print_number + 300;
            $data = array();
            $data['printnumber'] = $print_number;
            $data['orderformid'] = $record;
            $data['ordersn'] = $orderform['ordersn'];
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['date'] = date('Y-m-d H:i:s');
            $orderprinterModel->create();
            $orderprinterModel->add($data);
        } else {
            $print_number = $printnumberResult['printnumber'];
        }

        $invoicemgrModel = D('invoice');
        $where = array();
        $where['type'] = 3; //是电子票
        $where['ordersn'] = $ordersn;
        $invoicemgrResult = $invoicemgrModel->where($where)->find();

        if (empty($invoicemgrResult)) {
            //没有发票,退出
            //$this->ajaxReturn(null);
        }

        //有电子发票
        if (!empty($invoicemgrResult)) {
            //返回收款人和复核人的信息,如果为空,就写默认
            $companymgrModel = D('companymgr');
            $where = array();
            $where['name'] = $company;
            $where['domain'] = $this->getDomain();
            $companymgrResult = $companymgrModel->field('cashier,checker')->where($where)->find();
            if (empty($companymgrResult['cashier'])) {
                $invoicemgrResult['cashier'] = mb_substr($opername, 0, 3, 'utf-8');
            } else {
                $invoicemgrResult['cashier'] = $companymgrResult['cashier'];
            }
            if (empty($companymgrResult['checker'])) {
                $invoicemgrResult['checker'] = '丽华';
            } else {
                $invoicemgrResult['checker'] = $companymgrResult['checker'];
            }

            //将发票数据存入到invoiceweb表
            $data = array();
            $data['invoiceid'] = $invoicemgrResult['invoiceid'];
            $data['ordersn'] = $ordersn;
            $data['orderformid'] = $orderformid;
            $data['print_number']= $print_number;
            $data['ordertxt'] = $invoicemgrResult['orderformtxt'];
            $data['header'] = $invoicemgrResult['header'];
            $data['body'] = $invoicemgrResult['body'];
            $data['money'] = $invoicemgrResult['ordermoney'];
            $data['KPR'] = mb_substr($opername, 0, 3, 'utf-8'); //开票人
            $data['SKR'] = $invoicemgrResult['cashier']; //收款人
            $data['FHR'] = $invoicemgrResult['checker']; //复核人
            $data['gmf_nsrsbh'] = strtoupper($invoicemgrResult['gmf_nsrsbh']); //购买方纳税人识别号
            $data['gmf_dzdh'] = $invoicemgrResult['gmf_dzdh'];
            $data['gmf_yhzh'] = $invoicemgrResult['gmf_yhzh'];
            $data['printman'] = mb_substr($opername, 0, 3, 'utf-8'); //操作员
            $data['telphone'] = $orderformResult['telphone'];
            $data['type'] = 1;
            $data['date'] = date('Y-m-d');
            $data['createdate'] = date('Y-m-d H:i:s');
            // 获取分公司
            $company = $this->userInfo['department'];
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $where = array();
            $where['ordersn'] = $ordersn;
            //$where['state'] = array('EQ', 0);
            //$where['cancel_state'] = 0;
            $invoicewebModel = D('invoiceweb');
            $invoicewebResult = $invoicewebModel->where($where)->find();
            if (empty($invoicewebResult)) {
                //hack一下,让电子票能够识别不同的地区
                if ($this->getDomain() == 'bj.lihuaerp.com') {
                    $data['eticketno'] = '1' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                } else {
                    $data['eticketno'] = '2' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                }
                $data['state'] = 0; //启动开票
                $invoicewebModel->create();
                $invoicewebModel->add($data);
            } else {
                $where['state'] = array('NEQ', 2);
                $where['cancel_state'] = 0;
                $data['state'] = 0; //启动开票
                $data['eticketno'] = $invoicewebResult['eticketno'];
                $invoicewebModel->where($where)->save($data);
            }
            $eticketno = $data['eticketno'];
            // 同时写入日志中
            // 记入操作到action中
            $orderactionModel = D('Orderaction');
            $actiondata = array();
            $actiondata['orderformid'] = $orderformid; // 订单号
            $actiondata['ordersn'] = $ordersn;
            $actiondata['action'] = "产生电子票提取码:" . $eticketno;
            $actiondata['logtime'] = date('H:i:s');
            $actiondata['domain'] = $this->getDomain();
            $orderactionModel->create();
            $result = $orderactionModel->add($actiondata);

            $this->ajaxReturn($data);
        }
        $this->ajaxReturn(null);
    }

    /**
     * 打印订单的处理页面
     */
    public function OrderPrintView()
    {
        $this->display('OrderHandle/orderprintview');
    }

    //打印派单：根据输入的代码获取送餐员名字
    public function OrerPrintGetSendnameByCode()
    {

        //获得处理过了的编码
        $code = $_REQUEST['code'];

        //定义返回的数组
        $returnInfo = array();

        /** 先编辑送餐员的编码 ***/
        //根据编码取得送餐员姓名
        $sendnameMgrModel = D('Sendnamemgr');
        $where = array();
        $where['code'] = $code; //送餐员的编号
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];
        //查询条件
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();

        $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($where)->find();

        if ($sendnameResult) {
            $sendname = $sendnameResult['name'];
            $telphone = $sendnameResult['telphone'];
        } else {
            $returnInfo['error'] = 'error';
            $returnInfo['msg'] = '没有查到信息';
            $this->ajaxReturn($returnInfo);
        }
        //根据送餐员信息，处理订单
        $orderPrint['sendname'] = $sendname;

        //定义返回
        $returnInfo['success'] = 'success';
        $returnInfo['data'] = $orderPrint;
        $this->ajaxReturn($returnInfo, 'JSON');
    }

    //根据订单号码，取得订单详情
    public function getOrderTxtByid()
    {
        //打印号
        $orderPrintNumber = $_REQUEST['printNumber'];
        //送餐员姓名
        $sendname = urldecode($_REQUEST['sendname']);
        $where = array();
        $where['printnumber'] = $orderPrintNumber;
        $userInfo = $_SESSION['userInfo'];
        $company = $this->userInfo['department'];
        //查询条件
        $where['company'] = $company;
        $where['domain'] = $this->getDomain();
        //打印表
        $orderprinterModel = D('OrderPrinter');
        $orderprinter = $orderprinterModel->where($where)->find();
        if ($orderprinter) {
            $ordersn = trim($orderprinter['ordersn']);

            //订单表
            $orderformModel = D('Orderform');
            $where = array();
            $where['ordersn'] = $ordersn;
            $orderformResult = $orderformModel->where($where)->find();
            if (!empty($orderformResult)) {
                $address = $orderformResult['address'] . $orderformResult['clientname'];
                $ordertxt = $orderformResult['ordertxt'];
                $returnData['addressOrdertxt'] = $address . ' ' . $ordertxt . ' ' .
                    $orderformResult['custtime'];
                $returnData['ordersn'] = $ordersn;

                //将订单派给送餐员，改写订单的sendname字段
                if ($sendname !== $orderformResult['sendname']) {
                    $where = array();
                    $where['ordersn'] = $ordersn;
                    $data = array();
                    $data['sendname'] = $sendname;
                    $orderformModel->where($where)->save($data);
                }

                $orderformid = $orderformResult['orderformid'];
                $addressordertxt = trim($orderformResult['address']) . trim($orderformResult['ordertxt']);
                $origin = $orderformResult['origin'];

                // 同时写入日志中
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $data = array();
                $data['orderformid'] = $orderformid; // 订单号
                $data['ordersn'] = $ordersn;
                $data['action'] = "订单配送给" . $sendname . "送餐员";
                $data['logtime'] = date('H:i:s');
                $data['domain'] = $this->getDomain();
                $orderactionModel->create();
                $result = $orderactionModel->add($data);

                // 写入到状态表中
                $orderstateModel = D('Orderstate');
                $data = array();
                $data['handle'] = 1;
                $data['handletime'] = date('Y-m-d H:i:s');
                $data['handlecontent'] = $sendname . ' ' . $telphone;
                $where = array();
                $where['orderformid'] = $orderformid;
                $orderstateModel->where($where)->save($data);

                //通知客户的消息, 如果是微信或者推送
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['telphone'] = $telphone;
                $data['app_tk'] = $sendname;
                $data['contenttype'] = 'deliver';
                $data['origin'] = $orderformResult['origin'];
                $data['domain'] = $this->getDomain();
                $notifyclientModel = D('NotifyClient');
                $notifyclientModel->create();
                $notifyclientModel->add($data);

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

                // 取得分公司名称
                $company = $this->userInfo['department'];
                // 保存到送餐员餐售情况
                $sendnameproductsModel = D('Sendnameproducts');
                $where = array();
                $where['extid'] = $orderformid;
                $where['type'] = '已送';
                $where['domain'] = $this->getDomain();
                $sendnameproductsModel->where($where)->delete();

                // 查询订货
                $orderproductsModel = D('Orderproducts');
                $where = array();
                $where['orderformid'] = $orderformid;
                $orderproductsResult = $orderproductsModel->where($where)->select();

                // 是为了计算装箱送餐员的饭
                foreach ($orderproductsResult as $productsValue) {
                    $code = $productsValue['code'];
                    $name = $productsValue['name'];
                    $shortname = $productsValue['shortname'];
                    $price = $productsValue['price'];
                    $number = $productsValue['number'];
                    $money = $productsValue['money'];
                    $data = array();
                    $data['productsname'] = $name;
                    $data['shortname'] = $shortname;
                    $data['type'] = '已送';
                    $data['number'] = $number;
                    $data['extid'] = $orderformid;
                    $data['sendname'] = $sendname; // 送餐员
                    $data['company'] = $company;
                    $data['date'] = date('Y-m-d');
                    $data['ap'] = $this->getAp();
                    $data['domain'] = $this->getDomain();
                    $sendnameproductsModel->create();
                    $sendnameproductsModel->add($data);
                }

                /*****************************************
                 * 新加的代码,写入对送餐员APP的数据通知的代码
                 */
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['sendname'] = $sendname;
                $data['company'] = $company;
                $data['domain'] = $this->getDomain();
                $data['date'] = date('Y-m-d H:i:s');
                $data['ap'] = $this->getAp();
                //如果订单还没有派单
                if (empty($orderformResult['sendname'])) {
                    $data['type'] = 'order'; //新订单
                    $sendnameappModel = D('Sendnameapp');
                    $sendnameappModel->create();
                    $sendnameappModel->add($data);
                } else {
                    //已派单,但是还是原来的送餐员
                    if ($orderformResult['sendname'] == $sendname) {
                        if ($orderformResult['state'] == '改单') {
                            $data['type'] = 'change';
                        } else if ($orderformResult['state'] == '打印改') {
                            $data['type'] = 'change';
                        } else if ($orderformResult['state'] == '催送') {
                            $data['type'] = 'hurry';
                        } else if ($orderformResult['state'] == '打印催') {
                            $data['type'] = 'hurry';
                        } else if ($orderformResult['state'] == '退餐') {
                            $data['type'] = 'return';
                        } else if ($orderformResult['state'] == '作废') {
                            $data['type'] = 'return';
                        } else {
                            $data['type'] = 'order';
                        }

                        $sendnameappModel = D('Sendnameapp');
                        $sendnameappModel->create();
                        $sendnameappModel->add($data);
                    }

                    //已派单,送餐员不相同
                    if ($orderformResult['sendname'] != $sendname) {
                        //先保存新送餐员的信息
                        $data['type'] = 'order';
                        $sendnameappModel = D('Sendnameapp');
                        $sendnameappModel->create();
                        $sendnameappModel->add($data);

                        //再处理原来的送餐员的信息
                        if ($orderformResult['state'] == '改单') {
                            $data['type'] = 'again';
                        } else if ($orderformResult['state'] == '打印改') {
                            $data['type'] == 'agian';
                        } else if ($orderformResult['state'] == '催送') {
                            $data['type'] == 'agian';
                        } else if ($orderformResult['state'] == '打印催') {
                            $data['type'] == 'agian';
                        } else if ($orderformResult['state'] == '退餐') {
                            $data['type'] = 'return';
                        } else if ($orderformResult['state'] == '作废') {
                            $data['type'] = 'return';
                        } else {
                            $data['type'] = 'again';
                        }
                        $data['sendname'] = $orderformResult['sendname'];
                        $sendnameappModel = D('Sendnameapp');
                        $sendnameappModel->create();
                        $sendnameappModel->add($data);
                    }

                }

                // 写入到营收状态表
                $data = array();
                $data['orderformid'] = $orderformid;
                $data['ordersn'] = $ordersn;
                $data['status'] = 0;
                $data['assisstatus'] = 0;
                $data['domain'] = $this->getDomain();
                $orderyingshouexchangeModel = D('Orderyingshouexchange');
                $orderyingshouexchangeModel->create();
                $orderyingshouexchangeModel->add($data);

                //写入到网站状态接口表
                $data = array();
                $data['ordersn'] = $ordersn;
                $data['type'] = 3; //表示派单
                $data['content'] = "订单派给[" . $sendname . "]送餐员";
                $data['date'] = date('Y-m-d H:i:s');
                $data['origin'] = $origin;
                $data['domain'] = $this->getDomain();
                $webstatusModel = D('Webstatus');
                $webstatusModel->create();
                $webstatusModel->add($data);

                //处理电子发票,把电子发票发送给客户
                $invoicemgrModel = D('invoice');
                $where = array();
                $where['ordersn'] = $ordersn;
                $where['type'] = 3;
                $invoicemgrResult = $invoicemgrModel->where($where)->find();
                //把电子票信息存入invoiceweb
                if (!empty($invoicemgrResult)) {
                    //返回收款人和复核人的信息,如果为空,就写默认
                    $companymgrModel = D('companymgr');
                    $where = array();
                    $where['name'] = $company;
                    $where['domain'] = $this->getDomain();
                    $companymgrResult = $companymgrModel->field('cashier,checker')->where($where)->find();
                    if (empty($companymgrResult['cashier'])) {
                        $invoicemgrResult['cashier'] = '丽华';
                    } else {
                        $invoicemgrResult['cashier'] = $companymgrResult['cashier'];
                    }
                    if (empty($companymgrResult['checker'])) {
                        $invoicemgrResult['checker'] = '丽华';
                    } else {
                        $invoicemgrResult['checker'] = $companymgrResult['checker'];
                    }
                    // 接线员的姓名
                    $userInfo = $_SESSION['userInfo'];
                    $opername = $userInfo['truename'];
                    //将发票数据存入到invoiceweb表
                    $data = array();
                    $data['ordersn'] = $ordersn;
                    $data['ordertxt'] = $invoicemgrResult['orderformtxt'];
                    $data['header'] = $invoicemgrResult['header'];
                    $data['body'] = $invoicemgrResult['body'];
                    $data['money'] = $invoicemgrResult['ordermoney'];
                    $data['KPR'] = mb_substr($opername, 0, 3, 'utf-8'); //开票人
                    $data['SKR'] = $invoicemgrResult['cashier']; //收款人
                    $data['FHR'] = $invoicemgrResult['checker']; //复核人
                    $data['gmf_nsrsbh'] = strtoupper($invoicemgrResult['gmf_nsrsbh']); //购买方纳税人识别号
                    $data['gmf_dzdh'] = $invoicemgrResult['gmf_dzdh'];
                    $data['gmf_yhzh'] = $invoicemgrResult['gmf_yhzh'];
                    $data['issms'] = 1;
                    $data['telphone'] = $orderform['telphone'];
                    $data['type'] = 1;
                    $data['date'] = date('Y-m-d H:i:s');
                    $data['createdate'] = date('Y-m-d H:i:s');
                    // 获取分公司
                    $company = $this->userInfo['department'];
                    $data['company'] = $company;
                    $data['state'] = 0;
                    $data['domain'] = $this->getDomain();
                    $where = array();
                    $where['ordersn'] = $ordersn;
                    $invoicewebModel = D('invoiceweb');
                    $invoicewebResult = $invoicewebModel->where($where)->find();
                    if (empty($invoicewebResult)) {
                        //hack一下,让电子票能够识别不同的地区
                        if ($this->getDomain() == 'bj.lihuaerp.com') {
                            $data['eticketno'] = '1' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                        } else {
                            $data['eticketno'] = '2' . rand(10, 100) . date('s') . date('md') . $invoicemgrResult['invoiceid'] . rand(10, 1000);
                        }
                        $invoicewebModel->create();
                        $invoicewebModel->add($data);
                    } else {
                        if ($invoicewebResult['state'] != 2) {
                            $data['eticketno'] = $invoicewebResult['eticketno'];
                            $invoicewebModel->where($where)->save($data);
                        }
                    }
                    $eticketno = $data['eticketno'];

                    //提取信息存入sms库中
                    // * 2017.08.28作废,恢复
                    $data = array();
                    $data['ordersn'] = $ordersn;
                    $data['telphone'] = $orderform['telphone'];
                    $data['content'] = "您的电子票提取码:" . $eticketno . "请您到http://invoice.lihua.com/?n=" . $eticketno . " 上开具发票!谢谢!";
                    $data['createdate'] = date('Y-m-d H:i:s');
                    $data['domain'] = $this->getDomain();
                    $invoicesmsModel = D('invoicesms');
                    //$invoicesmsModel->create();
                    //$invoicesmsModel->add($data);

                    // 同时写入日志中
                    // 记入操作到action中
                    $orderactionModel = D('Orderaction');
                    $data = array();
                    $data['orderformid'] = $orderformid; // 订单号
                    $data['ordersn'] = $ordersn;
                    $data['action'] = "产生电子票提取码:" . $eticketno;
                    $data['logtime'] = date('H:i:s');
                    $data['domain'] = $this->getDomain();
                    $orderactionModel->create();
                    $result = $orderactionModel->add($data);
                }

                //将订单配送情况,写入送餐监测表中
                $checkorderformModel = D('checkorderform');
                $where = array();
                $where['ordersn'] = $ordersn;
                $checkorderformResult = $checkorderformModel->where($where)->find();
                if (empty($checkorderformResult)) {
                    $data = array();
                    $data['ordersn'] = $ordersn;
                    $data['ordertxt'] = $addressordertxt;
                    $data['sendname'] = $sendname;
                    $data['company'] = $company;
                    $data['domain'] = $this->getDomain();
                    $data['noreceivetime'] = date('H:i:s');
                    $checkorderformModel->create();
                    $checkorderformModel->add($data);
                } else {
                    $data = array();
                    $data['sendname'] = $sendname;
                    $data['ordertxt'] = $addressordertxt;
                    $data['company'] = $company;
                    $data['domain'] = $this->getDomain();
                    $data['noreceivetime'] = date('H:i:s');
                    $checkorderformModel->where($where)->save($data);
                }

                //删除一下备餐表中的信息，防止错误
                $productsprepareModel = D('productsprepare');
                $where = array();
                $where['ordersn'] = $ordersn;
                $productsprepareModel->where($where)->delete();

                //判断这个订单是否已经派发
                if (!empty($orderformResult['sendname'])) {
                    $returnData['success'] = 'repeat';
                    $returnData['addressOrdertxt'] .= " ★☆已经派发给:" . $orderformResult['sendname'] . ",将修改";
                    $this->ajaxReturn($returnData, 'JSON');
                } else {
                    $returnData['success'] = 'success';
                    $this->ajaxReturn($returnData, 'JSON');
                }
            } else {
                $returnData['error'] = 'error';
                $returnData['sql'] = $orderformModel->getLastSql();
                $this->ajaxReturn($returnData, 'JSON');

            }
        } else {
            $returnData['error'] = 'error';
            $returnData['sql'] = $orderprinterModel->getLastSql();
            $this->ajaxReturn($returnData, 'JSON');

        }

    }

}
