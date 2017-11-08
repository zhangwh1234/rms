<?php

/**
 * 企业组织机构模块管理
 * 分为3级，总公司->部门->工作区
 */
class OrganizationAction extends ModuleAction
{
    /**
     * 列表节点
     */
    public function listview()
    {
        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块的模型
        $focus = D($moduleName);

        //取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName);         //导航名称

        //生成list字段列表
        $listFields = $focus->listFields;
        //模块的ID
        $moduleId = $focus->getPk();
        //加入模块id到listHeader中
        //array_unshift($listFields,$moduleNameId);
        $listHeader = $listFields;
        $this->assign("listHeader", $listHeader);   //列表头
        $this->assign('returnAction', 'listview');  //定义返回的方法

        //导入分页类
        import('ORG.Util.Page');// 导入分页类
        $total = $focus->count();// 查询满足要求的总记录数
        //查session取得page的firstRos和listRows


        if (!isset($_SESSION[$moduleName . 'firstRowlistview'])) {
            $Page->firstRow = $_SESSION[$moduleName . 'firstRowlistview'];
        }

        //var_dump($_SESSION['test']);
        $listMaxRows = C('LIST_MAX_ROWS'); //定义显示的列表函数
        if (isset($listMaxRows)) {
            $Page->listRows = $listMaxRows;
        } else {
            $listMaxRows = 15;
        }
        $Page = new Page($total, $listMaxRows);
        $show = $Page->show();


        //查询模块的数据
        $selectFields = $listFields;
        array_unshift($selectFields, $moduleId);
        $listResult = $focus->field($selectFields)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order("$moduleId desc")->select();

        // 从数据中列出列表的数据
        $listviewEntries = $this->getListviewEntity($listResult, $moduleId);

        $where = array();
        $where['domain'] = $this->getDomain();

        $this->assign('moduleId', $moduleId);
        $this->assign('listEntries', $listviewEntries);
        $this->assign('page', $show);// 赋值分页输出

        $field = array('id', 'name', 'pid');
        $organization = D('organization')->where($where)->field($field)->select();

        $this->organization = node_merge($organization);

        $this->display('Organization/listview');
    }


    /**
     * 新建部门
     */
    public function createviewDepartment()
    {
        $this->createview();
    }


    //插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $arr = array(
            array('level', 1),
            array('sort', 1),
            array('domain', $this->getDomain())
        );
        return $arr;
    }

    /**
     * 编辑部门
     */
    public function editviewDepartment()
    {
        $this->editview();
    }

    /**
     * 删除部门
     */
    public function deleteDepartment()
    {
        //返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        //取得保存的主键
        $record = $_REQUEST['record'];

        $moduleId = $focus->getPk();

        $where[$moduleId] = $record;

        //查询模块下面是否有方法，如果有方法，就不能删除
        $result = $focus->where("pid=$record")->count();
        if ($result > 0) {
            $this->error('不能删除', "$moduleName/listview");
        }

        //删除记录
        $result = $focus->where($where)->delete();

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '删除成功';
            $this->ajaxReturn($info, 'JSON');
        } else {
            $info['status'] = 0;
            $info['info'] = '删除失败';
            $this->ajaxReturn($info, 'JSON');
        }

    }

    /**
     * 新建功能
     */
    public function createviewTeam()
    {
        //取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName);   //模块名称

        //启动当前模块的模型
        $focus = D($moduleName);

        //取得对应的导航名称
        $navName = $focus->getnavName($moduleName);
        $this->assign('navName', $navName);         //导航名称

        //改写创建的字段为功能的字段
        $focus->createFields = $focus->createTeamFields;

        //返回新建区块和字段
        $blocks = $focus->createBlocks();

        $this->pid = I('pid');

        $this->assign('blocks', $blocks);         //编辑字段区
        $this->assign('fields_focus', $this->getFocusFields());  //指定字段获得焦点

        $this->display();
    }

    /**
     * 保存模块的方法
     *
     */
    public function insertTeam()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName ();

        //返回当前的模块名
        $currentModule = $this->getActionName();
        $focus = D($currentModule);

        $pid = $_REQUEST['pid'];
        $name = $_REQUEST['teamname'];
        $auto = array(
            array('pid', $pid),
            array('name', $name),
            array('level', 2),
            array('domain', $this->getDomain())
        );

        $focus->setProperty("_auto", $auto);
        //保存主表
        $focus->create();
        $result = $focus->add();

        $record = $result;

        $info['record'] = $result;
        // 生成查看的url
        $detailviewUrl = U( "$moduleName/detailview", array ('record' => $record
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $info['url'] = $return;
        $this->ajaxReturn($info,'JSON');

    }

    /***
     * 编辑班组
     */
    public function editviewTeam()
    {
        $this->editview();
    }

    //编辑保存
    public function updateTeam(){
        $moduleName = $this->getActionName ();

        //返回当前的模块名
        $currentModule = $this->getActionName();
        $focus = D($currentModule);

        $record = $_REQUEST['record'];
        $name = $_REQUEST['teamname'];
        $data = array();

        $data['id'] = $record;
        $data['name'] = $name;

        //保存主表
        $result = $focus->save($data);

        $record = $result;

        $info['record'] = $result;
        // 生成查看的url
        $detailviewUrl = U ( "$moduleName/listview", array (
        ) );
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] ='保存成功' ;
        $info['url'] = $return;
        $this->ajaxReturn($info,'JSON');
    }

    /**
     * 删除班组
     */
    public function deleteTeam()
    {
        //返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        //取得保存的主键
        $record = $_REQUEST['record'];

        $moduleId = $focus->getPk();

        $where[$moduleId] = $record;

        //删除记录
        $result = $focus->where($where)->delete();

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '删除成功';
            $this->ajaxReturn($info, 'JSON');
        } else {
            $info['status'] = 0;
            $info['info'] = '删除失败';
            $this->ajaxReturn($info, 'JSON');
        }
    }
}

?>
