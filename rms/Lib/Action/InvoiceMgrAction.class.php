<?php
    /***
    * 发票管理系统，支持发票的查看，打印等功能
    * 2014-05-30
    */

    class InvoiceMgrAction extends ModuleAction{

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

                $where = array();
                $where ['state'] = array(
                    'notlike',
                    '已%'
                );

                //用户信息
                $userInfo = $_SESSION['userInfo'];
                //如果是超级管理员，显示系统管理导航
                if((C('RBAC_SUPERADMIN') == $userInfo['name'])){

                }else{
                    // 分公司的名称
                    $company = $userInfo ['department'];
                    $where ['company'] = $company;
                }
                //1是电子票,2是普通发票
                //需要显示普通发票
                //$where['type'] = array('NEQ','1');
                //$where ['_string'] = "length(trim(company)) > 0";
                $where ['domain'] = $_SERVER ['HTTP_HOST'];

                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $focus->where($where)->count(); // 查询满足要求的总记录数

                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ($pageNumber)) {
                    $pageNumber = 1;
                }

                //使用cookie读取rows
                $listMaxRows = $_COOKIE['listMaxRows'];
                if(!empty($listMaxRows)){

                }else{
                    $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
                }

                //订单配送还要显示两个统计数据
                $listMaxRows = $listMaxRows -2;

                // 取得页数
                $_GET ['p'] = $pageNumber;
                $Page = new Page ($total, $listMaxRows);


                // 查session取得page的firstRos和listRows
                if (isset ($_SESSION [$moduleName . 'firstRowlistview'])) {
                    $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
                }


                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

                // 查询模块的数据
                $selectFields = $listFields;
                array_unshift($selectFields, $moduleId);

                $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order('invoiceid desc')->select();
                //var_dump($focus->getLastSql());
                $orderHandleArray ['total'] = $total;
                if (count($listResult) > 0) {
                    $orderHandleArray ['rows'] = $listResult;
                } else {
                    $orderHandleArray ['rows'] = array();
                }

                $this->ajaxReturn($orderHandleArray, 'JSON');
            } else {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块
                $focus = D($moduleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称
                $domain = $_SERVER['HTTP_HOST'];
                if($domain == 'bj.lihuaerp.com'){
                    $this->display('InvoiceMgr/listview');
                }else{
                    $this->display('InvoiceMgr/czlistview');
                }


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
                $where = array ();
                $searchText = urldecode($_REQUEST ['searchOther']); // 查询内容
                foreach ( $focus->searchFields as $value ) {
                    $where [$value] = array (
                        'like',
                        '%' . $searchText . '%'
                    );
                }
                $where ['_logic'] = 'OR';

                $map['_complex'] = $where;
                $map['domain'] = $_SERVER['HTTP_HOST'];
                // 获取分公司
                $company = $this->userInfo ['department'];
                $map ['company'] = $company;


                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $focus->where($map)->count(); // 查询满足要求的总记录数


                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ($pageNumber)) {
                    $pageNumber = 1;
                }

                if($_SESSION['maxRows']){
                    $listMaxRows = $_SESSION['maxRows'];
                }else{
                    $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
                }
                //订单配送还要显示两个统计数据
                $listMaxRows = $listMaxRows -2;

                // 取得页数
                $_GET ['p'] = $pageNumber;
                $Page = new Page ($total, $listMaxRows);

                // 查询模块的数据
                foreach($listFields as $key => $value) {
                    $selectFields[] = $key;
                }
                array_unshift ( $selectFields, $moduleId );
                $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();

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

                // 启动当前模块的模型
                $focus = D($moduleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称

                $this->assign('operName', '综合查询');

                $searchOther = urldecode($_REQUEST ['searchTextOther']); // 查询内容
                $this->assign('searchOther', $searchOther);
                $this->assign('searchIntroduce', '查询内容：' . $searchOther);
                $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法
                $this->display('InvoiceMgr/searchviewother'); // 查询的结果显示
            }

        }

        //返回发票信息
        public function getInvoiceInfo($invoiceid){
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            $where = array();
            $where['invoiceid'] = $invoiceid;
            $where['ordermoney'] = array('gt',0);

            $fields = array(
              'invoiceid','header','body','gmf_nsrsbh','gmf_dzdh','gmf_yhzh','ordermoney','type','ordersn','orderformtxt',
            );
            $invoiceResult = $focus->field($fields)->where($where)->find();

            //1是电子票,2是普通票,这里是电子票的处理逻辑
            if(!empty($invoiceResult)){
                if(empty($invoiceResult['body'])){
                    $invoiceResult['body'] = '工作餐';
                }

                // 接线员的姓名
                $userInfo = $_SESSION ['userInfo'];
                $name = $userInfo ['truename'];
                $invoiceResult['name'] = mb_substr($name,0,3,'utf-8');  //开票人

                // 获取分公司
                $company = $this->userInfo ['department'];

                //返回收款人和复核人的信息,如果为空,就写默认
                $companymgrModel = D('companymgr');
                $where = array();
                $where['name'] = $company;
                $where['domain'] = $_SERVER['HTTP_HOST'];
                $companymgrResult = $companymgrModel->field('cashier,checker')->where($where)->find();
                if(empty($companymgrResult['cashier'])){
                    $invoiceResult['cashier'] = mb_substr($name,0,3,'utf-8');
                }else{
                    $invoiceResult['cashier'] = $companymgrResult['cashier'];
                }
                if(empty($companymgrResult['checker'])){
                    $invoiceResult['checker'] = '丽华';
                }else{
                    $invoiceResult['checker'] = $companymgrResult['checker'];
                }
                //如果是电子票,就直接返回
                if($invoiceResult['type'] == 2){
                    if($_SERVER['HTTP_HOST'] == 'bj.lihuaerp.com'){
                        $invoiceResult['city'] = 'bj';
                    }
                    $this->ajaxReturn($invoiceResult);
                }

                //将发票数据存入到invoiceweb表
                $data = array();
                $data['invoiceid'] = $invoiceid;
                $data['ordersn'] = $invoiceResult['ordersn'];
                $data['ordertxt'] = $invoiceResult['orderformtxt'];
                $data['header'] = $invoiceResult['header'];
                $data['body'] = $invoiceResult['body'];
                $data['gmf_nsrsbh'] = $invoiceResult['gmf_nsrsbh'];
                $data['gmf_dzdh'] = $invoiceResult['gmf_dzdh'];
                $data['gmf_yhzh'] = $invoiceResult['gmf_yhzh'];
                $data['money'] = $invoiceResult['ordermoney'];
                $data['KPR'] = mb_substr($name,0,3,'utf-8');  //开票人
                $data['SKR'] =  $invoiceResult['cashier'];    //收款人
                $data['FHR'] = $invoiceResult['checker']; //复核人
                $data['printman'] = mb_substr($name,0,3,'utf-8');  //操作员
                $data['type'] = 1;
                $data['date'] = date('Y-m-d H:i:s');
                $data['createdate'] = date('Y-m-d H:i:s');
                // 获取分公司
                $company = $this->userInfo ['department'];
                $data['company'] = $company;
                $data['domain'] = $_SERVER['HTTP_HOST'];
                $where = array();
                $where['ordersn'] = $invoiceResult['ordersn'];
                $invoicewebModel = D('invoiceweb');
                $invoicewebResult = $invoicewebModel->where($where)->find();
                if(empty($invoicewebResult)){
                    //hack一下,让电子票能够识别不同的地区
                    if($_SERVER['HTTP_HOST'] == 'bj.lihuaerp.com'){
                        $data['eticketno'] = '1'.rand(1,10).date('s').date('md') . $invoiceResult['invoiceid'].rand(10,1000);
                        $data['city'] = 'bj';
                    }else{
                        $data['eticketno'] = '2'.rand(1,10).date('s'). date('md') . $invoiceResult['invoiceid'].rand(10,1000);
                    }
                    $invoicewebModel->create();
                    $invoicewebModel->add($data);
                }else{
                    $data['eticketno'] = $invoicewebResult['eticketno'];
                    $invoicewebModel->where($where)->save($data);
                }

                $eticketno = $data['eticketno'];
                // 同时写入日志中,先取得orderformid
                $orderformModel  = D('orderform');
                $where = array();
                $where['ordersn'] = $invoiceResult['ordersn'];
                $orderformResult = $orderformModel->field('orderformid')->where($where)->find();
                // 记入操作到action中
                $orderactionModel = D('Orderaction');
                $actiondata = array();
                $actiondata ['orderformid'] = $orderformResult['orderformid']; // 订单号
                $actiondata ['ordersn'] = $invoiceResult['ordersn'];
                $actiondata ['action'] = "产生电子票提取码:".$eticketno;
                $actiondata ['logtime'] = date('H:i:s');
                $actiondata ['domain'] = $_SERVER ['HTTP_HOST'];
                $orderactionModel->create();
                $result = $orderactionModel->add($actiondata);

                $this->ajaxReturn($data);
            }

            $this->ajaxReturn($invoiceResult);
        }

        //开票成功,设置标志
        public function setInvoiceOpened($invoiceid){
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            $where = array();
            $where['invoiceid'] = $invoiceid;

            $data = array();
            $data['state'] = '已开票';
            $data['opentime'] = date('H:i:s Y-m-d');
            // 接线员的姓名
            $userInfo = $_SESSION ['userInfo'];
            $data['openname'] =  $userInfo ['truename'];
            $result = $focus->where($where)->save($data);

            if($result){
                $this->ajaxReturn(array('status'=>1));
            }else{
                $this->ajaxReturn(array());
            }
        }

        //放弃开票
        public function cancelInvoice($invoiceid){
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            $where = array();
            $where['invoiceid'] = $invoiceid;

            $data = array();
            $data['state'] = '已放弃';
            $result = $focus->where($where)->save($data);

            if($result){
                $this->ajaxReturn(array('status'=>1,'info'=>'成功!'));
            }else{
                $this->ajaxReturn(array('info'=>'失败!'));
            }
        }

        /* 一般顺序表记录的保存
         * 2016-6-2改写
        */
        public function insert() {
            // 返回当前的模块名
            $moduleName = $this->getActionName ();

            $focus = D ( $moduleName );
            $this->assign ( 'moduleName', $moduleName );

            // 回调自动完成的函数
            $auto = $this->autoParaInsert ();
            $focus->setProperty ( "_auto", $auto );

            // 保存主表
            $result = $focus->create ();
            if (! $result) {
                exit ( $focus->getError () );
            }
            $result = $focus->add ();

            if (! $result) {
                $info['status'] = 0;
                $info['info'] =  '保存数据不成功！' ;
                $this->ajaxReturn(json_encode($info),'EVAL');
            }

            // 取得保存的主键
            $record = $result;

            // 新写的保存从表方案
            $result = $this->save_slave_table ( $record );

            // 如果保存订单都成功，就跳转到查看页面
            $return ['record'] = $record;

            $returnAction = $_REQUEST['returnAction'];

            // 生成查看的url
            $detailviewUrl = U ( "$moduleName/listview", array (
                'record' => $record,'returnAction'=>$returnAction
            ) );
            $return = $detailviewUrl;
            $info['status'] = 1;
            $info['info'] ='保存成功' ;
            $info['url'] = $return;
            $this->ajaxReturn(json_encode($info),'EVAL');
        }



        //插入，补充数据的回调函数
        public function autoParaInsert(){
            //分公司名称
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];
            $auto = array (
                array('orderformtxt','自开'),
                array('company',$company),  //分公司名称
                array('domain',$_SERVER['HTTP_HOST']),
                array('ordertime',date('H:i:s')),
                array('header','trim',3,'function') ,
                array('gmf_nsrsbh','trim',3,'function') ,
                array('gmf_dzdh','trim',3,'function') ,
                array('gmf_yhzh','trim',3,'function') ,
            );

            return $auto;

        }

        // 更新记录
        public function update() {

            // 返回当前的模块名
            $moduleName = $this->getActionName ();

            $focus = D ( $moduleName );
            $this->assign ( 'moduleName', $moduleName );
            // 返回的页面
            $returnAction = $_REQUEST ['returnAction'];

            // 取得记录号
            $record = $_REQUEST ['record'];
            $moduleId = $focus->getPk ();

            // 回调自动完成的函数
            $auto = $this->autoParaUpdate ();
            $focus->setProperty ( "_auto", $auto );
            // 保存主表
            $focus->create ();

            $where = array();
            $where[$moduleId] = $record;
            $result = $focus->where ( $where )->save ();

            $return ['record'] = $record;

            // 生成查看的url
            $detailviewUrl = U ( "$moduleName/listview", array (
                'record' => $record,'returnAction'=>$returnAction
            ) );
            $return = $detailviewUrl;
            $info['status'] = 1;
            $info['info'] ='保存成功' ;
            $info['url'] = $return;
            $this->ajaxReturn(json_encode($info),'EVAL');
        }

    }
?>
