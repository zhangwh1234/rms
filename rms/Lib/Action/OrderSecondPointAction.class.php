<?php
    /**
    * 分送点管理模块，是分公司（配送店）俗称的输血点
    */
    class OrderSecondPointAction extends ModuleAction{
        //定义编辑页面的菜单的路径
        var $detailviewMenuPath = false;
        //定义查看页面的路径
        var $detailviewPath = false;

        //列表
        public function listview(){   

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName',$navName);         //导航名称

            //启动列表菜单            
            $this->display('OrderSecondPoint/listviewmenu');
            $this->display('OrderSecondPoint/listview');

        }

        /**
        * 地址查询条件页面
        */
        public function searchviewForAddress(){

            //如果是从listviewMenu进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchAddressOrderHandle']);
                unset($_SESSION['searchApOrderHandleAddress']);
            }

            //取得模块的名称
            $moduleName =  $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称 
            $this->assign('actionName','searchviewForAddress');            //输出程序名称  

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民

            $this->assign('operName','地址查询操作');

            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = $focus->getPk();

            //加入模块id到listHeader中
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','searchviewForAddress');  //定义返回的方法

            //建立查询条件
            $where = array();
            $searchAddress = $_REQUEST['searchAddress'];  //查询地址
            if(isset($searchAddress)){
                $where['address'] = array('like','%'.$searchAddress.'%');
                $this->assign('searchAddress',$searchAddress);
                $_SESSION['searchAddressOrderHandle'] = $searchAddress;
            }else{
                if(isset($_SESSION['searchAddressOrderHandle'])){
                    $where['address'] = array('like','%'.$_SESSION['searchAddressOrderHandle'].'%');
                    $where['ap'] = $_SESSION['searchApOrderHandle'];
                    $this->assign('searchAddress',$_SESSION['searchAddressOrderHandle']);
                }
            }

            //查询的午别
            $searchAp = $_REQUEST['searchAp'];     
            if(isset($searchAp)){
                if($searchAp == '全天'){
                }else{
                    $where['ap'] = $searchAp;
                    $_SESSION['searchApOrderHandleAddress'] = $searchAp; 
                    $this->assign('searchAp', $searchAp);
                }                
            }else{
                if(isset($_SESSION['searchApOrderHandleAddress'])){
                    $where['ap'] = $_SESSION['searchApOrderHandleAddress'];
                    $this->assign('searchAp',$_SESSION['searchApOrderHandleAddress']);
                }else{ //如果没有指定上午或者下午，那取当前时间的上午和下午
                    $this->assign('searchAp',$this->getAp()); 
                }
            }

            //获取分公司
            $company = $this->userInfo['department']; 
            $where['company'] = $company;

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

            $this->display('OrderSecondPoint/searchviewmenu');
            $this->display('OrderSecondPoint/searchviewoptionaddress');  //查询参数选择
            $this->display('OrderSecondPoint/searchviewlist');  //查询的结果显示
        }

        /**
        *  送餐员条件查询
        */
        public function searchviewForSendname(){
            //如果是从listviewMenu进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchSendnameOrderHandle']);
                unset($_SESSION['searchApOrderHandleSendname']);
            }

            //取得模块的名称
            $moduleName =  $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称  
            $this->assign('actionName','searchviewForSendname');            //输出程序名称  

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);         //导航民

            $this->assign('operName','送餐员查询操作');

            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = $focus->getPk();
            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId); 
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','searchviewForSendname');  //定义返回的方法

            //建立查询条件
            $where = array();
            $searchSendname = $_REQUEST['searchSendname'];  //查询的送餐员
            if(isset($searchSendname)){
                $where['sendname'] = $searchSendname;
                $this->assign('searchSendname',$searchSendname);
                $_SESSION['searchSendnameOrderHandle'] = $searchSendname;
            }else{
                if(isset($_SESSION['searchSendnameOrderHandle'],$_SESSION['searchApOrderHandle'])){
                    $where['Sendname'] = $_SESSION['searchSendnameOrderHandle'];
                    $this->assign('searchSendname',$_SESSION['searchSendnameOrderHandle']);
                }
            }

            $this->assign('searchSendnameCode',$_REQUEST['searchSendnameCode']);  //返回送餐代码

            //查询的午别
            $searchAp = $_REQUEST['searchAp'];     
            if(isset($searchAp)){
                if($searchAp == '全天'){
                }else{
                    $where['ap'] = $searchAp;
                    $_SESSION['searchApOrderHandleSendname'] = $searchAp; 
                    $this->assign('searchAp', $searchAp);
                }                
            }else{
                if(isset($_SESSION['searchApOrderHandleSendname'])){
                    $where['ap'] = $_SESSION['searchApOrderHandleSendname'];
                    $this->assign('searchAp',$_SESSION['searchApOrderHandleSendname']);
                }else{ //如果没有指定上午或者下午，那取当前时间的上午和下午
                    $this->assign('searchAp',$this->getAp()); 
                }
            }


            //获取分公司
            $company = $this->userInfo['department']; 
            $where['company'] = $company;

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
                $listMaxRows = 100;
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

            $this->display('OrderSecondPoint/searchviewmenu');
            $this->display('OrderSecondPoint/searchviewoptionsendname');  //查询参数选择
            $this->getSendnameproductsByName();  //显示送餐员餐数情况 
            // $this->display('OrderHandle/searchviewlist');  //查询的结果显示
        }

        /**
        * 其他条件查询
        */
        public function searchviewForOther(){

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //如果是从listview进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchOption'.$moduleName]);
                unset($_SESSION['searchText'.$moduleName]);
            }

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $tabName = $focus->getTabName($moduleName);
            $this->assign('tabName',$tabName);
            $this->assign('operName','其他查询操作');  


            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = $focus->getPk();

            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId); 
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','searchview');  //定义返回的方法



            //建立查询条件
            $where = array();
            $searchText = $_REQUEST['searchText'];      //查询内容
            foreach($focus->searchFields as $value){
                $where[$value] =  array('like','%'.$searchText.'%');                       
            }
            $where['_logic'] = 'OR';
            $this->assign('searchTextValue',$searchText);

            //获取分公司
            $company = $this->userInfo['department']; 
            $map['_complex'] = $where;
            $map['company'] = $company;

            //导入分页类
            import('ORG.Util.Page');// 导入分页类
            $total      = $focus->where($map)->count();// 查询满足要求的总记录数   
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

            $Page = new Page($total,$listMaxRows);
            $show = $Page->show();

            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);
            $listResult = $focus->field($selectFields)->where($map)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();
            //var_dump($listResult);
            //var_dump($focus->getLastSql());
            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出

            $searchOption = $focus->searchFields;           
            $this->assign('searchOption',$searchOption);
            $this->assign('returnAction','searchview');  //定义返回的方法

            $this->display('OrderSecondPoint/searchviewmenu');
            $this->display('OrderSecondPoint/searchviewoptionother');  //查询参数选择
            $this->display('OrderHandle/searchviewlist');  //查询的结果显示
        }


        //返回ajax 所有的订单
        public function alllistjson(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = strtolower($focus->getPk());


            //取得显示页数
            $pageNumber = $_REQUEST['page'];
            if(empty($pageNumber)){
                $pageNumber = 1;
            }

            //配送店（分公司）的信息
            //分公司的名称
            $company = $this->userInfo['department'];
            $where = array();
            $where['state'] = array('notlike','已%');
            $where['company'] = $company;
            $where['_string'] = "length(trim(company)) > 0";

            //导入分页类
            import('ORG.Util.Page');// 导入分页类
            $total      = $focus->where($where)->count();// 查询满足要求的总记录数   
            //log::write($focus->getLastSql(),'info');

            //查session取得page的firstRos和listRows
            if(isset($_SESSION[$moduleName.'firstRowlistview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowlistview'];
            }
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            } 
            //取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total,$listMaxRows);

            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);

            $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow.','.$Page->listRows)->select();
            //log::write($focus->getLastSql(),'info');

            $orderHandleArray['total'] = $total;
            if(count($listResult) > 0){
                $orderHandleArray['rows'] = $listResult;
            }else{
                $orderHandleArray['rows']= array();
            }

            $this->ajaxReturn($orderHandleArray,'JSON');

        }



        /**根据输入的编码处理订单
        *  code 输入的编码
        */
        public function orderHandleByCode(){

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];   

            //获得处理过了的编码
            $code = $_REQUEST['code'];
            //订单号
            $record = $_REQUEST['orderformid'];

            //定义返回的数组
            $returnInfo = array();

            /** 先编辑送餐员的编码 ***/
            //根据编码取得送餐员姓名
            $sendnameMgrModel = D('Sendnamemgr');
            $where = array();
            $where['code'] = $code; //送餐员的编号
            $where['company'] = $company;
            $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($where)->find();
            //var_dump($sendnameMgrModel->getLastSql());
            //var_dump($sendnameResult);
            if($sendnameResult){
                $sendname = $sendnameResult['name'];
                $telphone = $sendnameResult['telphone'];
                $sendtype = $sendnameResult['sendtype'];
            }else{
                $returnInfo['error'] = 'error';
                $returnInfo['msg']  = '没有查到信息';
                $this->ajaxReturn($returnInfo);
            }
            //查询订单的本身状态
            $data = array();
            $where = array();
            $where['orderformid'] = $record;
            $orderformResult = $focus->field('state')->where($where)->find();
            if(!empty($orderformResult)){
                if($orderformResult['state'] == '退餐'){  //立即返回
                    $data['state'] = '已退餐';
                }elseif($orderformResult['state'] == '已退餐'){
                    $data['state'] = '已退餐';
                }elseif($orderformResult['state'] == '废单'){
                    $data['state'] = '废单';
                }elseif($orderformResult['state'] == '已作废'){
                    $data['state'] = '已作废';
                }else{
                    $data['state'] = '已处理'; 
                }
            }else{
                $data['state'] = '已处理';
            }
            //根据送餐员信息，处理订单
            $orderformid = $_REQUEST['orderformid'];
            $data['sendname'] = $sendname;
            $state = $data['state'];
            $focus->where($where)->save($data);
            //var_dump($focus->getLastSql());

            //输入短信表中，如果要发短信的话
            if(!empty($telphone) and (strlen($telphone) == 11)){
                $orderform = $focus->where("orderformid='$record'")->find();
                // echo $focus->getLastSql();
                $smsString = $orderform['address'].'|'.$orderform['ordertxt'].'|'.$orderform['custtime'].'|'.$orderform['telphone'];
                //计算已经发生的信息量
                $smsmgr_model = D('Smsmgr');
                $smscount = $smsmgr_model->where("telphone='$telphone'")->count();
                if($smscount){
                    $smscount += 1;
                }else{
                    $smscount = 1;
                }
                $smsString = $smscount.'|'.$smsString;
                $smsData = array();
                $smsData['telphone'] = $telphone;
                $smsData['content'] = $smsString;
                $smsData['firstdate'] = date('Y-m-d H:i:s');
                $smsData['company'] = $company;
                $smsData['sendtype'] = '短信';
                $smsData['sendname'] = $sendname;
                $smsData['orderformid'] = $orderformid;
                //var_dump($smsData);
                //$smsmgr_model->create();
                $smsmgr_model->add($smsData);
                //echo $smsmgr_model->getLastSql();
            }

            //同时写入日志中
            //记入操作到action中
            $orderactionModel = D('Orderaction');
            $orderactionData['orderformid'] = $orderformid;  //订单号
            $company = $data['company'];
            $orderactionData['action'] = "订单配送给".$sendname."送餐员";
            $orderactionData['logtime'] = date('H:i:s');
            //$orderactionModel->create();
            $result = $orderactionModel->add($orderactionData);


            //写入到状态表中
            $orderstateModel = D('Orderstate');
            $data = array();
            $data['handle'] = 1;
            $data['handletime'] = date('Y-m-d H:i:s');
            $data['handlecontent'] = $sendname.' '.$telphone;
            $where = array();
            $where['orderformid'] =$orderformid;
            $orderstateModel->where($where)->save($data);

            //取得分公司名称
            $company = $this->userInfo['department'];
            //保存到送餐员餐售情况
            $sendnameproductsModel = D('Sendnameproducts');
            $where = array();
            $where['extid'] = $orderformid;
            $sendnameproductsModel->where($where)->delete();
            //查询订货
            $orderproductsModel = D('Orderproducts');
            $where = array();
            $where['orderformid'] = $orderformid;
            $orderproductsResult = $orderproductsModel->where($where)->select();

            foreach($orderproductsResult as $productsValue){
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
                $data['sendname'] = $sendname; //送餐员
                $data['company'] = $company;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $sendnameproductsModel->create();
                $sendnameproductsModel->add($data);
            } 

            //定义返回
            $returnInfo['success'] = 'success';
            $orderformData['sendname'] = $sendname;
            $orderformData['state'] = $state;
            $returnInfo['data'] = $orderformData;
            $this->ajaxReturn($returnInfo,'JSON');


        } 

        //1,F10处理订单的更改或者催单
        public function changehurryOrderHandle(){

            //取得模块的名称
            $currentModule = $this->getActionName();
            //var_dump($currentModule);
            //启动当前模块
            $focus = D($currentModule);



            $orderformid = $_REQUEST['orderformid'];

            $data['state'] = '已处理';
            //$data['orderformid'] = 8;
            $focus->where("orderformid=$orderformid")->save($data);
            //echo $focus->getLastSql();
            //var_dump($telphone);


            $this->ajaxReturn($data,'JSON');


        } 

        //显示产品明细等
        public function showproducts(){
            //取得记录号
            $record = $_REQUEST['orderformid'];

            //取得模块的名称
            $currentModule = $this->getActionName();
            //var_dump($currentModule);
            //启动当前模块
            $focus = D($currentModule);

            //取得订单信息
            $orderform = $focus->where("orderformid=$record")->find();
            //dump($orderform);
            $orderproducts_model = D('Orderproducts');
            //取得订单产品信息
            $orderproducts = $orderproducts_model->where("orderformid=$record")->select();
            //echo $orderproducts_model->getLastSql();

            $this->ajaxReturn($orderproducts,'JSON');

            $this->assign('orderform',$orderform);
            $this->assign('orderproducts',$orderproducts);
            // echo $this->fetch('OrderHandle/productsDetail');
            //echo $this->fetch('./Tpl/OrderHandle/getuser.html');
        }


        /*  查询 */
        public function addresssearchview(){

            $this->display('OrderDistribution/addressSearchView');
        }

        /*取得打印需要的数据*/
        function getPrintOrder(){
            //取得订单号
            $record = $_REQUEST['orderformid'];
            //查询订单
            $orderformModel = D('Orderform');
            $orderform = $orderformModel->where("orderformid=$record")->find();
            //查询订货
            $orderproductsModel = D('Orderproducts');
            $orderproducts = $orderproductsModel->where("orderformid=$record")->select();
            //echo $orderproducts_model->getLastSql();
            $order['orderform'] = $orderform;
            $order['orderproducts'] = $orderproducts;

            $this->ajaxReturn($order,'JSON');

        }

        /* 设定订单已打印状态*/
        function setOrderPrinted(){
            //取得订单号
            $record = $_REQUEST['orderformid'];
            //查询订单
            $orderformModel = D('Orderform');
            //查询订单状态，如果是退餐或者是废单，是不能改变订单状态的
            $data = array();
            $where = array();
            $where['orderformid'] = $record;
            $orderformResult = $orderformModel->field('state')->where($where)->find();
            if(!empty($orderformResult)){
                if($orderformResult['state'] == '退餐'){  //立即返回
                    $data['state'] = '已退餐';
                }elseif($orderformResult['state'] == '已退餐'){
                    $data['state'] = '已退餐';
                }elseif($orderformResult['state'] == '废单'){
                    $data['state'] = '废单';
                }elseif($orderformResult['state'] == '已作废'){
                    $data['state'] = '已作废';
                }else{
                    $data['state'] = '已打印'; 
                }
            }else{
                $data['state'] = '已打印';
            }
            $result  = $orderformModel->where($where)->save($data);

            //同时写入日志中
            //记入操作到action中
            $orderactionModel = D('Orderaction');
            $action['orderformid'] = $record;  //订单号
            $company = $this->userInfo['department'];
            $action['action'] = $company."打印订单";
            $action['logtime'] = date('H:i:s');
            $orderactionModel->create();
            $result = $orderactionModel->add($action);

        }

        //设置打印机类型
        public function setprintupdateview(){
            //返回当前的模块名
            $currentModule = $this->getActionName();
            //var_dump($currentModule);
            $focus = D($currentModule);



            //引入模块菜单
            $Modulemenu = A('ModuleMenu');
            $Modulemenu->index($this->getActionName(),'createview');

            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['company'];

            $companymgr_model = D('Companymgr');
            $printtype = $companymgr_model->field('printtype')->where("name='$name'")->find();
            //dump($printtype);

            $this->assign('printtype',$printtype['printtype']);  //指定字段获得焦点

            //dump($this->blocks);
            $this->display('./Tpl/OrderSecondPoint/setprintupdateview.html');

        }


        //保存打印设置
        public function saveSetPrint(){

            //打印类型
            $printtype = $_REQUEST['printtypesetup'];
            //var_dump($printtype);

            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['company'];

            $companymgr_model = D('Companymgr');
            $data['printtype'] = $printtype;
            $result = $companymgr_model->where("name='$name'")->save($data);
            //echo $companymgr_model->getLastSql();

            //保存成功 
            //$this->redirect("OrderHandle/setprintdetailview", array(), 0, '页面跳转中...');
            $returnArr = array();
            $this->ajaxReturn($returnArr,'JSON');
        }

        //显示打印设置的保存结果
        public function setprintdetailview(){
            //返回当前的模块名
            $currentModule = $this->getActionName();
            //var_dump($currentModule);
            $focus = D($currentModule);



            //引入模块菜单
            $Modulemenu = A('ModuleMenu');
            $Modulemenu->index($this->getActionName(),'createview');

            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['company'];

            $companymgr_model = D('Companymgr');
            $printtype = $companymgr_model->field('printtype')->where("name='$name'")->find();
            //dump($printtype);
            $this->assign('printtype',$printtype['printtype']);
            $this->display();


        }


        /* 返回分公司订单的情况 */
        function getOrderMonit(){
            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $company = $userInfo['department'];

            $where = array();
            $where['name'] = $company;
            $ordermonit_model = D('Ordermonit');
            $ordermonit = $ordermonit_model->where($where)->select();
            $this->ajaxReturn($ordermonit,'JSON');
        }



        //根据代码获取送餐员名字
        public function getSendnameByCode(){
            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $company = $userInfo['company'];

            //获得处理过了的编码
            $code = $_REQUEST['code'];

            //定义返回的数组
            $returnInfo = array();

            /** 先编辑送餐员的编码 ***/
            //根据编码取得送餐员姓名
            $sendnameMgrModel = D('Sendnamemgr');
            $sendnameWhere['code'] = $code; //送餐员的编号
            //$sendnameWhere['company'] = $company;
            $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($sendnameWhere)->find();
            //var_dump($sendnameMgrModel->getLastSql());
            if($sendnameResult){
                $sendname = $sendnameResult['name'];
                $telphone = $sendnameResult['telphone'];
            }else{
                $returnInfo['error'] = 'error';
                $returnInfo['msg']  = '没有查到信息';
                $this->ajaxReturn($returnInfo);
            }
            //根据送餐员信息，处理订单
            $orderformData['sendname'] = $sendname;


            //定义返回
            $returnInfo['success'] = 'success';
            $returnInfo['data'] = $orderformData;
            $this->ajaxReturn($returnInfo,'JSON');


        }

        //返回所有送餐员的名称和代码
        function getSendnameMgr(){
            $sendnameMgrModel = D('Sendnamemgr');
            $sendnameMgrResult = $sendnameMgrModel->field("code,name,telphone")->select();
            $this->ajaxReturn($sendnameMgrResult,'JSON');
        }

        //处理订单的退餐，改为已退餐
        //处理改单或者催单
        public function cancelchangehurryOrderHandle(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);
            //输入代码
            $inputKey = $_REQUEST['code'];
            //订单号
            $record = $_REQUEST['orderformid'];

            //查询当前的订单状态
            $where = array();
            $where['orderformid'] = $record;
            $orderformResult = $focus->field('state,sendname')->where($where)->find();
            if(!empty($orderformResult)){
                if($orderformResult['state'] == '退餐'){
                    $data = array();
                    $data['state'] = '已退餐';
                    $focus->where($where)->save($data);

                    //同时写入日志中
                    //记入操作到action中
                    $orderactionModel = D('Orderaction');
                    $orderactionData['orderformid'] = $record;  //订单号
                    $company = $this->userInfo['department'];
                    $orderactionData['action'] = $company."将订单处理成已退餐";
                    $orderactionData['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($orderactionData);


                    //写入到状态表中
                    $orderstateModel = D('Orderstate');
                    $data = array();
                    $data['cancel'] = 1;
                    $data['canceltime'] = date('Y-m-d H:i:s');
                    $data['cancelcontent'] = $company.'处理成已退餐';
                    $where = array();
                    $where['orderformid'] =$record;
                    $orderstateModel->where($where)->save($data);

                    //返回成功
                    $returnInfo['success'] = 'success';
                    $returnInfo['state'] = '已退餐';
                    $this->ajaxReturn($returnInfo,'JSON');
                }

                if($orderformResult['state'] == '改单'){
                    if($orderformResult['sendname'] == ''){
                        //返回成功
                        $returnInfo['error'] = 'error';
                        $returnInfo['msg'] = '订单还没有配给送餐员,改单无法处理！';
                        $this->ajaxReturn($returnInfo,'JSON');
                    }
                    $data = array();
                    $data['state'] = '已更改';
                    $focus->where($where)->save($data);

                    //同时写入日志中
                    //记入操作到action中
                    $orderactionModel = D('Orderaction');
                    $orderactionData['orderformid'] = $record;  //订单号
                    $company = $this->userInfo['department'];
                    $orderactionData['action'] = $company."将订单处理成已更改";
                    $orderactionData['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($orderactionData);

                    //返回成功
                    $returnInfo['success'] = 'success';
                    $returnInfo['state'] = '已更改';
                    $this->ajaxReturn($returnInfo,'JSON');
                }

                if($orderformResult['state'] == '催送'){
                    if($orderformResult['sendname'] == ''){
                        //返回成功
                        $returnInfo['error'] = 'error';
                        $returnInfo['msg'] = '订单还没有配送送餐员,无法处理催送！';
                        $this->ajaxReturn($returnInfo,'JSON');
                    }
                    $data = array();
                    $data['state'] = '已催送';
                    $focus->where($where)->save($data);

                    //同时写入日志中
                    //记入操作到action中
                    $orderactionModel = D('Orderaction');
                    $orderactionData['orderformid'] = $record;  //订单号
                    $company = $this->userInfo['department'];
                    $orderactionData['action'] = $company."将订单处理成已催送";
                    $orderactionData['logtime'] = date('H:i:s');
                    $orderactionModel->create();
                    $result = $orderactionModel->add($orderactionData);

                    //返回成功
                    $returnInfo['success'] = 'success';
                    $returnInfo['state'] = '已催送';
                    $this->ajaxReturn($returnInfo,'JSON');
                }


                $returnInfo['error'] = 'error';
                $returnInfo['msg'] = '订单无法处理';
                $this->ajaxReturn($returnInfo,'JSON');

            }

            //返回错误
            $returnInfo['error'] = 'error';
            $returnInfo['msg'] = '订单不存在';
            $this->ajaxReturn($returnInfo,'JSON');
        }

        //订单返回的操作
        public function backOrderHandle(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);
            //输入代码
            $inputKey = $_REQUEST['code'];
            //订单号
            $record = $_REQUEST['orderformid'];

            //查询返回订单
            $where = array();
            $where['orderformid'] = $record;
            $data = array();
            $data['company'] = '';
            $data['sendname'] = '';
            $result = $focus->where($where)->save($data);
            if($result == false){
                //更新失败
                $returnInfo['success'] = 'error';
                $returnInfo['msg'] = '返回失败';
                $this->ajaxReturn($returnInfo,'JSON');  
            }

            //同时写入日志中
            //记入操作到action中
            $orderactionModel = D('Orderaction');
            $orderactionData['orderformid'] = $record;  //订单号
            $company = $this->userInfo['department'];
            $orderactionData['action'] = $company."将订单返回";
            $orderactionData['logtime'] = date('H:i:s');
            $orderactionModel->create();
            $result = $orderactionModel->add($orderactionData);

            //返回成功
            $returnInfo['success'] = 'success';
            $this->ajaxReturn($returnInfo,'JSON');
        } 

        //根据送餐员，显示送餐员的送餐情况
        public function getSendnameproductsByName(){

            $sendname =$_REQUEST['searchSendname']; //送餐员姓名
            $userInfo = $this->userInfo;
            $company = $userInfo['department'];

            //产品表
            $sendnameproductsModel = D('Sendnameproducts');
            $where = array();
            $where['sendname'] = $sendname;
            $where['company'] = $company;
            //定义统计表
            $tongji = array();
            $productsResult = $sendnameproductsModel->Distinct(True)->field('productsname,shortname')->where($where)->select();

            //查询装箱
            $listHeader = array();
            foreach($productsResult as $value){
                $tongji['装箱'][$value['shortname']] = 0;
                $tongji['已送'][$value['shortname']] = 0;
                $tongji['剩余'][$value['shortname']] = 0;
                $listHeader[] = $value['shortname'];
            }
            $this->sendnameProductsListHeader = $listHeader;

            //查询
            $where = array();
            $where['sendname'] = $sendname;
            $where['company']  = $company;
            $sendnameProductsResult = $sendnameproductsModel->where($where)->select();
            foreach($sendnameProductsResult as $key => $value){             
                if($value['type'] == '装箱'){
                    $tongji['装箱'][$value['shortname']] += $value['number'];
                }
                if($value['type'] == '已送'){
                    $tongji['已送'][$value['shortname']] += $value['number'];
                }
            }


            //计算剩余
            foreach($listHeader as $value){
                $tongji['剩余'][$value] = $tongji['装箱'][$value] - $tongji['已送'][$value]; 
            }

            $this->sendnameProductsTongji = $tongji;
            $this->display('OrderSecondPoint/sendnameproductsresult');
        }

    }


?>
