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
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }
            }

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
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

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("lastdatetime desc,$moduleId desc")->select();

            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray  = $listResult;
            } else {
                $orderHandleArray  = array();
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
                unset($_SESSION ['searchText' . $moduleName]);
                unset($_SESSION [$moduleName . 'page']);
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

            //是否有查询字段
            $searchText = $_REQUEST ['searchText']; // 查询内容
            if (!empty($searchText)) {
                $searchArray = array('searchText' => $searchText);
                $this->assign('searchIntroduce', '查询内容:' . $searchText);
                $_SESSION ['searchText' . $moduleName] = $searchText;
            } else {
                $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchArray = array('searchText' => $searchText);
                    $this->assign('searchIntroduce', '查询内容:' . $searchText);
                } else {
                    $_SESSION ['searchText' . $moduleName] = '';
                }
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $searchArray),
                    'pageNumber' => 1,
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
            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate'
            );

            //计算接线员的接单量
            // 接线员的姓名
            $userInfo = $_SESSION ['userInfo'];
            $name = $userInfo ['truename'];
            $where =array();
            $where['telname'] = $name;
            $telOrderNumber = $focus->where($where)->count();
            $telOrderNumber = '['.$name.']' . $telOrderNumber . '张订单';

            $this->assign('orderformOtherMsg',$telOrderNumber);
            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

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
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            $searchText = urldecode($_REQUEST ['searchTextAddress']); // 查询内容
            if (!empty ($searchText)) {
                $where ['address'] = array(
                    'like',
                    '%' . $searchText . '%'
                );
                $where ['_logic'] = 'and';
            } else {
                $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchText = $_SESSION ['searchText' . $moduleName];
                    $where ['address'] = array(
                        'like',
                        '%' . $searchText . '%'
                    );
                    $where ['_logic'] = 'and';
                }
            }

            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            // 导入分页类
            import('ORG.Util.NewPage'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowSearchview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowSearchview'];
            }

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            $Page = new NewPage ($total, $listMaxRows);

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("lastdatetime desc,$moduleId desc")->select();

            $orderHandleArray ['total'] = $total;
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

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '地址查询操作');

            // 生成list字段列表
            $listFields = $focus->searchListFields;
            // 模块的ID
            $moduleId = $focus->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;

            // 建立查询条件
            $where = array();
            $searchText = urlencode($_REQUEST ['searchTextAddress']); // 查询内容

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewAddress', array('searchTextAddress' => $searchText)),
                    'pageNumber' => 1
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
            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewAddressModule.operate'
            );
            $this->assign('datagrid', $datagrid);


            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法

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
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            // 查询内容
            $searchTelphone = $_REQUEST ['searchTextTelphone'];
            if (isset ($searchTelphone)) {
                $where ['telphone'] = array(
                    'like',
                    '%' . $searchTelphone . '%'
                );
                $this->assign('searchTelphoneValue', $searchTelphone);
                $_SESSION ['searchTelphone' . $moduleName . 'Telphone'] = $searchTelphone;
            } else {
                if (isset ($_SESSION ['searchTelphone' . $moduleName . 'Telphone'])) {
                    $where ['telphone'] = array(
                        'like',
                        '%' . $_SESSION ['searchTelphone' . $moduleName . 'Telphone'] . '%'
                    );
                    $this->assign('searchTelphoneValue', $_SESSION ['searchTelphone' . $moduleName . 'Telphone']);
                }
            }

            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            // 导入分页类
            import('ORG.Util.NewPage'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowSearchview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowSearchview'];
            }

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            $Page = new NewPage ($total, $listMaxRows);
            $show = $Page->show();

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("lastdatetime desc,$moduleId desc")->select();

            $this->assign('moduleId', $moduleId);


            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
            $this->ajaxReturn($data);
        } else {
            // /取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 如果是从listview进入的，必须删除session['where']
            if (isset ($_REQUEST ['delsession'])) {
                unset ($_SESSION ['searchTelphone' . $moduleName . 'Telphone']);
                unset ($_SESSION ['searchAp' . $moduleName . 'Telphone']);
            }

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

            // 建立查询条件
            $where = array();
            $searchText = $_REQUEST ['searchTextTelphone']; // 查询内容

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewTelphone', array('searchTextTelphone' => $searchText)),
                    'pageNumber' => 1
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
            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewTelphoneModule.operate'
            );
            $this->assign('datagrid', $datagrid);

            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'searchviewTelphone'); // 定义返回的方法

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
            $searchTelphone = $_REQUEST ['searchTextTelphone'];
            if (isset ($searchTelphone)) {
                $where ['telphone'] = array(
                    'like',
                    '%' . $searchTelphone . '%'
                );
                $this->assign('searchTelphoneValue', $searchTelphone);
                $_SESSION ['searchTelphone' . $moduleName . 'Telphone'] = $searchTelphone;
            } else {
                if (isset ($_SESSION ['searchTelphone' . $moduleName . 'Telphone'])) {
                    $where ['telphone'] = array(
                        'like',
                        '%' . $_SESSION ['searchTelphone' . $moduleName . 'Telphone'] . '%'
                    );
                    $this->assign('searchTelphoneValue', $_SESSION ['searchTelphone' . $moduleName . 'Telphone']);
                }
            }


            // 导入分页类
            import('ORG.Util.NewPage'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowSearchview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowSearchview'];
            }

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }


            $Page = new NewPage ($total, $listMaxRows);
            $show = $Page->show();

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $this->assign('moduleId', $moduleId);


            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);
            $this->ajaxReturn($data);
        } else {
            // /取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 如果是从listview进入的，必须删除session['where']
            if (isset ($_REQUEST ['delsession'])) {
                unset ($_SESSION ['searchTelphone' . $moduleName . 'Telphone']);
                unset ($_SESSION ['searchAp' . $moduleName . 'Telphone']);
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

            // 建立查询条件
            $where = array();
            // 查询内容
            $searchTextTelphone = $_REQUEST ['searchTextTelphone'];

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewComeinTelphone', array('searchTextTelphone' => $searchTextTelphone)),
                    'pageNumber' => 1
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

            $this->assign('datagrid', $datagrid);
            $this->assign('returnAction', 'searchviewComeinTelphone'); // 定义返回的方法
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
            $searchText = $_REQUEST ['searchTextOther']; // 查询内容
            if (isset ($searchText)) {
                foreach ($focus->searchFields as $value) {
                    $where [$value] = array(
                        'like',
                        '%' . $searchText . '%'
                    );
                }
                $_SESSION ['searchText' . $moduleName . 'Other'] = $searchText;
            } else {
                if (isset ($_SESSION ['searchText' . $moduleName . 'Other'])) {
                    $searchText = $_SESSION ['searchText' . $moduleName . 'Other'];
                    foreach ($focus->searchFields as $value) {
                        $where [$value] = array(
                            'like',
                            '%' . $searchText . '%'
                        );
                    }
                    $this->assign('searchTextValue', $_SESSION ['searchText' . $moduleName . 'Other']);
                }
            }

            if (count($where) == 0) {
                $map = array();
            } else {
                $where ['_logic'] = 'OR';
                //组成复合查询
                $map = array();
                $map['_complex'] = $where;
                $map ['domain'] = $_SERVER ['HTTP_HOST'];
            }
            $this->assign('searchTextValue', $searchText);

            // 导入分页类
            import('ORG.Util.NewPage'); // 导入分页类
            $total = $focus->where($map)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowSearchview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowSearchview'];
            }

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            $Page = new NewPage ($total, $listMaxRows);
            $show = $Page->show();

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order('orderformid asc')->select();

            $orderHandleArray ['total'] = $total;
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

            // 建立查询条件
            $where = array();
            // 查询内容
            $searchTextOther = $_REQUEST ['searchTextOther'];

            $datagrid = array(
                'options' => array(
                    'url' => U('OrderForm/searchviewOther', array('searchTextOther' => $searchTextOther)),
                    'pageNumber' => 1
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

            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 40,
                'align' => 'center',
                'formatter' => $moduleName . 'SearchviewOtherModule.operate'
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法
            $this->display('OrderForm/searchviewother'); // 查询的结果显示
        }
    }


    // 退单的操作
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
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign('returnAction', $returnAction);


        // 模块的ID
        $moduleId = $focus->getPk();

        // 退餐的描述信息
        $moduleReturnBlocks = F($moduleName . 'ReturnBlocks');
        if (!empty ($moduleReturnBlocks)) {
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
        $record = $_REQUEST ['record'];
        $where [$moduleId] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('blocks', $blocks);
        $this->assign('record', $record);

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
        $userName = $this->userInfo ['truename'];

        // 取得订单号
        $record = $_REQUEST ['record'];
        $returnAction = $_REQUEST ['returnAction']; // 返回的路径

        // 取得退餐的原因
        $cancelcontent = $_REQUEST ['cancelcontent'];

        $where = array();
        $where ['orderformid'] = $record;

        //删除订餐内容
        $orderproductsModel =D('orderproducts');
        $orderproductsModel->where($where)->delete();

        // 对订单状态处理,如果订单已经分配到分公司，只能是退餐，如果是未分配，也写退餐，让联络员来处理
        $data = array();
        $data ['state'] = '退餐';
        $data ['totalmoney'] = 0;
        $data ['paidmoney'] = 0;
        $data ['shouldmoney'] = 0;
        $data ['shippingmoney'] = 0;
        $data ['ordertxt'] = '';
        $focus->where($where)->save($data);

        // 写入订单状态表
        $orderStateModel = D('Orderstate');
        $data = array();
        $data ['cancel'] = 1;
        $data ['canceltime'] = date('Y-m-d H:i:s');
        $data ['cancelcontent'] = $userName . ' ' . $cancelcontent;
        $orderStateModel->where($where)->save($data);

        // 写入订单日志
        $orderActionModel = D('Orderaction');
        $data = array();
        $data ['orderformid'] = $record;
        $data ['action'] = $userName . '将订单退餐,原因：' . $cancelcontent; // 写入日志内容
        $data ['logtime'] = date('Y-m-d H:i:s'); // 记入时间
        $orderActionModel->add($data);

        // 生成查看的url
        $detailviewUrl = U ( "OrderForm/detailview", array (
            'record' => $record,'returnAction'=>$returnAction
        ) );
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info),'EVAL');

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
        $userName = $this->userInfo ['truename'];

        // 取得订单号
        $record = $_REQUEST ['record'];
        $returnAction = $_REQUEST ['returnAction']; // 返回的路径

        $where = array();
        $where ['orderformid'] = $record;
        // 取得原来订单的情况
        $orderformResult = $focus->where($where)->find();

        // 对订单状态处理
        $data = array();
        $data ['state'] = '订餐';
        $data ['sendname'] = '';
        $focus->where($where)->save($data);

        // 写入订单状态表
        $orderStateModel = D('Orderstate');
        $data = array();
        $data ['cancel'] = 0;
        $data ['canceltime'] = date('Y-m-d H:i:s');
        $data ['cancelcontent'] = $userName . '将订单恢复为订餐状态。';
        $data ['handle'] = 0;
        $orderStateModel->where($where)->save($data);

        // 写入订单日志
        $orderActionModel = D('Orderaction');
        $data = array();
        $data ['orderformid'] = $record;
        $data ['action'] = $userName . '将订单修改为订餐状态。'; // 写入日志内容
        $data ['logtime'] = date('Y-m-d H:i:s'); // 记入时间
        $orderActionModel->add($data);

        $this->redirect('OrderForm/detailview', array(
            'record' => $record,
            '$returnAction' => $returnAction
        ));
    }

    // 返回一些其他的数据,比如下拉列表框等的数据
    public function returnMainFnPara()
    {
        // 分公司的数据
        $companymgr_model = D('companymgr');
        $companymgr = $companymgr_model->field('name')->select();
        // 在company字段中写入值
        $this->assign('company', $companymgr);


        // 查询送餐方式和送餐费的设置
        $this->assign('shippingname', '分公司配送');
        $this->assign('shippingmoney', 5);
        // 发票内容
        $invoicecontent = array(
            array(
                'name' => '工作餐'
            ),
            array(
                'name' => '盒饭'
            )
        );
        $this->assign('invoicecontent', $invoicecontent);

        // 返回今日菜单的内容
        $todaymenuModel = D('Todaymenu');
        $todayDate = date('Y-m-d'); // 今日日期
        $where = array();
        $where ['date'] = $todayDate;
        $todaymenuResult = $todaymenuModel->where($where)->find();
        $this->todayDate = $todayDate;
        $this->todaymenuContent = $todaymenuResult ['content'];

        // 返回百度地图需要的城市名称
        $HTTP_POST = $_SERVER ['HTTP_HOST'];
        require APP_PATH . 'Conf/datapath.php';
        $cityMap = $rmsDataPath [$HTTP_POST] ['CITY'];
        $this->assign('CITY', $cityMap); // 返回地图中的城市

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
            $row = $_REQUEST ['row'];

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
            $where['domain'] = $_SERVER['HTTP_HOST'];

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
            $popupModuleName = 'Products';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupProductsview'),
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
                'formatter' => $moduleName . 'PopupProductsviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule',$_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row',$row);  //返回点击的订购商品行

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

        // 单独取得订单金额
        $orderform_model = D('Orderform');
        $orderform = $orderform_model->field('totalmoney')->where("orderformid=$record")->select();
        $totalmoney = $orderform [0] ['totalmoney'];
        $this->assign('totalmoney', $totalmoney);

        //取得活动信息
        $orderactivity_model = D('Orderactivity');
        $orderactivity = $orderactivity_model->where("orderformid=$record")->select();
        $this->assign('orderactivity',$orderactivity);

        //取得订单支付信息
        $orderpayment_model = D('Orderpayment');
        $orderpayment = $orderpayment_model->where("orderformid=$record")->select();
        $this->assign('orderpayment',$orderpayment);

        // 取得订单的状态
        $orderStateModel = D('Orderstate');
        $orderStateResult = $orderStateModel->where("orderformid=$record")->find();  //
        $this->assign('orderstate', $orderStateResult);

        // 取得订单日志
        $orderaction_model = D('Orderaction');
        $orderaction = $orderaction_model->where("orderformid=$record")->select(); //
        $this->assign('orderaction', $orderaction);

    }

    // 保存产品数据等其他数据
    function save_slave_table($record)
    {
        // 订单号
        $moduleId = 'orderformid';
        // 订单编号
        $ordersn = $record . date('Ymd');

        $orderformModel = D('Orderform');

        $orderproductsModel = D('orderproducts');
        // 先清掉数据
        $orderproductsModel->where("orderformid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        // 保存地址的数量
        $productsLength = $_REQUEST ['productsLength'];
        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST ['productsCode_' . $i];
            $name = $_REQUEST ['productsName_' . $i];
            $shortname = $_REQUEST ['productsShortName_' . $i];
            $price = $_REQUEST ['productsPrice_' . $i];
            $number = $_REQUEST ['productsNumber_' . $i];
            $money = $_REQUEST ['productsMoney_' . $i];
            $data = array();
            $data ['code'] = $code;
            $data ['name'] = $name;
            $data ['shortname'] = $shortname;
            $data ['price'] = $price;
            $data ['number'] = $number;
            $data ['money'] = $money;
            $data ['orderformid'] = $record;
            $data ['ordersn'] = $ordersn;
            $data ['domain'] = $_SERVER['HTTP_HOST'];
            if (!empty ($name) and !empty ($number)) {
                $orderproductsModel->create();
                $orderproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];

        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $action ['ordersn'] = $ordersn;
        $action ['action'] = $name . ' 新建 ' . $_REQUEST ['address'] . ' ' . $orderTxt .
            '分:' . $_REQUEST['company'] . ' ' . $_REQUEST['beizhu'];
        $action ['logtime'] = date('H:i:s');
        $action ['domain'] = $_SERVER['HTTP_HOST'];
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST ['shippingmoney'];
        $totalmoney += $shippingmoney;

        // 存入订单表中
        $where = array();
        $where ['orderformid'] = $record;
        // 保存数量规格
        $data = array();
        $data ['ordersn'] = $ordersn;
        $data ['ordertxt'] = $orderTxt;
        $data ['totalmoney'] = $totalmoney;
        $data ['shippingmoney'] = $shippingmoney; // 加入配送费
        $result = $orderformModel->where("$moduleId=$record")->save($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data ['create'] = 1;
        $data ['createtime'] = date('Y-m-d H:i:s');
        $data ['createcontent'] =  $name . '新建订单';
        $data ['orderformid'] = $record;
        $data ['ordersn'] = $ordersn;
        $data ['domain'] = $_SERVER['HTTP_HOST'];
        $orderstateModel->create();
        $orderstateModel->add($data);

        // 写入到来电历史表中
        $telphone = trim($_REQUEST ['telphone']);
        $SESSIONTelphone = trim($_SESSION ['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty ($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data ['telphone'] = $telphone;
                $data ['telname'] = $this->userInfo ['truename'];
                $data ['teltime'] = date('H:i:s');
                $data ['teldate'] = date('Y-m-d');
                $data ['teltask'] = $name . ' 新建 ' . $_REQUEST ['address'] . ' ' . $orderTxt;
                $data ['domain'] = $_SERVER['HTTP_HOST'];
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }


        //通知客户的消息
        if(preg_match("/^1[34578]\d{9}$/", $telphone)) {
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['telphone'] = $telphone;
            $data['app_tk'] = '';
            $data['contenttype'] = 'confirm';
            $data['origin'] = '电话';
            $data['domain'] = $_SERVER['HTTP_HOST'];
            $notifyclientModel = D('NotifyClient');
            $notifyclientModel->create();
            $notifyclientModel->add($data);
        }
    }

    // 保存产品数据等其他数据
    function update_slave_table($record)
    {
        // 订单号
        $entity_id = 'orderformid';

        $orderform_model = D('Orderform');

        $orderformResult = $orderform_model->field('ordersn')->where("$entity_id=$record")->find();
        $ordersn = $orderformResult['ordersn'];

        $orderproducts_model = D('orderproducts');
        // 先清掉数据
        $orderproducts_model->where("orderformid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        // 保存地址的数量
        $productsLength = $_REQUEST ['productsLength'];
        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST ['productsCode_' . $i];
            $name = $_REQUEST ['productsName_' . $i];
            $shortname = $_REQUEST ['productsShortName_' . $i];
            $price = $_REQUEST ['productsPrice_' . $i];
            $number = $_REQUEST ['productsNumber_' . $i];
            $money = $_REQUEST ['productsMoney_' . $i];
            $data ['code'] = $code;
            $data ['name'] = $name;
            $data ['shortname'] = $shortname;
            $data ['price'] = $price;
            $data ['number'] = $number;
            $data ['money'] = $money;
            $data ['orderformid'] = $record;
            $data ['ordersn'] = $ordersn;
            $data ['domain'] = $_SERVER['HTTP_HOST'];
            if (!empty ($name) and !empty ($number)) {
                $orderproducts_model->create();
                $orderproducts_model->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];
        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $action ['ordersn'] = $ordersn;
        $action ['action'] = $name . ' 改单 ' . $_REQUEST ['address'] . ' ' . $orderTxt .
            '分:' . $_REQUEST['company'] . ' ' . $_REQUEST['beizhu'];
        $action ['logtime'] = date('H:i:s');
        $action ['domain'] = $_SERVER['HTTP_HOST'];
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST ['shippingmoney'];
        $totalmoney += $shippingmoney;

        // 保存数量规格
        $data = array();
        $data ['ordertxt'] = $orderTxt;
        $data ['totalmoney'] = $totalmoney;
        $data ['shippingmoney'] = $shippingmoney; // 加入配送费
        $result = $orderform_model->where("$entity_id=$record")->save($data);

        // 写入到来电历史表中
        $telphone = trim($_REQUEST ['telphone']);
        $SESSIONTelphone = trim($_SESSION ['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty ($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data ['telphone'] = $SESSIONTelphone;
                $data ['telname'] = $this->userInfo ['truename'];
                $data ['teltime'] = date('H:i:s');
                $data ['teldate'] = date('Y-m-d');
                $data ['teltask'] = $name . ' 新建 ' . $_REQUEST ['address'] . ' ' . $orderTxt;
                $data ['domain'] = $_SERVER['HTTP_HOST'];
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }

    }

    // 复制产品数据等其他数据
    function duplicate_slave_table($record)
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
        $productsLength = $_REQUEST ['productsLength'];

        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST ['productsCode_' . $i];
            $name = $_REQUEST ['productsName_' . $i];
            $shortname = $_REQUEST ['productsShortName_' . $i];
            $price = $_REQUEST ['productsPrice_' . $i];
            $number = $_REQUEST ['productsNumber_' . $i];
            $money = $_REQUEST ['productsMoney_' . $i];
            $data = array();
            $data ['code'] = $code;
            $data ['name'] = $name;
            $data ['shortname'] = $shortname;
            $data ['price'] = $price;
            $data ['number'] = $number;
            $data ['money'] = $money;
            $data ['orderformid'] = $record;
            $data ['ordersn'] = $ordersn;
            $data ['domain'] = $_SERVER['HTTP_HOST'];
            if (!empty ($name) and !empty ($number)) {
                $orderproductsModel->create();
                $orderproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];
        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $data ['ordersn'] = $ordersn;
        $action ['action'] = $name . ' 复制订单 ' . $_REQUEST ['address'] . ' ' . $orderTxt;
        $action ['logtime'] = date('H:i:s');
        $action ['domain'] = $_SERVER['HTTP_HOST'];
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 保存配送费
        $shippingmoney = $_REQUEST ['shippingmoney'];
        $totalmoney += $shippingmoney;

        // 保存数量规格
        $data = array();
        $data ['ordertxt'] = $orderTxt;
        $data ['totalmoney'] = $totalmoney;
        $data ['shippingmoney'] = $shippingmoney; // 加入配送费
        $orderformModel = D('Orderform');
        $result = $orderformModel->where("$moduleId=$record")->save($data);

        // 写入到状态表中
        $orderstateModel = D('Orderstate');
        $data = array();
        $data ['create'] = 1;
        $data ['createtime'] = date('Y-m-d H:i:s');
        $data ['createcontent'] = $sendname . ' ' . $telphone;
        $data ['orderformid'] = $record;
        $data ['domain'] = $_SERVER['HTTP_HOST'];
        $orderstateModel->create();
        $orderstateModel->add($data);

        $telphone = trim($_REQUEST ['telphone']);
        $SESSIONTelphone = trim($_SESSION ['telphoneIncome']);
        $telhistoryModel = D('Telhistory');
        if (!empty ($SESSIONTelphone)) {
            if ($telphone == $SESSIONTelphone) {
                $data = array();
                $data ['telphone'] = $SESSIONTelphone;
                $data ['telname'] = $this->userInfo ['truename'];
                $data ['teltime'] = date('H:i:s');
                $data ['teldate'] = date('Y-m-d');
                $data ['teltask'] = $name . ' 新建 ' . $_REQUEST ['address'] . ' ' . $orderTxt;
                $data ['domain'] = $_SERVER['HTTP_HOST'];
                $telhistoryModel->create();
                $telhistoryModel->add($data);
            }
        }

    }

    /* 催送订单 */
    function hurry()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得记录号
        $record = $_REQUEST ['record'];
        // 订单编号
        $ordersn = $record . date('Ymd');
        // 返回的页面
        $returnAction = $_REQUEST ['returnAction'];
        // 模块的ID
        $moduleId = $focus->getPk();

        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];

        // 这里必须查一下订单原来的状态
        $state_result = $focus->field('state')->where("$moduleId = $record")->find();
        $stateBefore = $state_result ['state'];
        if ($stateBefore == '订餐') {
            $data ['state'] = '订餐';
        } else {
            $data ['state'] = '催送';
        }
        $data ['hurrynumber'] = array(
            'exp',
            'hurrynumber+1'
        );
        $data ['hurrytime'] = date('H:i:s');
        $data ['lastdatetime'] = date('Y-m-d H:i:s'); // 记录最后的修改时间
        $result = $focus->where("$moduleId = $record")->save($data);

        // 记入操作到action中
        $orderaction_model = D('Orderaction');
        $action ['orderformid'] = $record; // 订单号
        $action ['ordersn'] = $ordersn;
        $action ['action'] = $name . ' 催送订单 ';
        $action ['logtime'] = date('H:i:s');
        $orderaction_model->create();
        $result = $orderaction_model->add($action);

        // 记入到催餐表中orderhurry中
        $orderhurryModel = D('Orderhurry');
        $data = array();
        $data ['orderformid'] = $record;
        $data ['ordersn'] = $ordersn;
        $data ['hurrytime'] = date('H:i:s');
        $orderhurryModel->create();
        $result = $orderhurryModel->add($data);

        // 如果保存订单都成功，就跳转到查看页面
        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $this->ajaxReturn(json_encode($info),'EVAL');
    }

    // 定义启动是的焦点字段
    public function getFocusFields()
    {
        $fields = "clientname";
        return $fields;
    }

    // 根据来电，返回来电的发票抬头
    public function getTelphoneHeader()
    {
        // 取来电
        $telphone = $_REQUEST ['telphoneNumber'];
        // 取得来电客户的ID
        $telCustomerModel = D('Telcustomer');
        $where = array();
        $where ['telphone'] = $telphone;
        $telCustomerResult = $telCustomerModel->where($where)->find();

        $returnajax = array();
        if ($telCustomerResult) {
            $telCustomerId = $telCustomerResult ['telcustomerid'];
        } else {
            $returnajax ['error'] = 'error';
            $this->ajaxReturn($returnajax, 'JSON');
        }

        $telFapiaoModel = D('Telinvoice');
        // 查询
        $where = array();
        $where ['telcustomerid'] = $telCustomerId;
        $headerResult = $telFapiaoModel->field('header')->where($where)->select();
        // var_dump($telFapiaoModel->getLastSql());
        if ($headerResult) {
            $returnajax ['success'] = 'success';
            $returnajax ['data'] = $headerResult;
            $this->ajaxReturn($returnajax, 'JSON');
        } else {
            $returnajax ['error'] = 'error';
            $this->ajaxReturn($returnajax, 'JSON');
        }
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $custtime = $_REQUEST ['custtime']; // 要餐时间
        if (empty ($custtime)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            $custtime = $_REQUEST ['custtime'] ;
            //判断配送时间,如何小于当前时间,就返回错误
            if(strtotime($custtime) < time()){
                $info['status'] = 0;
                $info['info'] =  '要餐时间小于当前时间,错误！' ;
                $this->ajaxReturn(json_encode($info),'EVAL');
            }
        }
        // 设置午别
        if (!empty ($custtime)) {
            $apTime = $custtime;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];
        $auto = array(
            array(
                'custdate',
                date('Y-m-d')
            ), // 送餐日期
            array(
                'recdate',
                date('Y-m-d')
            ), // 录入日期
            array(
                'custtime',
                $custtime
            ), // 要餐时间
            array(
                'rectime',
                date('H:i:s')
            ), // 对录入时间
            array(
                'telname',
                $name
            ), // 接线员
            array(
                'ap',
                $ap
            ),
            array(
                'invoiceheader',
                $_REQUEST ['invoiceheader']
            ), // 发票抬头
            array(
                'invoicebody',
                $_REQUEST ['invoicebody']
            ), // 发票内容
            array(
                'state',
                '订餐'
            ),
            array(
                'lastdatetime',
                date('Y-m-d H:i:s')
            ),
            array(
                'domain',
                $_SERVER['HTTP_HOST']
            )
        ); // 最后的修改时间

        return $auto;
    }

    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);

        $custtime = $_REQUEST ['custtime']; // 要餐时间
        if (empty ($custtime)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            $custtime = $_REQUEST ['custtime'] ;
        }

        // 设置午别
        if (!empty ($custtime)) {
            $apTime = $custtime;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];
        // 取得订单未修改前的状态
        $stateBefore = $_REQUEST ['state'];
        // var_dump(strstr($stateBefore,"已"));
        if ($stateBefore == '订餐') {
            $state = "订餐";
        } else {
            $state = "改单";
        }

        //修改订单的时候，如果已经分配，就不能把分公司修改为空，需要加条件判断
        // 取得记录号
        $record = $_REQUEST ['record'];
        $moduleId = $focus->getPk();
        $where = array();
        $where[$moduleId] = $record;
        $orderformResule = $focus->field('company')->where($where)->find();
        $company = $orderformResule['company'];

        $_REQUEST['company'] = '2222';


        $auto = array(
            array(
                'custtime',
                $custtime
            ), // 要餐时间
            array(
                'ap',
                $ap
            ),
            array(
                'handlestate',
                0
            ), // 处理状态为0
            array(
                'distributionstate',
                0
            ), // 分配状态为0
            array(
                'state',
                $state
            ),
            array(
                'lastdatetime',
                date('Y-m-d H:i:s')
            ),
            array(
                'domain',
                $_SERVER['HTTP_HOST']
            )
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
        $where ['date'] = $todaymenuDate;
        $todaymenuResult = $todaymenuModel->where($where)->find();

        $this->assign('todayDate', $todaymenuDate);

        $this->assign('todaymenuContent',$todaymenuResult['content']);

        $this->display('todaymenuview');
    }

    // 根据日期返回今日菜单的内容
    public function getTodaymenuContent()
    {
        // 日期
        $todaymenuDate = $_REQUEST ['date'];
        // 今日菜单
        $todaymenuModel = D('Todaymenu');
        // 查询菜单
        $where = array();
        $where ['date'] = $todaymenuDate;
        $todaymenuResult = $todaymenuModel->where($where)->find();

        if (empty ($todaymenuResult)) {
            $data = array();
            $data ['error'] = 'error';
            $this->ajaxReturn($data, 'JSON');
        }
        $data = array();
        $data ['success'] = 'success';
        $data ['content'] = $todaymenuResult ['content'];
        $this->ajaxReturn($data, 'JSON');
    }


}

?>
