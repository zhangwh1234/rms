<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/8/13
 * Time: 下午5:41
 * 营收报表系统
 */

class YingshouReportAction extends ModuleAction{
    public function index() {
        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块的模型
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        $this->display ( 'listview' );
    }

    /**
     * 营收营收汇总表
     */
    public  function getHuizongReport(){
        $this->display ( 'YingshouReport/huizonglist' );
    }

    /**
     * 显示营收汇总的报表内容
     */
    public function showHuizongReport(){
        $this->display('YingshouReport/huizong');
    }

    /**
     * 客户往来汇总表
     */
    public function getAccountReport(){
        $this->display ( 'YingshouReport/accountlist' );
    }

    /**
     * 客户往来报表的内容
     */
    public function showAccountReport(){

    }

    /**
     * 客户往来明细表
     */
    public function getAccountMingxiReport(){
        $this->display ( 'YingshouReport/accountmingxilist' );
    }

    /**
     * 客户往来明细表的内容
     */
    public function showAccountMingxiReport(){

    }

    /**
     * 送餐员汇总表
     */
    public function getSendnameHuizongReport(){

    }

    /**
     * 送餐员明细表
     */
    public function showSendnameMingxiReport(){

    }
}