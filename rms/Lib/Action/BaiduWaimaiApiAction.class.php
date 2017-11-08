<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2017/11/24
 * Time: 上午11:08
 * 百度外卖入口守护程序，百度外卖新API的callbackurl
 */

class BaiduWaimaiApiAction extends Action
{

    public function index()
    {

        // 定义日志文件
        $LogFile = LOG_PATH . 'apiBaiduWaimai_' . date('Y_m_d') . ".log";

        // 记入日志
        Log::write('有人在访问：', Log::INFO, Log::FILE, $LogFile);
        Log::write('客户浏览器：' . $_SERVER ['HTTP_USER_AGENT'], Log::INFO, Log::FILE, $LogFile);
        Log::write('来源IP：' . $_SERVER ['REMOTE_ADDR'], Log::INFO, Log::FILE, $LogFile);

        $data = file_get_contents('php://input');
        Log::write('数据：' . $data, Log::INFO, Log::FILE, $LogFile);
        if (empty ($_POST)) {
            var_dump('hello,I miss you!225');
            exit ();
        }

        // 保存到日志中
        Log::write('获得百度外卖推送', Log::INFO, Log::FILE, $LogFile);
        $baiduJson = json_encode($_POST);
        Log::write($baiduJson, Log::INFO, Log::FILE, $LogFile);

        //$input = '{"cmd":"order.create","timestamp":"1511490366","version":"3","ticket":"F9426A35-B62B-B950-28CB-A131F1A44D28","source":"32622","body":{"order_id":"15114900955445"},"sign":"10F600FC5F3C336A1F5BFF60C26B8515","encrypt":""}';
        //$input = '{"body":{"source":"apitest","shop":{"id":"123","name":"\u767e\u5ea6\u6d4b\u8bd5\u5546\u6237","baidu_shop_id":"111961344375"},"order":{"order_id":"14398939438680","send_immediately":1,"send_time":"1","send_fee":500,"package_fee":0,"discount_fee":1200,"total_fee":4700,"shop_fee":3696,"user_fee":3500,"pay_type":2,"pay_status":2,"need_invoice":2,"invoice_title":"","remark":"","delivery_party":1,"create_time":"1439893943"},"user":{"name":"\u5f20\u4e09","phone":"13800138000","gender":1,"address":"\u594e\u79d1\u79d1\u6280\u5927\u53a6","coord":{"longitude":116.366936,"latitude":39.957442}},"products":[{"product_id":"24627","product_name":"\u5357\u74dc\u7ca5","product_price":800,"product_amount":1,"product_fee":800,"package_price":0,"package_amount":"1","package_fee":0,"total_fee":800,"upc":""},{"product_id":"24711","product_name":"\u96ea\u68a8\u94f6\u8033\u767e\u5408\u7ca5","product_price":900,"product_amount":1,"product_fee":900,"package_price":0,"package_amount":"1","package_fee":0,"total_fee":900,"upc":""},{"product_id":"24639","product_name":"\u6392\u9aa8\u7096\u8c46\u89d2","product_price":2200,"product_amount":1,"product_fee":2200,"package_price":0,"package_amount":"1","package_fee":0,"total_fee":2200,"upc":""},{"product_id":"24669","product_name":"\u56db\u5ddd\u6ce1\u83dc","product_price":300,"product_amount":1,"product_fee":300,"package_price":0,"package_amount":"1","package_fee":0,"total_fee":300,"upc":""}],"discount":[{"type":"jian","activity_id":"12423e210ece56bfeb607e01ee9210fb","fee":1000,"baidu_rate":1000,"shop_rate":0,"agent_rate":0,"logistics_rate":0,"desc":"\u7acb\u51cf\u4f18\u60e0"},{"type":"payenjoy","activity_id":"12423e210ece56bfeb607e01ee9210fb","fee":200,"baidu_rate":200,"shop_rate":0,"agent_rate":0,"logistics_rate":0,"desc":"\u5728\u7ebf\u652f\u4ed8"}]},"cmd":"order.create","sign":"62D58B8154AAF02CDE8E3E01CBF4FB4B","source":"apitest","ticket":"AC88FF4A-08AC-E62E-F8FA-CB45B6FCECB6","timestamp":1439893955,"version":2}';
        //将$input decode成数组
        $input = json_decode($data,true);

        import('@.Extend.BaiduOpenApi');

        $config = array();
        $config['encrypt'] = ''; //加密方式;普通对接对解放为空
        $config['source'] =  $input['source'];
        $config['secret'] =  $this->getSecret($config['source']);
        $config['url'] = 'http://api.waimai.baidu.com';

        // 实例化对象
        $obj = new BaiduOpenApi($config);

        $paraseInput = $obj->parseCallback($input);

        //开始解析数据
        if (false === $paraseInput) {
            Log::write($obj->getLastError(), Log::INFO, Log::FILE, $LogFile);
        } else {
            Log::write('解析订单数据成功', Log::INFO, Log::FILE, $LogFile);

            //获取解析出来的命令
            $cmd = $obj->getLastCmd();

            $data = array();
            $res = array();
            if ('order.create' == $cmd) {
                //获取推送过来的订单数据
                $orderInfo = $obj->getLastBody();
                $this->orderidSave($orderInfo['order_id'], 'bj.lihuaerp');
                Log::write('保存订单号:'.$orderInfo['order_id'], Log::INFO, Log::FILE, $LogFile);
                $data = array(
                    'source_order_id' => rand(),
                );
                $res = $obj->buildRes($cmd, $obj->getLastTicket(), 0, 'SUCCESS', $data);
            } elseif ('order.status.push' == $cmd) {
                //哦，我收到的是订单状态通知命令...
                //像处理订单一样，开始处理
            } else {
                //嗯？这是什么命令，我无法识别，怎么办？写个错误原因告诉百度吧，并且错误码填写一个非零的数字
                $errno = 1;
                $error = '这个命令没有实现';
                $res = $obj->buildRes('', $obj->getLastTicket(), $errno, $error, $data);
            }
            $res = json_encode($res);
            echo $res . PHP_EOL;
        }

    }

