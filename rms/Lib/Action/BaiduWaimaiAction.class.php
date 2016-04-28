<?php

/**
 * 百度外卖平台管理；主要管理商户信息，菜单信息，订单信息，完成和百度平台的接口工作。
 * 2015-03-18开始编制
 * source_name：lihua
 * secret_key:lihuashi_test
 * 测试服务器地址：123.125.115.62:8086
 * source_name对应的值，也就是lihua
 */
class BaiduWaimaiAction extends ModuleAction
{
    // 显示管理主面板
    public function index()
    {
        $this->display('mainview');
    }

    // 显示管理主面板的菜单
    public function mainviewLeftMenu()
    {
        $this->display('mainviewleftmenu');
    }

    // 显示左面板的菜单
    public function mainviewTopMenu()
    {
        // 取得模块的名称
        $moduleName = $this->getActionName();
        $this->assign('moduleName', $moduleName); // 模块名称

        // 启动当前模块的模型
        $focus = D($moduleName);

        // 取得对应的导航名称
        $navName = $focus->getNavName($moduleName);
        $this->assign('navName', $navName); // 导航名称

        $this->display('mainviewtopmenu');
    }

    // 显示平台说明
    public function mainviewPlatformIntroduce()
    {
        $this->display('mainviewplatformintroduce');
    }

    // 商店信息管理列表查看
    public function shopinfoListview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $total = $baiduwaimaishopinfoModel->where($where)->count();
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();

        // 从数据中列出列表的数据
        if (count($baiduwaimaishopinfoResult) > 0) {
            $listData ['rows'] = $baiduwaimaishopinfoResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }

