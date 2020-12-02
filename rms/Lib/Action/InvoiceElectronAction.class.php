<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 17/3/15
 * Time: 下午4:27
 * 电子发票管理工具
 */

class InvoiceElectronAction extends ModuleAction
{

    /**
     * 列表
     */
    public function listview()
    {

        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            $invoicewebModel = D('invoiceweb');

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = 'invoicewebid';

            $invoicesearchdate = $_REQUEST['invoicesearchdate'];
            $invoicesearchheader = $_REQUEST['invoicesearchheader'];
            $invoicesearchcompany = $_REQUEST['invoicesearchcompany'];
            $invoicesearchstate = $_REQUEST['invoicesearchstate'];

            $where = array();
            if (!empty($invoicesearchdate)) {
                $where ['createdate'] = array(
                    'like',
                    $invoicesearchdate . "%"
                );
            } else {
                $where ['createdate'] = array(
                    'like',
                    date('Y-m-d') . "%"
                );
            }
            if (!empty($invoicesearchheader)) {
                $where ['header'] = array(
                    'like',
                    '%' . $invoicesearchheader . "%"
                );

            }

            if (!empty($invoicesearchcompany)) {
                $where ['company'] = array(
                    'like',
                    '%' . $invoicesearchcompany . "%"
                );
            }
            if ($invoicesearchstate == '全部') {

            }

            if ($invoicesearchstate == '已开') {
                $where['state'] = 2;
            }

            if ($invoicesearchstate == '未开') {
                $where['state'] = array('neq', 2);
            }

            //用户信息
            $userInfo = $_SESSION['userInfo'];
            //如果是超级管理员，显示系统管理导航
            if ((C('RBAC_SUPERADMIN') == $userInfo['name'])) {

            }

            // 分公司的名称
            $company = $userInfo ['department'];
            if (!empty($company)) {
                switch ($company){
                    case '客服中心':
                        break;
                    default:
                        $where['company'] = $company;
                }
            }


            //1是电子票,2是普通发票
            //需要显示普通发票
            //$where['type'] = array('NEQ','1');
            //$where ['_string'] = "length(trim(company)) > 0";
            $where ['domain'] = $this->getDomain();


            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $invoicewebModel->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                $pageNumber = 1;
            }

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if(!empty($listMaxRows)){

            }else{
                $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
            }

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $pageNumber = $_REQUEST ['page'];
            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'listview' . 'page'] = $pageNumber;

            /**
            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowlistview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
            }
             * */


            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);

            $listResult = $invoicewebModel->field('invoicewebid,ordersn,ordertxt,eticketno,header,body,telphone,email,money,gmf_nsrsbh,gmf_dzdh,gmf_yhzh,KPR,SKR,FHR,createdate,opendate,state,download_state,cancel_state,cancel_download_state,fp_dm,fp_hm,sendemail,issms,pdf_url,company,domain')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('createdate desc')->select();

            //$orderHandleArray ['sql'] = $invoicewebModel->getLastSql();

            foreach ($listResult as $key => $value) {
                if ($value['cancel_state'] == 2) {
                    $listResult[$key]['state'] = 3;
                }
                if($company == '客服中心'){
                    $listResult[$key]['canceloper'] = 'IS';
                }
                if ((C('RBAC_SUPERADMIN') == $userInfo['name'])) {
                    $listResult[$key]['canceloper'] = 'IS';
                }
                $listResult[$key]['canceloper'] = 'IS';
            }


            $orderHandleArray ['total'] = $total;
            if (count($listResult) > 0) {
                $orderHandleArray ['rows'] = $listResult;
            } else {
                $orderHandleArray ['rows'] = array();
            }

            $this->ajaxReturn($orderHandleArray, 'JSON');
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 配送店（分公司）的信息
            $companymgrModel = D('companymgr');
            $where = array();
            $where['domain'] = $this->getDomain();
            $companymgrResult = $companymgrModel->field('name')->where($where)->select();
            foreach ($companymgrResult as $company) {
                $companymgr_arr[] = $company['name'];
            }
            $this->assign('cdate', date('Y-m-d'));
            $this->assign('companyselect', $companymgr_arr);
            //如果存在页数,获取
            if (isset($_REQUEST['pagetype'])) {
                $pageNumber = $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page'];
            } else {
                $pageNumber = 1;
            }

            $this->display('InvoiceElectron/listview');
        }
    }

    /**
     * 获取开票日志
     */
    public function getInvoiceWebLog()
    {
        $invoiceweblogModel = D('invoiceweblog');
        $where = array();
        $where['ordersn'] = $_REQUEST['ordersn'];
        $invoiceweblog = $invoiceweblogModel->where($where)->select();
        $this->ajaxReturn($invoiceweblog, 'JSON');
    }

    /**
     * 参数设置
     */
    public function invoiceEleParaview()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 模块的ID
        $moduleId = 'invoiceeleprarid';

        $where = array();
        $where['domain'] = $this->getDomain();

        $invoiceeleparaModel = D('invoiceelepara');
        // 返回模块的行记录
        $result = $invoiceeleparaModel->where($where)->find();

        $this->assign('info', $result);
        $this->display('InvoiceElectron/parametartview');
    }

    /**
     * 参数编辑保存
     */
    public function paramaterEdit()
    {

        $invoiceeleparaModel = D('invoiceelepara');

        $where = array();
        $where['domain'] = $this->getDomain();

        $data = array();
        $data['xsf_nsrsbh'] = $_REQUEST['xsf_nsrsbh'];
        $data['xsf_mc'] = $_REQUEST['xsf_mc'];
        $data['xsf_dzdh'] = $_REQUEST['xsf_dzdh'];
        $data['xsf_yhzh'] = $_REQUEST['xsf_yhzh'];
        $data['sl'] = $_REQUEST['sl'];
        $data['domain'] = $this->getDomain();
        $data['invoiceelectron_open'] = $_REQUEST['invoiceelectron_open'];

        $invoiceeleparaResult = $invoiceeleparaModel->where($where)->find();
        if (empty($invoiceeleparaResult)) {
            $invoiceeleparaModel->create();
            $invoiceeleparaModel->add($data);
        } else {
            $invoiceeleparaModel->where($where)->save($data);
        }

        $info['status'] = 1;
        $info['info'] = '保存成功';
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /**
     * 导出发票表
     */
    public function outFapiao()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        $invoicewebModel = D('invoiceweb');

        // 生成list字段列表
        $listFields = $focus->listFields;
        // 模块的ID
        $moduleId = strtolower($focus->getPk());

        $invoicesearchdate = $_REQUEST['invoicesearchdate'];
        $invoicesearchheader = $_REQUEST['invoicesearchheader'];
        $invoicesearchcompany = $_REQUEST['invoicesearchcompany'];
        $invoicesearchstate = $_REQUEST['invoicesearchstate'];

        $where = array();
        if (!empty($invoicesearchdate)) {
            $where ['createdate'] = array(
                'like',
                $invoicesearchdate . "%"
            );
        }
        if (!empty($invoicesearchheader)) {
            $where ['header'] = array(
                'like',
                '%' . $invoicesearchheader . "%"
            );
        }

        if (!empty($invoicesearchcompany)) {
            $where ['company'] = array(
                'like',
                '%' . $invoicesearchcompany . "%"
            );
        }
        if ($invoicesearchstate == '全部') {

        }

        if ($invoicesearchstate == '已开') {
            $where['state'] = 2;
        }
        if ($invoicesearchstate == '未开') {
            $where['state'] = array('neq', 2);
        }

        //用户信息
        $userInfo = $_SESSION['userInfo'];
        //如果是超级管理员，显示系统管理导航
        if ((C('RBAC_SUPERADMIN') == $userInfo['name'])) {

        } else {
            // 分公司的名称
            $company = $userInfo ['department'];
            $where ['company'] = $company;
        }
        //1是电子票,2是普通发票
        //需要显示普通发票
        //$where['type'] = array('NEQ','1');
        //$where ['_string'] = "length(trim(company)) > 0";
        $where ['domain'] = $this->getDomain();


        // 取得显示页数
        $pageNumber = $_REQUEST ['page'];
        if (empty ($pageNumber)) {
            $pageNumber = 1;
        }


        // 查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);

        $listResult = $invoicewebModel->where($where)->select();

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel ();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 1;
        $listHeader = $listFields;
        foreach ($listHeader as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, $value);
            $i++;
        }

        $i = 1;
        $l = 0;
        foreach ($listResult as $colKey => $colValue) {
            $l = $l + 1;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $colValue);
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('电子发票');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '电子发票' . $invoicesearchdate . '-' . $invoicesearchcompany;

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit ();
    }

    /**
     * 发送短信通知
     */
    public function sendSms()
    {

        //id号
        $invoicewebid = $_REQUEST['id'];

        $invoicewebModel = D('invoiceweb');

        //查询电子发票状态
        $where = array();
        $where['invoicewebid'] = $invoicewebid;
        $invoicewebResult = $invoicewebModel->where($where)->find();
        if (!empty($invoicewebResult)) {
            if ($invoicewebResult['state'] == 2) {  //发票已经开启了
                //重新发送短信通知
                $data = array();
                $data['ordersn'] = $invoicewebResult['ordersn'];
                $data['content'] = '您的电子票提取码：' . $invoicewebResult['eticketno'] . ' 请您到http://invoice.lihua.com/?n=' . $invoicewebResult['eticketno'] . ' 上下载发票。谢谢！';
                $data['createdate'] = date('Y-m-d H:i:s');
                $data['state'] = 1;  //避开，单独发送
                $data['telphone'] = $invoicewebResult['telphone'];
                $data['domain'] = $this->getDomain();
                $invoicesmsModel = D('invoicesms');
                $invoicesmsModel->create();
                $invoicesmsModel->add($data);
                //写入到日志中
                $data = array();
                $data['ordersn'] = $invoicewebResult['ordersn'];
                $data['log'] = '发送一次短信通知';
                $data['date'] = date('Y-m-d H:i:s');
                $data['domain'] = $this->getDomain();
                $invoiceweblogModel = D('invoiceweblog');
                $invoiceweblogModel->create();
                $invoiceweblogModel->add($data);
                $return = array();
                $return['success'] = 'ok';
                $this->ajaxReturn($return, 'JSON');
            } else {
                //不能发送短信通知
                $return = array();
                $return['success'] = 'err';
                $return['info'] = '发票还没有开出，不能发短信';
                $this->ajaxReturn($return, 'JSON');
            }
        } else {
            //不能发送短信通知
            //不能发送短信通知
            $return = array();
            $return['success'] = 'err';
            $return['info'] = '发票不存在，不能发短信';
            $this->ajaxReturn($return, 'JSON');
        }

    }

    /**
     * 发票退票
     */
    public function cancelInvoice(){
        //提取码
        $eticketno = $_REQUEST['id'];

        $invoicewebModel = D('invoiceweb');

        $where = array();
        $where['eticketno'] = $eticketno;

        $invoicewebResult = $invoicewebModel->where($where)->find();

        $data = array();
        $data['cancel_state'] = 1;
        $invoicewebModel->where($where)->save($data);

        //写入到日志中
        $data = array();
        $data['ordersn'] = $invoicewebResult['ordersn'];
        $data['log'] = '客服中心执行退票操作';
        $data['date'] = date('Y-m-d H:i:s');
        $data['domain'] = $this->getDomain();
        $invoiceweblogModel = D('invoiceweblog');
        $invoiceweblogModel->create();
        $invoiceweblogModel->add($data);

    }

    /**
     * 发票重新下载
     */
    public function doubleDownload(){
        //提取码
        $ordersn = $_REQUEST['id'];

        $invoicewebModel = D('invoiceweb');

        $where = array();
        $where['ordersn'] = $ordersn;

        $data = array();
        $data['download_state'] = 1;
        $invoicewebModel->where($where)->save($data);

        $invoicewebResult = $invoicewebModel->where($where)->find();

        //写入到日志中
        $data = array();
        $data['ordersn'] = $invoicewebResult['ordersn'];
        $data['log'] = '重新下载发票操作';
        $data['date'] = date('Y-m-d H:i:s');
        $data['domain'] = $this->getDomain();
        $invoiceweblogModel = D('invoiceweblog');
        $invoiceweblogModel->create();
        $invoiceweblogModel->add($data);
    }

    /**
     * 发票余量查询
     */
    public function fpyl(){
        $where = array();
        $where['domain'] = $this->getDomain();
        $invoiceeleparaModel = D('invoiceelepara');
        // 返回模块的行记录
        $result = $invoiceeleparaModel->where($where)->find();

        $return = array();
        $return['number'] = $result['fpyl'];
        $this->ajaxReturn($return, 'JSON');
    }
}