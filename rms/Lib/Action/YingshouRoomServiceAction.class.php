<?php
/**
 * 外送结账模块
 * Created by zhangwh
 * User: lihua
 * Date: 16/7/5
 * Time: 下午12:46
 */

class YingshouRoomServiceAction extends ModuleAction

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

            $connectionDb = $this->connectReveueDb('');
            // 连接数据库
            $roomserviceModel = M("roomservice_10", " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());


            // 建立查询条件
            $where = array();
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            $total = $roomserviceModel->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if(!empty($listMaxRows)){

            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
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
            $selectFields[] = 'longitude';
            $selectFields[] = 'latitude';
            $selectFields[] = 'sendlongitude';
            $selectFields[] = 'sendlatitude';
            array_unshift($selectFields, $moduleId);

            $selectFields = array('roomserviceid','name','totalmoney','turnover','cash','nocharge');

            $listResult = $roomserviceModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray  = $listResult;
            } else {
                $orderHandleArray  = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray,'sql'=>$roomserviceModel->getLastSql());
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

            //如果存在页数,获取
            if(isset($_REQUEST['pagetype'])){
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            }else{
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', array()),
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
                if($key == 'sendname'){
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'ListviewModule.sendname'
                    );
                }else{
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width']
                    );
                }

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

            //是否存在选中的行号
            if(isset($_REQUEST['rowIndex'])){
                $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
            }else{
                $this->assign ( 'rowIndex',0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期
            $this->assign('cdate',date('Y-m-d'));
            //当前午别
            $this->assign('cap',$this->getAp());
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }


    //生成报数单界面
    public function generalview(){
        // 返回当前的模块名
        $moduleName = $this->getActionName ();
        //当前日期
        $this->assign('cdate',date('Y-m-d'));
        //当前午别
        $this->assign('cap',$this->getAp());
        $this->display($moduleName . '/generalview');
    }

    /**
     *
     */
    public function roomCalculate(){
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        //结账日期
        $roomDate = $_REQUEST['room_date'];
        //结账午别
        $roomAp = $_REQUEST['room_ap'];

        $where=array();
        $where['custdate'] = $roomDate;
        $where['ap'] = $roomAp;

        //开启订单数据表
        $orderformModel  = D('orderform');
        $ordergoodsModel = D('ordergoods');

        //查询出所有结账的送餐员
        $sendnameResult = $orderformModel->distinct(true)->field('sendname')->select();
        foreach($sendnameResult as $sendname){
            $totalmoney[$sendname['sendname']] = 0;
        }

        //查询数据
        $orderformResult = $orderformModel->where($where)->select();
        //开始计算每一个送餐员的数据
        foreach($orderformResult as $orderform){
            $where = array();
            $where['ordersn'] = $orderform['ordersn'];
            $ordergoodsResule = $ordergoodsModel->where($where)->select();
            $goodsMoney = 0;
            foreach($ordergoodsResule as $ordergoods){
                $goodsMoney += $ordergoods['number'] * $ordergoods['price'];
            }
            $totalmoney[$orderform['sendname']] +=  $goodsMoney;
        }

        $db_type = 'mysql';
        $db_user = C('db_user');
        $db_pwd = C('db_pwd');
        $db_host = C('db_host');
        $db_port = C('db_port');
        $db_name = 'rms_revenue';

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        // 连接数据库
        $roomserviceModel = M("roomservice09", " ", $connectionDns);

        //保存到表中
        foreach($sendnameResult as $sendname){
            $data = array();
            $data['name'] = $sendname['sendname'];
            $data['totalmoney'] = $totalmoney[$sendname['sendname']];
            $data['domain'] = $_SERVER['HTTP_HOST'];
            $where = array();
            $where['name'] = $sendname['sendname'];
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $roomserviceResult = $roomserviceModel->where($where)->find();
            if(!empty($roomserviceResult)){
                $data['update_time'] = date('H:i:s');
                $roomserviceModel->where($where)->save($data);
            }else{
                $data['create_time'] = date('H:i:s');
                $roomserviceModel->create();
                $roomserviceModel->add($data);
            }

        }

        $this->ajaxReturn ('ok');

    }

    /**
     * 查看送餐员结账的订单
     */
    public function checkOrder(){
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);


            $db_type = 'mysql';
            $db_user = C('db_user');
            $db_pwd = C('db_pwd');
            $db_host = C('db_host');
            $db_port = C('db_port');
            $db_name = 'rms_revenue';

            $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

            // 连接数据库
            $roomserviceModel = M("roomservice09", " ", $connectionDns);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());


            // 建立查询条件
            $where = array();
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            $total = $roomserviceModel->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if(!empty($listMaxRows)){

            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
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
            $selectFields[] = 'longitude';
            $selectFields[] = 'latitude';
            $selectFields[] = 'sendlongitude';
            $selectFields[] = 'sendlatitude';
            array_unshift($selectFields, $moduleId);

            $selectFields = array('name','totalmoney','turnover','cash','nocharge');

            $listResult = $roomserviceModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray  = $listResult;
            } else {
                $orderHandleArray  = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray,'sql'=>$roomserviceModel->getLastSql());
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
                if ($key == 'sendname') {
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'CheckOrderModule.sendname'
                    );
                } else {
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width']
                    );
                }

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

    // 编辑结账数据的页面editview
    public function editview() {

        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign ( 'returnAction', $returnAction );


        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $getdate = $_REQUEST['getdate'];
        $where = array();
        $where [$moduleId] = $record;

        $connectionDb = $this->connectReveueDb($getdate);

        // 连接数据库
        $roomserviceModel = M("roomservice_".substr($getdate,5,2), " ", $connectionDb);


        // 返回模块的行记录
        $result =  $roomserviceModel->where ( $where )->find ();

        $this->assign ( 'info', $result );
        $this->assign ( 'fieldsFocus', $focus->fieldsFocus ); // 指定字段获得焦点
        $this->assign ( 'record', $record ); // 订单记录号
        $this->assign ( 'pagenumber',$_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign ( 'rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign ( 'pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara ();

        // 返回从表的内容
        $this->get_slave_table ( $record ,$getdate);

        $this->display ( $moduleName . '/editview' );

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
            $this->assign('returnModule',$_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row',$row);  //返回点击的订购商品行

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
            $popupModuleName = 'YingshouFreebie';

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
            $popupModuleName = 'YingshouAccounts';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

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
                'formatter' => $moduleName . 'PopupFreebieviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule',$_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row',$row);  //返回点击的订购商品行

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
            $popupModuleName = 'YingshouMealticket';

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
            $popupModuleName = 'YingshouMealticket';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

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
                'formatter' => $moduleName . 'PopupMealticketviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule',$_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row',$row);  //返回点击的订购商品行

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

        // 模块的ID
        $moduleNameId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST ['record'];

        // 重新设定订单历史查询的数据库
        $getdate = $_REQUEST ['getdate'];

        $connectionDb = $this->connectReveueDb($getdate);

        // 连接数据库
        $roomserviceModel = M("roomservice_".substr($getdate,5,2), " ", $connectionDb);

        // 返回模块的记录
        $result = $roomserviceModel->where("roomserviceid=$record")->find();


        $this->assign ( 'info', $result );
        $this->assign ( 'record', $record );

        // 返回从表的内容
        $this->get_slave_table ( $record , $getdate);
        $this->display($moduleName.'/detailview');
    }

    // 返回结账从表的内容:签单;赠卡,餐券
    public function get_slave_table($record,$getdate)
    {
        //链接数据库
        $connectionDb = $this->connectReveueDb($getdate);

        //定义查询条件
        $where = array();
        $where['roomserviceid'] = $record;

        //取得客户表
        $roomserviceaccountsModel = M("roomserviceaccounts_".substr($getdate,5,2), " ", $connectionDb);
        $roomserviceaccountsResult = $roomserviceaccountsModel->where($where)->select();
        $this->assign('roomserviceaccounts',$roomserviceaccountsResult);

        //取得赠卡
        $roomservicefreebieModel = M('roomservicefreebie_'.substr($getdate,5,2), " ", $connectionDb);
        $roomservicefreebieResult = $roomservicefreebieModel->where($where)->select();
        $this->assign('roomservicefreebie',$roomservicefreebieResult);

        //取得赠券
        $roomservicemealticketModel = M('roomservicemealticket_'.substr($getdate,5,2), " ", $connectionDb);
        $roomservicemealticketResult = $roomservicemealticketModel->where($where)->select();
        $this->assign('roomservicemealticket',$roomservicemealticketResult);
    }


}