        $this->assign('listData', json_encode($listData));
        $this->display('shopinfolistview');
    }

    // 新建商户
    public function shopinfoCreateview()
    {
        $this->display('shopinfocreateview');
    }

    // 保存新建商户的信息
    public function shopinfoSave()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $data = array();
        $data = $_REQUEST;

        // 保存商店log
        if ($_FILES ['shop_logo'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['shop_logo'] = $ftpPathName;
            }
        }
        //处理字符串，防止有回车等特俗字符
        $data['content'] = $this->ReMoveChar($data['content']);
        $data['delivery_region_region'] = str_replace(PHP_EOL, '', $data['delivery_region_region']);
        $data ['domain'] = $_SERVER ['HTTP_HOST'];

        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoModel->create();
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->add($data);

        $returnData = array();
        if ($baiduwaimaishopinfoResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn($returnData, 'JSON');
    }

    // 查看商户信息
    public function shopinfoDetailview()
    {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $this->assign("shopinfo", $baiduwaimaishopinfoResult [0]);
        $this->display('shopinfodetailview');
    }

    // 编辑商户
    public function shopinfoEditview()
    {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $this->assign("shopinfo", $baiduwaimaishopinfoResult [0]);
        $this->display('shopinfoeditview');
    }

    // 编辑商户信息后保存
    public function shopinfoEditUpdate()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $where = array();
        $where ['shopinfoid'] = $_REQUEST ['shopinfoid'];
        $data = array();
        $data = $_REQUEST;

        // 保存商店log
        if ($_FILES ['shop_logo_tmp'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }
            if ($info [0] ['savename']) {
                // 上传品牌的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['shop_logo'] = $ftpPathName;
            }
        }
        //处理字符串，防止有回车等特俗字符
        $data['content'] = $this->ReMoveChar($data['content']);

        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->save($data);

        $returnData = array();
        if ($baiduwaimaishopinfoResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    // 设置上传信息
    public function shopinfoCreate()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoCreate($body);
            $data = array();
            $data ['shop_state'] = 1;
            $baiduwaimaishopinfoModel->where($where)->save($data);
            $this->ajaxReturn($resp);
        } else{

        }
    }

    //设置下线商户
    public function shopinfoOffline()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoOffline($body);
            $data = array();
            $data ['shop_state'] = 0;
            $baiduwaimaishopinfoModel->where($where)->save($data);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //列表商户信息
    public function shopinfoList()
    {
        import('@.Extend.Baiduwaimai');
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaiApi = new Baiduwaimai();
        $resp = $baiduwaimaiApi->shopinfoList();
        if($resp['data']){
            foreach($resp['data'] as $key=>$value){
                $data = array();
                $data['baidu_shop_id'] = $value['baidu_shop_id'];
                $where = array();
                $where['shopinfoid'] = $value['shop_id'];
                $baiduwaimaishopinfoModel->where($where)->save($data);
            }
        }
        $this->ajaxReturn($resp);
    }

    // 设置查询信息
    public function shopinfoShow()
    {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $data = array();
        $data ['showstate'] = 1;
        $baiduwaimaishopinfoModel->where($where)->save($data);
        echo '设置查询成功';
    }

    // 设置更新信息
    public function shopinfoUpdate()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoUpdate($body);
            $this->ajaxReturn($resp);
        } else {

        };
    }

    // 设置休息信息
    public function shopinfoClose()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoClose($body);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    // 设置恢复营业信息
    public function shopinfoOpen()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoOpen($body);
            $this->ajaxReturn($resp);
        } else {

        }
        $data = array();
        $data ['openstate'] = 1;
        $data ['business_state'] = 1;
        $baiduwaimaishopinfoModel->where($where)->save($data);
        echo '设置恢复营业成功';
    }

    // 设置订单阈值
    public function shopinfoThreshold()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoTheshold($body);
            $this->ajaxReturn($resp);
        } else {

        }
        $data = array();
        $data ['thresholdstate'] = 1;
        $baiduwaimaishopinfoModel->where($where)->save($data);
        echo '设置上传订单阈值成功';
    }

    // 设置商户配送时延参数
    public function shopinfoDeliveryDelay()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoDelivery($body);
            $this->ajaxReturn($resp);
        } else {

        }
        $data = array();
        $data ['deliverystate'] = 1;
        $baiduwaimaishopinfoModel->where($where)->save($data);
        echo '设置上传商户配送时延成功';
    }

    // 设置商户公告
    public function shopinfoContent()
    {
        import('@.Extend.Baiduwaimai');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->find();
        if ($baiduwaimaishopinfoResult) {
            $body = array();
            $body = $baiduwaimaishopinfoResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->shopinfoContent($body);
            $this->ajaxReturn($resp);
        } else {

        }
        $data = array();
        $data ['contentstate'] = 1;
        $baiduwaimaishopinfoModel->where($where)->save($data);
        echo '设置上传商户公告成功';
    }

    // 分类管理列表
    public function categorymgrListview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $total = $baiduwaimaicategorymgrModel->where($where)->count();
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->select();
        // 从数据中列出列表的数据
        if (count($baiduwaimaicategorymgrResult) > 0) {
            $listData ['rows'] = $baiduwaimaicategorymgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('categorymgrlistview');
    }

    //新建分类
    public function categorymgrCreateview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $this->assign('categorymgr', $shopinfoResult);
        $this->display('categorymgrcreateview');
    }

    // 保存分类内容
    public function categorymgrSave()
    {
        $data = array();
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrModel->create();
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->add($data);
        $returnData = array();
        if ($baiduwaimaicategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    //分类编辑页面
    public function categorymgrEditview()
    {
        $categoryid = $_REQUEST ['categoryid'];
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();

        $this->assign('categorymgr', $baiduwaimaicategorymgrResult);
        $this->display('categorymgreditview');
    }

    // 更新分类内容
    public function categorymgrEditUpdate()
    {
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $where = array();
        $where['categoryid'] = $_REQUEST['categoryid'];
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        //查询原产品分类
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if($baiduwaimaicategorymgrResult) {
            $old_name = $baiduwaimaicategorymgrResult['name'];
        }else{
            $old_name = '';
        }
        $data['old_name'] = $old_name;
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->save($data);
        $returnData = array();
        if ($baiduwaimaicategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn($returnData, 'JSON');
    }

    //查看分类内容
    public function categorymgrDetailview()
    {
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        $this->assign('categorymgr', $baiduwaimaicategorymgrResult);
        $this->display('categorymgrdetailview');
    }

    //新增菜品分类
    public function categorymgrCreate()
    {
        import('@.Extend.Baiduwaimai');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if ( $baiduwaimaicategorymgrResult) {
            $body = array();
            $body =  $baiduwaimaicategorymgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->categorymgrCreate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //更新菜品分类
    public function categorymgrUpdate(){
        import('@.Extend.Baiduwaimai');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if ( $baiduwaimaicategorymgrResult) {
            $body = array();
            $body =  $baiduwaimaicategorymgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->categorymgrUpdate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }



    // 菜单管理列表
    public function menumgrListview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $total = $baiduwaimaimenumgrModel->where($where)->count();
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->select();
        // 从数据中列出列表的数据
        if (count($baiduwaimaimenumgrResult) > 0) {
            $listData ['rows'] = $baiduwaimaimenumgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('menumgrlistview');
    }

    // 新建菜单信息
    public function menumgrCreateview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $fields = array(
            'shopinfoid',
            'name'
        );
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->field($fields)->where($where)->select();
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);
        $this->assign('categorymgr',$baiduwaimaicategorymgrResult);
        $this->display('menumgrcreateview');
    }

    // 保存菜单内容
    public function menumgrSave()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $data = array();
        $data = $_REQUEST;
        if ($_FILES ['pic_tmp'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['pic'] = $ftpPathName;
            }
        }
        unset($data['pic_tmp']);
        $data['description'] = $this->ReMoveChar($data['description']); //处理字符串，防止有回车等特俗字符
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrModel->create();
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->add($data);
        $returnData = array();
        if ($baiduwaimaimenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    // 编辑菜单信息
    public function menumgrEditview()
    {
        $menuid = $_REQUEST ['menuid'];
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->select();

        if( $baiduwaimaimenumgrResult [0] ['shop_id'] == 0){
            $this->assign('shopinfo_name', '全部公司');
        }else{
            // 取得shopid的商户名称
            $where = array();
            $where ['domain'] = $_SERVER ['HTTP_HOST'];
            $where ['shopinfoid'] = $baiduwaimaimenumgrResult [0] ['shop_id'];
            $fields = array(
                'shopinfoid',
                'name'
            );
            $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
            $shopinfoResult = $baiduwaimaishopinfoModel->field($fields)->where($where)->select();
            $this->assign('shopinfo_name', $shopinfoResult [0] ['name']);

        }
        // 取得所有商户号和名称
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];

        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);

        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->select();
        $this->assign('categorymgr',$baiduwaimaicategorymgrResult);

        $this->assign('menumgr', $baiduwaimaimenumgrResult [0]);
        $this->display('menumgreditview');
    }

    // 保存菜单修改信息
    public function menumgrEditUpdate()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['menuid'] = $menuid;
        $data = array();
        $data = $_REQUEST;
        if ($_FILES ['pic_tmp'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['pic'] = $ftpPathName;
            }
        }
        unset($data['pic_tmp']);
        $data['description'] = $this->ReMoveChar($data['description']); //处理字符串，防止有回车等特俗字符
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->save($data);

        $returnData = array();
        if ($baiduwaimaimenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    // 查看菜单信息
    public function menumgrDetailview()
    {
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->select();
        $this->assign('menumgr', $baiduwaimaimenumgrResult [0]);
        $this->display('menumgrdetailview');
    }

    // 设置上传菜单信息
    public function menumgrCreate()
    {
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrCreate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    // 设置查询菜单信息
    public function menumgrShow()
    {
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $data = array();
        $data ['showstate'] = 1;
        $baiduwaimaimenumgrModel->where($where)->save($data);
        echo '设置查询成功';
    }

    // 设置更新菜单信息
    public function menumgrUpdate()
    {
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrUpdate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    // 设置删除菜单信息
    public function menumgrDelete()
    {
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrDelete($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }

    }

    //菜单上线设置
    public function menumgrOnline(){
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrOnline($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //菜单下线设置
    public function menumgrOffline(){
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrOffline($body,$baiduwaimaishopinfoResult );
            $this->ajaxReturn($resp);
        } else {
        }
    }

    //菜单阈值设置
    public function menumgrThreshold(){
        import('@.Extend.Baiduwaimai');
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['menuid'] = $menuid;
        $baiduwaimaimenumgrModel = D('baiduwaimaimenumgr');
        $baiduwaimaimenumgrResult = $baiduwaimaimenumgrModel->where($where)->find();
        if ( $baiduwaimaimenumgrResult) {
            $body = array();
            $body =  $baiduwaimaimenumgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->menumgrThreshold($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    // 显示商户图片信息管理
    public function shopimgmgrListview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopimgmgrModel = D('baiduwaimaishopimgmgr');
        $total = $baiduwaimaishopimgmgrModel->where($where)->count();
        $baiduwaimaishopimgmgrResult = $baiduwaimaishopimgmgrModel->where($where)->select();
        // 从数据中列出列表的数据
        if (count($baiduwaimaishopimgmgrResult) > 0) {
            $listData ['rows'] = $baiduwaimaishopimgmgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('shopimgmgrlistview');
    }

    // 新建商户图片
    public function shopimgmgrCreateview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $fields = array(
            'shopinfoid',
            'poi_name'
        );
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->field($fields)->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);
        $this->display('shopimgmgrcreateview');
    }

    // 保存商户图片信息
    public function shopimgmgrSave()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $data = array();
        $data ['source_name'] = $_REQUEST ['source_name'];
        $data ['shop_id'] = $_REQUEST ['shop_id'];
        $data ['type'] = $_REQUEST ['type'];

        // 保存商店log
        if ($_FILES ['img'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }

            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['img'] = $ftpPathName;
            }
        }
        $data ['desc'] = $_REQUEST ['desc'];
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopimgmgrModel = D('baiduwaimaishopimgmgr');
        $baiduwaimaishopimgmgrModel->create();
        $baiduwaimaishopimgmgrResult = $baiduwaimaishopimgmgrModel->add($data);

        $returnData = array();
        if ($baiduwaimaishopimgmgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    // 查看商户图片信息
    public function shopimgmgrDetailview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $fields = array(
            'shopinfoid',
            'poi_name'
        );
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->field($fields)->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);

        $where = array();
        $where ['shopimgmgrid'] = $_REQUEST ['shopimgmgrid'];
        $baiduwaimaishopimgmgrModel = D('baiduwaimaishopimgmgr');
        $baiduwaimaishopimgmgrResult = $baiduwaimaishopimgmgrModel->where($where)->select();

        $this->assign('shopimgmgr', $baiduwaimaishopimgmgrResult [0]);
        $this->display('shopimgmgrdetailview');
    }

    // 编辑商户图片信息
    public function shopimgmgrEditview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $fields = array(
            'shopinfoid',
            'poi_name'
        );
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $shopinfoResult = $baiduwaimaishopinfoModel->field($fields)->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);

        $where = array();
        $where ['shopimgmgrid'] = $_REQUEST ['shopimgmgrid'];
        $baiduwaimaishopimgmgrModel = D('baiduwaimaishopimgmgr');
        $baiduwaimaishopimgmgrResult = $baiduwaimaishopimgmgrModel->where($where)->select();

        $this->assign('shopimgmgr', $baiduwaimaishopimgmgrResult [0]);
        $this->display('shopimgmgreditview');
    }

    // 更新商户图片信息
    public function shopimgmgrEditUpdate()
    {
        import('ORG.Net.UploadFile');
        import('@.Extend.FtpUtil');

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $where = array();
        $where ['shopimgmgrid'] = $_REQUEST ['shopimgmgrid'];
        $data = array();
        $data ['source_name'] = $_REQUEST ['source_name'];
        $data ['shop_id'] = $_REQUEST ['shop_id'];
        $data ['type'] = $_REQUEST ['type'];
        // 保存商店log
        if ($_FILES ['img'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }

            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['img'] = $ftpPathName;
            }
        }
        $data ['desc'] = $_REQUEST ['desc'];
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopimgmgrModel = D('baiduwaimaishopimgmgr');
        $baiduwaimaishopimgmgrResult = $baiduwaimaishopimgmgrModel->where($where)->save($data);

        $returnData = array();
        if ($baiduwaimaishopimgmgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'success';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    //订单管理列表
    public function ordermgrListview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaiordermgrModel = D('baiduwaimaiordermgr');
        $total = $baiduwaimaiordermgrModel->where($where)->count();
        $baiduwaimaiordermgrResult = $baiduwaimaiordermgrModel->where($where)->select();
        //var_dump($baiduwaimaiordermgrModel->getLastSql());
        //var_dump($baiduwaimaiordermgrResult);
        // 从数据中列出列表的数据
        if (count($baiduwaimaiordermgrResult) > 0) {
            $listData ['rows'] = $baiduwaimaiordermgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }

        $this->assign('listData', json_encode($listData));
        $this->display('ordermgrlistview');
    }

    // 新建订单信息
    public function ordermgrCreateview()
    {
        $this->display('ordermgrcreateview');
    }

    // 保存订单内容
    public function ordermgrSave()
    {
        $data = array();
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaiordermgrModel = D('baiduwaimaiordermgr');
        $baiduwaimaiordermgrModel->create();
        $baiduwaimaiordermgrResult = $baiduwaimaiordermgrModel->add($data);
        $sql = $baiduwaimaiordermgrModel->getLastSql();
        $returnData = array();
        if ($baiduwaimaiordermgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error'.$sql;
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    //分类编辑页面
    public function ordermgrEditview()
    {
        $ordermgrid = $_REQUEST ['ordermgrid'];
        $where ['ordermgrid'] = $ordermgrid;
        $baiduwaimaiordermgrModel = D('baiduwaimaiordermgr');
        $baiduwaimaiordermgrResult = $baiduwaimaiordermgrModel->where($where)->find();

        $this->assign('ordermgr', $baiduwaimaiordermgrResult);
        $this->display('ordermgreditview');
    }

    // 更新分类内容
    public function ordermgrEditUpdate()
    {
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $where = array();
        $where['categoryid'] = $_REQUEST['categoryid'];
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        //查询原产品分类
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if($baiduwaimaicategorymgrResult) {
            $old_name = $baiduwaimaicategorymgrResult['name'];
        }else{
            $old_name = '';
        }
        $data['old_name'] = $old_name;
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->save($data);
        $returnData = array();
        if ($baiduwaimaicategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn($returnData, 'JSON');
    }

    //查看分类内容
    public function ordermgrDetailview()
    {
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        $this->assign('categorymgr', $baiduwaimaicategorymgrResult);
        $this->display('categorymgrdetailview');
    }

    //新增菜品分类
    public function ordermgrCreate()
    {
        import('@.Extend.Baiduwaimai');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if ( $baiduwaimaicategorymgrResult) {
            $body = array();
            $body =  $baiduwaimaicategorymgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->categorymgrCreate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //更新菜品分类
    public function ordermgrUpdate(){
        import('@.Extend.Baiduwaimai');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $baiduwaimaishopinfoModel = D('baiduwaimaishopinfo');
        $baiduwaimaishopinfoResult = $baiduwaimaishopinfoModel->where($where)->select();
        $where = array();
        $where ['categoryid'] = $categoryid;
        $baiduwaimaicategorymgrModel = D('baiduwaimaicategorymgr');
        $baiduwaimaicategorymgrResult = $baiduwaimaicategorymgrModel->where($where)->find();
        if ( $baiduwaimaicategorymgrResult) {
            $body = array();
            $body =  $baiduwaimaicategorymgrResult;
            $baiduwaimaiApi = new Baiduwaimai();
            $resp = $baiduwaimaiApi->categorymgrUpdate($body,$baiduwaimaishopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //订单完成操作
    public function ordermgrComplete(){
        import('@.Extend.Baiduwaimai');
        $orderid = $_REQUEST ['orderid'];
        $body['orderid'] = $orderid;
        $baiduwaimaiApi = new Baiduwaimai();
        $resp = $baiduwaimaiApi->ordermgrComplete($body);
        $this->ajaxReturn($resp);
    }


    // 显示操作日志
    public function operatelogListview()
    {
        $baiduwaimailogModel = D('baiduwaimailog');
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $total = $baiduwaimailogModel->where($where)->count();
        $baiduwaimailogResult = $baiduwaimailogModel->where($where)->order('date desc')->select();
        foreach ($baiduwaimailogResult as $key => $value) {
            $replaceStr = $value ['operate'];
            $replaceStr = str_replace('"', "", $replaceStr);
            $replaceStr = str_replace('{', "", $replaceStr);
            $replaceStr = str_replace('}', "", $replaceStr);
            $replaceStr = str_replace('[', "", $replaceStr);
            $replaceStr = str_replace(']', "", $replaceStr);
            $replaceStr = str_replace(',', " ", $replaceStr);
            $replaceStr = str_replace(':', " ", $replaceStr);
            $baiduwaimailogResult [$key] ['operate'] = $replaceStr;
        }
        // 从数据中列出列表的数据
        if (count($baiduwaimailogResult) > 0) {
            $listData ['rows'] = $baiduwaimailogResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('operateloglistview');
    }




    //删除特殊的字符
    function ReMoveChar($text)
    {
        $text=str_replace("`","",$text);
        $text=str_replace("'","",$text);
        $text=str_replace("~","",$text);
        $text=str_replace('"',"",$text);
        $text=str_replace('　'," ",$text);
        $text=str_replace('，',"",$text);
        $text=str_replace(',',"",$text);
        $text=str_replace('.','元',$text);
        $text=str_replace('.','<',$text);
        $text=str_replace('.','>',$text);

        for($i =0;$i<32;$i++){
            $text=str_replace(chr($i),"",$text);
        }
        return htmlspecialchars($text,ENT_QUOTES);
    }
}
