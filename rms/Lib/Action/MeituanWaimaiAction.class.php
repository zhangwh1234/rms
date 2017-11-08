<?php
/**
 * Created by zhangwh1234
 * User: 丽华快餐
 * Date: 15/6/13
 * 美团外卖管理界面
 */

class MeituanWaimaiAction extends ModuleAction{

    // 显示管理主面板
    public function index() {
        $this->display ( 'mainview' );
    }

    // 显示管理主面板的顶部菜单
    public function mainviewTopMenu() {
        $this->display ( 'mainviewtopmenu' );
    }

    // 显示左面板的菜单
    public function mainviewLeftMenu() {
        $this->display ( 'mainviewleftmenu' );
    }

    // 显示平台说明
    public function mainviewPlatformIntroduce() {
        $this->display ( 'mainviewplatformintroduce' );
    }

    // 商店信息管理列表查看
    public function shopinfoListview() {
        $where = array ();
        $where ['domain'] = $this->getDomain();
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $total = $meituanshopinfoModel->where ( $where )->count ();
        $meituanshopinfoResult = $meituanshopinfoModel->where ( $where )->select ();

        // 从数据中列出列表的数据
        if (count ( $meituanshopinfoResult ) > 0) {
            $listData ['rows'] = $meituanshopinfoResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array ();
            $listData ['total'] = 0;
        }

        $this->assign ( 'listData', json_encode ( $listData ) );
        $this->display ( 'shopinfolistview' );
    }


    // 新建商户
    public function shopinfoCreateview() {
        $this->display ( 'shopinfocreateview' );
    }


