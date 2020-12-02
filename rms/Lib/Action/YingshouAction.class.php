<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/16
 * Time: 下午12:15
 * 定义营收系统的基类
 */

class YingshouAction extends ModuleAction
{

    // listview
    public function listview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            //结账日期
            $getDate = $_REQUEST['getDate'];
            //结账午别
            $getAp = $_REQUEST['getAp'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate) || empty($getAp)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb('');
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = $getDate;
            $where['ap'] = $getAp;
            $where['domain'] = $this->getDomain();

            $total = $Model->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            //加入其它字段
            foreach ($focus->otherListFields as $otherFields) {
                array_unshift($selectFields, $otherFields);
            }

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $Model->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION['searchText' . $moduleName]);
                unset($_SESSION[$moduleName . 'page']);
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
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            //结账日期
            $getDate = $_REQUEST['getDate'];
            //结账午别
            $getAp = $_REQUEST['getAp'];

            if (empty($getDate)) {
                $getDate = date('Y-m-d');
                $getAp = $this->getAp();
            }

            $param = array(
                'getDate' => $getDate,
                'getAp' => $getAp,
            );

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $param),
                    'pageNumber' => $pageNumber,
                    'pageSize' => 10,
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'ListviewModule.sendname',
                    );
                } else {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                    );
                }
            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期
            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $getAp);
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    // listview 的第二种，没有午别
    public function listviewTwo()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            //结账日期
            $getDate = $_REQUEST['getDate'];

            //当前日期和当前午别
            $currentDate = date('Y-m-d');
            $currentAp = $this->getAp();
            if (empty($getDate)) {
                $getDate = $currentDate;
                $getAp = $currentAp;
            }

            //连接字符串
            $connectionDb = $this->connectReveueDb('');
            //连接的数据表
            $tableName = $focus->getTableName();
            // 连接数据库
            $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();
            $where['date'] = $getDate;
            $where['domain'] = $this->getDomain();

            $total = $Model->where($where)->count(); // 查询满足要求的总记录数

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST['page'];
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'listview' . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            //加入其它字段
            foreach ($focus->otherListFields as $otherFields) {
                array_unshift($selectFields, $otherFields);
            }

            $listResult = $Model->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId asc")->select(); //lastdatetime desc,

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $Model->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION['searchText' . $moduleName]);
                unset($_SESSION[$moduleName . 'page']);
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
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            if (empty($_REQUEST['getDate'])) {
                $getDate = date('Y-m-d');
            } else {
                $getDate = $_REQUEST['getDate'];
            }
            $param = array(
                'getDate' => $getDate,
            );
            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $param),
                    'pageNumber' => $pageNumber,
                    'pageSize' => 10,
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            foreach ($listFields as $key => $value) {
                $header = L($key);
                if ($key == 'sendname') {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                        'formatter' => $moduleName . 'ListviewModule.sendname',
                    );
                } else {
                    $datagrid['fields'][$header] = array(
                        'field' => $key,
                        'align' => $value['align'],
                        'width' => $value['width'],
                    );
                }

            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //是否存在选中的行号
            if (isset($_REQUEST['rowIndex'])) {
                $this->assign('rowIndex', $_REQUEST['rowIndex']);
            } else {
                $this->assign('rowIndex', 0);
            }

            $this->assign('returnAction', 'listview');
            //当前日期

            $this->assign('getDate', $getDate);
            //当前午别
            $this->assign('getAp', $this->getAp());
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    //生成报数单界面
    public function generalview()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        //当前日期
        $this->assign('cdate', date('Y-m-d'));
        //当前午别
        $this->assign('cap', $this->getAp());
        $this->display($moduleName . '/generalview');
    }

    // 查看数据的页面
    public function detailview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where($where)->find();
        
        // 返回区块
        $blocks = $focus->detailBlocks($result);
 
        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table($record, $getDate);
        $this->display($moduleName . '/detailview');
    }

    // 创建新数据createView
    public function createview()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara($focus);

        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 模块的ID
        $moduleNameId = strtolower($moduleName) . 'id';

        if (!empty($moduleBlocks)) {
            $this->blocks = $moduleBlocks;
        } else {
            // 返回新建区块和字段
            $this->blocks = $focus->createBlocks();
        }

        $this->assign('module', 'create');
        $this->assign('blocks', $this->blocks); // 编辑字段区
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点

        $this->display($moduleName . '/createview');
    }

    // 编辑数据的页面editview
    public function editview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        $getDate = $_REQUEST['getDate'];

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where = array();
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where($where)->find();
        var_dump($Model->getLastSql());

        // 返回区块
        $blocks = $focus->editBlocks($result);

        $this->assign('info', $result);
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点
        $this->assign('record', $record); // 订单记录号
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('blocks', $blocks);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara();

        $this->assign('getDate', $getDate);
        // 返回从表的内容
        $this->get_slave_table($record, $getDate);

        $this->display($moduleName . '/editview');

    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $data = array(
            array(
                'domain',
                $_SERVER['HTTP_HOST'],
            ),
            array(
                'create_time',
                date('Y-m-d H:i:s'),
            ),
        );
        return $data;
    }

    /* 一般顺序表记录的保存 */
    public function insert()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        $getDate = $_REQUEST['date'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaInsert();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $result = $Model->create();

        if (!$result) {
            exit($Model->getError());
        }
        $result = $Model->add();

        if (!$result) {
            $info['status'] = 0;
            $info['info'] = '保存数据不成功！';
            $info['sql'] = $Model->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }
        $sql = $Model->getLastSql();


        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->save_slave_table($record, $getDate);

        // 如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'getDate' => $getDate,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $info['sql'] = $sql;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        $data = array(
            array(
                'domain',
                $_SERVER['HTTP_HOST'],
            ),
            array(
                'update_time',
                date('Y-m-d H:i:s'),
            ),
        );
        return $data;
    }

    // 更新记录
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);
        // 返回的页面
        $returnAction = $_REQUEST['returnAction'];

        // 取得记录号
        $record = $_REQUEST['record'];
        $moduleId = $focus->getPk();

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $Model->create();

        $where = array();
        $where[$moduleId] = $record;
        $result = $Model->where($where)->save();

        // 新写的保存从表方案
        $slaveResult = $this->update_slave_table($record, $getDate);

        $return['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'rowIndex' => $_REQUEST['rowIndex'], 'getDate' => $getDate,
        ));

        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $detailviewUrl;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /* 删除记录 */
    public function delete()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['record'];
        $getDate = $_REQUEST['getDate'];

        $moduleId = $focus->getPk();

        $where[$moduleId] = $record;

        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 删除记录
        $result = $Model->where($where)->delete();

        //删除从表
        $this->delete_slave_table($record, $getDate);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '删除成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '删除失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }
    }

    /**
     * 生成报数单错误,产生的结果,显示一下
     */
    public function resultview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 重新设定订单历史查询的数据库
        $getdate = $_REQUEST['getdate'];
        $module = $_REQUEST['module'];

        $connectionDb = $this->connectReveueDb($getdate);

        // 连接数据库
        $resultModel = M($module . "result", " ", $connectionDb);

        $where = array();
        $where['domain'] = $this->getDomain();
        // 返回模块的记录
        $resultResult = $resultModel->where($where)->select();

        $result = '';
        foreach ($resultResult as $value) {
            $result .= "<p>" . $value['result'] . "</p>";
        }

        $this->assign('result', $result);
        $this->display($moduleName . '/resultview');
    }

    //根据客户代码，查询客户支付名称
    public function getAccountsByCode()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        $code = $_REQUEST['code'];
        $paymentmgrModel = D('PaymentMgr');
        $where = array();
        $where['code'] = $code;
        $where['company'] = array(
            array('eq', '总部'),
            array('eq', $company),
            'or',
        );
        $where['is_shenhe'] = 1;
        $where['domain'] = $this->getDomain();
        $accounts = $paymentmgrModel->field('name')->where($where)->find();
        $this->ajaxReturn($accounts, 'JSON');
    }

    /**
     * 基础方法,获取结账的连接数据库
     * @pama getdate
     * $pama getap
     */
    public function connectReveueDb($getdate = '')
    {
        //为空,返回当前日期
        if (empty($getdate)) {
            $getdate = date('Y-m-d');
        }

        $db_type = 'mysql';
        $db_user = C('db_user');
        $db_pwd = C('db_pwd');
        $db_host = C('db_host');
        $db_port = C('db_port');
        $db_name = 'rms_revenue_' . substr($getdate, 0, 4);

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        return $connectionDns;
    }

    /**
     * 返回历史订单的链接
     */
    public function connectHistoryRmsDb($getdate = '')
    {
        //为空,返回当前日期
        if (empty($getdate)) {
            $getdate = date('Y-m-d');
        }

        $db_type = 'mysql';
        $db_user = C('db_user');
        $db_pwd = C('db_pwd');
        $db_host = C('db_host');
        $db_port = C('db_port');
        $domain = $this->getDomain();
        if ($domain == 'bj.lihuaerp.com') {
            $db_name = 'bjrms_' . substr($getdate, 0, 4);

        } else {
            $db_name = 'czrms_' . substr($getdate, 0, 4);
        }
        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";
        return $connectionDns;
    }

    /**
     * 获取当前时间的午别
     * 由于结账系统的特殊，需要重写上午下午，下午的时间需要延后，让内勤有时间结账
     * @return string $ap
     */
    public function getAp()
    {
        $nowTime = time();
        $splitTime = strtotime('16:00:00'); // 分割的时间
        if (($nowTime - $splitTime) >= 0) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        return $ap;
    }

    /**
     * 获取操作员的权限
     */
    public function getRevparType()
    {
        //定义是哪个结账
        $userid = $_SESSION['userid'];
        //查询角色ID
        $roleuserModel = D('role_user');
        $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
        $roleid = $roleuserResult['role_id'];

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
        //查询分公司结账节点
        $nodeidResult = $nodeModel->where("name='companyRevpar'")->find();
        $nodeidCompanyRevpar = $nodeidResult['id'];
        if (in_array($nodeidCompanyRevpar, $accessArr)) {
            $revparType = 'company';
        }

        //查询财务结账节点
        $nodeidResult = $nodeModel->where("name='financeRevpar'")->find();
        $nodeidFinanceRevpar = $nodeidResult['id'];
        if (in_array($nodeidFinanceRevpar, $accessArr)) {
            $revparType = 'finance';
        }

        return $revparType;
    }

    /**
     * 获取结账数据库的午别，一般是3点，和查看午别不同，查看是16点以后
     */
    public function getDbAp()
    {
        $nowTime = time();
        $splitTime = strtotime('15:30:00'); // 分割的时间
        if (($nowTime - $splitTime) >= 0) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        return $ap;
    }

}
