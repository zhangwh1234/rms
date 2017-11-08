<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2019/1/25
 * Time: 3:22 PM
 * 营收收入管理
 */

class YingshouIncomeMgrAction extends YingshouAction{

    /*
     * 需要返回的字段
     */
    public function returnMainFnPara()
    {
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午')
        );
        $this->assign('ap',$ap);
        $this->assign('currentDate',date('Y-m-d'));
        $this->assign('currentAp',$this->getAp());

        $type = array(
            array('name' => '还欠')
        );
        $this->assign('operation',$type);
    }

    //根据客户代码，查询客户名称
    public function getAccountsByCode()
    {
        //查询客户，显示客户
        $code = $_REQUEST['code'];
        $paymentmgrModel = D('PaymentMgr');
        $where = array();
        $where['code'] = $code;
        $where['domain'] = $this->getDomain();
        $paymentResult = $paymentmgrModel->field('name')->where($where)->find();
        //显示客户的账户
        $paymentResult['accounts'] = $this->fetch('accountbill');
        $this->ajaxReturn($paymentResult, 'JSON');
    }

    //弹出客户选择窗口
    public function popupAccountsview(){
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
            $row = $_REQUEST ['row'];

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
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset ($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("accountsid desc")->select();

            $orderHandleArray ['total'] = count($listResult);
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
                    'pageSize' => 10
                )
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid ['fields'] [$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width']
                );
            }

            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'PopupAccountsviewModule.operate'
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST ['row'];
            $this->assign('row', $row);  //返回点击的订购商品行

            $this->display('YingshouIncomeMgr/popupAccountsview');
        }
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
            array(
                'code',
                $_REQUEST['paymentcode'],
            )
        );
        return $data;
    }
}