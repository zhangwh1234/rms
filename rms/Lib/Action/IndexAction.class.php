<?php
class IndexAction extends Action
{
    public function index()
    {
        $userid = $_SESSION['userid'];

        //用户如果没有登陆，跳转到登陆页面
        if (!isset($userid)) {
            $this->redirect("/Login");
        } else {
            //接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            $userName = $userInfo['truename'];
            /*查询角色的来电，打印等功能 */
            //查询角色ID
            $roleuserModel = D('role_user');
            $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
            $roleid = $roleuserResult['role_id'];

            if (isset($roleid)) {
                //查询角色权限
                $role = D('Role')->where("id=$roleid")->find();
                if ($role) {
                    //启动的时候，启动的模块
                    $startModuleArr = explode(',', $role['first_start_module']);
                    if ($startModuleArr[0] == '') {
                        $startModuleArr = array('Notice');
                    } else {
                        $firstStartModule = array();
                        foreach ($startModuleArr as $value) {
                            $firstStartModule[] = L("$value");
                        }
                    }

                } else {
                    $startModuleArr = array();
                }
            } else {
                $startModuleArr = array();
            }

            $firstStartModuleTitle = implode(',', $firstStartModule);
            $this->assign('startModule', $startModuleArr);

            //查询角色的功能
            $accessModel = D('access');
            $where = array();
            $where['role_id'] = $roleid;
            $accessResult = $accessModel->field('node_id')->where($where)->select();
            foreach ($accessResult as $value) {
                $accessArr[] = $value['node_id'];
            }

            //节点表
            $nodeModel = D('node');
            //来电
            $nodeidResult = $nodeModel->where("name='Telphone'")->find();
            $nodeidTelphone = $nodeidResult['id'];
            if (in_array($nodeidTelphone, $accessArr)) {
                $this->TelphoneOn = "开启";
            }

            //来电类型，亿鸿达
            $nodeYeahdone = $nodeModel->where("name='yeahdone'")->find();
            $nodeidYeahdone = $nodeYeahdone['id'];
            if (in_array($nodeidYeahdone, $accessArr)) {
                $this->TelphoneType = "yeahdone";
                $_SESSION['TelphoneType'] = 'yeahdone';
            }
            //来电类型，华旗呼叫中心
            $nodeCCLinkServer = $nodeModel->where("name='CCLinkServer'")->find();
            $nodeidCCLinkServer = $nodeCCLinkServer['id'];
            if (in_array($nodeidCCLinkServer, $accessArr)) {
                $this->TelphoneType = 'CCLinkServer'; //来电类型
                $this->UserName = $userName; //接线员姓名，用户登陆到华旗呼叫中心使用
                $_SESSION['TelphoneType'] = 'CCLinkServer';
            }

            //华旗呼叫中心新版,支持http连接
            //来电类型，华旗呼叫中心
            $nodeCCLinkServer = $nodeModel->where("name='CCLink2008'")->find();
            $nodeidCCLinkServer = $nodeCCLinkServer['id'];
            if (in_array($nodeidCCLinkServer, $accessArr)) {
                $this->TelphoneType = 'CCLink2008'; //来电类型
                $this->UserName = $userName; //接线员姓名，用户登陆到华旗呼叫中心使用
                $_SESSION['TelphoneType'] = 'CCLink2008';
            }
            //华旗呼叫中心新版,支持http连接
            //来电类型，华旗呼叫中心
            $nodeCCLinkServer = $nodeModel->where("name='BJCCLink2019'")->find();
            $nodeidCCLinkServer = $nodeCCLinkServer['id'];
            if (in_array($nodeidCCLinkServer, $accessArr)) {
                $this->TelphoneType = 'BJCCLink2019'; //来电类型
                $this->UserName = $userName; //接线员姓名，用户登陆到华旗呼叫中心使用
                $_SESSION['TelphoneType'] = 'BJCCLink2019';
            }

            //打印功能
            $nodePrinter = $nodeModel->where("name='printer'")->find();
            $nodeidPrinter = $nodePrinter['id'];
            if (in_array($nodeidPrinter, $accessArr)) {
                $this->PrinterOn = "开启11";
                $_SESSION['PrintOn'] = '开启';
            }

            $this->display();
        }
    }

    /**
     * 是否需要启动消息功能
     */
    public function isStart()
    {
        $userid = $_SESSION['userid'];
        //查询角色ID
        $roleuserModel = D('role_user');
        $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
        $roleid = $roleuserResult['role_id'];
        //查询角色权限
        $role = D('Role')->where("id=$roleid")->find();
        if ($role['name'] == 'telname') {
            $res['isStart'] = 1;
            $this->ajaxReturn($res);
        }
        if ($role['name'] == 'contactman') {
            $res['isStart'] = 1;
            $this->ajaxReturn($res);
        }

        if ($role['name'] == 'dispatcher') {
            $res['isStart'] = 1;
            $this->ajaxReturn($res);
        }
        if ($role['name'] == 'customer') {
            $res['isStart'] = 1;
            $this->ajaxReturn($res);
        }

        $res['isStart'] = 0;
        $this->ajaxReturn($res);

    }

    public function _empty()
    {
        header("HTTP/1.0 404 Not Found"); //使HTTP返回404状态码
        $this->display("Public:404");
    }

}
