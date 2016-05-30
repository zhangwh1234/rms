<?php
    //启动登陆
    class LoginAction extends Action{
        public function index(){
            if(!isset($_SESSION['userid'])) {
                require APP_PATH.'Conf/datapath.php';
                $HTTP_POST = $_SERVER['HTTP_HOST'];
                $this->assign('city',$rmsDataPath[$HTTP_POST]['CITY']);
                $this->display();
            }else{
                $currentModule = $_SESSION['currentModule'];  //当前的模块
                if($currentModule){
                    $this->redirect("/$currentModule/index");
                }else{
                    $this->redirect("/Notice/index");
                }
            }
        }

        public function verify(){
            ob_clean();
            import('ORG.Util.Image');
            Image::buildImageVerify(4,1,'png',120,25);
        }

       //登陆验证
        public function login(){

            if(empty($_POST['name'])) {
                $this->error('帐号不能为空！');
            }elseif (empty($_POST['password'])){
                $this->error('密码必须！');
            }elseif (empty($_POST['verify'])){
                $this->error('验证码必须！');
            }
            if($_SESSION['verify'] != md5($_REQUEST['verify'])) {
                  $this->error('验证码错误！');
            }
			
            //实例用户
            $userModel = D('User');
          
            //验证用户名
            $name = $_REQUEST['name'];
            $where = array();
            $where['name'] = $name;
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $userName = $userModel->field('userid,name')->where($where)->find();

            if(empty($userName)){
                $this->error('帐号错误！'); 
            }

            //验证密码
            $password = $_REQUEST['password'];
            $where = array();
            $where['password'] = $password;
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $userPassword = $userModel->field('password')->where($where)->find(); 
            if(empty($userPassword)){
                $this->error('密码错误！'); 
            }
            $userid = $userName['userid'];
            //重新查出用户所有的信息
            $userInfo = $userModel->field('userid,name,rolename,truename')->where("userid=$userid")->find();
   
            //用户的部门
            $userorganizationModel = D('userorganization');
            $where['user_id'] = $userid;
            $where['levle'] = 2;
            $result = $userorganizationModel->where($where)->find();
            //var_dump($userorganizationModel->getLastSql());
            $organization_id = $result['organization_id'];
            //取得部门的名称
            $organizationModel = D('organization');
            $where= array();
            $where['id'] = $organization_id;
            $result = $organizationModel->field('name')->where($where)->find();

            $departmentName = $result['name'];
            if(empty($departmentName)){
               $departmentName = '';
            }
            //加入用户信息,部门名称
            $userInfo['department'] = $departmentName;
            $userInfo['rolename'] = trim($userInfo['rolename']);
            //记录登陆的时间
            $data = array();
            $data['lastlog'] = date('Y-m-d H:i:s');
            $userModel->where("userid=$userid")->save($data);
            
            //写入 session
            $_SESSION['userid'] = $userInfo['userid'];
            $_SESSION['userInfo'] = $userInfo; 
            $_SESSION['verify'] = null;

            //如果登陆验证成功，跳转到首页
            $this->redirect("/Index");            
        }
        

        //退出系统
        public function logout(){
            //if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            session_unset();
            session_destroy();
            $this->success('登出成功！');
            //}else {
            //    $this->error('已经登出！');
            //}
            $this->redirect("/Login/index");
        }
        
        //重新登陆的页面
        public function again(){
            $this->display();
        }

    }
?>
