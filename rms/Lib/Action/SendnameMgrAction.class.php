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
            $searchOption = $_REQUEST['searchOption'];  //查询项目
            $searchText = $_REQUEST['searchText'];      //查询内容
            if(isset($searchOption) && isset($searchText)){
                if($searchOption == '全部'){ //如果是全部，那么全都要查询
                    foreach($focus->searchFields as $value){
                        $where[$value] =  array('like','%'.$searchText.'%');                       
                    }
                    $where['_logic'] = 'OR';
                    $_SESSION['searchOption'.$moduleName] = $searchOption;
                    $_SESSION['searchText'.$moduleName] = $searchText;
                }else{
                    $where[$searchOption] = array('like','%'.$searchText.'%');
                    $this->assign('searchOptionValue',$searchOption);
                    $this->assign('searchTextValue',$searchText);
                    $_SESSION['searchOption'.$moduleName] = $searchOption;
                    $_SESSION['searchText'.$moduleName] = $searchText; 
                }
            }else{
                if(isset($_SESSION['searchOption'.$moduleName],$_SESSION['searchText'.$moduleName])){
                    $where[$_SESSION['searchOption'.$moduleName]] = array('like','%'.$_SESSION['searchText'].$moduleName.'%');
                    $this->assign('searchOptionValue',$_SESSION['searchOption'.$moduleName]);
                    $this->assign('searchTextValue',$_SESSION['searchText'.$moduleName]);
                }
            }

            $userInfo = $_SESSION['userInfo'];  
            $company = $this->userInfo['department'];
            //查询条件分公司的限制
            if($where){
                $map['_complex'] = $where;
            }
            $map['company'] = $company;
            $map['domain'] = $_SERVER['HTTP_HOST'];


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
				array('domain',$_SERVER['HTTP_HOST']),
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
            //查询条件
            $where['company'] = $company;
            $where['domain'] = $_SERVER['HTTP_HOST'];
        }
    }
?>
