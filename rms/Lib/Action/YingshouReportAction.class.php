<?php
/**
 * Created by PhpStorm.
 * User: lihua
 * Date: 2018/8/13
 * Time: 下午5:41
 * 营收报表系统
 */

class YingshouReportAction extends YingshouAction
{
    public function index()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        $this->display('listview');
    }

    /**
     * 营收营收汇总表
     */
    public function getHuizongReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/huizonglist');
    }

    /**
     * 显示营收汇总的报表内容
     */
    public function showHuizongReport()
    {

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        $domain = $this->getDomain();

        //获取客户支付类型
        $paymentmgrModel = D('paymentmgr');
        $where = array();
        $where['company'] = array(
            array('EQ', $reportCompany),
            array('EQ', '总部'),
            'OR',
        );

        $paymentmgrResult = $paymentmgrModel->field('name,accounting')->where($where)->order('company,accounting,moneytype')->select();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        // 连接结账数据表
        $revparmgrModel = M('revparmgr_' . substr($reportStartDate, 5, 2), " ", $reveueConnectDb);

        $revparmgr = array();
        $l = 1;

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '销售总额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '营收');
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
        }

        $l += 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '促销额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '营收(减)');
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }

        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '营收情况', '', '',
        );

        $l = $l + 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['type'] = array('EQ', 'M1');

        $creditmoney = 0;
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->order('row')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '入账');
            $creditmoney += $value['money'];
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
            $l = $l + 1;
        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '贷方营收合计', $creditmoney, '',
        );
        $l = $l + 1;

        $where['type'] = array('EQ', 'M2');
        $debtormoney = 0;
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->order('row')->select();
        foreach ($paymentmgrResult as $payValue) {
            foreach ($revparmgrResult as $key => $value) {
                if (trim($value['name']) == $payValue['name']) {
                    $repparmgr[$l][] = $l;
                    array_unshift($value, $payValue['accounting']);
                    $debtormoney += $value['money'];
                    foreach ($value as $v) {
                        $revparmgr[$l][] = $v;
                    }
                    $l = $l + 1;
                    unset($revparmgrResult[$key]);
                    break;
                }
            }
        }
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '');
            $debtormoney += $value['money'];
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
            $l = $l + 1;
        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '借方营收合计', $debtormoney, '',
        );

        //var_dump($revparmgr);
        $this->assign('revparmgr', $revparmgr);
        $this->display('YingshouReport/huizongresult');
    }

    /**
     * 导出营收汇总的excel的内容
     */
    public function outputHuizongExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        $domain = $this->getDomain();

        //获取客户支付类型
        $paymentmgrModel = D('paymentmgr');
        $where = array();
        $where['company'] = array(
            array('EQ', $reportCompany),
            array('EQ', '总部'),
            'OR',
        );

        $paymentmgrResult = $paymentmgrModel->field('name,accounting,moneytype')->where($where)->order('company,accounting')->select();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        // 连接结账数据表
        $revparmgrModel = M('revparmgr_' . substr($reportStartDate, 5, 2), " ", $reveueConnectDb);

        $revparmgr = array();
        $l = 1;

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '销售总额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '营收');
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
        }

        $l += 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '促销额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '营收(减)');
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }

        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '营收情况', '', '',
        );

        $l = $l + 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['type'] = array('EQ', 'M1');

        $creditmoney = 0;
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->order('row')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '入账');
            $creditmoney += $value['money'];
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
            $l = $l + 1;
        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '贷方营收合计', $creditmoney, '',
        );
        $l = $l + 1;

        $where['type'] = array('EQ', 'M2');
        $debtormoney = 0;
        $qiandanmoney = 0; //欠单金额
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->order('row')->select();
        foreach ($paymentmgrResult as $payValue) {
            foreach ($revparmgrResult as $key => $value) {
                if (trim($value['name']) == $payValue['name']) {
                    $repparmgr[$l][] = $l;
                    array_unshift($value, $payValue['accounting']);
                    $debtormoney += $value['money'];
                    if ($payValue['moneytype'] == '欠单') {
                        $qiandanmoney += $value['money'];
                    }
                    foreach ($value as $v) {
                        $revparmgr[$l][] = $v;
                    }
                    $l = $l + 1;
                    unset($revparmgrResult[$key]);
                    break;
                }
            }
        }
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            array_unshift($value, '');
            $debtormoney += $value['money'];
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
            $l = $l + 1;
        }

        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '借方营收合计', $debtormoney, '',
        );
        $l = $l + 1;
        $revparmgr[$l] = array(
            '', '欠单金额', $qiandanmoney, '',
        );

        // $revparmgr = array();
        // $l = 1;

        // $where = array();
        // $where['date'] = array(
        //     array('EGT', $reportStartDate),
        //     array('ELT', $reportEndDate),
        //     'AND',
        // );
        // if ($reportAp == '全天') {

        // } else {
        //     $where['ap'] = $reportAp;
        // }
        // $where['domain'] = $domain;
        // $where['company'] = $reportCompany;
        // $where['name'] = '销售总额';
        // $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
        //     ->where($where)->group('name')->select();
        // foreach ($revparmgrResult as $key => $value) {
        //     $repparmgr[$l][] = $l;
        //     foreach ($value as $v) {
        //         $revparmgr[$l][] = $v;
        //     }
        // }

        // $l += 1;
        // $where = array();
        // $where['date'] = array(
        //     array('EGT', $reportStartDate),
        //     array('ELT', $reportEndDate),
        //     'AND',
        // );
        // if ($reportAp == '全天') {

        // } else {
        //     $where['ap'] = $reportAp;
        // }
        // $where['domain'] = $domain;
        // $where['company'] = $reportCompany;
        // $where['name'] = '促销额';
        // $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
        //     ->where($where)->group('name')->select();
        // foreach ($revparmgrResult as $key => $value) {
        //     $repparmgr[$l][] = $l;
        //     foreach ($value as $v) {
        //         $revparmgr[$l][] = $v;
        //     }

        // }

        // $l = $l + 1;
        // $where = array();
        // $where['date'] = array(
        //     array('EGT', $reportStartDate),
        //     array('ELT', $reportEndDate),
        //     'AND',
        // );
        // if ($reportAp == '全天') {

        // } else {
        //     $where['ap'] = $reportAp;
        // }
        // $where['domain'] = $domain;
        // $where['company'] = $reportCompany;
        // $where['type'] = array(
        //     array('EQ', 'M1'),
        //     array('EQ', 'M2'),
        //     'OR',
        // );

        // $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
        //     ->where($where)->group('name')->order('row')->select();
        // foreach ($revparmgrResult as $key => $value) {
        //     $repparmgr[$l][] = $l;
        //     foreach ($value as $v) {
        //         $revparmgr[$l][] = $v;
        //     }
        //     $l = $l + 1;
        // }

        $selectFields = array('row', 'accounting', 'name', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("营收系统")->setSubject("营收管理系统")->setDescription("统计订单系统用")->setKeywords("营收金额")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(16);

        $i = 1;
        $l = 0;

        foreach ($revparmgr as $tongjiKey => $tongjiValue) {

            if ($tongjiValue[1] == '营收情况') {
                $i = $i + 1;
                $l = 0;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $tongjiKey);

                // 设置边框
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'E' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':E' . $i);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->getStartColor()->setARGB('FF99CCFF');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $i, '营收情况');

            } elseif ($tongjiValue[1] == '贷方营收合计') {
                $i = $i + 1;
                $l = 0;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $tongjiKey);

                // 设置边框
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'E' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i);
                $objPHPExcel->getActiveSheet()->mergeCells('D' . $i . ':E' . $i);

                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->getStartColor()->setARGB('FF99CCFF');
                $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':E' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY); //水平方向上两端对齐
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $i, '贷方营收合计');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $i, $tongjiValue[2]);
            } elseif ($tongjiValue[1] == '借方营收合计') {
                $i = $i + 1;
                $l = 0;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $tongjiKey);

                // 设置边框
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'E' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i);
                $objPHPExcel->getActiveSheet()->mergeCells('D' . $i . ':E' . $i);

                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->getStartColor()->setARGB('FF99CCFF');
                $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':E' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY); //水平方向上两端对齐
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $i, '借方营收合计');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $i, $tongjiValue[2]);
            }elseif ($tongjiValue[1] == '欠单金额') {
                $i = $i + 1;
                $l = 0;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $tongjiKey);

                // 设置边框
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'E' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':C' . $i);
                $objPHPExcel->getActiveSheet()->mergeCells('D' . $i . ':E' . $i);

                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->getFill()->getStartColor()->setARGB('FF99CCFF');
                $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':E' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY); //水平方向上两端对齐
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $i, '欠单金额');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $i, $tongjiValue[2]);
            }
             else {
                $i = $i + 1;
                $l = 0;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $tongjiKey);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

                foreach ($tongjiValue as $colKey => $colValue) {
                    $l = $l + 1;
                    if ($l == 1) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, " " . $colValue);

                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $colValue);

                    }

                }
                // 设置边框
                $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'E' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('营收金额导出');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = $reportCompany . '营收汇总' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 打印营收汇总的报表内容
     */
    public function printHuizongReport()
    {

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        $domain = $this->getDomain();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        // 连接结账数据表
        $revparmgrModel = M('revparmgr_' . substr($reportStartDate, 5, 2), " ", $reveueConnectDb);

        $revparmgr = array();
        $l = 1;

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '销售总额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }
        }

        $l += 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['name'] = '促销额';
        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money ', 'company'))
            ->where($where)->group('name')->select();
        foreach ($revparmgrResult as $key => $value) {
            $repparmgr[$l][] = $l;
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
            }

        }

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['type'] = array(
            array('EQ', 'M1'),

            'OR',
        );

        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money '))
            ->where($where)->group('name')->select();

        $i = 0;
        $l = 0;
        $revparmgr = array();
        foreach ($revparmgrResult as $key => $value) {
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
                $i = $i + 1;
            }
            if ($i === 8) {
                $i = 0;
                $l = $l + 1;
            }
        }
        $this->assign('revparmgr_head', $revparmgr);

        $l = $l + 1;
        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['domain'] = $domain;
        $where['company'] = $reportCompany;
        $where['type'] = array(

            array('EQ', 'M2'),
            'OR',
        );

        $revparmgrResult = $revparmgrModel->field(array('name', ' sum(money) as money '))
            ->where($where)->group('name')->select();

        $i = 0;
        $l = 0;
        $revparmgr = array();
        foreach ($revparmgrResult as $key => $value) {
            foreach ($value as $v) {
                $revparmgr[$l][] = $v;
                $i = $i + 1;
            }
            if ($i === 8) {
                $i = 0;
                $l = $l + 1;
            }
        }

        // var_dump($revparmgrModel->getLastSql());
        //echo '<pre>';
        //var_dump($revparmgr);
        $this->assign('company', $reportCompany);
        $this->assign('printDate', $reportStartDate . '-' . $reportEndDate . $reportAp);
        $this->assign('revparmgr_body', $revparmgr);
        $this->display('YingshouReport/huizongprint');
    }

    /**
     * 客户往来汇总表
     */
    public function getAccountReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/accountlist');
    }

    /**
     * 客户往来汇总报表的内容
     */
    public function showAccountReport()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        //选择的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //获取类型
        $revparType = $this->getRevparType();
        //如果是分公司，就显示分公司，如果是总部，就显示全部
        if ($revparType == 'company') {
            $where = " where company= '$company' ";
        } else {
            if ($reportCompany == '总部') {

            } else {
                $where = " where company= '$reportCompany'";
            }
        }

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($currentDate);

        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
        }

        $accountsbillsmingxiModel->query('set @rownum=0');
        $accountResult = $accountsbillsmingxiModel->query("SELECT  @rownum:=@rownum+1 as rownum, name,sum(money) as money,company FROM  " . $accountsbillsmingxiDbname . $where . "  GROUP by name order by rownum");

        //汇总金额
        $accountTotal = $accountsbillsmingxiModel->query("SELECT   sum(money) as totalmoney  FROM " . $accountsbillsmingxiDbname . $where . "  ");

        $this->assign('account', $accountResult);

        //汇总
        $this->assign('totalnumber', count($accountResult));
        $this->assign('totalmoney', $accountTotal[0]['totalmoney']);
        $this->display('YingshouReport/accountresult');
    }

    /**
     * 导出客户汇总的excel的内容
     */
    public function outputAccountExcel()
    {
        $company = $_REQUEST['reportCompany'];

        //获取类型
        $revparType = $this->getRevparType();

        //如果是分公司，就显示分公司的客户汇总，如果不是，就显示全部
        if ($revparType == 'company') {
            $where = " where company= '$company'";
        }

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($currentDate);

        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;

        }

        $accountsbillsmingxiModel->query('set @rownum=0');
        $accountResult = $accountsbillsmingxiModel->query("SELECT  @rownum:=@rownum+1 as rownum, name,sum(money) as money,company FROM  " . $accountsbillsmingxiDbname . $where . " GROUP by name order by rownum");

        $selectFields = array('row', 'name', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 1;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($accountResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, ' ');
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('客户汇总');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '客户汇总表-' . $currentDate;

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

    /**
     * 客户往来明细表
     */
    public function getAccountMingxiReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //根据revparType来返回客户支付
        $where = array();
        if ($revparType == 'company') {
            $where[] = "  company='$company' or company='总部' ";
        }
        $where['domain'] = $this->getDomain();
        $paymentmgrModel = D('paymentmgr');
        $paymentmgrResult = $paymentmgrModel->distinct(true)->field('name')->where($where)->order('code asc')->select();
        foreach ($paymentmgrResult as $value) {
            $paymentSelect[] = trim($value['name']);
        }
        $this->assign('paymentSelect', $paymentSelect);

        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));

        $this->display('YingshouReport/accountmingxilist');
    }

    /**
     * 客户往来明细表的内容
     */
    public function showAccountMingxiReport()
    {

        // 分公司的名称
        $company = $this->userInfo['department'];

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //查询的客户
        $reportPayment = $_REQUEST['reportPayment'];

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }

        if ($reportCompany == '总部') {

        } else {
            $where['company'] = $reportCompany;
        }

        if (!empty($reportPayment)) {
            $where['name'] = $reportPayment;
        }

        $domain = $this->getDomain();

        $where['domain'] = $domain;

        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
        }

        $fields = array('code,name,money,date,type,company');

        $accountResult = $accountsbillsmingxiModel->field($fields)->where($where)->order('date desc')->select();

        $account = array();
        foreach ($accountResult as $key => $value) {
            //$account[$key]['No'] = $key;
            $account[$key]['code'] = $value['code'];
            $account[$key]['name'] = $value['name'];
            $account[$key]['money'] = $value['money'];
            $account[$key]['type'] = $value['type'];
            $account[$key]['date'] = $value['date'];
            $account[$key]['ap'] = $reportAp;
            $account[$key]['company'] = $reportCompany;
        }
        $this->assign('account', $account);

        //汇总金额
        $totalnumber = $accountsbillsmingxiModel->where($where)->count();
        $totalmoney = $accountsbillsmingxiModel->where($where)->sum('money');

        //汇总
        $this->assign('totalnumber', $totalnumber);
        $this->assign('totalmoney', $totalmoney);
        $this->display('YingshouReport/accountmingxiresult');
    }

    /**
     * 导出客户明细的excel的内容
     */
    public function outputAccountMingxiExcel()
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        //获取类型
        $revparType = $this->getRevparType();

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        //$reportCompany = $_REQUEST['reportCompany'];
        //解析出分公司
        $e = $_REQUEST['_URL_'][2];
        $c = explode('=', ($e));
        $reportCompany = $c[1];

        $where = array();
        $where['custdate'] = $revparDate;
        $where['ap'] = $revparAp;

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $domain = $this->getDomain();
        // 连接结账数据表
        $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;
        }

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }

        if ($company == $reportCompany) {
            $where['company'] = $reportCompany;
        } else {

        }

        $fields = array('code,name,money,date,company');

        $accountResult = $accountsbillsmingxiModel->field($fields)->where($where)->select();

        $account = array();
        foreach ($accountResult as $key => $value) {
            $account[$key]['no'] = $key;
            $account[$key]['code'] = $value['code'];
            $account[$key]['name'] = $value['name'];
            $account[$key]['money'] = $value['money'];
            $account[$key]['date'] = $value['date'];
            $account[$key]['ap'] = $reportAp;
            $account[$key]['company'] = $reportCompany;
        }

        $selectFields = array(
            'code', 'name', 'money', 'date', 'ap', 'company',
        );

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 1;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($account as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            //$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, '1');
            foreach ($tongjiValue as $colKey => $colValue) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
                $l = $l + 1;
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('客户明细');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '客户明细导出-' . $reportStartDate;

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

    /**
     * 产品汇总列表
     */
    public function getProductsReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));

        $this->display('YingshouReport/productslist');
    }

    /**
     * 产品汇总结果表
     */
    public function showProductsReport()
    {

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //获取类型
        $revparType = $this->getRevparType();
        $domain = $this->getDomain();

        if ($revparType == 'company') {
            $where = " and b.company='$reportCompany' ";
        }

        if ($reportAp == '全天') {
            $reportAp = $this->getDbAp();
        } else {
            $where .= " and b.ap='$reportAp' ";
        }
        $where .= " and b.domain = '$domain' ";

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);

            //
            $sql = " select a.code,a.name,sum(a.number),a.price,sum(a.money),b.company from rms_orderproducts_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  group by name";

            $sumSql = " select sum(a.number) as totalnumber, sum(a.money) as totalmoney from rms_orderproducts_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $sql = " select a.code,a.name,sum(a.number),a.price,sum(a.money),b.company from rms_orderproducts" .
                " as a join rms_orderform" . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where group by name ";
            //统计查询
            $sumSql = " select sum(a.number) as totalnumber, sum(a.money) as totalmoney from rms_orderproducts" .
                " as a join rms_orderform " . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  ";
        }

        //var_dump($sumSql);

        //不能跨月查询

        //进行统计
        $productsResult = $ordergoodsModel->query($sql);
        $productsTotal = $ordergoodsModel->query($sumSql);

        $this->assign('products', $productsResult);
        $this->assign('totalnumber', $productsTotal[0]['totalnumber']);
        $this->assign('totalmoney', $productsTotal[0]['totalmoney']);
        $this->display('YingshouReport/productsresult');

    }

    /**
     * 导出产品销售汇总的excel的内容
     */
    public function outputProductsExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //获取类型
        $revparType = $this->getRevparType();
        $domain = $this->getDomain();

        if ($revparType == 'company') {
            $where = " and b.company='$reportCompany' ";
        }

        if ($reportAp == '全天') {
            $reportAp = $this->getDbAp();
        } else {
            $where .= " and b.ap='$reportAp' ";
        }
        $where .= " and b.domain = '$domain' ";

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 配送店（分公司）的信息
        // 分公司的名称
        $company = $this->userInfo['department'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getDbAp();

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            $ordergoodsModel = M("orderproducts_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);

            //
            $sql = " select a.code,a.name,sum(a.number),a.price,sum(a.money),b.company from rms_orderproducts_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  group by name";

            $sumSql = " select sum(a.number) as totalnumber, sum(a.money) as totalmoney from rms_orderproducts_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
            $ordergoodsModel = D('orderproducts');
            $sql = " select a.code,a.name,a.number,a.price,a.money,b.company from rms_orderproducts" .
                " as a join rms_orderform" . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where group by name ";
            //统计查询
            $sumSql = " select sum(a.number) as totalnumber, sum(a.money) as totalmoney from rms_orderproducts" .
                " as a join rms_orderform " . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  ";
        }

        //不能跨月查询

        //进行统计
        $productsResult = $ordergoodsModel->query($sql);
        $productsTotal = $ordergoodsModel->query($sumSql);
        //表格头
        $selectFields = array('no', 'code', 'name', 'number', 'price', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($productsResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('产品汇总表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '产品销售汇总表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 送餐员汇总表
     */
    public function getSendnameReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));

        $this->display('YingshouReport/sendnamelist');
    }

    /**
     *  送餐员汇总结果表
     */
    public function showSendnameReport()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;
        $where['domain'] = $this->getDomain();

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        // 连接结账数据表
        $roomserviceModel = M('roomservice_' . substr($reportStartDate, 5, 2), " ", $reveueConnectDb);

        $fields = array('name,totalmoney,company');

        $roomserviceResult = $roomserviceModel->field($fields)->where($where)->group('name')->order('name')->select();
        //var_dump($roomserviceModel->getLastSql());

        $totalnumber = 0;
        $totalmoney = 0;
        //处理数据
        $roomservice = array();
        foreach ($roomserviceResult as $key => $value) {
            $sendname = $value['name'];
            $where = array();
            $where['sendname'] = $sendname;
            $where['custdate'] = array(
                array('EGT', $reportStartDate),
                array('ELT', $reportEndDate),
                'AND',
            );
            $totalOrder = $orderformModel->where($where)->count();
            $roomservice[$key]['row'] = $key;
            $roomservice[$key]['name'] = $sendname;
            $roomservice[$key]['totalorder'] = $totalOrder;
            $roomservice[$key]['totalmoney'] = $value['totalmoney'];
            $roomservice[$key]['company'] = $value['company'];
            //汇总
            $totalnumber += 1;
            $totalmoney += $value['totalmoney'];

        }

        $this->assign('roomservice', $roomservice);
        $this->assign('totalnumber', $totalnumber);
        $this->assign('totalmoney', $totalmoney);

        $this->display('YingshouReport/sendnameresult');

    }

    /**
     * 导出送餐员汇总表excel
     */
    public function outputSendnameExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;
        $where['domain'] = $this->getDomain();

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }

        // 连接结账数据表
        $roomserviceModel = M('roomservice_' . substr($reportStartDate, 5, 2), " ", $reveueConnectDb);

        $fields = array('name,totalmoney,company');

        $roomserviceResult = $roomserviceModel->field($fields)->where($where)->group('name')->order('name')->select();
        //var_dump($roomserviceModel->getLastSql());
        //return;

        $totalnumber = 0;
        $totalmoney = 0;
        //处理数据
        $roomservice = array();
        foreach ($roomserviceResult as $key => $value) {
            $sendname = $value['name'];
            $where = array();
            $where['sendname'] = $sendname;
            $where['custdate'] = array(
                array('EGT', $reportStartDate),
                array('ELT', $reportEndDate),
                'AND',
            );
            $totalOrder = $orderformModel->where($where)->count();
            $roomservice[$key]['row'] = $key;
            $roomservice[$key]['name'] = $sendname;
            $roomservice[$key]['totalorder'] = $totalOrder;
            $roomservice[$key]['totalmoney'] = $value['totalmoney'];
            $roomservice[$key]['company'] = $value['company'];
            //汇总
            $totalnumber += 1;
            $totalmoney += $value['totalmoney'];

        }

        //表格头
        $selectFields = array('no', 'name', 'number', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($roomservice as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('产品汇总表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '产品销售汇总表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 送餐员明细表
     */
    public function getSendnameMingxiReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/sendnamemingxilist');
    }

    /**
     * 送餐员明细结果表
     */
    public function showSendnameMingxiReport()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $fields = array('sendname,address,ordertxt,totalmoney', 'company');
        $orderformResult = $orderformModel->field($fields)->where($where)->order('sendname')->select();
        //var_dump($orderformModel->getLastSql());

        $this->assign('orderform', $orderformResult);
        $this->display('YingshouReport/sendnamemingxiresult');
    }
    /**
     * 导出excel 报表
     */
    public function outputSendnameMingxiExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        $where = array();
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $fields = array('sendname,address,ordertxt,totalmoney', 'company');
        $orderformResult = $orderformModel->field($fields)->where($where)->order('sendname')->select();

        //表格头
        $selectFields = array('no', 'name', 'address', 'ordertxt', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($orderformResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('送餐员明细表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '送餐员明细表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 赠卡明细表
     */
    public function getFreebieReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/freebielist');
    }

    /**
     * 赠卡明细结果表
     */
    public function showFreebieReport()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //
        $revparType = $this->getRevparType();
        //
        $domain = $this->getDomain();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';

        if ($reportAp == '全天') {

        } else {
            $where = " ap='$reportAp' ";
        }
        if ($revparType == 'company') {
            $where = $where . " and b.company='$company' ";
        }
        if ($revparType == 'finance') {
            if ($reportCompany == '总部') {

            } else {
                $where = $where . " and b.company='$reportCompany' ";
            }
        }
        $where = $where . " and b.domain='$domain' ";

        $where = $where . " and a.name='赠卡' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.ordersn,a.name,a.money,b.address,b.ordertxt,b.origin,b.company from rms_orderfinance_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderpaymentResult = $orderformModel->query($sql);
        //var_dump($orderpaymentResult);
        //var_dump($orderformModel->getLastSql());

        $this->assign('orderpayment', $orderpaymentResult);
        $this->display('YingshouReport/freebieresult');
    }

    /**
     * 导出赠卡明细表中的excel数据
     */
    public function outputFreebieExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;
        $where = " and name='赠卡' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.name,b.address,b.ordertxt,a.money,b.company from rms_orderpayment_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderpaymentResult = $orderformModel->query($sql);

        //表格头
        $selectFields = array('no', 'name', 'address', 'ordertxt', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($orderpaymentResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('赠卡明细表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '赠卡明细表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 餐券明细表
     */
    public function getMealticketReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/mealticketlist');
    }

    /**
     * 餐券明细结果表
     */
    public function showMealticketReport()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //
        $revparType = $this->getRevparType();
        //
        $domain = $this->getDomain();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';

        if ($reportAp == '全天') {

        } else {
            $where = " ap='$reportAp' ";
        }
        if ($revparType == 'company') {
            $where = $where . " and b.company='$company' ";
        }
        if ($revparType == 'finance') {
            if ($reportCompany == '总部') {

            } else {
                $where = $where . " and b.company='$reportCompany' ";
            }
        }
        $where = $where . " and b.domain='$domain' ";

        $where = $where . " and a.name like '%券%' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.ordersn,a.name,a.money,b.address,b.ordertxt,b.origin,b.company from rms_orderfinance_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderpaymentResult = $orderformModel->query($sql);

        //var_dump($orderpaymentResult);
        //var_dump($orderformModel->getLastSql());

        $this->assign('orderpayment', $orderpaymentResult);

        $this->display('YingshouReport/mealticketresult');
    }

    /**
     * 导出餐券明细表
     */
    public function outputMealticketExcel()
    {

        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';
        $where['date'] = array(
            array('EGT', $reportStartDate),
            array('ELT', $reportEndDate),
            'AND',
        );
        if ($reportAp == '全天') {

        } else {
            $where['ap'] = $reportAp;
        }
        $where['company'] = $reportCompany;
        $where = " and a.name='餐券' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($roomDate);
        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.name,b.address,b.ordertxt,a.money,b.company from rms_orderpayment_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where ";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderpaymentResult = $orderformModel->query($sql);
        //var_dump($orderpaymentResult);
        //var_dump($orderformModel->getLastSql());

        //表格头
        $selectFields = array('no', 'name', 'address', 'ordertxt', 'money', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($orderpaymentResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('餐券明细表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '餐券明细表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 活动明细表
     */
    public function getActivityReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }
        //显示当前日期
        $this->assign('reportStartValue', date('Y-m-d'));
        $this->assign('reportEndValue', date('Y-m-d'));
        $this->display('YingshouReport/activitylist');
    }

    /**
     * 活动明细结果表
     */
    public function showActivityReport()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //
        $revparType = $this->getRevparType();
        //
        $domain = $this->getDomain();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';

        if ($reportAp == '全天') {

        } else {
            $where = " ap='$reportAp' ";
        }
        if ($revparType == 'company') {
            $where = $where . " and company='$company' ";
        }
        if ($revparType == 'finance') {
            if ($reportCompany == '总部') {

            } else {
                $where = $where . " and company='$reportCompany' ";
            }
        }
        $where = $where . " and domain='$domain' ";
        //$where['company'] = $reportCompany;
        //$where = " and a.name='餐券' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);

        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.ordersn,a.name,a.money,b.address,b.ordertxt,b.origin,b.company from rms_orderactivity_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  order by a.name";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderactivityResult = $orderformModel->query($sql);
        //var_dump($orderactivityResult);
        //var_dump($orderformModel->getLastSql());

        $this->assign('orderactivity', $orderactivityResult);

        $this->display('YingshouReport/activityresult');

    }

    /**
     * 导出餐券excel
     */
    public function outputActivityExcel()
    {
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];
        //结账午别
        $reportAp = $_REQUEST['reportAp'];
        //查询的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //
        $revparType = $this->getRevparType();
        //
        $domain = $this->getDomain();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);

        $where = '';

        if ($reportAp == '全天') {

        } else {
            $where = " ap='$reportAp' ";
        }
        if ($revparType == 'company') {
            $where = $where . " and company='$company' ";
        }
        if ($revparType == 'finance') {
            if ($reportCompany == '总部') {

            } else {
                $where = $where . " and company='$reportCompany' ";
            }
        }
        $where = $where . " and domain='$domain' ";
        //$where['company'] = $reportCompany;
        //$where = " and a.name='餐券' ";

        $rmsConnectDb = $this->connectHistoryRmsDb($reportStartDate);

        //根据日期和午别来选择不同的数据库，如果是当前日期和午别，就选择当前数据库
        //如果不是，就要选择备份库
        if (($currentDate != $reportStartDate) || ($currentAp != $reportAp)) {
            $orderformModel = M("orderform_" . substr($reportStartDate, 5, 2), "rms_", $rmsConnectDb);
            //
            $sql = " select a.ordersn,a.name,a.money,b.address,b.ordertxt,b.origin,b.company from rms_orderactivity_" . substr($reportStartDate, 5, 2) .
            " as a join rms_orderform_" . substr($reportStartDate, 5, 2) . " as b on a.ordersn = b.ordersn " .
                " where b.custdate >= '$reportStartDate' and b.custdate <= '$reportEndDate'  $where  order by a.name";

        } else {
            //连接当前库和表
            $orderformModel = D('orderform');
        }
        $orderactivityResult = $orderformModel->query($sql);

        //表格头
        $selectFields = array('no', 'ordersn', 'name', 'money', 'address', 'ordertxt', 'origin', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($orderactivityResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('活动明细表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '活动明细表' . $reportStartDate . '-' . $reportEndDate;

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

    /**
     * 客户出入汇总表view
     */
    public function getAccountInoutReport()
    {
        //获取类型
        $revparType = $this->getRevparType();

        // 分公司的名称
        $company = $this->userInfo['department'];
        $companySelect = array();
        //所有分公司
        $where = array();
        $where['domain'] = $this->getDomain();
        $companymgrModel = D('companymgr');
        $companymgrResult = $companymgrModel->field('name')->where($where)->select();
        $companySelect[] = '总部';
        foreach ($companymgrResult as $value) {
            $companySelect[] = $value['name'];
        }
        //非分公司可以看到全部分公司
        $this->assign('companySelect', $companySelect);
        if ($revparType == 'company') {
            $companySelect = array();
            $companySelect[] = $company;
            $this->assign('companySelect', $companySelect);
        }

        $this->display('YingshouReport/accountinoutlist');

    }

    /**
     * 客户出入汇总表的数据
     */
    public function showAccountInoutReport()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        //选择的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);
        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
        }
        $accountinoutModel = M('accountinout', " ", $reveueConnectDb);

        $where = array();
        $where['domain'] = $this->getDomain();
        $accountinoutModel->where($where)->delete();

        $where = '';
        //获取类型
        $revparType = $this->getRevparType();
        //如果是分公司，就显示分公司，如果是总部，就显示全部
        if ($revparType == 'company') {
            $whereFirst = " where company= '$company' and ";
        } else {
            if ($reportCompany == '总部') {
                $whereFirst = ' where  ';
            } else {
                $whereFirst = " where company= '$reportCompany' and ";
            }
        }

        //本期余额
        //加入日期
        $where = '';
        $where .= $whereFirst;
        $where .= "   date <= '" . $reportEndDate . "' ";
        $where .= " and domain='" . $domain . "' ";
        $accountResult = $accountsbillsmingxiModel->query("SELECT  code,name,sum(money) as money,company FROM  " . $accountsbillsmingxiDbname . $where . "  GROUP by name ,company");
        //var_dump($accountsbillsmingxiModel->getLastSql());
        //var_dump(count($accountResult));
        foreach ($accountResult as $key => $value) {
            $where = array();
            $where['name'] = $value['name'];

            $data = array();
            $data['code'] = empty($value['code']) ? '' : $value['code'];
            $data['name'] = $value['name'];
            $data['currentmoney'] = $value['money'];
            $data['company'] = $value['company'];
            $data['domain'] = $this->getDomain();
            $accountinoutModel->create();
            $accountinoutModel->add($data);
        }

        //上期余额
        //加入日期
        $where = '';
        $where .= $whereFirst;
        $where .= "   date < '" . $reportStartDate . "' ";
        //$accountsbillsmingxiModel->query('set @rownum=0');
        $where .= " and domain='" . $domain . "' ";
        $accountResult = $accountsbillsmingxiModel->query("SELECT   name,sum(money) as money,company FROM " . $accountsbillsmingxiDbname . $where . "  GROUP by name,company ");
        //var_dump($accountsbillsmingxiModel->getLastSql());
        foreach ($accountResult as $key => $value) {
            $where = array();
            $where['name'] = $value['name'];
            $where['company'] = $value['company'];
            $where['domain'] = $domain;
            $data = array();
            $data['code'] = empty($value['code']) ? '' : $value['code'];
            $data['name'] = $value['name'];
            $data['periodmoney'] = $value['money'];
            $data['company'] = $value['company'];
            $data['domain'] = $this->getDomain();
            $accountinoutModel->where($where)->save($data);
        }

        //本期欠额
        $where = '';
        $where .= $whereFirst;
        $where .= "   date >= '" . $reportStartDate . "' and date <= '" . $reportEndDate . "' and money > 0 ";
        $where .= " and domain='" . $domain . "' ";
        $accountResult = $accountsbillsmingxiModel->query("SELECT   name,sum(money) as money,company FROM " . $accountsbillsmingxiDbname . $where . "  GROUP by name ,company ");
        //var_dump($accountsbillsmingxiModel->getLastSql());
        foreach ($accountResult as $key => $value) {
            $where = array();
            $where['name'] = $value['name'];
            $where['company'] = $value['company'];
            $where['domain'] = $domain;

            $data = array();
            $data['code'] = empty($value['code']) ? '' : $value['code'];
            $data['name'] = $value['name'];
            $data['outmoney'] = $value['money'];
            $data['domain'] = $this->getDomain();
            $accountinoutModel->where($where)->save($data);
        }

        //本期还额
        $where = '';
        $where .= $whereFirst;
        $where .= "   date >= '" . $reportStartDate . "' and date <= '" . $reportEndDate . "' and money < 0 and (type='还欠' or type='调整单' or type='预收' or type= '退预付款') ";
        $where .= " and domain='" . $domain . "' ";
        $accountResult = $accountsbillsmingxiModel->query("SELECT  name,sum(money) as money,company FROM " . $accountsbillsmingxiDbname . $where . "  GROUP by name,company order by name");
        //var_dump($accountsbillsmingxiModel->getLastSql());
        foreach ($accountResult as $key => $value) {
            $where = array();
            $where['name'] = $value['name'];
            $where['company'] = $value['company'];
            $where['domain'] = $domain;

            $data = array();
            $data['name'] = $value['name'];
            $data['inmoney'] = $value['money'];
            $data['domain'] = $this->getDomain();
            $accountinoutModel->where($where)->save($data);
        }

        $where = array();
        $where['domain'] = $domain;
        $accountinoutResult = $accountinoutModel->field('name,periodmoney,outmoney,inmoney,currentmoney,company')->where($where)->order(" company,name desc")->select();
        $this->assign('account', $accountinoutResult);

        $this->display('YingshouReport/accountinoutresult');

    }

    /**
     * 客户出入汇总表导出excel
     */
    public function outputAccountInoutExcel()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        //选择的分公司
        $reportCompany = $_REQUEST['reportCompany'];
        //查询开始日期
        $reportStartDate = $_REQUEST['reportStartDate'];
        //查询结束日期
        $reportEndDate = $_REQUEST['reportEndDate'];

        //当前日期和当前午别
        $currentDate = date('Y-m-d');
        $currentAp = $this->getAp();

        //链接结账库
        $reveueConnectDb = $this->connectReveueDb($reportStartDate);
        $domain = $this->getDomain();
        // 连接结账数据表
        switch ($domain) {
            case 'bj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_bj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_bj';
                break;
            case 'nj.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_nj', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_nj';
                break;
            case 'cz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
            case 'wx.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_wx', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_wx';
                break;
            case 'sz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sz';
                break;
            case 'sh.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_sh', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_sh';
                break;
            case 'gz.lihuaerp.com':
                $accountsbillsmingxiModel = M('accountsbillsmingxi_gz', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi_gz';
                break;
            default:
                $accountsbillsmingxiModel = M('accountsbillsmingxi', " ", $reveueConnectDb);
                $accountsbillsmingxiDbname = 'accountsbillsmingxi';
                break;
        }
        $accountinoutModel = M('accountinout', " ", $reveueConnectDb);

        $where = array();
        $where['domain'] = $domain;
        $accountinoutResult = $accountinoutModel->field('name,periodmoney,outmoney,inmoney,currentmoney,company')->where($where)->order(" company,name desc")->select();

        //表格头
        $selectFields = array('序号', 'name', 'periodmoney', 'outmoney', 'inmoney', 'currentmoney', 'company');

        // 引入类
        vendor('PHPExcel.PHPExcel');

        // 创建excel对象
        $objPHPExcel = new PHPExcel();

        // 设置文档的属性
        $objPHPExcel->getProperties()->setCreator("丽华快餐")->setLastModifiedBy("丽华快餐集团")->setTitle("统计文档")->setSubject("订单管理系统统计")->setDescription("统计订单系统用")->setKeywords("统计 订单")->setCategory("文件");
        $i = 0;
        foreach ($selectFields as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i, 1, L($value));
            $i++;
        }

        $i = 1;
        $l = 0;

        foreach ($accountinoutResult as $tongjiKey => $tongjiValue) {
            $i = $i + 1;
            $l = 0;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, $i - 1);
            foreach ($tongjiValue as $colKey => $colValue) {
                $l = $l + 1;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($l, $i, L($colValue));
            }

        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('客户出入汇总表');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $filename = '客户出入汇总表' . $reportStartDate . '-' . $reportEndDate;

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

    //弹出客户支付选择窗口
    public function popupAccountsview()
    {
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'Accounts';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            // 模块的ID
            $moduleId = $popupModule->getPk();
            // 加入模块id到listHeader中
            // array_unshift($listFields,$moduleNameId);
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset($listMaxRows)) {
                $listMaxRows = 15;
            }
            // 取得页数
            $_GET['p'] = $pageNumber;
            $Page = new Page($total, $listMaxRows);

            //保存页数
            $_SESSION[$moduleName . 'page'] = $pageNumber;

            // 查询模块的数据
            // 查询模块的数据
            foreach ($listFields as $key => $value) {
                $selectFields[] = $key;
            }
            array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("accountsid desc")->select();

            $orderHandleArray['total'] = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult);

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'YingshouAccounts';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupAccountsFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popupAccountsview'),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true,
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'PopupAccountsviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouWorklunch/popupAccountsview');
        }
    }

    /* 弹出客户支付选择窗口 */
    public function popupPaymentMgrview()
    {
        $company = $_REQUEST['company'];
        if (empty($company)) {
            $company = '';
        }
        $this->assign('company', $company);
        $this->display('YingshouReport/selectpaymentmgrview');

        return;
        if (IS_POST) {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称
            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'paymentmgr';

            $this->assign('moduleName', $popupModuleName); // 模块名称

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];

            // 生成list字段列表
            $listFields = $focus->popupPaymentMgrFields;

            // 模块的ID
            $moduleId = 'paymentmgrid';

            // 加入模块id到listHeader中
            $listHeader = $listFields;
            $this->assign("listHeader", $listHeader); // 列表头
            $this->assign('returnAction', 'listview'); // 定义返回的方法

            $where = array();
            //$where['company'] = $company;
            //$where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数
            // 查session取得page的firstRos和listRows

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                $pageNumber = 1;
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                }
            }

            $listMaxRows = C('LIST_MAX_ROWS'); // 定义显示的列表函数
            if (isset($listMaxRows)) {
                $listMaxRows = 15;
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
            //array_unshift($selectFields, $moduleId);

            $listResult = $popupModule->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

            $total = count($listResult);
            if (count($listResult) > 0) {
                $orderHandleArray['rows'] = $listResult;
            } else {
                $orderHandleArray['rows'] = array();
            }
            $data = array('total' => $total, 'rows' => $listResult, 'sql' => $popupModule->getLastSql());

            $this->ajaxReturn($data);

        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            // 启动当前模块
            $focus = D($moduleName);

            // 取得模块的名称
            $popupModuleName = 'paymentmgr';

            // 启动弹出选择的模块
            $popupModule = D($popupModuleName);

            // 模块的ID
            $moduleId = $popupModule->getPk();

            // 生成list字段列表
            $listFields = $focus->popupPaymentMgrFields;

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/popuppaymentmgrview'),
                    'pageNumber' => 1,
                    'pageSize' => 10,
                ),
            );

            $datagrid['fields'][$moduleId] = array(
                'field' => 'ck',
                'checkbox' => true,
            );

            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid['fields'][$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'width' => $value['width'],
                );
            }

            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => 'YingshouRoomServicePopupPaymentMgrviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('returnModule', $_REQUEST['returnModule']);
            // 取得父窗口的表格行数
            $row = $_REQUEST['row'];
            $this->assign('row', $row); //返回点击的订购商品行

            $this->display('YingshouRoomService/popuppaymentmgrview');
        }
    }

    /**
     * 获取操作员的权限
     */
    public function getRevparType()
    {
        //定义是哪个结账
        $userid = $_SESSION['userid'];
        //查询角色ID
        $roleuserModel = D('role_user');
        $roleuserResult = $roleuserModel->where("user_id=$userid")->find();
        $roleid = $roleuserResult['role_id'];

        //查询角色的功能
        $accessModel = D('access');
        $where = array();
        $where['role_id'] = $roleid;
        $accessResult = $accessModel->field('node_id')->where($where)->select();
        foreach ($accessResult as $value) {
            $accessArr[] = $value['node_id'];
        }
        //节点表
        $nodeModel = D('node');
        //查询分公司结账节点
        $nodeidResult = $nodeModel->where("name='companyRevpar'")->find();
        $nodeidCompanyRevpar = $nodeidResult['id'];
        if (in_array($nodeidCompanyRevpar, $accessArr)) {
            $revparType = 'company';
        }

        //查询财务结账节点
        $nodeidResult = $nodeModel->where("name='financeRevpar'")->find();
        $nodeidFinanceRevpar = $nodeidResult['id'];
        if (in_array($nodeidFinanceRevpar, $accessArr)) {
            $revparType = 'finance';
        }

        return $revparType;
    }

    /**
     * 获取分公司支付方式
     */
    public function getCompanyPayment()
    {
        // 分公司的名称
        $company = $this->userInfo['department'];
        $getCompany = $_REQUEST['company'];

        $revparType = $this->getRevparType();

        $paymentmgrModel = D('paymentmgr');
        $where = array();
        if ($revparType == 'finance') {

            if (!empty($getCompany)) {
                if ($getCompany == '总部') {
                    $where['company'] = '总部';
                } else {
                    $where['company'] = $getCompany;
                }
            }

        } else {
            if (!empty($company)) {
                $where['company'] = array(
                    array('EQ', '$company'),
                    array('EQ', '总部'),
                    'or',
                );
            }

        }
        $where['domain'] = $this->getDomain();
        $paymentmgr = $paymentmgrModel->where($where)->select();

        import('@.Extend.ChinesePinyin');
        $Pinyin = new ChinesePinyin();

        $companyArr = array();
        foreach ($paymentmgr as $value) {
            //$py = $this->getFirstCharter(trim($value['name']));
            $py = $Pinyin->TransformUcwords(trim($value['name']));
            $py = $py[0];
            if ($py == 'A') {
                $A[] = trim($value['name']);
            }

            if ($py == 'B') {
                $B[] = trim($value['name']);
            }

            if ($py == 'C') {
                $C[] = trim($value['name']);
            }

            if ($py == 'D') {
                $D[] = trim($value['name']);
            }

            if ($py == 'E') {
                $E[] = trim($value['name']);
            }

            if ($py == 'F') {
                $F[] = trim($value['name']);
            }

            if ($py == 'G') {
                $G[] = trim($value['name']);
            }

            if ($py == 'H') {
                $H[] = trim($value['name']);
            }

            if ($py == 'I') {
                $I[] = trim($value['name']);
            }

            if ($py == 'J') {
                $J[] = trim($value['name']);
            }

            if ($py == 'K') {
                $K[] = trim($value['name']);
            }

            if ($py == 'L') {
                $L[] = trim($value['name']);
            }

            if ($py == 'M') {
                $M[] = trim($value['name']);
            }

            if ($py == 'N') {
                $N[] = trim($value['name']);
            }

            if ($py == 'O') {
                $O[] = trim($value['name']);
            }

            if ($py == 'P') {
                $P[] = trim($value['name']);
            }

            if ($py == 'Q') {
                $Q[] = trim($value['name']);
            }

            if ($py == 'R') {
                $R[] = trim($value['name']);
            }

            if ($py == 'S') {
                $S[] = trim($value['name']);
            }

            if ($py == 'T') {
                $T[] = trim($value['name']);
            }

            if ($py == 'U') {
                $U[] = trim($value['name']);
            }

            if ($py == 'V') {
                $V[] = trim($value['name']);
            }

            if ($py == 'W') {
                $W[] = trim($value['name']);
            }

            if ($py == 'X') {
                $X[] = trim($value['name']);
            }

            if ($py == 'Y') {
                $Y[] = trim($value['name']);
            }

            if ($py == 'Z') {
                $Z[] = trim($value['name']);
            }

        }
        if (!empty($A)) {
            $A_arr = array(
                'key' => 'A',
                'data' => $A,
            );
            $companyArr[] = $A_arr;
        }

        if (!empty($B)) {
            $B_arr = array(
                'key' => 'B',
                'data' => $B,
            );
            $companyArr[] = $B_arr;
        }

        if (!empty($C)) {
            $C_arr = array(
                'key' => 'C',
                'data' => $C,
            );
            $companyArr[] = $C_arr;
        }

        if (!empty($D)) {
            $D_arr = array(
                'key' => 'D',
                'data' => $D,
            );
            $companyArr[] = $D_arr;
        }

        if (!empty($E)) {
            $E_arr = array(
                'key' => 'E',
                'data' => $E,
            );
            $companyArr[] = $E_arr;
        }

        if (!empty($F)) {
            $F_arr = array(
                'key' => 'F',
                'data' => $F,
            );
            $companyArr[] = $F_arr;
        }

        if (!empty($G)) {
            $G_arr = array(
                'key' => 'G',
                'data' => $G,
            );
            $companyArr[] = $G_arr;
        }

        if (!empty($H)) {
            $H_arr = array(
                'key' => 'H',
                'data' => $H,
            );
            $companyArr[] = $H_arr;
        }

        if (!empty($I)) {
            $I_arr = array(
                'key' => 'I',
                'data' => $I,
            );
            $companyArr[] = $I_arr;
        }

        if (!empty($J)) {
            $J_arr = array(
                'key' => 'J',
                'data' => $J,
            );
            $companyArr[] = $J_arr;
        }

        if (!empty($K)) {
            $K_arr = array(
                'key' => 'K',
                'data' => $K,
            );
            $companyArr[] = $K_arr;
        }

        if (!empty($L)) {
            $L_arr = array(
                'key' => 'L',
                'data' => $L,
            );
            $companyArr[] = $L_arr;
        }

        if (!empty($M)) {
            $M_arr = array(
                'key' => 'M',
                'data' => $M,
            );
            $companyArr[] = $M_arr;
        }

        if (!empty($N)) {
            $N_arr = array(
                'key' => 'N',
                'data' => $N,
            );
            $companyArr[] = $N_arr;
        }

        if (!empty($O)) {
            $O_arr = array(
                'key' => 'O',
                'data' => $O,
            );
            $companyArr[] = $O_arr;
        }

        if (!empty($P)) {
            $P_arr = array(
                'key' => 'P',
                'data' => $P,
            );
            $companyArr[] = $P_arr;
        }

        if (!empty($Q)) {
            $Q_arr = array(
                'key' => 'Q',
                'data' => $Q,
            );
            $companyArr[] = $Q_arr;
        }

        if (!empty($R)) {
            $R_arr = array(
                'key' => 'R',
                'data' => $R,
            );
            $companyArr[] = $R_arr;
        }

        if (!empty($S)) {
            $S_arr = array(
                'key' => 'S',
                'data' => $S,
            );
            $companyArr[] = $S_arr;
        }

        if (!empty($T)) {
            $T_arr = array(
                'key' => 'T',
                'data' => $T,
            );
            $companyArr[] = $T_arr;
        }

        if (!empty($U)) {
            $U_arr = array(
                'key' => 'U',
                'data' => $U,
            );
            $companyArr[] = $U_arr;
        }

        if (!empty($V)) {
            $V_arr = array(
                'key' => 'V',
                'data' => $V,
            );
            $companyArr[] = $V_arr;
        }

        if (!empty($W)) {
            $W_arr = array(
                'key' => 'W',
                'data' => $W,
            );
            $companyArr[] = $W_arr;
        }

        if (!empty($X)) {
            $X_arr = array(
                'key' => 'X',
                'data' => $X,
            );
            $companyArr[] = $X_arr;
        }

        if (!empty($Y)) {
            $Y_arr = array(
                'key' => 'Y',
                'data' => $Y,
            );
            $companyArr[] = $Y_arr;
        }

        if (!empty($Z)) {
            $Z_arr = array(
                'key' => 'Z',
                'data' => $Z,
            );
            $companyArr[] = $Z_arr;
        }

        $returnArr['city'] = $companyArr;

        /**
         * 获取总部
         */
        $companyArr = array();
        $where = array();
        if ($revparType == 'finance') {
            $where['company'] = '总部';

        } else {
            $where['company'] = '总部';
        }
        $where['domain'] = $this->getDomain();

        $paymentmgr = $paymentmgrModel->where($where)->select();

        foreach ($paymentmgr as $value) {

            $companyArr[] = trim($value['name']);

        }
        $returnArr['area'] = $companyArr;

        $this->ajaxReturn($returnArr, 'JSON');
    }

}
