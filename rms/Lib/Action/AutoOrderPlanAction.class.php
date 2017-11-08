<?php
/**
 * Created by zhangwh
 * User: lihua
 * Date: 16/9/1
 * Time: 下午12:20
 * 订单抄单模块
 */

    class AutoOrderPlanAction extends ModuleAction{

        /* 删除记录 */
        public function delete() {
            // 返回当前的模块名
            $moduleName = $this->getActionName ();

            $focus = D ( $moduleName );
            $this->assign ( 'moduleName', $moduleName );

            // 取得保存的主键
            $record = $_REQUEST ['record'];

            $moduleId = $focus->getPk ();

            $where [$moduleId] = $record;

            $data = array();
            $data['bookdate'] = '';
            // 删除记录
            $result = $focus->where ( $where )->save($data);

            if($result){
                $info['status'] = 1;
                $info['info'] ='删除成功' ;
                $this->ajaxReturn(json_encode($info),'EVAL');
            }else{
                $info['status'] = 0;
                $info['info'] ='删除失败' ;
                $this->ajaxReturn(json_encode($info),'EVAL');
            }

        }

        /**
         *   地址查询框
         */
        public function searchAddressInput()
        {
            $this->display('AutoOrderPlan/searchaddressinput');
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
                if(isset($_REQUEST['pagetype'])){
                    $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
                }else{
                    $pageNumber = 1;
                }

                $searchText = urlencode($_REQUEST ['searchTextAddress']); // 查询内容

                $datagrid = array(
                    'options' => array(
                        'url' => U('AutoOrderPlan/searchviewAddress', array('searchTextAddress' => $searchText)),
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
                $datagrid ['fields'] ['操作'] = array (
                    'field' => 'id',
                    'width' => 20,
                    'align' => 'center',
                    'formatter' => $moduleName.'ListviewModule.operate'
                );
                $this->assign('datagrid', $datagrid);


                // 加入模块id到listHeader中
                // array_unshift($listFields,$moduleNameId);
                $listHeader = $listFields;
                $this->assign("listHeader", $listHeader); // 列表头
                $this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法
                //是否存在选中的行号
                if(isset($_REQUEST['rowIndex'])){
                    $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
                }else{
                    $this->assign ( 'rowIndex',0);
                }
                $this->assign('action_name','searchviewaddress');
                $this->display('AutoOrderPlan/listview'); // 查询的结果显示
            }
        }


        /**
         * 电话查询输入
         */
        public function searchTelphoneInput()
        {
            $this->display('AutoOrderPlan/searchtelphoneinput');
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
                if(isset($_REQUEST['pagetype'])){
                    $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
                }else{
                    $pageNumber = 1;
                }

                $datagrid = array(
                    'options' => array(
                        'url' => U('AutoOrderPlan/searchviewTelphone', array('searchTextTelphone' => $searchText)),
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
                if(isset($_REQUEST['rowIndex'])){
                    $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
                }else{
                    $this->assign ( 'rowIndex',0);
                }
                $this->assign('action_name','searchviewtelphone');
                $this->display('AutoOrderPlan/listview'); // 查询的结果显示
            }

        }


        /**
         * 综合查询页面
         */
        public function searchOtherInput(){
            // 返回当前的模块名
            $moduleName = $this->getActionName ();
            $this->assign ( 'moduleName', $moduleName );
            $this->display($moduleName.'/searchotherinput');
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
                if(isset($_REQUEST['pagetype'])){
                    $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
                }else{
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
                if(isset($_REQUEST['rowIndex'])){
                    $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
                }else{
                    $this->assign ( 'rowIndex',0);
                }
                $this->assign('action_name','searchviewother');
                $this->display('BookOrder/listview'); // 查询的结果显示
            }
        }


        //返回一些其他的数据,比如下拉列表框等的数据
        public function returnMainFnPara(){

            //分公司的数据
            $companymgr_model = D('companymgr');
            $companymgr = $companymgr_model->field('name')->select();
            //在company字段中写入值
            $this->assign('company',$companymgr);

            //因为卫林平的需要，添加所有产品
            //$products_model = D('Products');
            //$orderproducts = $products_model->field("productsid,code,name,price")->order('price asc')->select();
            //echo $products_model->getLastSql();
            //dump($orderproducts);

            //$this->assign('orderproducts',$orderproducts);

            //查询送餐方式和送餐费的设置
            $this->assign('shippingname','分公司配送');
            $this->assign('shippingmoney',5);
            //发票内容
            $invoicecontent = array(
                array('name'=>'工作餐'),
                array('name'=>'盒饭'),
                array('name'=>'餐饮')
            );
            $this->assign('invoicecontent',$invoicecontent);

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
        public function popupview(){

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称

            //启动当前模块
            $focus = D($moduleName);

            //取得模块的名称
            $popupModuleName = I('module');;;
            $this->assign('moduleName',$popupModuleName);   //模块名称

            //启动弹出选择的模块
            $popupModule = D($popupModuleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航名称

            //取得父窗口的表格行数
            $row = $_REQUEST['row'];



            //生成list字段列表
            $listFields = $focus->popupProductsFields;
            //模块的ID
            $moduleId = $popupModule->getPk();
            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','listview');  //定义返回的方法

            //导入分页类
            import('ORG.Util.SimplePage');// 导入分页类
            $total      = $popupModule->count();// 查询满足要求的总记录数
            //查session取得page的firstRos和listRows


            if(!isset($_SESSION[$moduleName.'firstRowlistview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowlistview'];
            }

            //var_dump($_SESSION['test']);
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            }
            $Page = new SimplePage($total,$listMaxRows);
            $show = $Page->show();



            //查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);
            $listResult = $popupModule->field($selectFields)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();


            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出
            $this->assign('returnAction','listview');  //返回的 action
            $this->assign('list_link_field',$focus->popupProductsLinkField);  //定义焦点字段
            $this->assign('row',$row);  //返回选择的行

            $this->display('OrderForm/popupviewProducts');
        }


        //根据产品代码，查询产品名称
        public function getProductsByCode(){
            $code = $_REQUEST['code'];
            $products_model = D('Products');
            $products = $products_model->field('name,price')->where("code='$code'")->find();
            //echo $products_model->getLastSql();
            //dump($products);
            $this->ajaxReturn($products,'JSON');
        }

        //返回从表的内容:产品
        public function get_slave_table($record){

            //取得产品信息
            $autoproductsModel = D('Autoproducts');
            $autoproducts = $autoproductsModel->field('autoorderplanid,code,name,shortname,price,number,money')->where("autoorderplanid=$record")->select();
            $this->assign('orderproducts',$autoproducts);

            //取得订单日志
            $autoactionModel =D('Autoaction');
            $autoaction = $autoactionModel->where("autoorderplanid=$record")->select();
            $this->assign('orderaction',$autoaction);

            //单独取得订单金额,预计预订日期
            $autoorderplanModel = D('Autoorderplan');
            $autoorderplanResult = $autoorderplanModel->field('totalmoney,custtime,repeattype,repeatcontent,startdate,enddate,saturday,sunday')->where("autoorderplanid=$record")->select();
            $totalmoney = $autoorderplanResult[0]['totalmoney'];
            $this->assign('totalmoney',$totalmoney);
            $custtimeResult = $autoorderplanResult[0]['custtime'];
            if($autoorderplanResult[0]['saturday'] ==  1){
                $this->assign('saturday','checked');
            }
            if($autoorderplanResult[0]['sunday'] ==  1){
                $this->assign('sunday','checked');
            }
            $this->assign('startdate',$autoorderplanResult[0]['startdate']);
            $this->assign('enddate',$autoorderplanResult[0]['enddate']);
            if($custtimeResult){
                $this->assign('custtime_1',substr($custtimeResult,0,2));
                $this->assign('custtime_2',substr($custtimeResult,3,2));
            }
            if($autoorderplanResult[0]['repeattype'] == '按周重复'){
                $this->assign('repeattype','按周重复');
                $repeatcontent = $autoorderplanResult[0]['repeatcontent'];
                $repeatcontentArr = explode(',',$repeatcontent);
                if(in_array('周一',$repeatcontentArr)){
                    $this->assign('week1','checked');
                }
                if(in_array('周二',$repeatcontentArr)){
                    $this->assign('week2','checked');
                }
                if(in_array('周三',$repeatcontentArr)){
                    $this->assign('week3','checked');
                }
                if(in_array('周四',$repeatcontentArr)){
                    $this->assign('week4','checked');
                }
                if(in_array('周五',$repeatcontentArr)){
                    $this->assign('week5','checked');
                }
                if(in_array('周六',$repeatcontentArr)){
                    $this->assign('week6','checked');
                }
                if(in_array('周日',$repeatcontentArr)){
                    $this->assign('week7','checked');
                }
            }
            if($autoorderplanResult[0]['repeattype'] == '按月重复') {
                $this->assign('repeattype','按月重复');
                $repeatcontent = $autoorderplanResult[0]['repeatcontent'];
                $repeatcontentArr = explode(',',$repeatcontent);
                if(in_array('1号',$repeatcontentArr)){
                    $this->assign('day1','checked');
                }
                if(in_array('2号',$repeatcontentArr)){
                    $this->assign('day2','checked');
                }
                if(in_array('3号',$repeatcontentArr)){
                    $this->assign('day3','checked');
                }
                if(in_array('4号',$repeatcontentArr)){
                    $this->assign('day4','checked');
                }
                if(in_array('5号',$repeatcontentArr)){
                    $this->assign('day5','checked');
                }
                if(in_array('6号',$repeatcontentArr)){
                    $this->assign('day6','checked');
                }
                if(in_array('7号',$repeatcontentArr)){
                    $this->assign('day7','checked');
                }
                if(in_array('8号',$repeatcontentArr)){
                    $this->assign('day8','checked');
                }
                if(in_array('9号',$repeatcontentArr)){
                    $this->assign('day9','checked');
                }
                if(in_array('10号',$repeatcontentArr)){
                    $this->assign('day10','checked');
                }
                if(in_array('11号',$repeatcontentArr)){
                    $this->assign('day11','checked');
                }
                if(in_array('12号',$repeatcontentArr)){
                    $this->assign('day12','checked');
                }
                if(in_array('13号',$repeatcontentArr)){
                    $this->assign('day13','checked');
                }
                if(in_array('14号',$repeatcontentArr)){
                    $this->assign('day14','checked');
                }
                if(in_array('15号',$repeatcontentArr)){
                    $this->assign('day15','checked');
                }
                if(in_array('16号',$repeatcontentArr)) {
                    $this->assign('day16', 'checked');
                }
                if(in_array('17号',$repeatcontentArr)){
                    $this->assign('day17','checked');
                }
                if(in_array('18号',$repeatcontentArr)){
                    $this->assign('day18','checked');
                }
                if(in_array('19号',$repeatcontentArr)){
                    $this->assign('day19','checked');
                }
                if(in_array('20号',$repeatcontentArr)){
                    $this->assign('day20','checked');
                }
                if(in_array('21号',$repeatcontentArr)){
                    $this->assign('day21','checked');
                }
                if(in_array('22号',$repeatcontentArr)){
                    $this->assign('day22','checked');
                }
                if(in_array('23号',$repeatcontentArr)){
                    $this->assign('day23','checked');
                }
                if(in_array('24号',$repeatcontentArr)){
                    $this->assign('day24','checked');
                }
                if(in_array('25号',$repeatcontentArr)){
                    $this->assign('day25','checked');
                }
                if(in_array('26号',$repeatcontentArr)){
                    $this->assign('day26','checked');
                }
                if(in_array('27号',$repeatcontentArr)){
                    $this->assign('day27','checked');
                }
                if(in_array('28号',$repeatcontentArr)){
                    $this->assign('day28','checked');
                }
                if(in_array('29号',$repeatcontentArr)){
                    $this->assign('day29','checked');
                }
                if(in_array('30号',$repeatcontentArr)){
                    $this->assign('day30','checked');
                }
                if(in_array('31号',$repeatcontentArr)){
                    $this->assign('day31','checked');
                }
            }
        }


        //保存产品数据等其他数据
        function  save_slave_table($record){

            $orderTxt = '';
            $totalmoney = 0;
            //保存地址的数量
            $productsLength = $_REQUEST['productsLength'];
            $autoproductsModel = D('Autoproducts');
            for($i=1;$i<= $productsLength;$i++){
                $code = $_REQUEST['productsCode_'.$i];
                $name = $_REQUEST['productsName_'.$i];
                $shortname = $_REQUEST['productsShortName_' . $i];
                $price = $_REQUEST['productsPrice_'.$i];
                $number = $_REQUEST['productsNumber_'.$i];
                $money = $_REQUEST['productsMoney_'.$i];
                $data = array();
                $data['code'] = $code;
                $data['name'] = $name;
                $data['shortname'] = $shortname;
                $data['price'] = $price;
                $data['number'] = $number;
                $data['money'] = $money;
                $data['autoorderplanid'] = $record;
                if( !empty($name) and  !empty($number)){
                    $autoproductsModel->create();
                    $autoproductsModel->add($data);
                    $orderTxt .= $number . '×' . $shortname. ' ';
                    $totalmoney += $number * $price;
                }
            }

            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['truename'];
            //记入操作到action中
            $autoactionModel = D('Autoaction');
            $data = array();
            $data['autoorderplanid'] = $record;  //订单号
            $data['action'] = $name . ' '.date('Y-m-d').' 新建抄单单 '.$_REQUEST['address'].' '.$orderTxt;
            $data['logtime'] = date('H:i:s');
            $data['domain'] = $this->getDomain();
            $autoactionModel->create();
            $result = $autoactionModel->add($data);

            $data = array();
            $data['startdate'] = $_REQUEST['autoorderplan_startdate'];
            $data['enddate'] = $_REQUEST['autoorderplan_enddate'];
            if($_REQUEST['autoorderplan_saturday'] == true){
                $data['saturday'] = 1;  //星期六不送
            }
            if($_REQUEST['autoorderplan_sunday'] == true){
                $data['sunday'] = 1; //星期天不送
            }
            //计算重复的情况
            $repeattype = $_REQUEST['autoorderplan_repeattype'];
            if($repeattype == '按周重复'){
                $data['repeattype'] = $repeattype;
                if($_REQUEST['autoorderdate_week1'] == true){
                    $data['repeatcontent'] = '周一,';
                }
                if($_REQUEST['autoorderdate_week2'] == true){
                    $data['repeatcontent'] .= '周二,';
                }
                if($_REQUEST['autoorderdate_week3'] == true){
                    $data['repeatcontent'] .= '周三,';
                }
                if($_REQUEST['autoorderdate_week4'] == true){
                    $data['repeatcontent'] .= '周四,';
                }
                if($_REQUEST['autoorderdate_week5'] == true){
                    $data['repeatcontent'] .= '周五,';
                }
                if($_REQUEST['autoorderdate_week6'] == true){
                    $data['repeatcontent'] .= '周六,';
                }
                if($_REQUEST['autoorderdate_week7'] == true){
                    $data['repeatcontent'] .= '周日,';
                }
            }
            if($repeattype == '按月重复') {
                $data['repeattype'] = $repeattype;
                if($_REQUEST['autoorderdate_1'] == true){
                    $data['repeatcontent'] = '1号,';
                }
                if($_REQUEST['autoorderdate_2'] == true){
                    $data['repeatcontent'] .= '2号,';
                }
                if($_REQUEST['autoorderdate_3'] == true){
                    $data['repeatcontent'] .= '3号,';
                }
                if($_REQUEST['autoorderdate_4'] == true){
                    $data['repeatcontent'] .= '4号,';
                }
                if($_REQUEST['autoorderdate_5'] == true){
                    $data['repeatcontent'] .= '5号,';
                }
                if($_REQUEST['autoorderdate_6'] == true){
                    $data['repeatcontent'] .= '6号,';
                }
                if($_REQUEST['autoorderdate_7'] == true){
                    $data['repeatcontent'] .= '7号,';
                }
                if($_REQUEST['autoorderdate_8'] == true){
                    $data['repeatcontent'] .= '8号,';
                }
                if($_REQUEST['autoorderdate_9'] == true){
                    $data['repeatcontent'] .= '9号,';
                }
                if($_REQUEST['autoorderdate_10'] == true){
                    $data['repeatcontent'] .= '10号,';
                }
                if($_REQUEST['autoorderdate_11'] == true){
                    $data['repeatcontent'] .= '11号,';
                }
                if($_REQUEST['autoorderdate_12'] == true){
                    $data['repeatcontent'] .= '12号,';
                }
                if($_REQUEST['autoorderdate_13'] == true){
                    $data['repeatcontent'] .= '13号,';
                }
                if($_REQUEST['autoorderdate_14'] == true){
                    $data['repeatcontent'] .= '14号,';
                }
                if($_REQUEST['autoorderdate_15'] == true){
                    $data['repeatcontent'] .= '15号,';
                }
                if($_REQUEST['autoorderdate_16'] == true){
                    $data['repeatcontent'] .= '16号,';
                }
                if($_REQUEST['autoorderdate_17'] == true){
                    $data['repeatcontent'] .= '17号,';
                }
                if($_REQUEST['autoorderdate_18'] == true){
                    $data['repeatcontent'] .= '18号,';
                }
                if($_REQUEST['autoorderdate_19'] == true){
                    $data['repeatcontent'] .= '19号,';
                }
                if($_REQUEST['autoorderdate_20'] == true){
                    $data['repeatcontent'] .= '20号,';
                }
                if($_REQUEST['autoorderdate_21'] == true){
                    $data['repeatcontent'] .= '21号,';
                }
                if($_REQUEST['autoorderdate_22'] == true){
                    $data['repeatcontent'] .= '22号,';
                }
                if($_REQUEST['autoorderdate_23'] == true){
                    $data['repeatcontent'] .= '23号,';
                }
                if($_REQUEST['autoorderdate_24'] == true){
                    $data['repeatcontent'] .= '24号,';
                }
                if($_REQUEST['autoorderdate_25'] == true){
                    $data['repeatcontent'] .= '25号,';
                }
                if($_REQUEST['autoorderdate_26'] == true){
                    $data['repeatcontent'] .= '26号,';
                }
                if($_REQUEST['autoorderdate_27'] == true){
                    $data['repeatcontent'] .= '27号,';
                }
                if($_REQUEST['autoorderdate_28'] == true){
                    $data['repeatcontent'] .= '28号,';
                }
                if($_REQUEST['autoorderdate_29'] == true){
                    $data['repeatcontent'] .= '29号,';
                }
                if($_REQUEST['autoorderdate_30'] == true){
                    $data['repeatcontent'] .= '30号,';
                }
                if($_REQUEST['autoorderdate_31'] == true){
                    $data['repeatcontent'] .= '31号,';
                }
            }

            //加送餐费
            $totalmoney = $totalmoney + $_REQUEST['shippingmoney'];
            //保存数量规格
            $data['ordertxt'] = $orderTxt;
            $data['totalmoney'] = $totalmoney;
            $data['paidmoney'] = $_REQUEST['paidmoney']; //已付金额
            $data['shouldmoney'] = $_REQUEST['shouldmoney'];
            $data['lastdatetime'] = date('Y-m-d H:i:s');  //最后修改时间
            $autoorderModel = D('Autoorderplan');
            $result = $autoorderModel->where("autoorderplanid=$record")->save($data);
        }

        //保存产品数据等其他数据
        function  update_slave_table($record){
            //订单号
            $moduleId = 'autoorderplanid';

            $autoproductsModel = D('Autoproducts');
            //先清掉数据
            $autoproductsModel->where("autoorderplanid=$record")->delete();

            $orderTxt = '';
            $totalmoney = 0;
            //保存地址的数量
            $productsLength = $_REQUEST['productsLength'];
            for($i=1;$i<= $productsLength;$i++){
                $code = $_REQUEST['productsCode_'.$i];
                $name = $_REQUEST['productsName_'.$i];
                $shortname = $_REQUEST['productsShortName_' . $i];
                $price = $_REQUEST['productsPrice_'.$i];
                $number = $_REQUEST['productsNumber_'.$i];
                $money = $_REQUEST['productsMoney_'.$i];
                $data['code'] = $code;
                $data['name'] = $name;
                $data['shortname'] = $shortname;
                $data['price'] = $price;
                $data['number'] = $number;
                $data['money'] = $money;
                $data['autoorderplanid'] = $record;
                if( !empty($name) and  !empty($number)){
                    $autoproductsModel->create();
                    $autoproductsModel->add($data);
                    $orderTxt .= $number . '×' . $shortname. ' ';
                    $totalmoney += $number * $price;
                }
            }



            $data = array();
            $data['startdate'] = $_REQUEST['autoorderplan_startdate'];
            $data['enddate'] = $_REQUEST['autoorderplan_enddate'];
            if($_REQUEST['autoorderplan_saturday'] == true){
                $data['saturday'] = 1;  //星期六不送
            }else{
                $data['saturday'] = 0;  //星期六送
            }
            if($_REQUEST['autoorderplan_sunday'] == true){
                $data['sunday'] = 1; //星期天不送
            }else{
                $data['sunday'] = 0; //星期天送
            }
            //计算重复的情况
            $repeattype = $_REQUEST['autoorderplan_repeattype'];
            if($repeattype == '按周重复'){
                $data['repeattype'] = $repeattype;
                if($_REQUEST['autoorderdate_week1'] == true){
                    $data['repeatcontent'] = '周一,';
                }
                if($_REQUEST['autoorderdate_week2'] == true){
                    $data['repeatcontent'] .= '周二,';
                }
                if($_REQUEST['autoorderdate_week3'] == true){
                    $data['repeatcontent'] .= '周三,';
                }
                if($_REQUEST['autoorderdate_week4'] == true){
                    $data['repeatcontent'] .= '周四,';
                }
                if($_REQUEST['autoorderdate_week5'] == true){
                    $data['repeatcontent'] .= '周五,';
                }
                if($_REQUEST['autoorderdate_week6'] == true){
                    $data['repeatcontent'] .= '周六,';
                }
                if($_REQUEST['autoorderdate_week7'] == true){
                    $data['repeatcontent'] .= '周日,';
                }
            }
            if($repeattype == '按月重复') {
                $data['repeattype'] = $repeattype;
                if($_REQUEST['autoorderdate_1'] == true){
                    $data['repeatcontent'] = '1号,';
                }
                if($_REQUEST['autoorderdate_2'] == true){
                    $data['repeatcontent'] .= '2号,';
                }
                if($_REQUEST['autoorderdate_3'] == true){
                    $data['repeatcontent'] .= '3号,';
                }
                if($_REQUEST['autoorderdate_4'] == true){
                    $data['repeatcontent'] .= '4号,';
                }
                if($_REQUEST['autoorderdate_5'] == true){
                    $data['repeatcontent'] .= '5号,';
                }
                if($_REQUEST['autoorderdate_6'] == true){
                    $data['repeatcontent'] .= '6号,';
                }
                if($_REQUEST['autoorderdate_7'] == true){
                    $data['repeatcontent'] .= '7号,';
                }
                if($_REQUEST['autoorderdate_8'] == true){
                    $data['repeatcontent'] .= '8号,';
                }
                if($_REQUEST['autoorderdate_9'] == true){
                    $data['repeatcontent'] .= '9号,';
                }
                if($_REQUEST['autoorderdate_10'] == true){
                    $data['repeatcontent'] .= '10号,';
                }
                if($_REQUEST['autoorderdate_11'] == true){
                    $data['repeatcontent'] .= '11号,';
                }
                if($_REQUEST['autoorderdate_12'] == true){
                    $data['repeatcontent'] .= '12号,';
                }
                if($_REQUEST['autoorderdate_13'] == true){
                    $data['repeatcontent'] .= '13号,';
                }
                if($_REQUEST['autoorderdate_14'] == true){
                    $data['repeatcontent'] .= '14号,';
                }
                if($_REQUEST['autoorderdate_15'] == true){
                    $data['repeatcontent'] .= '15号,';
                }
                if($_REQUEST['autoorderdate_16'] == true){
                    $data['repeatcontent'] .= '16号,';
                }
                if($_REQUEST['autoorderdate_17'] == true){
                    $data['repeatcontent'] .= '17号,';
                }
                if($_REQUEST['autoorderdate_18'] == true){
                    $data['repeatcontent'] .= '18号,';
                }
                if($_REQUEST['autoorderdate_19'] == true){
                    $data['repeatcontent'] .= '19号,';
                }
                if($_REQUEST['autoorderdate_20'] == true){
                    $data['repeatcontent'] .= '20号,';
                }
                if($_REQUEST['autoorderdate_21'] == true){
                    $data['repeatcontent'] .= '21号,';
                }
                if($_REQUEST['autoorderdate_22'] == true){
                    $data['repeatcontent'] .= '22号,';
                }
                if($_REQUEST['autoorderdate_23'] == true){
                    $data['repeatcontent'] .= '23号,';
                }
                if($_REQUEST['autoorderdate_24'] == true){
                    $data['repeatcontent'] .= '24号,';
                }
                if($_REQUEST['autoorderdate_25'] == true){
                    $data['repeatcontent'] .= '25号,';
                }
                if($_REQUEST['autoorderdate_26'] == true){
                    $data['repeatcontent'] .= '26号,';
                }
                if($_REQUEST['autoorderdate_27'] == true){
                    $data['repeatcontent'] .= '27号,';
                }
                if($_REQUEST['autoorderdate_28'] == true){
                    $data['repeatcontent'] .= '28号,';
                }
                if($_REQUEST['autoorderdate_29'] == true){
                    $data['repeatcontent'] .= '29号,';
                }
                if($_REQUEST['autoorderdate_30'] == true){
                    $data['repeatcontent'] .= '30号,';
                }
                if($_REQUEST['autoorderdate_31'] == true){
                    $data['repeatcontent'] .= '31号,';
                }
            }

            //加送餐费
            $totalmoney = $totalmoney + $_REQUEST['shippingmoney'];
            //保存数量规格
            $data['ordertxt'] = $orderTxt;
            $data['totalmoney'] = $totalmoney;
            $data['paidmoney'] = $_REQUEST['paidmoney']; //已付金额
            $data['shouldmoney'] = $_REQUEST['shouldmoney'];
            $data['lastdatetime'] = date('Y-m-d H:i:s');  //最后修改时间
            $autoorderplanModel = D('Autoorderplan');
            $result = $autoorderplanModel->where("$moduleId=$record")->save($data);

            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo ['truename'];

            //记入操作到action中
            $autoactionModel = D('Autoaction');
            $action['autoorderplanid'] = $record;  //订单号
            $action['action'] = $name .' '.date('Y-m-d'). ' 改单 '.$_REQUEST['address'].' '.$orderTxt ;
            $action['logtime'] = date('H:i:s');
            $action['domain'] = $this->getDomain();
            $autoactionModel->create();
            $result = $autoactionModel->add($action);
        }

        //根据来电，返回来电的发票抬头
        public function getTelphoneHeader(){
            //取来电
            $telphone = $_REQUEST['telphoneNumber'];
            //取得来电客户的ID
            $telCustomerModel = D('Telcustomer');
            $where = array();
            $where['telphone'] = $telphone;
            $telCustomerResult = $telCustomerModel->where($where)->find();

            $returnajax = array();
            if($telCustomerResult){
                $telCustomerId = $telCustomerResult['telcustomerid'];
            }else{
                $returnajax['error'] = 'error';
                $this->ajaxReturn($returnajax,'JSON');
            }

            $telFapiaoModel  = D('Telinvoice');
            //查询
            $where =array();
            $where['telcustomerid'] = $telCustomerId;
            $headerResult = $telFapiaoModel->field('header')->where($where)->select();
            //var_dump($telFapiaoModel->getLastSql());
            if($headerResult){
                $returnajax['success'] = 'success';
                $returnajax['data'] = $headerResult;
                $this->ajaxReturn($returnajax,'JSON');
            }else{
                $returnajax['error'] = 'error';
                $this->ajaxReturn($returnajax,'JSON');
            }

        }

        //插入，补充数据的回调函数
        public function autoParaInsert(){

            $custtime_1 = $_REQUEST['custtime_1'];
            $custtime_2 = $_REQUEST['custtime_2'];
            if (empty ($custtime_1)) {
                $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
            } else {
                if(empty($custtime_2)){
                    $custtime = $custtime_1 . ":00:00";
                }else{
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
            $auto = array (
                array('recdate',date('Y-m-d')),  //录入日期
                array('rectime',date('H:i:s')), // 对录入时间
                array('telname',$name),   //接线员
                array('ap',$ap),
                array('custtime',$custtime),
                array(
                    'invoiceheader',
                    $_REQUEST ['invoiceheader']
                ), // 发票抬头
                array(
                    'invoicebody',
                    $_REQUEST ['invoicebody']
                ), // 发票内容
                array('state','抄单'),
                array('domain',$this->getDomain())
            );

            return $auto;

        }

        //更新，补充数据的回调函数
        public function autoParaUpdate(){

            $custtime_1 = $_REQUEST['custtime_1'];
            $custtime_2 = $_REQUEST['custtime_2'];
            if (empty ($custtime_1)) {
                $custtime = date('H:i:s', time() + 30 * 60); // 自动加半小时
            } else {
                if(empty($custtime_2)){
                    $custtime = $custtime_1 . ":00:00";
                }else{
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
            $auto = array (
                array('ap',$ap),
                array('custtime',$custtime),
                array(
                    'invoiceheader',
                    $_REQUEST ['invoiceheader']
                ), // 发票抬头
                array(
                    'invoicebody',
                    $_REQUEST ['invoicebody']
                ), // 发票内容
                array('state','抄单'),
                array('domain',$this->getDomain())
            );

            return $auto;
        }


        //导入订单到订单表中
        public function  importOrder()
        {
            //抄单主表
            $autoorderplanModel = D('Autoorderplan');
            //抄单产品
            $autoproductModel = D('Autoproducts');
            //抄单活动表
            $autoactivityModel = D('Autoactivity');
            //抄单支付表
            $autopaymentModel = D('Autopayment');
            //抄单日志表
            $autoactionModel = D('Autoaction');

            //当前日期
            $currentDate = date('j');
            $currentDateSql = $currentDate . '号';

            //当前星期
            $week = date("w");
            switch( $week){
                case 1:
                    $currentWeek = '周一';
                    break;
                case 2:
                    $currentWeek = '周二';
                    break;
                case 3:
                    $currentWeek = '周三';
                    break;
                case 4:
                    $currentWeek = '周四';
                    break;
                case 5:
                    $currentWeek = '周五';
                    break;
                case 6:
                    $currentWeek = '周六';
                    break;
                case 0:
                    $currentWeek = '周日';
                    break;

                }

            /** 首先找出所以的按月重复的订单  */
            $where = array();
            $where['repeattype'] = '按月重复';
            $where['startdate'] = array('ELT',date('Y-m-d'));
            $where['enddate'] = array('EGT',date('Y-m-d'));
            $autoorderplanResult = $autoorderplanModel->where($where)->select();
            foreach($autoorderplanResult as $autoValue){
                $repeatcontent = $autoValue['repeatcontent'];
                $operdate = $autoValue['operdate'];
                if($operdate == date('Y-m-d')){  //说明已经导入过
                    continue;
                }
                //当前星期,星期六或者星期天是否不送
                if($currentWeek == '周六'){
                    if($autoValue['satyday'] == 1){
                        continue;
                    }
                }
                if($currentWeek == '周日'){
                    if($autoValue['sunday'] == 1){
                        continue;
                    }
                }
                $repeatcontentArr = explode(',',$repeatcontent);
                if(in_array( $currentDateSql,$repeatcontentArr)){
                    $autoorderplanID = $autoValue['autoorderplanid'];
                    $this->saveOrder($autoorderplanID);
                }

            }




            //然后找出按周重复的订单
            $where = array();
            $where['repeattype'] = '按周重复';
            $where['startdate'] = array('ELT',date('Y-m-d'));
            $where['enddate'] = array('EGT',date('Y-m-d'));
            $autoorderplanResult = $autoorderplanModel->where($where)->select();
            foreach($autoorderplanResult as $autoValue){
                $repeatcontent = $autoValue['repeatcontent'];
                $operdate = $autoValue['operdate'];
                if($operdate == date('Y-m-d')){  //说明已经导入过
                    continue;
                }
                //当前星期,星期六或者星期天是否不送
                if($currentWeek == '周六'){
                    if($autoValue['satyday'] == 1){
                        continue;
                    }
                }
                if($currentWeek == '周日'){
                    if($autoValue['sunday'] == 1){
                        continue;
                    }
                }
                $repeatcontentArr = explode(',',$repeatcontent);
                if(in_array($currentWeek,$repeatcontentArr)){
                    $autoorderplanID = $autoValue['autoorderplanid'];
                    $this->saveOrder($autoorderplanID);
                }

            }

            /**
             * 清除无效的订单
             * 计算最后修改日期是一周前
             */
            $where = 'TO_DAYS(NOW()) - TO_DAYS(enddate) >= 7';
            $autoorderplanResult = $autoorderplanModel->where($where)->select();
            foreach ($autoorderplanResult as $autoorderValue) {
                $where = array();
                $where['autoorderplanid'] = $autoorderValue['autoorderplanid'];
                //没有预订日期,可以清除
                //清除抄单订单
                $autoorderplanModel->where($where)->delete();
                //清除预订产品
                $autoproductModel->where($where)->delete();
                //清除预订活动表
                $autoactivityModel->where($where)->delete();
                //清除预订支付表
                $autopaymentModel->where($where)->delete();
                //清除预订日志表
                $autoactionModel->where($where)->delete();
            }

            $info['status'] = 1;
            $info['info'] = ' 导出完毕!';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        //存入操作
        private function saveOrder($autoorderplanid){
            //抄单主表
            $autoorderplanModel = D('Autoorderplan');
            //抄单产品
            $autoproductModel = D('Autoproducts');
            //抄单活动表
            $autoactivityModel = D('Autoactivity');
            //抄单支付表
            $autopaymentModel = D('Autopayment');
            //抄单日志表
            $autoactionModel = D('Autoaction');

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
            $where['autoorderplanid'] = $autoorderplanid;
            $autoorderplanResult = $autoorderplanModel->where($where)->select();
            foreach ($autoorderplanResult as $orderValue) {

                $autoordeplanrid = $orderValue['autoorderplanid'];

                $ordersn = rand(1000, 9999) . date('Ymd') . $orderValue['autoorderplanid'];
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
                $data['state'] = '抄单';
                $data['invoiceheader'] = $orderValue['invoiceheader'];
                $data['gmf_dzdh'] = $orderValue['gmf_dzdh'];
                $data['gmf_nsrsbh'] = $orderValue['gmf_nsrsbh'];
                $data['invoicebody'] = $orderValue['invoicebody'];
                $data['invoicetype'] = $orderValue['invoicetype'];
                $data['shippingname'] = $orderValue['shippingname'];
                $data['shippingmoney'] = $orderValue['shippingmoney'];
                $data['domain'] = $this->getDomain();
                $data['lastdatetime'] = date('Y-m-d H:i:s');
                $orderformModel->create();
                $record = $orderformModel->add($data);

                $where = array();
                $where['autoorderplanid'] = $autoorderplanid;
                //取预订产品的内容
                $autoproductResult = $autoproductModel->where($where)->select();
                foreach ($autoproductResult as $productsValue) {
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
                $autoactivityResult = $autoactivityModel->where($where)->select();
                foreach ($autoactivityResult as $activityValue) {
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
                $autopaymentResult = $autopaymentModel->where($where)->select();
                foreach ($autopaymentResult as $paymentValue) {
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
                $action['action'] = $name . '将抄单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '导入订单表中';
                $action['logtime'] = date('Y-m-d H:i:s');
                $action ['domain'] = $this->getDomain();
                $orderactionModel->create();
                $result = $orderactionModel->add($action);

                // 写入到状态表中
                $orderstateModel = D('Orderstate');
                $data = array();
                $data ['create'] = 1;
                $data ['createtime'] = date('Y-m-d H:i:s');
                $data ['createcontent'] = $name . '导入抄单';
                $data ['orderformid'] = $record;
                $data ['ordersn'] = $ordersn;
                $data ['domain'] = $this->getDomain();
                $orderstateModel->create();
                $orderstateModel->add($data);


                //记入到预订的日至中
                //记入操作到action中
                $action = array();
                $action['autoorderplanid'] = $autoorderplanid;  //预订单号
                $action['action'] = ' '.date('H:i:s') . ' '. $name . '将抄单' . $orderValue['address'] . ' ' . $orderValue['ordertxt'] . '输入订单表中，订单号：' . $record;
                $action['logtime'] = date('Y-m-d H:i:s');
                $autoactionModel->create();
                $result = $autoactionModel->add($action);

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
                $where['autoorderplanid'] = $autoorderplanid;
                $data = array();
                $data['operdate'] = date('Y-m-d');
                $autoorderplanModel->where($where)->save($data);

            }




        }

}
?>
