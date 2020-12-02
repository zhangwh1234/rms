<?php
/**
 * 2014-06-02,开始编制统计订单,
 * 2014-10-07作为一个工作流程，能够根据发过来的ncmq消息，为不同的地区工作。
 * help:http://115.29.43.18:1818/?name=rmsprogram&opt=put&data=OrderTongjinj.lihuaerp.com&ver=2
 * http://115.29.43.18:1818/?name=rmsprogram&opt=get&ver=2
 * 测试命令：/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php OrderTongji/test
 */
class OrderTongji
{
    // 定义数据库的连接字符
    public $dnsConnectionDB = '';
    public $LogFile = '';
    public $tongjiName = ''; // 统计方名称
    /**
     * 架构函数
     *
     * @access public
     */
    public function __construct()
    {
        Log::$format = '[y-m-d H:i:s]';
        $this->LogFile = LOG_PATH . 'Tongji_' . date('Y-m-d') . '.log';
    }

    //
    public function index()
    {

    }

    // 查询队列，然后执行统计
    public function getMessageTongji()
    {
        load("@.function");
        // 建立执行的消息命令,消息命令是取队列的数据
        $programCmd = 'http://' . C('ncmqServer') . ':' . C('ncmqPort') . "/?name=rmsprogram&opt=get&ver=2";
        $output = curl_post($programCmd); // 写入队列
        Log::write('队列中提取消息', INFO, LOG::FILE, $this->LogFile);
        if (substr($output, 0, 12) != 'UCMQ_HTTP_OK') {
            Log::write('消息队列空:' . $output, INFO, LOG::FILE, $this->LogFile);
            exit(); // 没有命令消息，就退出
        }
        // 处理消息，获得命令
        $areaActionCmd = substr($output, 14);
        Log::write('队列中有消息:' . $output, INFO, LOG::FILE, $this->LogFile);
        $areaActionCmd = 'OrderTongjinj.lihuaerp.com';
        $this->tongjiName = $areaActionCmd;
        require APP_PATH . 'Conf/dataconfig.php';
        // 获得数据库的路径
        $this->dnsConnectionDB = $rmsDataPath[$areaActionCmd];
        // 执行统计命令
        $this->actionTongji();
    }

    // 测试
    public function test()
    {
        $this->tongjiName = 'OrderTongjicz.lihuaerp.com';
        $this->LogFile = LOG_PATH . str_replace('.', '', $this->tongjiName) . '_' . date('Y-m-d') . '.log';
        require APP_PATH . 'Conf/dataconfig.php';
        // 获得数据库的路径
        $this->dnsConnectionDB = $rmsDataPath['OrderTongjicz.lihuaerp.com'];
        //$this->tongjiOrder ();
        // $this->tongjiProductsNumber();
        // $this->tongjiProductsCustomerNumber ();
        // $this->tongjiProductsTotalMoney ();
        // $this->tongjiProductsCustomerFengbu();
        $this->tongjiTelname();
        // $this->tongjiOrderHurry();
        // $this->tongjiBigCustomer ();
        //$this->tongjiOrderBackCancel ();
        //$this->tongjiAllOrder();
    }

    /**
     * 执行统计
     */
    public function actionTongji($domain)
    {

        // 取得消息的命令内容
        // 订单统计
        $this->tongjiOrder($domain);

        // 产品数量
        $this->tongjiProductsNumber($domain);

        // 客户数
        $this->tongjiProductsCustomerNumber($domain);

        // 产品金额
        $this->tongjiProductsTotalMoney($domain);

        // 产品客户数分布
        $this->tongjiProductsCustomerFengbu($domain);

        // 接线量统计
        $this->tongjiTelname($domain);

        // 催送统计
        $this->tongjiOrderHurry($domain);
        // 大客户统计
        $this->tongjiBigCustomer($domain);
        // 退餐统计
        $this->tongjiOrderBackCancel($domain);
        // 统计全部
        $this->tongjiAllOrder($domain);
        // 统计完成率
        $this->sendtimeOrderTj($domain);
    }

