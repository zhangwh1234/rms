<?php

/***
 * 今日菜单，显示今天菜单的内容
 */
class TodayMenuAction extends ModuleAction
{

    /**
     * 首页
     */
    public function index()
    {
        $this->detailview();
    }

    //查看数据的页面
    public function detailview()
    {

        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块
        $focus = D($moduleName);

        //取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName);         //导航民

        //取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);


        //模块的ID
        $moduleId = $focus->getPk();

        //返回新建区块和字段
        //$blocks = $focus->detailBlocks();

        //回调主程序需要的参数,比如下拉框的数据
        //$this->returnMainFnPara();

        //取得记录ID


        //取得查询的日期
        if (isset($_REQUEST['date'])) {
            $date = $_REQUEST['date'];
            $where['date'] = $date;
        } elseif (isset($_REQUEST['record'])) {
            $record = $_REQUEST['record'];
            $where[$moduleId] = $record;
        } else {
            //使用当期日期
            $date = date('Y-m-d');
            $where['date'] = $date;
        }

        $where['domain'] = $_SERVER['HTTP_HOST'];

        //返回模块的行记录
        $result = $focus->where($where)->find();
        //设置日期
        if (isset($_REQUEST['record'])) {
            $todaymenudate = $result['date'];
        } elseif (isset($_REQUEST['date'])) {
            $todaymenudate = $_REQUEST['date'];
        } else {
            $todaymenudate = date('Y-m-d');
        }


        //返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('blocks', $blocks);
        $this->assign('record', $record);
        $this->assign('todaymenudate', $todaymenudate);


        //返回从表的内容
        $this->get_slave_table($record);

        $this->display($moduleName . '/detailview');

    }


    //编辑数据的页面
    public function editview()
    {

        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块
        $focus = D($moduleName);

        //取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName);         //导航民

        //取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);


        //模块的ID
        $moduleId = $focus->getPk();

        //返回新建区块和字段
        //$blocks = $focus->detailBlocks();

        //回调主程序需要的参数,比如下拉框的数据
        //$this->returnMainFnPara();

        //取得记录ID


        //取得查询的日期
        if (isset($_REQUEST['date'])) {
            $date = $_REQUEST['date'];
            $where['date'] = $date;
        } elseif (isset($_REQUEST['record'])) {
            $record = $_REQUEST['record'];
            $where[$moduleId] = $record;
        } else {
            //使用当期日期
            $date = date('Y-m-d');
            $where['date'] = $date;
        }

        //返回模块的行记录
        $result = $focus->where($where)->find();
        //设置日期
        if (isset($_REQUEST['record'])) {
            $todaymenudate = $result['date'];
        } elseif (isset($_REQUEST['date'])) {
            $todaymenudate = $_REQUEST['date'];
        } else {
            $todaymenudate = date('Y-m-d');
        }


        //返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('blocks', $blocks);
        $this->assign('record', $record);
        $this->assign('todaymenudate', $todaymenudate);


        //返回从表的内容
        $this->get_slave_table($record);

        $this->display($moduleName . '/editview');

    }


    // 定义一个空的函数，用于返回主程序需要的一些参数
    public function returnMainFnPara() {
        $this->assign('todaymenudate',date('Y-m-d'));
    }

    /* 一般顺序表记录的保存 */
    public function insert()
    {
        //返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        //回调自动完成的函数
        $auto = $this->autoParaInsert();
        $focus->setProperty("_auto", $auto);

        $where['date'] = $_REQUEST['date'];
        $where['domain'] = $_SERVER['HTTP_HOST'];

        $result = $focus->where($where)->find();
        if ($result) {
            $info['status'] = 0;
            $info['info'] =  '菜单日期重复，不能保存！' ;
            $this->ajaxReturn(json_encode($info),'EVAL');
            return;
        }

        //保存主表
        $result = $focus->create();
        if (!$result) {
            exit($focus->getError());
        }
        $result = $focus->add();

        if (!$result) {
            $info['status'] = 0;
            $info['info'] =  '保存数据不成功！' ;
            $this->ajaxReturn($info,'JSON');
        }

        //取得保存的主键
        $record = $result;

        //新写的保存从表方案
        $result = $this->save_slave_table($record);

        $returnAction = $_REQUEST['returnAction'];
        //如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;

        // 生成查看的url
        $detailviewUrl = U ( "$moduleName/detailview", array (
            'record' => $record,'returnAction'=>$returnAction
        ) );
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info),'EVAL');

    }
}

?>
