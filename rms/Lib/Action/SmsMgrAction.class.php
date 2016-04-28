<?php
    /**
    * 短信管理
    */
    class SmsMgrAction extends ModuleAction{

        //回调自动完成的函数
        //插入，补充数据的回调函数
        public function autoParaInsert(){
        	$userInfo = $_SESSION['userInfo'];
        	$company = $userInfo['department'];
            $auto = array ( 
            	array('firstdate',date('Y-m-d H:i:s')),  //录入日期
            	array('issend',0), // 状态
            	array('company',$company),
            	array('domain',$_SERVER['HTTP_HOST']),
            );

            return $auto;
        }

        public function getUser(){
            $sms_model = D('Smsmgr');
            $smscount = $sms_model->query("select count(*) as c  from users");
            //dump($smscount);
            $result["total"] = $smscount[0]['c'];
            $smsuser = $sms_model->query("select * from users limit 0,10");
            $result['rows'] = $smsuser;
            $this->ajaxReturn($result,'JSON');
        }

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

        //定义一个空的函数，用于返回主程序需要的一些参数
        public function returnMainFnPara(){

        }

        // 根据代码获取送餐员名字
        public function getSendnameByCode()
        {
            // 分公司的名称
            $userInfo = $_SESSION ['userInfo'];
            $company = $userInfo ['department'];

            // 获得处理过了的编码
            $code = $_REQUEST ['code'];

            // 定义返回的数组
            $returnInfo = array();
            /**
             * 先编辑送餐员的编码 **
             */
            // 根据编码取得送餐员姓名
            $sendnameMgrModel = D('Sendnamemgr');
            $where = array();
            $where ['code'] = $code; // 送餐员的编号
            //$where['company'] = $company;
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $sendnameResult = $sendnameMgrModel->field('name,telphone,weixin')->where($where)->find();
            if ($sendnameResult) {
                $sendname = trim($sendnameResult ['name']);
                $telphone = $sendnameResult ['telphone'];
                $weixin = trim($sendnameResult ['weixin']);
            } else {
                $returnInfo ['error'] = 'error';
                $returnInfo ['msg'] = '没有查到信息';
                $this->ajaxReturn($returnInfo);
            }
            // 根据送餐员信息，处理订单
            $orderformData ['sendname'] = $sendname;
            $orderformData ['telphone'] = $telphone;
            $orderformData ['weixin'] = $weixin;

            // 定义返回
            $returnInfo ['success'] = 'success';
            $returnInfo ['data'] = $orderformData;
            $this->ajaxReturn($returnInfo, 'JSON');
        }
    }
?>
