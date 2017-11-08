<?php

/**
 * Created by zhangwh
 * User: lihua
 * Date: 15/6/11
 * Time: 上午11:54
 * 饿了吗管理平台
 */
class ElemeAction extends ModuleAction
{
    // 显示管理主面板
    public function index()
    {
        $this->display('mainview');
    }

    // 显示管理主面板的顶部菜单
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

    // 显示左面板的菜单
    public function mainviewLeftMenu()
    {
        $this->display('mainviewleftmenu');
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
        $elemeshopinfoModel = D('elemeshopinfo');
        $total = $elemeshopinfoModel->where($where)->count();
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->select();

        // 从数据中列出列表的数据
        if (count($elemeshopinfoResult) > 0) {
            $listData ['rows'] = $elemeshopinfoResult;
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

        $data = array();
        $data ['restaurant_id'] = $_REQUEST ['restaurant_id'];
        $data ['address_text'] = $_REQUEST ['address_text'];
        $data ['geo'] = $_REQUEST ['geo'];
        $data ['agent_fee'] = $_REQUEST ['agent_fee'];
        $data ['close_description'] = $_REQUEST ['close_description'];
        $data ['deliver_description'] = $_REQUEST ['deliver_description'];
        $data ['description'] = $_REQUEST ['description'];
        $data ['name'] = $_REQUEST ['name'];
        $data ['is_bookable'] = $_REQUEST ['is_bookable'];
        $data ['open_time'] = $_REQUEST ['open_time'];
        $data ['phone'] = $_REQUEST ['phone'];
        $data ['promotion_info'] = $_REQUEST ['promotion_info'];
        $data ['is_open'] = $_REQUEST ['is_open'];
        $data ['delivery_price'] = $_REQUEST ['delivery_price'];
        $data ['geo_json'] = $_REQUEST ['gen_json'];


        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->create();
        $elemeshopinfoResult = $elemeshopinfoModel->add($data);
        var_dump($elemeshopinfoModel->getLastSql());
        $returnData = array();
        if ($elemeshopinfoResult) {
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
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->select();
        $this->assign("shopinfo", $elemeshopinfoResult [0]);
        $this->display('shopinfodetailview');
    }

    // 编辑商户
    public function shopinfoEditview()
    {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->select();
        $this->assign("shopinfo", $elemeshopinfoResult [0]);
        $this->display('shopinfoeditview');
    }

    // 编辑商户信息后保存
    public function shopinfoEditUpdate()
    {

        $where = array();
        $where ['shopinfoid'] = $_REQUEST ['shopinfoid'];
        $data = array();
        $data ['restaurant_id'] = $_REQUEST ['restaurant_id'];
        $data ['address_text'] = $_REQUEST ['address_text'];
        $data ['geo'] = $_REQUEST ['geo'];
        $data ['agent_fee'] = $_REQUEST ['agent_fee'];
        $data ['close_description'] = $_REQUEST ['close_description'];
        $data ['deliver_description'] = $_REQUEST ['deliver_description'];
        $data ['description'] = $_REQUEST ['description'];
        $data ['name'] = $_REQUEST ['name'];
        $data ['is_bookable'] = $_REQUEST ['is_bookable'];
        $data ['open_time'] = $_REQUEST ['open_time'];
        $data ['phone'] = $_REQUEST ['phone'];
        $data ['promotion_info'] = $_REQUEST ['promotion_info'];
        $data ['is_open'] = $_REQUEST ['is_open'];
        $data ['delivery_price'] = $_REQUEST ['delivery_price'];
        $data ['geo_json'] = $_REQUEST ['gen_json'];

        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->save($data);

        $returnData = array();
        if ($elemeshopinfoResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    /**
     * 改变商户的下单模式
     * 命令:http://localhost/rms/index.php?s=/Eleme/changeOrderMode
     */
    public  function changeOrderMode(){

        import('@.Extend.Eleme');
        $ElemeApi = new Eleme();
        $resp = $ElemeApi->changeOrderMode();
        //$this->ajaxReturn($resp);
        echo '<pre>';
        var_dump($resp);
    }

    /**
     * 获得商户下面所有门店的信息
     */
    public function shopGetOwn(){
        import('@.Extend.Eleme');
        $ElemeApi = new Eleme();
        $resp = $ElemeApi->shopGetOwn();
        //$this->ajaxReturn($resp);
        echo '<pre>';
        var_dump($resp);
    }

    /**
     * 获取门店的详细信息
     */
    public function shopinfoGetShopInfo(){
        import('@.Extend.Eleme');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();
        if ($elemeshopinfoResult) {
            $body = array();
            $body = $elemeshopinfoResult;
            $ElemeApi = new Eleme();
            $resp = $ElemeApi->shopinfoGetShopInfo($body);
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        } else {

        }
    }

    /**
     * 更新商户基本信息
     */
    public function shopInfoUpdate()
    {
        import('@.Extend.Eleme');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();
        if ($elemeshopinfoResult) {
            $body = array();
            $body = $elemeshopinfoResult;
            $ElemeApi = new Eleme();
            $resp = $ElemeApi->shopUpdate($body);
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        } else {

        }

    }

    /**
     * 更新商户营业信息
     */
    public function updateShopBussinessStatus()
    {
        import('@.Extend.Eleme');
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->where($where)->find();
        if ($elemeshopinfoResult) {
            $body = array();
            $body = $elemeshopinfoResult;
            $ElemeApi = new Eleme();
            $resp = $ElemeApi->shopUpdateBusinessStatus($body);
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        } else {

        }
    }

    /**
     * 更新商户送餐范围
     */
    public function updateShopGeo()
    {

    }


    /**
     * 菜单分类管理
     */
    public function categorymgrListview()
    {

        $elemecategorymgrModel = D('elemecategorymgr');
        $total = $elemecategorymgrModel->count();
        $elemecategorymgrResult = $elemecategorymgrModel->select();
        // 从数据中列出列表的数据
        if (count($elemecategorymgrResult) > 0) {
            $listData ['rows'] = $elemecategorymgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('categorymgrlistview');
    }

    /**
     * 新建菜单分类
     */
    public function categorymgrCreateview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];

        $elemeshopinfoModel = D('elemeshopinfo');
        $shopinfoResult = $elemeshopinfoModel->where($where)->select();
        $this->assign('categorymgr', $shopinfoResult);
        $this->display('categorymgrcreateview');
    }

    /**
     * 保存菜单分类
     */
    public function categorymgrSave()
    {
        $data = array();
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrModel->create();
        $elemecategorymgrResult = $elemecategorymgrModel->add($data);
        $returnData = array();
        if ($elemecategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    /**
     * 菜单分类查看
     */
    public function categorymgrDetailview()
    {
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();
        $this->assign('categorymgr', $elemecategorymgrResult);
        $this->display('categorymgrdetailview');
    }

    /**
     * 菜单分类编辑
     */
    public function categorymgrEditview()
    {
        $categoryid = $_REQUEST ['categoryid'];
        $where ['categoryid'] = $categoryid;
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();

        $this->assign('categorymgr', $elemecategorymgrResult);
        $this->display('categorymgreditview');
    }

    /**
     * 分类编辑保存
     */
    public function categorymgrEditUpdate()
    {
        $data = $_REQUEST;
        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $where = array();
        $where['categoryid'] = $_REQUEST['categoryid'];
        $elemecategorymgrModel = D('elemecategorymgr');
        //查询原产品分类
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();
        if ($elemecategorymgrResult) {
            $old_name = $elemecategorymgrResult['name'];
        } else {
            $old_name = '';
        }
        $data['old_name'] = $old_name;
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->save($data);
        $returnData = array();
        if ($elemecategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn($returnData, 'JSON');
    }

    //新增菜品分类
    public function categorymgrCreate()
    {
        import('@.Extend.eleme');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->select();
        var_dump($elemeshopinfoResult);
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();
        if ($elemecategorymgrResult) {
            $body = array();
            $body = $elemecategorymgrResult;
            $elemeApi = new Eleme();
            $resp = $elemeApi->categorymgrCreate($body, $elemeshopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //更新菜品分类
    public function categorymgrUpdate()
    {
        import('@.Extend.eleme');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->select();
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();
        if ($elemecategorymgrResult) {
            $body = array();
            $body = $elemecategorymgrResult;
            $elemeApi = new Eleme();
            $resp = $elemeApi->categorymgrUpdate($body, $elemeshopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //删除菜品分类
    public function categorymgrDelete()
    {
        import('@.Extend.eleme');
        $categoryid = $_REQUEST ['categoryid'];
        $where = array();
        $where ['categoryid'] = $categoryid;
        $elemeshopinfoModel = D('elemeshopinfo');
        $elemeshopinfoResult = $elemeshopinfoModel->select();
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->where($where)->find();
        if ($elemecategorymgrResult) {
            $body = array();
            $body = $elemecategorymgrResult;
            $elemeApi = new Eleme();
            $resp = $elemeApi->categorymgrUpdate($body, $elemeshopinfoResult);
            $this->ajaxReturn($resp);
        } else {

        }
    }


    /**
     * 菜单管理
     */
    public function menumgrListview()
    {
        $elememenumgrModel = D('elememenumgr');
        $total = $elememenumgrModel->count();
        $elememenumgrResult = $elememenumgrModel->select();
        // 从数据中列出列表的数据
        if (count($elememenumgrResult) > 0) {
            $listData ['rows'] = $elememenumgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array();
            $listData ['total'] = 0;
        }
        $this->assign('listData', json_encode($listData));
        $this->display('menumgrlistview');
    }

    /**
     * 菜单新建
     */
    public function menumgrCreateview()
    {
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $fields = array(
            'shopinfoid',
            'poi_name'
        );
        $elemeshopinfoModel = D('elemeshopinfo');
        $shopinfoResult = $elemeshopinfoModel->field($fields)->where($where)->select();
        $this->assign('shopinfo', $shopinfoResult);
        $this->display('menumgrcreateview');
    }

    /**
     * 保存菜单
     */
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

        $data ['domain'] = $_SERVER ['HTTP_HOST'];
        $elememenumgrModel = D('elememenumgr');
        $elememenumgrModel->create();
        $elememenumgrResult = $elememenumgrModel->add($data);
        $returnData = array();
        if ($elememenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    /**
     * 菜单查看
     */
    public function menumgrDetailview()
    {
        $menuid = $_REQUEST ['menuid'];
        $where = array();
        $where ['menuid'] = $menuid;
        $elememenumgrModel = D('elememenumgr');
        $elememenumgrResult = $elememenumgrModel->where($where)->select();
        $this->assign('menumgr', $elememenumgrResult [0]);
        $this->display('menumgrdetailview');
    }

    /**
     * 菜单编辑
     */
    public function menumgrEditview()
    {
        $menuid = $_REQUEST ['menuid'];
        $where ['menuid'] = $menuid;
        $elememenumgrModel = D('elememenumgr');
        $elememenumgrResult = $elememenumgrModel->where($where)->select();
        // 取得shopid的商户名称
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];
        $where ['shopinfoid'] = $elememenumgrResult [0] ['shop_id'];
        $fields = array(
            'shopinfoid',
            'poi_name'
        );
        $elemeshopinfoModel = D('elemeshopinfo');
        $shopinfoResult = $elemeshopinfoModel->field($fields)->where($where)->select();
        $this->assign('shopinfoid_poi_name', $shopinfoResult [0] ['poi_name']);
        // 取得所有商户号和名称
        $where = array();
        $where ['domain'] = $_SERVER ['HTTP_HOST'];

        $elemeshopinfoModel = D('elemeshopinfo');
        $shopinfoResult = $elemeshopinfoModel->where($where)->find();
        $this->assign('shopinfo', $shopinfoResult);

        $this->assign('menumgr', $elememenumgrResult [0]);
        $this->display('menumgreditview');
    }

    /**
     * 菜单编辑保存
     */
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
        $data ['source_name'] = $_REQUEST ['source_name']; // 合作方名称
        $data ['shop_id'] = $_REQUEST ['shop_id']; // 商户ID
        $data ['catalog'] = $_REQUEST ['catalog'];
        $data ['name'] = $_REQUEST ['name'];
        if ($_FILES ['takeaway_menu_dishpic'] ['name']) {
            if (!$upload->upload()) { // 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload($info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename']);
                $data ['takeaway_menu_dishpic'] = $ftpPathName;
            }
        }
        $data ['origin_price'] = $_REQUEST ['origin_price'];
        $data ['current_price'] = $_REQUEST ['current_price'];
        $data ['sell_number'] = $_REQUEST ['sell_number'];
        $data ['unit'] = $_REQUEST ['unit'];
        $data ['soldout'] = $_REQUEST ['soldout'];
        $data ['special'] = $_REQUEST ['special'];
        $data ['min_order_number'] = $_REQUEST ['min_order_number'];
        $data ['packge_box_number'] = $_REQUEST ['packge_box_number'];
        $data ['packge_box_price'] = $_REQUEST ['packge_box_price'];
        $data ['description'] = $_REQUEST ['description'];
        $data ['available_times'] = $_REQUEST ['available_times'];
        $data ['rank'] = $_REQUEST ['rank'];
        $data ['uploadstate'] = 1;
        $elememenumgrModel = D('elememenumgr');
        $elememenumgrResult = $elememenumgrModel->where($where)->save($data);

        $returnData = array();
        if ($elememenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn($returnData, 'JSON');
    }

    //新增食物
    public function menumgrCreate()
    {
        import('@.Extend.eleme');
        $categoryid = $_REQUEST ['menuid'];
        $where = array();
        $where ['menuid'] = $categoryid;
        $elemecategorymgrModel = D('elemecategorymgr');
        $elemecategorymgrResult = $elemecategorymgrModel->select();
        $menumgrModel = D('elememenumgr');
        $menumgrResult = $menumgrModel->where($where)->find();
        if ($menumgrResult) {
            $body = array();
            $body = $menumgrResult;
            $elemeApi = new Eleme();
            $resp = $elemeApi->menumgrCreate($body, $elemecategorymgrResult);
            var_dump($resp);
            $this->ajaxReturn($resp);
        } else {

        }
    }

    //获取商户的新订单
    public function orderGetNew()
    {
        import('@.Extend.eleme');
        $ElemeApi = new Eleme();
        $resp = $ElemeApi->orderGetNew();
        //$this->ajaxReturn($resp);
        echo '<pre>';
        var_dump($resp);

    }


}