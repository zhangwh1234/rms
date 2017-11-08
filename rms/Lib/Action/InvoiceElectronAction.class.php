<?php
/**
 * Created by zhangwh1234
 * User: apple
 * Date: 17/3/15
 * Time: 下午4:27
 * 电子发票管理工具
 */

class InvoiceElectronAction extends ModuleAction{

    /**
     * 列表
     */
    public function listview(){
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
            $moduleId = strtolower($focus->getPk());

            $invoicesearchdate = $_REQUEST['invoicesearchdate'];
            $invoicesearchheader = $_REQUEST['invoicesearchheader'];
            $invoicesearchcompany = $_REQUEST['invoicesearchcompany'];
            $invoicesearchstate = $_REQUEST['invoicesearchstate'];

            $where = array();
            if(!empty($invoicesearchdate)){
                $where ['createdate'] = array(
                    'like',
                    $invoicesearchdate . "%"
                );
            }
            if(!empty($invoicesearchheader)){
                $where ['header'] = array(
                    'like',
                    '%'.$invoicesearchheader . "%"
                );
            }

            if(!empty($invoicesearchcompany)){
                $where ['company'] = array(
                    'like',
                    '%'.$invoicesearchcompany . "%"
                );
            }
            if($invoicesearchstate == '全部'){

            }

            if($invoicesearchstate == '已开'){
               $where['state'] = 2;
            }
            if($invoicesearchstate == '未开'){
                $where['state'] = array('neq' , 2 );
            }

            //用户信息
            $userInfo = $_SESSION['userInfo'];
            //如果是超级管理员，显示系统管理导航
            if((C('RBAC_SUPERADMIN') == $userInfo['name'])){

            }else{
                // 分公司的名称
                $company = $userInfo ['department'];
                $where ['company'] = $company;
            }
            //1是电子票,2是普通发票
            //需要显示普通发票
            //$where['type'] = array('NEQ','1');
            //$where ['_string'] = "length(trim(company)) > 0";
            $where ['domain'] = $_SERVER ['HTTP_HOST'];


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

            //订单配送还要显示两个统计数据
            $listMaxRows = $listMaxRows -2;

            // 取得页数
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);


            // 查session取得page的firstRos和listRows
            if (isset ($_SESSION [$moduleName . 'firstRowlistview'])) {
                $Page->firstRow = $_SESSION [$moduleName . 'firstRowlistview'];
            }


            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

            // 查询模块的数据
            $selectFields = $listFields;
            array_unshift($selectFields, $moduleId);

            $listResult = $invoicewebModel->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();

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
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $companymgrResult = $companymgrModel->field('name')->where($where)->select();
            foreach($companymgrResult as $company){
                $companymgr_arr[] = $company['name'];
            }
            $this->assign('companyselect',$companymgr_arr);

            $this->display('InvoiceElectron/listview');
        }
    }

    /**
     * 参数设置
     */
    public function invoiceEleParaview(){
        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 模块的ID
        $moduleId = 'invoiceeleprarid';

        $where = array();
        $where['domain'] = $_SERVER['HTTP_HOST'];

        $invoiceeleparaModel = D('invoiceelepara');
        // 返回模块的行记录
        $result = $invoiceeleparaModel->where ( $where )->find ();

        $this->assign ( 'info', $result );
        $this->display('InvoiceElectron/parametartview');
    }

    /**
     * 参数编辑保存
     */
    public function paramaterEdit(){

        $invoiceeleparaModel = D('invoiceelepara');

        $where = array();
        $where['domain'] = $_SERVER['HTTP_HOST'];

        $data = array();
        $data['xsf_nsrsbh'] = $_REQUEST['xsf_nsrsbh'];
        $data['xsf_mc'] = $_REQUEST['xsf_mc'];
        $data['xsf_dzdh'] = $_REQUEST['xsf_dzdh'];
        $data['xsf_yhzh'] = $_REQUEST['xsf_yhzh'];
        $data['sl'] = $_REQUEST['sl'];
        $data['domain'] = $_SERVER['HTTP_HOST'];
        $data['invoiceelectron_open'] = $_REQUEST['invoiceelectron_open'];

        $invoiceeleparaResult = $invoiceeleparaModel->where($where)->find();
        if(empty($invoiceeleparaResult)){
            $invoiceeleparaModel->create();
            $invoiceeleparaModel->add($data);
        }else{
            $invoiceeleparaModel->where($where)->save($data);
        }

        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $this->ajaxReturn(json_encode($info),'EVAL');
    }

    /**
     * 导出发票表
     */
    public function outFapiao(){

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
        if(!empty($invoicesearchdate)){
            $where ['createdate'] = array(
                'like',
                $invoicesearchdate . "%"
            );
        }
        if(!empty($invoicesearchheader)){
            $where ['header'] = array(
                'like',
                '%'.$invoicesearchheader . "%"
            );
        }

        if(!empty($invoicesearchcompany)){
            $where ['company'] = array(
                'like',
                '%'.$invoicesearchcompany . "%"
            );
        }
        if($invoicesearchstate == '全部'){

        }

        if($invoicesearchstate == '已开'){
            $where['state'] = 2;
        }
        if($invoicesearchstate == '未开'){
            $where['state'] = array('neq' , 2 );
        }

        //用户信息
        $userInfo = $_SESSION['userInfo'];
        //如果是超级管理员，显示系统管理导航
        if((C('RBAC_SUPERADMIN') == $userInfo['name'])){

        }else{
            // 分公司的名称
            $company = $userInfo ['department'];
            $where ['company'] = $company;
        }
        //1是电子票,2是普通发票
        //需要显示普通发票
        //$where['type'] = array('NEQ','1');
        //$where ['_string'] = "length(trim(company)) > 0";
        $where ['domain'] = $_SERVER ['HTTP_HOST'];


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
        vendor ( 'PHPExcel.PHPExcel' );

        // 创建excel对象
        $objPHPExcel = new PHPExcel ();

        // 设置文档的属性
        $objPHPExcel->getProperties ()->setCreator ( "丽华快餐" )->setLastModifiedBy ( "丽华快餐集团" )->setTitle ( "统计文档" )->setSubject ( "订单管理系统统计" )->setDescription ( "统计订单系统用" )->setKeywords ( "统计 订单" )->setCategory ( "文件" );
        $i = 1;
        $listHeader = $listFields;
        foreach ( $listHeader as $key => $value ) {
            $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $i, 1, $value );
            $i ++;
        }

        $i = 1;
        $l = 0;
        foreach ( $listResult as $colKey => $colValue ) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex ( 0 )->setCellValueByColumnAndRow ( $l, $i, $colValue );
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet ()->setTitle ( '电子发票' );

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex ( 0 );

        $filename = '电子发票' . $invoicesearchdate . '-' . $invoicesearchcompany;

        // Redirect output to a client’s web browser (Excel5)
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' );
        header ( 'Cache-Control: max-age=0' );
        // If you're serving to IE 9, then the following may be needed
        header ( 'Cache-Control: max-age=1' );

        // If you're serving to IE over SSL, then the following may be needed
        header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
        header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
        header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
        header ( 'Pragma: public' ); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
        $objWriter->save ( 'php://output' );
        exit ();
    }
}