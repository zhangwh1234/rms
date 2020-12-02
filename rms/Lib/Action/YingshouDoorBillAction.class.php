<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/9
 * Time: 下午4:44
 */

class YingshouDoorBillAction extends YingshouAction
{
    // listview
    public function listview()
    {
        if (IS_POST) {

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];
            //获取类型
            $revparType = $this->getRevparType();

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
            //分公司
            $companyStr = $_REQUEST['companyArr'];
            $companyTmp = explode(',', $companyStr);
            $companyArr = array();
            foreach ($companyTmp as $value) {
                if (!empty($value)) {
                    $companyArr[] = $value;
                }
            }

            //连接字符串
            $rmsConnectDb = $this->connectHistoryRmsDb($getDate);

            $domain = $this->getDomain();
            $company_where = '';
            if ($revparType == 'finance') {
                if (count($companyArr) > 0) {
                    foreach ($companyArr as $value) {
                        $company_where = " and a.company = '" . $value . "'";
                    }
                    //$company_where = substr($company_where,0,strlen($company_where)-1);

                }
            }

            // 连接数据库
            $yingshoudoorbillModel = M('accountsbillsmingxi_bj', " ", $rmsConnectDb);
            if (($currentDate != $getDate) || ($currentAp != $getAp)) {
                if ($revparType == 'finance') {
                    $query = "SELECT a.diningsaleid ,a.productstxt ,a.money,b.name ,a.date,a.ap,a.saletime,a.company  FROM rms_diningsale_" . substr($getDate, 5, 2) . " as a join rms_diningsalepayment_" . substr($getDate, 5, 2) . "  as b on a.diningsaleid = b.diningsaleid  where a.domain  = '$domain'  and a.date='$getDate' and a.ap='$getAp' " . $company_where;
                } else {
                    $query = "SELECT a.diningsaleid ,a.productstxt ,a.money,b.name ,a.date,a.ap,a.saletime  FROM rms_diningsale_" . substr($getDate, 5, 2) . " as a join rms_diningsalepayment_" . substr($getDate, 5, 2) . "  as b on a.diningsaleid = b.diningsaleid  where a.domain  = '$domain' and a.company  = '$company' and a.date='$getDate' and a.ap='$getAp'";
                }
            } else {
                if ($revparType == 'finance') {
                    $query = "SELECT a.diningsaleid ,a.productstxt ,a.money,b.name ,a.date,a.ap,a.saletime,a.state,a.company  FROM rms_diningsale as a join rms_diningsalepayment  as b on a.diningsaleid = b.diningsaleid  where a.domain  = '$domain'  and a.date='$getDate' and a.ap='$getAp'" . $company_where;
                } else {
                    $query = "SELECT a.diningsaleid ,a.productstxt ,a.money,b.name ,a.date,a.ap,a.saletime,a.state  FROM rms_diningsale as a join rms_diningsalepayment  as b on a.diningsaleid = b.diningsaleid  where a.domain  = '$domain' and a.company  = '$company' and a.date='$getDate' and a.ap='$getAp'";
                }
            }

            $listResult = $yingshoudoorbillModel->query($query);

            foreach ($listResult as $key => $value) {
                $company = '';
                if ($value['company']) {
                    $company = $value['company'];
                } else {
                    $company = '';
                }
                if ($value['state'] == 1) {
                    $listResult[$key]['state'] = '失效';
                } else {
                    $listResult[$key]['state'] = '正常';
                }
                if ($revparType == 'finance') {
                    $listResult[$key]['revparType'] = 'finance';
                }
            }

            $total = count($listResult); // 查询满足要求的总记录数

            $orderHandleArray['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }
            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $query);
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
            //分公司
            $companyArr = $_REQUEST['companyArr'];

            if (empty($getDate)) {
                $getDate = date('Y-m-d');
                $getAp = $this->getAp();
            }

            $param = array(
                'getDate' => $getDate,
                'getAp' => $getAp,
                'companyArr' => $companyArr,
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
            $revparType = $this->getRevparType();
            $this->assign('revparType', $revparType); //财务权限
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }
    /*
     * 需要返回的字段
     */
    public function returnMainFnPara($focus)
    {
        $ap = array(
            array('name' => '上午'),
            array('name' => '下午'),
        );
        $this->assign('ap', $ap);

        //查询分公司名称，作为门市名称
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];
        $company = '怀南';
        // 接线员的姓名
        $userInfo = $_SESSION['userInfo'];
        $operator = $userInfo['truename'];

        $createFields = $focus->createFields;
        foreach ($createFields['LBL_DOORBILL_INFORMATION'] as $index => $fields) {
            foreach ($fields as $key => $value) {
                if ($key == 'name') {
                    if ($value == 'code') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = '01';
                    }
                    if ($value == 'name') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $company;
                    }
                    if ($value == 'operator') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $operator;
                    }
                    if ($value == 'date') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = date('Y-m-d');
                    }
                    if ($value == 'ap') {
                        $focus->createFields['LBL_DOORBILL_INFORMATION'][$index]['value'] = $this->getAp();
                    }
                }
            }
        }
    }

    // 保存工作餐支付数据等其他数据
    public function save_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['date'] = $getDate;
            $data['note'] = $note;
            $data['doorbillid'] = $record;
            $data['domain'] = $this->getDomain();
            if (!empty($code) && !empty($name)) {
                $doorbillpayModel->create();
                $doorbillpayModel->add($data);
            }
        };

    }

    //更新门市支付
    public function update_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);
        $where = array();
        $where['doorbillid'] = $record;
        $where['date'] = $getDate;
        $where['domain'] = $this->getDomain();
        //先删除订单支付表中的数据
        $doorbillpayModel->where($where)->delete();

        //保存在订单支付表中
        $accountLength = $_REQUEST['accountsLength'];
        for ($i = 1; $i <= $accountLength; $i++) {
            $code = $_REQUEST['accountsCode_' . $i];
            $name = $_REQUEST['accountsName_' . $i];
            $money = $_REQUEST['accountsMoney_' . $i];
            $note = $_REQUEST['accountsNote_' . $i];
            $data = array();
            $data['code'] = $code;
            $data['name'] = $name;
            $data['money'] = $money;
            $data['date'] = $getDate;
            $data['note'] = $note;
            $data['doorbillid'] = $record;
            $data['domain'] = $this->getDomain();
            if (!empty($code) && !empty($name)) {
                $doorbillpayModel->create();
                $doorbillpayModel->add($data);
            }
        };

    }

    //显示工作餐支付信息
    public function get_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['doorbillid'] = $record;
        $where['domain'] = $this->getDomain();
        $doorbillpayResult = $doorbillpayModel->where($where)->select();
        $this->assign('doorbillaccounts', $doorbillpayResult);
    }

    //定义删除从表
    public function delete_slave_table($record, $getDate)
    {
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = 'doorbillpay_';

        // 连接数据库
        $doorbillpayModel = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        $where = array();
        $where['doorbillid'] = $record;
        $where['date'] = $getDate;
        $where['domain'] = $this->getDomain();
        $doorbillpayResult = $doorbillpayModel->where($where)->delete();

    }

    /* 修改记录 */
    public function change()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['record'];
        $getDate = $_REQUEST['getDate'];

        $moduleId = $focus->getPk();

        $where['diningsaleid'] = $record;

        //连接字符串
        $connectionDb = $this->connectHistoryRmsDb($getDate);

        //连接的数据表
        $tableName = 'rms_diningsale_';

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 失效记录
        $data = array();
        $data['state'] = 1;
        $result = $Model->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '失效成功';
            $info['sql'] = $Model->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '失效失败';
            $info['sql'] = $Model->getLastSql();

            $this->ajaxReturn(json_encode($info), 'EVAL');
        }
    }

    /* 恢复记录 */
    public function rechange()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['record'];
        $getDate = $_REQUEST['getDate'];

        $moduleId = $focus->getPk();

        $where['diningsaleid'] = $record;

        //连接字符串
        $connectionDb = $this->connectHistoryRmsDb($getDate);

        //连接的数据表
        $tableName = 'rms_diningsale_';

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $connectionDb);

        // 失效记录
        $data = array();
        $data['state'] = 0;
        $result = $Model->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '恢复成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '恢复失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }
    }

    /**
     * 返回Tree
     */
    public function tree()
    {
        $where = array();
        $where['domain'] = $this->getDomain();
        $compayResult = D('companymgr')->field('companymgrid as id,name')->where($where)->select();
        $compay = array();

        foreach ($compayResult as $value) {
            $company[] = array(
                'id' => $value['name'],
                'text' => $value['name'],
            );
        }
        $this->ajaxReturn($company);
    }

    /**
     * 获取客户收银类型
     */
    public function getPaymentMgr()
    {
        $paymentmgrModel = D('PaymentMgr');
        $where = array();
        $where['company'] = '总部';
        $where['is_dining'] = 1;
        $where['domain'] = $this->getDomain();
        $paymentmgrResult = $paymentmgrModel->field('name')->where($where)->order('code asc')->select();

        $this->assign('paymentmgr', $paymentmgrResult);
        $this->display('payinput');

    }

    /**
     *  修改堂口支付类型，比如扫码还是现金
     */
    public function resetPay()
    {
        $diningpaymenttype = $_REQUEST['diningpaymenttypeone'];
        $diningsaleid = $_REQUEST['diningid'];
        $diningsaleid = (int)$diningsaleid;
        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $rmsConnectDb = $this->connectHistoryRmsDb($getDate);
        

        //连接的数据表
        $tableName = 'rms_diningsalepayment_';

        // 连接数据库
        $Model = M($tableName . substr($getDate, 5, 2), " ", $rmsConnectDb);

        // 失效记录
        $data = array();
        $data['name'] = $diningpaymenttype;
        $where = array();
        $where['diningsaleid'] = $diningsaleid;
        $result = $Model->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '成功';
            $info['sql'] = $Model->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '失败';
            $info['sql'] = $Model->getLastSql();

            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }

}
