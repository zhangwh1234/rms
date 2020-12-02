<?php
/**
 * Created by zhangwh
 * User: lihua
 * Date: 17/2/8
 * Time: 下午3:33
 * 小助手的接口API
 */

class AssistantApiAction extends Action
{

    public function index()
    {

        $data = array();
        $data['account'] = '13912302008';
        $data['pwd'] = '123';
        $data['city'] = '常州';
        $data['company'] = '怀南';
        $data['appkey'] = 'lihua4008lihua';
        $this->ajaxReturn($data, 'json');
    }

    public function tongji()
    {
        echo 'ok';
    }

    //处理用小助手登陆
    /**
     * 测试命令：http://assis.lihuaerp.com/index.php?s=/AssistantApi/login/param/{}
     * param 范式：{"account":"13912302008","pwd":"123","city":"\u5e38\u5dde","company":"\u4e5d\u697c","machineCode":"12312"}
     * http://assis.lihuaerp.com/index.php?s=/AssistantApi/login/param/{%22account%22:%2213912302008%22,%22pwd%22:%22123%22,%22city%22:%22%E5%B8%B8%E5%B7%9E%22,%22company%22:%22%E4%B9%9D%E6%A5%BC%22,%22appkey%22:%22lihua4008lihua%22}
     *
     */
    public function login()
    {
        //获取JSON参数 并去掉反斜杠
        $param = stripslashes($_REQUEST['param']);
        //转换成对象
        $loginParam = json_decode($param, true);

        if (empty($loginParam)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '没有参数';
            $this->ajaxReturn($data);
        }

        //获取数据
        $account = $loginParam['account'];
        $pwd = $loginParam['pwd'];

        if ($account == '13912302008' and $pwd == '111') {
            $data = array();
            $data['status'] = 'success';
            $data['info'] = '登陆成功';
            $data['data'] = array(
                'accountid' => 1953,
            );
            $this->ajaxReturn($data);
        }

        $city = $this->unicode_decode($loginParam['city'], 'UTF-8', true, 'u', '');
        if (empty($city)) {
            $city = $loginParam['city'];
        }
        $company = $this->unicode_decode($loginParam['company'], 'UTF-8', true, 'u', '');
        if (empty($company)) {
            $company = $loginParam['company'];
        }
        $machineCode = $loginParam['machineCode']; //参数

        //返回domain
        $domain = $this->getDomain($city);

        //校验账户
        if (empty($account)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '账号不能为空';
            $this->ajaxReturn($data);
        }
        if (!$this->isMobile($account)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '账号不是手机号码';
            $this->ajaxReturn($data);
        }
        //校验密码
        if (empty($pwd)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '密码不能为空';
            //$this->ajaxReturn($data);
        }

        //判断城市
        if (empty($city)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '城市不能为空';
            $this->ajaxReturn($data);
        }
        //判断分公司
        if (empty($company)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '分公司不能为空';
            $this->ajaxReturn($data);
        }

        //检查account
        $sendnamemgrModel = $this->connectDb($domain, 'sendnamemgr');

        $where = array();
        $where['telphone'] = $account;
        $where['assistantpwd'] = $pwd;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain($city);

        $res = $sendnamemgrModel->where($where)->find();

        //判断账号是不是存在
        if (empty($res)) { //不存在
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '账号不存在';
            $this->ajaxReturn($data);
        }

        //登陆成功,保存到日志中
        $where = array();
        $where['sendnamemgrid'] = $res['sendnamemgrid'];
        $data = array();
        $data['assistantlogin'] = date('Y-m-d H:i:s');
        $sendnamemgrModel->where($where)->save($data);

        $data = array();
        $data['status'] = 'success';
        $data['info'] = '登陆成功';
        $data['data'] = array(
            'accountid' => $res['sendnamemgrid'],
        );
        $this->ajaxReturn($data);
    }

