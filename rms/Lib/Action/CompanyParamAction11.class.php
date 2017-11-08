<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/11/1
 * Time: 下午12:37
 * 公司参数设置
 */

class CompanyParamAction extends ModuleAction{

    /**
     * 第一个页面是编辑页面
     */
    public function index() {
        $this->detailview ();
    }


    // 编辑数据的页面editview
    public function editview() {

        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign ( 'returnAction', $returnAction );


        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $where = array();
        $where ['domain'] = $_SERVER['HTTP_HOST'];

        // 返回模块的行记录
        $result = $focus->where ( $where )->find ();

        $this->assign ( 'info', $result );

        $this->display ( $moduleName . '/editview' );

    }


    // 查看数据的页面
    public function detailview() {
        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign ( 'returnAction', $returnAction );

        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $where = array();
        $where ['domain'] = $_SERVER['HTTP_HOST'];

        // 返回模块的行记录
        $result = $focus->where ( $where )->find ();

        $this->assign ( 'info', $result );

        $this->display ( $moduleName . '/detailview' );
    }


    // 更新记录
    public function update() {

        // 返回当前的模块名
        $moduleName = $this->getActionName ();

        $focus = D ( $moduleName );
        $this->assign ( 'moduleName', $moduleName );
        // 返回的页面
        $returnAction = $_REQUEST ['returnAction'];

        // 取得记录号
        $record = $_REQUEST ['record'];
        $moduleId = $focus->getPk ();

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate ();
        $focus->setProperty ( "_auto", $auto );

        $where = array();
        $where['domain'] = $_SERVER['HTTP_HOST'];

        $data = array();
        $data = $_REQUEST;

        $result = $focus->where ( $where )->save ($data);

        $return ['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U ( "$moduleName/detailview", array (
            'record' => $record,'returnAction'=>$returnAction,
            'rowIndex' => $_REQUEST['rowIndex'],'pagetype' =>$pagetype
        ) );
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info']  = $this->info  . ' 保存成功' ;
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info),'EVAL');
    }
}