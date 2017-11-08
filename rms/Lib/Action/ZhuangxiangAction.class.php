<?php
    /**
    * 装箱单模块
    * 2014-5-25开发
    */

    class ZhuangxiangAction extends ModuleAction{

        //定义一个空的函数，用于返回主程序需要的一些参数
        public function returnMainFnPara(){

        }

        //根据代码获取送餐员名字
        public function getSendnameByCode(){

            //分公司的名称
            $userInfo = $_SESSION['userInfo'];
            $company = $userInfo['department'];


            //获得处理过了的编码
            $code = $_REQUEST['code'];

            //定义返回的数组
            $returnInfo = array();

            /** 先编辑送餐员的编码 ***/
            //根据编码取得送餐员姓名
            $sendnameMgrModel = D('Sendnamemgr');
            $where['code'] = $code; //送餐员的编号
            $where['company'] = $company;
            $sendnameResult = $sendnameMgrModel->field('name,telphone')->where($where)->find();
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

        //保存产品数据等其他数据
        function  save_slave_table($record){

            //取得分公司名称
            $company = $this->userInfo['department'];

            //订单号
            $moduleId = 'zhuangxiangid';

            $zhuangxiangproductsModel = D('Zhuangxiangproducts');
            //先清掉数据
            $zhuangxiangproductsModel->where("zhuangxiangid=$record")->delete();

            $zhuangxiangTxt = '';
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
                $data['zhuangxiangid'] = $record;
                $data['sendname'] = trim($_REQUEST['sendname']);
                $data['company'] = $company;
                $data['domain'] = $this->getDomain();
                if( !empty($name) and  !empty($number)){
                    $zhuangxiangproductsModel->create();
                    $zhuangxiangproductsModel->add($data);
                    $zhuangxiangTxt .= $number . '×' . $shortname. ' ';
                    $totalmoney += $number * $price; 
                }   
            }  

            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $name = $userInfo['truename'];

            //记入操作到action中
            $orderactionModel = D('Zhuangxiangaction');
            $action['zhuangxiangid'] = $record;  //订单号
            $action['action'] = $name . ' 新建 装箱单给:'.trim($_REQUEST['sendname']).' '.$zhuangxiangTxt;
            $action['logtime'] = date('H:i:s');
            $orderactionModel->create();
            $result = $orderactionModel->add($action);
            // echo $orderaction_model->getLastSql();

            //保存数量规格
            $data = array();
            $data['zhuangxiangtxt'] = $zhuangxiangTxt;
            $data['totalmoney'] = $totalmoney;
            $zhuangxiangModel = D('Zhuangxiangform');
            $where = array();
            $where[$moduleId] = $record;
            $result = $zhuangxiangModel->where($where)->save($data);

            //保存到送餐员餐售情况
            $sendnameproductsModel = D('Sendnameproducts');
            $where = array();
            $where['extid'] = $record;
            $where['domain'] = $this->getDomain();
            $where['company'] = $company;
            $sendnameproductsModel->where($where)->delete();
            $productsLength = $_REQUEST['productsLength'];
            for($i=1;$i<= $productsLength;$i++){
                $code = $_REQUEST['productsCode_'.$i];
                $name = $_REQUEST['productsName_'.$i];
                $shortname = $_REQUEST['productsShortName_'.$i];
                $price = $_REQUEST['productsPrice_'.$i];
                $number = $_REQUEST['productsNumber_'.$i];
                $money = $_REQUEST['productsMoney_'.$i]; 
                $data = array();                
                $data['productsname'] = $name;
                $data['shortname'] = $shortname;
                $data['type'] = '装箱';
                $data['number'] = $number;
                $data['extid'] = $record;
                $data['sendname'] = trim($_REQUEST['sendname']); //送餐员
                $data['company'] = $company;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $this->getDomain();
                if( !empty($name) and  !empty($number)){
                    $sendnameproductsModel->create();
                    $sendnameproductsModel->add($data);
                }   
            } 

        }

        //保存产品数据等其他数据
        function  update_slave_table($record){
            $this->save_slave_table($record);
        }

        //插入，补充数据的回调函数
        public function autoParaInsert(){
            $apTime = date('H'); 
            if($apTime > 15){
                $ap = '下午';
            }else{
                $ap = '上午';
            }
            //接线员的姓名
            $userInfo = $this->userInfo;
            $name = $userInfo['truename'];
            $company = $userInfo['department'];
            $auto = array ( 
            array('recdate',date('Y-m-d')),  //录入日期
            array('rectime',date('H:i:s')), // 对录入时间
            array('inputname',$name),   //输入者
            array('company',$company),   //分公司
            array('domain',$this->getDomain()),
            array('ap',$ap),
            array('state','装箱')                
            );

            return $auto;

        }

        public function get_slave_table($record){
            //取得产品信息
            $zhuangxiangproductsModel = D('Zhuangxiangproducts');
            $zhuangxiangproducts = $zhuangxiangproductsModel->field('zhuangxiangid,code,name,shortname,price,number,money')->where("zhuangxiangid=$record")->select();
            //dump($orderproducts);
            $this->assign('orderproducts',$zhuangxiangproducts);   

            //取得订单日志
            $zhuangxiangactionModel =D('Zhuangxiangaction');
            $zhuangxiangaction = $zhuangxiangactionModel->where("zhuangxiangid=$record")->select();
            $this->assign('orderaction',$zhuangxiangaction);

            //单独取得订单金额 
            $orderform_model = D('Orderform');
            $orderform = $orderform_model->field('totalmoney')->where("orderformid=$record")->select();
            $totalmoney = $orderform[0]['totalmoney'];
            $this->assign('totalmoney',$totalmoney);  


        }


        /*取得打印需要的数据*/
        function getPrintOrder(){
            //取得订单号
            $record = $_REQUEST['zhuangxiangid'];
            //查询订单
            $zhuangxiangformModel = D('Zhuangxiangform');
            $where = array();
            $where['zhuangxiangid'] = $record;
            $zhuangxiangResult = $zhuangxiangformModel->where($where)->find();
            //查询订货
            $zhuangxiangproductsModel = D('Zhuangxiangproducts');
            $zhuangxiangproducts = $zhuangxiangproductsModel->where($where)->select();

            $order['zhuangxiangform'] = $zhuangxiangResult;
            $order['zhuangxiangproducts'] = $zhuangxiangproducts;
            $this->ajaxReturn($order,'JSON');

        }


        /* 设定订单已打印状态*/
        function setOrderPrinted(){
            //取得订单号
            $record = $_REQUEST['zhuangxiangid'];
            //查询订单
            $zhuangxiangformModel = D('Zhuangxiangform');
            $data = array();
            $data['state'] = '已打印';
            $where = array();
            $where['zhuangxiangid'] = $record;
            $result  = $zhuangxiangformModel->where($where)->save($data);

            //同时写入日志中
            //记入操作到action中
            $zhuangxiangactionModel = D('Zhuangxiangaction');
            $action = array();
            $action['zhuangxiangid'] = $record;  //订单号
            //$company = $data['company'];
            $action['action'] = "装箱单打印,打印号:" . $record;
            $action['logtime'] = date('H:i:s');
            $zhuangxiangactionModel->create();
            $result = $zhuangxiangactionModel->add($action);
            $this->ajaxReturn(array());

        }

        /**
         * 返回listview的查询条件
         */
        public function returnWhere(&$where){
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];
            //查询条件
            $where['company'] = $company;
            $where['domain'] = $this->getDomain();
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
                $where['domain'] = $this->getDomain();

                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $popupModule->where($where)->count(); // 查询满足要求的总记录数
                // 查session取得page的firstRos和listRows


                // 取得显示页数
                //使用cookie读取rows
                $listMaxRows = $_COOKIE['listMaxRows'];
                if (!empty($listMaxRows)) {

                } else {
                    $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
                }

                //订单配送还要显示两个统计数据
                $listMaxRows = $listMaxRows - 2;

                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $pageNumber = $_REQUEST ['page'];
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
                        'pageSize' => 20
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

                $this->display('Zhuangxiang/popupviewProducts');
            }
        }

    }
?>
