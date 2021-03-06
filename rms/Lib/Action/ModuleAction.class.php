<?php

/***
 * 普通模块类，是大部分模块的基类
 */
class ModuleAction extends Action
{

    // 定义用户信息
    public $userInfo;

    /**
     * 类的默认初始化方法
     */
    public function _initialize()
    {
        // 引入用户信息
        $this->userInfo = $_SESSION['userInfo'];

        // 用户如果没有登陆，跳转到登陆页面
        if (!isset($this->userInfo['userid'])) {
            $this->redirect('/Login/again');
        }

        // 不需要验证的功能
        $noAuth = in_array(ACTION_NAME, explode(',', C('NOT_AUTH_MODULE')));

        // 权限验证
        if (C('USER_AUTH_ON') && !$noAuth) {
            import('ORG.Util.RBAC');
            // RBAC::AccessDecision() || $this->error('no rolse');
        }
    }

    public function index()
    {
        $this->listview();
    }

    // 空action 自动跳到listview
    public function _empty()
    {
        //$this->listview ();
    }

    // listview
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

            // 建立查询条件
            $where = array();
            $searchText = urldecode($_REQUEST['searchText']); // 查询内容
            if (!empty($searchText)) {
                foreach ($focus->searchFields as $value) {
                    $where[$value] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                }
                $where['_logic'] = 'OR';
            } else {
                $searchText = $_SESSION['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchText = $_SESSION['searchText' . $moduleName];
                    foreach ($focus->searchFields as $value) {
                        $where[$value] = array(
                            'like',
                            '%' . $searchText . '%',
                        );
                    }
                    $where['_logic'] = 'OR';
                }
            }

            $this->returnWhere($where);

