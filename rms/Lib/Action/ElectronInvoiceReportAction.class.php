<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/10/10
 * Time: 下午5:10
 * 电子发票统计模块
 */

class ElectronInvoiceReportAction extends ModuleAction
{
    // listview
    public function listview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = strtolower($focus->getPk());

            // 建立查询条件
            $where = array();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];

            //使用cookie读取rows
            $listMaxRows = $_COOKIE['listMaxRows'];
            if (!empty($listMaxRows)) {

            } else {
                $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            }

            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            //加入其它字段
            foreach ($focus->otherListFiels as $otherFields) {
                array_unshift($selectFields, $otherFields);
            }

            $listResult = $focus->where($where)->field($selectFields)
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->order("$moduleId  desc")->select();

            if (count($listResult) > 0) {
                $orderHandleArray = $listResult;
            } else {
                $orderHandleArray = array();
            }

            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $focus->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', array()),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'halign' => $value['halign'],
                    'width' => $value['width'],
                );
            }

            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            //所有分公司
            $where = array();
            $where['domain'] = $this->getDomain();
            $companymgrModel = D('companymgr');
            $companymgrResult = $companymgrModel->distinct(true)->field('name')->where($where)->select();
            $this->assign('companySelect', $companymgrResult);
            // 执行list的一些其它数据的操作
            $this->display('ElectronInvoiceReport' . '/listview'); // 执行方法自身的列表
        }
    }

    //返回发票情况
    public function getInvoiceReport()
    {
        $date = $_REQUEST['date'];
        $year = substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $companySelect = $_REQUEST['company'];

        //定义返回
        $returnInvoice = array();

        $invoicereportModel = D('invoicereport');
        $where = array();
        $where['year'] = $year;
        $where['month'] = $month;
        if ($companySelect == '全部') {

        } else {
            $where['company'] = $companySelect;
        }
        $invoicereportResult = $invoicereportModel->where($where)->select();
        //var_dump($invoicereportModel->getLastSql());
        //返回发票统计
        if (empty($invoicereportResult)) {
            $returnInvoice = array();
        } else {
            $returnInvoice = $invoicereportResult;
        }

        //计算汇总
        //$where = array();
        $field = array(
            "'汇总' as company",
            "'" . $year . "' as year",
            "'" . $month . "' as month",
            "sum(heji_number) as heji_number",
            "sum(heji_money) as heji_money",
            "sum(day1_number) as day1_number",
            "sum(day1_money) as day1_money",
            "sum(day2_number) as day2_number",
            "sum(day2_money) as day2_money",
            "sum(day3_number) as day3_number",
            "sum(day3_money) as day3_money",
            "sum(day4_number) as day4_number",
            "sum(day4_money) as day4_money",
            "sum(day5_number) as day5_number",
            "sum(day5_money) as day5_money",
            "sum(day6_number) as day6_number",
            "sum(day6_money) as day6_money",
            "sum(day7_number) as day7_number",
            "sum(day7_money) as day7_money",
            "sum(day8_number) as day8_number",
            "sum(day8_money) as day8_money",
            "sum(day9_number) as day9_number",
            "sum(day9_money) as day9_money",
            "sum(day10_number) as day10_number",
            "sum(day10_money) as day10_money",
            "sum(day11_number) as day11_number",
            "sum(day11_money) as day11_money",
            "sum(day12_number) as day12_number",
            "sum(day12_money) as day12_money",
            "sum(day13_number) as day13_number",
            "sum(day13_money) as day13_money",
            "sum(day14_number) as day14_number",
            "sum(day14_money) as day14_money",
            "sum(day15_number) as day15_number",
            "sum(day15_money) as day15_money",
            "sum(day16_number) as day16_number",
            "sum(day16_money) as day16_money",
            "sum(day17_number) as day17_number",
            "sum(day17_money) as day17_money",
            "sum(day18_number) as day18_number",
            "sum(day18_money) as day18_money",
            "sum(day19_number) as day19_number",
            "sum(day19_money) as day19_money",
            "sum(day20_number) as day20_number",
            "sum(day20_money) as day20_money",
            "sum(day21_number) as day21_number",
            "sum(day21_money) as day21_money",
            "sum(day22_number) as day22_number",
            "sum(day22_money) as day22_money",
            "sum(day23_number) as day23_number",
            "sum(day23_money) as day23_money",
            "sum(day24_number) as day24_number",
            "sum(day24_money) as day24_money",
            "sum(day25_number) as day25_number",
            "sum(day25_money) as day25_money",
            "sum(day26_number) as day26_number",
            "sum(day26_money) as day26_money",
            "sum(day27_number) as day27_number",
            "sum(day27_money) as day27_money",
            "sum(day28_number) as day28_number",
            "sum(day28_money) as day28_money",
            "sum(day29_number) as day29_number",
            "sum(day29_money) as day29_money",
            "sum(day30_number) as day30_number",
            "sum(day30_money) as day30_money",
            "sum(day31_number) as day31_number",
            "sum(day31_money) as day31_money",
        );

        $invoicereportResult = $invoicereportModel->field($field)->where($where)->select();
        //var_dump($invoicereportModel->getLastSql());

        //加入发票统计汇总、
        array_push($returnInvoice, $invoicereportResult[0]);

        $this->ajaxReturn($returnInvoice, 'JSON');

    }

    //导出发票统计
    public function exportExcel()
    {
        $date = $_REQUEST['date'];
        $year = substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $companySelect = $_REQUEST['company'];

        //计算汇总
        //$where = array();
        $field = array(
            "company",
            "'" . $year . "' as year",
            "'" . $month . "' as month",
            "heji_number",
            "heji_money",
            "day1_number",
            "day1_money",
            "day2_number",
            "day2_money",
            "day3_number",
            "day3_money",
            "day4_number",
            "day4_money",
            "day5_number",
            "day5_money",
            "day6_number",
            "day6_money",
            "day7_number",
            "day7_money",
            "day8_number",
            "day8_money",
            "day9_number",
            "day9_money",
            "day10_number",
            "day10_money",
            "day11_number",
            "day11_money",
            "day12_number",
            "day12_money",
            "day13_number",
            "day13_money",
            "day14_number",
            "day14_money",
            "day15_number",
            "day15_money",
            "day16_number",
            "day16_money",
            "day17_number",
            "day17_money",
            "day18_number",
            "day18_money",
            "day19_number",
            "day19_money",
            "day20_number",
            "day20_money",
            "day21_number",
            "day21_money",
            "day22_number",
            "day22_money",
            "day23_number",
            "day23_money",
            "day24_number",
            "day24_money",
            "day25_number",
            "day25_money",
            "day26_number",
            "day26_money",
            "day27_number",
            "day27_money",
            "day28_number",
            "day28_money",
            "day29_number",
            "day29_money",
            "day30_number",
            "day30_money",
            "day31_number",
            "day31_money",

        );

        //定义返回
        $returnInvoice = array();

        $invoicereportModel = D('invoicereport');
        $where = array();
        $where['year'] = $year;
        $where['month'] = $month;
        if ($companySelect == '全部') {

        } else {
            $where['company'] = $companySelect;
        }
        $invoicereportResult = $invoicereportModel->field($field)->where($where)->select();
        //var_dump($invoicereportResult);
        //var_dump($invoicereportModel->getLastSql());
        //返回发票统计
        if (empty($invoicereportResult)) {
            $returnInvoice = array();
        } else {
            $returnInvoice = $invoicereportResult;
        }

        //计算汇总
        //$where = array();
        $field = array(
            "'汇总' as  company",
            "'" . $year . "' as year",
            "'" . $month . "' as month",
            "sum(heji_number) as heji_number",
            "sum(heji_money) as heji_money",
            "sum(day1_number) as day1_number",
            "sum(day1_money) as day1_money",
            "sum(day2_number) as day2_number",
            "sum(day2_money) as day2_money",
            "sum(day3_number) as day3_number",
            "sum(day3_money) as day3_money",
            "sum(day4_number) as day4_number",
            "sum(day4_money) as day4_money",
            "sum(day5_number) as day5_number",
            "sum(day5_money) as day5_money",
            "sum(day6_number) as day6_number",
            "sum(day6_money) as day6_money",
            "sum(day7_number) as day7_number",
            "sum(day7_money) as day7_money",
            "sum(day8_number) as day8_number",
            "sum(day8_money) as day8_money",
            "sum(day9_number) as day9_number",
            "sum(day9_money) as day9_money",
            "sum(day10_number) as day10_number",
            "sum(day10_money) as day10_money",
            "sum(day11_number) as day11_number",
            "sum(day11_money) as day11_money",
            "sum(day12_number) as day12_number",
            "sum(day12_money) as day12_money",
            "sum(day13_number) as day13_number",
            "sum(day13_money) as day13_money",
            "sum(day14_number) as day14_number",
            "sum(day14_money) as day14_money",
            "sum(day15_number) as day15_number",
            "sum(day15_money) as day15_money",
            "sum(day16_number) as day16_number",
            "sum(day16_money) as day16_money",
            "sum(day17_number) as day17_number",
            "sum(day17_money) as day17_money",
            "sum(day18_number) as day18_number",
            "sum(day18_money) as day18_money",
            "sum(day19_number) as day19_number",
            "sum(day19_money) as day19_money",
            "sum(day20_number) as day20_number",
            "sum(day20_money) as day20_money",
            "sum(day21_number) as day21_number",
            "sum(day21_money) as day21_money",
            "sum(day22_number) as day22_number",
            "sum(day22_money) as day22_money",
            "sum(day23_number) as day23_number",
            "sum(day23_money) as day23_money",
            "sum(day24_number) as day24_number",
            "sum(day24_money) as day24_money",
            "sum(day25_number) as day25_number",
            "sum(day25_money) as day25_money",
            "sum(day26_number) as day26_number",
            "sum(day26_money) as day26_money",
            "sum(day27_number) as day27_number",
            "sum(day27_money) as day27_money",
            "sum(day28_number) as day28_number",
            "sum(day28_money) as day28_money",
            "sum(day29_number) as day29_number",
            "sum(day29_money) as day29_money",
            "sum(day30_number) as day30_number",
            "sum(day30_money) as day30_money",
            "sum(day31_number) as day31_number",
            "sum(day31_money) as day31_money",
        );
        $invoicereportResult = $invoicereportModel->field($field)->where($where)->select();
        //var_dump($invoicereportModel->getLastSql());

        //加入发票统计汇总、
        array_push($returnInvoice, $invoicereportResult[0]);

        //var_dump($returnInvoice);
        //return;

        $selectFields = array('序号', '分公司', '年', '月', '合计发票',
            '1号发票', '1号发票', '2号发票', '2号发票', '3号发票', '3号发票', '4号发票', '4号发票',
            '5号发票', '5号发票', '6号发票', '6号发票', '7号发票', '7号发票',
            '8号发票', '8号发票', '9号发票', '9号发票', '10号发票', '10号发票',
            '11号发票', '11号发票',
            '12号发票', '12号发票',
            '13号发票', '13号发票',
            '14号发票', '14号发票',
            '15号发票', '15号发票',
            '16号发票', '16号发票',
            '17号发票', '17号发票',
            '18号发票', '18号发票',
            '19号发票', '19号发票', '20号发票', '20号发票', '21号发票', '21号发票', '22号发票',
            '23号发票', '24号发票', '24号发票',
            '25号发票', '25号发票', '26号发票', '26号发票', '27号发票', '27号发票', '28号发票', '28号发票', '28号发票', '29号发票', '29号发票', '30号发票', '30号发票', '31号发票', '31号发票');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计电子发票统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }
        // 合并
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A1');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:B1');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:C1');
        $objPHPExcel->getActiveSheet()->mergeCells('D1:D1');
        $objPHPExcel->getActiveSheet()->mergeCells('E1:F1');
        $objPHPExcel->getActiveSheet()->mergeCells('G1:H1');
        $objPHPExcel->getActiveSheet()->mergeCells('I1:J1');
        $objPHPExcel->getActiveSheet()->mergeCells('K1:L1');
        $objPHPExcel->getActiveSheet()->mergeCells('M1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('O1:P1');
        $objPHPExcel->getActiveSheet()->mergeCells('Q1:R1');
        $objPHPExcel->getActiveSheet()->mergeCells('S1:T1');
        $objPHPExcel->getActiveSheet()->mergeCells('U1:V1');
        $objPHPExcel->getActiveSheet()->mergeCells('W1:X1');
        $objPHPExcel->getActiveSheet()->mergeCells('Y1:Z1');
        $objPHPExcel->getActiveSheet()->mergeCells('AA1:AB1');
        $objPHPExcel->getActiveSheet()->mergeCells('AC1:AD1');
        $objPHPExcel->getActiveSheet()->mergeCells('AE1:AF1');
        $objPHPExcel->getActiveSheet()->mergeCells('AG1:AH1');
        $objPHPExcel->getActiveSheet()->mergeCells('AI1:AJ1');
        $objPHPExcel->getActiveSheet()->mergeCells('AK1:AL1');
        $objPHPExcel->getActiveSheet()->mergeCells('AM1:AN1');
        $objPHPExcel->getActiveSheet()->mergeCells('AO1:AP1');
        $objPHPExcel->getActiveSheet()->mergeCells('AQ1:AR1');
        $objPHPExcel->getActiveSheet()->mergeCells('AS1:AT1');
        $objPHPExcel->getActiveSheet()->mergeCells('AU1:AV1');
        $objPHPExcel->getActiveSheet()->mergeCells('AW1:AX1');
        $objPHPExcel->getActiveSheet()->mergeCells('AY1:AZ1');
        $objPHPExcel->getActiveSheet()->mergeCells('AA1:AB1');
        $objPHPExcel->getActiveSheet()->mergeCells('BC1:BD1');
        $objPHPExcel->getActiveSheet()->mergeCells('BE1:BF1');
        $objPHPExcel->getActiveSheet()->mergeCells('BG1:BH1');
        $objPHPExcel->getActiveSheet()->mergeCells('BI1:BJ1');
        $objPHPExcel->getActiveSheet()->mergeCells('BK1:BL1');
        $objPHPExcel->getActiveSheet()->mergeCells('BM1:BN1');
        $objPHPExcel->getActiveSheet()->mergeCells('BO1:BP1');
        $objPHPExcel->getActiveSheet()->mergeCells('BQ1:BR1');
        $objPHPExcel->getActiveSheet()->mergeCells('BS1:BT1');
        $objPHPExcel->getActiveSheet()->mergeCells('BU1:BV1');
        $objPHPExcel->getActiveSheet()->mergeCells('BW1:BX1');
        $objPHPExcel->getActiveSheet()->mergeCells('BY1:BZ1');
        // 设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ100')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // 字体和样式
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(14); //字体大小
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ1')->getFont()->setSize(15); //第一行字体大小
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ1')->getFont()->setBold(true); //第一行是否加粗

        // 设置行高度
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20); //设置默认行高
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25); //第一行行高

        $i = 1;
        $l = 0;

        foreach ($returnInvoice as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i - 1);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('客户汇总');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '发票统计表-' . $date;

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
        exit();

    }

    //导出当月发票明细
    public function exportMingxiExcel()
    {
        $date = $_REQUEST['date'];
        $year = substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $companySelect = $_REQUEST['company'];

        //定义返回
        $returnInvoice = array();

        $invoicereportModel = D('invoiceweb');
        $where = array();
        $where['createdate'] = array('like', substr($date, 0, 7) . '%');
        if ($companySelect == '全部') {

        } else {
            $where['company'] = $companySelect;
        }
        $where['state'] = 2;
        $where['download_state'] = 2;
        $where['domain'] = $this->getDomain();

        $field = array(
            'header', 'body', 'money', 'gmf_nsrsbh', 'gmf_dzdh',
            'gmf_yhzh', 'ordersn', 'ordertxt', 'opendate', 'company',
        );
        $invoicereportResult = $invoicereportModel->field($field)->where($where)->order('company desc')->select();
        //var_dump($invoicereportModel->getLastSql());
        //var_dump($invoicereportResult);

        $selectFields = array(
            'row','header', 'body', 'money', 'gmf_nsrsbh', 'gmf_dzdh',
            'gmf_yhzh', 'ordersn', 'ordertxt', 'opendate', 'company',
        );

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计电子发票统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }
        // 合并

        // 设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ100')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // 字体和样式
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(14); //字体大小
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ1')->getFont()->setSize(15); //第一行字体大小
        $objPHPExcel->getActiveSheet()->getStyle('A1:BZ1')->getFont()->setBold(true); //第一行是否加粗

        // 设置行高度
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20); //设置默认行高
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25); //第一行行高

        $i = 1;
        $l = 0;

        foreach ($invoicereportResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i - 1);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('客户汇总');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '发票明细表-' . $date;

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
        exit();

    }
}
