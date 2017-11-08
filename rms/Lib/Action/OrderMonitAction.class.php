<?php
    //定义订单情况监控模块
    class OrderMonitAction extends ModuleAction{
        //返回订单监控的数据
        public function getOrderMonit(){
            $userInfo = $this->userInfo;
            $where = array();
            if($userInfo['rolename'] == '调度员'){
                $where['name'] = $userInfo['department'];
            }
            $where['domain'] = $this->getDomain();
            $ordermonitModel = D('Ordermonit');
            $ordermonit = $ordermonitModel->where($where)->order('ordermonitid')->select();
            $this->ajaxReturn($ordermonit);
        }

        public function index(){
            $this->listview(); 
        }


        //测试listview
        public function listview(){ 

            //如果是从listview进入的，必须删除session['where']
            if(isset($_REQUEST['delsession'])){
                unset($_SESSION['searchOption']);
                unset($_SESSION['searchText']);
            }

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            //启动列表菜单
            $this->assign('moduleName',$moduleName);   

            $this->display('OrderMonit/listview');

        }

    }


?>
