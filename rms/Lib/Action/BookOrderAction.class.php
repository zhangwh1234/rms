<?php

/**
 * 订单预订模块
 */
class BookOrderAction extends ModuleAction
{


    /* 一般顺序表记录的保存 */
    public function insert()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);


        //获取预订日期
        $bookdateArray = $_REQUEST['bookorderdate'];
        foreach ($bookdateArray as $bookdateValue) {

            // 回调自动完成的函数
            $auto = $this->autoParaInsert();
            //预订日期
            $auto[] = array('bookdate', $bookdateValue);
            $focus->setProperty("_auto", $auto);

            // 保存主表
            $result = $focus->create();
            if (!$result) {
                exit ($focus->getError());
            }
            $result = $focus->add();

            // 取得保存的主键
            $record = $result;

            // 新写的保存从表方案
            $result = $this->save_slave_table($record);

        }

        // 如果保存订单都成功，就跳转到查看页面
        $return ['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        // 生成查看的url
        $detailviewUrl = U("$moduleName/listview", array(
            'record' => $record, 'returnAction' => $returnAction
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = '保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');

    }


    // 更新记录
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得记录号
        $record = $_REQUEST ['record'];
        $moduleId = $focus->getPk();

        //获取预订日期
        $bookdateArray = $_REQUEST['bookorderdate'];
        foreach ($bookdateArray as $bookdateValue) {

            $where = array();
            $where['bookorderid'] = $record;
            $where['bookdate'] = $bookdateValue;
            $bookorder = $focus->where($where)->find();
            if ($bookorder) {
                //执行更新
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


            } else {
                //执行插入数据
                // 回调自动完成的函数
                $auto = $this->autoParaInsert();
                //预订日期
                $auto[] = array('bookdate', $bookdateValue);
                $focus->setProperty("_auto", $auto);

                // 保存主表
                $result = $focus->create();
                if (!$result) {
                    exit ($focus->getError());
                }
                $result = $focus->add();
                // 取得保存的主键
                $record = $result;

                // 新写的保存从表方案
                $result = $this->save_slave_table($record);

            }

        }


        $return ['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("$moduleName/listview", array());
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = '保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }


    /* 删除记录 */
    public function delete()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST ['record'];

        $moduleId = $focus->getPk();

        $where [$moduleId] = $record;

        $data = array();
        $data['bookdate'] = '';
        // 删除记录
        $result = $focus->where($where)->save($data);

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
     *   地址查询框
     */
    public function searchAddressInput()
    {
        $this->display('BookOrder/searchaddressinput');
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

            $where ['domain'] = $this->getDomain();

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数


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
            $_SESSION [$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

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
            $listFields = $focus->listFields;

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $searchText = urlencode($_REQUEST ['searchTextAddress']); // 查询内容

            $datagrid = array(
                'options' => array(
                    'url' => U('BookOrder/searchviewAddress', array('searchTextAddress' => $searchText)),
                    'pageNumber' => $pageNumber
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
            $this->assign('action_name', 'searchviewaddress');
            $this->display('BookOrder/listview'); // 查询的结果显示
        }
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

            $where ['domain'] = $this->getDomain();


            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

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
            $_SESSION [$moduleName . 'searchviewtelphone' . 'page'] = $pageNumber;

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
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $searchText = $_REQUEST ['searchTextTelphone']; // 查询内容

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U('BookOrder/searchviewTelphone', array('searchTextTelphone' => $searchText)),
                    'pageNumber' => $pageNumber
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
                'formatter' => $moduleName . 'ListviewModule.operate'
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
            $this->assign('action_name', 'searchviewtelphone');
            $this->display('BookOrder/listview'); // 查询的结果显示
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
                $map ['domain'] = $this->getDomain();
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

            $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order('bookorderid desc')->select();

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

            // 查询内容
            $searchTextOther = $_REQUEST ['searchTextOther'];

            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U('BookOrder/searchviewOther', array('searchTextOther' => $searchTextOther)),
                    'pageNumber' => $pageNumber
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
                'formatter' => $moduleName . 'ListviewModule.operate'
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法
            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }
            $this->assign('action_name', 'searchviewother');
            $this->display('BookOrder/listview'); // 查询的结果显示
        }
    }


    //返回一些其他的数据,比如下拉列表框等的数据
    public function returnMainFnPara()
    {

        //分公司的数据
        $companymgr_model = D('companymgr');
        $companymgr = $companymgr_model->field('name')->select();
        //在company字段中写入值
        $this->assign('company', $companymgr);

        //因为卫林平的需要，添加所有产品
        //$products_model = D('Products');
        //$orderproducts = $products_model->field("productsid,code,name,price")->order('price asc')->select();
        //echo $products_model->getLastSql();
        //dump($orderproducts);

        //$this->assign('orderproducts', $orderproducts);

        //查询送餐方式和送餐费的设置
        $this->assign('shippingname', '分公司配送');
        $this->assign('shippingmoney', 5);
        //发票内容
        $invoicecontent = array(
            array('name' => '工作餐'),
            array('name' => '盒饭'),
            array('name' => '餐饮')
        );
        $this->assign('invoicecontent', $invoicecontent);

        $invoiceeleparaModel = D('invoiceelepara');
        $where = array();
        $where['domain'] = $this->getDomain();
        $invoiceelepara = $invoiceeleparaModel->where($where)->find();
        if(count($invoiceelepara) > 0){
            $this->invoiceelectronopen = $invoiceelepara['invoiceelectron_open'];
        }else{
            $this->invoiceelectronopen = '关闭';
        }


    }

    /* 弹出选择窗口 */
    public function popupview()
    {

        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块
        $focus = D($moduleName);

        //取得模块的名称
        $popupModuleName = I('module');;;
        $this->assign('moduleName', $popupModuleName);   //模块名称

        //启动弹出选择的模块
        $popupModule = D($popupModuleName);

        //取得对应的导航名称
        $tabName = $focus->getTabName($moduleName);
        $this->assign('tabName', $tabName);         //导航名称

        //取得父窗口的表格行数
        $row = $_REQUEST['row'];


        //生成list字段列表
        $listFields = $focus->popupProductsFields;
        //模块的ID
        $moduleId = $popupModule->getPk();
        //加入模块id到listHeader中
        //array_unshift($listFields,$moduleNameId);
        $listHeader = $listFields;
        $this->assign("listHeader", $listHeader);   //列表头
        $this->assign('returnAction', 'listview');  //定义返回的方法

        //导入分页类
        import('ORG.Util.SimplePage');// 导入分页类
        $total = $popupModule->count();// 查询满足要求的总记录数
        //查session取得page的firstRos和listRows


        if (!isset($_SESSION[$moduleName . 'firstRowlistview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowlistview'];
        }

        //var_dump($_SESSION['test']);
        $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
        if (isset($listMaxRows)) {
            $Page->listRows = $listMaxRows;
        } else {
            $listMaxRows = 15;
        }
        $Page = new SimplePage($total, $listMaxRows);
        $show = $Page->show();


        //查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);
        $listResult = $popupModule->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();


        // 从数据中列出列表的数据
        $listviewEntries = $this->getListviewEntity($listResult, $moduleId);

        $this->assign('moduleId', $moduleId);
        $this->assign('listEntries', $listviewEntries);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('returnAction', 'listview');  //返回的 action
        $this->assign('list_link_field', $focus->popupProductsLinkField);  //定义焦点字段
        $this->assign('row', $row);  //返回选择的行

        $this->display('OrderForm/popupviewProducts');
    }


    //根据产品代码，查询产品名称
    public function getProductsByCode()
    {
        $code = $_REQUEST['code'];
        $products_model = D('Products');
        $products = $products_model->field('name,price')->where("code='$code'")->find();
        //echo $products_model->getLastSql();
        //dump($products);
        $this->ajaxReturn($products, 'JSON');
    }

    //返回从表的内容:产品
    public function get_slave_table($record)
    {
        //取得产品信息
        $orderproductsModel = D('Bookproducts');
        $orderproducts = $orderproductsModel->field('bookorderid,code,name,shortname,price,number,money')->where("bookorderid=$record")->select();
        $this->assign('orderproducts', $orderproducts);

        //取得订单日志
        $bookactionModel = D('Bookaction');
        $bookaction = $bookactionModel->where("bookorderid=$record")->select();
        $this->assign('orderaction', $bookaction);

        //单独取得订单金额,预计预订日期
        $bookorderModel = D('Bookorder');
        $bookorderResult = $bookorderModel->field('totalmoney,bookdate,custtime')->where("bookorderid=$record")->select();
        $totalmoney = $bookorderResult[0]['totalmoney'];
        $booktmp = $bookorderResult[0]['bookdate'];
        $bookdate[] = array(
            'bookdate' => $booktmp
        );
        $this->assign('totalmoney', $totalmoney);
        $this->assign('bookdate', $bookdate);
        $custtimeResult = $bookorderResult[0]['custtime'];
        if ($custtimeResult) {
            $this->assign('custtime_1', substr($custtimeResult, 0, 2));
            $this->assign('custtime_2', substr($custtimeResult, 3, 2));
        }

    }


    //保存产品数据等其他数据
    function  save_slave_table($record)
    {

        $orderTxt = '';
        $totalmoney = 0;
        //保存地址的数量
        $productsLength = $_REQUEST['productsLength'];
        $bookproductsModel = D('Bookproducts');
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
            $data['bookorderid'] = $record;
            if (!empty($name) and !empty($number)) {
                $bookproductsModel->create();
                $bookproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        //接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        //记入操作到action中
        $bookactionModel = D('Bookaction');
        $action = array();
        $action['bookorderid'] = $record;  //订单号
        $action['action'] = $name . ' 新建预订单 ' . $_REQUEST['address'] . ' ' . $orderTxt;
        $action['logtime'] = date('H:i:s');
        $action['domain'] = $this->getDomain();
        $bookactionModel->create();
        $result = $bookactionModel->add($action);


        //加送餐费
        $totalmoney = $totalmoney + $_REQUEST['shippingmoney'];
        //保存数量规格
        $date = array();
        $data['ordertxt'] = $orderTxt;
        $data['totalmoney'] = $totalmoney;
        $data['paidmoney'] = $_REQUEST['paidmoney']; //已付金额
        $data['shouldmoney'] = $_REQUEST['shouldmoney'];
        $data['lastdatetime'] = date('Y-m-d H:i:s');  //最后修改时间
        $bookorderModel = D('Bookorder');
        $result = $bookorderModel->where("bookorderid=$record")->save($data);


    }

    //保存产品数据等其他数据
    function  update_slave_table($record)
    {
        //订单号
        $moduleId = 'bookorderid';

        $bookproductsModel = D('bookproducts');
        //先清掉数据
        $bookproductsModel->where("bookorderid=$record")->delete();

        $orderTxt = '';
        $totalmoney = 0;
        //保存地址的数量
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
            $data['bookorderid'] = $record;
            if (!empty($name) and !empty($number)) {
                $bookproductsModel->create();
                $bookproductsModel->add($data);
                $orderTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        //接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['name'];
        //记入操作到action中
        $orderactionModel = D('Bookaction');
        $action['bookorderid'] = $record;  //订单号
        $action['action'] = $name . ' 改单 ' . $_REQUEST['address'] . ' ' . $orderTxt;
        $action['logtime'] = date('Y-m-d H:i:s');
        $action['domain'] = $this->getDomain();
        $orderactionModel->create();
        $result = $orderactionModel->add($action);

        //加送餐费
        $totalmoney = $totalmoney + $_REQUEST['shippingmoney'];
        //保存数量规格
        $date = array();
        $data['ordertxt'] = $orderTxt;
        $data['totalmoney'] = $totalmoney;
        $data['paidmoney'] = $_REQUEST['paidmoney']; //已付金额
        $data['shouldmoney'] = $_REQUEST['shouldmoney'];
        $data['lastdatetime'] = date('Y-m-d H:i:s');  //最后修改时间
        $bookorderModel = D('Bookorder');
        $result = $bookorderModel->where("$moduleId=$record")->save($data);
    }


    //根据来电，返回来电的发票抬头
    public function getTelphoneHeader()
    {
        //取来电
        $telphone = $_REQUEST['telphoneNumber'];
        //取得来电客户的ID
        $telCustomerModel = D('Telcustomer');
        $where = array();
        $where['telphone'] = $telphone;
        $telCustomerResult = $telCustomerModel->where($where)->find();

        $returnajax = array();
        if ($telCustomerResult) {
            $telCustomerId = $telCustomerResult['telcustomerid'];
        } else {
            $returnajax['error'] = 'error';
            $this->ajaxReturn($returnajax, 'JSON');
        }

        $telFapiaoModel = D('Telinvoice');
        //查询
        $where = array();
        $where['telcustomerid'] = $telCustomerId;
        $headerResult = $telFapiaoModel->field('header')->where($where)->select();
        //var_dump($telFapiaoModel->getLastSql());
        if ($headerResult) {
            $returnajax['success'] = 'success';
            $returnajax['data'] = $headerResult;
            $this->ajaxReturn($returnajax, 'JSON');
        } else {
            $returnajax['error'] = 'error';
            $this->ajaxReturn($returnajax, 'JSON');
        }

    }

    //插入，补充数据的回调函数
    public function autoParaInsert()
    {

        $custtime_1 = $_REQUEST['custtime_1'];
        $custtime_2 = $_REQUEST['custtime_2'];
        if (empty ($custtime_1)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            if (empty($custtime_2)) {
                $custtime = $custtime_1 . ":00:00";
            } else {
                $custtime = $custtime_1 . ":" . $custtime_2 . ":00";
            }
        }

        // 设置午别
        if (!empty ($custtime_1)) {
            $apTime = $custtime_1;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }

        //接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        $auto = array(
            array('recdate', date('Y-m-d')),  //录入日期
            array('rectime', date('H:i:s')), // 对录入时间
            array('telname', $name),   //接线员
            array('ap', $ap),
            array('custtime', $custtime),
            array(
                'invoiceheader',
                $_REQUEST ['invoiceheader']
            ), // 发票抬头
            array(
                'invoicebody',
                $_REQUEST ['invoicebody']
            ), // 发票内容
            array('state', '预订'),
            array('domain', $this->getDomain())
        );

        return $auto;

    }

    //更新，补充数据的回调函数
    public function autoParaUpdate()
    {

        $custtime_1 = $_REQUEST['custtime_1'];
        $custtime_2 = $_REQUEST['custtime_2'];
        if (empty ($custtime_1)) {
            $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
        } else {
            if (empty($custtime_2)) {
                $custtime = $custtime_1 . ":00:00";
            } else {
                $custtime = $custtime_1 . ":" . $custtime_2 . ":00";
            }
        }

        // 设置午别
        if (!empty ($custtime_1)) {
            $apTime = $custtime_1;
        } else {
            $apTime = date('H');
        }
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }

        //接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $name = $userInfo['truename'];
        $auto = array(
            array('ap', $ap),
            array('custtime', $custtime),
            array(
                'invoiceheader',
                $_REQUEST ['invoiceheader']
            ), // 发票抬头
            array(
                'invoicebody',
                $_REQUEST ['invoicebody']
            ), // 发票内容
            array('state', '预订'),
            array('domain', $this->getDomain())
        );

        return $auto;
    }

    //导入订单到订单表中
    public function  importOrder()
    {
        //预订表
        $bookorderModel = D('Bookorder');
        //预订产品
        $bookproductModel = D('Bookproducts');
        //预订活动表
        $bookactivityModel = D('Bookactivity');
        //预订支付表
        $bookpaymentModel = D('Bookpayment');
        //预订日志表
        $bookactionModel = D('Bookaction');

        //订单表
        $orderformModel = D('Orderform');
        //订货表
        $orderproductsModel = D('Orderproducts');
        //活动表
        $orderactivityModel = D('Orderactivity');
        //支付表
        $orderpaymentModel = D('Orderpayment');
        //日志表
        $orderactionModel = D('Orderaction');


        /**
         * 从预订表中读出预订订单,写入订单表中
         */
        $where = array();
        $where['bookdate'] = date('Y-m-d');
        $where['state'] = '预订';
        $where['domain'] = $this->getDomain();
        $bookorderResult = $bookorderModel->where($where)->select();
        foreach ($bookorderResult as $orderValue) {

            $bookorderid = $orderValue['bookorderid'];

            $ordersn = rand(1000, 9999) . date('Ymd') . $orderValue['bookorderid'];
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['clientname'] = $orderValue['clientname'];
            $data['address'] = $orderValue['address'];
            $data['telphone'] = $orderValue['telphone'];
            $data['ordertxt'] = $orderValue['ordertxt'];
            $data['beizhu'] = $orderValue['beizhu'];
            $data['totalmoney'] = $orderValue['totalmoney'];
            $data['paidmoney'] = $orderValue['paidmoney'];
            $data['shouldmoney'] = $orderValue['shouldmoney'];
            $data['custtime'] = $orderValue['custtime'];
            $data['custdate'] = date('Y-m-d');
            $data['ap'] = $orderValue['ap'];
            $data['telname'] = $orderValue['telname'];
            $data['rectime'] = $orderValue['rectime'];
            $data['recdate'] = $orderValue['recdate'];
            $data['state'] = '预订';
            $data['invoiceheader'] = $orderValue['invoiceheader'];
            $data['invoicebody'] = $orderValue['invoicebody'];
            $data['invoicetype'] = $orderValue['invoicetype'];
            $data['gmf_nsrsbh'] = $orderValue['gmf_nsrsbh'];
            $data['gmf_dzdh'] = $orderValue['gmf_dzdh'];
            $data['gmf_yhzh'] = $orderValue['gmf_yhzh'];
            $data['shippingname'] = $orderValue['shippingname'];
            $data['shippingmoney'] = $orderValue['shippingmoney'];
            $data['origin'] = '电话';
            $data['domain'] = $this->getDomain();
            $data['lastdatetime'] = date('Y-m-d H:i:s');
            $orderformModel->create();
            $record = $orderformModel->add($data);

            $where = array();
            $where['bookorderid'] = $bookorderid;
            //取预订产品的内容
            $bookproductResult = $bookproductModel->where($where)->select();
            foreach ($bookproductResult as $productsValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data ['ordersn'] = $ordersn;
                $data['code'] = $productsValue['code'];
                $data['name'] = $productsValue['name'];
                $data['shortname'] = $productsValue['shortname'];
                $data['price'] = $productsValue['price'];
                $data['number'] = $productsValue['number'];
                $data['money'] = $productsValue['money'];
                $orderproductsModel->create();
                $orderproductsModel->add($data);
            }

            //保存活动表
            $bookactivityResult = $bookactivityModel->where($where)->select();
            foreach ($bookactivityResult as $activityValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data['ordersn'] = $ordersn;
                $data['name'] = $activityValue['name'];
                $data['money'] = $activityValue['money'];
                $data['date'] = date('Y-m-d H:i:s');
                $orderactivityModel->create();
                $orderactivityModel->add($data);
            }

            //保存支付表
            $bookpaymentResult = $bookpaymentModel->where($where)->select();
            foreach ($bookpaymentResult as $paymentValue) {
                $data = array();
                $data['orderformid'] = $record;
                $data['ordersn'] = $ordersn;
                $data['name'] = $paymentValue['name'];
                $data['money'] = $paymentValue['money'];
                $data['date'] = date('Y-m-d H:i:s');
                $orderpaymentModel->create();
                $orderpaymentModel->add($data);
            }

            // 接线员的姓名
            $userInfo = $_SESSION ['userInfo'];
            $name = $userInfo ['truename'];

            //保存到订单表日志中
            //记入操作到action中
            $action = array();
            $action ['orderformid'] = $record;
            $action['ordersn'] = $ordersn;  //订单号
            $action['action'] = $name . '将预订单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '导入订单表中';
            $action['logtime'] = date('Y-m-d H:i:s');
            $action ['domain'] = $this->getDomain();
            $orderactionModel->create();
            $result = $orderactionModel->add($action);

            // 写入到状态表中
            $orderstateModel = D('Orderstate');
            $data = array();
            $data ['create'] = 1;
            $data ['createtime'] = date('Y-m-d H:i:s');
            $data ['createcontent'] = $name . '导入预订单';
            $data ['orderformid'] = $record;
            $data ['ordersn'] = $ordersn;
            $data ['domain'] = $this->getDomain();
            $orderstateModel->create();
            $orderstateModel->add($data);


            //记入到预订的日至中
            //记入操作到action中
            $action = array();
            $action['bookorderid'] = $bookorderid;  //预订单号
            $action['action'] = '将预订单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '输入订单表中，订单号：' . $record;
            $action['logtime'] = date('H:i:s');
            $bookactionModel->create();
            $result = $bookactionModel->add($action);

            //保存发票
            if (!empty($orderValue['invoiceheader'])) {
                $data = array();
                $data['header'] = substr($orderValue['invoiceheader'],0,80);
                $data['body'] = $orderValue['invoicebody'];
                if($orderValue['invoicetype'] == '电子票'){
                    $data['type'] = 3;
                }else{
                    $data['type'] = 2;  //普通发票
                }
                $data['gmf_nsrsbh'] = $orderValue['gmf_nsrsbh'];
                $data['gmf_dzdh'] = $orderValue['gmf_dzdh'];
                $data['gmf_yhzh'] = $orderValue['gmf_yhzh'];
                $data['ordersn'] = $ordersn;
                $data['orderformtxt'] = $orderValue['address'] . ' ' . $orderValue['ordertxt'];
                $data['ordermoney'] = $orderValue['totalmoney'];
                $data['ordertime'] = date('H:i:s');
                $data['state'] = '未开';
                $data['company'] =  '';
                $data['domain'] = $this->getDomain();
                $invoiceModel = D('Invoice');
                $invoiceModel->create();
                $invoice = $invoiceModel->add($data);
            }

            //修改预订的订单状态
            $where = array();
            $where['bookorderid'] = $bookorderid;
            $data = array();
            $data['state'] = '已处理';
            $bookorderModel->where($where)->save($data);

        }


        /**
         * 清除无效的订单
         * 计算最后修改日期是一周前
         */
        $where = 'TO_DAYS(NOW()) - TO_DAYS(bookdate) >= 3';
        $bookorderResult = $bookorderModel->where($where)->select();
        foreach ($bookorderResult as $bookorderValue) {
            $where = array();
            $where['bookorderid'] = $bookorderValue['bookorderid'];
            //没有预订日期,可以清除
            //清除预订订单
            $bookorderModel->where($where)->delete();
            //清除预订产品
            $bookproductModel->where($where)->delete();
            //清除预订活动表
            $bookactivityModel->where($where)->delete();
            //清除预订支付表
            $bookpaymentModel->where($where)->delete();
            //清除预订日志表
            $bookactionModel->where($where)->delete();
        }

        /**
         * 清除无效的订单
         */
        $where = "length(bookdate) = 0 or bookdate is null";
        $bookorderResult = $bookorderModel->where($where)->select();
        foreach ($bookorderResult as $bookorderValue) {
            $where = array();
            $where['bookorderid'] = $bookorderValue['bookorderid'];
            //没有预订日期,可以清除
            //清除预订订单
            $bookorderModel->where($where)->delete();
            //清除预订产品
            $bookproductModel->where($where)->delete();
            //清除预订活动表
            $bookactivityModel->where($where)->delete();
            //清除预订支付表
            $bookpaymentModel->where($where)->delete();
            //清除预订日志表
            $bookactionModel->where($where)->delete();
        }

        $info['status'] = 1;
        $info['info'] = ' 导出完毕!';
        $this->ajaxReturn(json_encode($info), 'EVAL');

    }


}

?>
