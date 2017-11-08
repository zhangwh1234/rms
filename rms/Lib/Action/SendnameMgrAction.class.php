<?php
    /**
    * 送餐员管理模块
    */
    class SendnameMgrAction extends ModuleAction{

        /*  查询 */
        public function searchview(){
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

            //启动列表菜单
            if($this->searchviewMenuPath){
                $this->display('Module/searchviewmenu');
            }else{
                $this->display($moduleName.'/searchviewmenu');
            }

            $this->searchviewMenu();        

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
            if(isset($searchText)){
                $where['telphone'] = array('like','%'.$searchText .'%');
                $this->assign('searchTextValue',$searchText);
                $_SESSION['searchText'.$moduleName] = $searchText;
            }else{
                if($_SESSION['searchText'.$moduleName]){
                    $where[$_SESSION['searchText'.$moduleName]] = array('like','%'.$_SESSION['searchText'].$moduleName.'%');
                    $this->assign('searchTextValue',$_SESSION['searchText'.$moduleName]);
                }
            }

            $userInfo = $_SESSION['userInfo'];  
            $company = $this->userInfo['department'];
            //查询条件分公司的限制
            if($where){
                $map['_complex'] = $where;
            }


            if(($userInfo['rolename'] == '客服经理') || ($userInfo['rolename'] == '联络员') || ($userInfo['rolename'] == '系统管理员')){
                $where['domain'] = $this->getDomain();
            }else{
                $map['company'] = $company;
                $map['domain'] = $this->getDomain();
            }

            var_dump($map);

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
            if(($userInfo['rolename'] == '客服经理') || ($userInfo['rolename'] == '联络员') || ($userInfo['rolename'] == '系统管理员')){
                array_unshift($selectFields,'company');
            }

            array_unshift($selectFields,$moduleId);
            $listResult = $focus->field($selectFields)->where($map)->limit($Page->firstRow.','.$Page->listRows)->order("$moduleId desc")->select();
   
            // 从数据中列出列表的数据
            $listviewEntries = $this->getListviewEntity($listResult,$moduleId);

            $this->assign('moduleId',$moduleId);
            $this->assign('listEntries',$listviewEntries);
            $this->assign('page',$show);// 赋值分页输出

            $searchOption = $focus->searchFields;           
            $this->assign('searchOption',$searchOption);
            $this->assign('returnAction','searchview');  //定义返回的方法

            if($this->searchviewPath){
                $this->display('Module/searchview');    
            }else{
                $this->display($moduleName.'/searchview');
            }

        }




        //插入，补充数据的回调函数
        public function autoParaInsert(){
            //分公司名称
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];
            $auto = array ( 
            	array('company',$company),  //分公司名称
				array('domain',$this->getDomain()),
                array('name','trim',1,'function')
            );

            return $auto;

        }

        //返回一些其他的数据,比如下拉列表框等的数据
        public function returnMainFnPara(){
            //通知送餐员消息的类型
            $sendtype = array();
            $sendtype[] = array('sendtype'=>'短信');
            $this->sendtype = $sendtype; 
            if(isset($_REQUEST['record'])){
                $currentName = array('name'=>$this->result['sendtype']);
                $this->currentName = $currentName;
            }else{
               $currentName = array('name'=>'短信');
                $this->currentName = $currentName; 
            }
            $this->display('SendnameMgr/sendnamemgrjs'); //引入自身的js
        }

        /**
        * 返回listview的查询条件
        */
        public function returnWhere(&$where){
            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];


            $userInfo = $_SESSION['userInfo'];
            $company = $this->userInfo['department'];


            if(($userInfo['rolename'] == '客服经理') || ($userInfo['rolename'] == '联络员') || ($userInfo['rolename'] == '系统管理员')){
            }else{
                //查询条件
                $where['company'] = $company;
            }
        }
    }
?>
