<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 17/3/10
 * Time: 上午10:03
 * 发票开具页面
 * http://localhost/rms/index.php?s=/InvoiceWeb/index.html
 * 2019-05-20 兼容国信电子的发票系统
 */

class InvoiceWebAction extends Action
{
    //定义链接字符串
    protected $connect_str = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com/czrms#utf8';
    protected $bjconnect_str = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq618.mysql.rds.aliyuncs.com/bjrms#utf8';
    protected $webconnect_str = 'mysql://rootlihua:zhangwh0731@rdsjj7vrrnaby2m955.mysql.rds.aliyuncs.com/invoice#utf8';

    public function test()
    {
        $this->display('openinvoice');
    }

    public function test_cancel()
    {
        $this->display('cancelinvoice');
    }

    public function test_show()
    {
        $invoicenumber = I('get.invoicenumber', '', 'strip_tags');
        $connect_str = 'mysql://root:@localhost/invoice#utf8';
        $invoicewebModel = M('invoiceweb', 'rms_', $connect_str);

        //查询发票内容
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $invoicewebModel->where($where)->find();
        if (!empty($invoicewebResult['pdf_content'])) {
            if (mb_strlen($invoicewebResult['header'], 'utf-8') > 16) {
                $invoicewebResult['header'] = mb_substr($invoicewebResult['header'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['header'] = $invoicewebResult['header'];
            }

            if (mb_strlen($invoicewebResult['xsf_dzdh'], 'utf-8') > 16) {
                $invoicewebResult['xsf_dzdh'] = mb_substr($invoicewebResult['xsf_dzdh'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['xsf_dzdh'] = $$invoicewebResult['xsf_dzdh'];
            }
            if (mb_strlen($invoicewebResult['xsf_mc'], 'utf-8') > 16) {
                $invoicewebResult['xsf_mc'] = $invoicewebResult['xsf_mc'];
            } else {
                $invoicewebResult['xsf_mc'] = $invoicewebResult['xsf_mc'];
            }

            //重新标示id
            $invoicewebResult['invoiceid'] = $invoicewebResult['invoicewebid'];
            //计算金额(不含税)
            //不含税单价
            $xmdj = ($invoicewebResult['money'] / (1 + $invoiceelectronResult['sl']));
            $xmdj = round($xmdj, 2);
            $invoicewebResult['xmdj'] = $xmdj; //金额(不含税)
            //税额
            $suimoney = $invoicewebResult['money'] - $xmdj;
            $invoicewebResult['suimoney'] = $suimoney;

            $invoicewebResult['url_pdf'] = "http://invoice.lihua.com/index.php?s=InvoiceWeb/downloadPDF/invoicenumber/" . $invoicenumber;
        }
        //发票已经开具
        $this->assign('invoiceinfo', $invoicewebResult);
        $this->display('showinvoice');
    }

    public function test_status()
    {
        $this->display('statusinvoice');
    }

    public function test_verify()
    {
        $this->display('verifyinvoice');
    }
    public function test_error()
    {
        $this->display('errorinvoice');
    }

    public function testshow()
    {

        //ob_end_clean();
        //Header("Content-type:image/gif");
        //header('Content-Disposition: attachment; filename="'.$invoicewebResult['ordersn'].'.pdf"');
        // echo($invoicewebResult['pdf_content']);
        //echo $invoicewebResult['pdf_content'];
        //echo 'test';
        //echo "<embed src='http://118.178.137.135/image.php?s=/InvoiceWeb/getpdf' type='application/pdf' width='100%' height='100%'>";

        $this->display();
    }

    public function testdown()
    {
        $url = 'http://dev.fapiao.com:19080/dzfp-platform/downloadAction.do?method=download&request=e5uhf8WETIOMgaa2cCUMtnx08-Fz6GM2mBjCcKyBCX3uZQpkecGb3BGuhe.YCwUh.AeL8Kfc1oHJzQAZYwB.Bg__%5EDhaDEahJca';
        //var_dump($url);
        import('ORG.Net.Http');
        Http::fsockopenDownload($url);
    }

    public function index()
    {

    }

    /**
     * 显示发票提取码,让客户输入发票提取码
     */
    public function verify()
    {
        //发票提取码
        $invoicenumber = I('get.number', '', 'strip_tags');

        //提取发票提取码的第一个字，判断是哪个城市
        $firstCode = substr($invoicenumber, 0, 1);
        if ($firstCode == '1') {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->bjconnect_str);
            $invoiceweblogModel = M('invoiceweblog', 'rms_', $this->bjconnect_str);
        } else {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->connect_str);
            $invoiceweblogModel = M('invoiceweblog', 'rms_', $this->connect_str);
        }

        //对发票提取码进行验证
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $rmsinvoicewebModel->where($where)->find();

        if (($invoicewebResult['state'] == 2) and ($invoicewebResult['download_state'] == 2)) {
            $url = U('InvoiceWeb/show_invoice', 'number=' . $invoicenumber);
            header('Location:' . $url);
        }

        if (!empty($invoicenumber)) {
            $this->assign('invoicenumber', $invoicenumber);
        }

        //保存到日志中,方便查询
        $data = array();
        $data['ordersn'] = $invoicewebResult['ordersn'];
        $data['date'] = date('Y-m-d H:i:s');
        $data['log'] = '用户申请开票';
        $invoiceweblogModel->create();
        $invoiceweblogModel->add($data);

        $this->display('verifyinvoice');
    }

    /**
     * 验证验证码和发票提取码
     */
    public function verifynumber()
    {

        $invoicenumber = I('get.invoicenumber', '', 'strip_tags');
        $vCodeId = $_REQUEST['vCodeId'];

        //对验证码进行验证
        if (empty($vCodeId)) {
            $data['error'] = 1;
            $data['info'] = '验证码必须！';
            $this->ajaxReturn($data);
        }
        if (empty($invoicenumber)) {
            $data['error'] = 1;
            $data['info'] = '发票提取码必须！';
            $this->ajaxReturn($data);
        }

        if ($_SESSION['verify'] != md5($vCodeId)) {
            $data['error'] = 1;
            $data['info'] = '验证码错误！';
            // $this->ajaxReturn($data);
        }

        //提取发票提取码的第一个字，判断是哪个城市
        $firstCode = substr($invoicenumber, 0, 1);
        if ($firstCode == '1') {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->bjconnect_str);
        } else {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->connect_str);
        }

        //对发票提取码进行验证
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $rmsinvoicewebModel->where($where)->find();

        $data = array();
        if (empty($invoicewebResult)) {
            //电子发票不存在,不能开具
            $data['error'] = 1;
            $data['info'] = '电子发票提取码不存在！';
            $this->ajaxReturn($data);
        }

        //发票还没有开，处理开
        if ($invoicewebResult['state'] == 0) {
            $where = array();
            $where['eticketno'] = $invoicenumber;
            $data = array();
            $data['state'] = 1; //1为开票
            $rmsinvoicewebModel->where($where)->save($data);
            $data = array();
            $data['error'] = 0;
            $data['info'] = '启动开票！';
            $data['url'] = U('InvoiceWeb/status_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

        //发票还没有开，现在正在处理状态
        if ($invoicewebResult['state'] == 1) {
            $data['error'] = 0;
            $data['info'] = '启动开票！';
            $data['url'] = U('InvoiceWeb/status_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

        if (($invoicewebResult['state'] == 2) and ($invoicewebResult['download_state'] == 1)) {
            $data['error'] = 0;
            $data['info'] = '启动开票！';
            $data['url'] = U('InvoiceWeb/status_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

        //发票已经开，显示发票
        if (($invoicewebResult['state'] == 2) and ($invoicewebResult['download_state'] == 2)) {
            $data['error'] = 0;
            $data['info'] = '显示发票！';
            //$data['url'] = APP_PATH .'index.php?s=/InvoiceWeb/show_invoice/number/' . $invoicenumber;
            $data['url'] = U('InvoiceWeb/show_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

        //发票退票
        if ($invoicewebResult['state'] == 3) {
            //退票显示
            if ($invoicewebResult['cancel_state'] == 2) {
                $this->assign('invoicenumber', $invoicenumber);
            }
            //电子发票不存在,不能开具
            $data['error'] = 0;
            $data['info'] = '电子发票退票！';
            $data['url'] = U('InvoiceWeb/cancel_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

        //发票错误
        if ($invoicewebResult['state'] == 4) {
            //电子发票错误
            $data['error'] = 0;
            $data['info'] = '启动开票！';
            $data['url'] = U('InvoiceWeb/error_invoice', 'number=' . $invoicenumber);
            $this->ajaxReturn($data);
        }

    }

    /**
     * 显示正在处理开票的状态
     */
    public function status_invoice()
    {

        //发票提取码
        $invoicenumber = I('get.number', '', 'strip_tags');
        //显示错误
        if (empty($invoicenumber)) {

        }
        if (!empty($invoicenumber)) {
            $this->assign('invoicenumber', $invoicenumber);
        }
        $this->display('statusinvoice');
    }

    /**
     * 显示发票退票状态
     */
    public function cancel_invoice()
    {
        //发票提取码
        $invoicenumber = I('get.number', '', 'strip_tags');
        //显示错误
        if (empty($invoicenumber)) {

        }
        $invoicewebModel = M('invoiceweb', 'rms_', $this->webconnect_str);

        //对发票提取码进行验证
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $invoicewebModel->field('state')->where($where)->find();

        $data = array();
        if (empty($invoicewebResult)) {
            //电子发票不存在,不能开具
            $data['error'] = 1;
            $data['info'] = '电子发票提取码不存在！';
            $this->assign('invoicenumber', $invoicenumber);
            $this->assign('errorInfo', '电子发票提取码不存在！');
            $this->display('errorinvoice');
        }

        //提取发票提取码的第一个字，判断是哪个城市
        $firstCode = substr($invoicenumber, 0, 1);
        if ($firstCode == '1') {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->bjconnect_str);
        } else {
            $rmsinvoicewebModel = M('invoiceweb', 'rms_', $this->connect_str);
        }

        //重新查询invoiceweb的数据
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $rmsinvoicewebModel->where($where)->find();
        $this->assign('invoiceinfo', $invoicewebResult);
        $this->display('cancelinvoice');
    }

    /**
     * 显示已经开好的发票
     */
    public function show_invoice()
    {

        $invoicenumber = I('get.number', '', 'strip_tags');
        $invoicewebModel = M('invoiceweb', 'rms_', $this->webconnect_str);

        //查询发票内容
        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $invoicewebModel->where($where)->find();

        $data = array();
        if (empty($invoicewebResult)) {
            //电子发票不存在,不能开具
            $data['error'] = 1;
            $data['info'] = '电子发票提取码不存在！';
            $this->assign('invoicenumber', $invoicenumber);
            $this->assign('errorInfo', '电子发票提取码不存在！');
            $this->display('errorinvoice');
        }

        //提取发票提取码的第一个字，判断是哪个城市
        //如果不是北京，是其他城市，改成微信
        $firstCode = substr($invoicenumber, 0, 1);
        if ($firstCode == '1') {

        } else {
            $weixin_url = $invoicewebResult['pdf_url_weixin'];

            header("Location:" . $weixin_url);
        }

        if (!empty($invoicewebResult['pdf_content'])) {
            if (mb_strlen($invoicewebResult['header'], 'utf-8') > 16) {
                $invoicewebResult['header'] = mb_substr($invoicewebResult['header'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['header'] = $invoicewebResult['header'];
            }

            if (mb_strlen($invoicewebResult['xsf_dzdh'], 'utf-8') > 16) {
                $invoicewebResult['xsf_dzdh'] = mb_substr($invoicewebResult['xsf_dzdh'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['xsf_dzdh'] = $$invoicewebResult['xsf_dzdh'];
            }
            if (mb_strlen($invoicewebResult['xsf_mc'], 'utf-8') > 16) {
                $invoicewebResult['xsf_mc'] = $invoicewebResult['xsf_mc'];
            } else {
                $invoicewebResult['xsf_mc'] = $invoicewebResult['xsf_mc'];
            }

            //重新标示id
            $invoicewebResult['invoiceid'] = $invoicewebResult['invoicewebid'];
            //计算金额(不含税)
            //不含税单价
            $xmdj = ($invoicewebResult['money'] / (1 + 0.6)); //$invoiceelectronResult['sl'] = 0.6
            $xmdj = round($xmdj, 2);
            $invoicewebResult['xmdj'] = $xmdj; //金额(不含税)
            //税额
            $suimoney = $invoicewebResult['money'] - $xmdj;
            $invoicewebResult['suimoney'] = $suimoney;

            $invoicewebResult['url_pdf'] = "http://invoice.lihua.com/index.php?s=InvoiceWeb/downloadPDF/invoicenumber/" . $invoicenumber;
        }

        //获取电子发票的参数
        $where = array();
        $where['domain'] = 'localhost'; //$invoicewebResult['domain'];
        $invoiceeleparaModel = M('invoiceelepara', 'rms_', $this->connect_str);
        $invoiceeleparaResult = $invoiceeleparaModel->where($where)->find();
        $invoicewebResult['xsf_mc'] = mb_substr($invoiceeleparaResult['xsf_mc'], 0, 20);
        $invoicewebResult['xsf_dzdh'] = mb_substr($invoiceeleparaResult['xsf_dzdh'], 0, 20);

        //发票已经开具
        $this->assign('invoiceinfo', $invoicewebResult);
        $this->display('showinvoice');
    }

    /**
     * 显示发票错误的原因
     */
    public function error_invoice()
    {
        $invoicenumber = I('get.number', '', 'strip_tags');
        $invoicewebModel = M('invoiceweb', 'rms_', $this->connect_str);

        $where = array();
        $where['eticketno'] = $invoicenumber;
        $invoicewebResult = $invoicewebModel->where($where)->find();

        //获取订单号
        $ordersn = $invoicewebResult['ordersn'];
        $where = array();
        $where['ordersn'] = $ordersn;
        //获取错误日志
        $invoiceweblogModel = M('invoiceweblog', 'rms_', $this->connect_str);
        $invoiceweblogResult = $invoiceweblogModel->where($where)->select();

        $this->assign('invoicenumber', $invoicenumber);
        $this->assign('errorInfo', $invoiceweblogResult);
        $this->display('errorinvoice');
    }

    /**
     * 下载发票，PDF文件
     */
    public function downloadPDF()
    {
        $invoicenumber = I('get.invoicenumber', '', 'strip_tags');
        // $connect_str = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/bjrms#utf8';
        $invoicewebModel = M('invoiceweb', 'rms_', $this->bjconnect_str);
        //对发票提取码进行验证
        $where = array();
        $where['eticketno'] = $invoicenumber;
        //删除sql欺骗

        $invoicewebResult = $invoicewebModel->where($where)->find();

        ob_end_clean();
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $invoicenumber . '.pdf"');
        echo ($invoicewebResult['pdf_content']);
    }

    /**
     * 直接查看PDF文件
     */
    public function getpdf()
    {
        //$connect_str = 'mysql://rootlihua:zhangwh0731@rdsq6jvauvez7rq.mysql.rds.aliyuncs.com/bjrms#utf8';
        $invoicewebModel = M('invoiceweb', 'rms_', $this->bjconnect_str);
        $where = array();
        $where['ordersn'] = $_REQUEST['ordersn'];
        $invoicewebResult = $invoicewebModel->where($where)->find();
        ob_end_clean();
        Header("Content-type:application/pdf");
        //header('Content-Disposition: attachment; filename="'.$invoicewebResult['ordersn'].'.pdf"');
        echo $invoicewebResult['pdf_content'];
    }

    /**
     * 开具发票,将状态设置为1
     */
    public function openinvoice()
    {

        $invoicewebModel = D('invoiceweb');
        $where = array();
        $where['invoicewebid'] = $_REQUEST['invoiceid'];
        $data = array();
        $data['state'] = 1;
        $data['header'] = I('post.header', '', 'strip_tags');
        if (!empty($_REQUEST['invtaxno'])) {
            $data['gmf_nsrsbh'] = $_REQUEST['invtaxno'];
        }
        if (!empty($_REQUEST['invaddrphone'])) {
            $data['gmf_dzdh'] = $_REQUEST['invaddrphone'];
        }
        if (!empty($_REQUEST['invbank'])) {
            $data['gmf_yhzh'] = $_REQUEST['invbank'];
        }
        if (!empty($_REQUEST['sendmail'])) {
            $data['email'] = I('post.sendmail', '', 'strip_tags');
        }
        if (!empty($_REQUEST['telphone'])) {
            $data['telphone'] = I('post.telphone', '', 'strip_tags');
        }

        $invoicewebResult = $invoicewebModel->where($where)->save($data);

        $invoicewebResult = $invoicewebModel->field('eticketno')->where($where)->find();

        $data = array();
        if ($invoicewebResult) {
            $this->assign('invoicenumber', $invoicewebResult['eticketno']);
            $data['open'] = $this->fetch('statusvoice');
            $data['error'] = 0;
            $this->ajaxReturn($data);
        } else {
            $data['error'] = 1;
            $data['info'] = '';
            $this->ajaxReturn($data);
        }

    }

    /**
     * 验证码
     */
    public function verifyCode()
    {
        ob_clean();
        import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, 'png', 120, 25);
    }

    //
    public function bak()
    {
        vendor("phpqrcode.phpqrcode");
        $data = 'http://192.168.0.135/rms/index.php?s=/InvoiceWeb/show.html';
        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        //$path = "images/";
        // 生成的文件名
        //$fileName = $path.$size.'.png';
        QRcode::png($data, false, $level, $size);
        //$this->display('invoiceweb');

        //发票是否开具
        if (!empty($invoicewebResult['pdf_content'])) {
            if (mb_strlen($invoicewebResult['header'], 'utf-8') > 16) {
                $invoicewebResult['header'] = mb_substr($invoicewebResult['header'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['header'] = $invoicewebResult['header'];
            }

            if (mb_strlen($invoiceelectronResult['xsf_dzdh'], 'utf-8') > 16) {
                $invoicewebResult['xsf_dzdh'] = mb_substr($invoiceelectronResult['xsf_dzdh'], 0, 16, 'utf-8') . '...';
            } else {
                $invoicewebResult['xsf_dzdh'] = $invoiceelectronResult['xsf_dzdh'];
            }
            if (mb_strlen($invoiceelectronResult['xsf_mc'], 'utf-8') > 16) {
                $invoicewebResult['xsf_mc'] = $invoiceelectronResult['xsf_mc'];
            } else {
                $invoicewebResult['xsf_mc'] = $invoiceelectronResult['xsf_mc'];
            }

            //重新标示id
            $invoicewebResult['invoiceid'] = $invoicewebResult['invoicewebid'];
            //计算金额(不含税)
            //不含税单价
            $xmdj = ($invoicewebResult['money'] / (1 + $invoiceelectronResult['sl']));
            $xmdj = round($xmdj, 2);
            $invoicewebResult['xmdj'] = $xmdj; //金额(不含税)
            //税额
            $suimoney = $invoicewebResult['money'] - $xmdj;
            $invoicewebResult['suimoney'] = $suimoney;

            $invoicewebResult['url_pdf'] = "http://invoice.lihua.com/index.php?s=InvoiceWeb/downloadPDF/invoicenumber/" . $invoicenumber;
            //发票已经开具
            $this->assign('invoiceinfo', $invoicewebResult);

            if ($invoicewebResult['state'] == 2) {
                $this->assign('invoicenumber', $invoicenumber);
                $data['open'] = $this->fetch('showinvoice');
            }
            //退票显示
            if ($invoicewebResult['cancel_state'] == 2) {
                $this->assign('invoicenumber', $invoicenumber);
                $data['open'] = $this->fetch('cancelinvoice');
            }

            //显示发票
            $data['error'] = 0;
            $data['info'] = '';
            $this->ajaxReturn($data);
        }
        return;
        if ($invoicewebResult['state'] == 0) {
            //重新标示id
            $invoicewebResult['invoiceid'] = $invoicewebResult['invoicewebid'];
            //发票还没有开具
            $this->assign('invoiceinfo', $invoicewebResult);
            $data['open'] = $this->fetch('openinvoice');
        }

    }
}
