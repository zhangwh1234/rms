<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/16
 * Time: 下午12:15
 * 定义营收系统的基类
 */

class YingshouAction extends ModuleAction{

    // listview
    public function listview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            $currDate = date('Y-m-d');
            //连接字符串
            $connectionDb = $this->connectReveueDb('');
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName .substr($currDate,5,2) , " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());


            // 建立查询条件
            $where = array();
            $where ['domain'] = $_SERVER ['HTTP_HOST'];

            $total = $Model->where($where)->count(); // 查询满足要求的总记录数

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
            $_SESSION [$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift ( $selectFields, $moduleId );

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray  = $listResult;
            } else {
                $orderHandleArray  = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray,'sql'=>$Model->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION ['searchText' . $moduleName]);
                unset($_SESSION [$moduleName . 'page']);
            }

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            //如果存在页数,获取
            if(isset($_REQUEST['pagetype'])){
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            }else{
                $pageNumber = 1;
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', array()),
                    'pageNumber' => $pageNumber,
                    'pageSize' => 10
                )
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid ['fields'] [$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width']
                );
            }

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if($key == 'sendname'){
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'ListviewModule.sendname'
                    );
                }else{
                    $datagrid ['fields'] [$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width']
                    );
                }

            }

            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate'
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if(isset($_REQUEST['rowIndex'])){
                $this->assign ( 'rowIndex',$_REQUEST['rowIndex']);
            }else{
                $this->assign ( 'rowIndex',0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期
            $this->assign('cdate',date('Y-m-d'));
            //当前午别
            $this->assign('cap',$this->getAp());
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
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

        $currDate = date('Y-m-d');
        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName .substr($currDate,5,2) , " ", $connectionDb);

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign ( 'returnAction', $returnAction );

        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $where [$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where ( $where )->find ();

        // 返回区块
        $blocks = $focus->detailBlocks ( $result );

        $this->assign ( 'info', $result );
        $this->assign ( 'record', $record );
        $this->assign ( 'blocks',$blocks);
        $this->assign ( 'pagenumber',$_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign ( 'rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign ( 'pagetype' , $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table ( $record );
        $this->display ( $moduleName . '/detailview' );
    }

    // 创建新数据createView
    public function createview() {
        // 返回当前的模块名
        $moduleName = $this->getActionName ();

        $focus = D ( $moduleName );
        $this->assign ( 'moduleName', $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara ();

        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction',$returnAction);

        // 模块的ID
        $moduleNameId = strtolower ( $moduleName ) . 'id';
        // 返回缓存blocks,不用缓存,不影响速度
        //$moduleBlocks = F ( $moduleName . 'Blocks' );
        if (! empty ( $moduleBlocks )) {
            $this->blocks = $moduleBlocks;
        } else {
            // 返回新建区块和字段
            $this->blocks = $focus->createBlocks ();
            // 缓存blocks
            F ( $moduleName . 'Blocks', $this->blocks );
        }

        $this->assign ( 'blocks', $this->blocks ); // 编辑字段区
        $this->assign ( 'fieldsFocus', $focus->fieldsFocus ); // 指定字段获得焦点

        $this->display ( $moduleName . '/createview' );
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

        $currDate = date('Y-m-d');
        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName .substr($currDate,5,2) , " ", $connectionDb);


        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $where = array();
        $where [$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where ( $where )->find ();

        // 返回区块
        $blocks = $focus->detailBlocks ( $result );



        $this->assign ( 'info', $result );
        $this->assign ( 'fieldsFocus', $focus->fieldsFocus ); // 指定字段获得焦点
        $this->assign ( 'record', $record ); // 订单记录号
        $this->assign ( 'pagenumber',$_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign ( 'blocks', $blocks);
        $this->assign ( 'rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign ( 'pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara ();

        // 返回从表的内容
        $this->get_slave_table ( $record );

        $this->display ( $moduleName . '/editview' );

    }


}