    // 保存新建商户的信息
    public function shopinfoSave() {

        $data = array ();
        $data ['poi_name'] = $_REQUEST ['poi_name'];
        $data ['alias'] = $_REQUEST ['alias'];
        $data ['logo_url'] = $_REQUEST ['logo_url'];
        $data ['province'] = $_REQUEST ['province'];
        $data ['city'] = $_REQUEST ['city'];
        $data ['county'] = $_REQUEST ['county'];
        $data ['poi_address'] = $_REQUEST ['poi_address'];
        $data ['aoi'] = $_REQUEST ['aoi'];
        $data ['coord_type'] = 'amap';
        $data ['longitude'] = $_REQUEST ['longitude'];
        $data ['latitude'] = $_REQUEST ['latitude'];
        $data ['phone'] = $_REQUEST ['phone'];
        $data ['phone_others'] = $_REQUEST ['phone_others'];
        $data ['category1'] = $_REQUEST ['category1'];
        $data ['category2'] = $_REQUEST ['category2'];
        $data ['tag'] = $_REQUEST ['tag'];
        $data ['description'] = $_REQUEST ['description'];
        $data ['business_state'] = $_REQUEST ['business_state'];
        $data ['overall_rating'] = $_REQUEST ['overall_rating'];
        $data ['source_name'] = $_REQUEST ['source_name'];
        $data ['source_logourl'] = $_REQUEST ['source_logourl'];
        $data ['source_url'] = 'PC'; // $_REQUEST['source_url'];
        $data ['source_url_mobilephone'] = $_REQUEST ['source_url_mobilephone'];
        $data ['takeout_service_phone'] = $_REQUEST ['takeout_service_phone'];
        $data ['takeout_shop_can_order'] = $_REQUEST ['takeout_shop_can_order'];
        $data ['takeout_phone'] = $_REQUEST ['takeout_phone'];
        $data ['takeout_area'] = $_REQUEST ['takeout_area'];
        $data ['takeout_aoi'] = $_REQUEST ['takeout_aoi'];
        $data ['takeout_radius'] = $_REQUEST ['takeout_radius'];
        $data ['takeout_deliver_regions'] = $_REQUEST ['takeout_deliver_regions'];
        $data ['takeout_invoice'] = $_REQUEST ['takeout_invoice'];
        $data ['takeout_price'] = $_REQUEST ['takeout_price'];
        $data ['takeout_cost'] = $_REQUEST ['takeout_cost'];
        $data ['takeout_coupon'] = $_REQUEST ['takeout_coupon'];
        $data ['takeout_open_time'] = $_REQUEST ['takeout_open_time'];
        $data ['takeout_average_time'] = $_REQUEST ['takeout_average_time'];
        $data ['takeout_current_time'] = $_REQUEST ['takeout_current_time'];
        $data ['takeout_announcement'] = $_REQUEST ['takeout_announcement'];
        $data ['takeout_number'] = $_REQUEST ['takeout_number'];
        $data ['org_code'] = $_REQUEST ['org_code'];
        $data ['order_threshold'] = $_REQUEST ['order_threshold'];
        $data ['domain'] = $this->getDomain();
        $data ['uploadstate'] = 1;

        $elemeshopinfoModel = D ( 'elemeshopinfo' );
        $elemeshopinfoResult = $elemeshopinfoModel->create ();
        $elemeshopinfoResult = $elemeshopinfoModel->add ( $data );

        $returnData = array ();
        if ($elemeshopinfoResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn ( $returnData, 'JSON' );
    }

    // 查看商户信息
    public function shopinfoDetailview() {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array ();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D ( 'elemeshopinfo' );
        $elemeshopinfoResult = $elemeshopinfoModel->where ( $where )->select ();
        $this->assign ( "shopinfo", $elemeshopinfoResult [0] );
        $this->display ( 'shopinfodetailview' );
    }

    // 编辑商户
    public function shopinfoEditview() {
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array ();
        $where ['shopinfoid'] = $shopinfoid;
        $elemeshopinfoModel = D ( 'elemeshopinfo' );
        $elemeshopinfoResult = $elemeshopinfoModel->where ( $where )->select ();
        $this->assign ( "shopinfo", $elemeshopinfoResult [0] );
        $this->display ( 'shopinfoeditview' );
    }

    // 编辑商户信息后保存
    public function shopinfoEditUpdate() {

        $where = array ();
        $where ['shopinfoid'] = $_REQUEST ['shopinfoid'];
        $data = array ();
        $data ['poi_name'] = $_REQUEST ['poi_name'];
        $data ['alias'] = $_REQUEST ['alias'];


        $data ['province'] = $_REQUEST ['province'];
        $data ['city'] = $_REQUEST ['city'];
        $data ['county'] = $_REQUEST ['county'];
        $data ['poi_address'] = $_REQUEST ['poi_address'];
        $data ['aoi'] = $_REQUEST ['aoi'];
        $data ['coord_type'] = 'amap';
        $data ['longitude'] = $_REQUEST ['longitude'];
        $data ['latitude'] = $_REQUEST ['latitude'];
        $data ['phone'] = $_REQUEST ['phone'];
        $data ['phone_others'] = $_REQUEST ['phone_others'];
        $data ['category1'] = $_REQUEST ['category1'];
        $data ['category2'] = $_REQUEST ['category2'];
        $data ['tag'] = $_REQUEST ['tag'];
        $data ['description'] = $_REQUEST ['description'];
        $data ['business_state'] = $_REQUEST ['business_state'];
        $data ['overall_rating'] = $_REQUEST ['overall_rating'];
        $data ['source_name'] = $_REQUEST ['source_name'];
        $data ['source_logourl'] = $_REQUEST ['source_logourl'];
        $data ['source_url'] = 'PC'; // $_REQUEST['source_url'];
        $data ['source_url_mobilephone'] = $_REQUEST ['source_url_mobilephone'];
        $data ['takeout_service_phone'] = $_REQUEST ['takeout_service_phone'];
        $data ['takeout_shop_can_order'] = $_REQUEST ['takeout_shop_can_order'];


        $data ['takeout_phone'] = $_REQUEST ['takeout_phone'];
        $data ['takeout_area'] = $_REQUEST ['takeout_area'];
        $data ['takeout_aoi'] = $_REQUEST ['takeout_aoi'];
        $data ['takeout_radius'] = $_REQUEST ['takeout_radius'];
        $data ['takeout_deliver_regions'] = $_REQUEST ['takeout_deliver_regions'];
        $data ['takeout_invoice'] = $_REQUEST ['takeout_invoice'];
        $data ['takeout_price'] = $_REQUEST ['takeout_price'];
        $data ['takeout_coupon'] = $_REQUEST ['takeout_coupon'];
        $data ['takeout_open_time'] = $_REQUEST ['takeout_open_time'];
        $data ['takeout_average_time'] = $_REQUEST ['takeout_average_time'];
        $data ['takeout_current_time'] = $_REQUEST ['takeout_current_time'];
        $data ['takeout_announcement'] = $_REQUEST ['takeout_announcement'];
        $data ['takeout_number'] = $_REQUEST ['takeout_number'];
        $data ['org_code'] = $_REQUEST ['org_code'];
        $data ['order_threshold'] = $_REQUEST ['order_threshold'];

        $elemeshopinfoModel = D ( 'elemeshopinfo' );
        $elemeshopinfoResult = $elemeshopinfoModel->where ( $where )->save ( $data );

        $returnData = array ();
        if ($elemeshopinfoResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn ( $returnData, 'JSON' );
    }

    /**
     * 设置商户营业状态
     */
    public function ShopInfoOpen(){
        import ( '@.Extend.Meituan' );
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array ();
        $where ['shopinfoid'] = $shopinfoid;
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $meituanshopinfoResult = $meituanshopinfoModel->where($where)->find();
        if($meituanshopinfoResult){
            $body = array();
            $body = $meituanshopinfoResult;
            $MeituanApi = new Meituan($meituanshopinfoResult);
            $resp = $MeituanApi->shopinfoOpen();
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        }else{

        }

    }

    /**
     * 设置商户休息状态
     */
    public function ShopInfoClose(){
        import ( '@.Extend.Meituan' );
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array ();
        $where ['shopinfoid'] = $shopinfoid;
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $meituanshopinfoResult = $meituanshopinfoModel->where($where)->find();
        if($meituanshopinfoResult){
            $body = array();
            $body = $meituanshopinfoResult;
            $MeituanApi = new Meituan($meituanshopinfoResult);
            $resp = $MeituanApi->shopinfoClose();
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        }else{

        }

    }

    /**
     * 设置商户预计送达时长
     */
    public function ShopInfoSendtime(){
        import ( '@.Extend.Meituan' );
        $shopinfoid = $_REQUEST ['shopinfoid'];
        $where = array ();
        $where ['shopinfoid'] = $shopinfoid;
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $meituanshopinfoResult = $meituanshopinfoModel->where($where)->find();
        if($meituanshopinfoResult){
            $body = array();
            $body = $meituanshopinfoResult;
            $MeituanApi = new Meituan($meituanshopinfoResult);
            $resp = $MeituanApi->shopinfoSendtime();
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        }else{

        }

    }

    /**
     * 更新商户营业信息
     */
    public function updateShopBussinessStatus(){

    }

    /**
     * 更新商户送餐范围
     */
    public function updateShopGeo(){

    }


    /**
     * 菜单分类管理
     */
    public function categorymgrListview(){
        $meituancategorymgrModel = D ( 'meituancategorymgr' );
        $where = array();
        $where['domain'] = $this->getDomain();
        $total = $meituancategorymgrModel->count ();
        $meituancategorymgrResult = $meituancategorymgrModel->select ();
        // 从数据中列出列表的数据
        if (count ( $meituancategorymgrResult ) > 0) {
            $listData ['rows'] = $meituancategorymgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array ();
            $listData ['total'] = 0;
        }
        $this->assign ( 'listData', json_encode ( $listData ) );
        $this->display ( 'categorymgrlistview' );
    }

    /**
     * 新建菜单分类
     */
    public function categorymgrCreateview(){
        $where = array ();
        $where ['domain'] = $this->getDomain();

        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $shopinfoResult = $meituanshopinfoModel->where ( $where )->select ();
        $this->assign ( 'categorymgr', $shopinfoResult );
        $this->display ( 'categorymgrcreateview' );
    }

    /**
     * 保存菜单分类
     */
    public function categorymgrSave(){
        $data = array ();
        $data = $_REQUEST;
        $data ['domain'] = $this->getDomain();
        $data['app_poi_code'] =  0;
        $meituancategorymgrModel = D ( 'meituancategorymgr' );
        $meituancategorymgrModel->create ();
        $meituancategorymgrResult = $meituancategorymgrModel->add ( $data );
        $returnData = array ();
        if ($meituancategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn ( $returnData, 'JSON' );
    }

    /**
     * 菜单分类查看
     */
    public function categorymgrDetailview(){
        $categoryid = $_REQUEST ['categoryid'];
        $where = array ();
        $where ['categoryid'] = $categoryid;
        $meituancategorymgrModel = D ( 'meituancategorymgr' );
        $meituancategorymgrResult = $meituancategorymgrModel->where ( $where )->find ();
        $this->assign ( 'categorymgr', $meituancategorymgrResult );
        $this->display ( 'categorymgrdetailview' );
    }

    /**
     * 菜单分类编辑
     */
    public function categorymgrEditview(){
        $categoryid = $_REQUEST ['categoryid'];
        $where ['categoryid'] = $categoryid;
        $meituancategorymgrModel = D ( 'meituancategorymgr' );
        $meituancategorymgrResult = $meituancategorymgrModel->where ( $where )->find ();

        $this->assign ( 'categorymgr', $meituancategorymgrResult );
        $this->display ( 'categorymgreditview' );
    }

    /**
     * 分类编辑保存
     */
    public function categorymgrEditUpdate(){
        $data = array ();
        $data = $_REQUEST;
        $data ['domain'] = $this->getDomain();
        $where = array();
        $where['categoryid'] = $_REQUEST['categoryid'];
        $meituancategorymgrModel = D ( 'meituancategorymgr' );
        $meituancategorymgrResult = $meituancategorymgrModel->where($where)->save( $data );

        $returnData = array ();
        if ($meituancategorymgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }
        $this->ajaxReturn ( $returnData, 'JSON' );
    }

    /**
     * 更新商品分类
     */
    public function categoryUpdate(){
        import ( '@.Extend.Meituan' );
        $categoryid = $_REQUEST ['categoryid'];
        $where = array ();
        $where ['categoryid'] = $categoryid;
        $meituancategoryModel = D ( 'meituancategorymgr' );
        $meituancategoryResult = $meituancategoryModel->where($where)->find();
        if($meituancategoryResult){
            $body = array();
            $body = $meituancategoryResult;
            $MeituanApi = new Meituan();
            $resp = $MeituanApi->categoryUpdate($meituancategoryResult);
            //$this->ajaxReturn($resp);
            echo '<pre>';
            var_dump($resp);
        }else{

        }
    }


    /**
     * 菜单管理
     */
    public function menumgrListview(){
        $meituanmenumgrModel = D ( 'meituanmenumgr' );
        $where = array();
        $where['domain'] = $this->getDomain();
        $total = $meituanmenumgrModel->where($where)->count ();
        $meituanmenumgrResult = $meituanmenumgrModel->where($where)->select ();
        // 从数据中列出列表的数据
        if (count ( $meituanmenumgrResult ) > 0) {
            $listData ['rows'] = $meituanmenumgrResult;
            $listData ['total'] = $total;
        } else {
            $listData ['rows'] = array ();
            $listData ['total'] = 0;
        }
        $this->assign ( 'listData', json_encode ( $listData ) );
        $this->display ( 'menumgrlistview' );
    }

    /**
     * 菜单新建
     */
    public function menumgrCreateview(){
        $where = array ();
        $where ['domain'] = $this->getDomain();
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $shopinfoResult = $meituanshopinfoModel->where ( $where )->select ();
        $this->assign ( 'shopinfo', $shopinfoResult );
        $this->display ( 'menumgrcreateview' );
    }

    /**
     * 保存菜单
     */
    public function menumgrSave(){
        import ( 'ORG.Net.UploadFile' );
        import ( '@.Extend.FtpUtil' );

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array (
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $data = array ();
        $data = $_REQUEST;
        if ($_FILES ['pic'] ['name']) {
            if (! $upload->upload ()) { // 上传错误提示错误信息
                $this->error ( $upload->getErrorMsg () );
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo ();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload ( $info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename'] );
                $data ['takeaway_menu_dishpic'] = $ftpPathName;
            }
        }

        $data ['domain'] = $this->getDomain();
        $meituanmenumgrModel = D ( 'meituanmenumgr' );
        $meituanmenumgrModel->create ();
        $meituanmenumgrResult = $meituanmenumgrModel->add ( $data );
        $returnData = array ();
        if ($meituanmenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn ( $returnData, 'JSON' );
    }

    /**
     * 菜单查看
     */
    public function menumgrDetailview(){
        $menuid = $_REQUEST ['menuid'];
        $where = array ();
        $where ['menuid'] = $menuid;
        $meituanmenumgrModel = D ( 'meituanmenumgr' );
        $meituanmenumgrResult = $meituanmenumgrModel->where ( $where )->select ();
        $this->assign ( 'menumgr', $meituanmenumgrResult [0] );
        $this->display ( 'menumgrdetailview' );
    }

    /**
     * 菜单编辑
     */
    public function menumgrEditview(){
        $menuid = $_REQUEST ['menuid'];
        $where ['menuid'] = $menuid;
        $meituanmenumgrModel = D ( 'meituanmenumgr' );
        $meituanmenumgrResult = $meituanmenumgrModel->where ( $where )->select ();
        // 取得shopid的商户名称
        $where = array ();
        $where ['domain'] = $this->getDomain();
        $where ['shopinfoid'] = $meituanmenumgrResult [0] ['shop_id'];
        $fields = array (
            'shopinfoid',
            'poi_name'
        );
        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $shopinfoResult = $meituanshopinfoModel->field ( $fields )->where ( $where )->select ();
        $this->assign ( 'shopinfoid_poi_name', $shopinfoResult [0] ['poi_name'] );
        // 取得所有商户号和名称
        $where = array ();
        $where ['domain'] = $this->getDomain();

        $meituanshopinfoModel = D ( 'meituanshopinfo' );
        $shopinfoResult = $meituanshopinfoModel->where ( $where )->find ();
        $this->assign ( 'shopinfo', $shopinfoResult );

        $this->assign ( 'menumgr', $meituanmenumgrResult [0] );
        $this->display ( 'menumgreditview' );
    }

    /**
     * 菜单编辑保存
     */
    public function menumgrEditUpdate(){
        import ( 'ORG.Net.UploadFile' );
        import ( '@.Extend.FtpUtil' );

        $upload = new UploadFile (); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array (
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型
        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录

        $menuid = $_REQUEST ['menuid'];
        $where = array ();
        $where ['menuid'] = $menuid;
        $data = array ();
        $data ['source_name'] = $_REQUEST ['source_name']; // 合作方名称
        $data ['shop_id'] = $_REQUEST ['shop_id']; // 商户ID
        $data ['catalog'] = $_REQUEST ['catalog'];
        $data ['name'] = $_REQUEST ['name'];
        if ($_FILES ['takeaway_menu_dishpic'] ['name']) {
            if (! $upload->upload ()) { // 上传错误提示错误信息
                $this->error ( $upload->getErrorMsg () );
            } else { // 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo ();
            }
            if ($info [0] ['savename']) {
                // 上传商店的log
                $ftpPathName = FtpUtil::upload ( $info [0] ['savename'], $info [0] ['savepath'] . $info [0] ['savename'] );
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
        $meituanmenumgrModel = D ( 'meituanmenumgr' );
        $meituanmenumgrResult = $meituanmenumgrModel->where ( $where )->save ( $data );

        $returnData = array ();
        if ($meituanmenumgrResult) {
            $returnData ['info'] = 'success';
        } else {
            $returnData ['info'] = 'error';
        }

        $this->ajaxReturn ( $returnData, 'JSON' );
    }



}