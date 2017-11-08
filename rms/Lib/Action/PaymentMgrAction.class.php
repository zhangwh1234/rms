<?php
/**
 * Created by Zhangwh1234
 * User: apple
 * Date: 17/9/30
 * Time: 下午3:59
 * 客户管理:营收结账系统使用
 */

class PaymentMgrAction extends ModuleAction{

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
            $searchText = urldecode($_REQUEST ['searchText']); // 查询内容
            if (!empty ($searchText)) {
                foreach ($focus->searchFields as $value) {
                    $where [$value] = array(
                        'like',
                        '%' . $searchText . '%'
                    );
                }
                $where ['_logic'] = 'OR';
            } else {
                $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchText = $_SESSION ['searchText' . $moduleName];
                    foreach ($focus->searchFields as $value) {
                        $where [$value] = array(
                            'like',
                            '%' . $searchText . '%'
                        );
                    }
                    $where ['_logic'] = 'OR';
                }
            }

            $this->returnWhere($where);

            $where['_logic'] = 'AND';
            $where['domain'] = $this->getDomain();

            // 导入分页类
            import('ORG.Util.Page'); // 导入分页类
            $total = $focus->where($where)->count(); // 查询满足要求的总记录数

            // 取得显示页数
            $pageNumber = $_REQUEST ['page'];
            if (empty ($pageNumber)) {
                // 查session取得page的值
                if (!empty ($_SESSION [$moduleName . 'page'])) {
                    $pageNumber = $_SESSION [$moduleName . 'page'];
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
            $_GET ['p'] = $pageNumber;
            $Page = new Page ($total, $listMaxRows);

            //保存页数
            $_SESSION [$moduleName . 'page'] = $pageNumber;

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

            //是否清除session的内容
            $delSession = $_REQUEST['delsession'];
            if (isset($delSession)) {
                unset($_SESSION ['searchText' . $moduleName]);
                unset($_SESSION [$moduleName . 'page']);
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
            $searchText = $_REQUEST ['searchText']; // 查询内容
            if (!empty($searchText)) {
                $searchArray = array('searchText' => $searchText);
                $this->assign('searchIntroduce', '查询内容:' . $searchText);
                $_SESSION ['searchText' . $moduleName] = $searchText;
            } else {
                $searchText = $_SESSION ['searchText' . $moduleName]; // 查询内容
                if (!empty($searchText)) {
                    $searchArray = array('searchText' => $searchText);
                    $this->assign('searchIntroduce', '查询内容:' . $searchText);
                } else {
                    $_SESSION ['searchText' . $moduleName] = '';
                }
            }

            $datagrid = array(
                'options' => array(
                    'url' => U($moduleName . '/listview', $searchArray),
                    'pageNumber' => 1,
                    'pageSize' => 10
                )
            );
            foreach ($listFields as $key => $value) {
                $header = L($key);
                $datagrid ['fields'] [$header] = array(
                    'field' => $key,
                    'align' => $value['align'],
                    'halign' => $value['halign'],
                    'width' => $value['width']
                );
            }
            $datagrid ['fields'] ['操作'] = array(
                'field' => 'id',
                'width' => 20,
                'align' => 'center',
                'formatter' => $moduleName . 'ListviewModule.operate'
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
            $companymgrModel = D('companymgr');
            $where = array();
            $companyResult = $companymgrModel->where($where)->select();

            $company = array(
                array(
                    'name' => '总部',
                ),           
            );
            foreach($companyResult as $value){
                $company[]['name'] = $value['name'];
            }
            $this->assign('company', $company);
            
            $type = array(
                array(
                    'name' => '外送',
                ),
                array(
                    'name' => '工作餐',
                )
            );
            $this->assign('type', $type);            
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