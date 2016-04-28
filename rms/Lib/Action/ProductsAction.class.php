<?php
    /**
    *  普通产品类
    *  产品没有分类
    */

    class ProductsAction extends ModuleAction{
        //根据产品代码，查询产品名称
        public function getProductsByCode(){
            $code = $_REQUEST['code'];
            $productsModel = D('Products');
            $where = array();
            $where['code'] = $code;
            $where['domain'] = $_SERVER['HTTP_HOST'];
            $products = $productsModel->field('name,shortname,price')->where($where)->find();
            $this->ajaxReturn($products,'JSON');
        }
    }
?>
