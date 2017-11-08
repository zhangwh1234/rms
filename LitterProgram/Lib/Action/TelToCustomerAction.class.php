<?php

/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 16/7/29
 * Time: 下午4:18
 * /usr/local/php/bin/php /home/ftp/1520/lihuaerp-20140513-Lra/czwork/litter.php TelToCustomer/Index
 */
class TelToCustomerAction extends Action
{


    public function index()
    {


        $domain = 'sz.lihuaerp.com';
        $connectDb = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com:3306/czrms';

        //老表
        $szTelcustomerModel = M('telcustomer_sz', 'rms_', $connectDb);
        //老电话
        $szTeladdressModel = M('teladdress_sz', 'rms_', $connectDb);

        //来电客户表
        $telcustomerModel = M('telcustomer', 'rms_', $connectDb);
        //来电地址表
        $teladdressModel = M('teladdress', 'rms_', $connectDb);
        //发票
        $telinvoiceModel = M('telinvoice', 'rms_', $connectDb);

        //查订单
        $where = array();
        $where['domain'] = $domain;
        $where['state'] = 0;
        $szTelCustomerResult = $szTelcustomerModel->where($where)->limit(10000)->select();
        foreach ($szTelCustomerResult as $value) {
            $telphone = $value['telphone'];
            //验证是否是电话号码
            //if ($this->isTel($telphone)) {
                //是电话号码,继续执行
                $where = array();
                $where['telphone'] = $value['telphone'];
                $where['domain'] = $domain;
                $telcustomerResult = $telcustomerModel->where($where)->find();
                if ($telcustomerResult) {
                    $telcustomerid = $telcustomerResult['telcustomerid'];
                    //更新来电客户表
                    $where = array();
                    $where['telphone'] = $value['telphone'];
                    $data = array();
                    $data['rectime'] = date('Y-m-d');
                    $data['address'] = $value['address'];
                    $telcustomerModel->where($where)->save($data);
                    var_dump($telcustomerModel->getLastSql());
                    $telcustomerResult = $telcustomerModel->where($where)->find();
                    var_dump($telcustomerResult);
                    //更新来电地址表
                    $where = array();
                    $where['telcustomerid'] = $$telcustomerid;
                    $data = array();
                    $data['telcustomerid'] = $telcustomerid;
                    $data['address'] = $value['address'];
                    $data['company'] = '';
                    $data['domain'] = $domain;
                    $teladdressModel->create();
                    $teladdressModel->add($data);
                    var_dump($teladdressModel->getLastSql());



                } else {
                    //存入来电客户表
                    $data = array();
                    $data['name'] = '';
                    $data['telphone'] = $value['telphone'];
                    $data['rectime'] = date('Y-m-d');
                    $data['address'] = $value['address'];
                    $data['domain'] = $domain;
                    $telcustomerModel->create();
                    $telcustomerModel->add($data);
                    var_dump($telcustomerModel->getLastSql());
                    //存入来电地址表
                    $data = array();
                    $data['telcustomerid'] = $telcustomerModel->getLastInsID();
                    $data['telphone'] = $value['value'];
                    $data['address'] = $value['address'];
                    $data['company'] = '';
                    $data['domain'] = $domain;
                    $teladdressModel->create();
                    $teladdressModel->add($data);
                    var_dump($teladdressModel->getLastSql());
                }
            //}
            $where = array();
            $where['telphone'] = $value['telphone'];
            $where['domain'] = $domain;
            $data = array();
            $data['state'] = 1;
            $szTelcustomerModel->where($where)->save($data);
            var_dump($szTelcustomerModel->getLastSql());
        }
    }

    /**
     * @param $tel
     * @param string $type
     * @return bool
     * 验证电话号码
     */
    function isTel($tel, $type = '')
    {
        $regxArr = array(
            'sj' => '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',
            'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            '400' => '/^400(-\d{3,4}){2}$/',
        );
        if ($type && isset($regxArr[$type])) {
            return preg_match($regxArr[$type], $tel) ? true : false;
        }
        foreach ($regxArr as $regx) {
            if (preg_match($regx, $tel)) {
                return true;
            }
        }
        return false;
    }

}

?>
