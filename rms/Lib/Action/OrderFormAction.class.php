<?php

class OrderFormAction extends ModuleAction
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

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['domain'] = $this->getDomain();

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

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
            $selectFields[] = 'longitude';
            $selectFields[] = 'latitude';
            $selectFields[] = 'sendlongitude';
            $selectFields[] = 'sendlatitude';
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select(); //lastdatetime desc,

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

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );

            //计算接线员的接单量
            // 接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['truename'];
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
            // 返回domain,用户电子发票识别
            $this->assign('domain', $this->getDomain());

            $this->assign('returnAction', 'listview');
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    /**
     *   地址查询框
     */
    public function searchAddressInput()
    {
        if (IS_POST) {
            $this->listAction('SearchviewAddress');
            $this->display('OrderForm/searchviewaddress');
        } else {
            $this->display('OrderForm/searchaddressinput');
        }
    }

    /**
     * 地址查询页面
     */
    public function searchviewAddress()
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
            $this->assign('operName', '地址查询操作');

            // 生成list字段列表
            $listFields = $focus->searchlistFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            $searchText = urldecode($_REQUEST['searchTextAddress']); // 查询内容
            if (!empty($searchText)) {
                if ($searchText == '全部') {
                    $where['address'] = array(
                        'like',
                        '%%',
                    );
                    $where['_logic'] = 'and';
                    unset($_SESSION['searchAddress' . $moduleName]);
                } else {
                    $where['address'] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                    $this->assign('searchAddressValue', $searchText);
                    $_SESSION['searchAddress' . $moduleName] = $searchText;
                    $where['_logic'] = 'and';
                }

            } else {
                $searchText = $_SESSION['searchAddress' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $where['address'] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                    $where['_logic'] = 'and';
                }
            }

            $where['domain'] = $this->getDomain();

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

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
            $_SESSION[$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            if ($total > 0) {
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

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '地址查询操作');

            // 生成list字段列表
            $listFields = $focus->searchListFields;

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $searchText = urlencode($_REQUEST['searchTextAddress']); // 查询内容

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewAddress', array('searchTextAddress' => $searchText)),
                    'pageNumber' => $pageNumber,
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'SearchviewAddressModule.sendname',
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
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewAddressModule.operate',
            );
            $this->assign('datagrid', $datagrid);

            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderForm/searchviewaddress'); // 查询的结果显示
        }
    }

    /**
     * 地址查询页面的订单查看
     */
    public function searchviewAddressDetailview()
    {
        $this->display('OrderForm/searchviewaddressdetailview');
    }

    /**
     * 电话查询输入
     */
    public function searchTelphoneInput()
    {
        $this->display('OrderForm/searchtelphoneinput');
    }

    /**
     * 电话查询页面
     */
    public function searchviewTelphone()
    {
        if (IS_POST) {
            // /取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->searchListFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            // 查询内容
            $searchTelphone = $_REQUEST['searchTextTelphone'];
            if (isset($searchTelphone)) {
                if ($searchTelphone == '全部') {
                    $where['telphone'] = array(
                        'like',
                        '%%',
                    );
                    unset($_SESSION['searchText' . $moduleName . 'Telphone']);
                } else {
                    $where['telphone'] = array(
                        'like',
                        '%' . $searchTelphone . '%',
                    );
                    $this->assign('searchTelphoneValue', $searchTelphone);
                    $_SESSION['searchTelphone' . $moduleName . 'Telphone'] = $searchTelphone;
                }

            } else {
                if (isset($_SESSION['searchTelphone' . $moduleName . 'Telphone'])) {
                    $where['telphone'] = array(
                        'like',
                        '%' . $_SESSION['searchTelphone' . $moduleName . 'Telphone'] . '%',
                    );
                    $this->assign('searchTelphoneValue', $_SESSION['searchTelphone' . $moduleName . 'Telphone']);
                }
            }

            $where['domain'] = $this->getDomain();

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

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
            $_SESSION[$moduleName . 'searchviewtelphone' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $this->assign('moduleId', $moduleId);

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
            $this->ajaxReturn($data);
        } else {
            // /取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 如果是从listview进入的，必须删除session['where']
            if (isset($_REQUEST['delsession'])) {
                unset($_SESSION['searchTelphone' . $moduleName . 'Telphone']);
                unset($_SESSION['searchAp' . $moduleName . 'Telphone']);
            }

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '电话查询操作');

            // 生成list字段列表
            $listFields = $focus->searchListFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $searchText = $_REQUEST['searchTextTelphone']; // 查询内容

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewTelphone', array('searchTextTelphone' => $searchText)),
                    'pageNumber' => $pageNumber,
                ),
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'SearchviewTelphoneModule.sendname',
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
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewTelphoneModule.operate',
            );

            $this->assign('datagrid', $datagrid);

            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'searchviewTelphone'); // 定义返回的方法
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderForm/searchviewtelphone'); // 查询的结果显示
        }

    }

    /**
     * 来电电话查询输入
     */
    public function searchComeintelphoneInput()
    {
        $this->display('OrderForm/searchcomeintelphoneinput');
    }

    /**
     * 电话查询结果
     */
    public function searchviewComeinTelphone()
    {
        if (IS_POST) {
            // /取得模块的名称
            $moduleName = 'Telhistory';

            // 启动当前模块
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->telphonecomeListFields;
            // 模块的ID
            $moduleId = 'telhistoryid';

            // 建立查询条件
            $where = array();
            // 查询内容
            $searchTelphone = $_REQUEST['searchTextTelphone'];
            if (isset($searchTelphone)) {
                $where['telphone'] = array(
                    'like',
                    '%' . $searchTelphone . '%',
                );
                $this->assign('searchTelphoneValue', $searchTelphone);
                $_SESSION['searchTelphone' . $moduleName . 'Telphone'] = $searchTelphone;
            } else {
                if (isset($_SESSION['searchTelphone' . $moduleName . 'Telphone'])) {
                    $where['telphone'] = array(
                        'like',
                        '%' . $_SESSION['searchTelphone' . $moduleName . 'Telphone'] . '%',
                    );
                    $this->assign('searchTelphoneValue', $_SESSION['searchTelphone' . $moduleName . 'Telphone']);
                }
            }

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

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

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $this->assign('moduleId', $moduleId);

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
            $this->ajaxReturn($data);
        } else {
            // /取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 如果是从listview进入的，必须删除session['where']
            if (isset($_REQUEST['delsession'])) {
                unset($_SESSION['searchTelphone' . $moduleName . 'Telphone']);
                unset($_SESSION['searchAp' . $moduleName . 'Telphone']);
            }

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '电话查询操作');

            // 生成list字段列表
            $listFields = $focus->telphonecomeListFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 查询内容
            $searchTextTelphone = $_REQUEST['searchTextTelphone'];

            //如果存在页数,获取
            if (isset($_REQUEST['pagenumber'])) {
                $pageNumber = $_REQUEST['pagenumber'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewComeinTelphone', array('searchTextTelphone' => $searchTextTelphone)),
                    'pageNumber' => $pageNumber,
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

            $this->assign('datagrid', $datagrid);
            $this->assign('returnAction', 'searchviewComeinTelphone'); // 定义返回的方法
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderForm/searchviewcomeintelphone'); // 查询的结果显示
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
            $this->assign('operName', '地址查询操作');

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            $searchText = $_REQUEST['searchTextOther']; // 查询内容
            if (isset($searchText)) {
                if ($searchText == '全部') {
                    $where['address'] = array(
                        'like',
                        '%%',
                    );
                    unset($_SESSION['searchText' . $moduleName . 'Other']);
                } else {
                    foreach ($focus->searchFields as $value) {
                        $where[$value] = array(
                            'like',
                            '%' . $searchText . '%',
                        );
                    }
                    $_SESSION['searchText' . $moduleName . 'Other'] = $searchText;
                }

            } else {
                if (isset($_SESSION['searchText' . $moduleName . 'Other'])) {
                    $searchText = $_SESSION['searchText' . $moduleName . 'Other'];
                    foreach ($focus->searchFields as $value) {
                        $where[$value] = array(
                            'like',
                            '%' . $searchText . '%',
                        );
                    }
                    $this->assign('searchTextValue', $_SESSION['searchText' . $moduleName . 'Other']);
                }
            }

            if (count($where) == 0) {
                $map = array();
            } else {
                $where['_logic'] = 'OR';
                //组成复合查询
                $map = array();
                $map['_complex'] = $where;
                $map['domain'] = $this->getDomain();
            }
            $this->assign('searchTextValue', $searchText);

            $total = $focus->where($map)->count(); // 查询满足要求的总记录数

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
            $_SESSION[$moduleName . 'searchviewother' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order('orderformid desc')->select();

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 如果是从listview进入的，必须删除session['where']
            //if (isset ($_REQUEST ['delsession'])) {
            //   unset ($_SESSION ['searchText' . $moduleName . 'Other']);
            //}

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '电话查询操作');

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 查询内容
            $searchTextOther = $_REQUEST['searchTextOther'];

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewOther', array('searchTextOther' => $searchTextOther)),
                    'pageNumber' => $pageNumber,
                ),
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'SearchviewOtherModule.sendname',
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
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewOtherModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->display('OrderForm/searchviewother'); // 查询的结果显示
        }
    }

    // 退单的操作的界面
    public function returnorderformview()
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

        // 模块的ID
        $moduleId = $focus->getPk();

        // 退餐的描述信息
        $moduleReturnBlocks = F($moduleName . 'ReturnBlocks');
        if (!empty($moduleReturnBlocks)) {
            $this->returnBlocks = $moduleReturnBlocks;
        } else {
            // 返回新建区块和字段
            $this->returnBlocks = $focus->returnBlocks();
            // 缓存blocks
            F($moduleName . 'ReturnBlocks', $this->returnBlocks);
        }

        // 回调主程序需要的参数,比如下拉框的数据
        // $this->returnMainFnPara();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('blocks', $blocks);
        $this->assign('record', $record);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table($record);

        $this->display('OrderForm/returnview');

    }

    // 退餐操作
    public function returnOrderOperation()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得当前用户名
        $userName = $this->userInfo['truename'];

        // 取得订单号
        $record = $_REQUEST['record'];
        $returnAction = $_REQUEST['returnAction']; // 返回的路径

        // 取得退餐的原因
        $cancelcontent = $_REQUEST['cancelcontent'];

        $where = array();
        $where['orderformid'] = $record;
        // 取得原来订单的情况
        $orderformResult = $focus->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        //删除订餐内容
        $orderproductsModel = D('orderproducts');
        $orderproductsModel->where($where)->delete();

        $where = array();
        $where['orderformid'] = $record;
        // 对订单状态处理,如果订单已经分配到分公司，只能是退餐，如果是未分配，也写退餐，让联络员来处理
        $data = array();
        $data['state'] = '退餐';
        $data['totalmoney'] = 0;
        $data['paidmoney'] = 0;
        $data['shouldmoney'] = 0;
        $data['shippingmoney'] = 0;
        $data['goodsmoney'] = 0;
        $data['ordertxt'] = '';
        $focus->where($where)->save($data);

        // 写入订单状态表
        $orderStateModel = D('Orderstate');
        $data = array();
        $data['cancel'] = 1;
        $data['canceltime'] = date('Y-m-d H:i:s');
        $data['cancelcontent'] = $userName . ' ' . $cancelcontent;
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $orderStateModel->where($where)->save($data);

        // 写入订单日志
        $orderActionModel = D('Orderaction');
        $data = array();
        $data['orderformid'] = $record;
        $data['action'] = $userName . '将订单退餐,原因：' . $cancelcontent; // 写入日志内容
        $data['logtime'] = date('Y-m-d H:i:s'); // 记入时间
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $orderActionModel->add($data);

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

        //查询电子发票，将电子发票设置为退票状态
        //启动退票
        $where = array();
        $where['ordersn'] = $ordersn;
        //$where['cancel_state'] = 0;
        $where['state'] = 2;
        $where['domain'] = $this->getDomain();
        $data = array();
        $data['cancel_state'] = 1;
        $invoicewebModel = D('invoiceweb');
        $invoicewebModel->where($where)->save($data);

        //写入到日志中
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['log'] = '接线' . $userName . '执行退票操作';
        $data['date'] = date('Y-m-d H:i:s');
        $data['domain'] = $this->getDomain();
        $invoiceweblogModel = D('invoiceweblog');
        $invoiceweblogModel->create();
        $invoiceweblogModel->add($data);

        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("OrderForm/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'rowIndex' => $_REQUEST['rowIndex'], 'pagetype' => $pagetype,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = '保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');

    }

    // 恢复订单为订餐状态
    public function reneworder()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得当前用户名
        $userName = $this->userInfo['truename'];

        // 取得订单号
        $record = $_REQUEST['record'];
        $returnAction = $_REQUEST['returnAction']; // 返回的路径

        $where = array();
        $where['orderformid'] = $record;
        // 取得原来订单的情况
        $orderformResult = $focus->where($where)->find();
        $ordersn = $orderformResult['ordersn'];

        $where = array();
        $where['orderformid'] = $record;
        // 对订单状态处理
        $data = array();
        $data['state'] = '订餐';
        $data['sendname'] = '';
        $focus->where($where)->save($data);

        // 写入订单状态表
        $orderStateModel = D('Orderstate');
        $data = array();
        $data['cancel'] = 0;
        $data['canceltime'] = date('Y-m-d H:i:s');
        $data['cancelcontent'] = $userName . '将订单恢复为订餐状态。';
        $data['handle'] = 0;
        $orderStateModel->where($where)->save($data);

        // 写入订单日志
        $orderActionModel = D('Orderaction');
        $data = array();
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $data['action'] = $userName . '将订单修改为订餐状态。'; // 写入日志内容
        $data['logtime'] = date('Y-m-d H:i:s'); // 记入时间
        $orderActionModel->add($data);

        $this->redirect('OrderForm/detailview', array(
            'record' => $record,
            '$returnAction' => $returnAction,
        ));
    }

    // 返回一些其他的数据,比如下拉列表框等的数据
    public function returnMainFnPara()
    {
        // 分公司的数据
        $companymgr_model = D('companymgr');
        $where = array();
        $where['telphoneauto'] = '营业';
        $where['domain'] = $this->getDomain();
        $companymgr = $companymgr_model->field('name')->where($where)->select();
        // 在company字段中写入值
        $this->assign('companymgr', $companymgr);

        //传递城市
        switch ($this->getDomain()) {
            case 'bj.lihuaerp.com':
                $this->assign('city', '北京');
                break;
            case 'nj.lihuaerp.com':
                $this->assign('city', '南京');
                break;
            case 'cz.lihuaerp.com':
                $this->assign('city', '常州');
                break;
            case 'wx.lihuaerp.com':
                $this->assign('city', '无锡');
                break;
            case 'sz.lihuaerp.com':
                $this->assign('city', '苏州');
                break;
            case 'sh.lihuaerp.com':
                $this->assign('city', '上海');
                break;
            case 'gz.lihuaerp.com':
                $this->assign('city', '广州');
                break;
        }

        // 查询送餐方式和送餐费的设置
        $this->assign('shippingname', '分公司配送');
        $this->assign('shippingmoney', 5);
        // 发票内容
        $invoicecontent = array(
            array(
                'name' => '工作餐',
            ),
            array(
                'name' => '盒饭',
            ),
        );
        $this->assign('invoicecontent', $invoicecontent);

        // 返回今日菜单的内容
        $todaymenuModel = D('Todaymenu');
        $todayDate = date('Y-m-d'); // 今日日期
        $where = array();
        $where['date'] = $todayDate;
        $todaymenuResult = $todaymenuModel->where($where)->find();
        $this->todayDate = $todayDate;
        $this->todaymenuContent = $todaymenuResult['content'];

        $invoiceeleparaModel = D('invoiceelepara');
        $where = array();
        $where['domain'] = $this->getDomain();
        $invoiceelepara = $invoiceeleparaModel->where($where)->find();
        if (count($invoiceelepara) > 0) {
            $this->invoiceelectronopen = $invoiceelepara['invoiceelectron_open'];
        } else {
            $this->invoiceelectronopen = '关闭';
        }

        // 返回百度地图需要的城市名称
        //$HTTP_POST = $this->getDomain();
        //require APP_PATH . 'Conf/datapath.php';
        //$cityMap = $rmsDataPath [$HTTP_POST] ['CITY'];
        //$this->assign('CITY', $cityMap); // 返回地图中的城市

    }

    /* 弹出选择窗口 */
    public function popupProductsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Products';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

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
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
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
            $popupModuleName = 'Products';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupProductsview'),
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
                'formatter' => $moduleName . 'PopupProductsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('OrderForm/popupviewProducts');
        }
    }

    // 返回从表的内容:产品
    public function get_slave_table($record)
    {
        // 取得产品信息
        $orderproducts_model = D('Orderproducts');
        $orderproducts = $orderproducts_model->field('orderformid,code,name,shortname,price,number,money')->where("orderformid=$record")->select();
        $this->assign('orderproducts', $orderproducts);

        $productsTotalNumber = $orderproducts_model->where("orderformid=$record")->sum('number');
        $this->assign('productstotalnumber', $productsTotalNumber);
        //产品金额
        $orderproductsTotalMoney = $orderproducts_model->where("orderformid=$record")->sum('money');
        $this->assign('orderproductsmoney', $orderproductsTotalMoney);

        //取得活动信息
        $orderactivity_model = D('Orderactivity');
        $orderactivity = $orderactivity_model->where("orderformid=$record")->select();
        $this->assign('orderactivity', $orderactivity);
        $orderactivityTotalMoney = $orderactivity_model->where("orderformid=$record")->sum('money');
        $this->assign('orderactivitymoney', $orderactivityTotalMoney);

        //取得订单支付信息
        $orderpayment_model = D('Orderpayment');
        $orderpayment = $orderpayment_model->where("orderformid=$record")->select();
        $this->assign('orderaccountpayment', $orderpayment);

        //订单支付总额
        $orderpaymentMoney = $orderpayment_model->where("orderformid=$record")->sum('money');
        $this->assign('orderpaymentmoney', $orderpaymentMoney);


        // 取得订单的状态
        $orderStateModel = D('Orderstate');
        $orderStateResult = $orderStateModel->where("orderformid=$record")->find(); //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderaction_model = D('Orderaction');
        $orderaction = $orderaction_model->where("orderformid=$record")->select(); //
        $this->assign('orderaction', $orderaction);

        $orderformResult = D('Orderform')->field('custtime,totalmoney,ordersn')->where("orderformid=$record")->find();
        if ($orderformResult) {
            $this->assign('custtime_1', substr($orderformResult['custtime'], 0, 2));
            $this->assign('custtime_2', substr($orderformResult['custtime'], 3, 2));
        }
        //订单总金额
        $this->assign('totalmoney', $orderformResult['totalmoney']);
        //订单号
        $ordersn = $orderformResult['ordersn'];

    }

    // 保存产品数据等其他数据
    public function save_slave_table($record)
    {
        // 订单号
        $moduleId = 'orderformid';
        // 订单编号
        $ordersn = $record . date('YmdHis');

        $orderformModel = D('Orderform');

        $orderproductsModel = D('orderproducts');
        // 先清掉数据
        $orderproductsModel->where("orderformid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        // 保存地址的数量
        $productsLength = $_REQUEST['productsLength'];
        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST['productsCode_' . $i];
            $name = $_REQUEST['productsName_' . $i];
            $shortname = $_REQUEST['productsShortName_' . $i];
            $price = $_REQUEST['productsPrice_' . $i];
            $number = $_REQUEST['productsNumber_' . $i];
            $money = $_REQUEST['productsMoney_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['shortname'] = $shortname;
            $data['price'] = $price;
            $data['number'] = $number;
            $data['money'] = $money;
            $data['orderformid'] = $record;
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($name) and !empty($number)) {
                $orderproductsModel->create();
                $orderproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        //保存到支付表中
        $orderpaymentModel = D('orderpayment');

        //保存在订单支付表中
        $accountpaymentLength = $_REQUEST['accountpaymentLength'];
        for ($i = 1; $i <= $accountpaymentLength; $i++) {
            $code = $_REQUEST['accountpaymentCode_' . $i];
            $name = $_REQUEST['accountpaymentName_' . $i];
            $money = $_REQUEST['accountpaymentMoney_' . $i];
            $note = $_REQUEST['accountpaymentNote_' . $i];
            //保存
            $data = array();
            $data['paymentid'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = $note;
            $data['date'] = date('Y-m-d H:i:s');
            $data['orderformid'] = $record;
            $data['ordersn'] = $ordersn;
            if (!empty($code) && !empty($name)) {
                $orderpaymentModel->create();
                $orderpaymentModel->add($data);
            }
        };

        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];

        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $action['ordersn'] = $ordersn;
        $action['action'] = $name . ' 新建 ' . $_REQUEST['address'] . ' ' . $orderTxt .
            '分:' . $_REQUEST['company'] . ' ' . $_REQUEST['beizhu'];
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $this->getDomain();
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST['shippingmoney'];
        $totalmoney += $shippingmoney;

        // 存入订单表中
        $where = array();
        $where['orderformid'] = $record;
        // 保存数量规格
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['ordertxt'] = $orderTxt;
        $data['totalmoney'] = $totalmoney;
        $data['shippingmoney'] = $shippingmoney; // 加入配送费
        $data['goodsmoney'] = $totalmoney;
        $result = $orderformModel->where("$moduleId=$record")->save($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data['create'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s');
        $data['createcontent'] = $name . '新建订单';
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $data['domain'] = $this->getDomain();
        $orderstateModel->create();
        $orderstateModel->add($data);

        if (!empty($_REQUEST['company'])) {
            // 同时写入日志中
            // 记入操作到action中
            $orderactionModel = D('Orderaction');
            $orderactionData['orderformid'] = $record; // 订单号
            $orderactionData['ordersn'] = $ordersn; // 订单号
            $orderactionData['action'] = "订单分配给" . $_REQUEST['company'] . "配送点";
            $orderactionData['logtime'] = date('H:i:s');
            $orderactionData['domain'] = $this->getDomain();
            $orderactionModel->create();
            $result = $orderactionModel->add($orderactionData);

            // 写入到状态表中
            $orderstateModel = D('Orderstate');
            $data = array();
            $data['distribution'] = 1;
            $data['distributiontime'] = date('Y-m-d H:i:s');
            $data['distributioncontent'] = $_REQUEST['company'];
            $where = array();
            $where['orderformid'] = $record;
            $orderstateModel->where($where)->save($data);

        }

        // 写入到来电历史表中
        $telphone = trim($_REQUEST['telphone']);
        $SESSIONTelphone = trim($_SESSION['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data['telphone'] = $telphone;
                $data['telname'] = $this->userInfo['truename'];
                $data['teltime'] = date('H:i:s');
                $data['teldate'] = date('Y-m-d');
                $data['teltask'] = $name . ' 新建 ' . $_REQUEST['address'] . ' ' . $orderTxt;
                $data['domain'] = $this->getDomain();
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }

        /**
        //通知客户的消息,2018-09-17停用
        if(preg_match("/^1[34578]\d{9}$/", $telphone)) {
        $data = array();
        $data['ordersn'] = $ordersn;
        $data['telphone'] = $telphone;
        $data['app_tk'] = '';
        $data['contenttype'] = 'confirm';
        $data['origin'] = '电话';
        $data['domain'] = $this->getDomain();
        $notifyclientModel = D('NotifyClient');
        $notifyclientModel->create();
        $notifyclientModel->add($data);
        }
         * */

        //保存发票
        if (!empty($_REQUEST['invoiceheader'])) {
            //普通纸张发票
            if (!empty($_REQUEST['gmf_nsrsbh'])) {
                $data = array();
                $data['header'] = mb_substr($_REQUEST['invoiceheader'], 0, 100, 'utf-8');
                $data['body'] = $_REQUEST['invoicebody'];
                $data['gmf_nsrsbh'] = $_REQUEST['gmf_nsrsbh'];
                $data['gmf_dzdh'] = $_REQUEST['gmf_dzdh'];
                $data['gmf_yhzh'] = $_REQUEST['gmf_yhzh'];
                $data['type'] = 2; //默认是普通票
                $data['ordersn'] = $ordersn;
                $data['orderformtxt'] = $_REQUEST['address'] . ' ' . $orderTxt;
                $data['ordermoney'] = $totalmoney;
                $data['ordertime'] = date('H:i:s');
                $data['state'] = '未开';
                if (empty($_REQUEST['company'])) {
                    $data['company'] = '';
                } else {
                    $data['company'] = $_REQUEST['company'];
                }
                if ($_REQUEST['invoicetype'] == '电子票') {
                    $data['type'] = 3; //电子票
                } else {
                    $data['type'] = 2; //普通发票
                }
                $data['domain'] = $this->getDomain();
                $invoiceModel = D('Invoice');
                $invoiceModel->create();
                $invoice = $invoiceModel->add($data);
            }
        }

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
    }

    // 保存产品数据等其他数据
    public function update_slave_table($record)
    {
        // 订单号
        $entity_id = 'orderformid';

        $orderform_model = D('Orderform');

        $orderformResult = $orderform_model->field('ordersn,company')->where("$entity_id=$record")->find();
        $ordersn = $orderformResult['ordersn'];

        //判断订单是否已经分配
        $company = '';
        if (!empty($orderformResult['company'])) {
            $company = $orderformResult['company'];
        }
        if (!empty($_REQUEST['company'])) {
            $company = $_REQUEST['company']; //这个优先
        }

        $orderproducts_model = D('orderproducts');
        // 先清掉数据
        $orderproducts_model->where("orderformid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        // 保存地址的数量
        $productsLength = $_REQUEST['productsLength'];
        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST['productsCode_' . $i];
            $name = $_REQUEST['productsName_' . $i];
            $shortname = $_REQUEST['productsShortName_' . $i];
            $price = $_REQUEST['productsPrice_' . $i];
            $number = $_REQUEST['productsNumber_' . $i];
            $money = $_REQUEST['productsMoney_' . $i];
            $data['code'] = $code;
            $data['name'] = $name;
            $data['shortname'] = $shortname;
            $data['price'] = $price;
            $data['number'] = $number;
            $data['money'] = $money;
            $data['orderformid'] = $record;
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($name) and !empty($number)) {
                $orderproducts_model->create();
                $orderproducts_model->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        //保存到支付表中
        $orderpaymentModel = D('orderpayment');
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderpaymentModel->where($where)->delete();
        //保存在订单支付表中
        $accountpaymentLength = $_REQUEST['accountpaymentLength'];
        for ($i = 1; $i <= $accountpaymentLength; $i++) {
            $code = $_REQUEST['accountpaymentCode_' . $i];
            $name = $_REQUEST['accountpaymentName_' . $i];
            $money = $_REQUEST['accountpaymentMoney_' . $i];
            $note = $_REQUEST['accountpaymentNote_' . $i];
            //保存
            $data = array();
            $data['paymentid'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = $note;
            $data['date'] = date('Y-m-d H:i:s');
            $data['orderformid'] = $record;
            $data['ordersn'] = $ordersn;
            if (!empty($code) && !empty($name)) {
                $orderpaymentModel->create();
                $orderpaymentModel->add($data);
            }
        }
        ;

        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $action['ordersn'] = $ordersn;
        $action['action'] = $name . ' 改单 ' . $_REQUEST['address'] . ' ' . $orderTxt .
            '分:' . $company . ' ' . $_REQUEST['beizhu'];
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $this->getDomain();
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST['shippingmoney'];
        $totalmoney += $shippingmoney;

        //活动费用
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderactivity_model = D('Orderactivity');
        $orderactivityResult = $orderaction_model->where($where)->select();
        $activitymoney = 0;
        foreach ($orderactivityResult as $activityValue) {
            $activitymoney += $activityValue['money'];
        }

        // 保存数量规格
        $data = array();
        $data['ordertxt'] = $orderTxt;
        $data['totalmoney'] = $totalmoney;
        $data['shippingmoney'] = $shippingmoney; // 加入配送费
        $result = $orderform_model->where("$entity_id=$record")->save($data);

        // 写入到来电历史表中
        $telphone = trim($_REQUEST['telphone']);
        $SESSIONTelphone = trim($_SESSION['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data['telphone'] = $SESSIONTelphone;
                $data['telname'] = $this->userInfo['truename'];
                $data['teltime'] = date('H:i:s');
                $data['teldate'] = date('Y-m-d');
                $data['teltask'] = $name . ' 新建 ' . $_REQUEST['address'] . ' ' . $orderTxt;
                $data['domain'] = $this->getDomain();
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }

        //保存发票
        if (!empty($_REQUEST['invoiceheader'])) {
            $data = array();
            $data['header'] = mb_substr($_REQUEST['invoiceheader'], 0, 100, 'utf-8');
            $data['body'] = $_REQUEST['invoicebody'];
            $data['gmf_nsrsbh'] = $_REQUEST['gmf_nsrsbh'];
            $data['gmf_dzdh'] = $_REQUEST['gmf_dzdh'];
            $data['gmf_yhzh'] = $_REQUEST['gmf_yhzh'];

            $data['ordersn'] = $ordersn;
            $data['orderformtxt'] = $_REQUEST['address'] . ' ' . $orderTxt;
            $data['ordermoney'] = $totalmoney;
            $data['ordertime'] = date('H:i:s');
            //先预习建立一下分公司，防止分配后修改的发票
            if (!empty($company)) {
                $data['company'] = $company;
            }
            if (empty($_REQUEST['company'])) {
                //$data['company'] =  ''; 应该是不需要改变
            } else {
                $data['company'] = $_REQUEST['company'];
            }
            if ($_REQUEST['invoicetype'] == '电子票') {
                $data['type'] = 3; //电子票
            } else {
                $data['type'] = 2; //普通发票
            }
            $data['domain'] = $this->getDomain();
            $invoiceModel = D('Invoice');
            //查询一下是否已经有发票存在
            $where = array();
            $where['ordersn'] = $ordersn;
            $invoiceResult = $invoiceModel->where($where)->find();
            if (!empty($invoiceResult)) {
                $invoiceModel->where($where)->save($data);
            } else {
                $invoiceModel->create();
                $invoice = $invoiceModel->add($data);
            }
        }

        /**
        if (!empty($_REQUEST['invoiceheader'])) {
        $invoiceModel = D('Invoice');
        $where = array();
        $where['ordersn'] = $ordersn;
        $invoice = $invoiceModel->where($where)->find();
        $data = array();
        $data['header'] = substr($_REQUEST['invoiceheader'],0,100);
        $data['body'] = $_REQUEST['invoicebody'];
        $data['gmf_nsrsbh'] = $_REQUEST['gmf_nsrsbh'];
        $data['gmf_dzdh'] = $_REQUEST['gmf_dzdh'];
        $data['gmf_yhzh'] = $_REQUEST['gmf_yhzh'];
        $data['type'] = 2;
        $data['ordersn'] = $ordersn;
        $data['orderformtxt'] = $_REQUEST['address'] . ' ' . $orderTxt;
        $data['ordermoney'] = $totalmoney - $activitymoney;
        $data['ordertime'] = date('H:i:s');
        $data['state'] = '未开';
        $data['domain'] = $this->getDomain();
        if(!empty($_REQUEST['company'])){
        $data['company'] = $_REQUEST['company'];
        }else{

        }
        if($_REQUEST['invoicetype'] == '电子票'){
        $data['type'] = 3;
        }else{
        $data['type'] = 2;  //普通发票
        }
        if(empty($invoice['header'])){
        $data['company'] = $company;
        $invoiceModel->create();
        $invoice = $invoiceModel->add($data);
        }else{
        $invoiceModel->where($where)->save($data);
        }
        }
         */

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

    }

    // 复制产品数据等其他数据
    public function duplicate_slave_table($record)
    {
        // 订单号
        $moduleId = 'orderformid';
        // 订单编号
        $ordersn = $record . date('Ymd');

        $orderproductsModel = D('orderproducts');
        // 先清掉数据
        $orderproductsModel->where("orderformid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        // 保存地址的数量
        $productsLength = $_REQUEST['productsLength'];

        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST['productsCode_' . $i];
            $name = $_REQUEST['productsName_' . $i];
            $shortname = $_REQUEST['productsShortName_' . $i];
            $price = $_REQUEST['productsPrice_' . $i];
            $number = $_REQUEST['productsNumber_' . $i];
            $money = $_REQUEST['productsMoney_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['shortname'] = $shortname;
            $data['price'] = $price;
            $data['number'] = $number;
            $data['money'] = $money;
            $data['orderformid'] = $record;
            $data['ordersn'] = $ordersn;
            $data['domain'] = $this->getDomain();
            if (!empty($name) and !empty($number)) {
                $orderproductsModel->create();
                $orderproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $data['ordersn'] = $ordersn;
        $action['action'] = $name . ' 复制订单 ' . $_REQUEST['address'] . ' ' . $orderTxt;
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $this->getDomain();
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST['shippingmoney'];
        $totalmoney += $shippingmoney;

        // 保存数量规格
        $data = array();
        $data['ordertxt'] = $orderTxt;
        $data['totalmoney'] = $totalmoney;
        $data['shippingmoney'] = $shippingmoney; // 加入配送费
        $orderformModel = D('Orderform');
        $result = $orderformModel->where("$moduleId=$record")->save($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data['create'] = 1;
        $data['createtime'] = date('Y-m-d H:i:s');
        $data['createcontent'] = $name . '复制新建订单';
        $data['orderformid'] = $record;
        $data['domain'] = $this->getDomain();
        $orderstateModel->create();
        $orderstateModel->add($data);

        $telphone = trim($_REQUEST['telphone']);
        $SESSIONTelphone = trim($_SESSION['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data['telphone'] = $SESSIONTelphone;
                $data['telname'] = $this->userInfo['truename'];
                $data['teltime'] = date('H:i:s');
                $data['teldate'] = date('Y-m-d');
                $data['teltask'] = $name . ' 新建 ' . $_REQUEST['address'] . ' ' . $orderTxt;
                $data['domain'] = $this->getDomain();
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }

        //保存发票
        if (!empty($_REQUEST['invoiceheader'])) {
            $data = array();
            $data['header'] = mb_substr($_REQUEST['invoiceheader'], 0, 100, 'utf-8');
            $data['body'] = $_REQUEST['invoicebody'];
            $data['gmf_nsrsbh'] = $_REQUEST['gmf_nsrsbh'];
            $data['gmf_dzdh'] = $_REQUEST['gmf_dzdh'];
            $data['gmf_yhzh'] = $_REQUEST['gmf_yhzh'];
            $data['type'] = 2; //默认是普通票
            $data['ordersn'] = $ordersn;
            $data['orderformtxt'] = $_REQUEST['address'] . ' ' . $orderTxt;
            $data['ordermoney'] = $totalmoney;
            $data['ordertime'] = date('H:i:s');
            $data['state'] = '未开';
            if (empty($_REQUEST['company'])) {
                $data['company'] = '';
            } else {
                $data['company'] = $_REQUEST['company'];
            }
            if ($_REQUEST['invoicetype'] == '电子票') {
                $data['type'] = 3; //电子票
            } else {
                $data['type'] = 2; //普通发票
            }
            $data['domain'] = $this->getDomain();
            $invoiceModel = D('Invoice');
            $invoiceModel->create();
            $invoice = $invoiceModel->add($data);
        }

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

    }

    /* 催送订单 */
    public function hurry()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得记录号
        $record = $_REQUEST['record'];
        // 订单编号
        $ordersn = $record . date('Ymd');
        // 返回的页面
        $returnAction = $_REQUEST['returnAction'];
        // 模块的ID
        $moduleId = $focus->getPk();

        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];

        // 这里必须查一下订单原来的状态
        $state_result = $focus->field('state')->where("$moduleId = $record")->find();
        $stateBefore = $state_result['state'];
        if ($stateBefore == '订餐') {
            $data['state'] = '订餐';
        } elseif ($stateBefore == '已打印') {
            $data['state'] = '打印催';
        } else {
            $data['state'] = '催送';
        }
        $data['hurrynumber'] = array(
            'exp',
            'hurrynumber+1',
        );
        $data['hurrytime'] = date('H:i:s');
        $data['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
        $result = $focus->where("$moduleId = $record")->save($data);

        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action['orderformid'] = $record; // 订单号
        $action['ordersn'] = $ordersn;
        $action['action'] = $name . ' 催送订单 ';
        $action['logtime'] = date('H:i:s');
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 记入到催餐表中orderhurry中
        $orderhurryModel = D('Orderhurry');
        $data = array();
        $data['orderformid'] = $record;
        $data['ordersn'] = $ordersn;
        $data['hurrytime'] = date('H:i:s');
        $orderhurryModel->create();
        $result = $orderhurryModel->add($data);

        // 如果保存订单都成功，就跳转到查看页面
        $info['status'] = 1;
        $info['info'] = '保存成功';
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    // 定义启动是的焦点字段
    public function getFocusFields()
    {
        $fields = "clientname";
        return $fields;
    }

    // 根据来电，返回来电的发票抬头
    public function getTelphoneInvoiceHeader()
    {
        // 取来电
        $telphone = $_REQUEST['telphoneNumber'];
        // 取得来电客户的ID
        $telcustomerModel = D('Telcustomer');
        $where = array();
        $where['telphone'] = $telphone;
        $where['domain'] = $this->getDomain();
        $telcustomerResult = $telcustomerModel->where($where)->find();

        $returnData = array();
        if ($telcustomerResult) {
            $telcustomerId = $telcustomerResult['telcustomerid'];
        } else {
            $returnData['telinvoice'] = array();
            $this->ajaxReturn($returnData, 'JSON');
        }

        $telinvoiceModel = D('Telinvoice');
        // 查询
        $where = array();
        $where['telcustomerid'] = $telcustomerId;
        $telinvoiceResult = $telinvoiceModel->field('header,body,gmf_nsrsbh,gmf_dzdh,gmf_yhzh')->where($where)->select();

        $telinvoice = $telinvoiceResult;

        $returnData['telinvoice'] = $telinvoice;
        $this->ajaxReturn($returnData, 'JSON');
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $custtime_1 = $_REQUEST['custtime_1'];
        $custtime_2 = $_REQUEST['custtime_2'];
        if (empty($custtime_1)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            if (empty($custtime_2)) {
                $custtime = $custtime_1 . ":00:00";
            } else {
                $custtime = $custtime_1 . ":" . $custtime_2 . ":00";
            }
            //判断配送时间,如何小于当前时间,就返回错误
            if (strtotime($custtime) < time()) {
                $this->info = '要餐时间小于当前时间,错误！';
            }
        }
        // 设置午别
        if (!empty($custtime_1)) {
            $apTime = $custtime_1;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        $auto = array(
            array(
                'custdate',
                date('Y-m-d'),
            ), // 送餐日期
            array(
                'recdate',
                date('Y-m-d'),
            ), // 录入日期
            array(
                'custtime',
                $custtime,
            ), // 要餐时间
            array(
                'rectime',
                date('H:i:s'),
            ), // 对录入时间
            array(
                'telname',
                $name,
            ), // 接线员
            array(
                'ap',
                $ap,
            ),
            array(
                'invoiceheader',
                $_REQUEST['invoiceheader'],
            ), // 发票抬头
            array(
                'invoicebody',
                $_REQUEST['invoicebody'],
            ), // 发票内容
            array(
                'state',
                '订餐',
            ),
            array(
                'lastdatetime',
                date('Y-m-d H:i:s'),
            ),
            array(
                'origin',
                '电话',
            ),
            array(
                'domain',
                $this->getDomain(),
            ),
        ); // 最后的修改时间

        return $auto;
    }

    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);

        $custtime_1 = $_REQUEST['custtime_1'];
        $custtime_2 = $_REQUEST['custtime_2'];
        if (empty($custtime_1)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            if (empty($custtime_2)) {
                $custtime = $custtime_1 . ":00:00";
            } else {
                $custtime = $custtime_1 . ":" . $custtime_2 . ":00";
            }
            //判断配送时间,如何小于当前时间,就返回错误
            if (strtotime($custtime) < time()) {
                $this->info = '要餐时间小于当前时间,错误！';
                //$this->ajaxReturn(json_encode($info),'EVAL');
            }
        }

        // 设置午别
        if (!empty($custtime_1)) {
            $apTime = $custtime_1;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        // 取得订单未修改前的状态
        $stateBefore = $_REQUEST['state'];
        // var_dump(strstr($stateBefore,"已"));
        if ($stateBefore == '订餐') {
            $state = "订餐";
        } elseif ($stateBefore == '已打印') {
            $state = '打印改';
        } else {
            $state = "改单";
        }

        //修改订单的时候，如果已经分配，就不能把分公司修改为空，需要加条件判断
        // 取得记录号
        $record = $_REQUEST['record'];
        $moduleId = $focus->getPk();
        $where = array();
        $where[$moduleId] = $record;
        $orderformResule = $focus->field('company')->where($where)->find();
        $company = $orderformResule['company'];

        $auto = array(
            array(
                'custtime',
                $custtime,
            ), // 要餐时间
            array(
                'ap',
                $ap,
            ),
            array(
                'handlestate',
                0,
            ), // 处理状态为0
            array(
                'distributionstate',
                0,
            ), // 分配状态为0
            array(
                'state',
                $state,
            ),
            array(
                'lastdatetime',
                date('Y-m-d H:i:s'),
            ),
            array(
                'domain',
                $this->getDomain(),
            ),
        ); // 最后的修改时间

        return $auto;
    }

    //显示菜单
    public function showToaymenuview()
    {

        $todaymenuDate = date('Y-m-d');
        // 今日菜单
        $todaymenuModel = D('Todaymenu');
        // 查询菜单
        $where = array();
        $where['date'] = $todaymenuDate;
        $where['domain'] = $this->getDomain();
        $todaymenuResult = $todaymenuModel->where($where)->find();

        $this->assign('todayDate', $todaymenuDate);

        $this->assign('todaymenuContent', $todaymenuResult['content']);

        $this->display('todaymenuview');
    }

    // 根据日期返回今日菜单的内容
    public function getTodaymenuContent()
    {
        // 日期
        $todaymenuDate = $_REQUEST['date'];
        // 今日菜单
        $todaymenuModel = D('Todaymenu');
        // 查询菜单
        $where = array();
        $where['date'] = $todaymenuDate;
        $where['domain'] = $this->getDomain();
        $todaymenuResult = $todaymenuModel->where($where)->find();

        if (empty($todaymenuResult)) {
            $data = array();
            $data['error'] = 'error';
            $this->ajaxReturn($data, 'JSON');
        }
        $data = array();
        $data['success'] = 'success';
        $data['content'] = $todaymenuResult['content'];
        $this->ajaxReturn($data, 'JSON');
    }

    //返回订单坐标地图
    public function mapshowview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        $record = $_REQUEST['record'];

        $where = array();
        $where['orderformid'] = $record;
        //查询坐标地址
        $orderformResult = $focus->field('longitude,latitude,address')->where($where)->find();

        $this->assign('longitude', $orderformResult['longitude']);
        $this->assign('latitude', $orderformResult['latitude']);
        $this->assign('address', $orderformResult['address']);

        //为了显示城市
        require APP_PATH . 'Conf/datapath.php';
        $HTTP_POST = $this->getDomain();
        $this->assign('city', $rmsDataPath[$HTTP_POST]['CITY']);
        $this->display('mapview');
    }

    //返回送餐员订单坐标地图
    public function sendmapshowview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        $record = $_REQUEST['record'];

        $where = array();
        $where['orderformid'] = $record;
        //查询坐标地址
        $orderformResult = $focus->field('longitude,latitude,address,sendname,sendlongitude,sendlatitude')->where($where)->find();

        $this->assign('longitude', $orderformResult['longitude']);
        $this->assign('latitude', $orderformResult['latitude']);
        $this->assign('address', $orderformResult['address']);
        $this->assign('sendname', $orderformResult['sendname']);
        $this->assign('sendlongitude', $orderformResult['sendlongitude']);
        $this->assign('sendlatitude', $orderformResult['sendlatitude']);
        if (empty($orderformResult['longitude'])) {
            //为了显示城市
            require APP_PATH . 'Conf/datapath.php';
            $HTTP_POST = $this->getDomain();
            $this->assign('city', $rmsDataPath[$HTTP_POST]['CITY']);
            $this->display('sendmapview_two');
            return;
        }
        //为了显示城市
        require APP_PATH . 'Conf/datapath.php';
        $HTTP_POST = $this->getDomain();
        $this->assign('city', $rmsDataPath[$HTTP_POST]['CITY']);
        $this->display('sendmapview');
    }

    /**
     * 显示送餐区域图,并能根据图,选择定位坐标
     */
    public function showArea()
    {
        //传递城市
        switch ($this->getDomain()) {
            case 'bj.lihuaerp.com':
                $this->assign('city', '北京');
                break;
            case 'nj.lihuaerp.com':
                $this->assign('city', '南京');
                break;
            case 'cz.lihuaerp.com':
                $this->assign('city', '常州');
                break;
            case 'wx.lihuaerp.com':
                $this->assign('city', '无锡');
                break;
            case 'sz.lihuaerp.com':
                $this->assign('city', '苏州');
                break;
            case 'sh.lihuaerp.com':
                $this->assign('city', '上海');
                break;
            case 'gz.lihuaerp.com':
                $this->assign('city', '广州');
                break;
        }

        $this->display('area');
    }

    /**
     * 返回分公司的送餐范围
     */
    public function getCompanyArea()
    {
        $companymgrModel = D('companymgr');

        $where = array();
        $where['telphoneauto'] = '营业';
        $where['domain'] = $this->getDomain();
        $companymgrResult = $companymgrModel->field('companymgrid as id,name,longitude,latitude,region')->where($where)->select();
        $return['data'] = $companymgrResult;
        $this->ajaxReturn($return);

    }

    /**
     *   根据代码，查询客户支付名称
     *   统一改为客户支付管理 accountpayment
     **/
    public function getAccountPaymentByCode()
    {
        $code = $_REQUEST['code'];
        $accountsModel = D('paymentmgr');
        $where = array();
        $where['code'] = $code;
        $where['company'] = '总部';
        $where['domain'] = $this->getDomain();
        $accounts = $accountsModel->field('name')->where($where)->find();
        $this->ajaxReturn($accounts, 'JSON');
    }

    //弹出客户支付选择窗口
    public function popupAccountsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Accounts';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

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
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("accountsid desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
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
                'formatter' => $moduleName . 'PopupAccountsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouWorklunch/popupAccountsview');
        }
    }

}
