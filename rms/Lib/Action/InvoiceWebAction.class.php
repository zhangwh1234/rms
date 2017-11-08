<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 17/3/10
 * Time: 上午10:03
 * 发票开具页面
 * http://localhost/rms/index.php?s=/InvoiceWeb/index.html
 */

class InvoiceWebAction extends Action{

    public function testdown(){
        $url = 'http://dev.fapiao.com:19080/dzfp-platform/downloadAction.do?method=download&request=e5uhf8WETIOMgaa2cCUMtnx08-Fz6GM2mBjCcKyBCX3uZQpkecGb3BGuhe.YCwUh.AeL8Kfc1oHJzQAZYwB.Bg__%5EDhaDEahJca';
        //var_dump($url);
        import('ORG.Net.Http');
        Http::fsockopenDownload($url);
    }

    public function index(){
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
    }


    /**
     * 显示发票提取码,让客户输入发票提取码
     */
    public function verify(){
        //发票提取码
        $invoicenumber = I('get.n','','strip_tags');
        if(!empty($invoicenumber)){
            $this->assign('invoicenumber',$invoicenumber);
        }
        $this->display('verifyinvoice');
    }

    /**
     * 验证验证码和发票提取码
     */
    public function verifynumber(){
        $invoicenumber = I('get.invoicenumber','','strip_tags');
        $vCodeId = $_REQUEST['vCodeId'];

        //对验证码进行验证
        if(empty($vCodeId)) {
            $data['error'] = 1;
            $data['info'] = '验证码必须！';
            $this->ajaxReturn($data);
        }elseif (empty($invoicenumber)){
            $data['error'] = 1;
            $data['info'] = '发票提取码必须！';
            $this->ajaxReturn($data);
        }

        if($_SESSION['verify'] != md5($vCodeId)) {
            $data['error'] = 1;
            $data['info'] = '验证码错误！';
            //$this->ajaxReturn($data);
        }


        //对发票提取码进行验证
        $where = array();
        $where['eticketno'] = $invoicenumber;
        //删除sql欺骗
        $invoicewebModel = D('invoiceweb');
        $invoicewebResult = $invoicewebModel->where($where)->find();

        $data = array();
        if(empty($invoicewebResult)){
            //电子发票不存在,不能开具
            $data['error'] = 1;
            $data['info'] = '电子发票提取码不存在！';
            $this->ajaxReturn($data);
        }else{

            //发票是否开具
            if($invoicewebResult['state'] === 2){
                if(mb_strlen($invoicewebResult['header'],'utf-8') > 16 ){
                    $invoicewebResult['header'] =  mb_substr($invoicewebResult['header'],0,16,'utf-8') . '...';
                }else{
                    $invoicewebResult['header'] = $invoicewebResult['header'];
                }
                //查询电子票参数
                $invoiceeleparaModel = D('invoiceelepara');
                $where = array();
                $invoiceelectronResult = $invoiceeleparaModel->where($where)->find();
                if(empty($invoiceelectronResult)){
                    $data['error'] = 1;
                    $data['info'] = '电子发票参数不存在,不能开票！';
                    $this->ajaxReturn($data);
                    return false;
                }
                if(mb_strlen($invoiceelectronResult['xsf_dzdh'],'utf-8') > 16 ){
                    $invoicewebResult['xsf_dzdh'] =  mb_substr($invoiceelectronResult['xsf_dzdh'],0,16,'utf-8') . '...';
                }else{
                    $invoicewebResult['xsf_dzdh'] = $invoiceelectronResult['xsf_dzdh'];
                }
                if(mb_strlen($invoiceelectronResult['xsf_mc'],'utf-8') > 16){
                    $invoicewebResult['xsf_mc'] = $invoiceelectronResult['xsf_mc'];
                }else{
                    $invoicewebResult['xsf_mc'] = $invoiceelectronResult['xsf_mc'];
                }

                //重新标示id
                $invoicewebResult['invoiceid'] = $invoicewebResult['invoicewebid'];
                //计算金额(不含税)
                //不含税单价
                $xmdj =  ($invoicewebResult['money'] / (1+ $invoiceelectronResult['sl']));
                $xmdj = round($xmdj,2);
                $invoicewebResult['xmdj'] = $xmdj; //金额(不含税)
                //税额
                $suimoney =  $invoicewebResult['money']  - $xmdj;
                $invoicewebResult['suimoney'] = $suimoney;

                $invoicewebResult['url_pdf'] = $invoicewebResult['pdf_url'];
                //发票已经开具
                $this->assign('invoiceinfo',$invoicewebResult);
                //显示发票
                $data['open'] = $this->fetch('showinvoice');
                $data['error'] = 0;
                $data['info'] = '';
                $this->ajaxReturn($data);
            }
            if($invoicewebResult['state'] === 1){
                $this->assign('invoicenumber',$invoicenumber);
                $data['open'] = $this->fetch('statusvoice');
            }
        }

        $data['error'] = 0;
        $data['info'] = '';
        $this->ajaxReturn($data);
    }

    /**
     * 开具发票,将状态设置为1
     */
    public function openinvoice(){

        $invoicewebModel = D('invoiceweb');
        $where = array();
        $where['invoicewebid'] = $_REQUEST['invoiceid'];
        $data = array();
        $data['state'] = 1;
        $data['header'] = I('post.header','','strip_tags');
        if(!empty($_REQUEST['invtaxno'])){
            $data['gmf_nsrsbh'] = $_REQUEST['invtaxno'];
        }
        if(!empty($_REQUEST['invaddrphone'])){
            $data['gmf_dzdh'] = $_REQUEST['invaddrphone'];
        }
        if(!empty($_REQUEST['invbank'])){
            $data['gmf_yhzh'] = $_REQUEST['invbank'];
        }
        if(!empty($_REQUEST['sendmail'])){
            $data['email'] =  I('post.sendmail','','strip_tags');
        }
        if(!empty($_REQUEST['telphone'])){
            $data['telphone'] =  I('post.telphone','','strip_tags');
        }

        $invoicewebResult = $invoicewebModel->where($where)->save($data);

        $invoicewebResult = $invoicewebModel->field('eticketno')->where($where)->find();

        $data = array();
        if($invoicewebResult){
            $this->assign('invoicenumber',$invoicewebResult['eticketno']);
            $data['open'] = $this->fetch('statusvoice');
            $data['error'] = 0;
            $this->ajaxReturn($data);
        }else{
            $data['error'] = 1;
            $data['info'] = '';
            $this->ajaxReturn($data);
        }

    }


    /**
     * 验证码
     */
    public function verifyCode(){
        ob_clean();
        import('ORG.Util.Image');
        Image::buildImageVerify(4,1,'png',120,25);
    }
}