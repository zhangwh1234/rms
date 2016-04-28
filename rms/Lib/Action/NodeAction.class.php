<?php
    /**
    * 控制器的节点管理
    */

    class NodeAction extends ModuleAction{

        /**
        * 列表节点
        */
        public function listview(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称


            //生成list字段列表
            $listFields = $focus->listFields;
            //模块的ID
            $moduleId = $focus->getPk();

            //加入模块id到listHeader中
            //array_unshift($listFields,$moduleNameId); 
            $listHeader = $listFields;
            $this->assign("listHeader",$listHeader);   //列表头
            $this->assign('returnAction','listview');  //定义返回的方法

            //导入分页类
            import('ORG.Util.Page');// 导入分页类
            $total      = $focus->count();// 查询满足要求的总记录数   
            //查session取得page的firstRos和listRows


            if(!isset($_SESSION[$moduleName.'firstRowlistview'])){
                $Page->firstRow = $_SESSION[$moduleName.'firstRowlistview'];
            }

            //var_dump($_SESSION['test']);
            $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
            if(isset($listMaxRows)){
                $Page->listRows = $listMaxRows;
            }else{
                $listMaxRows = 15;
            } 
            $Page = new Page($total,$listMaxRows);
            $show = $Page->show();
           
            $this->assign('moduleId',$moduleId);
  

            $field = array('id','name','title','pid');
            $node = D('node')->field($field)->order('id asc')->select();

            $this->node = node_merge($node);

            $this->display('Node/listview');
        }



        /**
        * 新建模块
        */
        public function createviewModule(){
            $this->createview();
        }

        /**
        * 编辑模块
        */
        public function editviewModule(){
            $this->editview();
        }

        /* 删除模块的记录 */
        public function deleteModule(){
            //返回当前的模块名
            $moduleName = $this->getActionName();

            $focus = D($moduleName);
            $this->assign('moduleName',$moduleName);

            //取得保存的主键
            $record = $_REQUEST['record'];

            $moduleId = $focus->getPk();

            $where[$moduleId] = $record;

            //查询模块下面是否有方法，如果有方法，就不能删除
            $result = $focus->where("pid=$record")->count();
            if($result > 0 ){
                $this->error('不能删除',"$moduleName/listview");
            }
            //删除记录
            $result = $focus->where($where)->delete();

            if($result){
                $info['status'] = 1;
                $info['info'] ='删除成功' ;
                $this->ajaxReturn($info,'JSON');
            }else{
                $info['status'] = 0;
                $info['info'] ='删除失败' ;
                $this->ajaxReturn($info,'JSON');
            }

        }


        //插入，补充数据的回调函数
        public function autoParaInsert(){
            $arr = array(
            array('level',1),
            array('sort',1),
            );
            return $arr;
        }

        /**
        * 新建功能
        */
        public function createviewMethod(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称

            //启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称


            //改写创建的字段为功能的字段
            $focus->createFields = $focus->createMethodFields;

            //返回新建区块和字段
            $blocks = $focus->createBlocks();

            $this->pid = I('pid');

            $this->assign('blocks',$blocks);         //编辑字段区
            $this->assign('fieldsFocus',$focus->fieldsFocus);  //指定字段获得焦点

            $this->display();
        }

        /**
        * 保存模块的方法
        * 
        */
        public function insertMethod(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);
            
            $pid = I('pid');
            $name = I('method_name');
            $title = I('method_title');
            $auto = array(
            array('pid',$pid),
            array('name',$name),
            array('title',$title),
            array('level',2), 
            );

            $focus->setProperty("_auto",$auto);
            //保存主表
            $focus->create();
            $result = $focus->add();
            $record = $result;
           //如果保存订单都成功，就跳转到查看页面
            $return['record'] = $record;

            // 生成查看的url
            $detailviewUrl = U ( "$moduleName/detailview", array (
                'record' => $record,
            ) );
            $return = $detailviewUrl;
            $info['status'] = 1;
            $info['info'] ='保存成功' ;
            $info['url'] = $return;
            $this->ajaxReturn($info,'JSON');
        }

        /**
         *  编辑方法
         */
        public function editviewMethod(){
            $this->editview();
        }

        /**
        * 删除方法的记录
        */
        public function deleteMethod(){
            //返回当前的模块名
            $moduleName = $this->getActionName();

            $focus = D($moduleName);            
            $this->assign('moduleName',$moduleName); 

            //取得保存的主键
            $record = $_REQUEST['record'];

            $moduleId = $focus->getPk();

            $where[$moduleId] = $record;
            //删除记录
            $focus->where($where)->delete();

            $this->redirect("$moduleName/listview", array(), 0, '页面跳转中...');

        }

    }
?>
