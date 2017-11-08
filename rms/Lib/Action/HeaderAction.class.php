<?php

    class HeaderAction extends Action{
        public function index(){

            //为了显示城市
            require APP_PATH.'Conf/datapath.php';
            $HTTP_POST = $this->getDomain();
            $this->assign('city',$rmsDataPath[$HTTP_POST]['CITY']);

            $this->display(); 
            $this->nav();
        }


        /* 系统导航  */
        public function nav(){

            /** 取得用户的权限模块 */
            //用户信息
            $userInfo = $_SESSION['userInfo'];
            $userid = $userInfo['userid'];


            //如果是超级管理员，显示系统管理导航
            if((C('RBAC_SUPERADMIN') == $userInfo['name'])){
                $this->assign('category','System');
            }

            //取得用户的角色
            $roleUserModel = D('RoleUser');
            $where = array();
            $where['user_id'] = $userid;
            $result = $roleUserModel->where($where)->find();

            //用户角色的ID
            $role_id = $result['role_id'];

            //获得用户权限
            $access = D('Access')->where(array('role_id'=>$role_id))->getField('node_id',true);


            //读取节点
            $field = array('id','name','title','pid');
            $node = D('Node')->field($field)->where(array('level'=>1))->select();

            $node_arr = array();
            foreach($node as $value){  //转换一下
                $node_arr[$value['id']] = $value['name'];
            }

            //将access填上模块名称
            $access_arr = array();
            foreach($access as $key=>$value){
                $access_arr[] = $node_arr[$value];
            }

            /****************************************/ 

            //dump($userInfo);
            //返回导航菜单的数据
            $navModel = D('Nav');
            $navResult = $navModel->field('navid,navname')->order('sequence')->select();
   
            $nav_info_array = array();  //id和label的对应
            foreach($navResult as $navKey => $navValue){
                $nav_info_array[$navValue['navid']] = $navValue['navname'];
            }
			//p($nav_info_array);
            //返回模块菜单
            $menuModel = D('Menu');
            $menuResult = $menuModel->select();

            $menu_info_array = array();
            foreach($menuResult as $menuKey => $menuValue){
                $menu_info_array[$menuValue['menuid']] = $menuValue['menuname'];
            }
			//p($menu_info_array);
            //返回导航和模块菜单关系表
            $navmenuModel = D('Nav_menu');
            //定义菜单表
            $nav_menu_arr = array();

            foreach($navResult as $navKey => $navValue){
                $where = array();
                $where['navid'] = $navValue['navid'];
                $navmenuResult = $navmenuModel->order('sequence')->where($where)->select();
                foreach($navmenuResult as $navmenuValue){
                    $menu_arr = $menu_info_array[$navmenuValue['menuid']];
                    if(in_array($menu_arr,$access_arr)){
                        if($menu_arr){  //如果模块存在，没有隐藏，就显示
                            $nav_menu_arr[$nav_info_array[$navmenuValue['navid']]][] = $menu_arr;
                        }
                    }elseif(C('RBAC_SUPERADMIN') == $userInfo['name']){
                        if($menu_arr){  //如果模块存在，没有隐藏，就显示
                            $nav_menu_arr[$nav_info_array[$navmenuValue['navid']]][] = $menu_arr;
                        }
                    }

                }
            }

            $this->assign('navmenu',$nav_menu_arr);
            //显示的导航条
            $role = D('Role')->where("id=$role_id")->find();       
            $category = $role['show_tab'];        
            $this->assign('category',$category);


            $this->display('Header/nav');

            //节点表 
            $nodeModel = D('Node');
            //来电
            $nodeidResult = $nodeModel->where("name='Telphone'")->find();
            $nodeidTelphone = $nodeidResult['id'];
            if(in_array($nodeidTelphone,$access)){
                $this->telphone();
            }

        }


        public function telphone(){
            $this->display('Header/telphone'); 
        }

    }
?>
