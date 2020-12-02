<?php
/**
 * Created by Zhangwh1234
 * User: apple
 * Date: 17/9/30
 * Time: 下午3:59
 * 客户管理:营收结账系统使用
 */

class PaymentMgrAction extends ModuleAction
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
            $searchText = urldecode($_REQUEST['searchText']); // 查询内容
            if (!empty($searchText)) {
                foreach ($focus->searchFields as $value) {
                    $where[$value] = array(
                        'like',
                        '%' . $searchText . '%',
                    );
                }
                $where['_logic'] = 'OR';
            } else {
                $searchText = $_SESSION['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchText = $_SESSION['searchText' . $moduleName];
                    foreach ($focus->searchFields as $value) {
                        $where[$value] = array(
                            'like',
                            '%' . $searchText . '%',
                        );
                    }
                    $where['_logic'] = 'OR';
                }
            }

            $this->returnWhere($where);

            //查询名称
            $search_name = $_REQUEST['search_name'];
            $where['name'] = array('like', '%' . $search_name . '%');
            //查询是否审核
            $search_shenhe = $_REQUEST['search_shenhe'];
            if ($search_shenhe == '审核') {
                $where['is_shenhe'] = 1;
            }
            if ($search_shenhe == '未审核') {
                $where['is_shenhe'] = 0;
            }
            if ($search_shenhe == '核算项空') {
                $where['accounting'] = '';
            }
            if ($search_shenhe == '科目空') {
                $where['subject'] = '';
            }

            // 配送店（分公司）的信息
            // 分公司的名称
            $company = $this->userInfo['department'];
            $revparType = $this->getRevparType();

            $where['_logic'] = 'AND';
            if ($revparType == 'company') {
                $where['company'] = array(
                    array('eq', $company),
                    array('eq', '总部'),
                    'or',
                );
            } else {

            }

            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST['page'];
            if (empty($pageNumber)) {
                // 查session取得page的值
                if (!empty($_SESSION[$moduleName . 'page'])) {
                    $pageNumber = $_SESSION[$moduleName . 'page'];
                } else {
                    $pageNumber = 1;
                }
            }

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
                ->order("code,company  asc")->select();

            //重新计算，以后和分公司的逻辑相适应
            $orderHandleArray = array();
            if ($revparType == 'company') {
                foreach ($listResult as $value) {
                    if ($value['company'] == '总部') {
                        $value['is_use'] = 1;
                    };
                    $value['revpartype'] = 'company';
                    $orderHandleArray[] = $value;
                }
            } else {
                foreach ($listResult as $value) {
                    if ($revparType == 'finance') {
                        $value['revpartype'] = 'finance';
                    }
                    $orderHandleArray[] = $value;
                }
            }

            if (empty($orderHandleArray)) {
                $orderHandleArray = array();
            }

            $data = array('total' => $total, 'rows' => $orderHandleArray, 'sql' => $focus->getLastSql());
            $this->ajaxReturn($data);
        } else {
            // 取得模块的名称
            $moduleName = $this->getActionName();
            $this->assign('moduleName', $moduleName); // 模块名称

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION['searchText' . $moduleName]);
                unset($_SESSION[$moduleName . 'page']);
            }

            // 启动当前模块的模型
            $focus = D($moduleName);

            // 取得对应的导航名称
            $navName = $focus->getNavName($moduleName);
            $this->assign('navName', $navName); // 导航名称

            // 生成list字段列表
            $listFields = $focus->listFields;
            // 模块的ID
            $moduleId = $focus->getPk();

            //是否有查询字段
            $searchText = $_REQUEST['searchText']; // 查询内容
            if (!empty($searchText)) {
                $searchArray = array('searchText' => $searchText);
                $this->assign('searchIntroduce', '查询内容:' . $searchText);
                $_SESSION['searchText' . $moduleName] = $searchText;
            } else {
                $searchText = $_SESSION['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchArray = array('searchText' => $searchText);
                    $this->assign('searchIntroduce', '查询内容:' . $searchText);
                } else {
                    $_SESSION['searchText' . $moduleName] = '';
                }
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $searchArray),
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
            $datagrid['fields']['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate',
            );
            $this->assign('datagrid', $datagrid);
            $this->assign('moduleId', $moduleId);

            // 执行list的一些其它数据的操作
            $this->listviewOther();
            $this->display($moduleName . '/listview'); // 执行方法自身的列表
        }
    }

    // 返回一些其他的数据,比如下拉列表框等的数据
    public function returnMainFnPara($result)
    {
        // 配送店（分公司）的信息
        // 分公司的名称
        $companyName = $this->userInfo['department'];
        $revparType = $this->getRevparType();

        $companymgrModel = D('companymgr');
        $where = array();
        $where['domain'] = $this->getDomain();
        $companyResult = $companymgrModel->where($where)->select();

        if ($revparType == 'finance') {
            $company = array(
                array(
                    'name' => '总部',
                ),
            );
            foreach ($companyResult as $value) {
                $company[]['name'] = $value['name'];
            }
        } else {
            $company = array(
                array(
                    'name' => $companyName,
                ),
            );
            $this->assign('currentcompany', $companyName);
        }
        $this->assign('company', $company);

        $type = array(
            array(
                'name' => '外送',
            ),
            array(
                'name' => '工作餐',
            ),
        );
        $this->assign('type', $type);
        $this->assign('currenttype', '外送');
    }

    /**
     * 返回编辑的数据
     */
    public function get_slave_table($record)
    {
        $paymentmgrModel = D('paymentmgr');
        $where = array();
        $where['paymentmgrid'] = $record;
        $paymentmgrResult = $paymentmgrModel->where($where)->find();
        $this->assign('is_edit', $paymentmgrResult['is_edit']);
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
     * 审核单据
     */
    public function audit()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['paymentmgrid'];

        $moduleId = $focus->getPk();

        $where['paymentmgrid'] = $record;
        $data = array();
        $data['is_shenhe'] = 1;

        // 删除记录
        $result = $focus->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '审核成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '审核失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }

    /**
     * 审核单据
     */
    public function noAudit()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST['paymentmgrid'];

        $moduleId = $focus->getPk();

        $where['paymentmgrid'] = $record;
        $data = array();
        $data['is_shenhe'] = 0;

        // 删除记录
        $result = $focus->where($where)->save($data);

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '弃审成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '弃审失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }

    /**
     * 客户支付管理的一些规则说明：
     * 1、有全局支付和分公司支付，比如现金，赠卡
     * 2、客户有单个分公司使用或者有几个分公司共享的客户
     * 3、使用过的客户不能编辑和删除
     * 4、客户支付管理放在财务？分公司只是可以查看
     * 4、客户余额和最后使用日期放在表中？
     */

}