    public function Message()
    {

        $data['message'] = 'ok';
        $this->ajaxReturn($data);
    }

    //将订单号插入表中
    function orderidSave($orderid, $domain)
    {
        $other_connect_str = C('OTHER_CONNECTSTR');
        //订单表
        $baiduwaimaiorderid_model = M('baiduwaimaiorderid', ' ', $other_connect_str);
        $where = array();
        $where['ordersn'] = $orderid;
        $where['domain'] = $domain;
        $baiduwaimaiorderid = $baiduwaimaiorderid_model->where($where)->select();
        if (count($baiduwaimaiorderid) > 0) {
            $data = array();
            $data['state'] = 0;
            $data['insertdate'] = date('Y-m-d H:i:s');
            $data['source'] = '32622';
            $data['domain'] = $domain;
            $baiduwaimaiorderid_model->$where($where)->save($data);
        } else {
            $data = array();
            $data['ordersn'] = $orderid;
            $data['insertdate'] = date('Y-m-d H:i:s');
            $data['source'] = '32622';
            $data['domain'] = $domain;
            $baiduwaimaiorderid_model->create();
            $baiduwaimaiorderid_model->add($data);
        }
    }

    // 获取secret
    public function getSecret($source)
    {
        if ($source == '30003') {
            return 'edc2a57943a77f17';
        } elseif ($source == '30004') {
            return '8a717a832cbe50a3';
        } elseif ($source == '30015') {
            return '34578b01219ce85e';
        } elseif ($source == '30016') {
            return 'e4949e398bffff6f';
        } elseif ($source == 'lihuash') {
            return 'tiU4YVuzGYKCUXyb';
        } elseif ($source == '30068') {  //广州
            return '04c6a7d2dbb6cad7';
        } elseif ($source == 'lihua') {  //北京
            return '02334mskd003';
        } elseif ($source == '32622') {  //北京新的id
            return '04f9750e760a24b7';
        } else {
            return '';
        }
    }

    /**
     * 把传过来的数据解析成数组
     */
    private function parseArray($inputStr)
    {
        $temp = explode($inputStr);
        $returnArray = array();
        foreach ($temp as $value) {
            $middle = explode('=', $value);
            if ('cmd' == $middle[0]) {
                $returnArray['cmd'] = $middle[1];
            }
            if ('timestamp' == $middle[0]) {
                $returnArray['timestamp'] = $middle[1];
            }
            if ('version' == $middle[0]) {
                $returnArray['version'] = $middle[1];
            }
            if ('ticket' == $middle[0]) {
                $returnArray['ticket'] = $middle[1];
            }
            if ('source' == $middle[0]) {
                $returnArray['source'] = $middle[1];
            }
            if ('body' == $middle[0]) {
                $returnArray['body'] = $middle[1];
            }
            if ('sign' == $middle[0]) {
                $returnArray['sign'] = $middle[1];
            }
            if ('encrypt' == $middle[0]) {
                $returnArray['encrypt'] = $middle[1];
            }
        }
    }







}
