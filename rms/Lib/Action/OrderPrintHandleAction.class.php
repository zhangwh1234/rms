<?php
    /**
    * 订单打印后，派给送餐员，同时输入系统中，让系统知道已经派给送餐员了
    * 2013-12-30开始编制
    */

    class OrderPrintHandleAction extends ModuleAction{

        /**
         * 返回listview的查询条件
         */
        public function returnWhere(&$where){
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];
            //查询条件
            $where['company'] = $company;
            $where['domain'] = $_SERVER['HTTP_HOST'];
        }

        /* 启动项目是输入送餐员和订单号  */
        public function index(){
            $this->createview();
        }


        //返回一些其他的数据,比如下拉列表框等的数据
        public function returnMainFnPara(){

        }

        //根据代码获取送餐员名字
        public function getSendnameByCode(){

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
            $where['domain'] = $_SERVER['HTTP_HOST'];

            $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($where)->find();

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

        //根据订单号码，取得订单详情
        public function getOrderTxtByid(){
            //打印号
            $orderPrintNumber =  $_REQUEST['printNumber'];
            $where = array();
            $where['printnumber'] = $orderPrintNumber;
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];
            //查询条件
            $where['company'] = $company;
            $where['domain'] = $_SERVER['HTTP_HOST'];
            //打印表
            $orderprinterModel = D('OrderPrinter');
            $orderprinter = $orderprinterModel->where($where)->find();
            if($orderprinter){
                $ordersn = trim($orderprinter['ordersn']);
                //订单表
                $orderformModel = D('Orderform');
                $where = array();
                $where['ordersn'] = $ordersn;
                $orderformResult = $orderformModel->where($where)->find();

                if(!empty($orderformResult)){
                    $address = $orderformResult['address'] . $orderformResult['clientname'];
                    $ordertxt = $orderformResult['ordertxt'];
                    $returnData['addressOrdertxt'] = $address. ' '.$ordertxt . ' ' .
                                                    $orderformResult['custtime'];
                    $returnData['ordersn'] = $ordersn;
                    //判断这个订单是否已经派发
                    if(!empty($orderformResult['sendname'])){
                        $returnData['success'] = 'repeat';
                        $returnData['addressOrdertxt'] .=  " ★☆已经派发给:" . $orderformResult['sendname'] ."☆★";
                        $this->ajaxReturn($returnData,'JSON');
                    }else{
                        $returnData['success'] = 'success';
                        $this->ajaxReturn($returnData,'JSON');
                    }
                }else{
                    $returnData['error'] = 'error';
                    $this->ajaxReturn($returnData,'JSON');

                }
            }else{
                $returnData['error'] = 'error';
                $this->ajaxReturn($returnData,'JSON');

            }

        }

        //保存数据等其他数据
        function  save_slave_table($record){
            //打印表
            $orderprintcontentModel = D('Orderprintcontent');
            //订单表
            $orderformModel = D('Orderform');
            //状态表中
            $orderstateModel = D('Orderstate');
            //日志表
            $orderactionModel = D('Orderaction');
            //通知表
            $notifyclientModel = D('NotifyClient');

            //删除以前的记录 
            $where = array();
            $where['orderprinthandleid'] = $record;
            $orderprintcontentModel->where($where)->delete();

            //内容保存在打印明细表
            $printNumberTxt = '';
            $orderPrintHandleLength = $_REQUEST['orderPrintHandleLength'];
            for($i=1;$i<= $orderPrintHandleLength;$i++){
                $printNumber =  $_REQUEST['orderPrintHandleid_'.$i];
                if(!empty($printNumber)){
                    //订单号
                    $ordersn =  $_REQUEST['orderPrintOrdersn_'.$i];

                    // 获得订单内容
                    $where = array();
                    $where ['ordersn'] = $ordersn;
                    $orderformResult = $orderformModel->where($where)->find();

                    $content =  $orderformResult['address'] . $orderformResult['clientname']
                                . $orderformResult['ordertxt'];

                    $data = array();
                    $data['orderprinthandleid'] = $record;
                    $data['ordersn'] = $_REQUEST['orderPrintOrdersn_'.$i];
                    $data['printnumber'] = $printNumber;
                    $data['content'] = $content;
                    $data ['domain'] = $_SERVER['HTTP_HOST'];
                    if(!empty($ordersn)){
                        $orderprintcontentModel->create();
                        $orderprintcontentModel->add($data);
                        $printNumberTxt .= $printNumber.',';

                        //写入到状态表中
                        $data = array();
                        $data ['handle'] = 1;
                        $data ['handletime'] = date('Y-m-d H:i:s');
                        $data ['handlecontent'] = $_REQUEST['name'] .'配送单发';
                        $where = array();
                        $where['orderformid'] = $orderformResult['orderformid'];
                        $where ['ordersn'] = $ordersn;
                        $orderstateModel->where($where)->save($data);

                        // 同时写入日志中
                        $data = array();
                        $data ['orderformid'] = $orderformResult['orderformid']; // 订单号
                        $data ['ordersn'] = $ordersn;
                        $data ['action'] = "订单给" . $_REQUEST['name'] . "单发";
                        $data ['logtime'] = date('H:i:s');
                        $data ['domain'] = $_SERVER ['HTTP_HOST'];
                        $orderactionModel->create();
                        $result = $orderactionModel->add($data);

                        //通知客户的消息, 如果是微信或者推送
                        if(!empty($orderformResult['app_tk'])){
                            $data = array();
                            $data['ordersn'] = $ordersn;
                            $data['app_tk'] = $orderformResult['app_tk'];
                            $data['contenttype'] = 'sendname';
                            $data['origin'] = 'APP';
                            $data['domain'] = $_SERVER['HTTP_HOST'];
                            $notifyclientModel->create();
                            $notifyclientModel->add($data);
                        }else{
                            //那么如果是手机号码
                            if(preg_match("/^1[34578]\d{9}$/", $orderformResult['telphone'])) {
                                $data = array();
                                $data['ordersn'] = $ordersn;
                                $data['telphone'] = '';
                                $data['contenttype'] = 'sendname';
                                $data['origin'] = '电话';
                                $data['domain'] = $_SERVER['HTTP_HOST'];
                                $notifyclientModel = D('NotifyClient');
                                $notifyclientModel->create();
                                $notifyclientModel->add($data);
                            }
                        }

                        //写入订单表中,标志为单发
                        $where = array();
                        $where['ordersn'] = $ordersn;
                        $data = array();
                        $data['sendname'] = $_REQUEST['name'];
                        $data['sendtype'] = '单发';
                        $orderformModel->where($where)->save($data);

                    }
                }
            }

            //取得分公司名称
            $userInfo = $this->userInfo;
            $company = $this->userInfo['department'];

            //存入打印表中
            $orderprinthandleModel = D('OrderPrintHandle');
            $data = array();
            $data['printnumbertxt'] = $printNumberTxt;
            $data['time'] = date('H:i:s');
            $data['date'] = date('Y-m-d');
            $data['company'] = $company;
            $data['domain'] = $_SERVER['HTTP_HOST'];
            $where = array();
            $where['orderprinthandleid'] = $record;
            $orderprinthandleModel->where($where)->save($data);
            

        }

        //保存产品数据等其他数据
        function  update_slave_table($record){
            $this->save_slave_table($record);
        }
        
        //
        public function get_slave_table($record){
            //打印表
            $orderprintcontentModel = D('Orderprintcontent');
            $orderprintcontentResult = $orderprintcontentModel->select();
            $this->orderPrintHandle = $orderprintcontentResult;
        }

        /* 弹出选择窗口送餐员 */
        public function popupSendnameview()
        {
            if (IS_POST) {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块
                $focus = D($moduleName);

                // 取得模块的名称
                $popupModuleName = 'Sendnamemgr';

                $this->assign('moduleName', $popupModuleName); // 模块名称

                // 启动弹出选择的模块
                $popupModule = D($popupModuleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称


                // 生成list字段列表
                $listFields = $focus->popupSendnameFields;

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
                $popupModuleName = 'Sendnamemgr';

                // 启动弹出选择的模块
                $popupModule = D($popupModuleName);

                // 生成list字段列表
                $listFields = $focus->popupSendnameFields;

                // 模块的ID
                $moduleId = $popupModule->getPk();

                $datagrid = array(
                    'options' => array(
                        'url' => U($moduleName . '/popupSendnameview'),
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
                    'formatter' => $moduleName . 'PopupSendnameviewModule.operate'
                );
                $this->assign('datagrid', $datagrid);
                $this->assign('returnModule',$_REQUEST['returnModule']);
                // 取得父窗口的表格行数
                $row = $_REQUEST ['row'];
                $this->assign('row',$row);  //返回点击的订购商品行

                $this->display('OrderPrintHandle/popupviewsendname');
            }
        }


    }
?>


