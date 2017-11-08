<?php
    /**
    * 模块菜单
    */
    class ModuleMenuAction extends Action{

        public function index($module_name,$type){
            //读取记录号
            $record = $_REQUEST['record'];
            $this->assign('record',$record);

            $tab_name = $this->getTab($module_name);
            $this->assign('tab_name',$tab_name);     
            $this->assign('module_name',$module_name);   //取得模块名称
            $this->assign('type',$type);       //执行的方法，比如listview等
            $this->display("Modulemenu/index");
            //放入普通查询
            // $this->display('./Tpl/Module/generalsearchview.html');
        }

        public function orderHistory($module_name,$type){
            //读取记录号
            $record = $_REQUEST['record'];
            $this->assign('record',$record);

            $tab_name = $this->getTab($module_name);
            $this->assign('date',date('Y-m-d'));
            $this->assign('tab_name',$tab_name);     
            $this->assign('module_name',$module_name);   //取得模块名称
            $this->assign('type',$type);       //执行的方法，比如listview等
            $this->display("Modulemenu/orderHistory"); 
        }

        public function orderHandle($module_name,$type){
            //读取记录号
            $record = $_REQUEST['record'];
            $this->assign('record',$record);

            $tab_name = $this->getTab($module_name);
            $this->assign('tab_name',$tab_name);     
            $this->assign('module_name',$module_name);   //取得模块名称
            $this->assign('type',$type);       //执行的方法，比如listview等
            $this->display("Modulemenu/orderHandle"); 
        }

        //订单分配
        public function orderDistribution($module_name,$type){
            //分公司的数据
            $companymgr_model = D('Companymgr');
            $companymgr = $companymgr_model->field('name')->select();
            $this->assign('companymgr',$companymgr);
            $this->assign('companyName','');

            if(isset($_SESSION['discompanyname'])){
                $this->assign('companyName',$_SESSION['discompanyname']);
            }
            if(isset($_SESSION['disaddress'])){
                $this->assign('address',$_SESSION['disaddress']);
            }
            if(isset($_SESSION['distelphone'])){
                $this->assign('telphone',$_SESSION['distelphone']); 
            }


            //读取记录号
            $record = $_REQUEST['record'];
            $this->assign('record',$record);

            $tab_name = $this->getTab($module_name);
            $this->assign('tab_name',$tab_name);     
            $this->assign('module_name',$module_name);   //取得模块名称
            $this->assign('type',$type);       //执行的方法，比如listview等
            $this->display("Modulemenu/orderDistribution"); 
        }

        //订单监控
        public function orderMonit($module_name,$type){
            //读取记录号
            $record = $_REQUEST['record'];
            $this->assign('record',$record);

            $tab_name = $this->getTab($module_name);
            $this->assign('tab_name',$tab_name);     
            $this->assign('module_name',$module_name);   //取得模块名称
            $this->assign('type',$type);       //执行的方法，比如listview等
            $this->display("./Tpl/Modulemenu/orderMonit.html"); 
        }

        //根据模块名称，取得导航名称
        function getTab($module_name){
            //根据模块名查找模块ID
            $module_model = M('module');
            $module_result = $module_model->field('moduleid')->where("name='$module_name'")->find();
            $moduleid = $module_result['moduleid'];

            //根据模块ID,查找关联的tabid
            $tab_module_model = M('tab_module_rel');
            $tab_module_result = $tab_module_model->field('tabid')->where("moduleid=$moduleid")->find();
            $tabid = $tab_module_result['tabid'];

            //取得tab的名称
            $tab_model = M('tab');
            $tab_result = $tab_model->field('tab_label')->where("tabid=$tabid")->find();
            $tab_name = $tab_result['tab_label'];

            //返回导航名称
            return $tab_name;
        }
    }
?>