            $where['_logic'] = 'AND';
            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                } else {
                    $pageNumber = 1;
                }
            }

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            //加入其它字段
            foreach ($focus->otherListFiels as $otherFields) {
                array_unshift($selectFields, $otherFields);
            }

            $listResult = $focus->where($where)->field($selectFields)
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->order("$moduleId  desc")->select();

            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }

            $data = array('total' => $total, 'rows' => $orderHandleArray);
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

            //是否有查询字段
            $searchText = $_REQUEST['searchText']; // 查询内容
            if (!empty($searchText)) {
                $searchArray = array('searchText' => $searchText);
                $this->assign('searchIntroduce', '查询内容:' . $searchText);
                $_SESSION['searchText' . $moduleName] = $searchText;
            } else {
                $searchText = $_SESSION['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchArray = array('searchText' => $searchText);
                    $this->assign('searchIntroduce', '查询内容:' . $searchText);
                } else {
                    $_SESSION['searchText' . $moduleName] = '';
                }
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $searchArray),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'halign' => $value['halign'],
                    'width' => $value['width'],
                );
            }
            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            // 执行list的一些其它数据的操作
            $this->listviewOther();
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    /**
     * list表的常规方法
     */
    public function listAction($actionName)
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        // 生成list字段列表
        $listFields = $focus->listFields;
        // 模块的ID
        $moduleId = $focus->getPk();
        // 加入模块id到listHeader中
        // array_unshift($listFields,$moduleNameId);
        $listHeader = $listFields;
        $this->assign("listHeader", $listHeader); // 列表头
        $this->assign('returnAction', 'listview'); // 定义返回的方法

        // 返回查询条件
        $where = $this->returnWhere();

        // 导入分页类
        import('ORG.Util.Page'); // 导入分页类
        $total = $focus->where($where)->count(); // 查询满足要求的总记录数
        // 查session取得page的firstRos和listRows

        if (!isset($_SESSION[$moduleName . 'firstRowlistview'])) {
            $firstRow = $_SESSION[$moduleName . 'firstRowlistview'];
        }

        $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
        if (isset($listMaxRows)) {
            $listRows = $listMaxRows;
        } else {
            $listMaxRows = 15;
        }
        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();

        // 查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);
        $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

        // 从数据中列出列表的数据
        $listviewEntries = $this->getListviewEntity($listResult, $moduleId);

        $datagrid = array(
            'options' => array(
                'url' => U($moduleName . '/listview'),
                'pageNumber' => 1,
                'pageSize' => 13,
                'pageList' => array(),
            ),
        );
        foreach ($listHeader as $value) {
            $header = L($value);
            $datagrid['fields'][$header] = array(
                'field' => $value,
                'align' => 'center',
                'width' => 25,
            );
        }
        $datagrid['fields']['操作'] = array(
            'field' => 'id',
            'width' => 20,
            'align' => 'center',
            'formatter' => $moduleName . $actionName . 'Module.operate',
        );
        $this->assign('datagrid', $datagrid);

        $this->assign('moduleId', $moduleId);
        $this->assign('listEntries', $listviewEntries);
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('returnAction', 'listview'); // 返回的 action
        $this->assign('listLinkField', $focus->listLinkField); // 列表快捷字
    }

    // listview的其它的一些操作
    public function listviewOther()
    {
    }

    //通用查询界面
    public function searchInput()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);
        $this->display('Module/searchinput');
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
        $this->returnMainFnPara();

        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 模块的ID
        $moduleNameId = strtolower($moduleName) . 'id';
        // 返回缓存blocks
        //$moduleBlocks = F($moduleName . 'Blocks');
        if (!empty($moduleBlocks)) {
            $this->blocks = $moduleBlocks;
        } else {
            // 返回新建区块和字段
            $this->blocks = $focus->createBlocks();
            // 缓存blocks
            //F($moduleName . 'Blocks', $this->blocks);
        }

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

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where = array();
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();
        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点
        $this->assign('record', $record); // 订单记录号
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('blocks', $blocks);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara();

        // 返回从表的内容
        $this->get_slave_table($record);

        $this->display($moduleName . '/editview');

    }

    /**
     * *
     * 定义复制数据的页面操作
     */
    public function duplicateview()
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

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();

        // 返回区块
        $blocks = $focus->editBlocks($result);

        $this->assign('info', $result);
        $this->assign('blocks', $blocks);
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('record', $record); // 订单记录号
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara();

        // 返回从表的内容
        $this->get_slave_table($record);

        $this->display($moduleName . '/duplicateview');

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

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction', $returnAction);

        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST['record'];
        $where[$moduleId] = $record;

        // 返回模块的行记录
        $result = $focus->where($where)->find();

        // 返回区块
        $blocks = $focus->detailBlocks($result);

        $this->assign('info', $result);
        $this->assign('record', $record);
        $this->assign('blocks', $blocks);
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']); //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table($record);
        $this->display($moduleName . '/detailview');
    }

    /* 弹出选择窗口 */
    public function popupview()
    {

        // 取得模块的名称
        $moduleName = I('module');

        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        // 取得父窗口的表格行数
        $row = $_REQUEST['row'];

        // 生成list字段列表
        $listFields = $focus->listFields;
        // 模块的ID
        $moduleId = $focus->getPk();
        // 加入模块id到listHeader中
        // array_unshift($listFields,$moduleNameId);
        $listHeader = $listFields;
        $this->assign("listHeader", $listHeader); // 列表头
        $this->assign('returnAction', 'listview'); // 定义返回的方法

        // 导入分页类
        import('ORG.Util.Page'); // 导入分页类
        $total = $focus->count(); // 查询满足要求的总记录数
        // 查session取得page的firstRos和listRows

        if (!isset($_SESSION[$moduleName . 'firstRowlistview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowlistview'];
        }

        // var_dump($_SESSION['test']);
        $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
        if (isset($listMaxRows)) {
            $Page->listRows = $listMaxRows;
        } else {
            $listMaxRows = 15;
        }
        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();

        // 查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);
        $listResult = $focus->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

        // 从数据中列出列表的数据
        $listviewEntries = $this->getListviewEntity($listResult, $moduleId);

        $this->assign('moduleId', $moduleId);
        $this->assign('listEntries', $listviewEntries);
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('returnAction', 'listview'); // 返回的 action

        // $this->display('Module/popupview');
    }

    /* 查询 */
    public function searchview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 如果是从listview进入的，必须删除session['where']
        if (isset($_REQUEST['delsession'])) {
            unset($_SESSION['searchOption' . $moduleName]);
            unset($_SESSION['searchText' . $moduleName]);
        }

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName);

        // 启动列表菜单
        if ($this->searchviewMenuPath) {
            $this->display('Module/searchviewmenu');
        } else {
            $this->display($moduleName . '/searchviewmenu');
        }

        $this->searchviewMenu();

        // 生成list字段列表
        $listFields = $focus->listFields;
        // 模块的ID
        $moduleId = $focus->getPk();

        // 加入模块id到listHeader中
        // array_unshift($listFields,$moduleNameId);
        $listHeader = $listFields;
        $this->assign("listHeader", $listHeader); // 列表头
        $this->assign('returnAction', 'searchview'); // 定义返回的方法

        // 建立查询条件
        $where = array();
        $searchOption = $_REQUEST['searchOption']; // 查询项目
        $searchText = urldecode($_REQUEST['searchText']); // 查询内容
        if (isset($searchOption) && isset($searchText)) {
            if ($searchOption == '全部') { // 如果是全部，那么全都要查询
                foreach ($focus->searchFields as $value) {
                    $where[$value] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                }
                $where['_logic'] = 'OR';
                $_SESSION['searchOption' . $moduleName] = $searchOption;
                $_SESSION['searchText' . $moduleName] = $searchText;
            } else {
                $where[$searchOption] = array(
                    'like',
                    '%' . $searchText . '%',
                );
                $this->assign('searchOptionValue', $searchOption);
                $this->assign('searchTextValue', $searchText);
                $_SESSION['searchOption' . $moduleName] = $searchOption;
                $_SESSION['searchText' . $moduleName] = $searchText;
            }
        } else {
            if (isset($_SESSION['searchOption' . $moduleName], $_SESSION['searchText' . $moduleName])) {
                $where[$_SESSION['searchOption' . $moduleName]] = array(
                    'like',
                    '%' . $_SESSION['searchText'] . $moduleName . '%',
                );
                $this->assign('searchOptionValue', $_SESSION['searchOption' . $moduleName]);
                $this->assign('searchTextValue', $_SESSION['searchText' . $moduleName]);
            }
        }

        // 导入分页类
        import('ORG.Util.Page'); // 导入分页类
        $total = $focus->where($where)->count(); // 查询满足要求的总记录数
        // 查session取得page的firstRos和listRows
        if (isset($_SESSION[$moduleName . 'firstRowSearchview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowSearchview'];
        }
        $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
        if (isset($listMaxRows)) {
            $Page->listRows = $listMaxRows;
        } else {
            $listMaxRows = 15;
        }

        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();

        // 查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);
        $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();
        // var_dump($listResult);
        // var_dump($focus->getLastSql());
        // 从数据中列出列表的数据
        $listviewEntries = $this->getListviewEntity($listResult, $moduleId);

        $this->assign('moduleId', $moduleId);
        $this->assign('listEntries', $listviewEntries);
        $this->assign('page', $show); // 赋值分页输出

        $searchOption = $focus->searchFields;
        $this->assign('searchOption', $searchOption);
        $this->assign('returnAction', 'searchview'); // 定义返回的方法

        if ($this->searchviewPath) {
            $this->display('Module/searchview');
        } else {
            $this->display($moduleName . '/searchview');
        }
    }

    /**
     * searchview的菜单
     */
    public function searchviewMenu()
    {
    }

    // 重新组装数据
    public function getListviewEntity($listResult, $moduleId)
    {
        $listBlock = array();
        // 开始
        foreach ($listResult as $listValue) {
            $id = $listValue[$moduleId];
            $listBlock[$id] = $listValue;
        }
        return $listBlock;
    }

    /* 一般顺序表记录的保存 */
    public function insert()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 回调自动完成的函数
        $auto = $this->autoParaInsert();
        $focus->setProperty("_auto", $auto);

        // 保存主表
        $result = $focus->create();

        if (!$result) {
            exit($focus->getError());
        }
        $result = $focus->add();

        if (!$result) {
            $info['status'] = 0;
            $info['info'] = '保存数据不成功！';
            $info['sql'] = $focus->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->save_slave_table($record);

        // 如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
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

        $moduleId = $focus->getPk();

        $where[$moduleId] = $record;
        // 删除记录
        $result = $focus->where($where)->delete();

        //删除从表
        $this->delete_slave_table($record);

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

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate();
        $focus->setProperty("_auto", $auto);
        // 保存主表
        $focus->create();

        $where = array();
        $where[$moduleId] = $record;
        $result = $focus->where($where)->save();

        // 新写的保存从表方案
        $slaveResult = $this->update_slave_table($record);

        $return['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'rowIndex' => $_REQUEST['rowIndex'], 'pagetype' => $pagetype,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /**
     * *
     * 复制记录
     */
    public function duplicate()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 返回的页面
        $returnAction = $_REQUEST['returnAction'];

        // 回调自动完成的函数
        $auto = $this->autoParaInsert();
        $focus->setProperty("_auto", $auto);

        // 保存主表
        $result = $focus->create();
        if (!$result) {
            exit($focus->getError());
        }
        $result = $focus->add();

        if (!$result) {
            $this->error('保存数据不成功！');
        }

        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->duplicate_slave_table($record);

        // 如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;
        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record,
            'returnAction' => $returnAction,
        ));
        $return = $detailviewUrl;
        $this->ajaxReturn(json_encode($return), 'EVAL');
    }

    // 定义保存从表
    public function save_slave_table($record)
    {
    }

    // 定义更新从表
    public function update_slave_table($record)
    {
    }

    //定义删除从表
    public function delete_slave_table($record)
    {

    }

    // 保存复制表
    public function duplicate_slave_table($record)
    {
    }

    // 定义返回从表的数据
    public function get_slave_table($record)
    {
    }

    // 定义一个空的函数，用于返回主程序需要的一些参数
    public function returnMainFnPara()
    {
    }

    // 返回自定义的list的select语句
    public function getListQuery($list_fields)
    {
    }

    public function getFocusFields()
    {
        return "";
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $data = array(
            array(
                'domain',
                $this->getDomain(),
            ),
        );
        return $data;
    }

    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        $data = array(
            array(
                'domain',
                $this->getDomain(),
            ),
        );
        return $data;
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

    // 根据模块名称，取得导航名称
    public function getTab($module_name)
    {
        // 根据模块名查找模块ID
        $module_model = D('module');
        $module_result = $module_model->field('moduleid')->where("name='$module_name'")->find();
        $moduleid = $module_result['moduleid'];

        // 根据模块ID,查找关联的tabid
        $tab_module_model = M('tab_module_rel');
        $tab_module_result = $tab_module_model->field('tabid')->where("moduleid=$moduleid")->find();
        $tabid = $tab_module_result['tabid'];

        // 取得tab的名称
        $tab_model = D('tab');
        $tab_result = $tab_model->field('tab_label')->where("tabid=$tabid")->find();
        $tab_name = $tab_result['tab_label'];

        // 返回导航名称
        return $tab_name;
    }

    // 获取当前时间的午别
    public function getAp()
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

    /**
     * 显示错误的页面
     */
    public function errorview($message, $jumpUrl = '', $ajax = false)
    {
        $this->message = $message;
        $this->jumpUrl = $jumpUrl;
        $this->waitSecond = 3;
        $this->display('Public/error');
    }

    /**
     * 返回listview的where
     */
    public function returnWhere(&$where)
    {
        //$where ['domain'] = $this->getDomain()
        if (count($where) == 0) {
            $where['domain'] = $this->getDomain();
        } else {
            $temp = $where;
            $map = array();
            $map['_complex'] = $temp;
            $map['domain'] = $this->getDomain();
            $where = $map;
        }

    }

    /**
     * 基础方法,获取订单的连接数据库
     */
    public function connectRmsDb($getDate = '')
    {

        $db_type = 'mysql';
        $db_user = C('db_user');
        $db_pwd = C('db_pwd');
        $db_host = C('db_host');
        $db_port = C('db_port');

        $domain = $this->getDomain();
        if ($domain == 'bj.lihuaerp.com') {
            $db_name = 'rms';
        } else {
            $db_name = 'czrms_' . substr($getDate, 0, 4);
        }

        $connectionDns = "mysql://$db_user:$db_pwd@$db_host:$db_port/$db_name";

        return $connectionDns;
    }

    //php获取中文字符拼音首字母
    public function getFirstCharter($str)
    {
        if (empty($str)) {return '';}
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }

        $s1 = iconv('UTF-8', 'GBK', $str);
        $s2 = iconv('GBK', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }

        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }

        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }

        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }

        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }

        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }

        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }

        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }

        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }

        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }

        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }

        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }

        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }

        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }

        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }

        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }

        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }

        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }

        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }

        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }

        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }

        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }

        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        } else if (ord($s) >= 48 && ord($s) <= 57) { //数字开头
            $aa = @iconv_substr($s, 0, 1, 'utf-8');
            switch ($aa) {
                case 1:
                    return "Y";
                case 2:
                    return "E";
                case 3:
                    return "S";
                case 4:
                    return "S";
                case 5:
                    return "W";
                case 6:
                    return "L";
                case 7:
                    return "Q";
                case 8:
                    return "B";
                case 9:
                    return "J";
                case 0:
                    return "L";
            }
        } else if (ord($s) >= 65 && ord($s) <= 90) { //大写英文开头
            return substr($s, 0, 1);
        } else if (ord($s) >= 97 && ord($s) <= 122) { //小写英文开头
            return strtoupper(substr($s, 0, 1));
        } else {
            return iconv_substr($s0, 0, 1, 'utf-8');
            //中英混合的词语，不适合上面的各种情况，因此直接提取首个字符即可
        }

        return null;
    }

}
