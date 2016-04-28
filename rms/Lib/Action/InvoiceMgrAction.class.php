<?php
    /***
    * 发票管理系统，支持发票的查看，打印等功能
    * 2014-05-30
    */

    class InvoiceMgrAction extends ModuleAction{

        // 列表
        public function listview()
        {
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
                //$where ['company'] = $company;
                //$where ['_string'] = "length(trim(company)) > 0";
                $where ['domain'] = $_SERVER ['HTTP_HOST'];

                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $focus->where($where)->count(); // 查询满足要求的总记录数

                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ($pageNumber)) {
                    $pageNumber = 1;
                }

                if($_SESSION['maxRows']){
                    $listMaxRows = $_SESSION['maxRows'];
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
                //var_dump($focus->getLastSql());
                $orderHandleArray ['total'] = $total;
                if (count($listResult) > 0) {
                    $orderHandleArray ['rows'] = $listResult;
                } else {
                    $orderHandleArray ['rows'] = array();
                }

                $this->ajaxReturn($orderHandleArray, 'JSON');
            } else {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块
                $focus = D($moduleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称

                $this->display('InvoiceMgr/listview');
            }
        }

        /**
         * 综合查询页面
         */
        public function searchOtherInput(){
            // 返回当前的模块名
            $moduleName = $this->getActionName ();
            $this->assign ( 'moduleName', $moduleName );
            $this->display($moduleName.'/searchotherinput');
        }

        /**
         * 其他条件查询
         */
        public function searchviewOther()
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
                $this->assign('operName', '综合查询操作');

                // 生成list字段列表
                $listFields = $focus->serchListFields;
                // 模块的ID
                $moduleId = strtolower($moduleName) . 'id';


                // 生成list字段列表
                $listFields = $focus->searchListFields;
                // 模块的ID
                $moduleId = $focus->getPk();
                // 加入模块id到listHeader中
                // array_unshift($listFields,$moduleNameId);
                $listHeader = $listFields;

                // 建立查询条件
                $where = array ();
                $searchText = urldecode($_REQUEST ['searchOther']); // 查询内容
                foreach ( $focus->searchFields as $value ) {
                    $where [$value] = array (
                        'like',
                        '%' . $searchText . '%'
                    );
                }
                $where ['_logic'] = 'OR';

                $map['_complex'] = $where;
                $map['domain'] = $_SERVER['HTTP_HOST'];
                // 获取分公司
                $company = $this->userInfo ['department'];
                $map ['company'] = $company;


                // 导入分页类
                import('ORG.Util.Page'); // 导入分页类
                $total = $focus->where($map)->count(); // 查询满足要求的总记录数


                // 取得显示页数
                $pageNumber = $_REQUEST ['page'];
                if (empty ($pageNumber)) {
                    $pageNumber = 1;
                }

                if($_SESSION['maxRows']){
                    $listMaxRows = $_SESSION['maxRows'];
                }else{
                    $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
                }
                //订单配送还要显示两个统计数据
                $listMaxRows = $listMaxRows -2;

                // 取得页数
                $_GET ['p'] = $pageNumber;
                $Page = new Page ($total, $listMaxRows);

                // 查询模块的数据
                foreach($listFields as $key => $value) {
                    $selectFields[] = $key;
                }
                array_unshift ( $selectFields, $moduleId );
                $listResult = $focus->where($map)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->select();

                $orderHandleArray ['total'] = $total;
                if (count($listResult) > 0) {
                    $orderHandleArray ['rows'] = $listResult;
                } else {
                    $orderHandleArray ['rows'] = array();
                }
                $data = array('total' => $total, 'rows' => $listResult);
                $this->ajaxReturn($data);
            } else {
                // 取得模块的名称
                $moduleName = $this->getActionName();
                $this->assign('moduleName', $moduleName); // 模块名称

                // 启动当前模块的模型
                $focus = D($moduleName);

                // 取得对应的导航名称
                $navName = $focus->getNavName($moduleName);
                $this->assign('navName', $navName); // 导航名称

                $this->assign('operName', '综合查询');

                $searchOther = urldecode($_REQUEST ['searchTextOther']); // 查询内容
                $this->assign('searchOther', $searchOther);
                $this->assign('searchIntroduce', '查询内容：' . $searchOther);
                $this->assign('returnAction', 'searchviewOther'); // 定义返回的方法
                $this->display('InvoiceMgr/searchviewother'); // 查询的结果显示
            }

        }

    }
?>