<?php
/**
 * User: apple
 * Date: 17/10/16
 * Time: 下午12:15
 * 定义营收系统的外送结账，财务结账等的基类
 * 主要是产生数据是从日期，午别中参数中产生
 */

class YingshouSearchDateAction extends YingshouAction{



    // 查看数据的页面
    public function detailview() {

        // 取得模块的名称
        $moduleName = $this->getActionName ();
        $this->assign ( 'moduleName', $moduleName ); // 模块名称

        // 启动当前模块
        $focus = D ( $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        $currDate = date('Y-m-d');
        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();
        // 连接数据库
        $Model = M($tableName .substr($currDate,5,2) , " ", $connectionDb);

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign ( 'returnAction', $returnAction );

        // 模块的ID
        $moduleId = $focus->getPk ();

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $where [$moduleId] = $record;

        // 返回模块的行记录
        $result = $Model->where ( $where )->find ();
        var_dump($Model->getLastSql());

        // 返回区块
        $blocks = $focus->detailBlocks ( $result );

        $this->assign ( 'info', $result );
        $this->assign ( 'record', $record );
        $this->assign ( 'blocks',$blocks);
        $this->assign ( 'pagenumber',$_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign ( 'rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign ( 'pagetype' , $_REQUEST['pagetype']);

        // 返回从表的内容
        $this->get_slave_table ( $record );
        $this->display ( $moduleName . '/detailview' );
    }

    // 创建新数据createView
    public function createview() {
        // 返回当前的模块名
        $moduleName = $this->getActionName ();

        $focus = D ( $moduleName );
        $this->assign ( 'moduleName', $moduleName );

        // 取得对应的导航名称
        $navName = $focus->getNavName ( $moduleName );
        $this->assign ( 'navName', $navName ); // 导航民

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara ();

        $returnAction = $_REQUEST['returnAction'];
        $this->assign('returnAction',$returnAction);

        // 模块的ID
        $moduleNameId = strtolower ( $moduleName ) . 'id';
        // 返回缓存blocks,不用缓存,不影响速度
        //$moduleBlocks = F ( $moduleName . 'Blocks' );
        if (! empty ( $moduleBlocks )) {
            $this->blocks = $moduleBlocks;
        } else {
            // 返回新建区块和字段
            $this->blocks = $focus->createBlocks ();
            // 缓存blocks
            F ( $moduleName . 'Blocks', $this->blocks );
        }

        $this->assign ( 'blocks', $this->blocks ); // 编辑字段区
        $this->assign ( 'fieldsFocus', $focus->fieldsFocus ); // 指定字段获得焦点

        $this->display ( $moduleName . '/createview' );
    }


    // 编辑结账数据的页面editview
    public function editview()
    {

        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航民

        // 取得返回的是列表还是查询列表
        $returnAction = $_REQUEST ['returnAction'];
        $this->assign('returnAction', $returnAction);


        // 模块的ID
        $moduleId = $focus->getPk();

        // 取得记录ID
        $record = $_REQUEST ['record'];
        $getDate = $_REQUEST['getDate'];

        $where = array();
        $where [$moduleId] = $record;

        $connectionDb = $this->connectReveueDb($getDate);

        // 连接数据库
        $roomserviceModel = M("roomservice_" . substr($getDate, 5, 2), " ", $connectionDb);


        // 返回模块的行记录
        $result = $roomserviceModel->where($where)->find();

        //对数据进行一些处理，防止0.00的显示，不方便
        if (!($result['turnover'] > 0)) $result['turnover'] = '';
        if (!($result['cash'] > 0)) $result['cash'] = '';
        if (!($result['nocharge'] > 0)) $result['nocharge'] = '';
        if (!($result['cheque'] > 0)) $result['cheque'] = '';
        if (!($result['note'] > 0)) $result['note'] = '';
        if (!($result['freebie'] > 0)) $result['freebie'] = '';
        if (!($result['mealticket'] > 0)) $result['mealticket'] = '';
        if (!($result['tickettelphone'] > 0)) $result['tickettelphone'] = '';


        $this->assign('info', $result);
        $this->assign('fieldsFocus', $focus->fieldsFocus); // 指定字段获得焦点
        $this->assign('record', $record); // 订单记录号
        $this->assign('pagenumber', $_SESSION[$moduleName . $_REQUEST['pagetype'] . 'page']);
        $this->assign('rowIndex', $_REQUEST['rowIndex']);  //选中的行号
        $this->assign('pagetype', $_REQUEST['pagetype']);
        $this->assign('getDate', $getDate);

        // 回调主程序需要的参数,比如下拉框的数据
        $this->returnMainFnPara();

        // 返回从表的内容
        $this->get_slave_table($record, $getDate);

        $this->display($moduleName . '/editview');

    }


    // 插入，补充数据的回调函数
    public function autoParaInsert()
    {
        $data = array(
            array(
                'domain',
                $_SERVER ['HTTP_HOST']
            ),
            array(
                'create_time',
                date('Y-m-d H:i:s')
            )
        );
        return $data;
    }


    /* 一般顺序表记录的保存 */
    public function insert()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName .substr($getDate,5,2) , " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaInsert();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $result = $Model->create();


        if (!$result) {
            exit ($Model->getError());
        }
        $result = $Model->add();

        if (!$result) {
            $info['status'] = 0;
            $info['info'] = '保存数据不成功！';
            $info['sql'] = $Model->getLastSql();
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

        // 取得保存的主键
        $record = $result;

        // 新写的保存从表方案
        $result = $this->save_slave_table($record,$getDate);

        // 如果保存订单都成功，就跳转到查看页面
        $return ['record'] = $record;

        $returnAction = $_REQUEST['returnAction'];

        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }


    // 更新，补充数据的回调函数
    public function autoParaUpdate()
    {
        $data = array(
            array(
                'domain',
                $_SERVER ['HTTP_HOST']
            ),
            array(
                'update_time',
                date('Y-m-d H:i:s')
            )
        );
        return $data;
    }

    // 更新记录
    public function update()
    {

        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);
        // 返回的页面
        $returnAction = $_REQUEST ['returnAction'];

        // 取得记录号
        $record = $_REQUEST ['record'];
        $moduleId = $focus->getPk();

        $getDate = $_REQUEST['getDate'];
        //连接字符串
        $connectionDb = $this->connectReveueDb($getDate);
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName .substr($getDate,5,2) , " ", $connectionDb);

        // 回调自动完成的函数
        $auto = $this->autoParaUpdate();
        $Model->setProperty("_auto", $auto);

        // 保存主表
        $Model->create();

        $where = array();
        $where[$moduleId] = $record;
        $result = $Model->where($where)->save();

        // 新写的保存从表方案
        $slaveResult = $this->update_slave_table($record);

        $return ['record'] = $record;
        $pagetype = $_REQUEST['pagetype'];
        // 生成查看的url
        $detailviewUrl = U("$moduleName/detailview", array(
            'record' => $record, 'returnAction' => $returnAction,
            'rowIndex' => $_REQUEST['rowIndex'], 'getDate' => $getDate
        ));
        $return = $detailviewUrl;
        $info['status'] = 1;
        $info['info'] = $this->info . ' 保存成功';
        $info['url'] = $return;
        $this->ajaxReturn(json_encode($info), 'EVAL');
    }

    /* 删除记录 */
    public function delete()
    {
        // 返回当前的模块名
        $moduleName = $this->getActionName();

        $focus = D($moduleName);
        $this->assign('moduleName', $moduleName);

        // 取得保存的主键
        $record = $_REQUEST ['record'];

        $moduleId = $focus->getPk();

        $where [$moduleId] = $record;

        $currDate = date('Y-m-d');
        //连接字符串
        $connectionDb = $this->connectReveueDb('');
        //连接的数据表
        $tableName = $focus->getTableName();

        // 连接数据库
        $Model = M($tableName .substr($currDate,5,2) , " ", $connectionDb);

        // 删除记录
        $result = $Model->where($where)->delete();

        if ($result) {
            $info['status'] = 1;
            $info['info'] = '删除成功';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        } else {
            $info['status'] = 0;
            $info['info'] = '删除失败';
            $this->ajaxReturn(json_encode($info), 'EVAL');
        }

    }


}