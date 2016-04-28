<?php

/**
 * Created by zhangwh1234
 * User: apple
 * Date: 16/3/10
 * Time: 上午11:47
 * 通过营收系统，发送送餐员的名字到小助手的服务器数据库中
 * 测试命令:/Applications/XAMPP/xamppfiles/bin/php /Applications/XAMPP/htdocs/rms/litter.php YingshouAssis/pushCzOrder
 */
class YingshouAssisAction extends Action
{
    protected $url = 'http://localhost/assisadmin.lihua.com/index.php?g=portal&m=AppOrder&a=yingshouPushOrder';


    /**
     * 发送常州的数据
     */
    public function pushCzOrder()
    {
        $assistant_model = M('assistant', Null, 'mysql://root:@localhost/bjlihua');
        $assis = $assistant_model->where(1)->select();
        var_dump($assis);
        exit;
        // 载入curl函数等
        load("@.function");

        $info = $this->getCzOrder();
        $fileds['param'] = json_encode($info);
        $resp = curl($this->url, $fileds);
        $order_result = json_decode($resp, true);
        $order_result = json_decode($order_result['data']);
        foreach ($order_result as $value) {
            $where = array();
            $where['cOrderID'] = $value;
            $data = array();
            $data['sendstatus'] = 1;
            $assistant_model = M('assistant', Null, 'mysql://root:@localhost/bjlihua');
            $assistant_model->where($where)->save($data);
        }
        usleep(100000);
        exit;

        while (1) {

        }
    }

    /**
     * 获取常州的数据
     */
    function getCzOrder()
    {
        $orderform_model = M('kforderform', Null, 'mysql://root:@localhost/bjlihua');
        $assistant_model = M('assistant', Null, 'mysql://root:@localhost/bjlihua');
        $assistant = $assistant_model->where('sendstatus=0')->order('id desc')->select();

        foreach ($assistant as $assis) {
            $where = array();
            $where['cOrderID'] = $assis['cOrderID'];
            $orderform = $orderform_model->where($where)->find();
            if ($orderform) {
                $info_assis = array();
                $info_assis['order_id'] = $orderform['cOrderID'];
                $info_assis['employee'] = $orderform['cCustName'];
                $info_assis['area'] = '常州';
            }
            $info[] = $info_assis;
        }

        return $info;
    }
}