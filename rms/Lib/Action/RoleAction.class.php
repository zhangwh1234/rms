<?php
    /**
    * 设置角色的类
    */

    class RoleAction extends ModuleAction{

        /**
        * 配置权限的页面
        */
        public function editviewAccess(){
            //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName',$navName);         //导航名称

            //读取节点
            $field = array('id','name','title','pid');
            $node = D('Node')->field($field)->select();

            //取得权限的ID
            $rid = $_REQUEST['rid'];
            $this->assign('rid',$rid);
            
            $field = array('node_id');
            $access = D('Access')->where(array('role_id'=>$rid))->getField('node_id',true);

            $node = node_merge($node,$access);
            $this->assign('node', $node );

            $this->display('Role/editviewaccess');
        }

        /**
        * 保存权限
        */
        public function editAccess(){
            //权限号
            $rid = $_REQUEST['rid'];
    
            //删除以前的数据
            $access = D('access');
            $access->where(array('role_id' => $rid))->delete();
 
            $data = array();
            foreach ($_REQUEST['access'] as $v){
                $tmp = explode('_',$v);
                $data[] = array(
                'role_id' => $rid,
                'node_id' => $tmp[0],
                'level' => $tmp[1]
                );
            }
            if($access->addAll($data)){
                // 生成查看的url
                $detailviewUrl = U ( "Role/detailviewAccess", array (
                    'rid' => $rid
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
        * 返回权限的详情
        */
        public function detailviewAccess(){
           //取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName',$moduleName);   //模块名称   

            //启动当前模块的模型
            $focus = D($moduleName);

            //取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName',$navName);         //导航名称

            //读取节点
            $field = array('id','name','title','pid');
            $node = D('node')->field($field)->select();

            //取得权限的ID
            $rid = $_REQUEST['rid'];
            $this->assign('rid',$rid);
            
            $field = array('node_id');
            $access = D('access')->where(array('role_id'=>$rid))->getField('node_id',true);
 
            $this->node = node_merge($node,$access);

            $this->display('Role/detailviewaccess');
        }

    }
?>