    /**
     * 统计订单量,列是项目
     */
    private function tongjiOrder($domain)
    {

        Log::write('开始执行订单量统计', Log::INFO, Log::FILE, $this->LogFile);
        if ($domain == 'bj.lihuaerp.com') {
            $this->dnsConnectionDB = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com:3306/bjrms';
        } else {
            $this->dnsConnectionDB = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com:3306/czrms';
        }

        //$this->dnsConnectionDB = 'mysql://root:@localhost:3306/rms';
        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        $tongji = array();

        $userModel = M('user', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['rolename'] = array(
            array(
                'like',
                '接线员',
            ),
            array(
                'like',
                '联络员',
            ),
            'or',
        );
        // 获得所有接线员
        $telnameResult = $userModel->field('truename')->where($where)->select();
        foreach ($telnameResult as $telnameValue) {
            $telnameArray[] = $telnameValue['truename'];
        }

        // 从订单中获取所有接线员
        $where = array();
        $where['domain'] = $domain;
        $ordertelnameResult = $orderformModel->Distinct(true)->field('telname')->where($where)->select();

        // 排除计算
        $name = array();
        foreach ($ordertelnameResult as $ordertelnameValue) {
            if (!in_array($ordertelnameValue['telname'], $telnameArray)) {
                $name[] = $ordertelnameValue['telname'];
            }
        }

        //从配送店管理中查询所有分公司
        $where = array();
        $where['domain'] = $domain;

        // 从查询所有分公司
        $where = array();
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $companyResult = $orderformModel->Distinct(true)->field('company')->where($where)->select();
        $companyResult[]['company'] = '全部';
        foreach ($companyResult as $value) {
            if (!empty($value['company'])) {
                foreach ($name as $itemValue) {
                    $tongji[$value['company']][$itemValue] = 0;
                }
            }
        }
        Log::write('订单量统计,统计项目:' . json_encode($tongji, JSON_UNESCAPED_UNICODE), Log::INFO, Log::FILE, $this->LogFile);
        // 返回所有订单
        $where = array();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['totalmoney'] = array(
            'GT',
            0,
        );

        $where['company'] = array(
            'NEQ',
            '',
        );

        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();

        Log::write('订单量统计，查询返回的订单数量:' . count($orderformResult), Log::INFO, Log::FILE, $this->LogFile);
        // 开始统计计算
        foreach ($orderformResult as $orderformValue) {
            $tongji['全部']['总订单'] += 1;
            $tongji[$orderformValue['company']]['总订单'] += 1;
            foreach ($name as $nameValue) {
                if ($nameValue == $orderformValue['telname']) {
                    $tongji['全部'][$nameValue] += 1;
                    $tongji[$orderformValue['company']][$nameValue] += 1;
                }
            }
            if (!in_array($orderformValue['telname'], $name)) {
                $tongji['全部']['电话'] += 1;
                $tongji[$orderformValue['company']]['电话'] += 1;
            }
            if ($orderformValue['hurrynumber'] > 0) {
                $tongji['全部']['催送'] += 1;
                $tongji[$orderformValue['company']]['催送'] += 1;
            }
        }

        Log::write('订单量统计,统计完毕', Log::INFO, Log::FILE, $this->LogFile);

        $tongjiorderModel = M('tongjiorder', 'rms_', $this->dnsConnectionDB);
        // 清除以前的数据
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $tongjiorderModel->where($where)->delete();

        Log::write('订单量统计，保存统计结果。', Log::INFO, Log::FILE, $this->LogFile);
        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            // var_dump($value);
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $tongjiorderModel->create();
                $tongjiorderModel->add($data);
            }
        }
        Log::write('订单量统计，保存结果完毕，统计结束!!!', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * *
     * 产品销售统计，
     * 内容：每个分公司销售的产品的数量
     */
    private function tongjiProductsNumber($domain)
    {
        Log::write('开始执行产品销售量统计', Log::INFO, Log::FILE, $this->LogFile);
        // 定义统计的数组
        $tongji = array();

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);
        // 产品表
        $orderproductsModel = M('orderproducts', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $where['domain'] = $domain;
        // 返回产品名称
        $productsResult = $orderproductsModel->Distinct(true)->where($where)->field('shortname')->select();
        // 定义要查询的分公司名称
        $where = array();
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $companyResult = $orderformModel->Distinct(true)->field('company')->where($where)->select();
        foreach ($companyResult as $value) {
            foreach ($productsResult as $productsValue) {
                $tongji[$value['company']][$productsValue['shortname']] = 0;
            }
        }
        Log::write('产品销售量统计，统计项目：' . json_encode($tongji, JSON_UNESCAPED_UNICODE), Log::INFO, Log::FILE, $this->LogFile);
        // 返回要查询的订单
        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        Log::write('产品销售量统计，订单数量：' . count($orderformResult), Log::INFO, Log::FILE, $this->LogFile);
        // 返回所有的订货
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $orderproductsReuslt = $orderproductsModel->where($where)->select();
        Log::write('产品销售量统计,产品总数：' . count($orderproductsReuslt), Log::INFO, Log::FILE, $this->LogFile);

        // 进行计算
        foreach ($orderformResult as $orderformValue) {
            foreach ($orderproductsReuslt as $productsValue) {
                if ($orderformValue['orderformid'] == $productsValue['orderformid']) { // 找到这个订单的产品
                    $tongji['全部'][$productsValue['shortname']] += $productsValue['number'];
                    $tongji[$orderformValue['company']][$productsValue['shortname']] += $productsValue['number'];
                }
            }
        }
        Log::write('产品销售量统计,统计完毕！', Log::INFO, Log::FILE, $this->LogFile);

        // 保存
        // 产品销售统计表
        $tongjiProductsModel = M('tongjiproductsnumber', 'rms_', $this->dnsConnectionDB);
        // 清除以前的数据
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiProductsModel->where($where)->delete();

        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $tongjiProductsModel->add($data);
            }
        }

