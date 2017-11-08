<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/5/19
 * Time: 下午12:30
 * 工作餐交账模块，实现工作餐的金额交账和结账
 */

class YingshouWorklunchAction extends YingshouAction
{

    /*
     * 需要返回的字段
     */
    public function returnMainFnPara()
    {
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午'),
        );
        $this->assign('ap', $ap);
        $this->assign('currentDate', date('Y-m-d'));
        $this->assign('currentAp', $this->getAp());
    }

    /* 弹出工作餐选择窗口 */
    public function popupWorklunchview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'worklunchmgr';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupWorklunchFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            //$where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $popupModule->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("worklunchmgrid desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'worklunchmgr';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupWorklunchFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupWorklunchview'),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true,
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => 'YingshouPopupWorklunchviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouWorklunch/popupworklunchview');
        }
    }

    /**
     * 获取工作餐
     */
    public function getWorklunchByCode()
    {
        //代码
        $worklunchcode = $_REQUEST['code'];

        $worklunchModel = D('worklunchmgr');
        $where = array();
        $where['code'] = $worklunchcode;
        $worklunchResult = $worklunchModel->where($where)->find();
        if ($worklunchResult) {
            $name = $worklunchResult['name'];
        } else {
            $name = '';
        }

        $data = array();
        $data['name'] = $name;
        $this->ajaxReturn($data);
    }

    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $data = array(
            array('code',
                $_REQUEST['worklunchcode']),
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

    // 保存工作餐支付数据等其他数据
    public function save_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'worklunchpay_';

        // 连接数据库
        $worklunchpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            if (empty($note)) {
                $note = '结账输入';
            }
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['note'] = $note;
            $data['date'] = $getDate;
            $data['worklunchid'] = $record;
            if (!empty($code) && !empty($name)) {
                $worklunchpayModel->create();
                $worklunchpayModel->add($data);
            }
        };

    }

    //更新工作餐支付
    public function update_slave_table($record)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'worklunchpay_';

        // 连接数据库
        $worklunchpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['date'] = $getDate;
//$where['domain'] = $this->getDomain();
        //先删除订单支付表中的数据
        $worklunchpayModel->where($where)->delete();

        $where = array();
        $where['date'] = $getDate;
        //$where['domain'] = $this->getDomain();
        $worklunchpayResult = $worklunchpayModel->where($where)->select();
        $this->assign('worklunchaccounts', $worklunchpayResult);
    }

    //显示工作餐支付信息
    public function get_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'worklunchpay_';

        // 连接数据库
        $worklunchpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['worklunchid'] = $record;
        $where['date'] = $getDate;
        //$where['domain'] = $this->getDomain();
        $worklunchpayResult = $worklunchpayModel->where($where)->select();
        $this->assign('worklunchaccounts', $worklunchpayResult);
    }

    //定义删除从表
    public function delete_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'worklunchpay_';

        // 连接数据库
        $worklunchpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['worklunchid'] = $record;
        $where['date'] = $getDate;
        //$where['domain'] = $this->getDomain();
        $worklunchpayResult = $worklunchpayModel->where($where)->delete();
    }

    //弹出客户支付选择窗口
    public function popupAccountsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Accounts';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("accountsid desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'YingshouAccounts';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupAccountsview'),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true,
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'PopupAccountsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouWorklunch/popupAccountsview');
        }
    }

}
