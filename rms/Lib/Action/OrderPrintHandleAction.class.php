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
            //$where['company'] = $company;
            //$where['domain'] = $_SERVER['HTTP_HOST'];

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

        //根据订单号码，取得订单详情
        public function getOrderByid(){
            //打印号
            $orderPrintNumber =  $_REQUEST['printNumber'];
            $where =array();
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
                $orderformid = $orderprinter['orderformid'];
                //订单表
                $orderformModel = D('Orderform');
                $where = array();
                $where['orderformid'] = $orderformid;
                $orderformResult = $orderformModel->where($where)->find();
                if(!empty($orderformResult)){
                    $address = $orderformResult['address'] . $orderformResult['clientname'];
                    $ordertxt = $orderformResult['ordertxt'];
                    $returnData['addressOrdertxt'] = $address. ' '.$ordertxt . ' ' .
                                                    $orderformResult['telphone'] . ' ' .
                                                    $orderformResult['custtime'];
                    $returnData['success'] = 'success';
                    $this->ajaxReturn($returnData,'JSON');
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
            //删除以前的记录 
            $where = array();
            $where['orderprinthandleid'] = $record;
            $orderprintcontentModel->where($where)->delete();

            $ordertxt = '';
            $orderPrintHandleLength = $_REQUEST['orderPrintHandleLength'];
            for($i=1;$i<= $orderPrintHandleLength;$i++){
                $orderformid =  $_REQUEST['orderPrintHandleid_'.$i];
                if(!empty($orderformid)){
                    $content =  $_REQUEST['addressOrdertxt_'.$i];
                    $data = array();
                    $data['orderprinthandleid'] = $record;
                    $data['orderformid'] = $orderformid;
                    $data['content'] = $content;
                    if(!empty($orderformid)){
                        $orderprintcontentModel->create();
                        $orderprintcontentModel->add($data);
                    }
                    $ordertxt .= $orderformid.',';
                }
            }

            //取得分公司名称
            $userInfo = $this->userInfo;
            $company = $this->userInfo['department'];

            //存入打印表中
            $orderprinthandleModel = D('OrderPrintHandle');
            $data = array();
            $data['content'] = $ordertxt;
            $data['company'] = $company;
            $data['domain'] = $_SERVER['HTTP_HOST'];
            $where = array();
            $where['orderprinthandleid'] = $record;
            $orderprinthandleModel->where($where)->save($data);
            

            //取得送餐员的送餐号码
            $sendnamemgrModel = D('Sendnamemgr');
            $where = array();
            $where['code'] = $_REQUEST['code'];
            $where['name'] = $_REQUEST['name'];
            $where['company'] = $company;
            $sendnamemgrResult = $sendnamemgrModel->where($where)->find();
            
            //写入到状态表中
            $orderstateModel = D('Orderstate');
            $data = array();
            $data['handle'] = 1;
            $data['handletime'] = date('Y-m-d H:i:s');
            $data['handlecontent'] = $_REQUEST['name'] .'配送 ';
            $where = array();
            $where['orderformid'] = $record;
            $orderstateModel->where($where)->save($data);
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

    }
?>