        Log::write('产品销售量统计，保存完毕！！！', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 客户数，统计产品的订单数量
     * 内容：销售产品的相关订单的数量
     */
    private function tongjiProductsCustomerNumber($domain)
    {
        Log::write('开始执行产品客户数统计', Log::INFO, Log::FILE, $this->LogFile);
        // 定义统计的数组
        $tongji = array();

        // 订单表
        $orderformModel = M('Orderform', 'rms_', $this->dnsConnectionDB);
        // 产品表
        $orderproductsModel = M('orderproducts', 'rms_', $this->dnsConnectionDB);
        // 返回产品名称
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $where['domain'] = $domain;
        $productsResult = $orderproductsModel->Distinct(true)->where($where)->field('shortname')->select();

        // 定义要查询的分公司名称
        $where = array();
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $companyResult = $orderformModel->Distinct(true)->field('company')->where($where)->select();
        $companyResult[]['company'] = '全部';
        foreach ($companyResult as $value) {
            foreach ($productsResult as $productsValue) {
                $tongji[$value['company']][$productsValue['shortname']] = 0;
            }
        }
        Log::write('产品客户数统计，统计项目：' . json_encode($tongji, JSON_UNESCAPED_UNICODE), Log::INFO, Log::FILE, $this->LogFile);

        // 返回要查询的订单
        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        Log::write('产品客户数统计，统计订单数' . count($orderformResult), Log::INFO, Log::FILE, $this->LogFile);
        // 返回所有的订货
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $orderproductsResult = $orderproductsModel->where($where)->select();
        Log::write('产品客户数统计，统计的产品数量' . count($orderproductsResult), Log::INFO, Log::FILE, $this->LogFile);

        // 进行计算
        foreach ($orderformResult as $orderformValue) {
            foreach ($orderproductsResult as $productsValue) {
                if ($orderformValue['orderformid'] == $productsValue['orderformid']) {
                    foreach ($companyResult as $companyValue) {
                        if ($companyValue['company'] == $orderformValue['company']) {
                            $tongji[$orderformValue['company']][$productsValue['shortname']] += 1;
                        }
                    }
                    $tongji['全部'][$productsValue['shortname']] += 1;
                }
            }
        }
        Log::write('产品客户数统计，统计完毕。', Log::INFO, Log::FILE, $this->LogFile);
        // var_dump ( $tongji );
        // 保存
        // 产品销售统计表
        $productsOrderNumberTongjiModel = M('tongjiproductscn', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $productsOrderNumberTongjiModel->where($where)->delete();

        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            // var_dump($value);
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $productsOrderNumberTongjiModel->add($data);
                // /var_dump($productsOrderNumberTongjiModel->getLastSql());
            }
        }
        Log::write('产品客户数统计，保存完毕！！！', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 产品销售单额，统计每一种产品的销售总额
     * 内容：每个产品的销售金额
     */
    private function tongjiProductsTotalMoney($domain)
    {
        Log::write('开始执行产品销售金额统计。', Log::INFO, Log::FILE, $this->LogFile);
        // 定义统计的数组
        $tongji = array();

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);
        // 产品表
        $orderproductsModel = M('orderproducts', 'rms_', $this->dnsConnectionDB);
        // 返回产品名称
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $where['money'] = array(
            'GT',
            0,
        );
        $where['name'] = array(
            'NEQ',
            '贵宾卡',
        );
        $where['name'] = array(
            'NEQ',
            '充值卡',
        );
        $where['domain'] = $domain;
        $productsResult = $orderproductsModel->Distinct(true)->where($where)->field('shortname')->select();

        // 定义要查询的分公司名称
        $where = array();
        $where['company'] = array(
            'NEQ',
            '',
        );
        $where['domain'] = $domain;
        $companyResult = $orderformModel->Distinct(true)->field('company')->where($where)->select();
        $tongji['全部']['汇总'] = 0;
        foreach ($companyResult as $value) {
            foreach ($productsResult as $productsValue) {
                $tongji[$value['company']][$productsValue['shortname']] = 0;
                $tongji[$value['company']]['汇总'] = 0;
            }
        }
        Log::write('产品客户数统计，统计项目：' . json_encode($tongji, JSON_UNESCAPED_UNICODE), Log::INFO, Log::FILE, $this->LogFile);

        // 返回要查询的订单
        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['company'] = array(
            'NEQ',
            '',
        );

        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        Log::write('产品销售金额统计，统计订单数' . count($orderformResult), Log::INFO, Log::FILE, $this->LogFile);
        // var_dump($orderformResult);
        // 返回所有的订货
        $where = array();
        $where['length(name)'] = array(
            'GT',
            0,
        );
        $where['money'] = array(
            'GT',
            0,
        );

        $where['name'] = array(
            'NEQ',
            '卡充值',
        );
        $orderproductsResult = $orderproductsModel->where($where)->select();
        Log::write('产品销售金额统计，统计的产品数量' . count($orderproductsResult), Log::INFO, Log::FILE, $this->LogFile);

        // 开始计算
        foreach ($orderformResult as $orderformValue) {
            foreach ($orderproductsResult as $productsValue) {
                if ($orderformValue['orderformid'] == $productsValue['orderformid']) { // 找到这个订单的产品
                    foreach ($companyResult as $companyValue) {
                        // $tongji ['全部'] [$productsValue ['shortname']] += $productsValue ['money'];
                        if ($companyValue['company'] == $orderformValue['company']) {
                            $tongji[$orderformValue['company']][$productsValue['shortname']] += $productsValue['number'] * $productsValue['price'];
                            $tongji[$orderformValue['company']]['汇总'] += $productsValue['money'];
                        }
                    }
                    $tongji['全部'][$productsValue['shortname']] += $productsValue['money'];
                    $tongji['全部']['汇总'] += $productsValue['money'];
                }
            }
        }

        Log::write('产品销售金额统计，统计完毕。' . count($orderproductsReuslt), Log::INFO, Log::FILE, $this->LogFile);

        // 产品销售统计表
        $tongjiProductsTotalMoneyModel = M('tongjiproductstotalmoney', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiProductsTotalMoneyModel->where($where)->delete();

        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $tongjiProductsTotalMoneyModel->add($data);
            }
        }

        Log::write('产品销售金额统计，保存完毕！！！' . count($orderproductsReuslt), Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 产品份数客户数，统计产品订购数量在1份，2份。。。上的分布
     */
    private function tongjiProductsCustomerFengbu($domain)
    {
        Log::write('开始执行产品客户分布统计', Log::INFO, Log::FILE, $this->LogFile);
        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        $tongji = array();

        // 定义要查询的项目名称
        $name = array(
            '1份',
            '2份',
            '3份',
            '4-10份',
            '11-20份',
            '21-50份',
            '50以上',
        );

        // 产品表
        $orderproductsModel = M('orderproducts', 'rms_', $this->dnsConnectionDB);
        // 返回产品名称
        $where = array();
        $where['length(shortname)'] = array(
            'GT',
            0,
        );
        $where['money'] = array(
            'GT',
            0,
        );
        $where['domain'] = $domain;
        $productsResult = $orderproductsModel->Distinct(true)->where($where)->field('shortname')->select();

        foreach ($productsResult as $value) {
            foreach ($name as $itemValue) {
                $tongji[$value['shortname']][$itemValue] = 0;
            }
        }
        Log::write('产品客户数分布统计,统计项目:' . json_encode($tongji, JSON_UNESCAPED_UNICODE), Log::INFO, Log::FILE, $this->LogFile);

        // 返回所有订单
        $where = array();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        Log::write('订单量统计，查询返回的订单数量:' . count($orderformResult), Log::INFO, Log::FILE, $this->LogFile);
        // 返回所有的订货
        $where = array();
        $where['length(shortname)'] = array(
            'GT',
            0,
        );
        $where['money'] = array(
            'GT',
            0,
        );
        $orderproductsReuslt = $orderproductsModel->where($where)->select();
        Log::write('产品销售量统计,产品总数：' . count($orderproductsReuslt), Log::INFO, Log::FILE, $this->LogFile);

        // 开始统计计算
        foreach ($orderformResult as $orderformValue) {
            foreach ($orderproductsReuslt as $productsValue) {
                if ($orderformValue['orderformid'] == $productsValue['orderformid']) {
                    if ($productsValue['number'] == 1) { // 订购1份的
                        $tongji[$productsValue['shortname']][$name[0]] += 1;
                    }
                    if ($productsValue['number'] == 2) { // 订购2份的
                        $tongji[$productsValue['shortname']][$name[1]] += 1;
                    }
                    if ($productsValue['number'] == 3) { // 订购3份的
                        $tongji[$productsValue['shortname']][$name[2]] += 1;
                    }
                    if ($productsValue['number'] > 3 && $productsValue['number'] < 11) { // 订购4-10份的
                        $tongji[$productsValue['shortname']][$name[3]] += 1;
                    }
                    if ($productsValue['number'] > 10 && $productsValue['number'] < 21) { // 订购11-20份的
                        $tongji[$productsValue['shortname']][$name[4]] += 1;
                    }
                    if ($productsValue['number'] > 21 && $productsValue['number'] < 51) { // 订购21-50份的
                        $tongji[$productsValue['shortname']][$name[5]] += 1;
                    }
                    if ($productsValue['number'] > 50) { // 订购50份以上的
                        $tongji[$productsValue['shortname']][$name[6]] += 1;
                    }
                }
            }
        }
        Log::write('产品客户数分布统计,统计完毕', Log::INFO, Log::FILE, $this->LogFile);

        $ordertongjiModel = M('tongjiproductscf', 'rms_', $this->dnsConnectionDB);
        // 清除以前的数据
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $ordertongjiModel->where($where)->delete();
        Log::write('订单量统计，保存统计结果。', Log::INFO, Log::FILE, $this->LogFile);
        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $ordertongjiModel->create();
                $ordertongjiModel->add($data);
            }
        }
        Log::write('产品客户数分布统计，保存结果完毕，统计结束!!!', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 订单的大客户统计
     */
    private function tongjiBigCustomer($domain)
    {
        Log::write('开始执行大客户统计', Log::INFO, Log::FILE, $this->LogFile);

        // 催餐统计表
        $tongjibigcustomerModel = M('tongjibigcustomer', 'rms_', $this->dnsConnectionDB);

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        // 清除以前的记录
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjibigcustomerModel->where($where)->delete();

        // 查询订单记录
        $where = array();
        $where['totalmoney'] = array(
            'GT',
            500,
        );
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderformValue) {
            $data = array();
            $data['address'] = $orderformValue['address'];
            $data['ordertxt'] = $orderformValue['ordertxt'];
            $data['telname'] = $orderformValue['telname'];
            $data['totalmoney'] = $orderformValue['totalmoney'];
            $data['sendname'] = $orderformValue['sendname'];
            $data['company'] = $orderformValue['company'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['domain'] = $domain;
            $tongjibigcustomerModel->create();
            $tongjibigcustomerModel->add($data);
        }
        Log::write('大客户统计完毕', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 统计接线员接单量
     */
    private function tongjiTelname($domain)
    {
        Log::write('开始执行接线员统计。', Log::INFO, Log::FILE, $this->LogFile);
        // 定义统计数组
        $tongji = array();

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        // 定义要查询的项目名称
        $name = array(
            '数量',
            '金额',
        );

        $userModel = M('user', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['rolename'] = array(
            array(
                'like',
                '接线员',
            ),
            array(
                'like',
                '联络员',
            ),
            'or',
        );
        $where['domain'] = $domain;
        // 获得所有接线员
        $telnameResult = $userModel->field('truename')->where($where)->select();

        foreach ($telnameResult as $telnameValue) {
            foreach ($name as $itemValue) {
                $tongji[$telnameValue['truename']][$itemValue] = 0;
            }
        }

        // 返回要查询的订单
        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['state'] = array(
            array(
                'notlike',
                '%退餐',
            ),
            array(
                'notlike',
                '%废单',
            ),
            'and',
        );
        $where['totalmoney'] = array(
            'GT',
            0,
        );
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderformValue) {
            //foreach ( $telnameResult as $telnameValue ) {
            //if (trim ( $orderformValue ['telname'] ) == trim ( $telnameValue ['truename'] )) {
            $tongji[$orderformValue['telname']]['数量'] += 1;
            $tongji[$orderformValue['telname']]['金额'] += $orderformValue['totalmoney'];
            //}
            //}
        }
        Log::write('接线员接单量统计，统计完毕！', Log::INFO, Log::FILE, $this->LogFile);

        // 接线量
        $tongjiTelnameModel = M('tongjitelname', 'rms_', $this->dnsConnectionDB);

        // 清除以前的数据
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiTelnameModel->where($where)->delete();

        foreach ($tongji as $key => $value) {
            $data = array();
            $data['name'] = $key;
            foreach ($value as $k => $v) {
                $data['content'] = $k;
                $data['number'] = $v;
                $data['date'] = date('Y-m-d');
                $data['ap'] = $this->getAp();
                $data['domain'] = $domain;
                $tongjiTelnameModel->add($data);
            }
        }
        Log::write('接线员接单量统计，保存结果完毕，统计结束!!!', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 催送订单统计
     */
    private function tongjiOrderHurry($domain)
    {
        Log::write('开始执行催送统计', Log::INFO, Log::FILE, $this->LogFile);
        // 催餐统计表
        $tongjiHurryModel = M('tongjihurry', 'rms_', $this->dnsConnectionDB);

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        // 清除以前的记录
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiHurryModel->where($where)->delete();

        // 查询订单记录
        $where = array();
        $where['hurrynumber'] = array(
            'GT',
            0,
        );
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderformValue) {
            $data = array();
            $data['address'] = $orderformValue['address'];
            $data['ordertxt'] = $orderformValue['ordertxt'];
            $data['telname'] = $orderformValue['telname'];
            $data['rectime'] = $orderformValue['rectime'];
            $data['custtime'] = $orderformValue['custtime'];
            $data['hurrytime'] = $orderformValue['hurrytime'];
            $data['hurrynumber'] = $orderformValue['hurrynumber'];
            //计算催的时间
            $hurrytimetime = strtotime($orderformValue['hurrytime']) - strtotime($orderformValue['custtime']);
            $hurrytimetime = $hurrytimetime / 60;
            $data['hurrytimetime'] = $hurrytimetime; //催送的间隔时间
            $data['sendname'] = $orderformValue['sendname'];
            $data['company'] = $orderformValue['company'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['domain'] = $domain;
            $tongjiHurryModel->create();
            $tongjiHurryModel->add($data);
        }
    }

    // 统计退餐和废单
    private function tongjiOrderBackCancel($domain)
    {
        Log::write('开始执行退餐废单统计', Log::INFO, Log::FILE, $this->LogFile);
        // 催餐统计表
        $tongjiorderbcModel = M('tongjiorderbc', 'rms_', $this->dnsConnectionDB);

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        // 清除以前的记录
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiorderbcModel->where($where)->delete();

        // 查询订单记录
        $where = array();
        $where['state'] = array(
            array(
                'like',
                '%退餐',
            ),
            array(
                'like',
                '%废%',
            ),
            'or',
        );
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderformValue) {
            $data = array();
            $data['ordersn'] = $orderformResult['ordersn'];
            $data['address'] = $orderformValue['address'];
            $data['ordertxt'] = $orderformValue['ordertxt'];
            $data['telname'] = $orderformValue['telname'];
            $data['rectime'] = $orderformValue['rectime'];
            $data['custtime'] = $orderformValue['custtime'];
            $data['totalmoney'] = $orderformValue['totalmoney'];
            $data['sendname'] = $orderformValue['sendname'];
            $data['company'] = $orderformValue['company'];
            $data['state'] = $orderformValue['state'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['domain'] = $domain;
            $tongjiorderbcModel->create();
            $tongjiorderbcModel->add($data);
        }

        Log::write('退餐废单统计完毕', Log::INFO, Log::FILE, $this->LogFile);
    }

    /**
     * 导出当时的订单到导出表中，统计要用到的
     */
    private function tongjiAllOrder($domain)
    {
        Log::write('开始执行导出所有订单的任务', Log::INFO, Log::FILE, $this->LogFile);
        // 导出订单表
        $tongjiallorderModel = M('tongjiallorder', 'rms_', $this->dnsConnectionDB);

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        // 清除以前的记录
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjiallorderModel->where($where)->delete();

        // 查询订单记录
        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $orderformValue) {
            $data = array();
            $data['ordersn'] = $orderformValue['ordersn'];
            $data['address'] = $orderformValue['address'];
            $data['ordertxt'] = $orderformValue['ordertxt'];
            $data['totalmoney'] = $orderformValue['totalmoney'];
            $data['telname'] = $orderformValue['telname'];
            $data['rectime'] = $orderformValue['rectime'];
            $data['custtime'] = $orderformValue['custtime'];
            $data['telphone'] = $orderformValue['telphone'];
            $data['sendname'] = $orderformValue['sendname'];
            $data['company'] = $orderformValue['company'];
            $data['beizhu'] = $orderformValue['beizhu'];
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['domain'] = $domain;
            $data['lastlogtime'] = date('Y-m-d H:i:s');
            $tongjiallorderModel->create();
            $tongjiallorderModel->add($data);
        }
        Log::write('导出订单任务执行完毕', Log::INFO, Log::FILE, $this->LogFile);
    }

    /********
     * 统计送达时间点数统计
     * 保存在数据表中sp_sendtime_order
     * php /Applications/XAMPP/htdocs/assisadmin.lihua.com/api.php Home/OrderTongji/sendtimeOrderTj
     * 服务器测试命令: /usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140513-Lra/czwork/litter.php OrderTongji/index
     */
    public function sendtimeOrderTj($domain)
    {
        //定义统计的数据组
        $a0115 = array();
        $a1630 = array();
        $a3140 = array();
        $a4150 = array();
        $a5160 = array();
        $a6100 = array();

        if ($domain == 'bj.lihuaerp.com') {
            $this->dnsConnectionDB = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com:3306/bjrms';
        } else {
            $this->dnsConnectionDB = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com:3306/czrms';
        }

        // 导出订单表
        $tongjisendtimeModel = M('tongjisendtime', 'rms_', $this->dnsConnectionDB);
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['domain'] = $domain;
        $tongjisendtimeModel->where($where)->delete();

        // 订单表
        $orderformModel = M('orderform', 'rms_', $this->dnsConnectionDB);

        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['company'] = array('NEQ', '');
        $where['domain'] = $domain;
        $companyResult = $orderformModel->distinct(true)->field('company')->where($where)->select();

        foreach ($companyResult as $company_value) {
            $company = $company_value['company'];
            $area_value = $domain;
            $a0115[$area_value][$company] = 0;
            $a1630[$area_value][$company] = 0;
            $a3140[$area_value][$company] = 0;
            $a4150[$area_value][$company] = 0;
            $a5160[$area_value][$company] = 0;
            $a6100[$area_value][$company] = 0;
        }
        $a0115[$area_value]['全部'] = 0;
        $a1630[$area_value]['全部'] = 0;
        $a3140[$area_value]['全部'] = 0;
        $a4150[$area_value]['全部'] = 0;
        $a5160[$area_value]['全部'] = 0;
        $a6100[$area_value]['全部'] = 0;

        $where = array();
        $where['custdate'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        //$where ['company'] = '怀南';
        $where['company'] = array('NEQ', '');
        $where['domain'] = $domain;
        //返回所有订单数据
        $order = $orderformModel->field('rectime,custtime,successtime,company,domain as area')->where($where)->select();

        foreach ($order as $value) {
            //计算要求时间
            if (!empty($value['custtime'])) {
                $custtime = strtotime($value['custtime']);
            } else {
                $custtime = strtotime($value['rectime']);
            }
            //计算到达时间
            if (!empty($value['successtime'])) {
                $donetime = strtotime($value['successtime']);
            } else {
                $donetime = 'max';
            }
            //计算送达的时间
            if ($donetime == 'max') {
                //还没有输入到达时间，所以无法计算
                $sendtime = 80;
            } else {
                $sendtime = ($donetime - $custtime) / 60;
            }

            //在1-15之内
            if (($sendtime > 1) and ($sendtime <= 15)) {
                $a0115[$value['area']][$value['company']] = $a0115[$value['area']][$value['company']] + 1;
                $a0115[$value['area']]['全部'] = $a0115[$value['area']]['全部'] + 1;
            }
            //在16-30之内
            if (($sendtime >= 16) and ($sendtime <= 30)) {
                $a1630[$value['area']][$value['company']] = $a1630[$value['area']][$value['company']] + 1;
                $a1630[$value['area']]['全部'] = $a1630[$value['area']]['全部'] + 1;
            }
            //在31-40之内
            if (($sendtime >= 31) and ($sendtime <= 40)) {
                $a3140[$value['area']][$value['company']] = $a3140[$value['area']][$value['company']] + 1;
                $a3140[$value['area']]['全部'] = $a3140[$value['area']]['全部'] + 1;
            }
            //在41-50之内
            if (($sendtime >= 41) and ($sendtime <= 50)) {
                $a4150[$value['area']][$value['company']] = $a4150[$value['area']][$value['company']] + 1;
                $a4150[$value['area']]['全部'] = $a4150[$value['area']]['全部'] + 1;
            }
            //在51-60之内
            if (($sendtime >= 51) and ($sendtime <= 60)) {
                $a5160[$value['area']][$value['company']] = $a5160[$value['area']][$value['company']] + 1;
                $a5160[$value['area']]['全部'] = $a5160[$value['area']]['全部'] + 1;
            }
            //在60以上
            if ($sendtime > 60) {
                $a6100[$value['area']][$value['company']] = $a6100[$value['area']][$value['company']] + 1;
                $a6100[$value['area']]['全部'] = $a6100[$value['area']]['全部'] + 1;
            }
        }
        $companyResult = $orderformModel->distinct(true)->field('company')->where($where)->select();

        foreach ($companyResult as $company_value) {
            $area_value = $domain;
            $company = $company_value['company'];
            //保存
            $data = array();
            $data['domain'] = $domain;
            $data['company'] = $company;
            $data['date'] = date('Y-m-d');
            $data['ap'] = $this->getAp();
            $data['a0115'] = $a0115[$area_value][$company];
            $data['a1630'] = $a1630[$area_value][$company];
            $data['a3140'] = $a3140[$area_value][$company];
            $data['a4150'] = $a4150[$area_value][$company];
            $data['a5160'] = $a5160[$area_value][$company];
            $data['a6100'] = $a6100[$area_value][$company];
            $where = array();
            $where['date'] = date('Y-m-d');
            $where['ap'] = $this->getAp();
            $where['company'] = $company;
            $where['domain'] = $domain;
            $tongjisendtimeModel->where($where)->delete();
            $tongjisendtimeModel->create();
            $tongjisendtimeModel->add($data);
        }
        //保存全部
        $data = array();
        $data['domain'] = $domain;
        $data['company'] = '全部';
        $data['date'] = date('Y-m-d');
        $data['ap'] = $this->getAp();
        $data['a0115'] = $a0115[$area_value]['全部'];
        $data['a1630'] = $a1630[$area_value]['全部'];
        $data['a3140'] = $a3140[$area_value]['全部'];
        $data['a4150'] = $a4150[$area_value]['全部'];
        $data['a5160'] = $a5160[$area_value]['全部'];
        $data['a6100'] = $a6100[$area_value]['全部'];
        $where = array();
        $where['date'] = date('Y-m-d');
        $where['ap'] = $this->getAp();
        $where['company'] = '全部';
        $where['domain'] = $domain;
        $tongjisendtimeModel->where($where)->delete();
        $tongjisendtimeModel->create();
        $tongjisendtimeModel->add($data);

    }

    // 获取当前时间的午别
    private function getAp()
    {
        $nowHour = date('H');
        if ((int) $nowHour >= 16) {
            $ap = '下午';
        } else {
            $ap = '上午';
        }
        return $ap;
    }
}