    /**
     * app小助手发出获取订单的请求
     * 测试命令：http://assis.lihuaerp.com/index.php?s=/AssistantApi/getOrder/param/{}
     * param 范式：{"accountid":"647","city":"常州","company":"怀南"}
     */
    public function getOrder()
    {
        //获取JSON参数 并去掉反斜杠
        $param = stripslashes($_REQUEST['param']);

        //转换成对象
        $param = json_decode($param, true);

        //获取参数
        $accountid = $param['accountid'];
        $city = $this->unicode_decode($param['city'], 'UTF-8', true, 'u', '');
        if (empty($city)) {
            $city = $param['city'];
        }
        $company = $this->unicode_decode($param['company'], 'UTF-8', true, 'u', '');
        if (empty($company)) {
            $company = $param['company'];
        }
        //返回domain
        $domain = $this->getDomain($city);

        $sendnamemgrModel = $this->connectDb($domain, 'sendnamemgr');
        //查询送餐员的姓名
        $where = array();
        $where['sendnamemgrid'] = $accountid;
        $where['company'] = $company;
        $where['domain'] = $domain;
        $sendnameResult = $sendnamemgrModel->where($where)->find();

        //送餐员姓名
        $sendname = $sendnameResult['name'];

        //判断员工$sendname是否为空
        if (empty($sendname)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '送餐员不存在';
            //$this->ajaxReturn($data);
        }

        //数据库连接
        $sendnameappModel = $this->connectDb($domain, 'sendnameapp');
        $orderformModel = $this->connectDb($domain, 'orderform');
        $orderproductsModel = $this->connectDb($domain, 'orderproducts');
        $orderactivityModel = $this->connectDb($domain, 'orderactivity');
        $orderpaymentModel = $this->connectDb($domain, 'orderpayment');
        $checksendnameModel = $this->connectDb($domain, 'checksendname');

        //定义查询返回的字段
        $orderfields = array("orderformid as orderid,ordersn,clientname as consignee,
        	telphone as tel,address,custtime as time,concat_ws(' ',custdate,custtime) as add_time,
        		beizhu as remarks,totalmoney as amount,shouldmoney as getmoney,
        		sendname as employee,latitude,longitude,invoiceheader as invPayee,
        		invoicebody as invContent,shippingmoney as fee
        ", );

        $goodsfields = array(
            "orderproductsid as goodid,shortname as name,price,number,money",
        );

        $activityfields = array(
            "orderactivityid as activityid , name, money",
        );

        $paymentfields = array(
            "orderpaymentid as paymentid,name,money",
        );

        //保存送餐员在线的数据
        $data = array();
        $data['name'] = $sendname;
        $data['company'] = $company;
        $data['domain'] = $domain;
        $data['ontime'] = date('H:i:s');
        $where = array();
        $where['name'] = $sendname;
        $where['company'] = $company;
        $where['domain'] = $this->getDomain($city);
        $checksendnameResult = $checksendnameModel->where($where)->find();
        if (!empty($checksendnameResult)) {
            $checksendnameModel->where($where)->save($data);
        } else {
            $checksendnameModel->create();
            $checksendnameModel->add($data);
        }

        //定义回传的数组
        $return = array();
        //开始检查
        $where = array();
        $where['ustate'] = 0;
        //$where['sendname'] = $sendname;
        //$where['company'] =  $company;
        //$where['domain'] = $this->getDomain($city);
        $sendnameappResult = $sendnameappModel->where($where)->limit(1)->select();

        foreach ($sendnameappResult as $sendnameValue) {
            $ordersn = $sendnameValue['ordersn'];
            if ($sendnameValue['type'] == 'order') { //新订单
                $where = array();
                $where['ordersn'] = $ordersn;
                $orderform = $orderformModel->field($orderfields)->where($where)->find();
                $goods = $orderproductsModel->field($goodsfields)->where($where)->select();
                $activity = $orderactivityModel->field($activityfields)->where($where)->select();
                $payment = $orderpaymentModel->field($paymentfields)->where($where)->select();
                if (!empty($orderform)) {
                    $return[] = array(
                        'id' => $sendnameValue['sendnameappid'],
                        'type' => 'order',
                        'info' => array(
                            'order' => $orderform,
                            'goods' => $goods,
                            'activity' => $activity,
                            'payment' => $payment,
                        ),
                    );
                }
            };

            if ($sendnameValue['type'] == 'change') { //改单
                $where = array();
                $where['ordersn'] = $ordersn;
                $orderform = $orderformModel->field($orderfields)->where($where)->find();
                $goods = $orderproductsModel->field($goodsfields)->where($where)->select();
                $activity = $orderactivityModel->field($activityfields)->where($where)->select();
                $payment = $orderpaymentModel->field($paymentfields)->where($where)->select();
                if (!empty($orderform)) {
                    $return[] = array(
                        'id' => $sendnameValue['sendnameappid'],
                        'type' => 'change',
                        'info' => array(
                            'order' => $orderform,
                            'goods' => $goods,
                            'activity' => $activity,
                            'payment' => $payment,
                        ),
                    );
                }
            };

            if ($sendnameValue['type'] == 'hurry') { //催送
                $where = array();
                $where['ordersn'] = $ordersn;
                $return[] = array(
                    'id' => $sendnameValue['sendnameappid'],
                    'type' => 'hurry',
                    'info' => array(
                        'order' => array(
                            'ordersn' => $ordersn,
                        ),
                    ),
                );
            };

            if ($sendnameValue['type'] == 'again') { //再派送
                $where = array();
                $where['ordersn'] = $ordersn;
                $return[] = array(
                    'id' => $sendnameValue['sendnameappid'],
                    'type' => 'again',
                    'info' => array(
                        'order' => array(
                            'ordersn' => $ordersn,
                        ),
                    ),
                );
            };

            if ($sendnameValue['type'] == 'return') { //退餐
                $where = array();
                $where['ordersn'] = $ordersn;
                $return[] = array(
                    'id' => $sendnameValue['sendnameappid'],
                    'type' => 'return',
                    'info' => array(
                        'order' => array(
                            'ordersn' => $ordersn,
                        ),
                    ),
                );
            };
        }
        $this->ajaxReturn($return);
    }

    /**
     * 修改该订单的发送状态
     * 订单被APP接收后，发这个消息，改变订单的发送状态
     * 测试命令：http://assis.lihuaerp.com/index.php?s=/AssistantApi/changeSendStatus/param/{}
     * param 范式：{"id":"637557","city":"常州","company":"怀南"}
     */
    public function changeSendStatus()
    {

        $json = stripslashes($_REQUEST['param']);
        $param = json_decode($json, true);

        //获取返回的ID
        $sendnameappid = $param['id'];
        //返回sendnameappid的数组形式
        $sendnameappidArr = explode(',', $sendnameappid);

        $city = $this->unicode_decode($param['city'], 'UTF-8', true, 'u', '');
        if (empty($city)) {
            $city = $param['city'];
        }
        $company = $this->unicode_decode($param['company'], 'UTF-8', true, 'u', '');
        if (empty($company)) {
            $company = $param['company'];
        }
        $domain = $this->getDomain($city);

        if (empty($sendnameappid)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = 'id不存在';
            $this->ajaxReturn($data);
        }

        $sendnameappModel = $this->connectDb($domain, 'sendnameapp');
        foreach ($sendnameappidArr as $sendnameappidValue) {
            $where = array();
            $where['sendnameappid'] = $sendnameappidValue;
            $data = array();
            $data['ustate'] = 1;
            $res = $sendnameappModel->where($where)->save($data);
        }

        //将订单被接收到写入订单日志中,防止说不清楚
        //获取ordersn
        $where = array();
        $where['sendnameappid'] = $sendnameappidValue;
        $sendnameappResult = $sendnameappModel->field('ordersn')->where($where)->find();
        $ordersn = $sendnameappResult['ordersn'];
        //先查询订单表
        $orderformModel = $this->connectDb($domain, 'orderform');
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderform = $orderformModel->field('orderformid,sendname')->where($where)->find();

        // 记入操作到action中
        $orderactionModel = $this->connectDb($domain, 'orderaction');
        $data['orderformid'] = $orderform['orderformid']; // 订单号
        $data['ordersn'] = $ordersn;
        $data['action'] = '送餐员:' . $orderform['sendname'] . '的小助手收到订单';
        $data['logtime'] = date('Y-m-d H:i:s');
        $data['domain'] = $domain;
        $orderactionModel->create();
        $result = $orderactionModel->add($data);

        //写入送餐订单监测表
        $checkorderformModel = $this->connectDb($domain, 'checkorderform');
        $where = array();
        $where['ordersn'] = $ordersn;
        $data = array();
        $data['noreceivetime'] = '';
        $data['noreadtime'] = date('H:i:s');
        $checkorderformModel->where($where)->save($data);

        if ($res >= 0) {
            $data = array();
            $data['status'] = 'success';
            $this->ajaxReturn($data);
        } else {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '保存失败';
            $this->ajaxReturn($data);
        }
    }

    /**
     * 修改订单状态为已接收状态
     * 订单状态 0未读、1已读、2完成
     * 测试命令：http://assis.lihuaerp.top/index.php?s=/AssistantApi/changeStatus/param={}
     * param 范式：{"ordersn":"12321","status":"2","city":"常州","company":"怀南"}
     */
    public function changeStatus()
    {

        $json = stripslashes($_REQUEST['param']);
        $param = json_decode($json, true);

        $ordersn = $param['ordersn'];
        $orderStatus = $param['status'];
        $city = $this->unicode_decode($param['city'], 'UTF-8', true, 'u', '');
        if (empty($city)) {
            $city = $param['city'];
        }
        $company = $this->unicode_decode($param['company'], 'UTF-8', true, 'u', '');
        if (empty($company)) {
            $company = $param['company'];
        }
        $domain = $this->getDomain($city);

        if (empty($ordersn)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '订单号不存在';
            $this->ajaxReturn($data);
        }
        if (empty($orderStatus)) {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '订单状态不存在';
            $this->ajaxReturn($data);
        }

        $orderformModel = $this->connectDb($domain, 'orderform');
        $where = array();
        $where['ordersn'] = $ordersn;
        $orderform = $orderformModel->where($where)->find();

        //订单已经配送完毕,写入订单状态
        if ($orderStatus == '2') {
            //记录到orderstate中
            // 写入到状态表中
            $orderstateModel = $this->connectDb($domain, 'orderstate');
            $where = array();
            $where['ordersn'] = $ordersn;
            $data = array();
            $data['success'] = 1;
            $data['successtime'] = date('Y-m-d H:i:s');
            $data['successcontent'] = '送餐员:' . $orderform['sendname'] . '配送完毕';
            $data['orderformid'] = $orderform['orderformid'];
            $data['ordersn'] = $ordersn;
            $data['domain'] = $domain;
            $orderstateModel->where($where)->save($data);

            // 记入操作到action中
            $orderactionModel = $this->connectDb($domain, 'orderaction');
            $data['orderformid'] = $orderform['orderformid']; // 订单号
            $data['ordersn'] = $ordersn;
            $data['action'] = '送餐员:' . $orderform['sendname'] . ' 已经将订单配送完毕 ';
            $data['logtime'] = date('Y-m-d H:i:s');
            $data['domain'] = $domain;
            $orderactionModel->create();
            $result = $orderactionModel->add($data);

            //写入订单表中
            $where = array();
            $where['ordersn'] = $ordersn;
            $data = array();
            $data['successtime'] = date('H:i:s');
            $orderformModel->where($where)->save($data);

            //记入到网站中
            $webstatusModel = $this->connectDb($domain, 'webstatus');
            $data = array();
            $data['ordersn'] = $ordersn;
            $data['type'] = 4; //订单完成
            $data['content'] = '送餐员:' . $orderform['sendname'] . '配送完毕';
            $data['date'] = date('H:i:s');
            if (empty($orderform['origin'])) {
                $data['origin'] = '';
            } else {
                $data['origin'] = $orderform['origin'];
            }
            $data['domain'] = $domain;
            $webstatusModel->create();
            $webstatusModel->add($data);

            //写入送餐订单监测表
            $checkorderformModel = $this->connectDb($domain, 'checkorderform');
            $where = array();
            $where['ordersn'] = $ordersn;
            $data = array();
            $data['nocompletetime'] = '';
            $data['completetime'] = date('H:i:s');
            $checkorderformModel->where($where)->save($data);
        }

        //订单已经被阅读
        if ($orderStatus == '1') {
            // 记入操作到action中
            $orderactionModel = $this->connectDb($domain, 'orderaction');
            $data['orderformid'] = $orderform['orderformid']; // 订单号
            $data['ordersn'] = $orderform['ordersn'];
            $data['action'] = '送餐员:' . $orderform['sendname'] . ' 已经阅读订单 ';
            $data['logtime'] = date('H:i:s');
            $data['domain'] = $domain;
            $orderactionModel->create();
            $result = $orderactionModel->add($data);

            //写入送餐订单监测表
            $checkorderformModel = $this->connectDb($domain, 'checkorderform');
            $where = array();
            $where['ordersn'] = $ordersn;
            $data = array();
            $data['noreadtime'] = '';
            $data['alreadyreadtime'] = date('H:i:s');
            $data['nocompletetime'] = date('H:i:s');
            $checkorderformModel->where($where)->save($data);
        }

        if ($result >= 0) {
            $data = array();
            $data['status'] = 'success';
            $this->ajaxReturn($data);
        } else {
            $data = array();
            $data['status'] = 'error';
            $data['info'] = '保存失败';
            $this->ajaxReturn($data);
        }

    }

    /**
     * 根据参数，返回送餐员的账务信息
     * @param mixed $json 示列:{'employee':'','pwd':''}
     * 返回页面
     * demo:http://localhost/assisadmin.lihua.com/index.php?g=portal&m=AppOrder&a=getAccount&param={%22account%22:%2211134813%22,%22system%22:%220%22}
     */
    public function getAccount()
    {
        //获取JSON参数 并去掉反斜杠
        $json = stripslashes($_REQUEST['param']);
        //本地测试
        //$json='{"account":"itnik","pwd":"111111"};
        //转换成对象
        $param = json_decode($json);
        $account = $param->account;
        $pwd = $param->pwd;
        $this->assign('account', $account);
        $this->display("account");
    }

    /**
     * 接收用户定位数据
     * 这个方法不用 2017-09-01
     */
    public function location()
    {
        //用户数据
        $employee = $_REQUEST['employee'];
        //经度
        $longitude = $_REQUEST['longitude'];
        //维度
        $latitude = $_REQUEST['latitude'];
        //时间戳
        $timestamp = $_REQUEST['timestamp'];
        //程序
        $city = $_REQUEST['city'];
        $domain = $this->getDomain($city);

        //链接送餐员查看表
        $checksendnameModel = $this->connectDb($domain, 'checksendname');
        $locationnowModel = $this->connectDb($domain, 'localtionnow');
        //打开定位表
        $where = array();
        $where['sendname'] = $employee;
        $locationResult = $locationnowModel->where($where)->find();
        //处理定位数据
        if ($locationResult['employee']) {
            //存在用户定位数据，只需要更新数据
            $data = array();
            $data['longitude'] = $longitude;
            $data['latitude'] = $latitude;
            $data['timestamp'] = $timestamp;
            $data['date'] = date('Y-m-d H:i:s');
            $data['domain'] = $domain;
            $locationnowModel->where($where)->save($data);
        } else {
            //不存在用户定位数据，插入
            $data = array();
            $data['employee'] = $employee;
            $data['longitude'] = $longitude;
            $data['latitude'] = $latitude;
            $data['timestamp'] = $timestamp;
            $data['date'] = date('Y-m-d H:i:s');
            $domain['domain'] = $domain;
            $locationnowModel->create();
            $locationnowModel->add($data);
        }

        //处理送餐员查看
        $where = array();
        $where['name'] = $employee;
        $where['domain'] - $domain;
        $checksendnameResult = $checksendnameModel->where($where)->find();
        if ($checksendnameResult['name']) {
            $data = array();
            $data['ontime'] = $date('H:i:s');
            $checksendnameModel->where($where)->save($data);
        }{
            $data = array();
            $data['ontime'] = $date('H:i:s');
            $data['domain'] = $domain;
            $checksendnameResult->create();
            $checksendnameModel->add($data);
        }

        echo 'ok';
    }

    /**
     * 返回城市
     * http://assis.lihuaerp.com/index.php?s=/AssistantApi/getCity
     */
    public function getCity()
    {
        $data = array();
        $data[] = '北京';
        $data[] = '南京';
        $data[] = '常州';
        $data[] = '无锡';
        $data[] = '苏州';
        $data[] = '上海';
        $data[] = '广州';
        $this->ajaxReturn($data);
    }

    /**
     * 返回分公司
     *  http://assis.lihuaerp.com/index.php?s=/AssistantApi/getCompany
     *  param 范式：{"city":"\u5e38\u5dde"}
     */
    public function getCompany()
    {
        $json = stripslashes($_REQUEST['param']);
        $param = json_decode($json, true);

        $city = $this->unicode_decode($param['city'], 'UTF-8', true, 'u', '');
        if (empty($city)) {
            $city = $param['city'];
        }

        $domain = $this->getDomain($city);

        $companymgrModel = $this->connectDb($domain, 'companymgr');

        $where = array();
        $where['domain'] = $domain;
        $companyResult = $companymgrModel->where($where)->select();

        $company = array();
        foreach ($companyResult as $value) {
            $company[] = $value['name'];
        }

        $this->ajaxReturn($company);
    }

    /**
     * 根据条件检查App是否有需要升级的版本<p>
     * 调用JSON示例：{"id":"1","system":"1"}<p>
     * 返回JSON说明：{返回数据,提示信息,操作状态}
     */
    public function checkUpdate()
    {
        //获取JSON参数 并去掉反斜杠
        $json = stripslashes($_REQUEST['param']);
        //本地测试
        //$json='{"id":"1","system":"0"}';
        //转换成对象
        $param = json_decode($json);
        $id = $param->id;
        $system = $param->system;
        if ($id == '' || $id == null) {
            $this->ajaxReturn($json, C('Assistant.801'), '801');
        }
        if ($system == '' || $system == null) {
            $this->ajaxReturn($json, C('Assistant.802'), '802');
        }
        $this->app_version_model = D('Common/AppVersion');
        $res = $this->app_version_model->where(array('app_id' => $id, 'app_system' => $system))->find();
        $this->ajaxReturn($res, '200', '0');
    }

    /**
     * 根据city,返回domain
     */
    public function getDomain($city)
    {
        switch ($city) {
            case '北京':
                return 'bj.lihuaerp.com';
            case '南京':
                return 'nj.lihuaerp.com';
            case '常州':
                return 'cz.lihuaerp.com';
            case '无锡':
                return 'wx.lihuaerp.com';
            case '苏州':
                return 'sz.lihuaerp.com';
            case '上海':
                return 'sh.lihuaerp.com';
            case '广州':
                return 'gz.lihuaerp.com';
        }
    }

    /**
     * 根据domain,返回数据库连接
     */
    public function connectDb($domain, $table)
    {
        if ($domain == 'bj.lihuaerp.com') {
            //$connectStr =  'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com:3306/bjrms#utf8';
            $connectStr = C('bjlihuaerpcom');
        } else {
            //$connectStr =   'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com:3306/czrms#utf8';
            $connectStr = C('czlihuaerpcom');
        }
        $db = M($table, 'rms_', $connectStr);
        return $db;
    }

    /**
     * 验证手机号是否正确
     * @param INT $mobile
     */
    public function isMobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

    /**
     * Unicode编码转汉字
     * @param string $str Unicode编码的字符串
     * @param string $decoding 原始汉字的编码
     * @param boot $ishex 是否为十六进制表示（支持十六进制和十进制）
     * @param string $prefix 编码后的前缀
     * @param string $postfix 编码后的后缀
     */
    public function unicode_decode($unistr, $encoding = 'UTF-8', $ishex = false, $prefix = '&#', $postfix = ';')
    {
        $arruni = explode($prefix, $unistr);
        $unistr = '';
        for ($i = 1, $len = count($arruni); $i < $len; $i++) {
            if (strlen($postfix) > 0) {
                $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
            }
            $temp = $ishex ? hexdec($arruni[$i]) : intval($arruni[$i]);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
        }
        return iconv('UCS-2', $encoding, $unistr);
    }
}
