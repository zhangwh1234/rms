<?php
/**
 * 堂口销售模块
 * Created by zhangwh1234.
 * User: apple
 * Date: 17/3/30
 * Time: 上午10:36
 */

class DiningSaleAction extends ModuleAction
{

    /**
     * 默认进入收银模块
     */
    public function index()
    {
        $paymentmgr = $this->getPaymentMgr();
        $this->assign('paymentmgr', $paymentmgr);
        $this->createview();
    }

    /**
     * 收银模块
     */
    public function createview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        $this->display('createview');
    }

    /**
     * 显示收银列表
     * @return array
     */
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
            // 接线员的姓名
            $userInfo = $_SESSION['userInfo'];
            // 获取分公司
            $company = $userInfo['department'];

            $where = array();

            //$where ['ap'] = $this->getAp();
            $where['company'] = $company;
            //$where ['_string'] = "length(trim(company)) > 0";
            $where['domain'] = $this->getDomain();

            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            //计算总金额
            $totalmoney = $focus->where($where)->sum('money');
            if (empty($totalmoney)) {
                $totalmoney = 0.00;
            }

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);

            $listResult = $focus->where($where)->field($selectFields)->select();

            // 从数据中列出列表的数据
            if (count($listResult) > 0) {
                $listData['rows'] = $listResult;
                $listData['total'] = $total;
            } else {
                $listData['rows'] = array();
                $listData['total'] = 0;
            }
            $returnArr = array();
            $returnArr['data'] = $listData;
            $returnArr['totalmoney'] = $totalmoney;
            $this->ajaxReturn($returnArr, 'JSON');

        }
    }

    // 保存的补充数据的回调函数
    public function autoParaInsert()
    {
        //设置午别
        $apTime = date('H');
        if ($apTime > 15) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        //操作员的姓名
        $userInfo = $_SESSION['userInfo'];
        $operator = $userInfo['truename'];
        // 分公司的名称
        $company = $userInfo['department'];
        $auto = array(
            array( //餐厅名字
                'diningname',
                $company,
            ),
            array(
                'date',
                date('Y-m-d'),
            ),
            array(
                'ap',
                $ap,
            ),
            array( //销售时间
                'saletime',
                date('H:i:s'),
            ),
            array(
                'operator',
                $operator,
            ),
            array(
                'company',
                $company,
            ),
            array(
                'domain',
                $this->getDomain(),
            ),
        ); // 最后的修改时间

        return $auto;
    }

    /**
     * 保存附件
     */
    public function save_slave_table($record)
    {

        $diningsaleproductsModel = D('diningsaleproducts');
        $productsTxt = ''; //产品简述
        $totalmoney = ''; //总额
        // 保存地址的数量
        $productsLength = $_REQUEST['productsLength'];
        for ($i = 1; $i <= $productsLength; $i++) {
            $code = $_REQUEST['productsCode_' . $i];
            $name = $_REQUEST['productsName_' . $i];
            $shortname = $_REQUEST['productsShortName_' . $i];
            $price = $_REQUEST['productsPrice_' . $i];
            $number = $_REQUEST['productsNumber_' . $i];
            $money = $_REQUEST['productsMoney_' . $i];
            $data = array();
            $data['diningsaleid'] = $record;
            $data['code'] = $code;
            $data['name'] = $name;
            $data['shortname'] = $shortname;
            $data['price'] = $price;
            $data['number'] = $number;
            $data['money'] = $money;
            $data['domain'] = $this->getDomain();
            if (!empty($name) and !empty($number)) {
                $diningsaleproductsModel->create();
                $diningsaleproductsModel->add($data);
                $productsTxt .= $number . '×' . $shortname . ' ';
                $totalmoney += $number * $price;
            }
        }

        //操作员的姓名
        $userInfo = $_SESSION['userInfo'];
        // 分公司的名称
        $company = $userInfo['department'];

        //将金额等内容保存到主表中
        $diningsaleModel = D('diningsale');
        $where = array();
        $where['domain'] = $this->getDomain();
        $where['ap'] = $this->getAp();
        $where['company'] = $company;
        //产生序号
        $count = $diningsaleModel->where($where)->count();

        $where = array();
        $where['diningsaleid'] = $record;
        $data = array();
        $data['money'] = $totalmoney;
        $data['productstxt'] = $productsTxt;
        $data['sequence'] = $count;
        $diningsaleModel->where($where)->save($data);

        //保存到支付表
        $diningsalepaymentModel = D('diningsalepayment');
        if (!empty($_REQUEST['diningpaymentmoneyone']) && $_REQUEST['diningpaymentmoneyone'] > 0) {
            $data = array();
            $data['diningsaleid'] = $record;
            $data['code'] = 1;
            $data['name'] = $_REQUEST['diningpaymenttypeone'];
            $data['money'] = $_REQUEST['diningpaymentmoneyone'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $diningsalepaymentModel->create();
            $diningsalepaymentModel->add($data);
        }
        //保存支付表2，第二种支付类型
        if (!empty($_REQUEST['diningpaymentmoneytwo']) && $_REQUEST['diningpaymentmoneytwo'] > 0) {
            $data = array();
            $data['diningsaleid'] = $record;
            $data['code'] = 1;
            $data['name'] = $_REQUEST['diningpaymenttypetwo'];
            $data['money'] = $_REQUEST['diningpaymentmoneytwo'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $diningsalepaymentModel->create();
            $diningsalepaymentModel->add($data);
        }

        //将金额保存到diningcollect表中,金额汇总表
        $where = array();
        $where['domain'] = $this->getDomain();
        $where['company'] = $company;
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();

        $diningcollectModel = D('diningcollect');
        $diningcollectResult = $diningcollectModel->where($where)->find();
        if (empty($diningcollectResult)) {
            $data = array();
            $data['domain'] = $this->getDomain();
            $data['company'] = $company;
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['money'] = $totalmoney;
            $diningcollectModel->add($data);
        } else {
            $where = array();
            $where['diningcollectid'] = $diningcollectResult['diningcollectid'];
            $data = array();
            $data['money'] = $totalmoney + $diningcollectResult['money'];
            $diningcollectModel->where($where)->save($data);
        }

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
        $sql = $focus->getLastSql();
        if (!$result) {
            $info['status'] = 0;
            $info['info'] = '保存数据不成功！';
            $info['sql'] = $sql;
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->save_slave_table($record);

        // 如果保存订单都成功，就跳转到查看页面
        $return['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        //返回数据
        $returnData = array();
        $where = array();
        $where['diningsaleid'] = $record;
        $returnData['diningsale'] = $focus->where($where)->find();
        $diningsaleproductsModel = D('diningsaleproducts');
        $returnData['diningsaleproducts'] = $diningsaleproductsModel->where($where)->select();

        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $record; //$return;
        $info['data'] = $returnData;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /**
     * 根据id号,获取数据
     */
    public function getDiningSaleOrder()
    {

        $diningsaleModel = D('diningsale');
        $record = $_REQUEST['id'];
        //返回数据
        $returnData = array();
        $where = array();
        $where['diningsaleid'] = $record;
        $returnData['diningsale'] = $diningsaleModel->where($where)->find();
        $diningsaleproductsModel = D('diningsaleproducts');
        $returnData['diningsaleproducts'] = $diningsaleproductsModel->where($where)->select();

        $info = array();
        $info['data'] = $returnData;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /* 弹出选择窗口 */
    public function popupProductsview()
    {
        $company = $_REQUEST['company'];
        if (empty($company)) {
            $company = '';
        }
        $this->assign('company', $company);

        $row = $_REQUEST['row'];
        $this->assign('row', $row);
        $this->display('DiningSale/selectproductsview');

        return;

        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Products';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

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

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

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
            $popupModuleName = 'Products';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupProductsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupProductsview'),
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
                'formatter' => $moduleName . 'PopupProductsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('DiningSale/popupviewProducts');
        }
    }

    /**
     * 获取产品
     */
    public function getProducts()
    {
        $company = $_REQUEST['company'];
        if (empty($company)) {
            $company = '';
        }
        $domain = $this->getDomain();
        //如果有缓存，就直接返回缓存
        $products_cache = F('diningsaleProducts' . $company . $domain);
        if (!empty($products_cache)) {
            $this->ajaxReturn($products_cache, 'JSON');
        }

        $prodcutsModel = D('products');

        $where = array();
        $where['domain'] = $domain;
        $products = $prodcutsModel->where($where)->select();

        import('@.Extend.ChinesePinyin');
        $Pinyin = new ChinesePinyin();

        //定义返回全部products，以便点击name的搜索code
        $products_all = array();
        $companyArr = array();
        foreach ($products as $value) {
            //赋值
            $products_all[$value['name']] = array(
                'code' => $value['code'],
                'price' => $value['price'],
            );
            //$py = $this->getFirstCharter(trim($value['name']));
            $py = $Pinyin->TransformUcwords(trim($value['name']));
            if (empty($py)) {
                $py = $this->getFirstCharter(trim($value['name']));
            }
            $py = $py[0];
            if ($py == 'A') {
                $A[] = trim($value['name']);
            }

            if ($py == 'B') {
                $B[] = trim($value['name']);
            }

            if ($py == 'C') {
                $C[] = trim($value['name']);
            }

            if ($py == 'D') {
                $D[] = trim($value['name']);
            }

            if ($py == 'E') {
                $E[] = trim($value['name']);
            }

            if ($py == 'F') {
                $F[] = trim($value['name']);
            }

            if ($py == 'G') {
                $G[] = trim($value['name']);
            }

            if ($py == 'H') {
                $H[] = trim($value['name']);
            }

            if ($py == 'I') {
                $I[] = trim($value['name']);
            }

            if ($py == 'J') {
                $J[] = trim($value['name']);
            }

            if ($py == 'K') {
                $K[] = trim($value['name']);
            }

            if ($py == 'L') {
                $L[] = trim($value['name']);
            }

            if ($py == 'M') {
                $M[] = trim($value['name']);
            }

            if ($py == 'N') {
                $N[] = trim($value['name']);
            }

            if ($py == 'O') {
                $O[] = trim($value['name']);
            }

            if ($py == 'P') {
                $P[] = trim($value['name']);
            }

            if ($py == 'Q') {
                $Q[] = trim($value['name']);
            }

            if ($py == 'R') {
                $R[] = trim($value['name']);
            }

            if ($py == 'S') {
                $S[] = trim($value['name']);
            }

            if ($py == 'T') {
                $T[] = trim($value['name']);
            }

            if ($py == 'U') {
                $U[] = trim($value['name']);
            }

            if ($py == 'V') {
                $V[] = trim($value['name']);
            }

            if ($py == 'W') {
                $W[] = trim($value['name']);
            }

            if ($py == 'X') {
                $X[] = trim($value['name']);
            }

            if ($py == 'Y') {
                $Y[] = trim($value['name']);
            }

            if ($py == 'Z') {
                $Z[] = trim($value['name']);
            }

        }
        if (!empty($A)) {
            $A_arr = array(
                'key' => 'A',
                'data' => $A,
            );
            $companyArr[] = $A_arr;
        }

        if (!empty($B)) {
            $B_arr = array(
                'key' => 'B',
                'data' => $B,
            );
            $companyArr[] = $B_arr;
        }

        if (!empty($C)) {
            $C_arr = array(
                'key' => 'C',
                'data' => $C,
            );
            $companyArr[] = $C_arr;
        }

        if (!empty($D)) {
            $D_arr = array(
                'key' => 'D',
                'data' => $D,
            );
            $companyArr[] = $D_arr;
        }

        if (!empty($E)) {
            $E_arr = array(
                'key' => 'E',
                'data' => $E,
            );
            $companyArr[] = $E_arr;
        }

        if (!empty($F)) {
            $F_arr = array(
                'key' => 'F',
                'data' => $F,
            );
            $companyArr[] = $F_arr;
        }

        if (!empty($G)) {
            $G_arr = array(
                'key' => 'G',
                'data' => $G,
            );
            $companyArr[] = $G_arr;
        }

        if (!empty($H)) {
            $H_arr = array(
                'key' => 'H',
                'data' => $H,
            );
            $companyArr[] = $H_arr;
        }

        if (!empty($I)) {
            $I_arr = array(
                'key' => 'I',
                'data' => $I,
            );
            $companyArr[] = $I_arr;
        }

        if (!empty($J)) {
            $J_arr = array(
                'key' => 'J',
                'data' => $J,
            );
            $companyArr[] = $J_arr;
        }

        if (!empty($K)) {
            $K_arr = array(
                'key' => 'K',
                'data' => $K,
            );
            $companyArr[] = $K_arr;
        }

        if (!empty($L)) {
            $L_arr = array(
                'key' => 'L',
                'data' => $L,
            );
            $companyArr[] = $L_arr;
        }

        if (!empty($M)) {
            $M_arr = array(
                'key' => 'M',
                'data' => $M,
            );
            $companyArr[] = $M_arr;
        }

        if (!empty($N)) {
            $N_arr = array(
                'key' => 'N',
                'data' => $N,
            );
            $companyArr[] = $N_arr;
        }

        if (!empty($O)) {
            $O_arr = array(
                'key' => 'O',
                'data' => $O,
            );
            $companyArr[] = $O_arr;
        }

        if (!empty($P)) {
            $P_arr = array(
                'key' => 'P',
                'data' => $P,
            );
            $companyArr[] = $P_arr;
        }

        if (!empty($Q)) {
            $Q_arr = array(
                'key' => 'Q',
                'data' => $Q,
            );
            $companyArr[] = $Q_arr;
        }

        if (!empty($R)) {
            $R_arr = array(
                'key' => 'R',
                'data' => $R,
            );
            $companyArr[] = $R_arr;
        }

        if (!empty($S)) {
            $S_arr = array(
                'key' => 'S',
                'data' => $S,
            );
            $companyArr[] = $S_arr;
        }

        if (!empty($T)) {
            $T_arr = array(
                'key' => 'T',
                'data' => $T,
            );
            $companyArr[] = $T_arr;
        }

        if (!empty($U)) {
            $U_arr = array(
                'key' => 'U',
                'data' => $U,
            );
            $companyArr[] = $U_arr;
        }

        if (!empty($V)) {
            $V_arr = array(
                'key' => 'V',
                'data' => $V,
            );
            $companyArr[] = $V_arr;
        }

        if (!empty($W)) {
            $W_arr = array(
                'key' => 'W',
                'data' => $W,
            );
            $companyArr[] = $W_arr;
        }

        if (!empty($X)) {
            $X_arr = array(
                'key' => 'X',
                'data' => $X,
            );
            $companyArr[] = $X_arr;
        }

        if (!empty($Y)) {
            $Y_arr = array(
                'key' => 'Y',
                'data' => $Y,
            );
            $companyArr[] = $Y_arr;
        }

        if (!empty($Z)) {
            $Z_arr = array(
                'key' => 'Z',
                'data' => $Z,
            );
            $companyArr[] = $Z_arr;
        }

        $returnArr['city'] = $companyArr;
        $returnArr['products'] = $products_all;
        F('diningsaleProducts' . $company . $domain ,$returnArr);

        $this->ajaxReturn($returnArr, 'JSON');

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
        return $paymentmgrResult;

    }

}
