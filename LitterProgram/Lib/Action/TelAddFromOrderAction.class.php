<?php

/**
 * 来电添加系统
 * 将来电填入地址系统中
 * 2014-06-10
 * 测试命令:
 * 电话测试:/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php TelAddFromOrder/testtel
 */
class TelAddFromOrderAction extends Action
{
    public function index()
    {

        $dataConnectArr = array(
            'bj.lihuaerp.com' => '',
            'cz.lihuaerp.com' => '',
            'nj.lihuaerp.com' => '',
            'wx.lihuaerp.com' => '',
            'sz.lihuaerp.com' => '',
            'sh.lihuaerp.com' => '',
            'gz.lihuaerp.com' => '',
        );

        $tableConnectArr = array(
            'bj.lihuaerp.com' => '',
            'cz.lihuaerp.com' => '',
            'nj.lihuaerp.com' => '_nj',
            'wx.lihuaerp.com' => '_wx',
            'sz.lihuaerp.com' => '_sz',
            'sh.lihuaerp.com' => '_sh',
            'gz.lihuaerp.com' => '',
        );

        $domain = $_GET['domain'];
        $connectDb = $dataConnectArr[$domain];

        //订单表
        $orderformModel = M('orderform', 'rms_', $connectDb);
        //来电客户表
        $telcustomerModel = M('telcustomer'.$tableConnectArr[$domain], 'rms_', $connectDb);
        //来电地址表
        $teladdressModel = M('teladdress'.$tableConnectArr[$domain], 'rms_', $connectDb);
        //发票
        $telinvoiceModel = M('telinvoice', 'rms_', $connectDb);

        //查订单
        $where = array();
        $where['domain'] = $domain;
        $orderformResult = $orderformModel->where($where)->select();
        foreach ($orderformResult as $value) {
            $telphone = $value['telphone'];
            //验证是否是电话号码
            if ( $this->isTel($telphone)) {
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
                    $data['company'] = $value['company'];
                    $data['domain'] = $domain;
                    $teladdressModel->create();
                    $teladdressModel->add($data);
                    var_dump($teladdressModel->getLastSql());

                    //删除最远的几个地址
                    $teladdressResule = $teladdressModel->where($where)->order('teladdressid asc')->select();
                    if(count($teladdressResule)>5){
                        $i  = count($teladdressResule);
                        foreach($teladdressResule as $telValue){
                            if($i <= 5 ){
                                break;  //已经是5个地址,可以退出
                            }
                            $teladdressid = $telValue['teladdressid'];
                            $where = array();
                            $where['teladdressid'] = $teladdressid;
                            $teladdressModel->where($where)->delete();
                        }
                    }
                    //还有发票
                    $invoiceHeader = $value['invoiceheader'];
                    $where = array();
                    $where['telcustomerid'] = $telcustomerid;
                    $telinvoiceResult = $telinvoiceModel->where($where)->select();
                    if($telinvoiceResult){
                        $data= array();
                        $data['telcustomerid'] = $telcustomerid;
                        $data['header'] = $invoiceHeader;
                        $data['domain'] = $domain;
                        $telinvoiceModel->create();
                        $telinvoiceModel->add($data);
                    }else{
                        $where = array();
                        $where['telcustomerid'] = $telcustomerid;
                        $data= array();
                        $data['telcustomerid'] = $telcustomerid;
                        $data['header'] = $invoiceHeader;
                        $telinvoiceModel->where($where)->save($data);
                    }
                } else {
                    //存入来电客户表
                    $data = array();
                    $data['name'] = $value['clientname'];
                    $data['telphone'] = $value['telphone'];
                    $data['rectime'] = date('Y-m-d');
                    $telcustomerModel->create();
                    $telcustomerModel->add($data);
                    var_dump($telcustomerModel->getLastSql());
                    //存入来电地址表
                    $data = array();
                    $data['telcustomerid'] = $telcustomerModel->getLastInsID();
                    $data['telphone'] = $value['value'];
                    $data['address'] = $value['address'];
                    $data['company'] = $value['company'];
                    $teladdressModel->create();
                    $teladdressModel->add($data);
                    var_dump($teladdressModel->getLastSql());
                    //保存发票
                    $data= array();
                    $data['telcustomerid'] = $telcustomerModel->getLastInsID();
                    $data['header'] = $invoiceHeader;
                    $data['domain'] = $domain;
                    $telinvoiceModel->create();
                    $telinvoiceModel->add($data);

                }
            }
        }
    }


    //电话测试
    public function testtel(){
        var_dump($this->isTel('#87779899'));
    }

    /**
     * @param $tel
     * @param string $type
     * @return bool
     * 验证电话号码
     */
    function isTel($tel, $type = '')
    {
        //处理电话前面的0
        if((strlen($tel) == 12) && (substr($tel,0,1) == '0') && (strpos($tel,'-') <= 0 )) {
            $tel = substr($tel,1);
        }
        $regxArr = array(
            'sj' => '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',
            'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            'otel' => '/^\d{7,9}(-\d+)?$/',
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
