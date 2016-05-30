<?php
    /**
    * 分送点管理模块，是分公司（配送店）俗称的输血点
    */
    class OrderSecondPointAction extends ModuleAction{

        //列表
        public function listview(){
            if (IS_POST) {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块的模型
                $focus = D($moduleName);

                // 生成list字段列表
                $listFields = $focus->listFields;
                // 模块的ID
                $moduleId = strtolower($focus->getPk());


                // 配送店（分公司）的信息
                // 分公司的名称
                $company = $this->userInfo ['department'];
                $where = array();
                $where ['state'] = array(
                    'notlike',
                    '已%'
                );
                $where ['company'] = $company;
                $where ['_string'] = "length(trim(company)) > 0";
                $where ['domain'] = $_SERVER ['HTTP_HOST'];

                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $focus->where($where)->count(); // 查询满足要求的总记录数

                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ($pageNumber)) {
                    $pageNumber = 1;
                }

                //使用cookie读取rows
                $listMaxRows = $_COOKIE['listMaxRows'];
                if(!empty($listMaxRows)){

                }else{
                    $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
                }

                //订单配送还要显示两个统计数据
                $listMaxRows = $listMaxRows -2;

                // 取得页数
                $_GET ['p'] = $pageNumber;
                $Page = new Page ($total, $listMaxRows);


                // 查session取得page的firstRos和listRows
                if (isset ($_SESSION [$moduleName . 'firstRowlistview'])) {
                    $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
                }


                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

                // 查询模块的数据
                $selectFields = $listFields;
                array_unshift($selectFields, $moduleId);

                $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();
                // 从数据中列出列表的数据
                if (count($listResult) > 0) {
                    $listData ['rows'] = $listResult;
                    $listData ['total'] = $total;
                } else {
                    $listData ['rows'] = array();
                    $listData ['total'] = 0;
                }
                $this->ajaxReturn($listData, 'JSON');

            } else {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块
                $focus = D($moduleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称

                // 启动列表菜单
                //$this->display ( 'OrderHandle/listviewmenu' );
                //获取打印的纸张类型
                $printPageName = cookie('rmsPrintPageName');
                $this->assign('rmsPrintPageName',$printPageName);

                //获取打印机
                $printerName = cookie('rmsPrinterName');
                $this->assign('rmsPrinterName',$printerName);

                // 取得订单的备注字段
                $beizhuorderModel = D('beizhuordermgr'); // 备注表
                $beizhuorderresult = $beizhuorderModel->select();
                $this->assign('beizhuOrderhandle', $beizhuorderresult);
                $this->display('OrderHandle/listview');
            }

        }




    }


?>
