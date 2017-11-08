<?php
    /**
    * 用户管理
    */
    class UserAction extends ModuleAction{
        /**
        * 配置组织部门的页面
        */
        public function editviewUserOrganization(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName',$navName);         //导航名称

            $where = array();
            $where['domain'] = $this->getDomain();
            //读取节点
            $field = array('id','name','pid');
            $organization = D('organization')->field($field)->where($where)->select();

            //取得权限的ID
            $this->uid = $_REQUEST['record'];
            $userorganization = D('userorganization')->where(array('user_id'=>$this->uid))->getField('organization_id',true);

            $this->userorganization = node_merge($organization,$userorganization);

            $this->display();
        }

        /**
        * 保存组织部门
        */
        public function editUserOrganization(){
            //权限号
            $uid = $_REQUEST['uid'];
            //删除以前的数据
            $userorganization = D('userorganization');
            $userorganization->where(array('user_id' => $uid))->delete();


            $data = array();
            foreach ($_POST['organize'] as $v){
                $tmp = explode('_',$v);
                $data[] = array(
                'user_id' => $uid,
                'organization_id' => $tmp[0],
                'level' => $tmp[1]
                );
            }

            if($userorganization->addAll($data)){
                // 生成查看的url
                $detailviewUrl = U ( 'detailviewOrganization', array (
                    'uid' => $uid
                ) );
                $return = $detailviewUrl;
                $info['status'] = 1;
                $info['info'] ='保存成功' ;
                $info['url'] = $return;
                $this->ajaxReturn($info,'JSON');
            }else{
                $this->error('修改失败!');
            }

        }

        /**
        * 返回保存的企业组织表单
        */
        public function detailviewOrganization(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getnavName($moduleName);
            $this->assign('navName',$navName);         //导航名称

            $where = array();
            $where['domain'] = $this->getDomain();
            //读取节点
            $field = array('id','name','pid');
            $organization = D('organization')->field($field)->where($where)->select();

            //取得权限的ID
            $this->uid = $_REQUEST['uid'];
            $userorganization = D('userorganization')->where(array('user_id'=>$this->uid))->getField('organization_id',true);
    
            $this->userorganization = node_merge($organization,$userorganization);
            $this->display();
        }

        //编辑数据的页面editview
        public function editview(){

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName',$navName);         //导航民

            //模块的ID
            $moduleId = $focus->getPk();

            //取得记录ID
            $record = $_REQUEST['record'];
            $where[$moduleId] = $record;

            $fields = array(
            'userid','name','password','password as passwordtwo','truename'
            );
            //返回模块的行记录
            $result = $focus->field($fields)->where($where)->find();

            //返回区块
            $blocks = $focus->editBlocks($result);

            $this->assign('blocks',$blocks);    
            $this->assign('fieldsFocus',$focus->fieldsFocus);  //指定字段获得焦点

            $this->assign('record',$record);  //订单记录号

            //取得返回的是列表还是查询列表
            $returnAction = $_REQUEST['returnAction'];
            $this->assign('returnAction',$returnAction);

            //回调主程序需要的参数,比如下拉框的数据
            $this->returnMainFnPara();

            //返回从表的内容
            $this->get_slave_table($record);

            $this->display($moduleName.'/editview');


        }



        //返回一些其他的数据,比如下拉列表框等的数据
        public function returnMainFnPara(){
            //角色表的信息
            $roleUserModel = D('role_user');
            //返回角色表的信息
            $roleModle = D('Role');
            $where = array();
            $where['user_id'] = $_REQUEST['record'];
            $result = $roleUserModel->where($where)->find();
            if(!empty($result)){
                $role_id = $result['role_id'];
                $where = array();
                $where['id'] = $role_id;
                $result = $roleModle->where($where)->find();
                //返回当前用户的角色
                $this->roleCurrent = $result;
            }
            //返回所有的角色
            $where = array();
            $where['domain'] = $this->getDomain();
            $this->role = $roleModle->where($where)->select();

        }




        //定义保存从表
        public function save_slave_table($record){
            //更新用户权限表
            $userRoleModel = D('role_user');
            //删除原因的记录
            $userRoleModel->where("user_id=$record")->delete();
            //保存用户权限的数据
            $data['user_id'] = $record;
            $data['role_id'] = $_REQUEST['role_id'];
            $userRoleModel->create();
            $userRoleModel->add($data);
            //获得角色的名称
            $roleModel = D('role');
            $where['id'] = $data['role_id'];
            $roleResult = $roleModel->where($where)->find();

            //修改用户表的职务
            $data = array();
            $data['rolename'] = $roleResult['remark'];
            $userModel = D('User');
            $userModel->where("userid=$record")->save($data);
            //$userModel->getLastSql()
        }

        //定义更新从表
        public function update_slave_table($record){
            //更新用户权限表
            $userRoleModel = D('role_user');
            //删除原因的记录
            $userRoleModel->where("user_id=$record")->delete();
            //保存用户权限的数据
            $data['user_id'] = $record;
            $data['role_id'] = $_REQUEST['role_id'];
            $userRoleModel->create();
            $userRoleModel->add($data);
            //获得角色的名称
            $roleModel = D('role');
            $where['id'] = $data['role_id'];
            $roleResult = $roleModel->where($where)->find();

            //修改用户表的职务
            $data = array();
            $data['rolename'] = $roleResult['remark'];
            $userModel = D('User');
            $userModel->where("userid=$record")->save($data);
        }

        //修改用户密码页面
        public function changeCodeView(){

            //返回用户userid
            $userid =  $_SESSION['userid'];
            $this->assign('recordchangecode',$userid);

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->display($moduleName.'/changecodeview');
        }

        //修改用户密码
        public function changeCode(){
            $userId = $_REQUEST['record'];
            $password = $_REQUEST['password'];

            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称

            //启动当前模块的模型
            $focus = D($moduleName);

            $where = array();
            $where['userid'] = $userId;
            $data = array();
            $data['password'] = $password;
            $focus->where($where)->save($data);

        }
    }
?>
