<?php
/**
 * Created by zhangwh1234.
 * User: lihua
 * Date: 17/8/28
 * Time: 下午12:59
 * 送餐员订单情况查看,记录送餐的已读未读,未完成等情况
 */

class CheckSendnameAction extends ModuleAction
{
//返回订单监控的数据
    public function getCheckSendname(){
        $userInfo = $this->userInfo;
        $where = array();
        if($userInfo['rolename'] == '调度员'){
            // 获取分公司
            $company = $this->userInfo ['department'];
            if(!empty($company)){
                $where ['company'] = $company;
            }
        }
        $where['domain'] = $this->getDomain();
        $checksendnameModel = D('Checksendname');
        $checksendname = $checksendnameModel->where($where)->select();
        $this->ajaxReturn($checksendname);
    }

    public function index(){
        $this->listview();
    }


    //测试listview
    public function listview(){

        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName',$moduleName);   //模块名称

        $where = array();


        //启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        //启动列表菜单
        $this->assign('moduleName',$moduleName);

        $this->display('CheckSendname/listview');

    }


    // 获取监测消息
    public function getMessages() {

        // 启动当前模块的模型
        $focus = D ( 'checkmessages' );

        // 查询当前是否有最新的消息
        $filed = array (
            'checkmessagesid',
            'content',
            'time'
        );
        $where = array ();
        $where ['status'] = 0;
        $where ['domain'] = $this->getDomain();
        $newMsg = $focus->field ( $filed )->where ( $where )->limit ( 0, 1 )->select ();

        if (!empty ( $newMsg ) ) {
            $returnMsg = array (
                $newMsg [0] ['content']
            );
            $data = array();
            $data ['status'] = 1;
            $where = array();
            $where ['checkmessagesid'] = $newMsg [0] ['checkmessagesid'];
            $focus->where ( $where )->save ( $data );
            $this->ajaxReturn ( $returnMsg );
        } else {
            $returnMsg = null;
            $this->ajaxReturn ( $returnMsg );
        }
    }

    /**
     * 送餐员查询监测页面
     */
    public function orderview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '地址查询操作');

            // 生成list字段列表
            $listFields = $focus->orderviewFields;
            // 模块的ID
            $moduleId = strtolower($moduleName) . 'id';

            // 建立查询条件
            $where = array();
            $where['checksendnameid'] = $_REQUEST['checksendnameid'];
            $checksendnameResult = $focus->where($where)->find();

            if(!empty($checksendnameResult)){
                $sendname = $checksendnameResult['name'];
            }else{

            }

            //$where ['domain'] = $this->getDomain();

            $where = array();
            $where['sendname'] = $sendname;


            $checkorderformModel = D('checkorderform');

            $total = $checkorderformModel->where($where)->count(); // 查询满足要求的总记录数


            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if(!empty($listMaxRows)){

            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'searchviewaddress' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }

            $listResult = $checkorderformModel->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("checkorderid desc")->select();

            if ($total > 0) {
                $orderHandleArray  = $listResult;
            } else {
                $orderHandleArray  = array();
            }
            $data = array('total' => $total, 'rows' =>  $orderHandleArray);
            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航民
            $this->assign('operName', '送餐监测查询');


            // 生成list字段列表
            $orderviewFields = $focus->orderviewFields;

            //如果存在页数,获取
            if(isset($_REQUEST['pagetype'])){
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            }else{
                $pageNumber = 1;
            }

            $checksendnameid = $_REQUEST ['checksendnameid']; // 查询内容

            $datagrid = array(
                'options' => array(
                    'url' => U('CheckSendname/orderview', array('checksendnameid' => $checksendnameid)),
                    'pageNumber' => $pageNumber
                )
            );
            foreach ($orderviewFields as $key => $value) {
                $header = L($key);
                $datagrid ['fields'] [$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width']);
            }

            $this->assign('datagrid', $datagrid);

            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $orderviewFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'index'); // 定义返回的方法
            //是否存在选中的行号
            if(isset($_REQUEST['rowIndex'])){
                $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
            }else{
                $this->assign ( 'rowIndex',0);
            }

            $this->display('CheckSendname/orderview'); // 查询的结果显示
        }
    }
}