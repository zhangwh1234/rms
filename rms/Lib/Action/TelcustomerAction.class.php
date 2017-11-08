<?php

/**
 *  来电客户管理的基础模块
 */
class  TelcustomerAction extends ModuleAction
{


    //listview   列表显示
    public function listview()
    {
        if (IS_POST) {

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName);   //模块名称

            //启动当前模块
            $focus = D($moduleName);
            //地址和发票模块
            $teladdressModel = D('Teladdress');
            $telinvoiceModel = D('Telinvoice');


            //模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = '';
            $searchText = $_REQUEST ['searchText']; // 查询内容
            if (isset ($searchText)) {
                foreach ($focus->searchFields as $value) {
                    $where .= $value . " like '%" . $searchText . "%'  and  ";
                }

            } else {
                if ($_SESSION ['searchText' . $moduleName]) {
                    $searchText = $_SESSION ['searchText' . $moduleName];
                    foreach ($focus->searchFields as $value) {
                        $where .= $value  . " like '%" . $searchText . "%'  and  ";
                    }

                }
            }

            if ($where) {
                $where .= $focus->trueTableName .".domain = '" . $this->getDomain() . "'";
            } else {
                $where = $focus->trueTableName .".domain = '" . $this->getDomain() . "'";
            }

            //导入分页类
            import ( 'ORG.Util.Page' ); // 导入分页类
            $sql = "select count(*) as total from ". $focus->trueTableName ;
            $sql = $sql . ' where ' .$where;


            $totalResult = $focus->query($sql);// 查询满足要求的总记录数
            $total = $totalResult[0]['total'];

            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            if (empty ( $pageNumber )) {
                $pageNumber = 1;
            }

            /*
            // 查session取得page的firstRos和listRows
            if (isset ( $_SESSION [$moduleName . 'firstRowlistview'] )) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
            }
            */

            if($_SESSION['listMaxRows']){
                $listMaxRows = $_SESSION['listMaxRows'];
            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ( $total, $listMaxRows );

            //特殊的查询方式，用于复合查询
            if ($this->getListQuery()) {
                $query = $this->getListQuery();

                $query = $query . ' where ' . $where;
                $query .= " order by " . $focus->trueTableName .".telcustomerid desc ";
                $query .= 'limit ' . $Page->firstRow . ', ' .$Page->listRows ;

                $listResult = $focus->query($query);
            }
            //var_dump($focus->getLastSql());
            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0 || empty($listResult)) {
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

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION ['searchText' . $moduleName]);
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
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            // 执行list的一些其它数据的操作
            $this->listviewOther();
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }

    }


    /**
     * 实际编写保存地址的程序
     *
     * @param mixed $record 主表订单号
     */
    public function save_slave_table($record)
    {
        //保存地址的数量
        $addressLength = $_REQUEST['addressLength'];
        $teladdress_model = D('Teladdress');

        for ($i = 1; $i <= $addressLength; $i++) {
            $address = $_REQUEST['telAddress_' . $i];
            $company = $_REQUEST['telCompany_' . $i];
            $data['address'] = $address;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['telcustomerid'] = $record;
            $teladdress_model->create();
            $teladdress_model->add($data);
        }
    }

    /**
     * 更新地址的程序
     *
     * @param mixed $record 主表订单号
     */
    public function update_slave_table($record)
    {

        $teladdress_model = D('Teladdress');
        //删除旧的数据
        $teladdress_model->where("telcustomerid=$record")->delete();

        //保存地址的数量
        $addressLength = $_REQUEST['addressLength'];

        for ($i = 1; $i <= $addressLength; $i++) {
            $address = $_REQUEST['telAddress_' . $i];
            $company = $_REQUEST['telCompany_' . $i];
            $data['address'] = $address;
            $data['company'] = $company;
            $data['domain'] = $this->getDomain();
            $data['telcustomerid'] = $record;
            $teladdress_model->create();
            $teladdress_model->add($data);
        }
    }

    //返回从表的内容:地址
    public function get_slave_table($record)
    {
        $teladdress_model = D('Teladdress');
        $teladdress = $teladdress_model->field('telcustomerid,address,company')->where("telcustomerid=$record")->order('teladdressid')->select();

        $this->assign('teladdress', $teladdress);
    }

    //返回自定义的list的select语句
    public function getListQuery()
    {
        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块
        $focus = D($moduleName);
        //地址和发票模块
        $teladdressModel = D('Teladdress');

        $sql = "select ". $focus->trueTableName .".telcustomerid,". $focus->trueTableName .".name," .
            $focus->trueTableName .".telphone," . $teladdressModel->trueTableName .".address," . $focus->trueTableName .
            ".rectime from ". $focus->trueTableName  ." left join " . $teladdressModel->trueTableName .
            " on ". $focus->trueTableName .".telcustomerid = ". $teladdressModel->trueTableName .".telcustomerid  ";
        return $sql;
    }

    //定义启动是的焦点字段
    public function getFocusFields()
    {
        $fields = "telphone";
        return $fields;
    }

    //来电显示
    public function getAddressByPhone()
    {
        //取得电话号码
        $telphone = $_REQUEST['telphone'];
        //将来电号码写入SESSION中
        $_SESSION['telphoneIncome'] = $telphone;
        //实例化来电历史表
        $telhistoryModel = D('Telhistory');
        //查询来电历史表
        $where = array();
        $where['telphone'] = $telphone;
        $where['teldate'] = date('Y-m-d');
        $where['domain'] = $this->getDomain();
        $telhistoryResult = $telhistoryModel->where($where)->select();

        //是否要保存在来电历史表中
        $addHistory = $_REQUEST['noaddhistory'];
        if($addHistory == 'no'){
            //不需要保存
        }else{
            //将来电记录到来电历史表中
            $data = array();
            $data['telphone'] = $telphone;
            $data['telname'] = $this->userInfo['truename'];
            $data['teltime'] = date('H:i:s');
            $data['teldate'] = date('Y-m-d');
            $data['teltask'] = '客户来电';
            $data['domain'] = $this->getDomain();
            $telhistoryModel->create();
            $telhistoryModel->add($data);
        }


        //查询，返回记录的地址
        $telcustomerModel = D('Telcustomer');
        $where = array();
        $where['telphone'] = $telphone;
        $where['domain'] = $this->getDomain();
        $telcustomer = $telcustomerModel->field("telcustomerid,name,telphone")->where($where)->find();

        $telcustomerid = $telcustomer['telcustomerid'];

        //查询地址
        $teladdressModel = D('Teladdress');
        $teladdress = $teladdressModel->field("teladdressid,address,longitude,latitude,company")->where("telcustomerid=$telcustomerid")->limit(5)->order('teladdressid desc')->select();
        if (empty($teladdress)) $teladdress = array();

        $returnData['teladdress'] = $teladdress;
        $returnData['telhistory'] = $telhistoryResult;

        $this->ajaxReturn($returnData, 'JSON');
    }



    //根据客户来电，显示客户以前的订单历史记录
    public function getByPhoneOrderhistoryView()
    {
        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块的模型
        $focus = D($moduleName);

        //生成list字段列表
        $listFields = array('orderformid', 'address', 'ordertxt', 'telphone', 'totalmoney', 'custtime', 'sendname', 'company', 'state', 'telname', 'rectime', 'custdate');
        //模块的ID
        $moduleId = 'orderformid';
        $listHeader = $listFields;

        $selectFields = $listFields;

        //获得查询的字段
        $telphone = $_REQUEST['telphoneNumber'];

        //查询当前订单表中的订单
        $orderformModel = D('Orderform');
        $where = array();
        $where['telphone'] = $telphone;
        $orderformResult = $orderformModel->field($selectFields)->where($where)->select();

        if (empty($orderformResult)) {
            $orderformResult = array();
        }

        //读取连接信息,根据用户访问的url来判断
        require APP_PATH . 'Conf/datapath.php';
        $HTTP_POST = $this->getDomain();
        $HTTP_POST = $HTTP_POST . 'History';
        $dbConfig = $rmsDataPath[$HTTP_POST];
        $connectionDns = $dbConfig['DB_TYPE'] . '://' . $dbConfig['DB_USER'] . ':' . $dbConfig['DB_PWD'] . '@' . $dbConfig['DB_HOST'] . ':' . $dbConfig['DB_PORT'] . '/' . $dbConfig['DB_NAME'];

        //连接历史数据库
        //$orderformHistoryModel = M("orderform", "rms_", $connectionDns);


        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //导入分页类
        import('ORG.Util.Page');// 导入分页类
        //$total = $orderformHistoryModel->where($where)->count();// 查询满足要求的总记录数

        //查session取得page的firstRos和listRows
        if (isset($_SESSION[$moduleName . 'firstRowSearchview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowSearchview'];
        }

        $listMaxRows = 17;

        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();


        //查询历史表
        //$listResult = $orderformHistoryModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        //合并数据
        if (empty($listResult)) {
            $listResult = $orderformResult;
        } else {
            $listResult = array_merge($orderformResult, $listResult);
        }

        //echo '<pre>';
        // var_dump($listResult);
        // 从数据中列出列表的数据
        $listviewEntity = $this->getListviewEntity($listResult, $moduleId);

        $this->assign('page', $show);// 赋值分页输出
        $this->assign('moduleId', $moduleId);
        $this->assign('$currentModule', $currentModule);
        $this->assign('list_link_field', $focus->list_link_field);
        $this->assign("listHeader", $listFields);
        $this->assign('listviewEntity', $listviewEntity);
        $this->assign('calltelphone', $telphone);
        //$this->assign('page',$show);// 赋值分页输出
        $this->display('Telcustomer/orderhistoryview');

    }


    //根据输入的内容查询的订单历史记录
    public function getOrderhistoryView()
    {
        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块的模型
        $focus = D($moduleName);

        //生成list字段列表
        $listFields = array('orderformid', 'address', 'ordertxt', 'telphone', 'totalmoney', 'custtime', 'sendname', 'company', 'state', 'telname', 'rectime', 'custdate');
        //模块的ID
        $moduleId = 'orderformid';
        $listHeader = $listFields;

        $selectFields = $listFields;

        //获得查询的字段
        $searchText = $_REQUEST['searchText'];

        $where = array();
        $where['telphone'] = array('like', '%' . $searchText . '%');
        $where['address'] = array('like', '%' . $searchText . '%');
        $where['_logic'] = 'or';

        //查询当前订单表中的订单
        $orderformModel = D('Orderform');
        $orderformResult = $orderformModel->field($selectFields)->where($where)->select();
        //var_dump($orderformModel->getLastSql());
        if (empty($orderformResult)) {
            $orderformResult = array();
        }

        //读取连接信息,根据用户访问的url来判断
        require APP_PATH . 'Conf/datapath.php';
        $HTTP_POST = $this->getDomain();
        $HTTP_POST = $HTTP_POST . 'History';
        $dbConfig = $rmsDataPath[$HTTP_POST];
        $connectionDns = $dbConfig['DB_TYPE'] . '://' . $dbConfig['DB_USER'] . ':' . $dbConfig['DB_PWD'] . '@' . $dbConfig['DB_HOST'] . ':' . $dbConfig['DB_PORT'] . '/' . $dbConfig['DB_NAME'];

        //连接历史数据库
        $orderformHistoryModel = M("orderform", "rms_", $connectionDns);


        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //导入分页类
        import('ORG.Util.Page');// 导入分页类
        $total = $orderformHistoryModel->where($where)->count();// 查询满足要求的总记录数

        //查session取得page的firstRos和listRows
        if (isset($_SESSION[$moduleName . 'firstRowSearchview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowSearchview'];
        }

        $listMaxRows = 17;

        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();


        //查询历史表
        $listResult = $orderformHistoryModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        //var_dump($orderformHistoryModel->getLastSql());
        //合并数据
        if (empty($listResult)) {
            $listResult = $orderformResult;
        } else {
            $listResult = array_merge($orderformResult, $listResult);
        }

        //echo '<pre>';
        // var_dump($listResult);
        // 从数据中列出列表的数据
        $listviewEntity = $this->getListviewEntity($listResult, $moduleId);

        $this->assign('page', $show);// 赋值分页输出
        $this->assign('moduleId', $moduleId);
        $this->assign('$currentModule', $currentModule);
        $this->assign('list_link_field', $focus->list_link_field);
        $this->assign("listHeader", $listFields);
        $this->assign('listviewEntity', $listviewEntity);
        $this->assign('calltelphone', $telphone);
        //$this->assign('page',$show);// 赋值分页输出
        $this->assign('searchText', $searchText);
        $this->display('Telcustomer/orderhistoryview');

    }


    //根据客户来电，显示客户以前的订单历史记录的详细记录
    public function getByPhoneOrderhistorydetailview()
    {

        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块
        $focus = D($moduleName);

        //引入模块菜单
        //$menu_module = A('ModuleMenu');
        //$menu_module->orderHistory($currentModule,'detailview');


        //返回模块的id
        //模块的ID
        $moduleId = $focus->getPk();

        //返回模块的字段表
        $fields = array();
        $fields_model = D('Fields');
        $fields = $fields_model->getModuleFields($moduleid);

        //重新设定订单历史查询的数据库
        $date = $_REQUEST['date'];
        $dbNameTableName = 'rms_' . substr($date, 0, 4) . '.rms_orderform_' . substr($date, 5, 2);

        //取得记录ID
        $record = $_REQUEST['record'];

        //返回模块的记录
        $result = $focus->table("$dbNameTableName")->where("orderformid=$record")->find();

        //返回区块
        $blocks = $focus->getDetailBlocks($moduleid, $result);

        //dump($blocks);
        $this->assign('blocks', $blocks);
        $this->assign('record', $record);

        //返回从表的内容
        $this->get_slave_table($record, $date);
        $this->display('OrderHisto/detailview');


    }

}

?>
