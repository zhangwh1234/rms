<?php
  /**
  * 发货单输入，记录发货订单的时间
  * 2014-1-3开始编制
  */
  
  class OrderDeliverAction extends ModuleAction{

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
        $where ['domain'] = $this->getDomain();

        // 导入分页类
        import('ORG.Util.Page'); // 导入分页类
        $total = $focus->where($where)->count(); // 查询满足要求的总记录数

        // 取得显示页数
        $pageNumber = $_REQUEST ['page'];
        if (empty ($pageNumber)) {
          $pageNumber = 1;
          // 查session取得page的值
          if (!empty ($_SESSION [$moduleName . 'page'])) {
            $pageNumber = $_SESSION [$moduleName . 'page'];
          }
        }

        //使用cookie读取rows
        $listMaxRows = $_COOKIE['listMaxRows'];
        if(!empty($listMaxRows)){

        }else{
          $listMaxRows = C ( 'LIST_MAX_ROWS' ); // 定义显示的列表函数
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

        $listResult = $focus->where($where)->field($selectFields)->limit($Page->firstRow . ',' . $Page->listRows)->order("lastdatetime desc,$moduleId desc")->select();

        $orderHandleArray ['total'] = $total;
        if (count($listResult) > 0) {
          $orderHandleArray  = $listResult;
        } else {
          $orderHandleArray  = array();
        }
        $data = array('total' => $total, 'rows' => $orderHandleArray);
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
              'width' => $value['width']
          );
        }
        $datagrid ['fields'] ['操作'] = array(
            'field' => 'id',
            'width' => 20,
            'align' => 'center',
            'formatter' => $moduleName . 'ListviewModule.operate'
        );

        //计算接线员的接单量
        // 接线员的姓名
        $userInfo = $_SESSION ['userInfo'];
        $name = $userInfo ['truename'];
        $where =array();
        $where['telname'] = $name;
        $telOrderNumber = $focus->where($where)->count();
        $telOrderNumber = '['.$name.']' . $telOrderNumber . '张订单';

        $this->assign('orderformOtherMsg',$telOrderNumber);
        $this->assign('datagrid', $datagrid);
        $this->assign('moduleId', $moduleId);

        $this->assign('returnAction', 'listview');
        $this->display($moduleName . '/listview'); // 执行方法自身的列表
      }
    }
      
  }
?>
