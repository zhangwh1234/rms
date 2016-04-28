<?php
    /**
    * 统计产品销售数量的程序
    * 2014-06-01
    */

    class ProductsMonitAction extends Action{
    	
    	/**
    	 *  计算所有的域名的情况
    	 */
    	public function index(){
    		require APP_PATH.'Conf/domainconfig.php';
    		foreach ($domainArray as $key => $value){
    			$this->doProductsMonit($value);
    		}
    	}

    	/***
    	 * 计算
    	 */
        public function doProductsMonit($access){
        
            //初始化产品销售表
            $productsMonitModel = D('Productsmonit','',$access);
            //清除产品销售表
            $where= array();
            $where['domain'] = $access;
            $productsMonitModel->where($where)->delete();
            
            //查询是否已经有产品
            $where =array();
            $where['name'] = '产品名称';
            $where['domain'] = $access;
            $productsMonitResult = $productsMonitModel->field('name')->where($where)->find();

            //初始化产品表
            $orderproductsModel = D('Orderproducts','',$access);
            $where = array();    
            $where['domain'] = $access;      
            $orderproductsResult = $orderproductsModel->Distinct(true)->where($where)->field('name')->select();
            $i = 1;
            $productsName = array();
            $data = array();
            $data['name'] = '产品名称';
            $data['domain'] = $access;
            foreach($orderproductsResult as $value){
                $p = 'p'.$i;
                $data[$p] = $value['name'];
                $i = $i + 1;
                //定义统计产品的数组
                $productsName[$p] = $value['name'];
            }
            if(empty($productsMonitResult)){  
                $productsMonitModel->add($data);
            }

            //查询是否有全部
            $where = array();
            $where['name'] = '全部';
            $where['domain'] = $access;
            $productsMonitResult = $productsMonitModel->field('name')->where($where)->find();

            if(empty($productsMonitResult)){
                $data = array();
                $data['name'] = '全部';
                $data['domain'] = $access;
                $productsMonitModel->create();
                $productsMonitModel->add($data);
                //定义统计的数组  
                $tongji['全部'] = array();
            }

            
            //查询分公司的名字
            $companyModel = D('Companymgr','',$access);
            $where = array();
            $where['domain'] = $access;
            $companyResult = $companyModel->Distinct(true)->where($where)->field('name')->select();
            $data = array();
            foreach($companyResult as $value){
                $data['name'] = $value['name'];
                $data['domain'] = $access;
                //查询是否有
                $where = array();
                $where['name'] = $value['name'];
                $where['domain'] = $access;
                $productsMonitResult = $productsMonitModel->field('name')->where($where)->find();
                if(empty($productsMonitResult)){
                    $productsMonitModel->add($data);
                    $tongji[$value['name']] = array(); 
                }
            }

           

            //返回所有订单
            $orderformModel = D('Orderform','',$access);
            $where = array();
            $where['ap'] = $this->getAp();
            // $where['custdate'] = date('Ý-m-d'); //当天的日期
            $where['domain'] = $access;
            $orderformResult = $orderformModel->where($where)->select();
            //var_dump($orderformResult);
            //返回所有订货
            $orderproductsModel = D('Orderproducts','',$access);
            $where = array();
            $where['domain'] = $access;
            $orderproductsResult = $orderproductsModel->where($where)->select();

            //开始计算全部的产品
            foreach($orderformResult as $value){
                $orderformid = $value['orderformid'];
                $i = 1;
                foreach($orderproductsResult as $orderproductsvalue){
                    if($orderproductsvalue['orderformid'] == $orderformid){

                        foreach($productsName as $productsKey =>$productsValue){

                            if($productsValue == $orderproductsvalue['name']){
                                //统计出来，加入统计数组中
                                $tongji['全部'][$productsKey] += $orderproductsvalue['number'];
                                // var_dump($tongji);     
                            }
                        }
                    }
                }
            }


            //开始计算每个分公司的订货情况
            foreach($orderformResult as $orderValue){
                $orderformid = $orderValue['orderformid'];
                foreach($companyResult as $companyValue){
                    //是否是这个分公司的订单
                    if(trim($orderValue['company']) == trim($companyValue['name'])){
                        foreach($orderproductsResult as $orderproductsvalue){  
                            if($orderproductsvalue['orderformid'] == $orderformid){
                                foreach($productsName as $productsKey =>$productsValue){
                                    if($productsValue == trim($orderproductsvalue['name'])){
                                        //统计出来，加入统计数组中
                                        $tongji[$companyValue['name']][$productsKey] += $orderproductsvalue['number'];

                                    }
                                }
                            }
                        }
                    }
                }
            }

		
            //保存统计表到数据库中
            foreach($tongji as $tongjiKey => $tongjiValue){
                $where = array();
                $where['name'] = $tongjiKey;
                $where['domain'] = $access;
                $data = array();
                foreach($tongjiValue as $key =>  $value){
                    $data[$key] = $value;
                }
                $data['domain'] = $access;
                $productsMonitModel->where($where)->save($data);
                //var_dump($productsMonitModel->getLastSql());
            }


        }


        //获取当前时间的午别
        public function getAp(){
            $nowHour = date('H');
            if((int)$nowHour >= 16 ){
                $ap = '下午';
            }else{
                $ap = '上午';
            }
            return $ap;
        }
    }
?>
