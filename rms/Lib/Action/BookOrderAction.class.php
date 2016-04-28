<?php
    /**
    * 订单预订模块
    */
    class BookOrderAction extends ModuleAction{

        /**
         *   地址查询框
         */
        public function searchAddressInput()
        {
            $this->display('BookOrder/searchaddressinput');
        }

        // listview
        public function searchviewAddress() {
            if (IS_POST) {
                // 取得模块的名称
                $moduleName = $this->getActionName ();
                $this->assign ( 'moduleName', $moduleName ); // 模块名称

                // 启动当前模块的模型
                $focus = D ( $moduleName );

                // 生成list字段列表
                $listFields = $focus->listFields;
                // 模块的ID
                $moduleId = strtolower ( $focus->getPk () );

                // 建立查询条件
                $where = array ();
                $searchText = $_REQUEST ['searchText']; // 查询内容
                if (!empty ( $searchText )) {
                    $where ['address'] = array (
                        'like',
                        '%' . $searchText . '%'
                    );
                    $where ['_logic'] = 'and';
                }else{
                    $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                    if(!empty($searchText)) {
                        $searchText =  $_SESSION ['searchText' . $moduleName];
                        $where ['address'] = array (
                            'like',
                            '%' . $searchText . '%'
                        );
                        $where ['_logic'] = 'and';
                    }
                }


                $where ['domain'] = $_SERVER ['HTTP_HOST'];

                // 导入分页类
                import ( 'ORG.Util.Page' ); // 导入分页类
                $total = $focus->where ( $where )->count (); // 查询满足要求的总记录数

                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ( $pageNumber )) {
                    $pageNumber = 1;
                }


                // 查session取得page的值
                if (!empty ( $_SESSION [$moduleName . 'page'] )) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }


                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
                if (isset ( $listMaxRows )) {
                    $listMaxRows = 10;
                }

                // 取得页数
                $_GET ['p'] = $pageNumber;
                $Page = new Page ( $total, $listMaxRows );

                //保存页数
                $_SESSION [$moduleName . 'page'] = $pageNumber;

                // 查询模块的数据
                foreach($listFields as $key => $value) {
                    $selectFields[] = $key;
                }
                array_unshift ( $selectFields, $moduleId );

                $listResult = $focus->where ( $where )->field ( $selectFields )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();

                $orderHandleArray ['total'] = $total;
                if (count ( $listResult ) > 0) {
                    $orderHandleArray ['rows'] = $listResult;
                } else {
                    $orderHandleArray ['rows'] = array ();
                }
                $data = array('total'=>$total, 'rows'=>$listResult);
                $this->ajaxReturn($data);
            } else {
                // 取得模块的名称
                $moduleName = $this->getActionName ();
                $this->assign ( 'moduleName', $moduleName ); // 模块名称

                //是否清除session的内容
                $delSession = $_REQUEST['delsession'];
                if(isset($delSession)){
                    unset($_SESSION ['searchText' . $moduleName]);
                    unset($_SESSION [$moduleName . 'page']);
                }

                // 启动当前模块的模型
                $focus = D ( $moduleName );

                // 取得对应的导航名称
                $navName = $focus->getNavName ( $moduleName );
                $this->assign ( 'navName', $navName ); // 导航名称

                // 生成list字段列表
                $listFields = $focus->listFields;
                // 模块的ID
                $moduleId = $focus->getPk ();

                //是否有查询字段
                $searchText = $_REQUEST ['searchTextAddress']; // 查询内容
                if(!empty($searchText)){
                    $searchArray = array('searchText'=>$searchText);
                    $this->assign('searchIntroduce','查询内容:'.$searchText);
                    $_SESSION ['searchText' . $moduleName] = $searchText;
                }else{
                    $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                    if(!empty($searchText)) {
                        $searchArray = array('searchText'=>$searchText);
                        $this->assign('searchIntroduce','查询内容:'.$searchText);
                    }else {
                        $_SESSION ['searchText' . $moduleName] = '';
                    }
                }

                $datagrid = array (
                    'options' => array (
                        'url' => U ( $moduleName . '/searchviewAddress' ,$searchArray ),
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
                $datagrid ['fields'] ['操作'] = array (
                    'field' => 'id',
                    'width' => 20,
                    'align' => 'center',
                    'formatter' => $moduleName.'ListviewModule.operate'
                );
                $this->assign ( 'datagrid', $datagrid );
                $this->assign ( 'moduleId', $moduleId );

                // 执行list的一些其它数据的操作
                $this->listviewOther ();
                $this->assign('returnAction', 'searchviewAddress'); // 定义返回的方法
                $this->display ( $moduleName . '/listview' ); // 执行方法自身的列表
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
         * 综合查询页面
         */
        public function searchOtherInput(){
            // 返回当前的模块名
            $moduleName = $this->getActionName ();
            $this->assign ( 'moduleName', $moduleName );
            $this->display($moduleName.'/searchotherinput');
        }


        /**
        * 地址查询页面
        */
        public function searchviewForAddress(){

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //如果是从listview进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchText'.$moduleName.'Address']);
                unset($_SESSION['searchAp'.$moduleName.'Address']);
            }

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民
            $this->assign('operName','地址查询操作');


            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = strtolower($moduleName).'id';

            //建立查询条件
            $where = array();
            $searchText = $_REQUEST['searchTextAddress'];      //查询内容
            if(isset($searchText)){ 
                $where['address'] = array('like','%'.$searchText.'%');
                $this->assign('searchTextValue',$searchText);
                $_SESSION['searchText'.$moduleName.'Address'] = $searchText; 
            }else{
                if(isset($_SESSION['searchText'.$moduleName.'Address'])){
                    $where['Address'] = array('like','%'.$_SESSION['searchText'].$moduleName.'Address'.'%');
                    $this->assign('searchTextValue',$_SESSION['searchText'].$moduleName.'Address');
                }
            }


            //导入分页类
            import('ORG.Util.NewPage');// 导入分页类
            $total      = $focus->where($where)->count();// 查询满足要求的总记录数   
            //查session取得page的firstRos和listRows
            if(isset($_SESSION[$moduleName.'firstRowSearchview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowSearchview'];
            }
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            } 

            $Page = new NewPage($total,$listMaxRows);
            $show = $Page->show();

            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();
            //var_dump($focus->getLastSql());
            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出

            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId); 
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','searchviewForAddress');  //定义返回的方法

            $this->display('OrderForm/searchviewmenu');
            $this->display('OrderForm/searchviewoptionaddress');  //查询参数选择
            $this->display('OrderForm/searchviewlist');  //查询的结果显示
        }

        /**
        * 电话查询页面
        */
        public function searchviewForTelphone(){
            ///取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   


            //如果是从listview进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchTelphone'.$moduleName.'Telphone']);
                unset($_SESSION['searchAp'.$moduleName.'Telphone']);
            }

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民
            $this->assign('operName','电话查询操作');


            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = strtolower($moduleName).'id';

            //建立查询条件
            $where = array();
            //查询内容
            $searchTelphone = $_REQUEST['searchTelphone']; 
            if(isset($searchTelphone)){
                $where['telphone'] = array('like','%'.$searchTelphone.'%');
                $this->assign('searchTelphoneValue',$searchTelphone);
                $_SESSION['searchTelphone'.$moduelName.'Telphone'] = $searchTelphone; 
            }else{
                if(isset($_SESSION['searchTelphone'.$moduelName.'Telphone'])){
                    $where['telphone'] = array('like','%'.$_SESSION['searchTelphone'.$moduelName.'Telphone'].'%');
                    $this->assign('searchTelphoneValue',$_SESSION['searchTelphone'.$moduleName.'Telphone']);
                }
            }

            //查询的午别
            $searchAp = $_REQUEST['searchAp'];      
            if(isset($searchAp)){
                if($searchAp == '全天'){
                }else{
                    $where['ap'] = $searchAp;
                    $_SESSION['searchAp'.$moduleName.'Telphone'] = $searchAp;
                    $this->assign('searchApValue', $searchAp);
                }                
            }else{
                if(isset($_SESSION['searchAp'.$moduleName.'Telphone'])){
                    $where['ap'] = $_SESSION['searchAp'.$moduleName.'Telphone'];
                    $this->assign('searchApValue',$_SESSION['searchAp'.$moduleName.'Telphone']);
                }else{ //如果没有指定上午或者下午，那取当前时间的上午和下午
                    $this->assign('searchApValue',$this->getAp()); 
                }
            }

            //导入分页类
            import('ORG.Util.NewPage');// 导入分页类
            $total      = $focus->where($where)->count();// 查询满足要求的总记录数   
            //查session取得page的firstRos和listRows
            if(isset($_SESSION[$moduleName.'firstRowSearchview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowSearchview'];
            }
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            } 

            $Page = new NewPage($total,$listMaxRows);
            $show = $Page->show();

            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);

            $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();
            //var_dump($focus->getLastSql());
            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出

            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId); 
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','searchviewForTelphone');  //定义返回的方法

            $this->display('OrderForm/searchviewmenu');
            $this->display('OrderForm/searchviewoptiontelphone');  //查询参数选择
            $this->display('OrderForm/searchlistview');  //查询的结果显示
        }

        //退单的操作
        public function returnorderformview(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民

            //取得返回的是列表还是查询列表
            $returnAction = $_REQUEST['returnAction'];
            $this->assign('returnAction',$returnAction);

            //启动列表菜单
            if($this->detailviewMenuPath){
                $this->display('Module/detailviewmenu');     
            }else{
                $this->display($moduleName.'/detailviewmenu');  
            }       

            //模块的ID
            $moduleId = $focus->getPk();

            //退餐的描述信息
            $moduleReturnBlocks = F($moduleName.'ReturnBlocks');
            if(!empty($moduleReturnBlocks)){
                $this->returnBlocks = $moduleReturnBlocks;
            }else{
                //返回新建区块和字段
                $this->returnBlocks = $focus->returnBlocks();
                //缓存blocks
                F($moduleName.'ReturnBlocks',$this->returnBlocks);
            }


            //回调主程序需要的参数,比如下拉框的数据
            //$this->returnMainFnPara();

            //取得记录ID
            $record = $_REQUEST['record'];
            $where[$moduleId] = $record;

            //返回模块的行记录
            $result = $focus->where($where)->find();

            //返回区块
            $blocks = $focus->detailBlocks($result);

            $this->assign('blocks',$blocks);    
            $this->assign('record',$record); 

            //返回从表的内容
            $this->get_slave_table($record);

            $this->display('OrderForm/returnview');
        }

        //退餐操作
        public function returnOrderOperation(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得当前用户名
            $userName = $this->userInfo['truename'];

            //取得订单号
            $record = $_REQUEST['record'];
            $returnAction = $_REQUEST['returnAction']; //返回的路径

            //取得退餐的原因
            $cancelcontent = $_REQUEST['cancelcontent'];

            $where = array();
            $where['orderformid'] = $record;
            //对订单状态处理
            $data = array();
            $data['state'] = '已退餐';
            $focus->where($where)->save($data);  

            //写入订单状态表
            $orderStateModel = D('Orderstate');
            $data = array();
            $data['cancel'] = 1;
            $data['canceltime'] = date('Y-m-d H:i:s');
            $data['cancelcontent'] = $userName . ' ' .$cancelcontent;
            $orderStateModel->where($where)->save($data);

            //写入订单日志
            $orderActionModel = D('Orderaction');
            $data = array();
            $data['orderformid'] = $record;
            $data['action'] = $userName . '将订单退餐,原因：'.$cancelcontent;  //写入日志内容
            $data['logtime'] =  date('Y-m-d H:i:s');  //记入时间      
            $orderActionModel->add($data);

            $this->redirect('OrderForm/detailview',array('record'=>$record,'$returnAction'=> $returnAction ));

        }

        //恢复订单为订餐状态
        public function reneworder(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得当前用户名
            $userName = $this->userInfo['truename'];

            //取得订单号
            $record = $_REQUEST['record'];
            $returnAction = $_REQUEST['returnAction']; //返回的路径



            $where = array();
            $where['orderformid'] = $record;
            //取得原来订单的情况
            $orderformResult = $focus->where($where)->find();

            //对订单状态处理
            $data = array();
            $data['state'] = '订餐';
            $data['sendname'] = '';
            $focus->where($where)->save($data);  

            //写入订单状态表
            $orderStateModel = D('Orderstate');
            $data = array();
            $data['cancel'] = 0;
            $data['canceltime'] = date('Y-m-d H:i:s');
            $data['cancelcontent'] = $userName . '将订单恢复为订餐状态。'; 
            $data['handle']  = 0;
            $orderStateModel->where($where)->save($data);


            //写入订单日志
            $orderActionModel = D('Orderaction');
            $data = array();
            $data['orderformid'] = $record;
            $data['action'] = $userName . '将订单修改为订餐状态。';  //写入日志内容
            $data['logtime'] =  date('Y-m-d H:i:s');  //记入时间      
            $orderActionModel->add($data);

            $this->redirect('OrderForm/detailview',array('record'=>$record,'$returnAction'=> $returnAction ));

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

            $this->assign('orderproducts',$orderproducts);

            //查询送餐方式和送餐费的设置
            $this->assign('shippingname','分公司配送');
            $this->assign('shippingmoney',5);
            //发票内容
            $invoicecontent = array(
            array('name'=>'工作餐'),
            array('name'=>'盒饭'),
            array('name'=>'eee')
            );
            $this->assign('invoicecontent',$invoicecontent);


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
            $orderproductsModel = D('Bookproducts');
            $orderproducts = $orderproductsModel->field('bookorderid,code,name,price,number,money')->where("bookorderid=$record")->select();
            //echo $orderproducts_model->getLastSql();
            //dump($orderproducts);
            $this->assign('orderproducts',$orderproducts);   

            //取得订单日志
            $bookactionModel =D('Bookaction');
            $bookaction = $bookactionModel->where("bookorderid=$record")->select();
            $this->assign('orderaction',$bookaction);

            //单独取得订单金额 
            $orderform_model = D('Bookorder');
            $orderform = $orderform_model->field('totalmoney')->where("bookorderid=$record")->select();
            $totalmoney = $orderform[0]['totalmoney'];
            $this->assign('totalmoney',$totalmoney);  


            //取得预订日期
            $bookdateModel = D('Bookdate');
            $where = array();
            $where['bookorderid'] = $record;
            $this->bookdate = $bookdateModel->where($where)->select();  
        }


        //保存产品数据等其他数据
        function  save_slave_table($record){
            //订单号
            $moduleId = 'bookorderid';

            $bookproductsModel = D('Bookproducts');
            //先清掉数据
            $bookproductsModel->where("bookorderid=$record")->delete();

            $orderTxt = '';
            $totalmoney = 0;
            //保存地址的数量
            $productsLength = $_REQUEST['productsLength'];

            for($i=1;$i<= $productsLength;$i++){
                $code = $_REQUEST['productsCode_'.$i];
                $name = $_REQUEST['productsName_'.$i];
                $shortname = $_REQUEST['productsShortName_'.$i];
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
                $data['bookorderid'] = $record;
                if( !empty($name) and  !empty($number)){
                    $bookproductsModel->create();
                    $bookproductsModel->add($data);
                    $orderTxt .= $number . '×' . $shortname. ' ';
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
            $action['action'] = $name . ' 新建预订单 '.$_REQUEST['address'].' '.$orderTxt;
            $action['logtime'] = date('H:i:s');
            $bookactionModel->create();
            $result = $bookactionModel->add($action);
            //echo $orderaction_model->getLastSql();

            //保存预订日期
            $dateTxt = ''; 
            $bookdateModel = D('Bookdate');
            $bookdateArray = $_REQUEST['bookorderdate'];
            foreach($bookdateArray as $bookdateValue){
                $data = array();
                $data['bookorderid'] = $record;
                $data['bookdate'] = $bookdateValue;
                $data['logtime'] = date('Y-m-d H:i:s');
                $bookdateModel->create();
                $result = $bookdateModel->add($data);
                //var_dump($bookdateModel->getLastSql());
                $dateTxt .= $bookdateValue . ',';
            }

            //保存数量规格
            $date = array();
            $data['ordertxt'] = $orderTxt;
            $data['totalmoney'] = $totalmoney;
            $data['datetxt'] = $dateTxt;
            $bookorderModel = D('Bookorder');
            $result = $bookorderModel->where("$moduleId=$record")->save($data);
            //echo $bookorderModel->getLastSql();

        }

        //保存产品数据等其他数据
        function  update_slave_table($record){
            //订单号
            $moduleId = 'bookorderid';

            $bookproductsModel = D('bookproducts');
            //先清掉数据
            $bookproductsModel->where("bookorderid=$record")->delete();

            //保存地址的数量
            $productsLength = $_REQUEST['productsLength'];
            for($i=1;$i<= $productsLength;$i++){
                $code = $_REQUEST['productsCode_'.$i];
                $name = $_REQUEST['productsName_'.$i];
                $shortname = $_REQUEST['productsShortName_'.$i];
                $price = $_REQUEST['productsPrice_'.$i];
                $number = $_REQUEST['productsNumber_'.$i];
                $money = $_REQUEST['productsMoney_'.$i];                 
                $data['code'] = $code;
                $data['name'] = $name;
                $data['shortname'] = $shortname;
                $data['price'] = $price;
                $data['number'] = $number;
                $data['money'] = $money;
                $data['bookorderid'] = $record;
                if( !empty($name) and  !empty($number)){
                    $bookproductsModel->create();
                    $bookproductsModel->add($data);
                    $orderTxt .= $number . '×' . $shortname. ' ';
                    $totalmoney += $number * $money; 
                }   
            }  


            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['name'];
            //记入操作到action中
            $orderactionModel = D('Bookaction');
            $action['bookorderid'] = $record;  //订单号
            $action['action'] = $name . ' 改单 '.$_REQUEST['address'].' '.$orderTxt;
            $action['logtime'] = date('H:i:s');
            $orderactionModel->create();
            $result = $orderactionModel->add($action);
            //echo $orderaction_model->getLastSql();

            //保存预订日期
            $dateTxt = ''; 
            $bookdateModel = D('Bookdate');
            //删掉过去的记录
            $bookdateModel->where("bookorderid=$record")->delete();      
            $bookdateArray = $_REQUEST['bookorderdate'];
            foreach($bookdateArray as $bookdateValue){
                $data = array();
                $data['bookorderid'] = $record;
                $data['bookdate'] = $bookdateValue;
                $data['logtime'] = date('H:i:s');
                $bookdateModel->create();
                $result = $bookdateModel->add($data);
                $dateTxt .= $bookdateValue . ',';
            }

            //保存数量规格
            $date = array();
            $data['ordertxt'] = $orderTxt;
            $data['totalmoney'] = $totalmoney;
            $data['datetxt'] = $dateTxt;
            $bookorderModel = D('Bookorder');
            $result = $bookorderModel->where("$moduleId=$record")->save($data);
            //echo  $bookorderModel->getLastSql();
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


            $custtime = $_REQUEST['custtime_1']; //要餐时间
            if(empty($custtime)){
                $custtime = date('H:i:s',time()+30*60); //自动加半小时
            }else{
                $custtime = $_REQUEST['custtime_1'].':'.$_REQUEST['custtime_2'];
            }

            //设置午别
            $apTime = substr($custtime,0,2);
            $apTime = (int)$apTime;
            if($apTime > 15){
                $ap = '下午';
            }else{
                $ap = '上午';
            }

            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['truename'];
            $auto = array ( 
            	array('recdate',date('Y-m-d')),  //录入日期
            	array('custtime',$custtime),     //要餐时间
            	array('rectime',date('H:i:s')), // 对录入时间
            	array('telname',$name),   //接线员
            	array('ap',$ap),
            	array('invoiceheader',$_REQUEST['invoiceheader']),  //发票抬头
            	array('invoicebody',$_REQUEST['invoicecontent']),   //发票内容
            	array('state','订餐'),
            	array('domain',$_SERVER['HTTP_HOST'])		                
            );

            return $auto;

        }

        //更新，补充数据的回调函数
        public function autoParaUpdate(){
            $custtime = $_REQUEST['custtime_1']; //要餐时间
            if(empty($custtime)){
                $custtime = date('H:i:s',time()+30*60); //自动加半小时
            }else{
                $custtime = $_REQUEST['custtime_1'].':'.$_REQUEST['custtime_2'];
            }

            //设置午别
            $apTime = substr($custtime,0,2);
            $apTime = (int)$apTime;
            if($apTime > 15){
                $ap = '下午';
            }else{
                $ap = '上午';
            }
            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['truename'];
            //取得订单未修改前的状态
            $stateBefore = $_REQUEST['state'];

            //var_dump(strstr($stateBefore,"已"));
            if($stateBefore == '订餐'){                              
                $state = "订餐";
            }else{            
                $state = "改单";
            }

            $auto = array ( 
            	array('telname',$name),   //接线员
            	array('ap',$ap),
            	array('handlestate',0),   //处理状态为0
            	array('distributionstate',0),   //分配状态为0
            	array('state',$state),
            	array('domain',$_SERVER['HTTP_HOST'])
            );

            return $auto;
        }

    }
?>
