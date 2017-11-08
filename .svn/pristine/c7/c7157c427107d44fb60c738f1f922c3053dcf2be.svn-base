<?php
  /**
  * 来电模块管理
  */
  class TelphoneAction extends ModuleAction{
      public function index(){
            $this->telphone(); 
        }
      
      public function telphone(){
          //$this->display();
      }
      
      
        //根据客户来电，显示客户以前的订单历史记录
        public function getByPhoneOrderhistoryView(){ 
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //生成list字段列表
            $listFields = array('address','ordertxt','telphone','totalmoney','custtime','sendname','company','state','telname','rectime');
            //模块的ID
            $moduleId = 'orderformid';
            $listHeader = $listFields;
            
            //获得查询的字段
            $telphone = $_REQUEST['telphone'];
            $address = $_REQUEST['address'];

            //查询当前订单表中的订单
            $orderformModel = D('Orderform');
            $where = array();
            $where['telphone'] = array('like',"%$telphone%");
            $where['address'] = array('like',"%$address%");
            $orderformResult = $orderformModel->where($where)->select();
            
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            //导入分页类
            import('ORG.Util.Page');// 导入分页类
            $total  = $focus->table("$dbNameTableName")->where($where)->count();// 查询满足要求的总记录数 

            //查session取得page的firstRos和listRows
            if(isset($_SESSION[$moduleName.'firstRowSearchview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowSearchview'];
            }

            $listMaxRows = 17;

            $Page = new Page($total,$listMaxRows);
            $show = $Page->show();

            //查询模块的数据 
            $selectFields = $listFields;
            array_unshift($selectFields,$moduleId);
            //查询历史表
            $listResult = $focus->table("$dbNameTableName")->where($where)->field($selectFields)->limit($Page->firstRow.','.$Page->listRows)->select();
            
            //查询当前表
            $dbNameTableName  = 'rms.rms_orderform';
             $currentListResult = $focus->table("$dbNameTableName")->where($where)->field($selectFields)->limit($Page->firstRow.','.$Page->listRows)->select();
             //var_dump($currentListResult);
             //var_dump($focus->getLastSql());
            $listResult = array_merge($currentListResult,$listResult);
            // 从数据中列出列表的数据
            $listviewEntity = $this->getListviewEntity($listResult,$moduleId);

            // dump($listview_entries);
            //$cvid = $CustomView->getCvid();
            $this->assign('page',$show);// 赋值分页输出
            $this->assign('moduleId',$moduleId);
            $this->assign('$currentModule',$currentModule);
            $this->assign('list_link_field',$focus->list_link_field);
            $this->assign("listHeader",$listFields);
            $this->assign('listviewEntity',$listviewEntity);
            $this->assign('calltelphone',$telphone);
            //$this->assign('page',$show);// 赋值分页输出
            $this->display('Telcustomer/orderhistoryview');

        }
  }
?>
